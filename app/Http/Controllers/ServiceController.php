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
            'price_type' => 'required|string|in:fixed-price,call-now,quote-now,free',
            'cost_price' => 'required|numeric|min:0',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'robots_noindex_follow' => 'nullable|integer',
            'exclude_sitemap' => 'nullable|integer',
        ]);

        $data = $request->all();
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
            $data['inner_image'] = $innerImageName; // <--- Save only the image name
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
            'price_type' => 'required|string|in:fixed-price,call-now,quote-now,free',
            'cost_price' => 'required|numeric|min:0',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'robots_noindex_follow' => 'nullable|integer',
            'exclude_sitemap' => 'nullable|integer',
        ]);

    $data = $request->except(['_token', '_method', 'image', 'inner_image', 'service_banner_path']);
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
    }


    public function destroy(CarService $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully!');
    }

    public function handleEnquiry(Request $request)
{
    $data = $request->validate([
        'vehicle_reg' => 'required',
        'first_name' => 'required',
        'email' => 'required|email',
        'address' => 'required',
        'selected_services' => 'required|array',
    ]);

    // Example: save to DB or send email
    // Mail::to('admin@example.com')->send(new QuoteEnquiryMail($data));

    return back()->with('success', 'Your enquiry has been sent.');
}

}
