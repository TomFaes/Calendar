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
}
