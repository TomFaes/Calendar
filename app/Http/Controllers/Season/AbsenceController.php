<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Http\Resources\AbsenceResource;

use App\Repositories\Contracts\IAbsence;
use App\Repositories\Contracts\ISeason;

class AbsenceController extends Controller
{
    protected $absenceRepo;
    protected $seasonRepo;
    
    public function __construct(IAbsence $absenceRepo, ISeason $seasonRepo) 
    {
        $this->middleware('absence:', ['only' => ['store', 'index']]);
        
        $this->absenceRepo = $absenceRepo;
        $this->seasonRepo = $seasonRepo;
    }

    public function index($seasonId) 
    {
        $season = $this->seasonRepo->getSeason($seasonId);
        $absence = array();
        foreach($season->group->groupUsers AS $groupUser){
            $absence[$groupUser->id] = $this->absenceRepo->getUserAbsence($seasonId, $groupUser->id);
        }
        return response()->json($absence, 200);
    }
    
    public function store(AbsenceRequest $request, $seasonId) 
    {
        $absence = $this->absenceRepo->create($request->all(), $seasonId);
        return response()->json(new AbsenceResource($absence), 200);
    }
    
    public function destroy($id) 
    {
        $this->absenceRepo->delete($id);
        return response()->json("absence is removed", 204);
    }
}
