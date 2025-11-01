<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\SchemaOrg\ContactPage;
use Spatie\SchemaOrg\JobPosting;
use Spatie\SchemaOrg\Organization;
use Spatie\SchemaOrg\Place;
use Spatie\SchemaOrg\PostalAddress;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\SearchResultsPage;
use Spatie\SchemaOrg\WebPage;

class SchemaOrgServiceProvider extends ServiceProvider
{
    // Public pages
    // https://schema.org/ContactPage
    // https://schema.org/WebPage
    public static function webPage($data)
    {
        $schema = new WebPage();
        $schema->abstract($data->title);

        return $schema;
    }

    // https://schema.org/ContactPage
    public static function contactPage($data)
    {
        $schema = new ContactPage();
        $schema->abstract($data->title);
        $schema->description($data->description);
        $schema->name($data->title);

        return $schema;
    }

    // https://schema.org/SearchResultsPage
    public static function searchResultsPage($data)
    {
        $schema = new SearchResultsPage();
        $schema->abstract($data->title);
        $schema->description($data->description);

        return $schema;
    }

    // https://schema.org/JobPosting
    public static function jobPosting($data)
    {
        // Create a PostalAddress object with the location
        $jobAddress = Schema::postalAddress()
            ->addressLocality($data->location ?? null);

        // Create a Place object and set the address
        $jobLocation = Schema::place()->address($jobAddress);

        // Create the JobPosting schema
        $schema = Schema::jobPosting()
            ->title($data->title)
            ->jobLocation($jobLocation)  // Assign Place to jobLocation
            ->employmentType($data->type->name)
            ->hiringOrganization(self::organization($data->company))
            ->skills($data->skills_human_format)
            ->description(self::getDescriptionExcerpt(strip_tags($data->description)))
            ->estimatedSalary($data->salary)
            ->datePosted($data->created_at->format('Y-m-d'))
            ->directApply('false')
            ->url(route('jobs.get', ['slug' => $data->slug]))
            ->logo($data->company->logo);

        if($data->activeSubscription){
            $schema->validThrough($data->activeSubscription->expires_at->format('Y-m-d'));
        }

        return $schema;
    }

    // https://schema.org/ProfilePage
    public static function profilePage()
    {
        // TODO: Prep it for JF
    }

    // https://schema.org/Organization
    public static function organization($data)
    {
        $schema = new Organization();
        $schema->logo($data->logo);
        $schema->name($data->name);
        $schema->description(self::getDescriptionExcerpt(strip_tags($data->description)));
        $schema->location($data->hq);
        $schema->url($data->website_url);

        return $schema;
    }

    public static function homepage()
    {
        $schema = new Organization();
        $schema->name(getSetting('site.name'));
        $schema->description(self::getDescriptionExcerpt(getSetting('site.description')));
        $schema->url(getSetting('site.app_url'));
        $schema->sameAs(self::getAvailableSocialNetworks());

        return $schema;
    }

    /**
     * Get available social networks set up (for advertising, not social-logins)
     * so it can be used on schema.org definitions.
     * @return array
     */
    public static function getAvailableSocialNetworks()
    {
        $data = array_values(array_filter([
            getSetting('social.facebook_url'),
            getSetting('social.twitter_url'),
            getSetting('social.instagram_url'),
            getSetting('social.whatsapp_url'),
            getSetting('social.tiktok_url'),
            getSetting('social.youtube_url'),
            getSetting('social.telegram_link'),
            getSetting('social.reddit_url'),
        ]));

        return json_encode($data);
    }

    /**
     * Generates truncated descriptions.
     * @param $description
     * @return string
     */
    public static function getDescriptionExcerpt($description)
    {
        $description = strip_tags($description);

        return strlen($description) > 250 ? $description.'...' : $description;
    }

    public static function getBlogPostSchema($post) {
        return Schema::BlogPosting()
            ->headline($post->title)
            ->url(route('blog.post.get', ['slug' => $post->slug]))
            ->image(GenericHelperServiceProvider::getStorageAssetLink($post->cover))
            ->articleBody($post->content)
            ->keywords($post->tags)
            ->datePublished($post->created_at) // Assuming the current date as published date
            ->author(
                Schema::Person()
                    ->name($post->user->name)
                    ->url(route('blog.post.get', ['slug' => $post->slug]))
            );
    }
}
