<?php

namespace App\Http\Middleware;

use App;
use App\Providers\InstallerServiceProvider;
use Auth;
use Closure;
use Cookie;
use JavaScript;
use Jenssegers\Agent\Agent;
use Session;

class JavascriptVariables
{
    public function handle($request, Closure $next)
    {
        $mode = Cookie::get('app_theme');
        if (!$mode) {
            $mode = getSetting('site.default_user_theme');
        }
        $jsData = [
            'debug' => env('APP_DEBUG'),
            'baseUrl' => url(''),
            'theme' => $mode,
        ];
        if (InstallerServiceProvider::checkIfInstalled()) {
            $jsData['ppMode'] = getSetting('payments.paypal_live_mode') != null && getSetting('payments.paypal_live_mode') ? 'live' : 'sandbox';
            $jsData['showCookiesBox'] = getSetting('compliance.enable_cookies_box');
            $jsData['currency'] = App\Providers\SettingsServiceProvider::getAppCurrencyCode();
            $jsData['currencySymbol'] = App\Providers\SettingsServiceProvider::getWebsiteCurrencySymbol();
            $jsData['currencyPosition'] = App\Providers\SettingsServiceProvider::getWebsiteCurrencyPosition();
            $jsData['enable_age_verification_dialog'] = getSetting('compliance.enable_age_verification_dialog');
            $jsData['companyDefaultAvatar'] = asset('/img/default-avatar.jpg');
            $jsData['open_ai_enabled'] = getSetting('ai.open_ai_enabled');
            $jsData['siteName'] = getSetting('site.name');
        }
        JavaScript::put(['app'=>$jsData]);

        if (Auth::check()) {
            JavaScript::put([
                'user' => [
                    'username' => Auth::user()->username,
                    'user_id' => Auth::user()->id,
                    'companiesCount' => count(Auth::user()->companies),
                ],
            ]);
        }

        // Handling expired CSRF Tokens and Expired users sessions
        if (Session::has('sessionStatus') && Session::get('sessionStatus') == 'expired') {
            JavaScript::put(['app' => ['sessionStatus' => 'expired']]);
        }

        return $next($request);
    }
}
