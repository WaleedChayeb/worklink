<?php

namespace App\Providers;

use App\Model\ApplicantsRange;
use App\Model\Company;
use App\Model\JobCategory;
use App\Model\JobListing;
use App\Model\JobType;
use App\Model\Plan;
use App\Model\SalaryRange;
use App\Model\Skill;
use App\Model\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Str;

class JobListingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Returns available job categories.
     * @return JobCategory[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAvailableCategories()
    {
        $data = JobCategory::all();

        return $data;
    }

    /**
     * Returns available job skills.
     * @return Skill[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAvailableSkills()
    {
        $data = Skill::all();

        return $data;
    }

    /**
     * Returns available job types.
     * @return JobType[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAvaialableJobTypes()
    {
        $data = JobType::all();
        return $data;
    }

    /**
     * Returns available salary ranges.
     * @return SalaryRange[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAvailableSalaryRanges()
    {
        $data = SalaryRange::all();

        return $data;
    }

    /**
     * Returns available applicants ranges.
     * @return SalaryRange[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAvailableApplicantsRanges()
    {
        $data = ApplicantsRange::all();

        return $data;
    }

    /**
     * Validates the job slug.
     * @param $slug
     * @return array
     */
    public static function validateJobSlug($slug)
    {
        if (!$slug) {
            return ['success' => false, 'message' => __('Job url can not be generated. Make sure to use alphanumeric characters for the job and company title.')];
        }
        if (JobListing::where('slug', $slug)->count()) {
            return ['success' => false, 'message' => __('Job slug could not be generated. Please try a different job name.')];
        }

        return ['success' => true];
    }

    /**
     * Check if a string contains non-ascii chars.
     * @param $string
     * @return bool
     */
    public static function checkIfStringIsASCII($string) {
        if(preg_match('/[^\x20-\x7e]/', $string)){
            return false;
        }
        return true;
    }

    /**
     * Attempts to get language code based on a string content.
     * @param $string
     * @return \LanguageDetector\Language|string
     */
    public static function getLanguageCodeFromString($string) {
        $detector = new \LanguageDetector\LanguageDetector();
        $language = $detector->evaluate($string)->getLanguage();
        if($language){
            return $language;
        }
        return 'en';
    }

    /**
     * Enhanced slugify, handles non-ASCII languages as well.
     * @param $string
     * @return mixed
     */
    public static function slugify($string) {
        $language = 'en';
        if(!self::checkIfStringIsASCII($string)){
            $language = self::getLanguageCodeFromString($string);
        }
        return Str::slug($string, '-', $language);
    }

    /**
     * Serializes draft session data object to eloquent like tree
     * Used for job creation - preview step.
     * @param $data
     * @param string $type
     * @return array|object
     */
    public static function parseJobDraftSessionData($data, $type = 'job')
    {
        $jobData = [];
        $companyData = [];
        foreach ($data as $key => $row) {
            if (!is_int(strpos($key, 'company_'))) {
                $jobData[$key] = $row;
            } else {
                $companyData[str_replace('company_', '', $key)] = $row;
            }
        }

        // Altering Job data
        // TODO: Use a function and re-use within jobs model ::decodedSkills
        $skills = [];
        foreach (json_decode($jobData['skills']) as $skill) {
            $skills[] = (object) ['name' => $skill];
        }
        $jobData['decodedSkills'] = $skills;
        $jobData['slug'] = '#';
        $jobData['created_at'] = Carbon::now();
        $jobType = JobType::where('id', $jobData['type_id'])->first();
        if($jobType){
            $jobData['type'] = (object) collect($jobType)->toArray();
        }
        else{
            $jobData['type'] = (object)['id' => null];
        }

        if (isset($companyData['id']) && $companyData['id']) {
            $companyData = Company::withCount(['jobs'])->with('logoAttachment')->where('id', $companyData['id'])->first();
            $companyData->logo = ''; // Fake accessor to init the virtual attributes aka harry potter
            $companyData = $companyData->toArray();
            $companyData['jobs'] = array_fill(0, $companyData['jobs_count'], 1);
            $companyData = (object) $companyData;
        } else {
            $companyData['logo'] = json_decode($companyData['logo']);
            $companyData['logo'] = $companyData['logo']->path;
            $companyData['slug'] = '#';
            $companyData['jobs'] = [0];
            $companyData = (object) collect($companyData)->toArray();
        }
        // Adding company data to job data
        $jobData['company'] = $companyData;
        $jobData = (object) collect($jobData)->toArray();

        if ($type == 'job') {
            return $jobData;
        } elseif ($type == 'company') {
            return $companyData;
        }
    }

    /**
     * Normalizes job search params.
     * @param $rawFilters
     * @return array
     */
    public static function parseSearchFilters($rawFilters)
    {
        $filters = [
            'terms' => isset($rawFilters, $rawFilters['terms']) ? $rawFilters['terms'] : false,
            'category_id' => isset($rawFilters, $rawFilters['category_id']) ? ($rawFilters['category_id'] !== 'all' ? $rawFilters['category_id'] : false) : false,
            'location' => isset($rawFilters, $rawFilters['location']) ? $rawFilters['location'] : false,
            'type_id' => isset($rawFilters, $rawFilters['type_id']) ? ($rawFilters['type_id'] !== 'all' ? $rawFilters['type_id'] : false) : false,
            'sort_range' => isset($rawFilters, $rawFilters['sort_range']) ? ($rawFilters['sort_range'] !== 'all' ? $rawFilters['sort_range'] : false) : false,
            'skills' => isset($rawFilters, $rawFilters['skills']) ? $rawFilters['skills'] : [],
            'applicants_number' => isset($rawFilters, $rawFilters['applicants_number']) ? ($rawFilters['applicants_number'] !== 'all' ? $rawFilters['applicants_number'] : false) : false,
            'company_id' => isset($rawFilters, $rawFilters['company_id']) ? $rawFilters['company_id'] : [],
            'skip_jobs' => isset($rawFilters, $rawFilters['skip_jobs']) ? $rawFilters['skip_jobs'] : [],
        ];

        return $filters;
    }

    /**
     * Fetches jobs based on different criteria - used on homepage & other listing areas.
     * @param array $options
     * @return mixed
     */
    public static function getJobs($options = [])
    {
        $perPage = isset($options['perPage']) ? $options['perPage'] : 9;
        $filters = self::parseSearchFilters(isset($options['filters']) ? $options['filters'] : []);
        $jobs = JobListing::select(['jobs.*', 'plans.highlight_ad'])
            ->leftJoin(DB::raw('(SELECT job_id, MAX(created_at) as latest_sub_created_at FROM subscriptions GROUP BY job_id) as latest_sub'), function ($join) {
                $join->on('jobs.id', '=', 'latest_sub.job_id');
            })
            ->leftJoin('subscriptions', function ($join) {
                $join->on('subscriptions.job_id', '=', 'jobs.id')
                    ->on('subscriptions.created_at', '=', 'latest_sub.latest_sub_created_at');
            })
            ->leftJoin('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->leftJoin('companies', 'jobs.company_id', '=', 'companies.id')
            ->whereNotNull('plans.id') // filter jobs w/o a plan/sub
            ->whereIn('subscriptions.status', [Subscription::ACTIVE_STATUS, Subscription::CANCELED_STATUS])
            ->whereDate('subscriptions.expires_at', '>', Carbon::now())
            ->orderByDesc('highlight_ad')
            ->orderByDesc('jobs.created_at');

        if ($filters['terms']) {
            // Might take a small hit on performance
            $jobs->where(function ($query) use ($filters) {
                $query->where('jobs.title', 'like', '%'.$filters['terms'].'%');
                $query->orWhere('jobs.description', 'like', '%'.$filters['terms'].'%');
            });
        }

        if ($filters['category_id']) {
            $jobs->where('category_id', $filters['category_id']);
        }

        if ($filters['location']) {
            $jobs->where('location', 'LIKE', '%'.$filters['location'].'%');
        }

        if ($filters['type_id']) {
            $jobs->where('jobs.type_id', $filters['type_id']);
        }

        if ($filters['sort_range'] && $filters['sort_range'] !== 1) {
            $range = config('app.site.jobs.sort_ranges');
            $rangeInterval = null;
            foreach ($range as $interval) {
                if ($interval['id'] == $filters['sort_range']) {
                    $rangeInterval = $interval;
                }
            }
            $jobs->whereDate('jobs.created_at', '>=', Carbon::today()->subDays($rangeInterval['interval']));
        }

        if ($filters['skills']) {
            $skillIDs = Skill::select(['id'])->whereIn('name', $filters['skills'])->get()->pluck('id');
            $jobs->whereHas('jobSkills', function ($q) use ($skillIDs) {
                $q->whereIn('skill_id', $skillIDs);
            });
        }

        if ($filters['applicants_number']) {
            $range = ApplicantsRange::where('id', $filters['applicants_number'])->first();
            $jobs->withCount('applicants');
            if (!$range->min_range && $range->max_range) {
                // apply >max
                $jobs->having('applicants_count', '>=', $range->max_range);
            } else {
                // regular interval
                $jobs->having('applicants_count', '>=', $range->min_range);
                $jobs->having('applicants_count', '<', $range->max_range);
            }
        }

        if ($filters['company_id']) {
            $jobs->where('companies.id', $filters['company_id']);
        }

        if ($filters['skip_jobs']) {
            $jobs->whereNotIn('jobs.id', $filters['skip_jobs']);
        }

        if ($perPage) {
            $jobs = $jobs->paginate($perPage);
        } else {
            $jobs = $jobs->get();
        }

        return $jobs;
    }

    /**
     * Generates new string for current addr&agent.
     * @return string
     */
    public static function generate2FaDeviceSignature()
    {
        return sha1(request()->ip().request()->header('User-Agent'));
    }

    /**
     * Gets list of jobs pinned to homepage.
     * @return mixed
     */
    public static function getPinnedJobs()
    {
        $homepagePinnedPlans = Plan::select(['id'])->where('main_page_pin', 1)->get()->pluck('id')->toArray();
        $jobs = JobListing::select(['jobs.*', 'plans.highlight_ad'])
            ->leftJoin('subscriptions', 'subscriptions.job_id', '=', 'jobs.id')
            ->leftJoin('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->whereRaw('plans.id IS NOT NULL') // filter jobs w/o a plan/sub
            ->where(function ($query) {
                $query->whereIn('subscriptions.status', [Subscription::ACTIVE_STATUS, Subscription::CANCELED_STATUS]);
            })
            ->whereDate('subscriptions.expires_at', '>', Carbon::now())
            ->whereIn('plans.id', $homepagePinnedPlans)
            ->orderByDesc('created_at')
            ->get();

        return $jobs;
    }
}
