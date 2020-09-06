<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaddleWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->merge([ 'passthrough' => json_decode($this->input('passthrough', '{}')) ]);

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
            'alert_name' => [ 'required' ]
        ];
    }
}
