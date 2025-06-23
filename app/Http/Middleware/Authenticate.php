<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            $guard = $request->route()->middleware()[1] ?? null;
            // dd( $guard);
            // \Log::info("Redirecting unauthenticated user. Guard: $guard");
    
            if ($guard === 'auth:customer') {
                return route('customer.login');
            } elseif ($guard === 'auth:web') {
                return route('webmaster.login');
            }
    
            return route('customer.login');
        }
    }
}
