<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BittRequest extends FormRequest
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
            'user' => 'required|string',
            'title' => 'required|string',
            'script' =>  'required|string',
//            'systems' =>  'required|array',
//            'category' => 'required|integer',
//            'private' =>  'nullable|boolean',
            'description' => 'required|string',
        ];
    }
}
