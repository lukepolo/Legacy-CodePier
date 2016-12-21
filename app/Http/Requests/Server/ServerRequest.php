<?php

namespace App\Http\Requests\Server;

use Illuminate\Foundation\Http\FormRequest;

class ServerRequest extends FormRequest
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
            'user_id' => \Auth::user()->id,
            'server_name' => 'required|string',
            'server_provider_id' => 'required|domain',
            'services' => 'required',
            'server_region' => 'required|integer',
            'server_option' => 'required|integer',
            'server_provider_features' => 'required',
            'server_features' => 'required',
            'pile_id' => 'required_unless:site',
            'site' => 'integer'
        ];
    }
}
