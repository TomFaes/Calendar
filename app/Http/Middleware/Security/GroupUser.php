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
        $statusCode = 200;
        $message = "Group user middleware: You do not have the right to change this group";
        $user = auth()->user();

        $groupRepo = app('App\Repositories\Contracts\IGroup');
        $groupUserRepo = app('App\Repositories\Contracts\IGroupUser');
        $group = $groupRepo->getGroup($request->route('group_id'));

        if (isset($group) === true) {
            //You can't delete the admin of a group without selecting another one
            if (last(request()->segments()) == "delete") {
                $groupUser = $groupUserRepo->getGroupUser($request->route('id'));
                if($groupUser->user_id == $group->admin_id){
                    return response()->json("You can't delete the admin of  a group, first choose another admin.", 202);
                }
            }
            //check if  logged in user is the admin of the group
            if ($group->admin_id == $user->id) {
                return $next($request);            
            }
            // if the method is a get you can proceed
            if($request->method() == 'GET' && last(request()->segments()) != "delete")
                {
                    foreach($group->groupUsers AS $groupUser){
                        if($groupUser->user_id == $user->id){
                            return $next($request);
                        }
                    }
                }
        }
        if ($method == 'API') {
            return response()->json($message, $statusCode);
        } else {
            return redirect('/')->with('error', $message);
        }
    }
}