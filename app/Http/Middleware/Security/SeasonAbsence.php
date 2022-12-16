<?php

namespace App\Http\Middleware\Security;
use Closure;
use Illuminate\Support\Facades\Auth;

class SeasonAbsence
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {       
        $season = $request->route('season') ?? null;

        if(isset($season) === false){
            return response()->json("There is no season with that id", 203);
        }

        if($season->is_generated != 0){
            return response()->json("This season (".$season->name.") is already generated, you can't change your absence anymore", 203);
        }

        if($season->admin_id == Auth::user()->id){
            return $next($request); 
        }

        //check if member is user
        $memberOfGroup = $this->checkIfUserIsMember($season->group);
        if($request->method() == 'GET'){
            if($memberOfGroup == 'User'){
                return $next($request);
            }
        }

        $canChangeAbsence = $this->checkIfUserCanChangeAbsence($season, $request->group_user_id);
        if ($canChangeAbsence == false){
            return response()->json("You can't change this absence in this season", 203);
        }
        if($canChangeAbsence == true){
            return $next($request);
        }

        $message = "Absence middleware: There is an undefined reason why you can't acces to the group: ".$season->name;
        return response()->json($message, 203);
    }

    protected function checkIfUserCanChangeAbsence($season, $requestGroupUser)
    {
        foreach($season->group->groupUsers AS $groupUser)
        {
            if($requestGroupUser != $groupUser->id){
                continue;
            }
            if($groupUser->user_id != Auth::user()->id){
                continue;
            }
            return true;
        }
        return false;
    }

    protected function checkIfUserIsMember($group)
    {
        foreach($group->groupUsers AS $groupUser){
            if($groupUser->user_id == Auth::user()->id){
                return "User";
            }
        }
        return false;
    }
}