<?php

namespace App\Http\Requests\Site;

use App\Rules\ValueGreaterThanZero;
use Illuminate\Foundation\Http\FormRequest;

class SiteLifeline extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'threshold' => ['required', new ValueGreaterThanZero()],
        ];
    }
}
