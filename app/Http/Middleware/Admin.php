<?php

namespace App\Http\Middleware;

use Closure;

class Admin
{
    /*
    |--------------------------------------------------------------------------
    | Admin Middleware
    |--------------------------------------------------------------------------
    |
    | This middleware will check if a user has the correct rights to do an action
    */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role = 'Admin')
    {
        if(auth()->user()->role == 'Admin'){
            return $next($request);
        }elseif(auth()->user()->role == $role){
            return $next($request);
        }
        return redirect('home')->with('error', 'Je hebt niet de juiste rechten rechten');
    }
}
