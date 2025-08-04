<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GarageDetails;
use Illuminate\Support\Facades\Log;

class GarageDetailsController extends Controller
{
    public function index()
    {
        $garages = GarageDetails::all();
        return view('AutoCare.garage_details.index', compact('garages'));
    }

    public function create()
    {
        return view('AutoCare.garage_details.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateGarage($request);

            GarageDetails::create($validated);

            return redirect()->route('AutoCare.garage_details.index')
                ->with('success', 'Garage created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Throwable $e) {
            Log::error('Garage Create Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
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
        return view('AutoCare.garage_details.create', compact('garage'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $this->validateGarage($request);

            $garage = GarageDetails::findOrFail($id);
            $garage->update($validated);

            return redirect()->route('AutoCare.garage_details.index')
                ->with('success', 'Garage updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Throwable $e) {
            Log::error('Garage Update Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $garage = GarageDetails::findOrFail($id);
            $garage->delete();

            return redirect()->route('AutoCare.garage_details.index')
                ->with('success', 'Garage deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Garage Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Validate garage data (shared by store/update).
     */
    private function validateGarage(Request $request): array
    {
        return $request->validate([
            'garage_name' => 'required|string|max:255',
            'company_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'eori_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:150',
            'mobile' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'zone' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'garage_opening_time' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|string|max:500',
            'social_instagram' => 'nullable|string|max:500',
            'social_twitter' => 'nullable|string|max:500',
            'social_youtube' => 'nullable|string|max:500',
            'google_map_link' => 'nullable|string|max:1000',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'google_reviews_link' => 'nullable|string|max:1000',
            'google_reviews_stars' => 'nullable|numeric|between:0,5',
            'google_reviews_count' => 'nullable|integer|min:0',
            'website_url' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
        ]);
    }
}
