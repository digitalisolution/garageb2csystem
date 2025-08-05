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
    try {
        $validatedData = $request->validate([
            'vehicle_reg_number' => 'required|string|max:20',
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

        // Check if vehicle already exists
        $existingVehicle = VehicleDetail::where('vehicle_reg_number', $validatedData['vehicle_reg_number'])->first();

        if ($existingVehicle) {
            $existingVehicle->update($validatedData);
            $message = 'Vehicle updated successfully!';
        } else {
            VehicleDetail::create($validatedData);
            $message = 'Vehicle added successfully!';
        }

        return redirect()->route('AutoCare.vehicles.index')->with('success', $message);
    } catch (\Throwable $e) {
        \Log::error("Error storing/updating Vehicle: " . $e->getMessage());
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
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
        try{
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
        } catch (\Throwable $e) {
            \Log::error("Error Updating Vehicle: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Remove the specified vehicle from the database
    public function destroy(VehicleDetail $vehicle)
    {
        try{
        $vehicle->forceDelete();

        return redirect()->route('AutoCare.vehicles.index')->with('success', 'Vehicle deleted successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error Deleting Vehicle: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}