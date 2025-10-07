<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategoryTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ServiceCategoryTagController extends Controller
{
    /**
     * Get all service category tags (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tags = ServiceCategoryTag::withCount('serviceCategories')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tags,
                'message' => 'Service category tags retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('ServiceCategoryTag index error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service category tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created service category tag (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:service_category_tags,name',
                'is_active' => 'boolean',
            ]);

            $tag = ServiceCategoryTag::create([
                'name' => $validated['name'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return response()->json([
                'success' => true,
                'data' => $tag,
                'message' => 'Service category tag created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('ServiceCategoryTag store error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create service category tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified service category tag (Admin only)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $tag = ServiceCategoryTag::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:service_category_tags,name,' . $id,
                'is_active' => 'boolean',
            ]);

            $tag->update([
                'name' => $validated['name'],
                'is_active' => $validated['is_active'] ?? $tag->is_active,
            ]);

            return response()->json([
                'success' => true,
                'data' => $tag,
                'message' => 'Service category tag updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('ServiceCategoryTag update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update service category tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified service category tag (Admin only)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $tag = ServiceCategoryTag::findOrFail($id);

            // Detach from all categories before deleting
            $tag->serviceCategories()->detach();
            $tag->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service category tag deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('ServiceCategoryTag destroy error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service category tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


