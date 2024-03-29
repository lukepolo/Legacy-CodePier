<?php

namespace App\Http\Requests;

use App\Rules\LanguageType;
use Illuminate\Foundation\Http\FormRequest;

class LanguageSettingRequest extends FormRequest
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
            'params' => 'array|nullable',
            'setting' => 'required|string',
            'language' => ['required', new LanguageType],
        ];
    }
}
