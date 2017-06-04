<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class SiteRequest extends FormRequest
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
        if ($this->get('domainless')) {
            return [
                'domain' => 'required',
                'pile_id' => 'required|integer',
            ];
        }

        return [
            'domain' => 'required_if:domainless,false|domain',
            'pile_id' => 'required|integer',
        ];
    }
}
