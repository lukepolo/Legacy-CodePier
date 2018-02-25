<?php

namespace App\Http\Requests;

use App\Rules\NotificationChannels;
use Illuminate\Foundation\Http\FormRequest;

class NotificationChannelsRequest extends FormRequest
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
            'slack_channel_preferences' => 'required',
            'slack_channel_preferences.*' => [
                'nullable',
                new NotificationChannels(),
            ],
        ];
    }
}
