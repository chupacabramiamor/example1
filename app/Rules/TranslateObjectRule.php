<?php

namespace App\Rules;

use App\Services\LngService;
use Illuminate\Contracts\Validation\Rule;

class TranslateObjectRule implements Rule
{

    private $lngSvc;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->lngSvc = app(LngService::class);
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
        if (!is_array($value)) {
            return false;
        }

        if (isset($value[0])) {
            return false;
        }

        foreach ($value as $code => $value) {
            if (!in_array($code, $this->lngSvc->codeList())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('transobj_incorrect_structure');
    }
}
