<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DatabaseName implements Rule
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
        return preg_match('/^([a-zA-Z_])([a-zA-Z0-9_])+$/', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You must input a valid database name.';
    }
}
