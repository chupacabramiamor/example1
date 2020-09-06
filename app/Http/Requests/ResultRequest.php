<?php

namespace App\Http\Requests;

use App\Rules\WebsiteOriginRule;
use Illuminate\Foundation\Http\FormRequest;

class ResultRequest extends FormRequest
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
            'url' => [ 'required', new WebsiteOriginRule() ],
            'scan_key' => [ 'required' ],
            'is_finished' => [ 'boolean' ],
            'pages_count' => [ 'int' ],
            'cookies.*.name' => [ 'required', 'min:1' ],
            'cookies.*.privider' => [ 'regex:/(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]/' ],
            'cookies.*.path' => [ 'regex:/^\/[\/\w\d\-\_\s\%]+/' ],
            'cookies.*.expired_at' => [ 'required', 'integer' ],
            'cookies.*.httpOnly' => [ 'required', 'boolean' ],
            'cookies.*.secure' => [ 'required', 'boolean' ],
            'cookies.*.session' => [ 'required', 'boolean' ],
            'cookies.*.sameSite' => [ 'required' ],
            'cookies.*.priority' => [ 'required' ],
        ];
    }
}
