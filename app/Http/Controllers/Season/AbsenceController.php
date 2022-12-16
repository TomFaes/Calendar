<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Http\Resources\AbsenceResource;
use App\Mail\RegisterAbsence;
use App\Models\Absence;
use App\Models\Season;
use Illuminate\Support\Facades\Mail;

class AbsenceController extends Controller
{
    public function index(Season $season) 
    {
        $absence = array();
        foreach($season->group->groupUsers AS $groupUser){
            $absence[$groupUser->id] = Absence::where('group_user_id', $groupUser->id)
                                        ->where('season_id', $season->id)
                                        ->orderBy('date', 'asc')
                                        ->get();
        }
        return response()->json($absence, 200);
    }
    
    public function store(AbsenceRequest $request, Season $season) 
    {
        $validated = $request->validated();
        $validated['season_id'] = $season->id;
        $absence = Absence::create($validated);
        return response()->json(new AbsenceResource($absence), 200);
    }
    
    public function destroy(Season $season, Absence $absence) 
    {
        $absence->delete();
        return response()->json("absence is removed", 204);
    }

    /**
     * sent a mail to all the users in the group to give up their absences for the selected season
     */
    public function sentMailRegisterAbsence(Season $season)
    {
        //$season = Season::find($season->id);
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
