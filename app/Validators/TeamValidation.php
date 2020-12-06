<?php

namespace App\Validators;

use Illuminate\Http\Request;

class TeamValidation extends Validation
{
    public function validateCreateTeam(Request $request)
    {
        return $this->validate(
            $request,
            [
                'date' => 'required|',
                'team' => 'required|',
                'ask_for_replacement' => 'nullable|boolean'
            ],
            [
                'date.required' => 'Afwezig op is een verplicht veld',
                'team.required' => 'Team op is een verplicht veld',
                'ask_for_replacement' => 'allow replacements must be true or false'
            ]
        );
    }
}