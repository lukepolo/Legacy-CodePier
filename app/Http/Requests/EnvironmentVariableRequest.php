<?php

namespace App\Http\Requests;

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
            'variable' => 'required|environmentVariable',
            'value' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'environment_variable' => 'Please enter a valid environment variable name. Must not start with a number, contain special characters or spaces.'
        ];
    }
}
