<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class WebsiteOriginRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $pattern = 'https?:\/\/[\w\d\-\.]+\.\w{2,6}\/?';
        return preg_match("~{$pattern}~", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please provide a correct website URL';
    }
}
