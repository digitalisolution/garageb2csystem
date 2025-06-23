<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CarService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = CarService::orderBy('sort_order', 'asc')->get();
        return view('AutoCare.services.index', compact('services'));
    }

    public function create()
    {
        return view('AutoCare.services.create');
    }
    public function getServices()
    {
        $services = CarService::where('status', 1)->get();
        return response()->json(['services' => $services]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'service_lead_time' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:car_services,slug',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Validate image
            'inner_image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Validate image
            'service_banner_path' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
            'display_status' => 'required|integer',
            'status' => 'required|integer',
            'sort_order' => 'nullable|integer',
            'cost_price' => 'required|numeric|min:0',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'robots_noindex_follow' => 'nullable|integer',
            'exclude_sitemap' => 'nullable|integer',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $image->storeAs('uploads/services/icon', $imageName, 'public');
            $data['image'] = $imageName;
        }

        if ($request->hasFile('inner_image')) {
            $innerImage = $request->file('inner_image');
            $innerImageName = $innerImage->getClientOriginalName();
            $innerImage->storeAs('uploads/services/image', $innerImageName, 'public');
            $data['inner_image']->inner_image = $innerImageName;
        }


        if ($request->hasFile('service_banner_path')) {
            $banner = $request->file('service_banner_path');
            $bannerName = $banner->getClientOriginalName();
            $banner->storeAs('uploads/services/banners', $bannerName, 'public');
            $data['service_banner_path'] = $bannerName;
        }

        // Create the CarService record
        CarService::create($data);

        return redirect()->route('services.index')->with('success', 'Service created successfully!');
    }



    public function edit(CarService $service)
    {
        return view('AutoCare.services.create', compact('service'));
    }

    public function update(Request $request, CarService $service)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'service_lead_time' => 'nullable|string|max:50',
            'content' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:car_services,slug,' . $service->service_id . ',service_id',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'inner_image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'service_banner_path' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
            'display_status' => 'required|integer',
            'status' => 'required|integer',
            'sort_order' => 'nullable|integer',
            'cost_price' => 'required|numeric|min:0',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'robots_noindex_follow' => 'nullable|integer',
            'exclude_sitemap' => 'nullable|integer',
        ]);

        // Handle image uploads
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $image->storeAs('uploads/services/icon', $imageName, 'public');
            $service->image = $imageName;
        }

        if ($request->hasFile('inner_image')) {
            $innerImage = $request->file('inner_image');
            $innerImageName = $innerImage->getClientOriginalName();
            $innerImage->storeAs('uploads/services/image', $innerImageName, 'public');
            $service->inner_image = $innerImageName;
        }

        if ($request->hasFile('service_banner_path')) {
            $banner = $request->file('service_banner_path');
            $bannerName = $banner->getClientOriginalName();
            $banner->storeAs('uploads/services/banners', $bannerName, 'public');
            $service->service_banner_path = $bannerName;
        }

        // Exclude file inputs from the bulk update
        $service->update($request->except(['_token', '_method', 'image', 'inner_image', 'service_banner_path']));

        // Save any changes to file paths
        $service->save();

        return redirect()->route('services.index')->with('success', 'Service updated successfully!');
    }


    public function destroy(CarService $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully!');
    }
}
