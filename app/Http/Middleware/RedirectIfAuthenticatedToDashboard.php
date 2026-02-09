<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedToDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

        public function handle($request, Closure $next)
        {
            // Check if the user is authenticated
            if (Auth::guard('web')->check()) {
                // Redirect authenticated users to the /search route
                return redirect('/dashboard');
            }
    
            // Allow unauthenticated users to proceed
            return $next($request);
        }
}
