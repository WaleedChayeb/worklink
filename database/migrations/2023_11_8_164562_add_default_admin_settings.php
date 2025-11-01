<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultAdminSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \DB::table('settings')->delete();

        \DB::table('settings')->insert(array (

                0 =>
                    array (
                        'id' => 1,
                        'key' => 'site.name',
                        'display_name' => 'Site name',
                        'value' => 'JustJobs',
                        'details' => '',
                        'type' => 'text',
                        'order' => 1,
                        'group' => 'Site',
                    ),
                1 =>
                    array (
                        'id' => 2,
                        'key' => 'site.description',
                        'display_name' => 'Site description',
                        'value' => 'JustJobs is a modern and versatile job listing SaaS platform',
                        'details' => '',
                        'type' => 'text',
                        'order' => 4,
                        'group' => 'Site',
                    ),
                2 =>
                    array (
                        'id' => 5,
                        'key' => 'admin.bg_image',
                        'display_name' => 'Admin Background Image',
                        'value' => '',
                        'details' => '',
                        'type' => 'image',
                        'order' => 5,
                        'group' => 'Admin',
                    ),
                3 =>
                    array (
                        'id' => 6,
                        'key' => 'admin.title',
                        'display_name' => 'Admin Title',
                        'value' => 'JustJobs Admin',
                        'details' => '',
                        'type' => 'text',
                        'order' => 1,
                        'group' => 'Admin',
                    ),
                4 =>
                    array (
                        'id' => 7,
                        'key' => 'admin.description',
                        'display_name' => 'Admin Description',
                        'value' => 'Welcome to JustJobs Admin Panel. Log in to manage and customize your site!',
                        'details' => '',
                        'type' => 'text',
                        'order' => 2,
                        'group' => 'Admin',
                    ),
                5 =>
                    array (
                        'id' => 8,
                        'key' => 'admin.loader',
                        'display_name' => 'Admin Loader',
                        'value' => '',
                        'details' => '',
                        'type' => 'image',
                        'order' => 3,
                        'group' => 'Admin',
                    ),
                6 =>
                    array (
                        'id' => 9,
                        'key' => 'admin.icon_image',
                        'display_name' => 'Admin Icon Image',
                        'value' => '',
                        'details' => '',
                        'type' => 'image',
                        'order' => 4,
                        'group' => 'Admin',
                    ),
                7 =>
                    array (
                        'id' => 32,
                        'key' => 'media.allowed_file_extensions',
                        'display_name' => 'Allowed file extensions',
                        'value' => 'png,jpg,jpeg',
                        'details' => '',
                        'type' => 'text',
                        'order' => 14,
                        'group' => 'Media',
                    ),
                8 =>
                    array (
                        'id' => 33,
                        'key' => 'media.max_file_upload_size',
                        'display_name' => 'Max file uploads size',
                        'value' => '10',
                        'details' => '{
"description":  "File size in MB. Do not exceed PHP maximum upload size & post size as video uploads might silently fail."
}',
                        'type' => 'text',
                        'order' => 15,
                        'group' => 'Media',
                    ),
                9 =>
                    array (
                        'id' => 39,
                        'key' => 'payments.invoices_sender_name',
                        'display_name' => 'Invoices Sender Name',
                        'value' => 'Web Development for Digital Marketing Agency',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 21,
                        'group' => 'Payments',
                    ),
                10 =>
                    array (
                        'id' => 40,
                        'key' => 'payments.invoices_sender_country_name',
                        'display_name' => 'Invoices Sender Country Name',
                        'value' => 'Australia',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 22,
                        'group' => 'Payments',
                    ),
                11 =>
                    array (
                        'id' => 41,
                        'key' => 'payments.invoices_sender_street_address',
                        'display_name' => 'Invoices Sender Street Address',
                        'value' => '121 Kopkes Road',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 23,
                        'group' => 'Payments',
                    ),
                12 =>
                    array (
                        'id' => 42,
                        'key' => 'payments.invoices_sender_state_name',
                        'display_name' => 'Invoices Sender State Name',
                        'value' => 'Victoria',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 24,
                        'group' => 'Payments',
                    ),
                13 =>
                    array (
                        'id' => 43,
                        'key' => 'payments.invoices_sender_city_name',
                        'display_name' => 'Invoices Sender City Name',
                        'value' => '3351',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 25,
                        'group' => 'Payments',
                    ),
                14 =>
                    array (
                        'id' => 44,
                        'key' => 'payments.invoices_sender_postcode',
                        'display_name' => 'Invoices Sender Postcode',
                        'value' => 'PITFIELD',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 26,
                        'group' => 'Payments',
                    ),
                15 =>
                    array (
                        'id' => 45,
                        'key' => 'payments.invoices_sender_company_number',
                        'display_name' => 'Invoices Sender Company Number',
                        'value' => '(03) 5391 1216',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 27,
                        'group' => 'Payments',
                    ),
                16 =>
                    array (
                        'id' => 46,
                        'key' => 'payments.invoices_prefix',
                        'display_name' => 'Invoices Prefix',
                        'value' => 'OF',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 28,
                        'group' => 'Payments',
                    ),
                17 =>
                    array (
                        'id' => 54,
                        'key' => 'site.light_logo',
                        'display_name' => 'Light site logo',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'file',
                        'order' => 35,
                        'group' => 'Site',
                    ),
                18 =>
                    array (
                        'id' => 55,
                        'key' => 'site.dark_logo',
                        'display_name' => 'Dark site logo',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'file',
                        'order' => 36,
                        'group' => 'Site',
                    ),
                19 =>
                    array (
                        'id' => 56,
                        'key' => 'site.favicon',
                        'display_name' => 'Site favicon',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'file',
                        'order' => 65,
                        'group' => 'Site',
                    ),
                20 =>
                    array (
                        'id' => 57,
                        'key' => 'payments.stripe_public_key',
                        'display_name' => 'Stripe Public Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 37,
                        'group' => 'Payments',
                    ),
                21 =>
                    array (
                        'id' => 58,
                        'key' => 'payments.stripe_secret_key',
                        'display_name' => 'Stripe Secret Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 38,
                        'group' => 'Payments',
                    ),
                22 =>
                    array (
                        'id' => 59,
                        'key' => 'payments.stripe_webhooks_secret',
                        'display_name' => 'Stripe Webhooks Secret',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 39,
                        'group' => 'Payments',
                    ),
                23 =>
                    array (
                        'id' => 60,
                        'key' => 'payments.paypal_client_id',
                        'display_name' => 'Paypal Client Id',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 40,
                        'group' => 'Payments',
                    ),
                24 =>
                    array (
                        'id' => 61,
                        'key' => 'payments.paypal_secret',
                        'display_name' => 'Paypal Secret',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 41,
                        'group' => 'Payments',
                    ),
                25 =>
                    array (
                        'id' => 74,
                        'key' => 'payments.paypal_live_mode',
                        'display_name' => 'Paypal Live Mode',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : true
}',
                        'type' => 'checkbox',
                        'order' => 42,
                        'group' => 'Payments',
                    ),
                26 =>
                    array (
                        'id' => 78,
                        'key' => 'emails.driver',
                        'display_name' => 'Email driver',
                        'value' => 'log',
                        'details' => '{
"default" : "log",
"options" : {
"log": "Log",
"sendmail": "Sendmail",
"smtp": "SMTP",
"mailgun": "Mailgun"
}
}',
                        'type' => 'select_dropdown',
                        'order' => 43,
                        'group' => 'Emails',
                    ),
                27 =>
                    array (
                        'id' => 79,
                        'key' => 'emails.from_name',
                        'display_name' => 'Mail from name',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 44,
                        'group' => 'Emails',
                    ),
                28 =>
                    array (
                        'id' => 80,
                        'key' => 'emails.from_address',
                        'display_name' => 'Mail from address',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 45,
                        'group' => 'Emails',
                    ),
                29 =>
                    array (
                        'id' => 81,
                        'key' => 'emails.mailgun_domain',
                        'display_name' => 'Mailgun domain',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 46,
                        'group' => 'Emails',
                    ),
                30 =>
                    array (
                        'id' => 82,
                        'key' => 'emails.mailgun_secret',
                        'display_name' => 'Mailgun secret',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 47,
                        'group' => 'Emails',
                    ),
                31 =>
                    array (
                        'id' => 83,
                        'key' => 'emails.smtp_host',
                        'display_name' => 'SMTP Host',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 49,
                        'group' => 'Emails',
                    ),
                32 =>
                    array (
                        'id' => 84,
                        'key' => 'emails.smtp_port',
                        'display_name' => 'SMTP Port',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 50,
                        'group' => 'Emails',
                    ),
                33 =>
                    array (
                        'id' => 85,
                        'key' => 'emails.smtp_encryption',
                        'display_name' => 'SMTP Encryption',
                        'value' => 'tls',
                        'details' => '{
"default" : "tls",
"options" : {
"tls": "TLS",
"ssl": "SSL"
}
}',
                        'type' => 'radio_btn',
                        'order' => 51,
                        'group' => 'Emails',
                    ),
                34 =>
                    array (
                        'id' => 86,
                        'key' => 'emails.smtp_username',
                        'display_name' => 'SMTP Username',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 52,
                        'group' => 'Emails',
                    ),
                35 =>
                    array (
                        'id' => 87,
                        'key' => 'emails.smtp_password',
                        'display_name' => 'SMTP Password',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 53,
                        'group' => 'Emails',
                    ),
                36 =>
                    array (
                        'id' => 88,
                        'key' => 'emails.mailgun_endpoint',
                        'display_name' => 'Mailgun endpoint',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 48,
                        'group' => 'Emails',
                    ),
                37 =>
                    array (
                        'id' => 95,
                        'key' => 'storage.driver',
                        'display_name' => 'Driver',
                        'value' => 'public',
                        'details' => '{
"default" : "public",
"options" : {
"public": "Local",
"s3": "S3",
"wasabi": "Wasabi",
"do_spaces": "DigitalOcean Spaces",
"minio": "Minio",
"pushr": "Pushr"
}
}',
                        'type' => 'select_dropdown',
                        'order' => 54,
                        'group' => 'Storage',
                    ),
                38 =>
                    array (
                        'id' => 96,
                        'key' => 'storage.aws_access_key',
                        'display_name' => 'Aws Access Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 55,
                        'group' => 'Storage',
                    ),
                39 =>
                    array (
                        'id' => 97,
                        'key' => 'storage.aws_secret_key',
                        'display_name' => 'Aws Secret Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 56,
                        'group' => 'Storage',
                    ),
                40 =>
                    array (
                        'id' => 98,
                        'key' => 'storage.aws_region',
                        'display_name' => 'Aws Region',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 57,
                        'group' => 'Storage',
                    ),
                41 =>
                    array (
                        'id' => 99,
                        'key' => 'storage.aws_bucket_name',
                        'display_name' => 'Aws Bucket Name',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 58,
                        'group' => 'Storage',
                    ),
                42 =>
                    array (
                        'id' => 100,
                        'key' => 'storage.aws_cdn_enabled',
                        'display_name' => 'Aws CloudFront Enabled',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"off" : "Off",
"checked" : false
}',
                        'type' => 'checkbox',
                        'order' => 59,
                        'group' => 'Storage',
                    ),
                43 =>
                    array (
                        'id' => 101,
                        'key' => 'storage.aws_cdn_presigned_urls_enabled',
                        'display_name' => 'Aws CloudFront PreSigned Url\'s Enabled',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false
}',
                        'type' => 'checkbox',
                        'order' => 61,
                        'group' => 'Storage',
                    ),
                44 =>
                    array (
                        'id' => 102,
                        'key' => 'storage.aws_cdn_key_pair_id',
                        'display_name' => 'Aws CloudFront Key Pair Id',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 62,
                        'group' => 'Storage',
                    ),
                45 =>
                    array (
                        'id' => 103,
                        'key' => 'storage.aws_cdn_private_key_path',
                        'display_name' => 'Aws CloudFront Private Key Path',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 63,
                        'group' => 'Storage',
                    ),
                46 =>
                    array (
                        'id' => 104,
                        'key' => 'storage.cdn_domain_name',
                        'display_name' => 'Aws CloudFront Domain Name',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 60,
                        'group' => 'Storage',
                    ),
                47 =>
                    array (
                        'id' => 106,
                        'key' => 'compliance.enable_cookies_box',
                        'display_name' => 'Enable cookies box',
                        'value' => '0',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true
}',
                        'type' => 'checkbox',
                        'order' => 1130,
                        'group' => 'Compliance',
                    ),
                48 =>
                    array (
                        'id' => 108,
                        'key' => 'site.allow_theme_switch',
                        'display_name' => 'Allow theme switch',
                        'value' => '1',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description" : "Allow users to switch between light and dark modes."
}',
                        'type' => 'checkbox',
                        'order' => 69,
                        'group' => 'Site',
                    ),
                49 =>
                    array (
                        'id' => 109,
                        'key' => 'site.default_user_theme',
                        'display_name' => 'Default theme',
                        'value' => 'light',
                        'details' => '{
"default" : "light",
"options" : {
"light": "Light theme",
"dark": "Dark theme"
}
}',
                        'type' => 'radio_btn',
                        'order' => 70,
                        'group' => 'Site',
                    ),
                50 =>
                    array (
                        'id' => 110,
                        'key' => 'site.allow_direction_switch',
                        'display_name' => 'Allow direction switch',
                        'value' => '1',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description": "Allow users to switch site direction from ltr to rtl."
}',
                        'type' => 'checkbox',
                        'order' => 71,
                        'group' => 'Site',
                    ),
                51 =>
                    array (
                        'id' => 111,
                        'key' => 'site.default_site_direction',
                        'display_name' => 'Default site direction',
                        'value' => 'ltr',
                        'details' => '{
"default" : "ltr",
"options" : {
"ltr": "Left to right",
"rtl": "Right to left"
}
}',
                        'type' => 'radio_btn',
                        'order' => 73,
                        'group' => 'Site',
                    ),
                52 =>
                    array (
                        'id' => 112,
                        'key' => 'site.allow_language_switch',
                        'display_name' => 'Allow language switch',
                        'value' => '1',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description": "Allow users to change site\'s language."
}',
                        'type' => 'checkbox',
                        'order' => 74,
                        'group' => 'Site',
                    ),
                53 =>
                    array (
                        'id' => 113,
                        'key' => 'site.default_site_language',
                        'display_name' => 'Default site language',
                        'value' => 'en',
                        'details' => '{
"description" : "Language code. Must have a present language file in the resources/lang directory."
}',
                        'type' => 'text',
                        'order' => 75,
                        'group' => 'Site',
                    ),
                55 =>
                    array (
                        'id' => 120,
                        'key' => 'payments.currency_code',
                        'display_name' => 'Site Currency Code',
                        'value' => 'USD',
                        'details' => '{
"description": "Mandatory for payment providers"
}',
                        'type' => 'text',
                        'order' => 66,
                        'group' => 'Payments',
                    ),
                56 =>
                    array (
                        'id' => 121,
                        'key' => 'payments.currency_symbol',
                        'display_name' => 'Site Currency Symbol',
                        'value' => '$',
                        'details' => '{
"description": "Can be a symbol or currency code and it`s shown everywhere in the website (if empty defaults to currency code)"
}',
                        'type' => 'text',
                        'order' => 67,
                        'group' => 'Payments',
                    ),
                57 =>
                    array (
                        'id' => 123,
                        'key' => 'site.app_url',
                        'display_name' => 'Site url',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 0,
                        'group' => 'Site',
                    ),
                58 =>
                    array (
                        'id' => 124,
                        'key' => 'site.allow_pwa_installs',
                        'display_name' => 'Allow PWA Installs',
                        'value' => '1',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description" : "Turns the site into an installable PWA on all devices. Website must be server from a root domain."
}',
                        'type' => 'checkbox',
                        'order' => 79,
                        'group' => 'Site',
                    ),
                59 =>
                    array (
                        'id' => 126,
                        'key' => 'social-media.facebook_url',
                        'display_name' => 'Facebook',
                        'value' => '#',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 80,
                        'group' => 'Social media',
                    ),
                60 =>
                    array (
                        'id' => 127,
                        'key' => 'social-media.instagram_url',
                        'display_name' => 'Instagram',
                        'value' => '#',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 81,
                        'group' => 'Social media',
                    ),
                61 =>
                    array (
                        'id' => 128,
                        'key' => 'social-media.twitter_url',
                        'display_name' => 'Twitter',
                        'value' => '#',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 82,
                        'group' => 'Social media',
                    ),
                62 =>
                    array (
                        'id' => 129,
                        'key' => 'social-media.whatsapp_url',
                        'display_name' => 'Whatsapp',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 83,
                        'group' => 'Social media',
                    ),
                63 =>
                    array (
                        'id' => 130,
                        'key' => 'social-media.tiktok_url',
                        'display_name' => 'Tik Tok',
                        'value' => '#',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 84,
                        'group' => 'Social media',
                    ),
                64 =>
                    array (
                        'id' => 131,
                        'key' => 'social-media.youtube_url',
                        'display_name' => 'Youtube',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 85,
                        'group' => 'Social media',
                    ),
                65 =>
                    array (
                        'id' => 140,
                        'key' => 'payments.deposit_min_amount',
                        'display_name' => 'Deposit minimum amount',
                        'value' => '5',
                        'details' => '{
"description": "Default: 5"
}',
                        'type' => 'text',
                        'order' => 93,
                        'group' => 'Payments',
                    ),
                66 =>
                    array (
                        'id' => 141,
                        'key' => 'payments.deposit_max_amount',
                        'display_name' => 'Deposit maximum amount',
                        'value' => '500',
                        'details' => '{
"description": "Default: 500"
}',
                        'type' => 'text',
                        'order' => 94,
                        'group' => 'Payments',
                    ),
                67 =>
                    array (
                        'id' => 144,
                        'key' => 'custom-code-ads.custom_css',
                        'display_name' => 'Custom CSS Code',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'code_editor',
                        'order' => 155,
                        'group' => 'Custom Code / Ads',
                    ),
                68 =>
                    array (
                        'id' => 145,
                        'key' => 'custom-code-ads.custom_js',
                        'display_name' => 'Custom JS Code',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'code_editor',
                        'order' => 154,
                        'group' => 'Custom Code / Ads',
                    ),
                69 =>
                    array (
                        'id' => 147,
                        'key' => 'storage.was_access_key',
                        'display_name' => 'Wasabi Access Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 111,
                        'group' => 'Storage',
                    ),
                70 =>
                    array (
                        'id' => 148,
                        'key' => 'storage.was_secret_key',
                        'display_name' => 'Wasabi Secret Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 112,
                        'group' => 'Storage',
                    ),
                71 =>
                    array (
                        'id' => 149,
                        'key' => 'storage.was_region',
                        'display_name' => 'Wasabi Region',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 113,
                        'group' => 'Storage',
                    ),
                72 =>
                    array (
                        'id' => 150,
                        'key' => 'storage.was_bucket_name',
                        'display_name' => 'Wasabi Bucket',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 114,
                        'group' => 'Storage',
                    ),
                73 =>
                    array (
                        'id' => 153,
                        'key' => 'custom-code-ads.sidebar_ad_spot',
                        'display_name' => 'Job page sidebar ad',
                        'value' => '',
                        'details' => '{
"description" : "Will be shown on the listing page right sidebar."
}',
                        'type' => 'code_editor',
                        'order' => 117,
                        'group' => 'Custom Code / Ads',
                    ),
                74 =>
                    array (
                        'id' => 154,
                        'key' => 'site.hide_identity_checks',
                        'display_name' => 'Hide identity checks menu',
                        'value' => '0',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description" : "If enabled, users the \'Verify\' user setting menu will be hidden by default."
}',
                        'type' => 'checkbox',
                        'order' => 77,
                        'group' => 'Site',
                    ),
                75 =>
                    array (
                        'id' => 156,
                        'key' => 'payments.coinbase_api_key',
                        'display_name' => 'CoinBase Api Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 35,
                        'group' => 'Payments',
                    ),
                76 =>
                    array (
                        'id' => 157,
                        'key' => 'payments.coinbase_webhook_key',
                        'display_name' => 'CoinBase Webhooks Secret',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 36,
                        'group' => 'Payments',
                    ),
                77 =>
                    array (
                        'id' => 158,
                        'key' => 'social-login.facebook_client_id',
                        'display_name' => 'Facebook client ID',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 70,
                        'group' => 'Social login',
                    ),
                78 =>
                    array (
                        'id' => 159,
                        'key' => 'social-login.facebook_secret',
                        'display_name' => 'Facebook client secret',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 118,
                        'group' => 'Social login',
                    ),
                79 =>
                    array (
                        'id' => 160,
                        'key' => 'social-login.twitter_client_id',
                        'display_name' => 'Twitter client ID',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 119,
                        'group' => 'Social login',
                    ),
                80 =>
                    array (
                        'id' => 161,
                        'key' => 'social-login.twitter_secret',
                        'display_name' => 'Twitter client secret',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 120,
                        'group' => 'Social login',
                    ),
                81 =>
                    array (
                        'id' => 162,
                        'key' => 'social-login.google_client_id',
                        'display_name' => 'Google client ID',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 121,
                        'group' => 'Social login',
                    ),
                82 =>
                    array (
                        'id' => 163,
                        'key' => 'social-login.google_secret',
                        'display_name' => 'Google client secret',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 122,
                        'group' => 'Social login',
                    ),
                83 =>
                    array (
                        'id' => 164,
                        'key' => 'site.slogan',
                        'display_name' => 'Site slogan',
                        'value' => 'Discover Top Talent, Effortlessly',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 4,
                        'group' => 'Site',
                    ),
                84 =>
                    array (
                        'id' => 168,
                        'key' => 'payments.allow_manual_payments',
                        'display_name' => 'Allow manual payments',
                        'value' => '1',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : true
}',
                        'type' => 'checkbox',
                        'order' => 43,
                        'group' => 'Payments',
                    ),
                85 =>
                    array (
                        'id' => 169,
                        'key' => 'media.use_chunked_uploads',
                        'display_name' => 'Use chunked uploads',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "If enabled, file uploads will be split across multiple requests, allowing to bypass Cloudflare or max file limits."
}',
                        'type' => 'checkbox',
                        'order' => 15,
                        'group' => 'Media',
                    ),
                86 =>
                    array (
                        'id' => 170,
                        'key' => 'media.upload_chunk_size',
                        'display_name' => 'Chunks size',
                        'value' => '2',
                        'details' => '{
"description": "File upload chunks size in MB. Can not exceed maximum server upload size."
}',
                        'type' => 'text',
                        'order' => 15,
                        'group' => 'Media',
                    ),
                87 =>
                    array (
                        'id' => 171,
                        'key' => 'site.enforce_email_validation',
                        'display_name' => 'Enforce email validations',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "If enabled, all users will be disabled site access until they verified the email. If turned of, users will still receive a confirmation pop up in user settings."
}',
                        'type' => 'checkbox',
                        'order' => 77,
                        'group' => 'Site',
                    ),
                88 =>
                    array (
                        'id' => 172,
                        'key' => 'site.homepage_redirect',
                        'display_name' => 'Homepage redirect',
                        'value' => NULL,
                        'details' => '{
"description": "EG: Enter the url of an alternative landing page."
}',
                        'type' => 'text',
                        'order' => 76,
                        'group' => 'Site',
                    ),
                89 =>
                    array (
                        'id' => 174,
                        'key' => 'security.enable_2fa',
                        'display_name' => 'Enable email 2FA on logins',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : true,
"description": "If enabled, users which have 2FA enabled for their account, will be prompted with a security check when logging from new devices."
}',
                        'type' => 'checkbox',
                        'order' => 85,
                        'group' => 'Security',
                    ),
                90 =>
                    array (
                        'id' => 175,
                        'key' => 'security.default_2fa_on_register',
                        'display_name' => 'Default 2FA setting on user register',
                        'value' => '1',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : true,
"description": "If enabled, users will have 2FA enabled by default, when creating new accounts."
}',
                        'type' => 'checkbox',
                        'order' => 90,
                        'group' => 'Security',
                    ),
                91 =>
                    array (
                        'id' => 176,
                        'key' => 'security.allow_users_2fa_switch',
                        'display_name' => 'Allow users to turn off 2FA',
                        'value' => '1',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : true,
"description": "If disabled, users won\'t be able to change their 2FA settings."
}',
                        'type' => 'checkbox',
                        'order' => 95,
                        'group' => 'Security',
                    ),
                92 =>
                    array (
                        'id' => 179,
                        'key' => 'storage.do_access_key',
                        'display_name' => 'DO Access Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 120,
                        'group' => 'Storage',
                    ),
                93 =>
                    array (
                        'id' => 180,
                        'key' => 'storage.do_secret_key',
                        'display_name' => 'DO Secret Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 121,
                        'group' => 'Storage',
                    ),
                94 =>
                    array (
                        'id' => 181,
                        'key' => 'storage.do_region',
                        'display_name' => 'DO Region',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 123,
                        'group' => 'Storage',
                    ),
                95 =>
                    array (
                        'id' => 183,
                        'key' => 'storage.do_bucket_name',
                        'display_name' => 'DO Bucket',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 122,
                        'group' => 'Storage',
                    ),
                96 =>
                    array (
                        'id' => 184,
                        'key' => 'payments.nowpayments_api_key',
                        'display_name' => 'NowPayments Api Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 33,
                        'group' => 'Payments',
                    ),
                97 =>
                    array (
                        'id' => 185,
                        'key' => 'payments.nowpayments_ipn_secret_key',
                        'display_name' => 'NowPayments IPN Secret Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 34,
                        'group' => 'Payments',
                    ),
                98 =>
                    array (
                        'id' => 186,
                        'key' => 'site.default_user_privacy_setting_on_register',
                        'display_name' => 'Default user privacy setting on user register',
                        'value' => 'public',
                        'details' => '{
"default" : "public",
"options" : {
"public": "Public profile",
"private": "Private profile"
}
}',
                        'type' => 'radio_btn',
                        'order' => 120,
                        'group' => 'Site',
                    ),
                99 =>
                    array (
                        'id' => 188,
                        'key' => 'security.enforce_app_ssl',
                        'display_name' => 'Enforce platform SSL usage',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Usually not required, rarely, some hosting providers needs it."
}',
                        'type' => 'checkbox',
                        'order' => 130,
                        'group' => 'Security',
                    ),
                100 =>
                    array (
                        'id' => 202,
                        'key' => 'colors.theme_color_code',
                        'display_name' => 'Theme color code',
                        'value' => 'F55536',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 210,
                        'group' => 'Colors',
                    ),
                101 =>
                    array (
                        'id' => 203,
                        'key' => 'colors.theme_gradient_from',
                        'display_name' => 'Theme gradient from',
                        'value' => 'F55536',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 220,
                        'group' => 'Colors',
                    ),
                102 =>
                    array (
                        'id' => 204,
                        'key' => 'colors.theme_gradient_to',
                        'display_name' => 'Theme gradient to',
                        'value' => 'FF773D',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 230,
                        'group' => 'Colors',
                    ),
                103 =>
                    array (
                        'id' => 209,
                        'key' => 'payments.offline_payments_owner',
                        'display_name' => 'Account owner',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 44,
                        'group' => 'Payments',
                    ),
                104 =>
                    array (
                        'id' => 210,
                        'key' => 'payments.offline_payments_account_number',
                        'display_name' => 'Account number',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 45,
                        'group' => 'Payments',
                    ),
                105 =>
                    array (
                        'id' => 211,
                        'key' => 'payments.offline_payments_bank_name',
                        'display_name' => 'Bank name',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 46,
                        'group' => 'Payments',
                    ),
                106 =>
                    array (
                        'id' => 212,
                        'key' => 'payments.offline_payments_routing_number',
                        'display_name' => 'Routing number',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 47,
                        'group' => 'Payments',
                    ),
                107 =>
                    array (
                        'id' => 213,
                        'key' => 'payments.offline_payments_iban',
                        'display_name' => 'IBAN',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 48,
                        'group' => 'Payments',
                    ),
                108 =>
                    array (
                        'id' => 214,
                        'key' => 'payments.offline_payments_swift',
                        'display_name' => 'BIC / SWIFT',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 49,
                        'group' => 'Payments',
                    ),
                109 =>
                    array (
                        'id' => 215,
                        'key' => 'payments.ccbill_account_number',
                        'display_name' => 'CCBill Account Number',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 28,
                        'group' => 'Payments',
                    ),
                110 =>
                    array (
                        'id' => 216,
                        'key' => 'payments.ccbill_subaccount_number_recurring',
                        'display_name' => 'CCBill SubAccount Number Recurring Payments',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 29,
                        'group' => 'Payments',
                    ),
                111 =>
                    array (
                        'id' => 217,
                        'key' => 'payments.ccbill_subaccount_number_one_time',
                        'display_name' => 'CCBill SubAccount Number One Time Payments',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 30,
                        'group' => 'Payments',
                    ),
                112 =>
                    array (
                        'id' => 218,
                        'key' => 'payments.ccbill_flex_form_id',
                        'display_name' => 'CCBill FlexForm Id',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 31,
                        'group' => 'Payments',
                    ),
                113 =>
                    array (
                        'id' => 219,
                        'key' => 'payments.ccbill_salt_key',
                        'display_name' => 'CCBill Salt Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 32,
                        'group' => 'Payments',
                    ),
                114 =>
                    array (
                        'id' => 220,
                        'key' => 'compliance.enable_age_verification_dialog',
                        'display_name' => 'Enable age verification dialog',
                        'value' => '0',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description" "Can be generally used for denying user access for minors, adult content info, etc."
}',
                        'type' => 'checkbox',
                        'order' => 1140,
                        'group' => 'Compliance',
                    ),
                115 =>
                    array (
                        'id' => 221,
                        'key' => 'compliance.age_verification_cancel_url',
                        'display_name' => 'Age verification box cancel url',
                        'value' => 'https://google.com',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 1150,
                        'group' => 'Compliance',
                    ),
                116 =>
                    array (
                        'id' => 225,
                        'key' => 'security.recaptcha_enabled',
                        'display_name' => 'Enable Google reCAPTCHA',
                        'value' => '0',
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
                117 =>
                    array (
                        'id' => 226,
                        'key' => 'security.recaptcha_site_key',
                        'display_name' => 'reCAPTCHA Site Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 1210,
                        'group' => 'Security',
                    ),
                118 =>
                    array (
                        'id' => 227,
                        'key' => 'security.recaptcha_site_secret_key',
                        'display_name' => 'reCAPTCHA Secret Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 1220,
                        'group' => 'Security',
                    ),
                119 =>
                    array (
                        'id' => 230,
                        'key' => 'media.max_avatar_cover_file_size',
                        'display_name' => 'Max avatar & cover file size',
                        'value' => '4',
                        'details' => '{
"description": "File size in MB. Used for both avatar & cover"
}',
                        'type' => 'text',
                        'order' => 1140,
                        'group' => 'Media',
                    ),
                120 =>
                    array (
                        'id' => 233,
                        'key' => 'social-media.telegram_link',
                        'display_name' => 'Telegram',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 86,
                        'group' => 'Social media',
                    ),
                121 =>
                    array (
                        'id' => 234,
                        'key' => 'payments.ccbill_datalink_username',
                        'display_name' => 'CCBill DataLink Username',
                        'value' => NULL,
                        'details' => '{
"description": "Used for cancelling CCBill subscriptions programmatically. Enables users cancelling their CCBill subscriptions from their profile"
}',
                        'type' => 'text',
                        'order' => 33,
                        'group' => 'Payments',
                    ),
                122 =>
                    array (
                        'id' => 235,
                        'key' => 'payments.ccbill_datalink_password',
                        'display_name' => 'CCBill DataLink Password',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 34,
                        'group' => 'Payments',
                    ),
                123 =>
                    array (
                        'id' => 236,
                        'key' => 'payments.ccbill_checkout_disabled',
                        'display_name' => 'Disable for checkout',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be shown on checkout, but it`s still available for deposits."
}',
                        'type' => 'checkbox',
                        'order' => 36,
                        'group' => 'Payments',
                    ),
                124 =>
                    array (
                        'id' => 237,
                        'key' => 'payments.ccbill_recurring_disabled',
                        'display_name' => 'Disable for recurring payments',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be available for subscription payments, but it`s still available for deposits and one time payments."
}',
                        'type' => 'checkbox',
                        'order' => 36,
                        'group' => 'Payments',
                    ),
                125 =>
                    array (
                        'id' => 238,
                        'key' => 'payments.stripe_checkout_disabled',
                        'display_name' => 'Disable for checkout',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be shown on checkout, but it`s still available for deposits."
}',
                        'type' => 'checkbox',
                        'order' => 40,
                        'group' => 'Payments',
                    ),
                126 =>
                    array (
                        'id' => 239,
                        'key' => 'payments.stripe_recurring_disabled',
                        'display_name' => 'Disable for recurring payments',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be available for subscription payments, but it`s still available for deposits and one time payments."
}',
                        'type' => 'checkbox',
                        'order' => 42,
                        'group' => 'Payments',
                    ),
                127 =>
                    array (
                        'id' => 240,
                        'key' => 'payments.paypal_checkout_disabled',
                        'display_name' => 'Disable for checkout',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be shown on checkout, but it`s still available for deposits."
}',
                        'type' => 'checkbox',
                        'order' => 44,
                        'group' => 'Payments',
                    ),
                128 =>
                    array (
                        'id' => 241,
                        'key' => 'payments.paypal_recurring_disabled',
                        'display_name' => 'Disable for recurring payments',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be available for subscription payments, but it`s still available for deposits and one time payments."
}',
                        'type' => 'checkbox',
                        'order' => 46,
                        'group' => 'Payments',
                    ),
                129 =>
                    array (
                        'id' => 242,
                        'key' => 'payments.nowpayments_checkout_disabled',
                        'display_name' => 'Disable for checkout',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be shown on checkout, but it`s still available for deposits."
}',
                        'type' => 'checkbox',
                        'order' => 36,
                        'group' => 'Payments',
                    ),
                130 =>
                    array (
                        'id' => 243,
                        'key' => 'payments.coinbase_checkout_disabled',
                        'display_name' => 'Disable for checkout',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be shown on checkout, but it`s still available for deposits."
}',
                        'type' => 'checkbox',
                        'order' => 38,
                        'group' => 'Payments',
                    ),
                131 =>
                    array (
                        'id' => 245,
                        'key' => 'payments.paystack_secret_key',
                        'display_name' => 'Paystack Secret Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 28,
                        'group' => 'Payments',
                    ),
                132 =>
                    array (
                        'id' => 246,
                        'key' => 'payments.paystack_checkout_disabled',
                        'display_name' => 'Disable for checkout',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Won`t be shown on checkout, but it`s still available for deposits."
}',
                        'type' => 'checkbox',
                        'order' => 44,
                        'group' => 'Payments',
                    ),
                133 =>
                    array (
                        'id' => 247,
                        'key' => 'ai.open_ai_enabled',
                        'display_name' => 'OpenAI Enabled',
                        'value' => '1',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "Allow creators to use OpenAI to suggest a post or a profile description"
}',
                        'type' => 'checkbox',
                        'order' => 10,
                        'group' => 'AI',
                    ),
                134 =>
                    array (
                        'id' => 248,
                        'key' => 'ai.open_ai_api_key',
                        'display_name' => 'OpenAI Api Key',
                        'value' => '',
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 20,
                        'group' => 'AI',
                    ),
                135 =>
                    array (
                        'id' => 249,
                        'key' => 'ai.open_ai_completion_max_tokens',
                        'display_name' => 'OpenAI Max Tokens',
                        'value' => '100',
                        'details' => '{
"description": "Dictates how long the suggestion should be. E.g. 1000 tokens is about 750 words. (shouldn`t exceed 2048 tokens)"
}',
                        'type' => 'text',
                        'order' => 30,
                        'group' => 'AI',
                    ),
                136 =>
                    array (
                        'id' => 250,
                        'key' => 'ai.open_ai_completion_temperature',
                        'display_name' => 'OpenAI Temperature',
                        'value' => '1',
                        'details' => '{
"description": "What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic."
}',
                        'type' => 'text',
                        'order' => 40,
                        'group' => 'AI',
                    ),
                137 =>
                    array (
                        'id' => 255,
                        'key' => 'site.newsletter_homepage_position',
                        'display_name' => 'Newsletter box homepage position',
                        'value' => 'top',
                        'details' => '{
"default" : "public",
"options" : {
"top": "Top",
"bottom": "Bottom",
"none": "None"
},
"description" : "Choose if the newsletter subscribe box is shown or the position for it."
}',
                        'type' => 'radio_btn',
                        'order' => 130,
                        'group' => 'Site',
                    ),
                138 =>
                    array (
                        'id' => 256,
                        'key' => 'storage.pushr_access_key',
                        'display_name' => 'Pushr Access Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 130,
                        'group' => 'Storage',
                    ),
                139 =>
                    array (
                        'id' => 257,
                        'key' => 'storage.pushr_secret_key',
                        'display_name' => 'Pushr Secret Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 140,
                        'group' => 'Storage',
                    ),
                140 =>
                    array (
                        'id' => 258,
                        'key' => 'storage.pushr_cdn_hostname',
                        'display_name' => 'Pushr CDN Hostname',
                        'value' => NULL,
                        'details' => '{
"description" : "This field must contain the https:// prefix."
}',
                        'type' => 'text',
                        'order' => 180,
                        'group' => 'Storage',
                    ),
                141 =>
                    array (
                        'id' => 259,
                        'key' => 'storage.pushr_bucket_name',
                        'display_name' => 'Pushr Bucket',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 160,
                        'group' => 'Storage',
                    ),
                142 =>
                    array (
                        'id' => 260,
                        'key' => 'site.show_featured_clients_area',
                        'display_name' => 'Display featured clients area on the homepage',
                        'value' => '1',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description" : "Choose if the featured clients area box is show on the homepage."
}',
                        'type' => 'checkbox',
                        'order' => 140,
                        'group' => 'Site',
                    ),
                143 =>
                    array (
                        'id' => 261,
                        'key' => 'storage.pushr_endpoint',
                        'display_name' => 'Pushr S3 Endpoint',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 170,
                        'group' => 'Storage',
                    ),
                144 =>
                    array (
                        'id' => 263,
                        'key' => 'storage.minio_access_key',
                        'display_name' => 'Minio Access Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 130,
                        'group' => 'Storage',
                    ),
                145 =>
                    array (
                        'id' => 264,
                        'key' => 'storage.minio_secret_key',
                        'display_name' => 'Minio Secret Key',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 140,
                        'group' => 'Storage',
                    ),
                146 =>
                    array (
                        'id' => 265,
                        'key' => 'storage.minio_region',
                        'display_name' => 'Minio Region',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 150,
                        'group' => 'Storage',
                    ),
                147 =>
                    array (
                        'id' => 266,
                        'key' => 'storage.minio_bucket_name',
                        'display_name' => 'Minio Bucket',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 160,
                        'group' => 'Storage',
                    ),
                148 =>
                    array (
                        'id' => 267,
                        'key' => 'storage.minio_endpoint',
                        'display_name' => 'Minio Endpoint',
                        'value' => NULL,
                        'details' => NULL,
                        'type' => 'text',
                        'order' => 170,
                        'group' => 'Storage',
                    ),
                149 =>
                    array (
                        'id' => 270,
                        'key' => 'site.show_popular_tags_box',
                        'display_name' => 'Display popular tags box on the homepage',
                        'value' => '1',
                        'details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description" : "Choose if the featured clients area box is show on the (bottom section of the) homepage."
}',
                        'type' => 'checkbox',
                        'order' => 150,
                        'group' => 'Site',
                    ),
                150 =>
                    array (
                        'id' => 271,
                        'key' => 'site.default_og_image',
                        'display_name' => 'Default site og:image',
                        'value' => '',
                        'details' => '{"description" : "The image to be used when sharing the site over social media sites."}',
                        'type' => 'file',
                        'order' => 65,
                        'group' => 'Site',
                    ),
                151 =>
                    array (
                        'id' => 272,
                        'key' => 'payments.currency_position',
                        'display_name' => 'Currency position',
                        'value' => 'left',
                        'details' => '{
"default" : "left",
"options" : {
"right": "Right (99.99$)",
"left": "Left ($99.99)"
},
"description": "Dictates if currency position should be left or right aligned besides amount across the website"
}',
                        'type' => 'select_dropdown',
                        'order' => 68,
                        'group' => 'Payments',
                    ),
                152 =>
                    array (
                        'id' => 273,
                        'key' => 'site.use_browser_language_if_available',
                        'display_name' => 'Use preferred browser locale, if available',
                        'value' => '1',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description" : "If this option is enabled, if the user browser locale is available as a language, that will be used by default."
}',
                        'type' => 'checkbox',
                        'order' => 125,
                        'group' => 'Site',
                    ),
                153 =>
                    array (
                        'id' => 274,
                        'key' => 'admin.send_notifications_on_contact',
                        'display_name' => 'Admin notifications for contact messages',
                        'value' => '0',
                        'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false,
"description": "If enabled, the admin users will receive an email with the contents of the contact message."
}',
                        'type' => 'checkbox',
                        'order' => 6,
                        'group' => 'Admin',
                    ),
                154 =>
                    array (
                        'id' => 275,
                        'key' => 'license.product_license_key',
                        'display_name' => 'Product license key',
                        'value' => NULL,
                        'details' => '{
"description": "Your product license key. Can be taken out of your Codecanyon downloads page."
}',
                        'type' => 'text',
                        'order' => 1000,
                        'group' => 'License',
                    )
            )
        );


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('settings')->delete();
    }
}
