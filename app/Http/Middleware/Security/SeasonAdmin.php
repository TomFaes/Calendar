<?php

namespace App\Http\Middleware\Security;
use Closure;
use Illuminate\Support\Facades\Auth;

class SeasonAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $season = $request->route('season') ?? null;

        if(isset($season) === false){
            return response()->json("There is no season with that id", 203);
        }

        if($season->admin_id != Auth::user()->id){
            return response()->json("Only season admins have access to these pages", 203);
        }
        return $next($request);
    }
}