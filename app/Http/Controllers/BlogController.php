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
        $blogs = Blog::orderBy('sort_order', 'asc')->get();// Fetch all HTML templates
        return view('AutoCare.blogs.index', compact('blogs'));
    }
 
    public function create()
    {
        $categories = BlogCategory::all();
        $selectedCategories = []; // Ensure the form has this variable even when creating
        return view('AutoCare.blogs.create', compact('categories', 'selectedCategories'));
    }


    public function store(Request $request)
    {
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required',
        'category_id' => 'required|array',
        'image' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
        'meta_title' => 'nullable|string|max:100',
        'meta_description' => 'nullable|string|max:200',
        'meta_keyword' => 'nullable|string|max:150',
        'view' => 'nullable|integer',
        'slug' => 'nullable|string|max:255|unique:blogs,slug',
        'sort_order' => 'nullable|integer',
        'status' => 'required|boolean',
    ]);

    $data = $request->all(); // Only keep this one
    $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
    $data['category_id'] = implode(',', $data['category_id']);
    $data['date_added'] = now();

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('frontend/themes/img/blogs/images', $imageName, 'public');
        $data['image'] = 'frontend/themes/img/blogs/images/'.$imageName;
    }

    Blog::create($data);

    return redirect()->route('AutoCare.blogs.index')->with('success', 'Blog created successfully!');
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
        $blog = Blog::findOrFail($blog_id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'category_id' => 'required|array',
            'image' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
            'meta_title' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:200',
            'meta_keyword' => 'nullable|string|max:150',
            'view' => 'nullable|integer',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blog->blog_id . ',blog_id',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['category_id'] = implode(',', $data['category_id']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();

            // Store directly in public folder
            $image->move(public_path('frontend/themes/img/blogs/images'), $imageName);

            // Save full relative path to DB
            $data['image'] = 'frontend/themes/img/blogs/images/' . $imageName;
        }


        $blog->update($data);

        return redirect()->route('AutoCare.blogs.index')->with('success', 'Blog updated successfully!');
    }


    public function destroy($blog_id)
    {
        $blog = Blog::findOrFail($blog_id);
        $blog->delete();

        return redirect()->route('blog.index')->with('success', 'Blog deleted successfully!');

        //return redirect()->route('AutoCare.blog.index')->with('success', 'Blog deleted successfully!');
    }
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        return view('AutoCare.blogs.index', compact('blog'));
    }

}

