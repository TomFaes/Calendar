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
        $statusCode = 200;
        $message = "Absence middleware: You do not have the right to change this season";

        $seasonRepo = app('App\Repositories\Contracts\ISeason');
        $season = $seasonRepo->getSeason($request->route('season_id'));

        if ($season->teams->count() == 0) {
            return $next($request);
        }
        
        if ($method == 'API') {
            return response()->json($message, $statusCode);
        } else {
            return redirect('/')->with('error', $message);
        }
    }
}