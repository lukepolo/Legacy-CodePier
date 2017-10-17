<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LanguageType implements Rule
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
        return collect([
            'PHP',
            'Ruby',
        ])->contains($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You must supply a language CodePier can handle.';
    }
}
