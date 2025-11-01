<?php

namespace App\Providers;

use App\Model\Company;
use App\Model\PublicPage;
use Illuminate\Support\ServiceProvider;

class SitemapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Generates a sitemap for user profiles and public pages.
     * @return bool
     */
    public static function generateSitemap()
    {
        $publicPages = PublicPage::all();
        $jobs = JobListingsServiceProvider::getJobs(['perPage' => false]);
        $companies = Company::get();

        $sitemapData = '
        <?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        // Jobs
        foreach ($jobs as $job) {
            $sitemapData .= '
           <url>
              <loc>'.route('jobs.get', ['slug'=>$job->slug]).'</loc>
              <lastmod>'.$job->updated_at->format('Y-m-d').'</lastmod>
              <changefreq>daily</changefreq>
              <priority>0.8</priority>
           </url>
            ';
        }

        // Companies
        foreach ($companies as $company) {
            $sitemapData .= '
           <url>
              <loc>'.route('company.get', ['slug' => $company->slug]).'</loc>
              <lastmod>'.$company->updated_at->format('Y-m-d').'</lastmod>
              <changefreq>daily</changefreq>
              <priority>0.8</priority>
           </url>
            ';
        }

        // Public pages
        foreach ($publicPages as $page) {
            $sitemapData .= '
           <url>
              <loc>'.route('pages.get', ['slug' => $page->slug]).'</loc>
              <lastmod>'.$page->updated_at->format('Y-m-d').'</lastmod>
              <changefreq>daily</changefreq>
              <priority>0.8</priority>
           </url>
            ';
        }

        $sitemapData .= '</urlset> ';
        file_put_contents(public_path('sitemap.xml'), trim($sitemapData));

        return true;
    }
}
