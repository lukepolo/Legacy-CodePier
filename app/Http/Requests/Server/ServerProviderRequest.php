<?php

namespace App\Http\Requests\Server;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServerProviderRequest extends FormRequest
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
            'token' => 'required',
            'account' =>  [
                'required',
                Rule::unique('user_server_providers')->where(function ($query) {
                    return $query->where('user_id', \Auth::user()->id)
                        ->where('deleted_at', null);
                })
            ],
        ];
    }
}
