<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V180 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // caprcha driver
        DB::table('settings')->insert(
            array(
                'key' => 'security.captcha_driver',
                'display_name' => 'Captcha driver',
                'value' => 'none',
                'details' => '{
"default" : "pusher",
"options" : {
"none": "None",
"turnstile": "Turnstile",
"hcaptcha": "hCaptcha",
"recaptcha": "reCaptcha"
}
}',
                'type' => 'select_dropdown',
                'order' => 79,
                'group' => 'Security',
            )
        );



        // captcha drivers fields
        DB::table('settings')->insert(
            array(
                array (
                    'key' => 'security.turnstile_site_key',
                    'display_name' => 'Turnstile Site Key',
                    'value' => '',
                    'type' => 'text',
                    'order' => 1230,
                    'group' => 'Security',
                )
            )
        );

        DB::table('settings')->insert(
            array(
                array (
                    'key' => 'security.turnstile_site_secret_key',
                    'display_name' => 'Turnstile Secret Key',
                    'value' => '',
                    'type' => 'text',
                    'order' => 1240,
                    'group' => 'Security',
                )
            )
        );

        DB::table('settings')->insert(
            array(
                array (
                    'key' => 'security.hcaptcha_site_key',
                    'display_name' => 'hCaptcha Site Key',
                    'value' => '',
                    'type' => 'text',
                    'order' => 1250,
                    'group' => 'Security',
                )
            )
        );

        DB::table('settings')->insert(
            array(
                array (
                    'key' => 'security.hcaptcha_site_secret_key',
                    'display_name' => 'hCaptcha Secret Key',
                    'value' => '',
                    'type' => 'text',
                    'order' => 1260,
                    'group' => 'Security',
                )
            )
        );

        // if recaptcha was on - set default driver to recaptcha and drop that column
        if(getSetting('security.recaptcha_enabled')){
            DB::table('settings')
                ->where('key', 'security.recaptcha_enabled')
                ->update([
                    'value' => 'reCaptcha',
                ]);
        }

        DB::table('settings')
            ->whereIn('key', [
                'security.recaptcha_enabled',
            ])
            ->delete();


// Merge the 'Social login' and 'Social links' groups into 'Social'
        DB::table('settings')
            ->whereIn('group', ['Social login', 'Social media'])
            ->update(['group' => 'Social']);

        // Update keys starting with 'social-login.' to 'social.'
        DB::table('settings')
            ->where('key', 'like', 'social-login.%')
            ->update([
                'key' => DB::raw("REPLACE(`key`, 'social-login.', 'social.')"),
            ]);

        // Update keys starting with 'social-links.' to 'social.'
        DB::table('settings')
            ->where('key', 'like', 'social-media.%')
            ->update([
                'key' => DB::raw("REPLACE(`key`, 'social-media.', 'social.')"),
            ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')
            ->whereIn('key', [
                'security.captcha_driver',
                'security.turnstile_site_key',
                'security.turnstile_site_secret_key',
                'security.hcaptcha_site_key',
                'security.hcaptcha_site_secret_key'
            ])
            ->delete();

        DB::table('settings')->insert(
            array(
                array (
                    'key' => 'security.recaptcha_enabled',
                    'display_name' => 'Enable Google reCAPTCHA',
                    'value' => NULL,
                    'details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description": "If enabled, it will be used on all public form pages."
}',
                    'type' => 'checkbox',
                    'order' => 1200,
                    'group' => 'Security',
                ),
            )
        );

        // Revert keys and groups for 'social-login' settings
        DB::table('settings')
            ->whereIn('key', [
                'social.facebook_client_id',
                'social.facebook_secret',
                'social.twitter_client_id',
                'social.twitter_secret',
                'social.google_client_id',
                'social.google_secret',
            ])
            ->update([
                'key' => DB::raw("REPLACE(`key`, 'social.', 'social-login.')"),
                'group' => 'Social login',
            ]);

        // Revert keys and groups for 'social-login' settings
        DB::table('settings')
            ->whereIn('key', [
                'social.facebook_client_id',
                'social.facebook_secret',
                'social.twitter_client_id',
                'social.twitter_secret',
                'social.google_client_id',
                'social.google_secret',
            ])
            ->update([
                'key' => DB::raw("REPLACE(`key`, 'social.', 'social-login.')"),
                'group' => 'Social login',
            ]);

        // Revert keys and groups for 'social-links' settings
        DB::table('settings')
            ->whereIn('key', [
                'social.facebook_url',
                'social.instagram_url',
                'social.twitter_url',
                'social.whatsapp_url',
                'social.tiktok_url',
                'social.youtube_url',
                'social.telegram_link',
                'social.reddit_url',
            ])
            ->update([
                'key' => DB::raw("REPLACE(`key`, 'social.', 'social-media.')"),
                'group' => 'Social links',
            ]);



    }
};
