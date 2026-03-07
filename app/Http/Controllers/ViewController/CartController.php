<?php
namespace App\Http\Controllers\ViewController;

use App\Models\TyresProduct;
use App\Models\CarService;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\OrderTypes;
use App\Http\Controllers\MailTyrePricingController;
use App\Http\Controllers\MobileTyrePricingController;
use Illuminate\Support\Facades\Session;
use App\Services\CartTotalService;
use App\Models\Booking;

class CartController extends Controller
{
    protected $mailTyrePricingController;
    protected $mobileTyrePricingController;
    protected $cartTotalService;

    public function __construct(MailTyrePricingController $mailTyrePricingController, MobileTyrePricingController $mobileTyrePricingController, CartTotalService $cartTotalService)
    {
        $this->mailTyrePricingController = $mailTyrePricingController;
        $this->mobileTyrePricingController = $mobileTyrePricingController;
        $this->cartTotalService = $cartTotalService;
    }
    // public function show()
    // {
    //     $cart = session('cart', []);
    //     $cartItems = [];
    //     $total = 0;
    //     $totalQuantity = 0;
    //     $userOrdertype = session('user_ordertype');
    //     $garageFittingCharge = Session::get('garageFittingCharge');
    //     $calenderBook = OrderTypes::where('status', 1)->where('calender_book', 1)->get()
    //         ->pluck('ordertype_name')
    //         ->toArray();
    //     $customer = Auth::guard('customer')->user();
    //     $selectedCounty = $customer->shipping_address_county;
    //     $selectedCountry = $customer->shipping_address_country;
    //     $events = Booking::all()->map(function ($booking) {
    //         return [
    //             'title' => $booking->title,
    //             'start' => $booking->start ? $booking->start->format('Y-m-d\TH:i:s') : null,
    //             'end' => $booking->end ? $booking->end->format('Y-m-d\TH:i:s') : null,
    //         ];
    //     })->filter(function ($event) {
    //         return $event['start'] && $event['end'];
    //     });

    //     if (empty($cart)) {
    //         return redirect()->route('home');
    //     }

    //     foreach ($cart as $item) {
    //         if (is_array($item) && isset($item['id'], $item['quantity'], $item['type'])) {
    //             $product = null;

    //             if ($item['type'] === 'tyre') {
    //                 $product = TyresProduct::where('product_id', $item['id'])->first();
    //             } elseif ($item['type'] === 'service') {
    //                 $product = CarService::find($item['id']);
    //             }

    //             if ($item['type'] === 'tyre') {
    //                 $image = $product->tyre_image ?? 'sample-tyre.png';
    //             } elseif ($item['type'] === 'service') {
    //                 $image = $product->inner_image ?? 'no-img-service.jpg';
    //             } else {
    //                 $image = null; // fallback
    //             }

    //             if ($product) {
    //                 $cartItem = [
    //                     'id' => $product->id,
    //                     'type' => $item['type'],
    //                     'image' => $image,
    //                     'tax_class_id' => $product->tax_class_id ?? '',
    //                     'model' => $product->model ?? ($item['type'] === 'service' ? $product->name : ''),
    //                     'price' => $item['price'] ?? 0,
    //                     'fitting_type' => $item['fitting_type'] ?? null,
    //                     'quantity' => $item['quantity'],
    //                     'total' => $item['price'] * $item['quantity'],
    //                 ];

    //                 if ($item['type'] === 'tyre') {
    //                     $cartItem['ean'] = $product->ean ?? '';
    //                     $cartItem['sku'] = $product->sku ?? '';
    //                     $cartItem['desc'] = $product->description ?? '';
    //                 }
    //                 $shippingData = session('postcode_data', []);
    //                 $cartItems[] = $cartItem;
    //                 $total += $item['price'] * $item['quantity'];
    //                 $totalQuantity += $item['quantity'];
    //             }
    //         }
    //     }

    //     Session::put('cartTotalPrice', $total);
    //     return view('checkout', compact('cartItems', 'total', 'garageFittingCharge', 'shippingData', 'userOrdertype', 'calenderBook', 'selectedCountry', 'selectedCounty', 'totalQuantity', 'events'));
    // }


    public function add(Request $request)
    {
        $itemId = $request->input('id');
        $fittingType = $request->input('fitting_type', null);
        $qty = $request->input('qty', 1);
        $type = $request->input('type', 'tyre');
        $shippingPricePerJob = 0;
        $shippingPricePerTyre = 0;
        $shippingData = session('postcode_data', []);
        $hasMobileFitting = false;
        $cart = Session::get('cart', []);
        $existingFittingTypes = collect($cart)->pluck('fitting_type')->unique()->values()->toArray();

        $hasConflictingFittingType = false;
        $existingFittingTypeLabel = '';

        if (!empty($existingFittingTypes)) {
            if (!in_array($fittingType, $existingFittingTypes)) {
                $hasConflictingFittingType = true;
                $existingFittingTypeLabel = $existingFittingTypes[0] ?? 'an existing fitting type';
            }
        }

        if ($hasConflictingFittingType) {
            return response()->json([
                'success' => false,
                'message' => "This item has a different fitting type ({$fittingType}). The cart currently contains items with fitting type '{$existingFittingTypeLabel}'.",
                'needs_confirmation' => true,
                'requested_item' => [
                    'id' => $itemId,
                    'type' => $type,
                    'fitting_type' => $fittingType,
                    'qty' => $qty,
                ],
                'existing_fitting_type' => $existingFittingTypeLabel,
            ], 200);
        }

        if ($qty < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum quantity is 1.'
            ], 400);
        }
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

        if (!$product) {
            return response()->json(['success' => false, 'message' => ucfirst($type) . ' not found'], 404);
        }
        $cartKey = $type . '_' . $itemId;

        $existingQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $totalRequested = $existingQuantity + $qty;

        if ($type === 'tyre' && $totalRequested > $product->tyre_quantity) {
            return response()->json([
                'success' => false,
                'message' => nl2br("Requested quantity exceeds available stock.</br>Available: {$product->tyre_quantity}"),
            ], 200);
        }

        if (session('user_postcode') && $fittingType === 'mailorder') {
            $user_postcode = new Request(['postcode' => session('user_postcode')]);
            $response = $this->mailTyrePricingController->calculateMailShipping($user_postcode);
            $shippingData = $response->getData(true);
            if (isset($shippingData['success'])) {
                session(['postcode_data' => $shippingData]);
            }
        }
        if (session('user_postcode') && $fittingType === 'mobile_fitted') {
            $user_postcode = new Request(['postcode' => session('user_postcode')]);
            $response = $this->mobileTyrePricingController->calculateShipping($user_postcode);
            $shippingData = $response->getData(true);
            if (isset($shippingData['success'])) {
                session(['postcode_data' => $shippingData]);
            }
        }

        // Add or update item in the cart
        if (isset($cart[$cartKey])) {
            if ($type === 'service') {

                return response()->json([
                    'success' => true,
                    'message' => 'Service already in cart',
                    'totalQuantity' => array_sum(array_column($cart, 'quantity')),
                    'cartSubTotal' => number_format(Session::get('cartSubTotal', 0), 2),
                    'vatTotal' => number_format(Session::get('vatTotal', 0), 2),
                    'cartTotalPrice' => number_format(Session::get('cartTotalPrice', 0), 2),
                ]);
            } else {
                $cart[$cartKey]['quantity'] += $qty;
            }
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
                $cartItem['image'] = $product->tyre_image ?? 'sample-tyre.png';
                $cartItem['ean'] = $product->tyre_ean ?? '';
                $cartItem['sku'] = $product->tyre_sku ?? '';
                $cartItem['desc'] = $product->tyre_description ?? '';
                $cartItem['tyre_weight'] = $product->tyre_weight ?? '10KG';
                $cartItem['supplier'] = $product->tyre_supplier_name ?? '';
            } elseif ($type === 'service') {
                $cartItem['image'] = $product->inner_image ?? 'no-img-service.jpg';
            }

            $cart[$cartKey] = $cartItem;
        }
        Session::put('cart', $cart);
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
            if ($item['fitting_type'] === 'mobile_fitted') {
                $hasMobileFitting = true;
                $shippingType = $shippingData['ship_type'] ?? 'job';
                $shippingPrice = $shippingData['ship_price'] ?? 0;

                if ($shippingType === 'job') {
                    $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                    $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                }
            }
            if ($item['fitting_type'] === 'mailorder') {
                $hasMobileFitting = true;
                $shippingType = $shippingData['ship_type'] ?? 'job';
                $shippingPrice = $shippingData['ship_price'] ?? 0;

                if ($shippingType === 'job') {
                    $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
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


        $totals = $this->cartTotalService->recalculate();

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' added to cart',
            'product' => $cart,
            'totalQuantity' => array_sum(array_column($cart, 'quantity')),
            'cartSubTotal' => $totals['cartSubTotal'] ?? number_format(Session::get('cartSubTotal', 0), 2),
            'vatTotal' => $totals['vatTotal'],
            'cartTotalPrice' => $totals['cartTotalPrice'],
            'shippingPricePerJob' => number_format(Session::get('shippingPricePerJob', 0), 2),
            'shippingPricePerTyre' => number_format(Session::get('shippingPricePerTyre', 0), 2),
            'shippingVAT' => number_format(Session::get('shippingVAT', 0), 2),
            'garageFittingCharge' => $totals['garageFittingCharge'],
            'garageFittingVAT' => $totals['garageFittingVAT'],
        ]);
    }
    public function clearCart(Request $request)
    {
        Session::forget('cart');
        Session::forget('cartSubTotal');
        Session::forget('vatTotal');
        Session::forget('cartTotalPrice');
        Session::forget('shippingPricePerJob');
        Session::forget('shippingPricePerTyre');
        Session::forget('shippingVAT');

        return response()->json(['success' => true, 'message' => 'Cart cleared successfully.']);
    }

    public function addAfterConfirmation(Request $request)
    {
        return $this->add($request);
    }
    public function update(Request $request)
    {
        $cart = session('cart', []);
        $garageFittingCharge = Session::get('garage_fitting_charge');
        $garageVatClass = Session::get('garageVatClass');
        $productId = $request->id;
        $action = $request->action;
        $itemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $productId && $item['type'] === 'tyre') {
                $itemKey = $key;
                break;
            }
        }
        if ($itemKey !== null && isset($cart[$itemKey])) {
            $product = TyresProduct::where('product_id', $productId)->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tyre not found in the database',
                ], 404);
            }

            $existingQuantity = $cart[$itemKey]['quantity'];
            $newQuantity = $existingQuantity;

            if ($action === 'increase') {
                $newQuantity++;
            } elseif ($action === 'decrease' && $existingQuantity > 1) {
                $newQuantity--;
            }

            if ($newQuantity > $product->tyre_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient Stock",
                ], 200);
            }

            $cart[$itemKey]['quantity'] = $newQuantity;
        }

        $cartSubTotal = 0;
        $vatTotal = 0;
        $grandTotal = 0;
        $shippingPricePerJob = 0;
        $shippingPricePerTyre = 0;
        $garageFittingCharges = 0;
        $garageFittingVAT = 0;

        $shippingData = session('postcode_data', []);
        $hasMobileFitting = false;
        $hasMailorderFitting = false;
        $hasGarageFittingCharge = false;

        foreach ($cart as $item) {
            $cartSubTotal += $item['price'] * $item['quantity'];
            if ($garageFittingCharge) {
                $garageFittingCharges = $garageFittingCharge * $item['quantity'];
            }
            if ($item['tax_class_id'] == 9) {
                $vatTotal += $item['price'] * $item['quantity'] * 0.2;
            }

            if ($item['fitting_type'] === 'mobile_fitted') {
                $hasMobileFitting = true;

                $shippingType = $shippingData['ship_type'] ?? 'job';
                $shippingPrice = $shippingData['ship_price'] ?? 0;

                if ($shippingType === 'job') {
                    $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                    $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                }
            }
            if ($item['fitting_type'] === 'mailorder') {
                $hasMailorderFitting = true;
                $shippingType = $shippingData['ship_type'] ?? 'job';
                $shippingPrice = $shippingData['ship_price'] ?? 0;

                if ($shippingType === 'job') {
                    $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                    $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                }
            }
            if ($item['fitting_type'] === 'fully_fitted') {
                $hasGarageFittingCharge = true;
            }
        }

        $shippingVAT = 0;
        if ($hasMobileFitting && ($shippingData['includes_vat'] ?? 0) == 9) {
            $totalShippingPrice = $shippingPricePerJob + $shippingPricePerTyre;
            $shippingVAT = ($shippingPricePerJob + $shippingPricePerTyre) * 0.2;
            $vatTotal += $shippingVAT;
            $grandTotal += $totalShippingPrice;
        }
        if ($hasMailorderFitting && ($shippingData['includes_vat'] ?? 0) == 9) {
            $totalShippingPrice = $shippingPricePerJob + $shippingPricePerTyre;
            $shippingVAT = ($shippingPricePerJob + $shippingPricePerTyre) * 0.2;
            $vatTotal += $shippingVAT;
            $grandTotal += $totalShippingPrice;
        }
        if ($hasGarageFittingCharge && $garageFittingCharges > 0 && $garageVatClass === 9) {
            $garageFittingVAT = $garageFittingCharges * 0.2;
            $vatTotal += $garageFittingVAT;
            $grandTotal += $garageFittingCharges;
        }
        $grandTotal += $cartSubTotal + $vatTotal;
        session([
            'cart' => $cart,
            'cartSubTotal' => $cartSubTotal,
            'vatTotal' => $vatTotal,
            'cartTotalPrice' => $grandTotal,
            'shippingPricePerJob' => $shippingPricePerJob,
            'shippingPricePerTyre' => $shippingPricePerTyre,
            'shippingVAT' => $shippingVAT,
            'garageFittingCharge' => $garageFittingCharges,
            'garageFittingVAT' => $garageFittingVAT,
            'garageVatClass' => $garageVatClass,
        ]);
        Session::save();
        $totals = $this->cartTotalService->recalculate();

        return response()->json([
            'success' => true,
            'message' => 'Item updated from cart',
            'cartSubTotal' => number_format($cartSubTotal, 2),
            'remainingItems' => array_sum(array_column($cart, 'quantity')),
            'shippingPricePerJob' => number_format($shippingPricePerJob, 2),
            'shippingPricePerTyre' => number_format($shippingPricePerTyre, 2),
            'shippingVAT' => number_format($shippingVAT, 2),
            'garageFittingCharges' => $garageFittingCharges,
            'garageFittingCharge' => $totals['garageFittingCharge'],
            'garageFittingVAT' => $totals['garageFittingVAT'],
            'vatTotal' => $totals['vatTotal'],
            'cartTotalPrice' => $totals['cartTotalPrice'],
        ]);
    }
    public function delete(Request $request)
    {
        $productId = $request->input('id');
        $cart = session()->get('cart', []);

        $itemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $productId) {
                $itemKey = $key;
                break;
            }
        }

        if ($itemKey !== null && isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            $cart = array_values($cart);
            session()->put('cart', $cart);

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
                if ($item['fitting_type'] === 'mobile_fitted') {
                    $hasMobileFitting = true;
                    $shippingType = $shippingData['ship_type'] ?? 'job';
                    $shippingPrice = $shippingData['ship_price'] ?? 0;

                    if ($shippingType === 'job') {
                        $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                    } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                        $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                    }
                }
            }

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
            $totals = $this->cartTotalService->recalculate();
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartSubTotal' => number_format($cartSubTotal, 2),
                'remainingItems' => array_sum(array_column($cart, 'quantity')),
                'shippingPricePerJob' => number_format($shippingPricePerJob, 2),
                'shippingPricePerTyre' => number_format($shippingPricePerTyre, 2),
                'shippingVAT' => number_format($shippingVAT, 2),
                'shippingPostcode' => $shippingData['postcode'] ?? '',
                'garageFittingCharge' => $totals['garageFittingCharge'],
                'garageFittingVAT' => $totals['garageFittingVAT'],
                'vatTotal' => $totals['vatTotal'],
                'cartTotalPrice' => $totals['cartTotalPrice'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart',
        ], 404);
    }
    public function remove(Request $request)
    {
        $productId = $request->input('id');
        $cart = session()->get('cart', []);

        $itemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $productId) {
                $itemKey = $key;
                break;
            }
        }

        if ($itemKey !== null && isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            $cart = array_values($cart);
            session()->put('cart', $cart);
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

                if ($item['fitting_type'] === 'mobile_fitted') {
                    $hasMobileFitting = true;
                    $shippingType = $shippingData['ship_type'] ?? 'job';
                    $shippingPrice = $shippingData['ship_price'] ?? 0;

                    if ($shippingType === 'job') {
                        $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                    } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
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
            $totals = $this->cartTotalService->recalculate();
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartSubTotal' => number_format($cartSubTotal, 2),
                'remainingItems' => array_sum(array_column($cart, 'quantity')),
                'shippingPricePerJob' => number_format($shippingPricePerJob, 2),
                'shippingPricePerTyre' => number_format($shippingPricePerTyre, 2),
                'shippingVAT' => number_format($shippingVAT, 2),
                'shippingPostcode' => $shippingData['postcode'] ?? '',
                'garageFittingCharge' => $totals['garageFittingCharge'],
                'garageFittingVAT' => $totals['garageFittingVAT'],
                'vatTotal' => $totals['vatTotal'],
                'cartTotalPrice' => $totals['cartTotalPrice'],
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
            'engine' => (string) $request->input('engine')
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