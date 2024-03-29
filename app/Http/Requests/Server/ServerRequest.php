<?php

namespace App\Http\Requests\Server;

use App\Rules\ServerName;
use App\Rules\ServerType;
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
            'server_name' => ['required', new ServerName],
            'server_provider_id' => 'required_without:custom',
            'user_server_provider_id' => 'required_with:server_provider_id',
            'services' => 'required',
            'server_region' => 'required_without:custom|integer',
            'server_option' => 'required_without:custom|integer',
            'site' => 'integer',
            'type' => [new ServerType],
        ];
    }
}
