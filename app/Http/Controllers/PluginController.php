<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientPlugins;

class PluginController extends Controller
{
    public function showSearchForm(Request $request)
    {
        // $client = ClientPlugins::where('client_id', $request->client_id)->first();

        // if (!$client || $client->secret_token !== $request->token) {
        //     abort(403, 'Unauthorized');
        // }

        return view('plugin.search');
    }

    public function redirectToSearchResults(Request $request)
    {
        // Validate client again
        // $client = ClientPlugins::where('client_id', $request->client_id)->first();

        // if (!$client || $client->secret_token !== $request->token) {
        //     abort(403, 'Unauthorized request');
        // }

        $url = route('tyreslist', [
            'vrm' => $request->vrm,
            'fitting_type' => $request->fitting_type,
            // 'client_id' => $request->client_id,
            // 'token' => $request->token
        ]);

        return redirect()->away($url);
    }
}
