<?php

namespace App\Http\Requests\Site;

use App\Rules\Repository;
use Illuminate\Foundation\Http\FormRequest;

class SiteRepositoryRequest extends FormRequest
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
            'branch' => 'required|string',
            'repository'=> [new Repository],
            'site_type' => 'required|string',
            'web_directory' => 'nullable|string',
            'user_repository_provider_id' => 'required_unless:custom_provider,true|nullable|integer',
        ];
    }
}
