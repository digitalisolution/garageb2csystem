<?php
namespace App\Http\Controllers\ViewController;

use App\Models\TyresProduct;
use App\Models\CarService; // Assuming a model for services
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\Booking;

class CartController extends Controller
{
    public function show()
    {
        $cart = session('cart', []);
        $cartItems = [];
        $total = 0;
        $totalQuantity = 0;
        $events = Booking::all()->map(function ($booking) {
            return [
                'title' => $booking->title,
                'start' => $booking->start ? $booking->start->format('Y-m-d\TH:i:s') : null,
                'end' => $booking->end ? $booking->end->format('Y-m-d\TH:i:s') : null,
            ];
        })->filter(function ($event) {
            return $event['start'] && $event['end']; // Exclude events with invalid dates
        });

        if (empty($cart)) {
            return redirect()->route('home'); // Redirect if cart is empty
        }

        foreach ($cart as $item) {
            if (is_array($item) && isset($item['id'], $item['quantity'], $item['type'])) {
                $product = null;

                if ($item['type'] === 'tyre') {
                    $product = TyresProduct::where('product_id', $item['id'])->first();
                } elseif ($item['type'] === 'service') {
                    $product = CarService::find($item['id']);
                }

                if ($product) {
                    $cartItem = [
                        'id' => $product->id,
                        'type' => $item['type'],
                        'tax_class_id' => $product->tax_class_id ?? '',
                        'model' => $product->model ?? ($item['type'] === 'service' ? $product->name : ''),
                        'price' => $item['price'] ?? 0,
                        'fitting_type' => $item['fitting_type'] ?? null,
                        'quantity' => $item['quantity'],
                        'total' => $item['price'] * $item['quantity'],
                    ];

                    // Add `ean` and `sku` only for `tyre` type
                    if ($item['type'] === 'tyre') {
                        $cartItem['ean'] = $product->ean ?? '';
                        $cartItem['sku'] = $product->sku ?? '';
                        $cartItem['desc'] = $product->description ?? '';
                    }
                    $shippingData = session('postcode_data', []);
                    $cartItems[] = $cartItem;
                    $total += $item['price'] * $item['quantity'];
                    $totalQuantity += $item['quantity'];
                }
            }
        }

        // Ensure total price is stored in the session, aligning with `add` function
        Session::put('cartTotalPrice', $total);

        return view('checkout', compact('cartItems', 'total', 'shippingData', 'totalQuantity', 'events'));
    }


    public function add(Request $request)
    {
        $itemId = $request->input('id');
        $fittingType = $request->input('fitting_type', null);
        $qty = $request->input('qty', 1);
        $type = $request->input('type', 'tyre'); // Default type is tyre
        $shippingPricePerJob = 0;
        $shippingPricePerTyre = 0;
        $shippingData = session('postcode_data', []);
        $hasMobileFitting = false;

        // Determine product details based on type
        if ($type === 'tyre') {
            $product = TyresProduct::where('product_id', $itemId)->first();
            $productIdField = 'product_id';
            $priceField = 'tyre_fullyfitted_price';
        } elseif ($type === 'service') {
            $product = CarService::where('service_id', $itemId)->first();
            $productIdField = 'service_id';
            $priceField = 'cost_price';
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid product type'], 400);
        }

        // Validate product existence
        if (!$product) {
            return response()->json(['success' => false, 'message' => ucfirst($type) . ' not found'], 404);
        }// After fetching $product and before updating the cart
        $cart = Session::get('cart', []);
        $cartKey = $type . '_' . $itemId;

        $existingQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $totalRequested = $existingQuantity + $qty;

        if ($type === 'tyre' && $totalRequested > $product->tyre_quantity) {
            return response()->json([
                'success' => false,
                'message' => nl2br("Requested quantity exceeds available stock.</br>Available: {$product->tyre_quantity}"),
            ], 200);
        }
        // Retrieve cart from session


        // Add or update item in the cart
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $qty;
        } else {
            $cartItem = [
                'id' => $product->$productIdField,
                'type' => $type,
                'tax_class_id' => $product->tax_class_id,
                'model' => $product->tyre_model ?? ($type === 'service' ? $product->name : ''),
                'price' => $product->$priceField ?? 0,
                'fitting_type' => $fittingType,
                'quantity' => $qty,
            ];

            if ($type === 'tyre') {
                $cartItem['ean'] = $product->tyre_ean ?? '';
                $cartItem['sku'] = $product->tyre_sku ?? '';
                $cartItem['desc'] = $product->tyre_description ?? '';
                $cartItem['supplier'] = $product->tyre_supplier_name ?? '';
            }

            $cart[$cartKey] = $cartItem;
        }

        // Save updated cart back to session
        Session::put('cart', $cart);

        // Calculate sub-total, VAT total, and grand total
        $cartSubTotal = array_reduce($cart, function ($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);

        $vatTotal = array_reduce($cart, function ($total, $item) {
            if ($item['tax_class_id'] == 9) {
                return $total + ($item['price'] * $item['quantity'] * 0.2);
            }
            return $total;
        }, 0);

        // Initialize shipping costs
        foreach ($cart as $item) {
            // Check if any item has fitting_type as mobile_fitted
            if ($item['fitting_type'] === 'mobile_fitted') {
                $hasMobileFitting = true;
                // Determine shipping type and apply charges accordingly
                $shippingType = $shippingData['ship_type'] ?? 'job'; // Default to 'job' if not specified
                $shippingPrice = $shippingData['ship_price'] ?? 0;

                if ($shippingType === 'job') {
                    // Add shipping price once per booking
                    $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                    // Add shipping price per tyre (multiplied by quantity)
                    $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                }
            }
        }

        // Calculate shipping VAT if applicable
        $shippingVAT = 0;
        if ($hasMobileFitting && ($shippingData['includes_vat'] ?? 0) == 9) {
            $shippingVAT = ($shippingPricePerJob + $shippingPricePerTyre) * 0.2;
            $vatTotal = $vatTotal + $shippingVAT;
        }

        // Calculate grand total
        $grandTotal = $cartSubTotal + $vatTotal + $shippingPricePerJob + $shippingPricePerTyre;

        // Save totals in session
        Session::put('cartSubTotal', $cartSubTotal);
        Session::put('vatTotal', $vatTotal);
        Session::put('cartTotalPrice', $grandTotal);
        Session::put('shippingPricePerJob', $shippingPricePerJob);
        Session::put('shippingPricePerTyre', $shippingPricePerTyre);
        Session::put('shippingVAT', $shippingVAT);
        Session::save();
        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' added to cart',
            'product' => $cart,
            'totalQuantity' => array_sum(array_column($cart, 'quantity')),
            'cartSubTotal' => number_format($cartSubTotal, 2),
            'vatTotal' => number_format($vatTotal, 2),
            'cartTotalPrice' => number_format($grandTotal, 2),
            'shippingPricePerJob' => number_format($shippingPricePerJob, 2),
            'shippingPricePerTyre' => number_format($shippingPricePerTyre, 2),
            'shippingVAT' => number_format($shippingVAT, 2),
        ]);
    }
    public function update(Request $request)
    {
        $cart = session('cart', []);
        $productId = $request->id; // Product ID passed from the frontend
        $action = $request->action; // Action: 'increase' or 'decrease'

        // Find the key in the cart that matches the product ID
        $itemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $productId && $item['type'] === 'tyre') { // Ensure it's a tyre
                $itemKey = $key;
                break;
            }
        }

        // If the item exists in the cart, validate and update its quantity
        if ($itemKey !== null && isset($cart[$itemKey])) {
            $product = TyresProduct::where('product_id', $productId)->first(); // Fetch product details from the database

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tyre not found in the database',
                ], 404);
            }

            $existingQuantity = $cart[$itemKey]['quantity']; // Current quantity in the cart
            $newQuantity = $existingQuantity;

            if ($action === 'increase') {
                $newQuantity++;
            } elseif ($action === 'decrease' && $existingQuantity > 1) {
                $newQuantity--;
            }

            // Validate against database stock
            if ($newQuantity > $product->tyre_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient Stock",
                ], 200);
            }

            // Update the cart quantity
            $cart[$itemKey]['quantity'] = $newQuantity;
        }

        // Recalculate sub-total, VAT total, and grand total
        $cartSubTotal = 0;
        $vatTotal = 0;
        $shippingPricePerJob = 0;
        $shippingPricePerTyre = 0;

        $shippingData = session('postcode_data', []);
        $hasMobileFitting = false;

        foreach ($cart as $item) {
            $cartSubTotal += $item['price'] * $item['quantity'];
            if ($item['tax_class_id'] == 9) {
                $vatTotal += $item['price'] * $item['quantity'] * 0.2;
            }

            // Check if any item has fitting_type as mobile_fitted
            if ($item['fitting_type'] === 'mobile_fitted') {
                $hasMobileFitting = true;

                // Determine shipping type and apply charges accordingly
                $shippingType = $shippingData['ship_type'] ?? 'job'; // Default to 'job' if not specified
                $shippingPrice = $shippingData['ship_price'] ?? 0;

                if ($shippingType === 'job') {
                    // Add shipping price once per booking
                    $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                    // Add shipping price per tyre (multiplied by quantity)
                    $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                }
            }
        }

        // Calculate shipping VAT if applicable
        $shippingVAT = 0;
        if ($hasMobileFitting && ($shippingData['includes_vat'] ?? 0) == 9) {
            $shippingVAT = ($shippingPricePerJob + $shippingPricePerTyre) * 0.2;
            $vatTotal = $vatTotal + $shippingVAT;
        }

        // Calculate grand total
        $grandTotal = $cartSubTotal + $vatTotal + $shippingPricePerJob + $shippingPricePerTyre;

        // Save updated cart and totals back to the session
        session([
            'cart' => $cart,
            'cartSubTotal' => $cartSubTotal,
            'vatTotal' => $vatTotal,
            'cartTotalPrice' => $grandTotal,
            'shippingPricePerJob' => $shippingPricePerJob,
            'shippingPricePerTyre' => $shippingPricePerTyre,
            'shippingVAT' => $shippingVAT,
        ]);
        Session::save();

        return response()->json([
            'success' => true,
            'message' => 'Item updated from cart',
            'cartSubTotal' => number_format($cartSubTotal, 2),
            'vatTotal' => number_format($vatTotal, 2),
            'cartTotalPrice' => number_format($grandTotal, 2),
            'remainingItems' => array_sum(array_column($cart, 'quantity')),
            'shippingPricePerJob' => number_format($shippingPricePerJob, 2),
            'shippingPricePerTyre' => number_format($shippingPricePerTyre, 2),
            'shippingVAT' => number_format($shippingVAT, 2),
        ]);
    }
    public function delete(Request $request)
    {
        $productId = $request->input('id');
        $cart = session()->get('cart', []);

        // Find the key in the cart matching the product ID
        $itemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $productId) {
                $itemKey = $key;
                break;
            }
        }

        // Check if the item key exists in the cart and remove it
        if ($itemKey !== null && isset($cart[$itemKey])) {
            unset($cart[$itemKey]); // Remove the item
            $cart = array_values($cart); // Reindex the array
            session()->put('cart', $cart); // Update the session

            // Recalculate sub-total, VAT total, and grand total
            $cartSubTotal = 0;
            $vatTotal = 0;
            $shippingPricePerJob = 0;
            $shippingPricePerTyre = 0;

            $shippingData = session('postcode_data', []);
            $hasMobileFitting = false;

            foreach ($cart as $item) {
                $cartSubTotal += $item['price'] * $item['quantity'];
                if ($item['tax_class_id'] == 9) {
                    $vatTotal += $item['price'] * $item['quantity'] * 0.2;
                }

                // Check if any item has fitting_type as mobile_fitted
                if ($item['fitting_type'] === 'mobile_fitted') {
                    $hasMobileFitting = true;

                    // Determine shipping type and apply charges accordingly
                    $shippingType = $shippingData['ship_type'] ?? 'job'; // Default to 'job' if not specified
                    $shippingPrice = $shippingData['ship_price'] ?? 0;

                    if ($shippingType === 'job') {
                        // Add shipping price once per booking
                        $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                    } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                        // Add shipping price per tyre (multiplied by quantity)
                        $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                    }
                }
            }

            // Calculate shipping VAT if applicable
            $shippingVAT = 0;
            if ($hasMobileFitting && ($shippingData['includes_vat'] ?? 0) == 9) {
                $shippingVAT = ($shippingPricePerJob + $shippingPricePerTyre) * 0.2;
                $vatTotal = $vatTotal + $shippingVAT;
            }

            // Calculate grand total
            $grandTotal = $cartSubTotal + $vatTotal + $shippingPricePerJob + $shippingPricePerTyre;

            // Update the session with the new totals
            session([
                'cartSubTotal' => $cartSubTotal,
                'vatTotal' => $vatTotal,
                'cartTotalPrice' => $grandTotal,
                'shippingPricePerJob' => $shippingPricePerJob,
                'shippingPricePerTyre' => $shippingPricePerTyre,
                'shippingVAT' => $shippingVAT,
            ]);
            Session::save();
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartSubTotal' => number_format($cartSubTotal, 2),
                'vatTotal' => number_format($vatTotal, 2),
                'cartTotalPrice' => number_format($grandTotal, 2),
                'remainingItems' => array_sum(array_column($cart, 'quantity')),
                'shippingPricePerJob' => number_format($shippingPricePerJob, 2),
                'shippingPricePerTyre' => number_format($shippingPricePerTyre, 2),
                'shippingVAT' => number_format($shippingVAT, 2),
                'shippingPostcode' => $shippingData['postcode'] ?? '', // Add this line
            ]);
        }

        // If the item does not exist, return an error
        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart',
        ], 404);
    }
    public function remove(Request $request)
    {
        $productId = $request->input('id');
        $cart = session()->get('cart', []);

        // Find the key in the cart matching the product ID
        $itemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $productId) {
                $itemKey = $key;
                break;
            }
        }

        // Check if the item key exists in the cart and remove it
        if ($itemKey !== null && isset($cart[$itemKey])) {
            unset($cart[$itemKey]); // Remove the item
            $cart = array_values($cart); // Reindex the array
            session()->put('cart', $cart); // Update the session

            // Recalculate sub-total, VAT total, and grand total
            $cartSubTotal = 0;
            $vatTotal = 0;
            $shippingPricePerJob = 0;
            $shippingPricePerTyre = 0;

            $shippingData = session('postcode_data', []);
            $hasMobileFitting = false;

            foreach ($cart as $item) {
                $cartSubTotal += $item['price'] * $item['quantity'];
                if ($item['tax_class_id'] == 9) {
                    $vatTotal += $item['price'] * $item['quantity'] * 0.2;
                }

                // Check if any item has fitting_type as mobile_fitted
                if ($item['fitting_type'] === 'mobile_fitted') {
                    $hasMobileFitting = true;

                    // Determine shipping type and apply charges accordingly
                    $shippingType = $shippingData['ship_type'] ?? 'job'; // Default to 'job' if not specified
                    $shippingPrice = $shippingData['ship_price'] ?? 0;

                    if ($shippingType === 'job') {
                        // Add shipping price once per booking
                        $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                    } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                        // Add shipping price per tyre (multiplied by quantity)
                        $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                    }
                }
            }

            // Calculate shipping VAT if applicable
            $shippingVAT = 0;
            if ($hasMobileFitting && ($shippingData['includes_vat'] ?? 0) == 9) {
                $shippingVAT = ($shippingPricePerJob + $shippingPricePerTyre) * 0.2;
                $vatTotal = $vatTotal + $shippingVAT;
            }

            // Calculate grand total
            $grandTotal = $cartSubTotal + $vatTotal + $shippingPricePerJob + $shippingPricePerTyre;

            // Update the session with the new totals
            session([
                'cartSubTotal' => $cartSubTotal,
                'vatTotal' => $vatTotal,
                'cartTotalPrice' => $grandTotal,
                'shippingPricePerJob' => $shippingPricePerJob,
                'shippingPricePerTyre' => $shippingPricePerTyre,
                'shippingVAT' => $shippingVAT,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartSubTotal' => number_format($cartSubTotal, 2),
                'vatTotal' => number_format($vatTotal, 2),
                'cartTotalPrice' => number_format($grandTotal, 2),
                'remainingItems' => array_sum(array_column($cart, 'quantity')),
                'shippingPricePerJob' => number_format($shippingPricePerJob, 2),
                'shippingPricePerTyre' => number_format($shippingPricePerTyre, 2),
                'shippingVAT' => number_format($shippingVAT, 2),
                'shippingPostcode' => $shippingData['postcode'] ?? '', // Add this line
            ]);
        }

        // If the item does not exist, return an error
        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart',
        ], 404);
    }
    public function storeVehicleData(Request $request)
    {
        $request->merge([
            'year' => (string) $request->input('year'),
            'engine' => (string) $request->input('engine') // Use the cleaned engine value
        ]);
        $request->validate([
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:200',
            'year' => 'required|string|digits:4',
            'regNumber' => 'nullable|string|max:15',
            'engine' => 'nullable|string|max:99',
        ]);

        session([
            'vehicleData' => [
                'make' => $request->make,
                'model' => $request->model,
                'year' => $request->year,
                'regNumber' => $request->regNumber,
                'engine' => $request->engine,
            ],
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Vehicle data stored in session successfully.',
        ]);
    }

}