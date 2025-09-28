<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Get all service categories with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = ServiceCategory::query();
        
        // Search by name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        $categories = $query->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));
        
        return response()->json($categories);
    }

    /**
     * Admin: Store a new service category
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:service_categories',
            'description' => 'nullable|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $categoryData = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->get('is_active', true),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'category_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('service-categories', $filename, 'public');
            $categoryData['image'] = $path;
        }

        $category = ServiceCategory::create($categoryData);

        return response()->json([
            'message' => 'Service category created successfully!',
            'category' => $category
        ], 201);
    }

    /**
     * Get a specific service category
     */
    public function show(ServiceCategory $category): JsonResponse
    {
        return response()->json([
            'category' => $category
        ]);
    }

    /**
     * Admin: Update a service category
     */
    public function update(Request $request, ServiceCategory $category): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:service_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['name', 'description', 'is_active']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $file = $request->file('image');
            $filename = 'category_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('service-categories', $filename, 'public');
            $updateData['image'] = $path;
        }

        $category->update($updateData);

        return response()->json([
            'message' => 'Service category updated successfully!',
            'category' => $category
        ]);
    }

    /**
     * Admin: Delete a service category
     */
    public function destroy(ServiceCategory $category): JsonResponse
    {
        // Check if category has services
        if ($category->services()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing services'
            ], 422);
        }

        // Delete category image if exists
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();

        return response()->json([
            'message' => 'Service category deleted successfully!'
        ]);
    }
}
