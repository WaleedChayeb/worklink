<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveNewContactMessageRequest;
use App\Model\ContactMessage;
use App\Model\Country;
use App\Model\NewsletterEmail;
use App\Model\Tax;
use App\Model\UserReport;
use App\Providers\EmailsServiceProvider;
use App\Providers\InstallerServiceProvider;
use App\User;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Models\Setting;
use Zip;

class GenericController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function countries()
    {
        // find taxes for all countries
        $allCountriesAppliedTaxes = Tax::query()
            ->select('taxes.*')
            ->join('country_taxes', 'taxes.id', '=', 'country_taxes.tax_id')
            ->join('countries', 'country_taxes.country_id', '=', 'countries.id')
            ->where('countries.name', '=', 'All')->get();

        $countries = Country::where('name', '!=', 'All')->with(['taxes'])->get();
        if (count($allCountriesAppliedTaxes)) {
            foreach ($countries as $country) {
                foreach ($allCountriesAppliedTaxes as $appliedTax) {
                    $country->taxes->add($appliedTax);
                }
            }
        }

        return response()->json([
            'countries'=> $countries,
        ]);
    }

    /**
     * Sets user locale.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLanguage(Request $request)
    {
        $locale = getSetting('site.default_site_language');

        if (Auth::check()) {
            $user = Auth::user();
            $user->settings = collect(array_merge($user->settings->toArray(), ['locale'=>$request->route('locale')]));
            $user->save();
            try {
                $locale = $user->settings['locale'];
            }
            catch (\Exception $e){
                $locale = 'en';
            }
        } else {
            $locale = $request->route('locale');
            Cookie::queue('app_locale', $locale, 356, null, null, null, false, false, null);
        }

        // Resetting cached translation files ( for frontend )
        App::setLocale($locale);
        $langPath = app()->langPath().'/'.$locale;
        session()->put('app_translations', file_get_contents($langPath.'.json'));

        return redirect()->back();
    }

    /**
     * Contact page main page.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contact(Request $request)
    {
        return view('pages.contact', []);
    }

    /**
     * Sends contact message.
     * @param SaveNewContactMessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Sends contact message.
     * @param SaveNewContactMessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendContactMessage(SaveNewContactMessageRequest $request)
    {
        ContactMessage::create([
            'email' => $request->get('email'),
            'subject' => $request->get('subject'),
            'message' => $request->get('message'),
        ]);
        if (getSetting('admin.send_notifications_on_contact')) {
            // Send admin notifications
            $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
            foreach ($adminEmails as $user) {
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $user->email,
                        'subject' => __('Action required | New contact message received'),
                        'title' => __('Hello, :name,', ['name' => $user->name]),
                        'content' => __('There is a new contact message on :siteName that requires your attention.', ['siteName' => getSetting('site.name')]),
                        'quote' => $request->get('message'),
                        'replyTo' => $request->get('email'),
                        'button' => [
                            'text' => __('Go to admin'),
                            'url' => route('voyager.dashboard').'/contact-messages',
                        ],
                    ]
                );
            }
        }

        return back()->with('success', __('Message sent.'));
    }

    /**
     * Manually resending verification emails method.
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendConfirmationEmail()
    {
        $user = Auth::user();
        $user->sendEmailVerificationNotification();

        return response()->json(['success' => true, 'message' => __('Verification email sent successfully.')]);
    }

    /**
     * Display the user verify page.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userVerifyEmail()
    {
        return view('vendor.auth.verify', []);
    }

    /**
     * Generates custom theme and saves the new colors to settings table.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function generateCustomTheme(Request $request)
    {
        $themingServer = 'https://themes-v2.qdev.tech';
        try {
            $response = InstallerServiceProvider::curlGetContent($themingServer.'?'.http_build_query($request->all()));
            $response = json_decode($response);
            if ($response->success) {
                Setting::where('key', 'colors.theme_color_code')->update(['value'=>$request->get('color_code')]);
                Setting::where('key', 'colors.theme_gradient_from')->update(['value'=>$request->get('gradient_from')]);
                Setting::where('key', 'colors.theme_gradient_to')->update(['value'=>$request->get('gradient_to')]);
                if (extension_loaded('zip')) {
                    $contents = InstallerServiceProvider::curlGetContent($themingServer.'/'.$response->path);
                    Storage::disk('tmp')->put('theme.zip', $contents);
                    $zip = Zip::open(storage_path('app/tmp/theme.zip'));
                    $zip->extract(public_path('css/theme/'));
                    Storage::disk('tmp')->delete('theme.zip');

                    return response()->json(['success' => true, 'data'=>['path'=>$response->path, 'doBrowserRedirect' => false], 'message' => __('Theme generated & updated the frontend.')], 200);
                }

                return response()->json(['success' => true, 'data'=>['path'=>$response->path, 'doBrowserRedirect' => true], 'message' => $response->message], 200);
            } else {
                return response()->json(['success' => false, 'error'=>$response->error], 500);
            }
        } catch (\Exception $exception) {
            return (object) ['success' => false, 'error' => 'Error: "'.$exception->getMessage().'"'];
        }
    }

    /**
     * Saves license.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLicense(Request $request)
    {
        try {
            $licenseCode = $request->get('product_license_key');
            $license = InstallerServiceProvider::gld($licenseCode);

            if (isset($license->error)) {
                return response()->json(['success' => false, 'error' => $license->error], 500);
            }
            Storage::disk('local')->put('installed', json_encode(array_merge((array) $license, ['code'=>$licenseCode])));
            Setting::where('key', 'license.product_license_key')->update(['value'=>$licenseCode]);

            return response()->json(['success' => true, 'message' => __('License key updated')], 200);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'error' => 'Error: "'.$exception->getMessage().'"'], 500);
        }
    }

    /**
     * Method used for saving user reports.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReport(Request $request)
    {
        $fromUserID = Auth::check() ? Auth::user()->id : null;
        $reportedJobID = $request->get('job_id');
        $reportedCompanyID = $request->get('company_id');
        $reportType = $request->get('type');
        $details = $request->get('details');
        try {
            $data = [
                'user_id' => $fromUserID,
                'job_id' => $reportedJobID,
                'company_id' => $reportedCompanyID,
                'type' => $reportType,
                'status' => UserReport::$statusMap[0],
                'details' => $details,
            ];
            UserReport::create($data);

            return response()->json(['success' => true, 'message' => __('Report sent.')]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [__('An internal error has occurred.')], 'message'=>$exception->getMessage()]);
        }
    }

    public function exportNewsletterUsers()
    {
        $fileName = 'newsletter-export-'.uniqid();
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$fileName.'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        $columns = ['Email'];
        $emails = NewsletterEmail::get();
        $callback = function () use ($emails, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($emails as $email) {
                fputcsv($file, [$email['email']]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function clearAppCache(Request $request) {
        Artisan::call('cache:clear');
        return response()->json(['success' => true, 'message' => __("Application cache cleared successfully")], 200);
    }

    public function markBannerAsSeen(Request $request) {
        $id = $request->get('id');
        Cookie::queue('dismissed_banner_'.$id, true, 356, null, null, null, false, false, null);
        return response()->json(['success' => true, 'message' => __("Banner marked as seen")], 200);
    }
}
