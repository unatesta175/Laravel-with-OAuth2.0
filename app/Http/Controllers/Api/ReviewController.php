<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Reviews endpoint - coming soon']);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Review created - coming soon']);
    }

    public function show($id)
    {
        return response()->json(['message' => 'Review details - coming soon']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Review updated - coming soon']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Review deleted - coming soon']);
    }

    public function getTherapistReviews($therapist)
    {
        return response()->json(['message' => 'Therapist reviews - coming soon']);
    }

    public function approve($id, Request $request)
    {
        return response()->json(['message' => 'Review approved - coming soon']);
    }

    public function getAllReviews()
    {
        return response()->json(['message' => 'All reviews - coming soon']);
    }
}
