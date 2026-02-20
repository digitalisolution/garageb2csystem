<?php

namespace App\Http\Controllers\ViewController;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use App\Models\GarageReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\DistanceService;
use Illuminate\Support\Facades\Auth;

class GarageViewController extends Controller
{

    protected $distanceService;

    public function __construct(DistanceService $distanceService)
    {
        $this->distanceService = $distanceService;
    }

    public function garages()
    {
        $cart = session()->get('cart', []);
        $user_postcode = session()->get('user_postcode');
        $user_ordertype = session()->get('user_ordertype');
        
        $hasTyres = collect($cart)->contains(function ($item) {
            return isset($item['type']) && $item['type'] === 'tyre';
        });

        if (!$hasTyres) {
            return redirect()->route('home');
        }

        $garagesQuery = Garage::where('garage_status', 1);
        
        if ($user_ordertype) {
            $garagesQuery->where(function ($query) use ($user_ordertype) {
                $query->where('garage_order_types', 'LIKE', '%' . $user_ordertype . '%')
                      ->orWhere('garage_order_types', '=', '');
            });
        }

        $garages = $garagesQuery->get();
        if ($user_postcode) {
            $origin = $user_postcode;
            $garages = $garages->map(function ($garage) use ($origin) {
                $destination = $garage->garage_postcode ?? $garage->garage_zone;
                if ($destination) {
                    $distance = $this->distanceService->getDistanceMiles($origin, $destination);
                    $garage->distance = $distance ?? 999;
                } else {
                    $garage->distance = 999;
                }
                return $garage;
            })->sortBy('distance')->values();
        }

        $domain = str_replace('.', '-', request()->getHost());
        return view('garages.listing', compact('garages', 'user_postcode', 'domain'));
    }

    public function garageProfile($id)
    {
        $garages = Garage::where('id', $id)->where('garage_status', 1)->firstOrFail();
        $reviews = $garages->reviews()->where('approved', true)->with('customer')->latest()->get();
        $avgRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();
        $hasReviewed = false;
        $myReview = null;

        if (Auth::guard('customer')->check()) {
            $customerId = Auth::guard('customer')->id();
            $myReview = $garages->reviews()->where('customer_id', $customerId)->first();
            $hasReviewed = $myReview ? true : false;
        }
        return view('garages.profile', compact(
            'garages',
            'reviews',
            'avgRating',
            'totalReviews',
            'hasReviewed',
            'myReview'
        ));
    }

    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
        ]);

        $garages = Garage::findOrFail($id);

        if (!Auth::guard('customer')->check()) {
            return redirect()->back()->with('error', 'You must be logged in to review.');
        }
        $customerId = Auth::guard('customer')->id();
        $existing = GarageReview::where('garage_id', $id)->where('customer_id', $customerId)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'You have already reviewed this garage.');
        }

        GarageReview::create([
            'garage_id' => $id,
            'customer_id' => $customerId,
            'rating' => $request->rating,
            'review' => $request->review,
            'approved' => true,
        ]);

        return redirect()->back()->with('success', 'Thank you for your review!');
    }

    public function list(Request $request)
    {
        $domain = str_replace('.', '-', $request->getHost());
        $user_postcode = Session::get('user_postcode');
        $user_ordertype = Session::get('user_ordertype');

        // Load active garages with order type filtering
        $garagesQuery = Garage::where('garage_status', 1);
        
        // Filter by order type if available
        if ($user_ordertype) {
            $garagesQuery->where(function ($query) use ($user_ordertype) {
                $query->where('garage_order_types', 'LIKE', '%' . $user_ordertype . '%')
                      ->orWhere('garage_order_types', '=', '');
            });
        }

        $garages = $garagesQuery->get();

        if ($user_postcode) {
            $origin = $user_postcode;
            $garages = $garages->map(function ($garage) use ($origin) {
                $destination = $garage->garage_postcode ?? $garage->garage_zone;
                if ($destination) {
                    $distance = $this->distanceService->getDistanceMiles($origin, $destination);
                    $garage->distance = $distance ?? 999;
                } else {
                    $garage->distance = 999;
                }
                return $garage;
            })->sortBy('distance')->values();
        }

        return view('garages.listing', compact('garages', 'user_postcode', 'domain'));
    }

    public function filter(Request $request)
    {
        try {
            $data = $request->validate([
                'postcode' => [
                    'required',
                    'string',
                    'regex:/^[A-Z]{1,2}[0-9][A-Z0-9]?\s?[0-9][A-Z]{2}$/i'
                ],
                'distance' => 'nullable|numeric|min:1|max:100',
                'sort' => 'nullable|string|in:distance,rating',
                'fitter_types' => 'nullable|array'
            ]);

            $postcode = strtoupper(trim($data['postcode']));
            $maxMiles = $data['distance'] ?? 15;
            $sort = $data['sort'] ?? 'distance';
            $fitterTypes = $data['fitter_types'] ?? [];
            $user_ordertype = Session::get('user_ordertype');

            // Save to session
            Session::put('user_postcode', $postcode);
            Session::put('distance', $maxMiles);
            Session::put('sort', $sort);

            $garagesQuery = Garage::where('garage_status', 1);

            // Filter by order type if available
            if ($user_ordertype) {
                $garagesQuery->where(function ($query) use ($user_ordertype) {
                    $query->where('garage_order_types', 'LIKE', '%' . $user_ordertype . '%')
                          ->orWhere('garage_order_types', '=', '');
                });
            }

            if (!empty($fitterTypes)) {
                $garagesQuery->whereIn('fitter_type', $fitterTypes);
            }

            $garages = $garagesQuery->get();

            $garages = $garages->map(function ($garage) use ($postcode) {
                $destination = $garage->garage_postcode ?? $garage->garage_zone;
                if ($destination) {
                    $distance = $this->distanceService->getDistanceMiles($postcode, $destination);
                    if ($distance !== null) {
                        $garage->distance = $distance;
                        return $garage;
                    }
                }
                return null;
            })->filter();

            $garages = $garages->filter(function ($garage) use ($maxMiles) {
                return isset($garage->distance) && $garage->distance <= $maxMiles;
            });

            // Sort
            if ($sort === 'rating') {
                $garages = $garages->sortByDesc(function ($garage) {
                    return $garage->reviews->avg('rating') ?? 0;
                })->values();
            } else {
                $garages = $garages->sortBy('distance')->values();
            }

            $domain = str_replace('.', '-', $request->getHost());
            
            // Return only the content, not the full response
            return response()->view('garages.partials.list', compact('garages', 'domain'))->content();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'error' => 'Please enter a valid UK postcode (e.g., B70 0XA)'
            ], 422);
        } catch (\Exception $e) {
            // Handle other errors
            return response()->json([
                'error' => 'Invalid postcode or unable to find garages in this area'
            ], 422);
        }
    }

    public function savePostcode(Request $request)
    {
        $request->validate(['postcode' => 'required|string']);
        Session::put('user_postcode', $request->postcode);
        return response()->json(['success' => true]);
    }
    
    public function bookNow($id)
{
    $garage = Garage::where('id', $id)
        ->where('garage_status', 1)
        ->firstOrFail();
    Session::put('selected_garage_id', $garage->id);
    Session::put('selected_garage_name', $garage->garage_name);
    Session::put('garage_fitting_charge', $garage->garage_fitting_charges ?? 0);    
    Session::put('garage_fitting_vat_class', $garage->garage_fitting_vat_class ?? 0);

    app(\App\Services\CartTotalService::class)->recalculate();

    return redirect()->route('checkout');
}

}