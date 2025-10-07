<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of active service categories (Public)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $categories = ServiceCategory::with(['tags', 'services'])
                ->active()
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'description' => $category->description,
                        'image' => $category->image,
                        'is_active' => $category->is_active,
                        'tags' => $category->tags->map(function ($tag) {
                            return [
                                'id' => $tag->id,
                                'name' => $tag->name,
                                'is_active' => $tag->is_active,
                            ];
                        }),
                        'services_count' => $category->services->count(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Service categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('ServiceCategory index error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service category (Public)
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = ServiceCategory::with(['tags', 'services'])
                ->where('id', $id)
                ->active()
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service category not found'
                ], 404);
            }

            $categoryData = [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'image' => $category->image,
                'is_active' => $category->is_active,
                'tags' => $category->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'is_active' => $tag->is_active,
                    ];
                }),
                'services' => $category->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'description' => $service->description,
                        'extradescription' => $service->extradescription,
                        'price' => $service->price,
                        'duration' => $service->duration,
                        'type' => $service->type,
                        'image' => $service->image,
                        'is_active' => $service->is_active,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => $categoryData,
                'message' => 'Service category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all service categories including inactive ones (Admin only)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = ServiceCategory::with(['tags', 'services']);

            // Search by name
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            $categories = $query->orderBy('created_at', 'desc')
                               ->paginate($request->get('per_page', 15));

            return response()->json([
                'data' => $categories->items(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
            ]);
        } catch (\Exception $e) {
            Log::error('ServiceCategory adminIndex error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific service category (Admin)
     */
    public function adminShow(string $id): JsonResponse
    {
        try {
            $category = ServiceCategory::with(['tags', 'services'])->findOrFail($id);

            return response()->json([
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service category not found'
            ], 404);
        }
    }

    /**
     * Store a newly created service category (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:service_categories',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'is_active' => 'boolean',
                'tag_ids' => 'nullable|array',
                'tag_ids.*' => 'exists:service_category_tags,id'
            ]);

            $category = ServiceCategory::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'image' => $validated['image'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Attach tags if provided
            if (!empty($validated['tag_ids'])) {
                $category->tags()->sync($validated['tag_ids']);
            }

            $category->load(['tags', 'services']);

            return response()->json([
                'message' => 'Service category created successfully!',
                'category' => $category
            ], 201);
        } catch (\Exception $e) {
            Log::error('ServiceCategory store error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create service category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified service category (Admin only)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $category = ServiceCategory::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:service_categories,name,' . $id,
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'is_active' => 'boolean',
                'tag_ids' => 'nullable|array',
                'tag_ids.*' => 'exists:service_category_tags,id'
            ]);

            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'image' => $validated['image'] ?? null,
                'is_active' => $validated['is_active'] ?? $category->is_active,
            ]);

            // Sync tags if provided
            if (isset($validated['tag_ids'])) {
                $category->tags()->sync($validated['tag_ids']);
            }

            $category->load(['tags', 'services']);

            return response()->json([
                'message' => 'Service category updated successfully!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            Log::error('ServiceCategory update error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update service category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified service category (Admin only)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = ServiceCategory::findOrFail($id);

            // Check if category has services
            if ($category->services()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete category with associated services'
                ], 422);
            }

            $category->tags()->detach(); // Remove tag associations
            $category->delete();

            return response()->json([
                'message' => 'Service category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('ServiceCategory destroy error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete service category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


