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
                'name' => 'required|string|max:50',
                'group_id' => 'required|',
                'begin' => 'required|date|',
                'end' => 'required|date|',
                'start_hour' => 'required|',
                'type' => 'required|string|max:255',
                'public' => 'nullable|boolean',
                'allow_replacement' => 'nullable|boolean'
            ],
            [
                'name.required' => 'een seizoensnaam is verplicht',
                'group_id.required' => 'group is een verplicht veld',
                'begin.required' => 'begin datum is een verplicht veld',
                'begin.date' => 'begin datum moet een datum format bevatten',
                'end.required' => 'eind datum is een verplicht veld',
                'end.date' => 'eind datum moet een datum format bevatten',
                'start_hour.required' => 'Uur is een verplicht veld',
                'type' => 'type is een verplicht veld',
                'public' => 'public must be true or false',
                'allow_replacement' => 'allow replacements must be true or false'
            ]
        );
    }

}