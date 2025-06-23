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
            'name' => 'required|string|max:64',
        ]);

        // Handle file uploads
        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('brands/images', 'public');
        }
        if ($request->hasFile('bannerimage')) {
            $data['bannerimage'] = $request->file('bannerimage')->store('brands/banners', 'public');
        }

        // Create the brand
        tyre_brands::create($data);

        return redirect()->route('brand.index')->with('success', 'Brand created successfully!');
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
        $request->validate([
            'name' => 'required|string|max:64',
            'slug' => 'required|string|unique:tyre_brands,slug,' . $brand->brand_id . ',brand_id|max:100',
            'description' => 'nullable|string',
            'status' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:200',
            'meta_keyword' => 'nullable|string|max:150',
            'promoted' => 'nullable|integer',
            'promoted_text' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'budget_type' => 'nullable|string|max:50',
            'recommended_tyre' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'product_type' => 'nullable|string|max:20',
            'bannerimage' => 'nullable|image|max:2048',
        ]);

        // Handle file uploads
        $data = $request->all();
        if ($request->hasFile('image')) {
            if ($brand->image) {
                \Storage::disk('public')->delete($brand->image);
            }
            $data['image'] = $request->file('image')->store('brands/images', 'public');
        }
        if ($request->hasFile('bannerimage')) {
            if ($brand->bannerimage) {
                \Storage::disk('public')->delete($brand->bannerimage);
            }
            $data['bannerimage'] = $request->file('bannerimage')->store('brands/banners', 'public');
        }
        // dd($data);
        // Update the brand
        $brand->update($data);

        return redirect()->route('brand.index')->with('success', 'Brand updated successfully!');
    }

    /**
     * Remove the specified brand from the database.
     */
    public function destroy(tyre_brands $brand)
    {
        // Delete the brand
        $brand->delete();

        return redirect()->route('brand.index')->with('success', 'Brand deleted successfully!');
    }

    public function show($slug)
    {
        $brand = tyre_brands::where('slug', $slug)->firstOrFail();

        return view('brand.show', compact('brand'));
    }


}
