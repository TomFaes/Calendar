<?php

namespace App\Http\Middleware\Edit;

use Closure;

class User
{
    /*
    |--------------------------------------------------------------------------
    | User Middleware
    |--------------------------------------------------------------------------
    |
    | This middleware will check if a user can be deleted 
    | and there are no teams in the season
    */
    
    public function handle($request, Closure $next)
    {
        $user = app('App\Repositories\Contracts\IUser');
        $user = $user->getUser($request->route('id'));

        if($user->groupUsers->count() == 0 && $user->seasonAdmin->count() == 0 && $user->userTeamsOne->count() == 0){
            return $next($request);
        }elseif($user->groupUsers->count() > 0){
            return redirect('user')->with('error', 'Deze gebruiker zit nog in '.$user->groupUsers->count().' groepen');
        }elseif($user->seasonAdmin->count() > 0){
            return redirect('user')->with('error', 'Deze gebruiker is nog admin van '.$user->seasonAdmin->count().' seizoenen');
        }elseif($user->userTeamsOne->count() > 0){
            return redirect('user')->with('error', 'Deze gebruiker zit nog in '.$user->seasonAdmin->count().' ploegen');
        }
        return redirect('user')->with('error', 'er is een fout');
    }
}
