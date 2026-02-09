<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderTypes;
use App\Models\CarserviceProduct;

class PluginController extends Controller
{
    public function showSearchForm(Request $request)
    {
         $fittingTypes = OrderTypes::where('status', 1)->get();
        $carserviceProduct = new CarserviceProduct();
        $carMakes = $carserviceProduct->getMakes();
        return view('plugin.search',compact('fittingTypes','carMakes'));
    }

    // public function showVehicleSearch(Request $request)
    // {
    // $vrm = $request->query('vrm');
    // $fittingType = $request->query('fitting_type', 'fully_fitted');

    // // Fetch vehicle data by VRM
    // $vehicleData = $this->vrmService->lookup($vrm);
    //     return view('plugin.vrm',compact('vrm','fittingTypes','vehicleData'));
    // }

    public function redirectToSearchResults(Request $request)
    {
       if ($request->has('vrm')) {
        // Case A: VRM is provided → redirect to vehicle details page
        $url = route('plugin.vehicle-search', [
            'vrm' => $request->vrm,
            'fitting_type' => $request->fitting_type,
        ]);

        return redirect()->away($url);
    }
    $make = $request->input('make');
    $model = $request->input('model');
    $year = $request->input('year');
    $engine = $request->input('engine');

    // Optional: validate required fields
    if (!$make || !$model || !$year) {
        return back()->withErrors(['error' => 'Missing vehicle details']);
    }

    // Save vehicle data in session for reuse
    session([
        'vehicleData' => compact('make', 'model', 'year', 'engine')
    ]);
        return redirect()->route('service');
    }
}
