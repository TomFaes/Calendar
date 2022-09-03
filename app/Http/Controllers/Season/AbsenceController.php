<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Http\Resources\AbsenceResource;
use App\Mail\RegisterAbsence;
use App\Models\Season;
use App\Repositories\Contracts\IAbsence;
use App\Repositories\Contracts\ISeason;
use Illuminate\Support\Facades\Mail;

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

    /**
     * sent a mail to all the users in the group to give up their absences for the selected season
     */
    public function sentMailRegisterAbsence($seasonId)
    {
        $season = Season::find($seasonId);
        $mailBCC = array();

        foreach($season->group->groupUsers AS $groupUser){
            if(isset($groupUser->user) === false){
                continue;
            }
            if($groupUser->user->email == ""){
                continue;
            }

            $mailBCC[] = $groupUser->user->email;
        }
    
        Mail::to($season->admin->email)
            ->bcc($mailBCC)
            ->send(new RegisterAbsence($season));

        return response()->json("Mails sent to users of the season: ".$season->name, 200);
    }
}
