<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'sometimes|string|min:8|confirmed',
        ];
    }

    public function message(){
        return [
            'password.required' => 'paswoord is a required field',
            'password.min' => 'password needs to have atleast 8 characters'
        ];
    }
}
