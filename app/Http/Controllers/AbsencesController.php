<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IAbsence;
use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IUser;

use App\Validators\AbsenceValidation;

class AbsencesController extends Controller
{
    /** @var App\Repositories\Contracts\IAbsence */
    protected $absence;
    /** @var  App\Repositories\Contracts\ISeason */
    protected $season;
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    
    /** @var App\Validators\AbsenceValidation */
    protected $absenceValidator;
    
    public function __construct(IAbsence $absenceRepo, AbsenceValidation $absenceValidator, ISeason $seasonRepo, IUser $userRepo) {
        $this->middleware('auth');
        $this->absence = $absenceRepo;
        $this->absenceValidator = $absenceValidator;
        $this->season = $seasonRepo;
        $this->user = $userRepo;
    }
    
    public function show($seasonId) {
        $days = $this->getArrayAvailableAbsences($seasonId, Auth::user()->id);
        $season = $this->season->getSeason($seasonId);
        $absences = $this->absence->getUserAbsence($seasonId, Auth::user()->id);
        
        return view('season.editAbsence')->with('days', $days)->with('season', $season)->with('absences' , $absences);
    }
    
    public function update(Request $request, $seasonId) {
        $this->absenceValidator->validateCreateAbsence($request);
        $this->absence->create($request, $seasonId, Auth::user()->id);
        return redirect()->to('absence/'.$seasonId)->send();
    }
    
    public function destroy($id) {
        $seasonId = $this->absence->delete($id);
        return redirect()->to('absence/'.$seasonId)->send();
    }
    
    private function getArrayAvailableAbsences($seasonId){
        //array of days in a season
        $daysSeason = $this->season->get7DaySeasonDates($seasonId);
        //get absences user
        $daysAbsence = $this->absence->getUserAbsence($seasonId, Auth::user()->id);
        
        foreach($daysAbsence as $day){
            if(isset($daysSeason[$day->date]) === true){
                unset($daysSeason[$day->date]);
            }
        }
        return $daysSeason;
    }
    
    
}
