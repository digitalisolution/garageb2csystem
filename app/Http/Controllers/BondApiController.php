<?php

namespace App\Http\Controllers;
use App\Services\BondService;
use Illuminate\Http\Request;

class BondApiController extends Controller
{
    protected $bondService;

    public function __construct(BondService $bondService)
    {
        $this->bondService = $bondService;
    }

    public function placeOrder(Request $request)
    {
        $reference = $request->input('reference');
        $products = $request->input('products'); // Expecting an array of products

        $response = $this->bondService->placeApiOrder($reference, $products);

        if ($response['status'] === 'success') {
            return response()->json(['message' => 'Order placed successfully!', 'data' => $response]);
        }

        return response()->json(['error' => $response['msg']], 400);
    }
}
