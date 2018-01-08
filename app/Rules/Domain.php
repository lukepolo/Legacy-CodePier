<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Domain implements Rule
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
        if (str_contains($value, [
            '.',
            'http',
            ':',
            'www',
        ])) {
            return preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,63}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,63}$/', $value) > 0;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You must supply a valid domain name or alias.';
    }
}
