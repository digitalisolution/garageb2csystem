<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingCharges;
use App\Models\GarageDetails;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class MobileTyrePricingController extends Controller
{
    // Display all mobile_fitting_pricing settings
    public function index()
    {
        $settings = ShippingCharges::all();
        // dd($settings);
        return view('AutoCare.mobile_fitting_pricing.index', compact('settings'));
    }

    // Show the form for creating a new mobile_fitting_pricing setting
    public function create()
    {
        return $this->editShippingCharges();
    }
    public function saveShippingCharges(Request $request)
    {
        // dd($request);
        // Validate the incoming request
        $request->validate([
            'miles.*.valueq' => 'nullable|numeric',
            'miles.*.valuep' => 'nullable|numeric',
            'miles.*.ship_type' => 'nullable|string',
            'miles.*.day' => 'nullable|string',
            'miles.*.callout_from_time' => 'nullable|string',
            'miles.*.callout_to_time' => 'nullable|string',

            'postcodes.*.post_code' => 'nullable|string',
            'postcodes.*.price' => 'nullable|numeric',
            'postcodes.*.ship_type' => 'nullable|string',
            'postcodes.*.day' => 'nullable|string',
            'postcodes.*.callout_from_time' => 'nullable|string',
            'postcodes.*.callout_to_time' => 'nullable|string',

            'charge_type' => 'required|in:1,2',
            'vat' => 'required|in:0,9',
        ]);

        // Process miles data
        $milesData = [];
        if ($request->has('miles')) {
            foreach ($request->input('miles') as $mile) {
                if (!empty($mile['valueq']) || !empty($mile['valuep'])) { // Ensure the row is not empty
                    $milesData[] = [
                        'valueq' => $mile['valueq'] ?? null,
                        'valuep' => $mile['valuep'] ?? null,
                        'ship_type' => $mile['ship_type'] ?? 'job',
                        'day' => $mile['day'] ?? null,
                        'callout_from_time' => $mile['callout_from_time'] ?? null,
                        'callout_to_time' => $mile['callout_to_time'] ?? null,
                    ];
                }
            }
        }

        // Save miles data
        $shippingByMiles = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct')
            ->first();

        if ($shippingByMiles) {
            $shippingByMiles->value = serialize($milesData);
            $shippingByMiles->save();
        } else {
            ShippingCharges::create([
                'code' => 'shippingbyproduct',
                'key' => 'shippingbyproduct',
                'value' => serialize($milesData),
            ]);
        }

        // Process postcodes data
        $postcodesData = [];
        if ($request->has('postcodes')) {
            foreach ($request->input('postcodes') as $postcode) {
                if (!empty($postcode['post_code']) || !empty($postcode['price'])) { // Ensure the row is not empty
                    $postcodesData[] = [
                        'post_code' => $postcode['post_code'] ?? null,
                        'price' => $postcode['price'] ?? null,
                        'ship_type' => $postcode['ship_type'] ?? 'job',
                        'day' => $postcode['day'] ?? null,
                        'callout_from_time' => $postcode['callout_from_time'] ?? null,
                        'callout_to_time' => $postcode['callout_to_time'] ?? null,
                    ];
                }
            }
        }

        // Save postcodes data
        $shippingByPostcode = ShippingCharges::where('code', 'shippingbypostcode')
            ->where('key', 'shippingbypostcode')
            ->first();

        if ($shippingByPostcode) {
            $shippingByPostcode->value = serialize($postcodesData);
            $shippingByPostcode->save();
        } else {
            ShippingCharges::create([
                'code' => 'shippingbypostcode',
                'key' => 'shippingbypostcode',
                'value' => serialize($postcodesData),
            ]);
        }

        // Save the shipping charge type
        ShippingCharges::updateOrCreate(
            ['code' => 'shippingbyproduct', 'key' => 'shippingbyproduct_status'],
            ['value' => $request->input('charge_type')]
        );

        // Save the VAT setting
        ShippingCharges::updateOrCreate(
            ['code' => 'shippingbyproduct', 'key' => 'shippingbyproduct_tax'],
            ['value' => $request->input('vat')]
        );

        return redirect()->back()->with('success', 'Shipping charges updated successfully.');
    }
    public function editShippingCharges()
    {
        // Retrieve records from the database
        $shippingbyproduct = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct')
            ->first();
        $shippingByPostcode = ShippingCharges::where('code', 'shippingbypostcode')
            ->where('key', 'shippingbypostcode')
            ->first();

        $shippingbyproductStatus = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct_status')
            ->first();

        $shippingbyproduct_taxrate = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct_tax')
            ->first();
        // Decode stored serialized data
        $milesData = $shippingbyproduct && !empty($shippingbyproduct->value) ? unserialize($shippingbyproduct->value) : [];
        $postcodeData = $shippingByPostcode && !empty($shippingByPostcode->value) ? unserialize($shippingByPostcode->value) : [];
        $shippingbyproduct_tax = $shippingbyproduct_taxrate
            ? (string) $shippingbyproduct_taxrate->value
            : '';

        // Determine the selected shipping charge type
        $selectedChargeType = ($shippingbyproductStatus && $shippingbyproductStatus->value == '1') ? '1' : '2';


        return view('AutoCare.mobile_fitting_pricing.manage', compact('milesData', 'postcodeData', 'selectedChargeType', 'shippingbyproduct_tax'));
    }
    public function calculateShipping(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'postcode' => 'required|string|max:8',
        ]);

        $postcode = strtoupper($request->input('postcode')); // Convert input postcode to uppercase
        $postcodePrefix = substr($postcode, 0, 3); // Extract the first 3 characters of the postcode

        // Fetch shipping charges from the database
        $shippingCharges = ShippingCharges::where('code', 'shippingbypostcode')
            ->where('key', 'shippingbypostcode')
            ->first();

        // Deserialize the postcode data
        $postcodeData = $shippingCharges && !empty($shippingCharges->value) ? unserialize($shippingCharges->value) : [];

        // Deserialize the miles data
        $shippingByMiles = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct')
            ->first();
        $milesData = $shippingByMiles && !empty($shippingByMiles->value) ? unserialize($shippingByMiles->value) : [];

        // Fetch the shippingbyproduct_status
        $shippingByProductStatus = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct_status')
            ->first();
        $status = $shippingByProductStatus && !empty($shippingByProductStatus->value) ? (int) $shippingByProductStatus->value : 1;
        // Fetch the shippingbyproduct_tax
        $shippingByProductTax = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct_tax')
            ->first();
        $taxValue = $shippingByProductTax && !empty($shippingByProductTax->value) ? (int) $shippingByProductTax->value : 0;

        // Check if the postcode exists in the postcode data (match by first 3 characters)
        $matchedPostcode = null;
        foreach ($postcodeData as $entry) {
            $postcodes = explode(',', $entry['post_code']);
            foreach ($postcodes as $pc) {
                $dbPostcodePrefix = strtoupper(substr(trim($pc), 0, 3)); 
                if ($dbPostcodePrefix === $postcodePrefix) {
                    $matchedPostcode = $entry;
                    break 2; // Exit both loops if a match is found
                }
            }
        }

        // If status is 2, use postcode-based pricing
        if ($status === 2) {
            if (!$matchedPostcode) {
                return response()->json([
                    'error' => 'Sorry, we are not covering that area.'
                ], 404);
            }

            // Use postcode-based pricing
            $price = $matchedPostcode['price'];
            $shipType = $matchedPostcode['ship_type'] ?? 'job'; // Default to 'job' if not specified

            // Add VAT if taxValue is 9
            $vatPercentage = 20;
            $vatAmount = 0;
            $priceWithVat = $price;
            if ($taxValue === 9) {
                $vatAmount = ($price * $vatPercentage) / 100;
                $priceWithVat += $vatAmount;
            }

            // Store shipping data in session
            session([
                'postcode_data' => [
                    'postcode' => $postcode,
                    'ship_price' => round($price, 2),
                    'total_price' => round($priceWithVat, 2),
                    'vat_amount' => round($vatAmount, 2),
                    'includes_vat' => $taxValue === 9 ? 9 : 0,
                    'ship_type' => $shipType,
                ],
            ]);

            return response()->json([
                'success' => true,
                'postcode' => $postcode,
                'ship_price' => round($price, 2),
                'total_price' => round($priceWithVat, 2),
                'vat_amount' => round($vatAmount, 2),
                'includes_vat' => $taxValue === 9 ? 9 : 0,
                'ship_type' => $shipType, // Include ship type in the response
            ]);
        }

        // If status is 1, use mileage-based pricing
        if ($status === 1) {
            if (empty($milesData)) {
                return response()->json([
                    'error' => 'No mileage data is available.'
                ], 404);
            }

            $garage = GarageDetails::first(); // Get the first garage record (modify as needed)
            $origin = $garage ? $garage->zone : 'DEFAULT_POSTCODE'; // Fallback in case no data is found
            $destination = $postcode . ', UK'; // Assuming UK postcodes
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => env('GOOGLE_MAPS_API_KEY'),
            ]);
            // dd($response);

            // Debug the API response
            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to fetch distance data. Please try again later.'
                ], 500);
            }

            $distanceData = $response->json();
            if ($distanceData['status'] !== 'OK') {
                return response()->json([
                    'error' => 'Unable to calculate distance for the given postcode. Error: ' . ($distanceData['error_message'] ?? 'Unknown error')
                ], 500);
            }
            $distanceInMeters = $distanceData['rows'][0]['elements'][0]['distance']['value'];
            $distanceInMiles = $this->convertMetersToMiles($distanceInMeters);

            // Use mileage-based pricing
            $price = 0;
            $shipType = 'job'; // Default ship type
            foreach ($milesData as $mileEntry) {
                $valueq = (float) $mileEntry['valueq']; // Upper limit of miles range
                $valuep = (float) $mileEntry['valuep']; // Price for this range
                if ($distanceInMiles <= $valueq) {
                    $price = $valuep;
                    $shipType = $mileEntry['ship_type'] ?? 'job'; // Extract ship type from miles data
                    break;
                }
            }

            if ($price === 0) {
                return response()->json([
                    'error' => 'Sorry, we are not covering that area.'
                ], 400);
            }

            // Add VAT if taxValue is 9
            $vatPercentage = 20;
            $vatAmount = 0;
            $priceWithVat = $price;
            if ($taxValue === 9) {
                $vatAmount = ($price * $vatPercentage) / 100;
                $priceWithVat += $vatAmount;
            }

            // Store shipping data in session
            session([
                'postcode_data' => [
                    'postcode' => $postcode,
                    'distance_in_miles' => round($distanceInMiles, 2),
                    'ship_price' => round($price, 2),
                    'total_price' => round($priceWithVat, 2),
                    'vat_amount' => round($vatAmount, 2),
                    'includes_vat' => $taxValue === 9 ? 9 : 0,
                    'ship_type' => $shipType,
                ],
            ]);

            return response()->json([
                'success' => true,
                'postcode' => $postcode,
                'distance_in_miles' => round($distanceInMiles, 2),
                'ship_price' => round($price, 2),
                'total_price' => round($priceWithVat, 2),
                'vat_amount' => round($vatAmount, 2),
                'includes_vat' => $taxValue === 9 ? 9 : 0,
                'ship_type' => $shipType, // Include ship type in the response
            ]);
        }

        // Default fallback if status is neither 1 nor 2
        return response()->json([
            'error' => 'Invalid shipping configuration. Please contact support.'
        ], 500);
    }

    private function convertMetersToMiles($meters)
    {
        return $meters * 0.000621371; // Conversion factor
    }

    public function storePostcodeSession(Request $request)
    {
        $data = $request->validate([
            'postcode' => 'required|string|max:8',
            'ship_price' => 'required|numeric',
            'total_price' => 'required|numeric',
            'vat_amount' => 'nullable|numeric',
            'includes_vat' => 'nullable|integer',
            'ship_type' => 'required|string',
            'distance_in_miles' => 'nullable|numeric',
        ]);

        // Store the data in the session
        session(['postcode_data' => $data]);

        return response()->json(['success' => true]);
    }


}