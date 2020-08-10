<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;


class HttpsProtocol {
    /*
    |--------------------------------------------------------------------------
    | HttpsProtocol Middleware
    |--------------------------------------------------------------------------
    |
    | This middleware will redirect all HTTP traffic to HTTPS
    |
    */
    public function handle($request, Closure $next)
    {
            if (!$request->secure() && App::environment() === 'production') {
                return redirect()->secure($request->getRequestUri());
            }
            return $next($request); 
    }
}
