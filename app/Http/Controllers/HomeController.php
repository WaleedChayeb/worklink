<?php

namespace App\Http\Controllers;

use App\Model\BlogPost;
use App\Model\FeaturedCategory;
use App\Model\JobCategory;
use App\Model\JobListing;
use App\Providers\InstallerServiceProvider;
use App\Providers\JobListingsServiceProvider;
use App\Providers\MembersHelperServiceProvider;
use Illuminate\Support\Facades\Redirect;
use JavaScript;

class HomeController extends Controller
{
    /**
     * Displays the landing page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index()
    {
        if (!InstallerServiceProvider::checkIfInstalled()) {
            return Redirect::to(route('installer.install'));
        }

        $data = [];

        // If there's a custom site index
        if (getSetting('site.homepage_redirect')) {
            return redirect()->to(getSetting('site.homepage_redirect'), 301)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        } else {
            $featuredCategories = FeaturedCategory::orderBy('order', 'ASC')->get();
            if ($featuredCategories->count() === 0) {
                if (!getSetting('site.disable_featured_categories_on_homepage')) {
                    $categories = JobListing::select('category_id')->distinct()->get()->pluck('category_id')->toArray();
                    $featuredCategories = JobCategory::whereIn('id', $categories)->get();
                } else {
                    $data['jobs'] = JobListingsServiceProvider::getJobs([
                        'perPage' => 12,
                        'filters' => [], // Needs raw filters, parsing is done again in there
                    ]);
                }
            }
            $featuredCategoriesListings = [];
            foreach ($featuredCategories as $category) {
                $listingsData = JobListingsServiceProvider::getJobs(['perPage' => 3, 'filters' => ['category_id' => isset($category->category->id) ? $category->category->id : $category->id]]);
                $featuredCategoriesListings[] = [
                    'category' => $category,
                    'listings' => $listingsData,
                ];
            }

            $data['featuredCategoriesListings'] = $featuredCategoriesListings;
            $data['pinnedJobListings'] = JobListingsServiceProvider::getPinnedJobs();
            $data['articles'] = $this->getArticles((isset($data['latestPost']) ? $data['latestPost'] : false), 6);
            return view('pages.home', $data);
        }
    }
    public function getArticles($latestPost, $pageNumber = 6)
    {
        // Getting the optional tag url param
        $excludedPosts = [];
        if ($latestPost) {
            $excludedPosts[] = $latestPost->id;
        }
        $articles = BlogPost::orderBy('created_at', 'desc')
            ->whereNotIn('id', $excludedPosts)
            ->whereIn('status', session('isAdmin') ? [0, 1] : [BlogPost::PUBLISHED_STATUS])
            ->paginate($pageNumber);
        return $articles;
    }
}
