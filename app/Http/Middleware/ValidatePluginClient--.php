<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ClientPlugins;

class ValidatePluginClient
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
    $client = ClientPlugins::where('client_id', $request->client_id)->first();

    if (!$client || $client->secret_token !== $request->token) {
        abort(403, 'Invalid client credentials.');
    }

    // Optionally pass client object to controller via request
    $request->merge(['validated_client' => $client]);

    return $next($request);
}

}
