<?php

namespace App\Http\Middleware\Security;

use Closure;

class Absence
{
    /*
    |--------------------------------------------------------------------------
    | Absence Middleware
    |--------------------------------------------------------------------------
    |
    | This middleware will check it is still possible to add absences 
    | to a season 
    */
    /**
     * will check if the logged in user is admin of the season and there are no teams in the season
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role = "Admin", $method = 'API')
    {
        $user = auth()->user();

        $seasonRepo = app('App\Repositories\Contracts\ISeason');
        $season = $seasonRepo->getSeason($request->route('season_id'));

        if(isset($season) === false)
        {
            return response()->json("There is no season with that id", 203);
        }

        if($season->is_generated != 0)
        {
            return response()->json("This season (".$season->name.") is already generated, you can't change your absence anymore", 203);
        }

        //check if member is user
        $memberOfGroup = $this->checkIfUserIsMember($season->group, $user->id);
        if($request->method() == 'GET')
        {
            if($memberOfGroup == 'User')
            {
                return $next($request);
            }
        }

         //check if  logged in user is the admin of the group
         if ($season->admin_id == $user->id) 
         {
            return $next($request); 
        }

         //check if member is user
         $canChangeAbsence = $this->checkIfUserCanChangeAbsence($season, $user->id, $request->group_user_id);

        if ($canChangeAbsence == false) 
        {
            return response()->json("You can't change your absence in this season", 203);
        }

        if($canChangeAbsence == true)
        {
            return $next($request);
        }

        //this should never happen.
        $message = "Absence middleware: There is an undefined reason why you can't acces to the group: ".$season->name;
        return response()->json($message, 203);
    }

    protected function checkIfUserCanChangeAbsence($season, $userId, $requestGroupUser)
    {
        $canChange = false;
        if($season->admin_id == $userId)
        {
            return true;
        }
        foreach($season->group->groupUsers AS $groupUser)
        {
            if($groupUser->user_id == $userId AND $requestGroupUser == $groupUser->id)
            {
                return true;
            }
        }
        return $canChange;
    }

    protected function checkIfUserIsMember($group, $userId)
    {
        $member = false;
        if($group->admin_id == $userId)
        {
            return "Admin";
        }
        foreach($group->groupUsers AS $groupUser)
        {
            if($groupUser->user_id == $userId)
            {
                return "User";
            }
        }
        return $member;
    }
}