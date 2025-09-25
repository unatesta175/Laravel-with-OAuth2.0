<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Dashboard data - coming soon']);
    }

    public function stats()
    {
        return response()->json(['message' => 'Dashboard stats - coming soon']);
    }
}
