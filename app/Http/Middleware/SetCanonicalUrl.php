<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetCanonicalUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Define the canonical URL
         $canonicalUrl = URL::current();
        if ($request->is('/')) {
            $canonicalUrl = url('/') . '/'; 
        }

        if ($request->is('search') && $request->filled(['width', 'profile', 'diameter'])) {
            $width = $request->input('width');
            $profile = $request->input('profile');
            $diameter = $request->input('diameter');

            $canonicalUrl = url("/tyres-size/{$width}-{$profile}-{$diameter}");
        }
        view()->share('canonical', $canonicalUrl);

        return $next($request);
    }
}
