<?php

namespace App\Http\Middleware\Edit;

use Closure;


class Season
{
    /*
    |--------------------------------------------------------------------------
    | Season Middleware
    |--------------------------------------------------------------------------
    |
    | This middleware will check if the logged in user is admin of the season 
    | and there are no teams in the season
    */
    /**
     * will check if the logged in user is admin of the season and there are no teams in the season
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /*
    public function handle($request, Closure $next)
    {
        $season = app('App\Repositories\Contracts\ISeason');
        $season = $season->getSeason($request->route('id'));
        
        if($season->admin_id == auth()->user()->id && $season->team->count() == 0){
            return $next($request);
        }elseif($season->admin_id != auth()->user()->id){
            return redirect('season')->with('error', 'Je hebt geen admin rechten om dit seizoen aan te passen');
        }elseif($season->team->count() > 0){
            return redirect('season')->with('error', 'Er zitten nog '.$season->team->count().' teams in dit seizoen');
        }
        return redirect('season')->with('error', 'Je hebt geen rechten of er zitten nog teams in het seizoen');
    }
    */
    public function handle($request, Closure $next, $type = '')
    {
        $season = app('App\Repositories\Contracts\ISeason');
        $season = $season->getSeason($request->route('season'));
        
        
        if($season->admin_id == auth()->user()->id && $season->team->count() == 0 && $type == 'delete'){
            return $next($request);
        }elseif($season->admin_id == auth()->user()->id && $type == ''){
            return $next($request);
        }elseif($season->admin_id != auth()->user()->id){
            return redirect('season')->with('error', 'Je hebt geen admin rechten om dit season aan te passen');
        }elseif($season->team->count() > 0){
            return redirect('season')->with('error', 'Er zitten nog '.$season->team->count().' teams in dit seizoen');
        }
        
        return redirect('season')->with('error', 'er is een fout '.$type);
    }
}
