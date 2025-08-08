<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('sort_order', 'asc')->get();
        
        return view('AutoCare.blogs.index', compact('blogs'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        $selectedCategories = [];
        return view('AutoCare.blogs.create', compact('categories', 'selectedCategories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|array',
                'image' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:2000',
                'meta_keyword' => 'nullable|string|max:250',
                'view' => 'nullable|integer',
                'slug' => 'nullable|string|max:255|unique:blogs,slug',
                'sort_order' => 'nullable|integer',
                'status' => 'required|boolean',
            ]);

            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
            $validated['category_id'] = implode(',', $validated['category_id']);
            $validated['date_added'] = now();

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();

                $domain = str_replace(['http://', 'https://'], '', request()->getHost());
                $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/blogs/images/';
                $destinationPath = public_path($imagePath);

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $image->move($destinationPath, $imageName);
                $validated['image'] = $imageName;
            }

            Blog::create($validated);

            return redirect()->route('AutoCare.blogs.index')->with('success', 'Blog created successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error storing Blog: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($blog_id)
    {
        $blog = Blog::findOrFail($blog_id);
        $categories = BlogCategory::all();
        $selectedCategories = explode(',', $blog->category_id);

        return view('AutoCare.blogs.create', compact('blog', 'categories', 'selectedCategories'));
    }

    public function update(Request $request, $blog_id)
    {
        try {
            $blog = Blog::findOrFail($blog_id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|array',
                'image' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:2000',
                'meta_keyword' => 'nullable|string|max:250',
                'view' => 'nullable|integer',
                'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blog->blog_id . ',blog_id',
                'sort_order' => 'nullable|integer',
                'status' => 'required|boolean',
            ]);

            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
            $validated['category_id'] = implode(',', $validated['category_id']);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();

                $domain = str_replace(['http://', 'https://'], '', request()->getHost());
                $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/blogs/images/';
                $destinationPath = public_path($imagePath);

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $image->move($destinationPath, $imageName);
                $validated['image'] = $imageName; 
            }

            $blog->update($validated);

            return redirect()->route('AutoCare.blogs.index')->with('success', 'Blog updated successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error updating Blog: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($blog_id)
    {
        try {
            $blog = Blog::findOrFail($blog_id);
            $blog->delete();

            return redirect()->route('AutoCare.blogs.index')->with('success', 'Blog deleted successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error deleting Blog: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return view('AutoCare.blogs.index', compact('blog'));
    }
}
