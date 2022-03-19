<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required',
            'name' => 'required',
            'email' => 'required||unique:users||email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'First name is required',
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Not a valid email adres',
            'password.required' => 'Password is required',
            'password.min' => 'Password needs atleast 6 characters',
            'confirm_password.required' => 'You need to repeat the password',
            'confirm_password.same' => 'The password is not the same',
        ];
    }
}
