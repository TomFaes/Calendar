<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstname' => 'required',
            'name' => 'required',
            'ignore_user' => 'nullable|boolean',
            'ignore_plays' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'firstname.required' => 'Firstname is required',
            'name.required' => 'Name is required',
        ];
    }
}