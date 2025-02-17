<?php

namespace App\Http\Requests;

use App\Rules\NonEmptySelectField;
use App\Rules\Sluggable;
use Illuminate\Foundation\Http\FormRequest;

class JobCreateRequest extends FormRequest
{
    public static function getRules()
    {
        return [
            'title' => ['required', new Sluggable, 'max:191'],
            'location' => 'required|max:191',
            'type_id' => ['required', new NonEmptySelectField],
            'category_id' => ['required', new NonEmptySelectField],
            'application_link' => 'required|max:1000',
            'skills' => 'required',
            'description' => 'required',
            'salary' => 'max:191',
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
