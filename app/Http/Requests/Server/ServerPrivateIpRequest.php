<?php

namespace App\Http\Requests\Server;

use App\Rules\PrivateIP;
use Illuminate\Foundation\Http\FormRequest;

class ServerPrivateIpRequest extends FormRequest
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
            'ip_addresses.*' => ['required', new PrivateIP],
        ];
    }
}
