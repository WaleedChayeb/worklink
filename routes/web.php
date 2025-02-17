<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Admin routes ( Needs to be placed above )
Route::group(['prefix' => 'admin', 'middleware' => ['jsVars',  'admin']], function () {
    Voyager::routes();
    Route::get('/metrics/new/users/value', 'MetricsController@newUsersValue')->name('admin.metrics.new.users.value');
    Route::get('/metrics/new/users/trend', 'MetricsController@newUsersTrend')->name('admin.metrics.new.users.trend');
    Route::get('/metrics/new/users/partition', 'MetricsController@newUsersPartition')->name('admin.metrics.new.users.partition');
    Route::get('/metrics/subscriptions/value', 'MetricsController@subscriptionsValue')->name('admin.metrics.subscriptions.value');
    Route::get('/metrics/subscriptions/trend', 'MetricsController@subscriptionsTrend')->name('admin.metrics.subscriptions.trend');
    Route::get('/metrics/subscriptions/partition', 'MetricsController@subscriptionsPartition')->name('admin.metrics.subscriptions.partition');

    Route::post('/theme/generate', 'GenericController@generateCustomTheme')->name('admin.theme.generate');
    Route::get('/export/newsletter', 'GenericController@exportNewsletterUsers')->name('admin.export.newsletter');
    Route::post('/license/save', 'GenericController@saveLicense')->name('admin.license.save');

    Route::get('/users/{id}/impersonate', 'UserController@impersonate')->name('admin.impersonate');
    Route::get('/leave-impersonation', 'UserController@leaveImpersonation')->name('admin.leaveImpersonation');
    Route::get('/clear-app-cache', 'GenericController@clearAppCache')->name('admin.clear.cache');

});

// Home & contact page
Route::any('/', ['uses' => 'HomeController@index', 'as'   => 'home']);
Route::get('/contact', ['uses' => 'GenericController@contact', 'as'   => 'contact']);
Route::post('/contact/send', ['uses' => 'GenericController@sendContactMessage', 'as'   => 'contact.send']);

// Language switcher route
Route::get('language/{locale}', ['uses' => 'GenericController@setLanguage', 'as'   => 'language']);

/* Auth Routes */
/* Auth Routes + Verify password */
Auth::routes(['verify'=>true]);
Route::get('email/verify', ['uses' => 'GenericController@userVerifyEmail', 'as' => 'verification.notice']);
Route::post('resendVerification', ['uses' => 'GenericController@resendConfirmationEmail', 'as'   => 'verfication.resend']);

// Social Auth login / register
Route::get('socialAuth/{provider}', ['uses' => 'Auth\LoginController@redirectToProvider', 'as' => 'social.login.start']);
Route::get('socialAuth/{provider}/callback', ['uses' => 'Auth\LoginController@handleProviderCallback', 'as' => 'social.login.callback']);

// Public pages
Route::get('/pages/{slug}', ['uses' => 'PublicPagesController@getPage', 'as'   => 'pages.get']);

/*
 * Protected routes
 */
Route::group(['middleware' => ['auth', 'verified', '2fa']], function () {
    Route::group(['prefix' => 'my', 'as' => 'my.'], function () {
        Route::post('/settings/flags/save', ['uses' => 'SettingsController@updateFlagSettings', 'as'   => 'settings.flags.save']);
        Route::post('/settings/profile/save', ['uses' => 'SettingsController@saveProfile', 'as'   => 'settings.profile.save']);
        Route::post('/settings/rates/save', ['uses' => 'SettingsController@saveRates', 'as'   => 'settings.rates.save']);
        Route::post('/settings/profile/upload/{uploadType}', ['uses' => 'SettingsController@uploadProfileAsset', 'as'   => 'settings.profile.upload']);
        Route::post('/settings/profile/remove/{assetType}', ['uses' => 'SettingsController@removeProfileAsset', 'as'   => 'settings.profile.remove']);
        Route::post('/settings/save', ['uses' => 'SettingsController@updateUserSettings', 'as'   => 'settings.save']);
        Route::post('/settings/verify/upload', ['uses' => 'SettingsController@verifyUpload', 'as'   => 'settings.verify.upload']);
        Route::post('/settings/verify/upload/delete', ['uses' => 'SettingsController@deleteVerifyAsset', 'as'   => 'settings.verify.delete']);
        Route::post('/settings/verify/save', ['uses' => 'SettingsController@saveVerifyRequest', 'as'   => 'settings.verify.save']);
        Route::get('/settings/privacy/countries', ['uses' => 'SettingsController@getCountries', 'as'   => 'settings.verify.countries']);

        // Profile save
        Route::get('/settings/{type?}', ['uses' => 'SettingsController@index', 'as'   => 'settings']);
        Route::post('/settings/account/save', ['uses' => 'SettingsController@saveAccount', 'as'   => 'settings.account.save']);

        Route::group(['prefix' => 'jobs', 'as' => 'jobs.'], function () {
            Route::get('/', ['uses' => 'JobsController@getMyJobs', 'as'   => 'get']);
            Route::get('/edit/{jobID}', ['uses' => 'JobsController@editJob', 'as'   => 'edit']);
            Route::post('/save', ['uses' => 'JobsController@saveJobIndividual', 'as'   => 'save']);
            Route::delete('/delete', 'JobsController@delete', ['as' => 'delete']);
        });

        Route::group(['prefix' => 'companies', 'as' => 'companies.'], function () {
            Route::get('/', ['uses' => 'CompanyController@getMyCompanies', 'as'   => 'get']);
            Route::get('/create', ['uses' => 'CompanyController@create', 'as'   => 'create']);
            Route::get('/edit/{companyID}', ['uses' => 'CompanyController@edit', 'as'   => 'edit']);
            Route::post('/save/{type}', ['uses' => 'CompanyController@save', 'as'   => 'save']);
            Route::delete('/delete', 'CompanyController@delete', ['as' => 'delete']);
            Route::post('/getSelectizedCompanies', ['uses' => 'CompanyController@getSelectizedCompanies', 'as'   => 'getSelectizedCompanies']);
        });
    });

    Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
        Route::post('/initiate', ['uses' => 'PaymentsController@initiatePayment', 'as'   => 'initiatePayment']);
        Route::post('/initiate/validate', ['uses' => 'PaymentsController@paymentInitiateValidator', 'as'   => 'initiatePaymentValidator']);
        Route::get('/paypal/status', ['uses' => 'PaymentsController@executePaypalPayment', 'as'   => 'executePaypalPayment']);
        Route::get('/stripe/status', ['uses' => 'PaymentsController@getStripePaymentStatus', 'as'   => 'checkStripePaymentStatus']);
        Route::get('/coinbase/status', ['uses' => 'PaymentsController@checkAndUpdateCoinbaseTransaction', 'as'   => 'checkCoinBasePaymentStatus']);
        Route::get('/nowpayments/status', ['uses' => 'PaymentsController@checkAndUpdateNowPaymentsTransaction', 'as'   => 'checkNowPaymentStatus']);
        Route::get('/ccbill/status', ['uses' => 'PaymentsController@processCCBillTransaction', 'as'   => 'checkCCBillPaymentStatus']);
        Route::get('/paystack/status', ['uses' => 'PaymentsController@verifyPaystackTransaction', 'as'   => 'checkPaystackPaymentStatus']);
    });

    Route::post('/settings/deposit/generateStripeSession', [
        'uses' => 'PaymentsController@generateStripeSession',
        'as'   => 'settings.deposit.generateStripeSession',
    ]);

    // Invoices routes
    Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function () {
        Route::get('/{id}', ['uses' => 'InvoicesController@index', 'as'   => 'get']);
    });
});

// 2FA related routes
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('device-verify', ['uses' => 'TwoFAController@index', 'as' => '2fa.index']);
    Route::post('device-verify', ['uses' => 'TwoFAController@store', 'as' => '2fa.post']);
    Route::get('device-verify/reset', ['uses' => 'TwoFAController@resend', 'as' => '2fa.resend']);
    Route::delete('device-verify/delete', ['uses' => 'TwoFAController@deleteDevice', 'as' => '2fa.delete']);
});

// Payment hooks
Route::post('payment/stripeStatusUpdate', [
    'as'   => 'stripe.payment.update',
    'uses' => 'PaymentsController@stripePaymentsHook',
]);

Route::post('payment/paypalStatusUpdate', [
    'as'   => 'paypal.payment.update',
    'uses' => 'PaymentsController@paypalPaymentsHook',
]);

Route::post('payment/coinbaseStatusUpdate', [
    'as'   => 'coinbase.payment.update',
    'uses' => 'PaymentsController@coinbaseHook',
]);

Route::post('payment/nowPaymentsStatusUpdate', [
    'as'   => 'nowPayments.payment.update',
    'uses' => 'PaymentsController@nowPaymentsHook',
]);

Route::post('payment/ccBillPaymentStatusUpdate', [
    'as'   => 'ccBill.payment.update',
    'uses' => 'PaymentsController@ccBillHook',
]);

Route::post('payment/paystackPaymentStatusUpdate', [
    'as'   => 'paystack.payment.update',
    'uses' => 'PaymentsController@paystackHook',
]);

// Ai routes
Route::group(['prefix' => 'suggestions', 'as' => 'suggestions.'], function () {
    Route::post('/generate', ['uses' => 'AiController@generateSuggestion', 'as'   => 'generate']);
});

Route::group(['prefix' => 'newsletter', 'as' => 'newsletter.'], function () {
    Route::get('/unsubscribe', ['uses' => 'NewsletterController@unsubscribe', 'as'   => 'unsubscribe']);
    Route::post('/add', ['uses' => 'NewsletterController@addEmail', 'as'   => 'add']);
    Route::delete('/remove', ['uses' => 'NewsletterController@removeEmail', 'as'   => 'remove']);
});

// Countries routes
Route::group(['prefix' => 'countries', 'as' => 'countries.'], function () {
    Route::get('', ['uses' => 'GenericController@countries', 'as'   => 'get']);
});

Route::group(['prefix' => 'jobs', 'as' => 'jobs.'], function () {
    Route::get('/create', ['uses' => 'JobsController@create', 'as'   => 'create']);
    Route::get('/preview', ['uses' => 'JobsController@preview', 'as'   => 'preview']);
    Route::get('/packages', ['uses' => 'JobsController@packages', 'as'   => 'packages']);
    Route::get('/checkout', ['uses' => 'JobsController@checkout', 'as'   => 'purchase']);
    Route::get('/edit/{job_id}', ['uses' => 'JobsController@edit', 'as'   => 'edit']);
    Route::post('/save/draft', ['uses' => 'JobsController@saveDraft', 'as'   => 'save.draft']);
    Route::post('/clear/draft', ['uses' => 'JobsController@clearDraft', 'as'   => 'clear.draft']);
    Route::post('/save/job', ['uses' => 'JobsController@saveJob', 'as'   => 'save']);
    Route::delete('/delete', ['uses' => 'JobsController@deleteJob', 'as'   => 'delete']);
    Route::get('/{slug}', ['uses' => 'JobsController@getJob', 'as'   => 'get']); //TODO: Review the slug
    Route::post('/add/applicant', ['uses' => 'JobsController@addApplicant', 'as'   => 'add.applicant']); //TODO: Review the slug
});

Route::group(['prefix' => 'company', 'as' => 'company.'], function () {
    Route::get('/{slug}', ['uses' => 'CompanyController@getCompany', 'as'   => 'get']); //TODO: Review the slug
});

Route::get('/search', ['uses' => 'SearchController@index', 'as'   => 'search.get']);
Route::get('/browse/{slug}', ['uses' => 'SearchController@browse', 'as'   => 'browse.get']);

// Install & upgrade routes
Route::get('/install', ['uses' => 'InstallerController@install', 'as'   => 'installer.install']);
Route::post('/install/savedbinfo', ['uses' => 'InstallerController@testAndSaveDBInfo', 'as'   => 'installer.savedb']);
Route::post('/install/beginInstall', ['uses' => 'InstallerController@beginInstall', 'as'   => 'installer.beginInstall']);
Route::get('/install/finishInstall', ['uses' => 'InstallerController@finishInstall', 'as'   => 'installer.finishInstall']);
Route::get('/update', ['uses' => 'InstallerController@upgrade', 'as'   => 'installer.update']);
Route::post('/update/doUpdate', ['uses' => 'InstallerController@doUpgrade', 'as'   => 'installer.doUpdate']);

// File uploader routes
Route::group(['prefix' => 'attachment', 'as' => 'attachment.'], function () {
    Route::post('/upload/{type}', ['uses' => 'AttachmentController@upload', 'as'   => 'upload']);
    Route::post('/uploadChunked/{type}', ['uses' => 'AttachmentController@uploadChunk', 'as'   => 'upload.chunked']);
    Route::post('/remove', ['uses' => 'AttachmentController@removeAttachment', 'as'   => 'remove']);
});

// Subscriptions routes
Route::group(['prefix' => 'subscriptions', 'as' => 'subscriptions.'], function () {
    Route::get('/{subscriptionId}/cancel', ['uses' => 'SubscriptionsController@cancelSubscription', 'as'   => 'cancel']);
});

Route::post('/report/content', ['uses' => 'GenericController@postReport', 'as'   => 'report.content']);

Route::post('/markBannerAsSeen', ['uses' => 'GenericController@markBannerAsSeen', 'as' => 'banner.mark.seen']);

Route::get('/blog', ['uses' => 'BlogController@index', 'as'   => 'blog.get']);
Route::get('/blog/{slug}', ['uses' => 'BlogController@getBlogPost', 'as'   => 'blog.post.get']);

Route::fallback(function () {
    abort(404);
});

 