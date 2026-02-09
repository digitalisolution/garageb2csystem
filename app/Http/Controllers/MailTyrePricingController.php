<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingCharges;
use App\Models\GarageDetails;
use App\Models\HeaderLink;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class MailTyrePricingController extends Controller
{
    public function index()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '12')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['settings'] = ShippingCharges::all();
        // dd($settings);
        return view('AutoCare.mail_order_pricing.index', $viewData);
    }

    // Show the form for creating a new mail_order_pricing setting
    public function create()
    {
        return $this->editMailShippingCharges();
    }
    public function saveMailShippingCharges(Request $request)
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
                if (!empty($mile['valueq']) || !empty($mile['valuep'])) {
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
            ->where('key', 'shippingbyproduct')->where('order_type', 'mailorder')
            ->first();

        if ($shippingByMiles) {
            $shippingByMiles->value = serialize($milesData);
            $shippingByMiles->save();
        } else {
            ShippingCharges::create([
                'code' => 'shippingbyproduct',
                'key' => 'shippingbyproduct',
                'value' => serialize($milesData),
                'order_type' => 'mailorder'
            ]);
        }

        // Process postcodes data
        $postcodesData = [];
        if ($request->has('postcodes')) {
            foreach ($request->input('postcodes') as $postcode) {
                if (!empty($postcode['post_code']) || !empty($postcode['price'])) {
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
            ->where('key', 'shippingbypostcode')->where('order_type', 'mailorder')
            ->first();

        if ($shippingByPostcode) {
            $shippingByPostcode->value = serialize($postcodesData);
            $shippingByPostcode->save();
        } else {
            ShippingCharges::create([
                'code' => 'shippingbypostcode',
                'key' => 'shippingbypostcode',
                'value' => serialize($postcodesData),
                'order_type' => 'mailorder',
            ]);
        }

        // Save the shipping charge type
        ShippingCharges::updateOrCreate(
            ['code' => 'shippingbyproduct', 'key' => 'shippingbyproduct_status'],
            ['value' => $request->input('charge_type')],
            ['order_type' => 'mailorder']
        );

        // Save the VAT setting
        ShippingCharges::updateOrCreate(
            ['code' => 'shippingbyproduct', 'key' => 'shippingbyproduct_tax', 'order_type' => 'mailorder'],
            ['value' => $request->input('vat')],
        );

        return redirect()->back()->with('success', 'Shipping charges updated successfully.');
    }
    public function editMailShippingCharges()
    {

        $shippingbyproduct = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct')->where('order_type', 'mailorder')
            ->first();
        $shippingByPostcode = ShippingCharges::where('code', 'shippingbypostcode')
            ->where('key', 'shippingbypostcode')->where('order_type', 'mailorder')
            ->first();

        $shippingbyproductStatus = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct_status')->where('order_type', 'mailorder')
            ->first();

        $shippingbyproduct_taxrate = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct_tax')->where('order_type', 'mailorder')
            ->first();
        // Decode stored serialized data
        $milesData = $shippingbyproduct && !empty($shippingbyproduct->value) ? unserialize($shippingbyproduct->value) : [];
        $postcodeData = $shippingByPostcode && !empty($shippingByPostcode->value) ? unserialize($shippingByPostcode->value) : [];
        $shippingbyproduct_tax = $shippingbyproduct_taxrate
            ? (string) $shippingbyproduct_taxrate->value
            : '';
        $selectedChargeType = ($shippingbyproductStatus && $shippingbyproductStatus->value == '1') ? '1' : '2';

        $viewData['header_link'] = HeaderLink::where("menu_id", '12')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();

        return view('AutoCare.mail_order_pricing.manage',array_merge($viewData, compact('milesData', 'postcodeData', 'selectedChargeType', 'shippingbyproduct_tax')));
    }
    public function calculateMailShipping(Request $request)
    {
        $request->validate([
            'postcode' => 'required|string|max:8',
        ]);

        $postcode = strtoupper($request->input('postcode'));
        $postcodePrefix = substr($postcode, 0, 3);
        $shippingCharges = ShippingCharges::where('code', 'shippingbypostcode')
            ->where('key', 'shippingbypostcode')->where('order_type', 'mailorder')
            ->first();
        $postcodeData = $shippingCharges && !empty($shippingCharges->value) ? unserialize($shippingCharges->value) : [];
        $shippingByMiles = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct')->where('order_type', 'mailorder')
            ->first();
        $milesData = $shippingByMiles && !empty($shippingByMiles->value) ? unserialize($shippingByMiles->value) : [];
        $shippingByProductStatus = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct_status')->where('order_type', 'mailorder')
            ->first();
        $status = $shippingByProductStatus && !empty($shippingByProductStatus->value) ? (int) $shippingByProductStatus->value : 1;
        $shippingByProductTax = ShippingCharges::where('code', 'shippingbyproduct')
            ->where('key', 'shippingbyproduct_tax')->where('order_type', 'mailorder')
            ->first();
        $taxValue = $shippingByProductTax && !empty($shippingByProductTax->value) ? (int) $shippingByProductTax->value : 0;
        $matchedPostcode = null;
        foreach ($postcodeData as $entry) {
            $postcodes = explode(',', $entry['post_code']);
            foreach ($postcodes as $pc) {
                $dbPostcodePrefix = strtoupper(substr(trim($pc), 0, 3));
                if ($dbPostcodePrefix === $postcodePrefix) {
                    $matchedPostcode = $entry;
                    break 2;
                }
            }
        }

        if ($status === 2) {
            if (!$matchedPostcode) {
                return response()->json([
                    'error' => 'Sorry, we are not covering that area.'
                ], 404);
            }

            $price = $matchedPostcode['price'];
            $shipType = $matchedPostcode['ship_type'] ?? 'job';
            $vatPercentage = 20;
            $vatAmount = 0;
            $priceWithVat = $price;
            if ($taxValue === 9) {
                $vatAmount = ($price * $vatPercentage) / 100;
                $priceWithVat += $vatAmount;
            }
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
                'ship_type' => $shipType,
            ]);
        }
        if ($status === 1) {
            if (empty($milesData)) {
                return response()->json([
                    'error' => 'No mileage data is available.'
                ], 404);
            }

            $garage = GarageDetails::first();
            $origin = $garage ? $garage->zone : 'DEFAULT_POSTCODE';
            $destination = $postcode . ', UK';
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => env('GOOGLE_MAPS_API_KEY'),
            ]);
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

            $price = 0;
            $shipType = 'job';
            foreach ($milesData as $mileEntry) {
                $valueq = (float) $mileEntry['valueq'];
                $valuep = (float) $mileEntry['valuep'];
                if ($distanceInMiles <= $valueq) {
                    $price = $valuep;
                    $shipType = $mileEntry['ship_type'] ?? 'job';
                    break;
                }
            }

            if ($price === 0) {
                return response()->json([
                    'error' => 'Sorry, we are not covering that area.'
                ], 400);
            }

            $vatPercentage = 20;
            $vatAmount = 0;
            $priceWithVat = $price;
            if ($taxValue === 9) {
                $vatAmount = ($price * $vatPercentage) / 100;
                $priceWithVat += $vatAmount;
            }

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
                'ship_type' => $shipType,
            ]);
        }

        return response()->json([
            'error' => 'Invalid shipping configuration. Please contact support.'
        ], 500);
    }

    private function convertMetersToMiles($meters)
    {
        return $meters * 0.000621371;
    }

    public function storeMailPostcodeSession(Request $request)
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