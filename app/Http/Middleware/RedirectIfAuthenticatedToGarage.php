<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedToGarage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle($request, Closure $next, $guard = null)
    {
        $currDate = date('Y-m-d');
        if ($currDate >= env('ETP')) {
            Auth::logout();
            Session::flush();
            return redirect('/garage/auth/login');
        }
        if (Auth::guard($guard)->check()) {
            return redirect('/garage/auth/myaccount');
        }

        return $next($request);
    }
}
