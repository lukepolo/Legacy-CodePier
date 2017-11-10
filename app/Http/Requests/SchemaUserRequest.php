<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchemaUserRequest extends FormRequest
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
            'password' => 'required',
            'schema_ids'=> 'required|array',
            'name' => 'required|alpha',
        ];
    }
}
