<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Services endpoint - coming soon']);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Service created - coming soon']);
    }

    public function show($id)
    {
        return response()->json(['message' => 'Service details - coming soon']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Service updated - coming soon']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Service deleted - coming soon']);
    }

    public function getByCategory($category)
    {
        return response()->json(['message' => 'Services by category - coming soon']);
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
}
