<?php

namespace App\Providers;

use App\Http\Requests\SaveNewContactMessageRequest;
use App\Model\ContactMessage;
use App\Model\FeaturedClient;
use App\Model\GlobalAnnouncement;
use App\Model\JobSkill;
use App\Model\Plan;
use App\Model\PublicPage;
use App\Model\Skill;
use App\Model\Subscription;
use App\Model\UserReport;
use App\User;
use Carbon\Carbon;
use Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;
use Mews\Purifier\Facades\Purifier;
use Ramsey\Uuid\Uuid;

class GenericHelperServiceProvider extends ServiceProvider
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
     * Check if user meets all ID verification steps.
     * @return bool
     */
    public static function isUserVerified()
    {
        if (
            (Auth::user()->verification && Auth::user()->verification->status == 'verified') &&
            Auth::user()->birthdate &&
            Auth::user()->email_verified_at
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Creates a default wallet for a user.
     * @param $user
     */
    public static function createUserWallet($user)
    {
        try {
            $userWallet = Wallet::query()->where('user_id', $user->id)->first();
            if ($userWallet == null) {
                // generate unique id for wallet
                do {
                    $id = Uuid::uuid4()->getHex();
                } while (Wallet::query()->where('id', $id)->first() != null);

                $balance = 0.0;
                if (getSetting('profiles.default_wallet_balance_on_register') && getSetting('profiles.default_wallet_balance_on_register') != 0) {
                    $balance = getSetting('profiles.default_wallet_balance_on_register');
                }
                Wallet::create([
                    'id' => $id,
                    'user_id' => $user->id,
                    'total' => $balance,
                ]);
            }
        } catch (\Exception $exception) {
            Log::error('User wallet creation error: '.$exception->getMessage());
        }
    }

    /**
     * Static function that handles remote storage drivers.
     *
     * @param $value
     * @return string
     */
    public static function getStorageAvatarPath($value) {
        if($value && $value !== config('voyager.user.default_avatar', '/img/default-avatar.png')){
            return self::getStorageAssetLink($value);
        }else{
            return str_replace('storage/', '', asset(config('voyager.user.default_avatar', '/img/default-avatar.png')));
        }
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public static function getStorageAssetLink($value) {
        if (getSetting('storage.driver') == 's3') {
            if (getSetting('storage.aws_cdn_enabled') && getSetting('storage.aws_cdn_presigned_urls_enabled')) {
                $fileUrl = AttachmentServiceProvider::signAPrivateDistributionPolicy(
                    'https://'.getSetting('storage.cdn_domain_name').'/'.$value
                );
            } elseif (getSetting('storage.aws_cdn_enabled')) {
                $fileUrl = 'https://'.getSetting('storage.cdn_domain_name').'/'.$value;
            } else {
                $fileUrl = 'https://'.getSetting('storage.aws_bucket_name').'.s3.'.getSetting('storage.aws_region').'.amazonaws.com/'.$value;
            }
            return $fileUrl;
        }
        elseif(getSetting('storage.driver') == 'wasabi' || getSetting('storage.driver') == 'do_spaces'){
            return Storage::url($value);
        }
        elseif(getSetting('storage.driver') == 'minio'){
            return rtrim(getSetting('storage.minio_endpoint'), '/').'/'.getSetting('storage.minio_bucket_name').'/'.$value;
        }
        elseif(getSetting('storage.driver') == 'pushr'){
            return rtrim(getSetting('storage.pushr_cdn_hostname'), '/').'/'.$value;
        }
        else{
            return Storage::disk('public')->url($value);
        }
    }

    /**
     * Helper to detect mobile usage.
     * @return bool
     */
    public static function isMobileDevice()
    {
        $agent = new Agent();

        return $agent->isMobile();
    }

    /**
     * Returns true if email enforce is not enabled or if is set to true and user is verified.
     * @return bool
     */
    public static function isEmailEnforcedAndValidated()
    {
        return (Auth::check() && Auth::user()->email_verified_at) || (Auth::check() && !getSetting('site.enforce_email_validation'));
    }

    public static function parseProfileMarkdownBio($bio)
    {
        if (getSetting('profiles.allow_profile_bio_markdown')) {
            $parsedOutput = Purifier::clean(Markdown::convert($bio)->getContent());

            return $parsedOutput;
        }

        return $bio;
    }

    /**
     * Fetches list of all public pages to be show in footer.
     * @return mixed
     */
    public static function getFooterPublicPages()
    {
        $pages = [];
        if (InstallerServiceProvider::checkIfInstalled()) {
            $pages = PublicPage::where('shown_in_footer', 1)->orderBy('page_order')->get();
        }

        return $pages;
    }

    /**
     * Fetches the default OGMeta image to be used (except for profile).
     * @return \Illuminate\Config\Repository|mixed|string|null
     */
    public static function getOGMetaImage()
    {
        if (getSetting('site.default_og_image')) {
            return getSetting('site.default_og_image');
        }

        return asset('img/logo-wide-light.png');
    }

    /**
     * Gets site direction. If rtl cookie not set, defaults to site setting.
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public static function getSiteDirection()
    {
        if (is_null(Cookie::get('app_rtl'))) {
            return getSetting('site.default_site_direction');
        }

        return Cookie::get('app_rtl');
    }

    /**
     * Returns list of report types.
     * @return array
     */
    public static function getReportTypes()
    {
        return UserReport::$typesMap;
    }

    /**
     * Fetches list of most popular tags - belonging to active listings.
     * @param int $limit
     * @return mixed
     */
    public static function getPopularTags($limit = 10)
    {
        $skills = JobSkill::selectRaw('job_skills.skill_id, COUNT(*) AS jobsCount')
            ->leftJoin('skills', 'skills.id', '=', 'job_skills.skill_id')
            ->leftJoin('jobs', 'jobs.id', '=', 'job_skills.job_id')
            ->leftJoin('subscriptions', 'subscriptions.job_id', '=', 'jobs.id')
            ->where(function ($query) {
                $query->whereIn('subscriptions.status', [Subscription::ACTIVE_STATUS, Subscription::CANCELED_STATUS]);
            })
            ->whereDate('subscriptions.expires_at', '>', Carbon::now())
            ->groupBy('skill_id')
            ->orderByDesc('jobsCount')
            ->limit($limit)
            ->get()
            ->pluck('skill_id')
            ->toArray();
        $skills = Skill::whereIn('id', $skills)->get();

        return $skills;
    }

    public static function getAvailableFeaturedClients()
    {
        return FeaturedClient::orderBy('order', 'ASC')->get();
    }

    public static function getAvailableCompanies()
    {
        return FeaturedClient::get();
    }

    public static function getMinimumPackPrice()
    {
        if(Plan::count()){
            $price = min(Plan::get()->pluck('price')->toArray());
            if ($price) {
                return $price;
            }
        }

        return 0;
    }

    public static function getBlogArticleExcerpt($content, $charLimit) {
        $cleanedContent = strip_tags(Purifier::clean($content));
        $excerpt = substr($cleanedContent, 0, $charLimit);
        if(strlen($cleanedContent) > $charLimit){
            $excerpt .= '...';
        }
        return $excerpt;
    }

    public static function getCleanedBlogContent($content) {
        return Purifier::clean($content);
    }

    public static function getUserFirstCompanyLink($user) {
        $company = $user->companies()->first();
        if($company){
            return route('company.get', ['slug' => $company->slug]);
        }
        return '#';
    }

    public static function getLatestGlobalMessage() {
        if (!Schema::hasTable('global_announcements')) {
            // Return an empty collection or array if the table doesn't exist
            return null;
        }

        $messages = GlobalAnnouncement::all();
        $skippedIDs = [];

        foreach($messages as $message){
            if(request()->cookie('dismissed_banner_'.$message->id)){
                $skippedIDs[] = $message->id;
            }
        }

        $message = GlobalAnnouncement::orderBy('created_at', 'desc')
            ->where('is_published', 1)
            ->whereNotIn('id', $skippedIDs)
            ->first();

        return $message;
    }

    /**
     * Returns the preferred user local
     * TODO: This is only used in the payments module | Maybe delete it and use LocaleProvider based alternative.
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public static function getPreferredLanguage() {
        // Defaults
        if (!Session::has('locale')) {
            if (InstallerServiceProvider::checkIfInstalled()) {
                return getSetting('site.default_site_language');
            } else {
                return Config::get('app.locale');
            }
        }
        // If user has locale setting, use that one
        if (isset(Auth::user()->settings['locale'])) {
            return Auth::user()->settings['locale'];
        }
        return getSetting('site.default_site_language');
    }

    public static function getSiteTheme() {
        $mode = Cookie::get('app_theme');
        if(!$mode){
            $mode = getSetting('site.default_user_theme');
        }
        return $mode;
    }
}
