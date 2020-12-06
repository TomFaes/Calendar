<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Repositories\Contracts\IAbsence;
use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IUser;

use App\Validators\AbsenceValidation;

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
    
    public function __construct(IAbsence $absenceRepo, AbsenceValidation $absenceValidator, ISeason $seasonRepo, IUser $userRepo) 
    {
        $this->middleware('auth:api');
        $this->middleware('absence:', ['only' => ['store', 'index']]);
        
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
    public function index($seasonId) 
    {
        $season = $this->season->getSeason($seasonId);
        $absence = array();
        foreach($season->group->groupUsers AS $groupUser){
            $absence[$groupUser->id] = $this->absence->getUserAbsence($seasonId, $groupUser->id);
        }
        return response()->json($absence, 200);
    }
    
     /**
     * Store a newly created resource in storage.
     *
     * @param  int  $seasonId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $seasonId) 
    {
        $this->absenceValidator->validateCreateAbsence($request);
        $absence = $this->absence->create($request->all(), $seasonId);
        return response()->json($absence, 200);
    }
    
     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $seasonId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) 
    {
        $this->absence->delete($id);
        return response()->json("absence is removed", 204);
    }
    
   
}
