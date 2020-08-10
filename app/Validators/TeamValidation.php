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
                'playerOneId' => 'required|',
            ],
            [
                'date.required' => 'Afwezig op is een verplicht veld',
                'team.required' => 'Team op is een verplicht veld',
                'playerOneId.required' => 'Speler 1 is een verplicht veld'
            ]
        );
    }
}