<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        return response()->json(['message' => 'Payment checkout - coming soon']);
    }

    public function complete($id, Request $request)
    {
        return response()->json(['message' => 'Payment completed - coming soon']);
    }

    public function status($id)
    {
        return response()->json(['message' => 'Payment status - coming soon']);
    }

    public function toyyibpayWebhook(Request $request)
    {
        return response()->json(['message' => 'Webhook received - coming soon']);
    }

    public function getAllPayments()
    {
        return response()->json(['message' => 'All payments - coming soon']);
    }
}
