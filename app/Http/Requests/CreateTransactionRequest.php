<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'job_id' => 'required',
            'plan_id' => 'required',
            'taxes' => '',
            'amount' => 'required',
            'provider' => '',
            'transaction_type' => 'required',
            'billing_address' => 'min:3|max:255',
            'first_name' => 'min:1|max:255',
            'last_name' => 'min:1|max:255',
            'country' => 'min:1|max:255',
            'state' => 'min:1|max:255',
            'postcode' => 'min:1|max:255',
            'city' => 'min:1|max:255',
            'manual_payment_files' => '',
            'manual_payment_description' => '',
        ];
    }
}
