<?php

namespace App\Http\Requests\Site;

use App\Rules\Domain;
use Illuminate\Validation\Rule;
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
        return [
            'domain' => [
                'required',
                new Domain,
                Rule::unique('sites', 'name')->where(function ($query) {
                    return $query->where('user_id', \Auth::user()->id)
                        ->where('pile_id', $this->get('pile_id'))
                        ->where('deleted_at', null);
                }),
            ],
            'pile_id' => 'required|integer',
            'wildcard_domain' => 'nullable|boolean',
        ];
    }
}
