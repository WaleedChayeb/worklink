<?php

namespace App\Http\Requests;

use App\Rules\NonEmptySelectField;
use App\Rules\Sluggable;
use Illuminate\Foundation\Http\FormRequest;

class CompanyCreateRequest extends FormRequest
{
    public static function getRules()
    {
        return [
            'company_name' => ['required', new Sluggable, 'max:191'],
            'company_hq'  => 'required|max:191',
            'company_website_url'  => 'required|max:191',
            'company_email'  => 'required|email|max:191',
            'company_description' => 'required',
            'company_logo' => 'required',
        ];
    }

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
        return self::getRules();
    }
}
