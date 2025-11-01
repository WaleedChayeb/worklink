<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyCreateRequest;
use App\Model\Attachment;
use App\Model\Company;
use App\Model\Post;
use App\Model\UserList;
use App\Providers\AttachmentServiceProvider;
use App\Providers\CompanyServiceProvider;
use App\Providers\JobListingsServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use JavaScript;

class CompanyController extends Controller
{
    /**
     * Displays the public company page.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getCompany(Request $request)
    {
        $slug = $request->route('slug');
        $companyData = Company::withCount(['jobs'])->where('slug', $slug)->first();
        if (!$companyData) {
            abort(404);
        }

        return view('pages.company', [
            'company' => $companyData,
            'companyJobs' => JobListingsServiceProvider::getJobs(['perPage' => 3, 'filters' => ['company_id' => $companyData->id]]),
            'slug' => $slug,
        ]);
    }

    /**
     * Displays the list of all user companies.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getMyCompanies(Request $request)
    {
        $companies = Company::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(8);

        return view('pages.companies', [
            'companies' => $companies,
        ]);
    }

    /**
     * Displays the individual company create form.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        JavaScript::put([
            'isAllowedToPost' => true,
            'mediaSettings' => [
                'allowed_file_extensions' => '.'.str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('videosFallback')),
                'max_file_upload_size' => (int) getSetting('media.max_file_upload_size'),
                'use_chunked_uploads' => (bool) getSetting('media.use_chunked_uploads'),
                'upload_chunk_size' => (int) getSetting('media.upload_chunk_size'),
            ],
        ]);

        return view('pages.my.companies.create', []);
    }

    /**
     * Displays the individual company edit form.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Request $request)
    {
        $companyID = $request->route('companyID');
        $company = Company::with(['logoAttachment'])->where('id', $companyID)->where('user_id', Auth::user()->id)->first();
        if (!$company) {
            abort(404);
        }

        JavaScript::put([
            'isAllowedToPost' => true,
            'mediaSettings' => [
                'allowed_file_extensions' => '.'.str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('videosFallback')),
                'max_file_upload_size' => (int) getSetting('media.max_file_upload_size'),
                'use_chunked_uploads' => (bool) getSetting('media.use_chunked_uploads'),
                'upload_chunk_size' => (int) getSetting('media.upload_chunk_size'),
            ],
            'companyData' => $company,
        ]);

        return view('pages.my.companies.create', []);
    }

    /**
     * Used for saving company data during create and edit operations.
     * @param CompanyCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(CompanyCreateRequest $request)
    {
        $companyLogoDecoded = json_decode($request->get('company_logo'));
        try {
            $type = $request->route('type');
            $data = [
                'user_id' => Auth::user()->id,
                'name' => $request->get('company_name'),
                'hq' => $request->get('company_hq'),
                'website_url' => $request->get('company_website_url'),
                'email' => $request->get('company_email'),
                'description' => $request->get('company_description'),
            ];
            if ($type == 'create') {
                // Plus, some manual validation
                $slugValidation = CompanyServiceProvider::validateCompanySlug(JobListingsServiceProvider::slugify($request->get('company_name')));
                if ($slugValidation['success'] == false) {
                    return response()->json(['success' => false, 'message' => __('The given data was invalid.'), 'errors' => ['company_name' => $slugValidation['message']]], 422);
                }
                $data['slug'] = JobListingsServiceProvider::slugify($request->get('company_name'));
                $company = Company::create($data);
                Attachment::where('id', $companyLogoDecoded->id)->update(['company_id' => $company->id]);
                Session::flash('success', __('Company created successfully.'));

                return response()->json(['success' => true, 'message' => __('Company created.'), 'redirect' => route('company.get', ['slug' => $company->slug])]);
            } elseif ($type == 'edit') {
                $companyID = $request->get('id');
                $company = Company::where('id', $companyID)->first();
                if ($company) {
                    $company->update($data);
                    Attachment::where('company_id', $company->id)->delete();
                    Attachment::where('id', $companyLogoDecoded->id)->update(['company_id' => $company->id]);
                    Session::flash('success', __('Company updated successfully.'));

                    return response()->json(['success' => true, 'message' => __('Company updated.'), 'redirect' => route('company.get', ['slug' => $company->slug])]);
                } else {
                    return response()->json(['success' => false, 'errors' => [__('Not authorized')], 'message' => __('Post not found')], 403);
                }
            }
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]]);
        }
    }

    /**
     * Company delete endpoint.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $companyID = $request->get('id');

        $company = Company::where('id', $companyID)->where('user_id', Auth::user()->id)->first();
        if ($company) {
            // Delete all jobs and attachments
            $company->jobs()->delete();
            $company->delete();
            $attachment = Attachment::where('company_id', $companyID)->first();
            if ($attachment) {
                $attachment->delete();
            }
            Session::flash('success', 'Company deleted successfully.');

            return response()->json(['success' => true, 'message' => __('Company deleted successfully.')]);
        } else {
            return response()->json(['success' => false, 'error' => __('Company deleted successfully.')]);
        }
    }

    /**
     * Returns list of companies encoded for selctize payloads.
     * @return mixed
     */
    public function getSelectizedCompanies()
    {
        $values = [
            'users' => [],
        ];
        $companies = Company::where('user_id', Auth::user()->id)->orderBy('id', 'ASC')->get();
        foreach ($companies as $k => $user) {
            $values['users'][$user->id]['id'] = $user->id;
            $values['users'][$user->id]['name'] = $user->name;
            $values['users'][$user->id]['avatar'] = $user->logo;
            $values['users'][$user->id]['label'] = '<div><img class="searchAvatar" src="uploads/users/avatars/'.$user->logo.'" alt=""><span class="name">'.$user->name.'</span></div>';
        }

        return $values['users'];
    }
}
