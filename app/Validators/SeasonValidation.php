<?php

namespace App\Validators;

use Illuminate\Http\Request;

class SeasonValidation extends Validation
{
    public function validateCreateSeason(Request $request)
    {
        return $this->validate(
            $request,
            [
                'name' => 'required|string|max:255',
                'groupId' => 'required|',
                'begin' => 'required|date|',
                'end' => 'required|date|',
                'hour' => 'required|',
                'type' => 'required|string|max:255'

            ],
            [
                'name.required' => 'een seizoensnaam is verplicht',
                'groupId.required' => 'group is een verplicht veld',
                'begin.required' => 'begin datum is een verplicht veld',
                'begin.date' => 'begin datum moet een datum format bevatten',
                'end.required' => 'eind datum is een verplicht veld',
                'end.date' => 'eind datum moet een datum format bevatten',
                'hour.required' => 'Uur is een verplicht veld',
                'type' => 'type is een verplicht veld'

            ]
        );
    }

}