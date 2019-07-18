<?php

namespace App\Http\Middleware\Edit;

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
    public function handle($request, Closure $next)
    {
        $season = app('App\Repositories\Contracts\ISeason');
        $season = $season->getSeason($request->route('seasonId'));

        if($season->teams->count() > 0){
            return redirect('season')->with('error', 'Afwezigheden toevoegen is niet meer mogelij voor seizoen '.$season->name);
        }
    }
}
