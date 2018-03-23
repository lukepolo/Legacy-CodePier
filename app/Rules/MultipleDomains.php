<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MultipleDomains implements Rule
{
    private $formRequest;

    public function __construct(FormRequest $formRequest)
    {
        $this->formRequest = $formRequest;
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
        $this->formRequest->domains = array_filter(explode(',', implode(',', explode(' ', $this->formRequest->get('domains')))));

        return ! $this->formRequest->get('wildcard') || count($this->formRequest->domains) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Wildcard certificates can only have one domain.';
    }
}
