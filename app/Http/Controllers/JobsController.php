<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentHelper;
use App\Http\Requests\CompanyCreateRequest;
use App\Http\Requests\JobCreateRequest;
use App\Model\Applicant;
use App\Model\Attachment;
use App\Model\Company;
use App\Model\JobListing;
use App\Model\JobSkill;
use App\Model\Plan;
use App\Model\Skill;
use App\Model\Subscription;
use App\Providers\AttachmentServiceProvider;
use App\Providers\JobListingsServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use JavaScript;

class JobsController extends Controller
{
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(PaymentHelper $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Displays all user's jobs.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getMyJobs()
    {
        $jobs = JobListing::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(5);

        return view('pages.jobs', ['jobs' => $jobs]);
    }

    /**
     * Displays public job post page.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getJob(Request $request)
    {
        $slug = $request->route('slug');
        $jobData = JobListing::withCount(['company', 'applicants'])->where('slug', $slug)->first();
        if (!$jobData) {
            abort(404);
        }

        JavaScript::put([
            'jobData' => ['jobID' => $jobData->id],
        ]);

        return view(
            'pages.job',
            [
                'job' => $jobData,
                'slug' => $slug,
                'companyJobs' => JobListingsServiceProvider::getJobs(['perPage' => 3, 'filters' => ['company_id' => $jobData->company_id, 'skip_jobs' => [$jobData->id]]]),
            ]
        );
    }

    /**
     * Displays the individual job post create form.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $data['showLoginDialog'] = false;
        JavaScript::put([
            'showLoginDialog' => $data['showLoginDialog'],
        ]);
        JavaScript::put([
            'isAllowedToPost' => true,
            'mediaSettings' => [
                'allowed_file_extensions' => '.'.str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('videosFallback')),
                'max_file_upload_size' => (int) getSetting('media.max_file_upload_size'),
                'use_chunked_uploads' => (bool) getSetting('media.use_chunked_uploads'),
                'upload_chunk_size' => (int) getSetting('media.upload_chunk_size'),
            ],
            'draftData' => session('jobRequest'),
        ]);

        return view('pages.create', [
            'jobsMeta',
        ]);
    }

    /**
     * Validates Job create/edit request.
     * @param Request $request
     */
    public function validateJobData(Request $request)
    {
        $rules = JobCreateRequest::getRules();
        if (!$request->get('company_id')) {
            $rules = array_merge($rules, CompanyCreateRequest::getRules());
        }
        $request->validate($rules);
    }

    /**
     * Validates the job slug to be unique.
     * @param $request
     * @return array
     */
    public function validateJobSlug($request)
    {
        $companyName = $request->get('company_name');
        if ($request->get('company_id')) {
            $company = Company::select(['name'])->where('id', $request->get('company_id'))->where('user_id', Auth::user()->id)->first();
            $companyName = $company->name;
        }
        $jobSlug = JobListingsServiceProvider::slugify($companyName.' '.$request->get('title'));
        $slugValidation = JobListingsServiceProvider::validateJobSlug($jobSlug);

        return $slugValidation;
    }

    /**
     * Saves the JS draft data as an session object.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDraft(Request $request)
    {
        // Validate basic request data
        $this->validateJobData($request);
        // Plus, some manual validation
        $slugValidation = $this->validateJobSlug($request);
        if ($slugValidation['success'] == false) {
            return response()->json(['success' => false, 'message' => __('The given data was invalid.'), 'errors' => ['title' => $slugValidation['message']]], 422);
        }
        session(['jobRequest' => $request->all()]);

        return response()->json(['success' => true, 'message' => __('Job draft created.'), 'redirect' => route('jobs.preview')]);
    }

    /**
     * Clears session based draft data.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearDraft(Request $request)
    {
        try {
            session()->forget('jobRequest');

            return response()->json(['success' => true, 'message' => __('Draft cleared.')]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage(), 'trace' => $exception->getTrace()], 500);
        }
    }

    /**
     * Displays the job preview page.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function preview(Request $request)
    {
        // redirect to checkout
        return view('pages.preview', []);
    }

    /**
     * Displays the packages preview page during job create/upgrade flows.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function packages(Request $request)
    {
        $jobId = $request->get('jobID');
        $plans = Plan::where('status', Plan::ACTIVE_STATUS)->orderBy('order', 'ASC')->get();

        $defaultPlan = Plan::where('status', Plan::ACTIVE_STATUS)->where('default_plan', 1)->orderBy('order', 'ASC')->select('id')->first();
        $defaultPlanID = $defaultPlan ? $defaultPlan->id : null;

        $jobPlanId = null;
        if ($jobId) {
            $job = JobListing::where('id', $jobId)->first();
            if (!$job) {
                abort(404);
            }
            if ($job->plan) {
                $jobPlanId = $job->plan->id;
            }
        }

        JavaScript::put([
            'plans' => [
                'defaultPack' => $defaultPlanID,
                'updatingFromPack' => $jobPlanId,
                'data' => $plans,
            ],
        ]);

        return view('pages.packages', ['plans' => $plans, 'jobID' => $jobId]);
    }

    /**
     * Displays the checkout step during job post create/upgrade flows.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function checkout(Request $request)
    {
        $jobId = $request->get('jobID');
        $data['showLoginDialog'] = false;
        $data['jobID'] = $jobId;

        $jsVars = [
            'showLoginDialog' => $data['showLoginDialog'],
        ];

        $selectedPlanId = $request->cookie('selectedPack');
        if ($selectedPlanId) {
            $selectedPlanData = Plan::query()->where('id', $selectedPlanId)->first();
            if (!$selectedPlanData) {
                abort(404);
            }
            $data['selectedPlan'] = $selectedPlanData;
            $jsVars['selectedPlan'] = $selectedPlanData;
            $jsVars['selectedPlan']['has_payment_for_plan'] = $selectedPlanData->hasPaymentForPlan;
        }
        else{
            return redirect()->route('jobs.packages');
        }

        if ($jobId && Auth::check()) {
            $subscription = Subscription::query()
                ->where([
                    'user_id' => Auth::user()->id,
                    'job_id' => $jobId,
                ])
                ->orderBy('id', 'DESC')
                ->first();
            if ($subscription) {
                $activeSubscription = $subscription->status === Subscription::ACTIVE_STATUS && $subscription->expires_at > new \DateTime();
                $jsVars['currentSubscription'] = [
                    'active' => $activeSubscription,
                ];
            }
        }

        JavaScript::put($jsVars);
        $discount = 0;
        if($selectedPlanData->price){
            $discount = 100 - (($selectedPlanData->yearly_price * 100) / ($selectedPlanData->price * 12));
            if ($discount > 0) {
                $discount = round($discount);
            }
        }

        $data['discount'] = $discount;

        return view('pages.checkout', $data);
    }

    /**
     * Saves the job entry into database for job create/edit flows.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveJob(Request $request)
    {
        // Validate basic request data
        $this->validateJobData($request);
        // Plus, some manual validation
        $slugValidation = $this->validateJobSlug($request);
        if ($slugValidation['success'] == false) {
            return response()->json(['success' => false, 'errors' => ['title' => $slugValidation['message']], 'message' => $slugValidation['message']], 422);
        }

        try {
            // Creating the company, if necessary
            if ($request->get('company_id')) {
                $company = Company::where('id', $request->get('company_id'))->where('user_id', Auth::user()->id)->first();
            } else {
                $companyLogoDecoded = json_decode($request->get('company_logo'));
                $company = Company::create([
                    'user_id' => Auth::user()->id,
                    'name' => $request->get('company_name'),
                    'slug' => JobListingsServiceProvider::slugify($request->get('company_name')).rand(1111, 999999),
                    'hq' => $request->get('company_hq'),
                    'website_url' => $request->get('company_website_url'),
                    'email' => $request->get('company_email'),
                    'description' => $request->get('company_description'),
                ]);
                Attachment::where('id', $companyLogoDecoded->id)->update(['company_id' => $company->id]);
            }

            // Creating the job
            $slug = JobListingsServiceProvider::slugify($company->name.' '.$request->get('title')).rand(1111, 999999);
            $job = JobListing::create([
                'user_id' => Auth::user()->id,
                'title' => $request->get('title'),
                'slug' => $slug,
                'status' => JobListing::APPROVED_STATUS,
                'company_id' => $company->id,
                'category_id' => $request->get('category_id'),
                'application_link' => $request->get('application_link'),
                'description' => $request->get('description'),
                'salary' => $request->get('salary'),
                'location' => $request->get('location'),
                'type_id' => $request->get('type_id'),
            ]);

            // Creating job skills
            $this->saveJobSkills($request->get('skills'), $job->id);

            Session::flash('success', __('Job create successfully.'));

            return response()->json(['success' => true, 'message' => __('Job created.'), 'redirect' => route('jobs.get', ['slug' => $slug]), 'id' => $job->id]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage(), 'trace' => $exception->getTrace()], 500);
        }
    }

    /**
     * Saves the jobs skills of a job during save/edit.
     * @param $skillsData
     * @param $jobId
     */
    protected function saveJobSkills($skillsData, $jobId)
    {
        JobSkill::where('job_id', $jobId)->delete();
        $skills = json_decode($skillsData);
        $skillsData = [];
        foreach ($skills as $skill) {
            $skillID = Skill::select(['id'])->where('name', $skill)->first();
            $skillsData[] = ['skill_id' => $skillID->id, 'job_id' => $jobId];
        }
        JobSkill::insert($skillsData);
    }

    /**
     * Displays the individual job edit form.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editJob(Request $request)
    {
        $jobID = $request->route('jobID');
        $job = JobListing::where('id', $jobID)->where('user_id', Auth::user()->id)->first();
        if (!$job) {
            abort(404);
        }
        // TODO: Maybe handle this nicer
        $job->skills = $job->js_decoded_skills;
        JavaScript::put([
            'jobData' => $job,
        ]);

        return view('pages.my.jobs.edit', []);
    }

    /**
     * Saves jobs data during individual job editing.
     * @param JobCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    // TODO: Re-use some shared code with previous save method
    public function saveJobIndividual(JobCreateRequest $request)
    {
        try {
            $jobID = $request->get('id');
            $job = JobListing::where('id', $jobID)->where('user_id', Auth::user()->id)->first();
            if (!$job) {
                abort(404);
            }
            $job->update([
                'title' => $request->get('title'),
                'category_id' => $request->get('category_id'),
                'application_link' => $request->get('application_link'),
                'description' => $request->get('description'),
                'skills' => json_decode($request->get('skills')),
                'salary' => $request->get('salary'),
                'location' => $request->get('location'),
                'type_id' => $request->get('type_id'),
            ]);
            $this->saveJobSkills($request->get('skills'), $job->id);
            Session::flash('success', 'Job updated successfully.');

            return response()->json(['success' => true, 'message' => __('Job updated.'), 'redirect' => route('jobs.get', ['slug' => $job->slug])]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]], 500);
        }
    }

    /**
     * Deletes a job listing.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $jobID = $request->get('id');
        $job = JobListing::where('id', $jobID)->where('user_id', Auth::user()->id)->first();
        if ($job) {
            $job->delete();
            Session::flash('success', __('Job deleted successfully.'));

            return response()->json(['success' => true, 'message' => __('Job deleted successfully.')]);
        } else {
            return response()->json(['success' => false, 'error' => __('Job deleted successfully.')]);
        }
    }

    /**
     * Registers a unique applicant per click.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addApplicant(Request $request)
    {
        try {
            $jobID = $request->get('id');
            Applicant::create([
                'job_id' => $jobID,
                'signature' => JobListingsServiceProvider::generate2FaDeviceSignature(),
            ]);

            return response()->json(['success' => true, 'message' => __('Applicant registered.')]);
        } catch (\Exception $exception) {
            if ($exception->getCode() !== '23000') {
                return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
            }
//            return response()->json(['success' => false, 'message' => 'Applicant already registered'], 500);
        }
    }

    public function getAllSkills()
    {
        $skills = Skill::all();
        return response()->json($skills);
    }

    public function getAllCompanies()
    {
        $companies = \App\Model\Company::all();
        return response()->json($companies);
    }
}
