<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class SiteFileRequest extends FormRequest
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
            'file_path' => 'required|string',
            'content' => 'required',
        ];
    }
}
