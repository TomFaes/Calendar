<?php
namespace App\Validators;

use Illuminate\Http\Request;

class GroupValidation extends Validation 
{
    public function validateCreateGroup(Request $request)
    {
        return $this->validate(
            $request, 
            [
                'name' => 'required|string|max:255',                
            ],
            [
                'name.required' => 'een groepsnaam is verplicht',
            ]
        );
    }
}
