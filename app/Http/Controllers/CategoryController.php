<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.category', compact('categories'));
    }

    // Show form to create a new category
    public function create()
    {
        return view('admin.categories.create');
    }

    // Store a newly created category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    // Show form to edit a category
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // Update the category
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    // Delete the category
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    // API endpoint to list all categories
    public function apiIndex()
    {
        try {
            $categories = Category::all();

            return response()->json([
                'success' => true,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load categories',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
