<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GarageDetails;
use App\Models\HeaderLink;
use Illuminate\Support\Facades\Log;

class GarageDetailsController extends Controller
{
    public function index()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '18')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['garages'] = GarageDetails::all();
        return view('AutoCare.garage_details.index', $viewData);
    }

    public function create()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '18')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        return view('AutoCare.garage_details.create', $viewData);
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateGarage($request);

            // Prepare path
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $basePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/logo/';
            $destinationPath = public_path($basePath);

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Logo
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $extension = $logo->getClientOriginalExtension();
                $logoName = 'logo.' . $extension;
                $logo->move($destinationPath, $logoName);
                $validated['logo'] = $logoName;
            }

            // Banner
            if ($request->hasFile('banner')) {
                $banner = $request->file('banner');
                $extension = $banner->getClientOriginalExtension();
                $bannerName = 'home-banner.' . $extension;
                $banner->move($destinationPath, $bannerName);
                $validated['banner'] = $bannerName;
            }

            // Favicon
            if ($request->hasFile('favicon')) {
                $favicon = $request->file('favicon');
                $extension = $favicon->getClientOriginalExtension();
                $faviconName = 'favicon.' . $extension;
                $favicon->move($destinationPath, $faviconName);
                $validated['favicon'] = $faviconName;
            }

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
        $viewData['header_link'] = HeaderLink::where("menu_id", '18')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['garage'] = GarageDetails::findOrFail($id);
        return view('AutoCare.garage_details.create', $viewData);
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $this->validateGarage($request);

            $garage = GarageDetails::findOrFail($id);

            // Prepare path
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $basePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/logo/';
            $destinationPath = public_path($basePath);

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Logo
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $extension = $logo->getClientOriginalExtension();
                $logoName = 'logo.' . $extension;
                $logo->move($destinationPath, $logoName);
                $validated['logo'] = $logoName;
            }

            // Banner
            if ($request->hasFile('banner')) {
                $banner = $request->file('banner');
                $extension = $banner->getClientOriginalExtension();
                $bannerName = 'home-banner.' . $extension;
                $banner->move($destinationPath, $bannerName);
                $validated['banner'] = $bannerName;
            }

            // Favicon
            if ($request->hasFile('favicon')) {
                $favicon = $request->file('favicon');
                $extension = $favicon->getClientOriginalExtension();
                $faviconName = 'favicon.' . $extension;
                $favicon->move($destinationPath, $faviconName);
                $validated['favicon'] = $faviconName;
            }
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
            'eori_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:150',
            'mobile' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'zone' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'banner' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'favicon' => 'nullable|image|mimes:png,ico|max:512',
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
