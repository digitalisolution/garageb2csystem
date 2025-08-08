<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CarService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        try {
            $services = CarService::orderBy('sort_order', 'asc')->get();
            return view('AutoCare.services.index', compact('services'));
        } catch (\Throwable $e) {
            \Log::error("Error creating service: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        return view('AutoCare.services.create');
    }
    public function getServices()
    {
        try {
            $services = CarService::where('status', 1)->get();
            return response()->json(['services' => $services]);
        } catch (\Throwable $e) {
            \Log::error("Error get service: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
           $validated = $request->validate([
                'name' => 'required|string|max:100',
                'service_lead_time' => 'nullable|integer|max:50',
                'content' => 'nullable|string',
                'service_features' => 'nullable|string',
                'service_whats_include' => 'nullable|string',
                'slug' => 'nullable|string|max:100|unique:car_services,slug',
                'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
                'inner_image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
                'service_banner_path' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
                'display_status' => 'required|integer',
                'status' => 'required|boolean',
                'tax_class_id' => 'required|integer',
                'sort_order' => 'nullable|integer',
                'price_type' => 'required|string|in:fixed-price,call-now,quote-now,free',
                'cost_price' => 'required|numeric|min:0',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'robots_noindex_follow' => 'nullable|integer',
                'exclude_sitemap' => 'nullable|integer',
            ]);


            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service_icon';
                $destinationimgPath = public_path($imagePath);

                if (!file_exists($destinationimgPath)) {
                    mkdir($destinationimgPath, 0755, true);
                }

                $image->move($destinationimgPath, $imageName);
                $validated['image'] = $imageName;
            }


            if ($request->hasFile('inner_image')) {
                $innerImage = $request->file('inner_image');
                $innerImageName = $innerImage->getClientOriginalName();
                $innerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-inner-img';
                $destinationinnerPath = public_path($innerPath);

                if (!file_exists($destinationinnerPath)) {
                    mkdir($destinationinnerPath, 0755, true);
                }

                $innerImage->move($destinationinnerPath, $innerImageName);
                $validated['inner_image'] = $innerImageName; // <--- Save only the image name
            }

            if ($request->hasFile('service_banner_path')) {
                $bannerImage = $request->file('service_banner_path');
                $bannerImageName = $bannerImage->getClientOriginalName();
                $bannerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-banners';
                $destinationbannerPath = public_path($bannerPath);

                if (!file_exists($destinationbannerPath)) {
                    mkdir($destinationbannerPath, 0755, true);
                }

                $bannerImage->move($destinationbannerPath, $bannerImageName);
                $validated['service_banner_path'] = $bannerImageName; // <--- Save only the image name
            }

            // Create the CarService record
            CarService::create($validated);

            return redirect()->route('services.index')->with('success', 'Service created successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error creating service: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(CarService $service)
    {
        return view('AutoCare.services.create', compact('service'));
    }

    public function update(Request $request, CarService $service)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'service_lead_time' => 'nullable|integer|max:50',
            'content' => 'nullable|string',
            'service_features' => 'nullable|string',
            'service_whats_include' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:car_services,slug,' . $service->service_id . ',service_id',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'inner_image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'service_banner_path' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'display_status' => 'required|integer',
            'status' => 'required|boolean',
            'tax_class_id' => 'required|integer',
            'sort_order' => 'nullable|integer',
            'price_type' => 'required|string|in:fixed-price,call-now,quote-now,free',
            'cost_price' => 'required|numeric|min:0',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'robots_noindex_follow' => 'nullable|integer',
            'exclude_sitemap' => 'nullable|integer',
        ]);


        $data = collect($validated)->except(['image', 'inner_image', 'service_banner_path'])->toArray();
        try {
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $domainimgPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service_icon';
                $destinationimgPath = public_path($domainimgPath);

                if (!file_exists($destinationimgPath)) {
                    mkdir($destinationimgPath, 0755, true);
                }

                $image->move($destinationimgPath, $imageName);
                $data['image'] = $imageName;
            }

            if ($request->hasFile('inner_image')) {
                $innerImage = $request->file('inner_image');
                $innerImageName = $innerImage->getClientOriginalName();
                $innerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-inner-img';
                $destinationinnerPath = public_path($innerPath);
                if (!file_exists($destinationinnerPath)) {
                    mkdir($destinationinnerPath, 0755, true);
                }

                $innerImage->move($destinationinnerPath, $innerImageName);
                $data['inner_image'] = $innerImageName;
            }

            if ($request->hasFile('service_banner_path')) {
                $bannerImage = $request->file('service_banner_path');
                $bannerImageName = $bannerImage->getClientOriginalName();
                $bannerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-banners';
                $destinationbannerPath = public_path($bannerPath);

                if (!file_exists($destinationbannerPath)) {
                    mkdir($destinationbannerPath, 0755, true);
                }

                $bannerImage->move($destinationbannerPath, $bannerImageName);
                $data['service_banner_path'] = $bannerImageName; // <--- Save only the image name
            }


            $service->update($data);

            return redirect()->route('services.index')->with('success', 'Service updated successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error creating service: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function destroy(CarService $service)
    {
        try {
            $service->delete();
            return redirect()->route('services.index')->with('success', 'Service deleted successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error deleting service: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

}
