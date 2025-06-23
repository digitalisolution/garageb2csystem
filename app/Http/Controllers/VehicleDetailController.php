<?php

namespace App\Http\Controllers;

use App\Models\VehicleDetail;
use Illuminate\Http\Request;

class VehicleDetailController extends Controller
{
    // Display a listing of vehicles
    public function index()
    {
        $vehicles = VehicleDetail::orderBy('id', 'desc')->get(); // Load 25 per page
        return view('AutoCare.vehicles.index', compact('vehicles'));
    }
    
    // Show the form for creating a new vehicle
    public function create()
    {
        return view('AutoCare.vehicles.create');
    }

    // Store a newly created vehicle in the database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'vehicle_reg_number' => 'required|string|max:20|unique:vehicle_details',
            'vehicle_category' => 'required|string|max:255',
            'vehicle_make' => 'required|string|max:255',
            'vehicle_model' => 'required|string|max:255',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'vehicle_cc' => 'nullable|string|max:10',
            'vehicle_fuel_type' => 'nullable|string|max:50',
            'vehicle_body_type' => 'nullable|string|max:50',
            'vehicle_bhp' => 'nullable|string|max:50',
            'vehicle_engine_number' => 'nullable|string|max:100',
            'vehicle_engine_size' => 'nullable|string|max:50',
            'vehicle_engine_code' => 'nullable|string|max:50',
            'vehicle_vin' => 'nullable|string|max:50',
            'vehicle_front_tyre_size' => 'nullable|string|max:50',
            'vehicle_rear_tyre_size' => 'nullable|string|max:50',
            'vehicle_colour' => 'nullable|string|max:50',
            'vehicle_first_registered' => 'nullable|date',
            'vehicle_chassis_no' => 'nullable|string|max:50',
            'vehicle_torque_settings' => 'nullable|string|max:50',
            'vehicle_mot_expiry_date' => 'nullable|date',
        ]);

        VehicleDetail::create($validatedData);

        return redirect()->route('AutoCare.vehicles.index')->with('success', 'Vehicle added successfully!');
    }

    // Display the specified vehicle
    public function show(VehicleDetail $vehicle)
    {
        return view('AutoCare.vehicles.show', compact('vehicle'));
    }

    // Show the form for editing the specified vehicle
    public function edit(VehicleDetail $vehicle)
    {
        return view('AutoCare.vehicles.create', compact('vehicle'));
    }

    // Update the specified vehicle in the database
    public function update(Request $request, VehicleDetail $vehicle)
    {
        // dd($request);
        $validatedData = $request->validate([
            'vehicle_reg_number' => 'required|string|max:20|unique:vehicle_details,vehicle_reg_number,' . $vehicle->id,
            'vehicle_category' => 'required|string|max:255',
            'vehicle_make' => 'required|string|max:255',
            'vehicle_model' => 'required|string|max:255',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'vehicle_cc' => 'nullable|string|max:10',
            'vehicle_fuel_type' => 'nullable|string|max:50',
            'vehicle_body_type' => 'nullable|string|max:50',
            'vehicle_bhp' => 'nullable|string|max:50',
            'vehicle_engine_number' => 'nullable|string|max:100',
            'vehicle_engine_size' => 'nullable|string|max:50',
            'vehicle_engine_code' => 'nullable|string|max:50',
            'vehicle_vin' => 'nullable|string|max:50',
            'vehicle_front_tyre_size' => 'nullable|string|max:50',
            'vehicle_rear_tyre_size' => 'nullable|string|max:50',
            'vehicle_colour' => 'nullable|string|max:50',
            'vehicle_first_registered' => 'nullable|date',
            'vehicle_chassis_no' => 'nullable|string|max:50',
            'vehicle_torque_settings' => 'nullable|string|max:50',
            'vehicle_mot_expiry_date' => 'nullable|date',
        ]);

        $vehicle->update($validatedData);

        return redirect()->route('AutoCare.vehicles.index')->with('success', 'Vehicle updated successfully!');
    }

    // Remove the specified vehicle from the database
    public function destroy(VehicleDetail $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('AutoCare.vehicles.index')->with('success', 'Vehicle deleted successfully!');
    }
}