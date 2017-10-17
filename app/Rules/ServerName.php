<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ServerName implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^[a-zA-Z0-9\.\-]+$/', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Your server name can only contain alphanumeric characters, dashes, and periods.';
    }
}
