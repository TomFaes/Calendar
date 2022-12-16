<?php

namespace App\Http\Middleware\Security;
use Closure;

class GroupUser
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
        $group = $request->route('group') ?? null;

        if(isset($group) === false)
        {
            return response()->json("There is no group with that id", 203);
        }

        //check if member is user
        $memberOfGroup = $this->checkIfUserIsMember($group, $user->id);

        // if the method is a get you can proceed
        if($request->method() == 'GET' && last(request()->segments()) != "delete")
        {
            if($memberOfGroup == 'User')
            {
                return $next($request);
            }
        }

        if($memberOfGroup != 'Admin')
        {
            return response()->json("Group user middleware: You do not have the right to change this group: ".$group->name, 203);
        } 
        //You can't delete the admin of a group without selecting another one
        if (last(request()->segments()) == "delete"OR $request->method() == "DELETE") 
        {
            $groupUser = $request->route('group_user');
            if($groupUser->user_id == $group->admin_id){
                return response()->json("You can't delete the admin of  a group, first choose another admin.", 202);
            }
        }
        //check if  logged in user is the admin of the group
        if($memberOfGroup == 'Admin')
        {
            return $next($request);            
        }

        //this should never happen.
        $message = "Group user middleware: There is an undefined reason why you can't acces to the users of this group: ".$group->name;
        return response()->json($message, 203);
    }

    protected function checkIfUserIsMember($group, $userId){
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