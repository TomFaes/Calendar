<?php

namespace App\Validators;

use Illuminate\Http\Request;

class GroupUserValidation extends Validation 
{

    public function validateGroupUser(Request $request, $id = 0)
    {
        return $this->validate(
            $request,
            [
                'firstname' => 'required',
                'name' => 'required',
                'email' => 'nullable|email',
            ],
            [
                'firstname.required' => 'Firstname is required',
                'name.required' => 'Name is required',
                'email.email' => 'This is not a valid email',
            ]
        );
    }
}