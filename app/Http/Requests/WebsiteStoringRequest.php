<?php

namespace App\Http\Requests;

use App\Rules\WebsiteOriginRule;
use Illuminate\Foundation\Http\FormRequest;

class WebsiteStoringRequest extends FormRequest
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
            'origin' => [ 'required', new WebsiteOriginRule() ]
        ];
    }
}
