<?php

namespace App\Validators;

use Illuminate\Http\Request;

class UserValidation extends Validation {
    public function validateCreateUser(Request $request, $id = null){
        return $this->validate(
            $request, 
            [
                'name' => 'required|string|max:255',
                'firstname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users'.($id ? ",id,$id" : ''),
                'password' => 'sometimes|string|min:6|confirmed',
            ],
            [
                'name.required' => 'Achternaam is een verplicht veld',
                'firstname.required' => 'Voornaam is een verplicht veld',
                'email.required' => 'email is een verplicht veld',
                'password.required' => 'paswoord is een verplicht veld',
                'password.min' => 'paswoord moet minstens 6 karakters hebben'
            ]
        );
    }

    public function validatUserPassword(Request $request){
        return $this->validate(
            $request,
            [
                'password' => 'sometimes|string|min:6|confirmed',
            ],
            [
                'password.required' => 'paswoord is een verplicht veld',
                'password.min' => 'paswoord moet minstens 6 karakters hebben'
            ]
        );
    }
    
    
    
    
    
    
    
}
