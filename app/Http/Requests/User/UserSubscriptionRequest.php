<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserSubscriptionRequest extends FormRequest
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
            'plan' => 'required',
            'token' => [function($attribute, $value, $fail) {
                if(!\Auth::user()->hasCardOnFile() && empty($attribute)) {
                    $fail($attribute. ' is required.');
                }
            }],
        ];
    }
}
