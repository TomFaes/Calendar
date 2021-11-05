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
        $message = "You do not have the right to change this group";

        $user = auth()->user();

        //an admin user can change a group but cannot delete it
        if ($user->role == "Admin") {
            //if($request->method() != "DELETE"){
            if (last(request()->segments()) != "delete" ) {
                return $next($request);
            }
        }

        //Group admins can change groups
        if ($request->route('id') > 0) {
            $groupRepo = app('App\Repositories\Contracts\IGroup');
            $group = $groupRepo->getGroup($request->route('id'));
            //webhost doesn't aloud a delete method
            //if($request->method() == "DELETE"){ 
            if (last(request()->segments()) == "delete" ) {
                $seasonRepo = app('App\Repositories\Contracts\ISeason');
                $groupSeasons = $seasonRepo->getGroupOfSeason($request->route('id'));
                if (count($groupSeasons) == 0 && $group->admin_id == $user->id) {
                    return $next($request);
                } else if ($group->admin_id != $user->id) {
                    $message = "You do not have the right to change this group";
                } else {
                    $message = "This group is connected to ".count($groupSeasons)." seasons";
                }
            } else {
                if ($group->admin_id == $user->id) {
                    return $next($request);
                }
                if($request->method() == 'GET')
                {
                    foreach($group->groupUsers AS $groupUser){
                        if($groupUser->user_id == $user->id){
                            return $next($request);
                        }
                    }
                }
            }
        }

        if ($method == 'API') {
            return response()->json($message, 200);
        } else {
            return redirect('/')->with('error', $message);
        }
    }
}
