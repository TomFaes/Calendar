<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'email' => 'required||unique:users,email,'.auth()->user()->id,
        ];
    }

    public function messages()
    {
        return [
            'firstname.required' => 'Firstname is required',
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
        ];
    }
}
