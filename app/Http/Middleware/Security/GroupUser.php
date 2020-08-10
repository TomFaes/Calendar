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
        $group = $groupRepo->getGroup($request->route('group_id'));

        if (isset($group) === true) {
            //check if  logged in user is the admin of the group
            if ($group->admin_id == $user->id) {
                return $next($request);                
            }
        }
        if ($method == 'API') {
            return response()->json($message, $statusCode);
        } else {
            return redirect('/')->with('error', $message);
        }
    }
}