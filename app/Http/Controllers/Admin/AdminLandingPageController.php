<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\LandingPageSection;
use Illuminate\Support\Facades\Storage;

class AdminLandingPageController extends Controller
{
    public function index()
    {
        $sections = LandingPageSection::all()->keyBy('key');

        return Inertia::render('Admin/Landing/Index', [
            'sections' => $sections
        ]);
    }

    public function update(Request $request, $key)
    {
        $section = LandingPageSection::where('key', $key)->firstOrFail();
        
        $validated = $request->validate([
            'content' => 'required|array',
            'images' => 'nullable|array',
        ]);

        $currentImages = $section->images ?? [];
        $newImages = $validated['images'] ?? [];

        foreach ($newImages as $imgKey => $imgValue) {
            if ($request->hasFile("images.$imgKey")) {
                // Delete old image if exists and is not a default asset
                if (isset($currentImages[$imgKey]) && !str_starts_with($currentImages[$imgKey], '/imagenes/')) {
                     // Simple check to avoid deleting default assets. 
                     // Ideally we check if file exists in storage.
                     $oldPath = str_replace('/storage/', '', $currentImages[$imgKey]);
                     if (Storage::disk('public')->exists($oldPath)) {
                         Storage::disk('public')->delete($oldPath);
                     }
                }

                $path = $request->file("images.$imgKey")->store('landing', 'public');
                $fullPath = "/storage/$path";
                $currentImages[$imgKey] = $fullPath;

                // Special handling for content nested images
                // Maps 'feature_0' -> content.items[0].image
                if (preg_match('/^feature_(\d+)$/', $imgKey, $matches)) {
                    $index = $matches[1];
                    if (isset($validated['content']['items'][$index])) {
                        $validated['content']['items'][$index]['image'] = $fullPath;
                    }
                }
                // Maps 'tool_0' -> content.items[0].image (if tools had images)
                if (preg_match('/^tool_(\d+)$/', $imgKey, $matches)) {
                    $index = $matches[1];
                    if (isset($validated['content']['items'][$index])) {
                         $validated['content']['items'][$index]['image'] = $fullPath;
                    }
                }

                // Maps 'testimonial_0' -> content.items[0].avatar
                if (preg_match('/^testimonial_(\d+)$/', $imgKey, $matches)) {
                    $index = $matches[1];
                    if (isset($validated['content']['items'][$index])) {
                         $validated['content']['items'][$index]['avatar'] = $fullPath;
                    }
                }
            }
        }

        $section->update([
            'content' => $validated['content'],
            'images' => $currentImages,
        ]);

        return back()->with('success', 'Sección actualizada correctamente.');
    }
}
