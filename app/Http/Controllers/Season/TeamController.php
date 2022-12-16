<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function updateRange(Request $request, Season $season)
    {
        $userId = auth()->user()->id;

        $updateTeams = json_decode($request['teamRange'], true);
        foreach($updateTeams AS $index=>$groupUser){
            $team = Team::find($index);
            if($team->season->admin_id != $userId){
                continue;
            }
            $team->group_user_id = $groupUser != "" ? $groupUser : NULL;
            $team->save();
        }
        return response()->json("", 200);
    }

    public function askForReplacement(Season $season, Team $team)
    {
        if(auth()->user()->id != $team->group_user->user_id){
            return response()->json("User is not the same as the group user", 200);
        }
        $team->ask_for_replacement = 1;
        $team->save();
        return response()->json("Ask for replacement is set to true", 200);
    }

    public function cancelRequestForReplacement(Season $season, Team $team){
        if(auth()->user()->id != $team->group_user->user_id){
            return response()->json("User is not the same as the group user", 200);
        }
        $team->ask_for_replacement = 0;
        $team->save();
        return response()->json("Replacement is set to false", 200);
    }

    public function confirmReplacement(Season $season, Team $team){
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
        $dayTeams = Team::where('season_id', $team->season_id)
                        ->where('date', $team->date)
                        ->get();
        foreach($dayTeams AS $dayTeam){
            if(auth()->user()->id == $dayTeam->group_user_id){
                return response()->json("User is already playing on this day", 200);
            }
        }
        $team->group_user_id = $group_user_id;
        $team->ask_for_replacement = 0;
        $team->save();
        return response()->json("Confirm replacement", 200);
    }
}
