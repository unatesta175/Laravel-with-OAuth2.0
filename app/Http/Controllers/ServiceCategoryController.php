<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of active service categories.
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
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service category.
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
}
