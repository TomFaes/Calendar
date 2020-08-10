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
        $statusCode = 200;
        $message = "Season middleware: You do not have the right to change this season";
        $user = auth()->user();

        $seasonRepo = app('App\Repositories\Contracts\ISeason');
        $season = $seasonRepo->getSeason($request->route('id'));

        if (isset($season) === true) {
            //check if  logged in user is the admin of the group
            if ($season->admin_id == $user->id) {
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