<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Categories endpoint - coming soon']);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Category created - coming soon']);
    }

    public function show($id)
    {
        return response()->json(['message' => 'Category details - coming soon']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Category updated - coming soon']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Category deleted - coming soon']);
    }
}
