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
            'server_name' => 'required:server_name',
            'server_provider_id' => 'required_without:custom',
            'services' => 'required',
            'server_region' => 'required_without:custom|integer',
            'server_option' => 'required_without:custom|integer',
            'pile_id' => 'required_without:site',
            'site' => 'integer',
            'type' => 'valid_server_type',
        ];
    }
}
