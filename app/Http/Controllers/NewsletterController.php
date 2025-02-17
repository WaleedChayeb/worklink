<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageNewsletterRequest;
use App\Model\NewsletterEmail;
use Request;

class NewsletterController extends Controller
{
    /**
     * Displays the email unsubscribe page.
     */
    public function unsubscribe(Request $request)
    {
        return view('pages.unsubscribe');
    }

    /**
     * Adds new email entry to the newsletter table.
     * @param ManageNewsletterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addEmail(ManageNewsletterRequest $request)
    {
        $email = $request->get('email');
        try {
            NewsletterEmail::create(['email' => $email]);

            return response()->json(['success' => true, 'message' => __('You subscribed to the newsletter.')]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Removes an email out of the newsletter table.
     * @param ManageNewsletterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeEmail(ManageNewsletterRequest $request)
    {
        $email = $request->get('email');
        try {
            $email = NewsletterEmail::where(['email' => $email])->first();
            if ($email) {
                $email->delete();

                return response()->json(['success' => true, 'message' => __('You have unsubscribed from the newsletter.')]);
            } else {
                return response()->json(['false' => true, 'error' => __('Email address not found.')]);
            }
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
        }
    }
}
