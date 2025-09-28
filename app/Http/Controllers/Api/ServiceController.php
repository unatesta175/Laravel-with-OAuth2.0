<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of active services.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Service::with(['category'])
                ->active();

            // Filter by category if provided
            if ($request->has('category') && $request->category !== 'All') {
                $query->where('category_id', $request->category);
            }

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort', 'popular');
            switch ($sortBy) {
                case 'price-low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price-high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'duration':
                    $query->orderBy('duration', 'asc');
                    break;
                case 'type':
                    $query->orderBy('type', 'desc'); // promo first
                    break;
                case 'popular':
                default:
                    $query->orderBy('type', 'desc')
                          ->orderBy('price', 'desc');
                    break;
            }

            $services = $query->get()->map(function ($service) {
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
                    'category' => [
                        'id' => $service->category->id,
                        'name' => $service->category->name,
                        'description' => $service->category->description,
                        'image' => $service->category->image,
                        'is_active' => $service->category->is_active,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $services,
                'message' => 'Services retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $service = Service::with(['category', 'therapists'])
                ->where('id', $id)
                ->active()
                ->first();

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            $serviceData = [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'extradescription' => $service->extradescription,
                'price' => $service->price,
                'duration' => $service->duration,
                'type' => $service->type,
                'image' => $service->image,
                'is_active' => $service->is_active,
                'category' => [
                    'id' => $service->category->id,
                    'name' => $service->category->name,
                    'description' => $service->category->description,
                    'image' => $service->category->image,
                    'is_active' => $service->category->is_active,
                ],
                'therapists' => $service->therapists->map(function ($therapist) {
                    return [
                        'id' => $therapist->id,
                        'name' => $therapist->name,
                        'email' => $therapist->email,
                        'phone' => $therapist->phone,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => $serviceData,
                'message' => 'Service retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get services by category.
     */
    public function getByCategory(string $categoryId): JsonResponse
    {
        try {
            $category = ServiceCategory::find($categoryId);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $services = Service::with(['category'])
                ->where('category_id', $categoryId)
                ->active()
                ->get()
                ->map(function ($service) {
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
                        'category' => [
                            'id' => $service->category->id,
                            'name' => $service->category->name,
                            'description' => $service->category->description,
                            'image' => $service->category->image,
                            'is_active' => $service->category->is_active,
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $services,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'image' => $category->image,
                ],
                'message' => 'Services retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Placeholder methods for future implementation
    public function store(Request $request)
    {
        return response()->json(['message' => 'Service created - coming soon']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Service updated - coming soon']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Service deleted - coming soon']);
    }

    public function assignTherapist($service, Request $request)
    {
        return response()->json(['message' => 'Therapist assigned - coming soon']);
    }

    public function removeTherapist($service, $therapist)
    {
        return response()->json(['message' => 'Therapist removed - coming soon']);
    }

    public function getTherapistAvailability($therapist)
    {
        return response()->json(['message' => 'Therapist availability - coming soon']);
    }

    public function getServiceTherapists($service)
    {
        return response()->json(['message' => 'Service therapists - coming soon']);
    }

    /**
     * Admin: Get all services with pagination and filtering
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Service::with(['category']);

        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json($services);
    }

    /**
     * Admin: Store a new service
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'category_id' => 'required|exists:service_categories,id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $serviceData = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'category_id' => $request->category_id,
            'is_active' => $request->get('is_active', true),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'service_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('services', $filename, 'public');
            $serviceData['image'] = $path;
        }

        $service = Service::create($serviceData);
        $service->load('category');

        return response()->json([
            'message' => 'Service created successfully!',
            'service' => $service
        ], 201);
    }

    /**
     * Admin: Update a service
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'duration' => 'sometimes|required|integer|min:1',
            'category_id' => 'sometimes|required|exists:service_categories,id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['name', 'description', 'price', 'duration', 'category_id', 'is_active']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image && \Storage::disk('public')->exists($service->image)) {
                \Storage::disk('public')->delete($service->image);
            }

            $file = $request->file('image');
            $filename = 'service_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('services', $filename, 'public');
            $updateData['image'] = $path;
        }

        $service->update($updateData);
        $service->load('category');

        return response()->json([
            'message' => 'Service updated successfully!',
            'service' => $service
        ]);
    }

    /**
     * Admin: Delete a service
     */
    public function destroy(Service $service): JsonResponse
    {
        // Delete service image if exists
        if ($service->image && \Storage::disk('public')->exists($service->image)) {
            \Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully!'
        ]);
    }
}
