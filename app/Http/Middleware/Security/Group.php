<?php
namespace App\Http\Middleware\Security;
use Closure;

class Group
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

        if ($request->route('id') > 0) 
        {
            $groupRepo = app('App\Repositories\Contracts\IGroup');
            $group = $groupRepo->getGroup($request->route('id'));
        }

        //If the group doesn't exist return an error message
        if(isset($group) === false)
        {
            return response()->json("There is no group with that id", 203);
        }
        //check if member is user
        $memberOfGroup = $this->checkIfUserIsMember($group, $user->id);

        //if a group is deleted there has to been some checks
        if (last(request()->segments()) == "delete" OR $request->method() == "DELETE" ) 
        {
            $seasonRepo = app('App\Repositories\Contracts\ISeason');
            $groupSeasons = $seasonRepo->getGroupOfSeason($request->route('id'));

            if(count($groupSeasons) > 0)
            {
                $message = "The group ".$group->name."  is connected to ".count($groupSeasons)." seasons";
                return response()->json($message, 203);
            }
            if ($group->admin_id != $user->id) 
            {
                $message = "You do not have the right to change ".$group->name;
                return response()->json($message, 203);
            }
            return $next($request);
        }

        //if you are not a member of the group
        if($memberOfGroup === false)
        {
            $message = "1 You do not have the right to the ".$group->name;
            return response()->json($message, 203);
        }

        //a user of a group can only have acces to the get method
        if($memberOfGroup == 'User')
        {
            if($request->method() != 'GET'){
                $message = "You do not have the right to change the group: ".$group->name;
                return response()->json($message, 203);
            }
            return $next($request);
        }

        //if the user is admin he can proceed
        if($memberOfGroup == 'Admin')
        {
            return $next($request);
        }

        //this should never happen.
        $message = "There is an undefined reason why you can't acces to the group: ".$group->name;
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
