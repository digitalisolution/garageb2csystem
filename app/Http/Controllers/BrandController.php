<?php

namespace App\Http\Controllers;

use App\Models\tyre_brands;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the brand.
     */
    public function index()
    {
        $brands = tyre_brands::orderBy('name', 'asc')->get();
        return view('AutoCare.brand.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('AutoCare.brand.create');
    }

    /**
     * Store a newly created brand in the database.
     */
    public function store(Request $request)
    {
            $request->validate([
                'name' => 'required|string|max:100',
                'slug' => 'nullable|string|max:255|unique:tyre_brands,slug',
                'description' => 'nullable|string',
                'status' => 'nullable|integer',
                'meta_title' => 'nullable|string|max:100',
                'meta_description' => 'nullable|string|max:200',
                'meta_keyword' => 'nullable|string|max:150',
                'promoted' => 'nullable|integer',
                'promoted_text' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Validate image
                'bannerimage' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Validate image  
                'recommended_tyre' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
                'product_type' => 'nullable|string|max:20',
            ]);

        // Handle file uploads
        $data = $request->validated();
        try {
         $domain = str_replace(['http://', 'https://'], '', request()->getHost());
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/brand-logo';
            $destinationimgPath = public_path($imagePath);
            
            if (!file_exists($destinationimgPath)) {
                mkdir($destinationimgPath, 0755, true);
            }
            
            $image->move($destinationimgPath, $imageName);
            $data['image'] = $imageName;
        }

        if ($request->hasFile('bannerimage')) {
            $bannerimage = $request->file('bannerimage');
            $bannerimageName = $bannerimage->getClientOriginalName();
            $bannerimagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/brand_img';
            $destinationimgPath = public_path($bannerimagePath);
            
            if (!file_exists($destinationimgPath)) {
                mkdir($destinationimgPath, 0755, true);
            }
            
            $bannerimage->move($destinationimgPath, $bannerimageName);
            $data['bannerimage'] = $bannerimageName;
        }

        // Create the brand
        tyre_brands::create($data);

        return redirect()->route('brand.index')->with('success', 'Brand created successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error creating Brand: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }


    /**
     * Show the form for editing the specified brand.
     */
    public function edit(tyre_brands $brand)
    {
        // The $brand is automatically resolved using route model binding
        return view('AutoCare.brand.create', compact('brand'));
    }

    /**
     * Update the specified brand in the database.
     */
    public function update(Request $request, tyre_brands $brand)
    {
        // Store validated data in a variable
        $validated = $request->validate([
            'name' => 'required|string|max:64',
            'slug' => 'required|string|unique:tyre_brands,slug,' . $brand->brand_id . ',brand_id|max:100',
            'description' => 'nullable|string',
            'status' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:200',
            'meta_keyword' => 'nullable|string|max:150',
            'promoted' => 'nullable|integer',
            'promoted_text' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'bannerimage' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'recommended_tyre' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'product_type' => 'nullable|string|max:20',
        ]);

        // Exclude images from $validated data before updating
        $data = collect($validated)->except(['image', 'bannerimage'])->toArray();

        try {        
            $domain = str_replace(['http://', 'https://'], '', $request->getHost());

            // Handle brand image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/brand-logo';
                $destinationimgPath = public_path($imagePath);
                
                if (!file_exists($destinationimgPath)) {
                    mkdir($destinationimgPath, 0755, true);
                }
                
                $image->move($destinationimgPath, $imageName);
                $data['image'] = $imageName;
            }

            // Handle banner image upload
            if ($request->hasFile('bannerimage')) {
                $bannerimage = $request->file('bannerimage');
                $bannerimageName = $bannerimage->getClientOriginalName();
                $bannerimagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/brand_img';
                $destinationimgPath = public_path($bannerimagePath);
                
                if (!file_exists($destinationimgPath)) {
                    mkdir($destinationimgPath, 0755, true);
                }
                
                $bannerimage->move($destinationimgPath, $bannerimageName);
                $data['bannerimage'] = $bannerimageName;
            }

            // Update brand
            $brand->update($data);

            return redirect()->route('brand.index')->with('success', 'Brand updated successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error updating Brand: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage() . ' Something went wrong. Please try again.');
        }  
    }


    /**
     * Remove the specified brand from the database.
     */
    public function destroy(tyre_brands $brand)
    {
        try{
        // Delete the brand
        $brand->delete();

        return redirect()->route('brand.index')->with('success', 'Brand deleted successfully!');
         } catch (\Throwable $e) {
            \Log::error("Error deleting Brand: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
    }

    public function show($slug)
    {
        $brand = tyre_brands::where('slug', $slug)->firstOrFail();

        return view('brand.show', compact('brand'));
    }


}
