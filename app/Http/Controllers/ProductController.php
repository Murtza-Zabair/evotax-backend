<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::with('category')->paginate(10);
            $categories = Category::all();
            return view('admin.admin', compact('products', 'categories'));
        } catch (\Exception $e) {
            return back()->withErrors('Failed to load products: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $categories = Category::all();
            return view('admin.products.create', compact('categories'));
        } catch (\Exception $e) {
            return back()->withErrors('Failed to load create page: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'hover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            $data = $request->only(['title', 'category_id', 'description']);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            if ($request->hasFile('hover_image')) {
                $data['hover_image'] = $request->file('hover_image')->store('products', 'public');
            }

            Product::create($data);

            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors('Failed to create product: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Product $product)
    {
        try {
            $categories = Category::all();
            return view('admin.products.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            return back()->withErrors('Failed to load edit page: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:4096',
                'hover_image' => 'nullable|image|max:4096',
            ]);

            $data = $request->only(['title', 'category_id', 'description']);

            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            if ($request->hasFile('hover_image')) {
                if ($product->hover_image && Storage::disk('public')->exists($product->hover_image)) {
                    Storage::disk('public')->delete($product->hover_image);
                }
                $data['hover_image'] = $request->file('hover_image')->store('products', 'public');
            }

            $product->update($data);

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors('Failed to update product: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            if ($product->hover_image && Storage::disk('public')->exists($product->hover_image)) {
                Storage::disk('public')->delete($product->hover_image);
            }

            $product->delete();

            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors('Failed to delete product: ' . $e->getMessage());
        }
    }

    // API endpoint to list all products
    public function apiIndex(Request $request)
    {
        try {
            $query = Product::with('category');

            // Filter by category if provided
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by category name if provided
            if ($request->has('category') && $request->category) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->category . '%');
                });
            }

            $products = $query->get()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->title,
                    'category' => $product->category->name,
                    'description' => $product->description,
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'hoverImage' => $product->hover_image ? asset('storage/' . $product->hover_image) : ($product->image ? asset('storage/' . $product->image) : null),
                ];
            });

            $categories = Category::all();

            return response()->json([
                'success' => true,
                'products' => $products,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load product data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // API endpoint to get products by category
    public function apiByCategory($categoryId)
    {
        try {
            $products = Product::with('category')
                ->where('category_id', $categoryId)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->title,
                        'category' => $product->category->name,
                        'description' => $product->description,
                        'image' => $product->image ? asset('storage/' . $product->image) : null,
                        'hoverImage' => $product->hover_image ? asset('storage/' . $product->hover_image) : ($product->image ? asset('storage/' . $product->image) : null),
                    ];
                });

            return response()->json([
                'success' => true,
                'products' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load products by category',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // API endpoint to show single product
    public function apiShow($id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->title,
                    'category' => $product->category->name,
                    'description' => $product->description,
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'hoverImage' => $product->hover_image ? asset('storage/' . $product->hover_image) : ($product->image ? asset('storage/' . $product->image) : null),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
