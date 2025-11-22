<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::all()->keyBy('position');
        return view('admin.banner', compact('banners'));
    }

    public function create()
    {
        // Optional: if you want a separate page to create banners
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'position' => 'required|integer|min:1|max:10',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:50', // Validate each size as string
        ]);

        if (Banner::where('position', $request->position)->exists()) {
            return back()->with('error', 'Banner for position ' . $request->position . ' already exists.');
        }

        $image = $request->file('image');
        if ($image) {
            $filename = 'banner_' . $request->position . '.' . $image->getClientOriginalExtension();
            $image->storeAs('banners', $filename, 'public');
        } else {
            return back()->with('error', 'image not exist');
        }

        // Process sizes - filter out empty values
        $sizes = $request->sizes ? array_filter($request->sizes, function($size) {
            return !empty(trim($size));
        }) : null;

        Banner::create([
            'position' => $request->position,
            'image' => 'banners/' . $filename, // Accessible via /storage/banners/banner_x.jpg
            'sizes' => $sizes,
        ]);

        return back()->with('success', 'Banner added successfully.');
    }

    public function show(Banner $banner)
    {
        // Optional: If you want to show a single banner
    }

    public function edit(Banner $banner)
    {
        // Optional: If you use a separate form to edit
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:50',
        ]);

        $updateData = [];

        // Handle image update
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'banner_' . $banner->position . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/banners', $filename);
            $updateData['image'] = 'banners/' . $filename;
        }

        // Handle sizes update
        if ($request->has('sizes')) {
            $sizes = $request->sizes ? array_filter($request->sizes, function($size) {
                return !empty(trim($size));
            }) : null;
            $updateData['sizes'] = $sizes;
        }

        if (!empty($updateData)) {
            $banner->update($updateData);
            return back()->with('success', 'Banner updated successfully.');
        }

        return back()->with('error', 'No changes made.');
    }

    public function destroy(Banner $banner)
    {
        if (Storage::exists('public/' . $banner->image)) {
            Storage::delete('public/' . $banner->image);
        }

        $banner->delete();
        return back()->with('success', 'Banner deleted successfully.');
    }

    public function getBannerByPosition($position)
    {
        $banner = Banner::where('position', $position)->first();
        
        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found for position ' . $position
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'position' => $banner->position,
                'image_url' => asset('storage/' . $banner->image),
                'image_path' => $banner->image,
                'sizes' => $banner->sizes ?? []
            ]
        ]);
    }
}