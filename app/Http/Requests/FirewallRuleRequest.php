<?php

namespace App\Http\Requests;

use App\Rules\FirewallPort;
use Illuminate\Foundation\Http\FormRequest;

class FirewallRuleRequest extends FormRequest
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
            'from_ip' => 'nullable|ip',
            'description' => 'required',
            'type' => 'required|string',
            'port' => ['required', new FirewallPort],
        ];
    }
}
