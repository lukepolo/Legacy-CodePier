<?php

namespace App\Http\Requests\Site;

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
            'type' => 'required|string',
            'branch' => 'required|string',
            'wildcard_domain' => 'nullable|boolean',
            'framework' => 'nullable|string',
            'repository'=> 'string',
            'zerotime_deployment' => 'nullable|boolean',
            'web_directory' => 'nullable|string',
            'user_repository_provider_id' => 'required_unless:custom_provider,true|integer',
            'keep_releases' => 'required_if:zerotime_deployment,true',
        ];
    }
}
