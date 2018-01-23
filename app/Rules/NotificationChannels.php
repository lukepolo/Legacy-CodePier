<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotificationChannels implements Rule
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
        return preg_match('/^([a-z0-9-_]){1,21}$/', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Channel names can be in any language and up to 21 characters in length. They may include lowercase letters, non-Latin characters, numbers, and hyphens (-).';
    }
}
