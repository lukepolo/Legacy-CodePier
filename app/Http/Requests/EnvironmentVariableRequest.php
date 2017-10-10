<?php

namespace App\Http\Requests;

use App\Rules\EnvironmentVariable;
use Illuminate\Foundation\Http\FormRequest;

class EnvironmentVariableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'variable' => ['required', new EnvironmentVariable],
            'value' => 'required',
        ];
    }
}
