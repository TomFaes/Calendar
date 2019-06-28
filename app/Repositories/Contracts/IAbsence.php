<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;


interface IAbsence
{
    public function getAllAbsences();
    public function getAbsence($absenceId);
    public function getUserAbsence($seasonId, $userId);
    public function getSeasonAbsence($seasonId);
    public function getSeasonAbsenceArray($seasonId);
    
    public function create(Request $request, $seasonId, $userId);
    public function delete($absenceId);
    public function deleteSeasonAbsence($seasonId);
}