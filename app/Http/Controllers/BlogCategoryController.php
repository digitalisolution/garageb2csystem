<?php
namespace App\Http\Controllers;

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::all();
       return view('AutoCare.blogs.blog_categories.index', compact('categories'));

    }

    public function create()
    {
        return view('AutoCare.blogs.blog_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        BlogCategory::create([
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->title),
        ]);

        return redirect()->route('AutoCare.blogs.blog_categories.index')->with('success', 'Category created!');
    }

    public function edit($id)
    {
        $category = BlogCategory::findOrFail($id);
        return view('AutoCare.blogs.blog_categories.edit', compact('category'));

    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $category->update([
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->title),
        ]);

        return redirect()->route('AutoCare.blogs.blog_categories.index')->with('success', 'Category updated!');
    }

    public function destroy($id)
    {
        BlogCategory::destroy($id);
        return redirect()->route('AutoCare.blogs.blog_categories.index')->with('success', 'Category deleted!');
    }
}
