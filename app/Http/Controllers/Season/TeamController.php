<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Repositories\Contracts\ITeam;


class TeamController extends Controller
{
    /** @var App\Repositories\Contracts\ITeam */
    protected $team;
    
    public function __construct(ITeam $teamRepo) 
    {
        $this->middleware('auth:api');        
        $this->team = $teamRepo;
    }

    public function updateRange(Request $request) 
    {
        $userId = auth()->user()->id;
        $updateTeams = json_decode($request['teamRange'], true);
        foreach($updateTeams AS $index=>$groupUser){
            $team = $this->team->getTeam($index);
            if($team->season->admin_id != $userId){
                continue;
            }
            $team->group_user_id = $groupUser != "" ? $groupUser : NULL;
            $this->team->saveTeam($team);
        }
        return response()->json("", 200);
    }

    public function askForReplacement($id)
    {
        $team = $this->team->getTeam($id);

        if(auth()->user()->id != $team->group_user->user_id){
            return response()->json("User is not the same as the group user", 200);
        }
        $this->team->askForReplacement($team);
        return response()->json("Ask for replacement is set to true", 200);
    }

    public function cancelRequestForReplacement($id){
        $team = $this->team->getTeam($id);

        if(auth()->user()->id != $team->group_user->user_id){
            return response()->json("User is not the same as the group user", 200);
        }
        $this->team->cancelRequestForReplacement($team);
        return response()->json("Replacement is set to false", 200);
    }

    public function confirmReplacement($id){
        $team = $this->team->getTeam($id);

        $group_user_id = 0;

        foreach($team->season->group->groupUsers AS $groupUser){
            if(auth()->user()->id == $groupUser->user_id){
                $group_user_id = $groupUser->id;
                break;
            }
        }

        if($group_user_id == 0){
            return response()->json("Group user is not found", 200);
        }

        //check if user is already playing this day
        $dayTeams = $this->team->getTeamsOnDate($team->season_id, $team->date);
        foreach($dayTeams AS $dayTeam){
            if(auth()->user()->id == $dayTeam->group_user_id){
                return response()->json("User is already playing on this day", 200);
            }
        }

        $this->team->confirmReplacement($team, $group_user_id);
        return response()->json("Confirm replacement", 200);
    }
}
