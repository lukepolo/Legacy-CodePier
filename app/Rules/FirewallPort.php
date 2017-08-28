<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FirewallPort implements Rule
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
        return $value == '*' || (
            is_numeric($value) &&
            ($value >= 0 && $value <= 65536)
        );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You must supply a valid port number (0 to 65536).';
    }
}
