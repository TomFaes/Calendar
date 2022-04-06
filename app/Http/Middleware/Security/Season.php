<?php

namespace App\Http\Middleware\Security;
use Closure;

class Season
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
        $user = auth()->user();

        $seasonRepo = app('App\Repositories\Contracts\ISeason');
        $season = $seasonRepo->getSeason($request->route('id'));

        if(isset($season) === false)
        {
            return response()->json("There is no season with that id", 203);
        }

        //check if member is user
        $memberOfGroup = $this->checkIfUserIsMember($season->group, $user->id);

        //if you are not a member of the group
        if($memberOfGroup === false)
        {
            $message = "You do not have the right to the this season: ".$season->name;
            return response()->json($message, 203);
        }

        //if the method is a get you can proceed
        if($request->method() == 'GET' && last(request()->segments()) != "delete")
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

        //this should never happen.
        $message = "Season middleware: There is an undefined reason why you can't acces to the users of this group: ".$season->name;
        return response()->json($message, 203);
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