<?php

namespace App\Http\Requests;

use App\Rules\TranslateObjectRule;
use Illuminate\Foundation\Http\FormRequest;

class UserCookieRequest extends FormRequest
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
            'group_id' => [ 'in:1,2,3,4,5' ],
            'description' => [ new TranslateObjectRule() ]
        ];
    }
}
