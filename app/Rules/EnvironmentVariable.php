<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnvironmentVariable implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // http://stackoverflow.com/questions/2821043/allowed-characters-in-linux-environment-variable-names
        // https://regex101.com/r/nBGmWp/1
        return preg_match('/^([a-zA-Z_])([a-zA-Z0-9_])+$/', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please enter a valid environment variable name. Must not start with a number, contain special characters or spaces.';
    }
}
