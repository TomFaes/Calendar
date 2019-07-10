<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IAbsence;
use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IUser;

use App\Validators\AbsenceValidation;

use App\Services\SeasonGeneratorService\GeneratorFactory;

class AbsenceController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @param  int  $seasonId
     * @return \Illuminate\Http\Response
     */
    public function index($seasonId) {
        $absences = $this->absence->getUserAbsence($seasonId, Auth::user()->id);
        $season = $this->season->getSeason($seasonId);
        $days = $this->getAvailablePlayDays($absences, $season);
        return view('season.editAbsence')->with('days', $days)->with('season', $season)->with('absences' , $absences);
    }
    
     /**
     * Store a newly created resource in storage.
     *
     * @param  int  $seasonId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $seasonId) {
        $this->absenceValidator->validateCreateAbsence($request);
        $this->absence->create($request, $seasonId, Auth::user()->id);
        return redirect()->to('season/'.$seasonId.'/absence/')->send();
    }
    
     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $seasonId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($seasonId, $id) {
        $seasonId = $this->absence->delete($id);
        return redirect()->to('season/'.$seasonId.'/absence/')->send();
    }
    
    /**
     * Return an array of days that the user can play and is not absence
     *
     * @param  Absence  $absence
     * @param  Season  $season
     * @return Array
     */
    protected function getAvailablePlayDays($absence, $season){
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $daysSeason = $seasonGenerator->getPlayDates($season->begin, $season->end);
        foreach($absence as $day){
            if(isset($daysSeason[$day->date]) === true){
                unset($daysSeason[$day->date]);
            }
        }
        return $daysSeason;
    }
}
