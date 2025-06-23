<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GarageDetails;

class GarageDetailsController extends Controller
{
    public function index()
    {
        $garages = GarageDetails::all();
        return view('AutoCare.garage_details.index', compact('garages'));
    }

    public function create()
    {
        return view('AutoCare.garage_details.create'); // Blade file for create/edit
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'garage_name' => 'required|string|max:255', // Garage name is required and must be a string up to 255 characters
                'company_number' => 'nullable|string|max:50', // Optional, string up to 50 characters
                'vat_number' => 'nullable|string|max:50', // Optional, string up to 50 characters
                'eori_number' => 'nullable|string|max:50', // Optional, string up to 50 characters
                'phone' => 'nullable|string|max:150', // Optional, string up to 20 characters
                'mobile' => 'required|string|max:150', // Optional, string up to 20 characters
                'email' => 'required|email|max:255', // Required, valid email format, up to 255 characters
                'street' => 'nullable|string|max:255', // Optional, string up to 255 characters
                'city' => 'nullable|string|max:100', // Optional, string up to 100 characters
                'zone' => 'nullable|string|max:100', // Optional, string up to 100 characters
                'country' => 'nullable|string|max:100', // Optional, string up to 100 characters
                'description' => 'nullable|string|max:500', // Optional, string up to 500 characters
                'garage_opening_time' => 'nullable|string|max:500', // Optional, valid time in "HH:mm" format
                'social_facebook' => 'nullable|string|max:255', // Optional, valid URL format
                'social_instagram' => 'nullable|string|max:255', // Optional, valid string format
                'social_twitter' => 'nullable|string|max:255', // Optional, valid string format
                'social_youtube' => 'nullable|string|max:255', // Optional, valid string format
                'google_map_link' => 'nullable|string|max:255', // Optional, valid string format
                'longitude' => 'nullable|numeric', // Optional, valid numeric value
                'latitude' => 'nullable|numeric', // Optional, valid numeric value
                'google_reviews_link' => 'nullable|string|max:255', // Optional, valid string format
                'google_reviews_stars' => 'nullable|numeric|between:0,5', // Optional, numeric between 0 and 5
                'google_reviews_count' => 'nullable|integer|min:0', // Optional, integer with minimum value of 0
                'website_url' => 'nullable|string|max:255', // Optional, valid string format
                'notes' => 'nullable|string', // Optional, string up to 1000 characters
                'status' => 'nullable|boolean', // Optional, must be true or false
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors()); // Display validation errors
        }


        try {
            GarageDetails::create($validated);
            return redirect()->route('AutoCare.garage_details.index')->with('success', 'Garage created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating garage:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create garage.');
        }

    }


    public function show($id)
    {
        $garage = GarageDetails::findOrFail($id);
        return view('AutoCare.garage_details.show', compact('garage'));
    }

    public function edit($id)
    {
        $garage = GarageDetails::findOrFail($id);
        return view('AutoCare.garage_details.create', compact('garage')); // Blade file for create/edit
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'garage_name' => 'required|string|max:255', // Garage name is required and must be a string up to 255 characters
            'company_number' => 'nullable|string|max:50', // Optional, string up to 50 characters
            'vat_number' => 'nullable|string|max:50', // Optional, string up to 50 characters
            'eori_number' => 'nullable|string|max:50', // Optional, string up to 50 characters
            'phone' => 'nullable|string|max:150', // Optional, string up to 20 characters
            'mobile' => 'required|string|max:150', // Optional, string up to 20 characters
            'email' => 'required|email|max:255', // Required, valid email format, up to 255 characters
            'street' => 'nullable|string|max:255', // Optional, string up to 255 characters
            'city' => 'nullable|string|max:100', // Optional, string up to 100 characters
            'zone' => 'nullable|string|max:100', // Optional, string up to 100 characters
            'country' => 'nullable|string|max:100', // Optional, string up to 100 characters
            'description' => 'nullable|string|max:500', // Optional, string up to 500 characters
            'garage_opening_time' => 'nullable|string|max:500', // Optional, valid time in "HH:mm" format
            'social_facebook' => 'nullable|string|max:255', // Optional, valid URL format
            'social_instagram' => 'nullable|string|max:255', // Optional, valid string format
            'social_twitter' => 'nullable|string|max:255', // Optional, valid string format
            'social_youtube' => 'nullable|string|max:255', // Optional, valid string format
            'google_map_link' => 'nullable|string', // Optional, valid string format
            'longitude' => 'nullable|numeric', // Optional, valid numeric value
            'latitude' => 'nullable|numeric', // Optional, valid numeric value
            'google_reviews_link' => 'nullable|string', // Optional, valid string format
            'google_reviews_stars' => 'nullable|numeric|between:0,5', // Optional, numeric between 0 and 5
            'google_reviews_count' => 'nullable|integer|min:0', // Optional, integer with minimum value of 0
            'website_url' => 'nullable|string|max:255', // Optional, valid string format
            'notes' => 'nullable|string', // Optional, string up to 1000 characters
            'status' => 'nullable|boolean', // Optional, must be true or false
        ]);

        $garage = GarageDetails::findOrFail($id);
        // dd($garage->update($validated));
        $garage->update($validated);

        return redirect()->route('AutoCare.garage_details.index')->with('success', 'Garage updated successfully.');
    }

    public function destroy($id)
    {
        $garage = GarageDetails::findOrFail($id);
        $garage->delete();

        return redirect()->route('AutoCare.garage_details.index')->with('success', 'Garage deleted successfully.');
    }
}
