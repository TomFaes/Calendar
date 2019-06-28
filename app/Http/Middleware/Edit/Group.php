<?php

namespace App\Http\Middleware\Edit;

use Closure;

class Group
{
    /*
    |--------------------------------------------------------------------------
    | Group Middleware
    |--------------------------------------------------------------------------
    |
    | This middleware will check if the logged in user is admin of the group.
    | if the group is going to be deleted then there will be an additional check
    | of the amount of users and if the group is connected to a season
    */
    
    public function handle($request, Closure $next, $type = '')
    {        
        $group = app('App\Repositories\Contracts\IGroup');
        $group = $group->getGroup($request->route('group'));
        
        
        if($group->admin_id == auth()->user()->id && $group->users->count() == 0 && $group->seasonGroup->count() == 0 && $type == 'delete'){
            return $next($request);
        }elseif($group->admin_id == auth()->user()->id && $type == ''){
            return $next($request);
        }elseif($group->admin_id != auth()->user()->id){
            return redirect('group')->with('error', 'Je hebt geen admin rechten om deze groep aan te passen');
        }elseif($group->users->count() > 0){
            return redirect('group')->with('error', 'Er zitten nog '.$group->users->count().' personen in deze groep');
        }elseif($group->seasonGroup->count() > 0){
            return redirect('group')->with('error', 'Deze groep is nog gekoppeld aan '.$group->seasonGroup->count().' seizoen(en)');
        }
        return redirect('group')->with('error', 'er is een fout'.$type);
    }
}
