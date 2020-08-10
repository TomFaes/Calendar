<?php
namespace App\Validators;


use Illuminate\Http\Request;

class AbsenceValidation extends Validation 
{

    public function validateCreateAbsence(Request $request)
    {
        return $this->validate(
            $request,
            [
                'date' => 'required|',
            ],
            [
                'date.required' => 'Afwezig op is een verplicht veld',
            ]
        );
    }
}