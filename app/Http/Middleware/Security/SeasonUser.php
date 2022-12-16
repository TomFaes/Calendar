<?php

namespace App\Http\Middleware\Security;
use Closure;
use Illuminate\Support\Facades\Auth;

class SeasonUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $method = 'API')
    {
        $season = $request->route('season') ?? null;

        if(isset($season) === false){
            return response()->json("There is no season with that id", 203);
        }

        //check if  logged in user is the admin of the season
        if ($season->admin_id == Auth::user()->id){
            return $next($request); 
        }

        //check if member is user
        $memberOfGroup = $this->checkIfUserIsMember($season->group);

        //if you are not a member of the group
        if($memberOfGroup === false){
            $message = "You are not a member of this season: ".$season->name;
            return response()->json($message, 203);
        }
        return $next($request);
    }

    protected function checkIfUserIsMember($group)
    {
        foreach($group->groupUsers AS $groupUser)
        {
            if($groupUser->user_id == Auth::user()->id){
                return "User";
            }
        }
        return false;
    }
}