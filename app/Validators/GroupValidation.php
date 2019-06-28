<?php
namespace App\Validators;

use Illuminate\Http\Request;

class GroupValidation extends Validation {
    public function validateCreateGroup(Request $request){
        return $this->validate(
            $request, 
            [
                'name' => 'required|string|max:255',
                'userId' => 'required|',
                
            ],
            [
                'name.required' => 'een groepsnaam is verplicht',
                'userId.required' => 'admin is een verplicht veld',
            ]
        );
    }
    
    public function validateAddUser(Request $request){
        return $this->validate(
            $request, 
            [
                'groupUsers' => 'required',                
            ],
            [
                'groupUsers.required' => 'Selecteer een user',
            ]
        );
    }
}
