<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\tyre_brands;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = tyre_brands::orderBy('name', 'asc')->get();
        return view('AutoCare.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('AutoCare.brand.create');
    }

    public function store(Request $request)
    {       
        try{
            $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:255|unique:tyre_brands,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Validate image
            'bannerimage' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Validate image 
            'promoted' => 'nullable|boolean',
            'status' => 'required|boolean', 
            'product_type' => 'nullable|string|max:20',
            'budget_type' => 'nullable|string|max:50',
            'recommended_tyre' => 'nullable|boolean',
            'promoted_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:100',
            'meta_keyword' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:255',
        ]);

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
            $validated['image'] = $imageName;
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
            $validated['bannerimage'] = $bannerimageName;
        }
        $validated['promoted'] = $validated['promoted'] ?? 0;

        tyre_brands::create($validated);

        return redirect()->route('brand.index')->with('success', 'Brand created successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error storing Brand: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

    }

    public function edit(tyre_brands $brand)
    {
        return view('AutoCare.brand.create', compact('brand'));
    }

    public function update(Request $request, tyre_brands $brand)
    {
        try{
        $validated = $request->validate([
            'name' => 'required|string|max:64',
            'slug' => 'required|string|unique:tyre_brands,slug,' . $brand->brand_id . ',brand_id|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Validate image
            'bannerimage' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048', // Validate image
            'promoted' => 'nullable|integer',   
            'product_type' => 'nullable|string|max:20',
            'budget_type' => 'nullable|string|max:50',
            'recommended_tyre' => 'nullable|boolean',
            'promoted_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:100',
            'meta_keyword' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:255',
        ]);

         $data = collect($validated)->except(['image', 'bannerimage'])->toArray();    
       
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
            $data['promoted'] = $data['promoted'] ?? 0;

            $brand->update($data);

            return redirect()->route('brand.index')->with('success', 'Brand updated successfully!');
            } catch (\Throwable $e) {
            \Log::error("Error Updating Brand: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(tyre_brands $brand)
    {
        try{
        $brand->delete();

        return redirect()->route('brand.index')->with('success', 'Brand deleted successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error Delelting Brand: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($slug)
    {
        $brand = tyre_brands::where('slug', $slug)->firstOrFail();

        return view('brand.show', compact('brand'));
    }


}
