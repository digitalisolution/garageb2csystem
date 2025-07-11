<?php

namespace App\Http\Controllers;
use App\Services\EdenService;
use Illuminate\Http\Request;

class EdenApiController extends Controller
{

    protected $edenService;

    public function __construct(EdenService $edenService)
    {
        $this->edenService = $edenService;
    }

    public function placeOrder(Request $request)
    {
        $reference = $request->input('reference');
        $products = $request->input('products'); // Expecting an array of products

        $response = $this->edenService->placeApiOrder($reference, $products);

        if ($response['status'] === 'success') {
            return response()->json(['message' => 'Order placed successfully!', 'data' => $response]);
        }

        return response()->json(['error' => $response['msg']], 400);
    }
}
