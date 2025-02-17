<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRatesSettingsRequest;
use App\Http\Requests\VerifyProfileAssetsRequest;
use App\Model\CreatorOffer;
use App\Model\JobCategory;
use App\Providers\JobListingsServiceProvider;
use Illuminate\Http\Request;
use JavaScript;
use Str;

class SearchController extends Controller
{
    /**
     * Displays the public search page.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $filters = JobListingsServiceProvider::parseSearchFilters($request->all());
        $jobs = JobListingsServiceProvider::getJobs([
            'perPage' => 5,
            'filters' => $request->all(), // Needs raw filters, parsing is done again in there
        ]);
        JavaScript::put([
            'selectedSkills' => $filters['skills'],
        ]);

        return view('pages.search', [
            'jobs' => $jobs,
            'filters' => $filters,
        ]);
    }

    /**
     * Displays the (SEO-indexable) browse page.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function browse(Request $request)
    {
        $slug = $request->route('slug');

        if($slug === 'all'){
            $category = new JobCategory();
            $category->name = __("all_categories_label");
            $category->slogan = __("all_categories_slogan");
            $category->description = __("all_categories_slogan");
        }
        else{
            $categories = JobCategory::orderByDesc('created_at')->get();
            $category = false;
            foreach ($categories as $item) {
                if ($slug == Str::slug($item->name)) {
                    $category = $item;
                }
            }
        }

        if (!$category) {
            abort(404);
        }
        $filters = [];
        if($slug !== 'all'){
            $filters['category_id'] = $category->id;
        }
        $filters = JobListingsServiceProvider::parseSearchFilters($filters);
        $jobs = JobListingsServiceProvider::getJobs([
            'perPage' => 6,
            'filters' => $filters, // Needs raw filters, parsing is done again in there
        ]);

        return view('pages.browse', [
            'jobs' => $jobs,
            'filters' => $filters,
            'category' => $category,
            'slug' => $slug,
        ]);
    }
}
