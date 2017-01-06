<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkerRequest extends FormRequest
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
            'command' => 'required',
            'auto_start'        => 'nullable|boolean',
            'auto_restart'      => 'nullable|boolean',
            'user'              => 'required|alpha',
            'number_of_workers' => 'required|integer',
        ];
    }
}
