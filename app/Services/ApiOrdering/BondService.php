<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\ApiOrder;

class BondService
{
    private $apiCode;
    private $tradingPoint;
    private $supplierEmail;
    private $apiUrl;

    public function __construct(array $supplierDetails = [])
    {
        // Use domain-specific configuration loaded by SetSiteEnv middleware
        // $this->tradingPoint = config('app.trading_point', $supplierDetails['trading_point'] ?? '');
        // $this->supplierEmail = config('app.supplier_email', $supplierDetails['bond_supplieremail'] ?? '');
        // $this->apiCode = config('app.api_code', $supplierDetails['bond_api_code'] ?? '');

        // // Determine API URL based on mode (live or test)
        // $apiMode = config('app.api_mode', $supplierDetails['bond_api_mode'] ?? 'test');
        $this->tradingPoint = 'BI06';
        $this->supplierEmail = 'shopersstore883@gmail.com';
        $this->apiCode = 'HA00036';

        // Determine API URL based on mode (live or test)
        $apiMode = 'test';
        $this->apiUrl = ($apiMode == 'live')
            ? 'https://b2b.bondint.co.uk/scripts/cgiip.exe/WService=BondWSLive/WEBS/wsgateway.p'
            : 'https://testb2b.bondint.co.uk/scripts/cgiip.exe/WService=BondWSTest/WEBS/wsgateway.p';
    }

    /**
     * Place an API order.
     *
     * @param string $reference The order reference.
     * @param array $products The products to order.
     * @param string|null $jobid The job ID (optional).
     * @return array
     */
    public function placeApiOrder(string $reference, array $products, string $jobid = null): array
    {
        $status = '';
        $apiOrderId = '';
        $msg = '';

        // Log the start of the API order process
        Log::channel('bond_api_ordering')->info('Proceed bond API order for ' . config('app.name') . ' ' . $reference . ' id:: ' . $jobid . ' ' . $this->apiUrl . ' ' . $this->apiCode);

        // Check if products are provided
        if (empty($products)) {
            return [
                'api_order_id' => $apiOrderId,
                'msg' => 'No products provided.',
                'status' => 'danger',
                'type' => 'api',
            ];
        }

        // Build the product lines for the XML request
        $line1 = '';
        foreach ($products as $key => $product) {
            $code = $product['sku'] ?? $product['name'];
            $qty = $product['quantity'];
            $line1 .= '<Line><Line_ref>' . $key . '</Line_ref><Product_Code>' . $code . '</Product_Code><requested_qty>' . $qty . '</requested_qty></Line>';
        }

        // Send the query stock request
        $queryXml = '<?xml version="1.0"?><QueryStock><Header><Trading_Point>' . $this->tradingPoint . '</Trading_Point><Customer_Code>' . $this->apiCode . '</Customer_Code></Header>' . $line1 . '</QueryStock>';
        Log::channel('bond_api_ordering')->info('Bond Query Request:', ['xml' => $queryXml]);

        $response = $this->sendCurlRequest($this->apiUrl, $queryXml);
        Log::channel('bond_api_ordering')->info('Bond Query Stock Response:', ['response' => $response]);

        // Handle errors in the response
        if ($response === false) {
            return [
                'api_order_id' => $apiOrderId,
                'msg' => 'Failed to send request.',
                'status' => 'danger',
                'type' => 'api',
            ];
        }

        // Parse the XML response
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        // Check for errors in the parsed response
        if (isset($array['Error'])) {
            $error = $array['Error']['error'] ?? $array['Error'][0]['error'];
            return [
                'api_order_id' => $apiOrderId,
                'msg' => $error,
                'status' => 'danger',
                'type' => 'api',
            ];
        }

        // Prepare order lines for the final order request
        $lineArray = isset($array['Line'][0]) ? $array['Line'] : [$array['Line']];
        $line = '';
        foreach ($lineArray as $data) {
            if ($data['available_stock'] >= $data['requested_qty'] && !isset($data['Error'])) {
                $line .= '<Line>
                    <Line_ref>' . $data['line_ref'] . '</Line_ref>
                    <Product_Code>' . $data['product_code'] . '</Product_Code>
                    <Order_Quantity>' . $data['requested_qty'] . '</Order_Quantity>
                </Line>';
            } else {
                return [
                    'api_order_id' => $apiOrderId,
                    'msg' => 'Quantity error or insufficient stock.',
                    'status' => 'danger',
                    'type' => 'api',
                ];
            }
        }

        // Send the final order request
        if (!empty($line)) {
            $orderXml = '<?xml version="1.0"?><Standard_Order><Sales_Order><Header>
                <Operation_Type>Create</Operation_Type>
                <Order_Type>Sale</Order_Type>
                <Trading_Point>' . $this->tradingPoint . '</Trading_Point>
                <Sender_ID>SP-AGN</Sender_ID>
                <Customer><Code>' . $this->apiCode . '</Code>
                <Customer_Reference>' . $reference . '</Customer_Reference>
                </Customer>
                </Header>' . $line . '</Sales_Order></Standard_Order>';

            Log::channel('bond_api_ordering')->info('Bond Order Request XML:', ['xml' => $orderXml]);

            $orderResponse = $this->sendCurlRequest($this->apiUrl, $orderXml);
            Log::channel('bond_api_ordering')->info('Bond Order Response XML:', ['response' => $orderResponse]);

            // Handle errors in the order response
            if ($orderResponse === false) {
                return [
                    'api_order_id' => $apiOrderId,
                    'msg' => 'Failed to send order request.',
                    'status' => 'danger',
                    'type' => 'api',
                ];
            }

            // Parse the order response
            $orderXml = simplexml_load_string($orderResponse);
            $orderJson = json_encode($orderXml);
            $orderArray = json_decode($orderJson, true);

            // Check if the order was successful
            if ($orderArray['Header']['Status']['Code'] == 'OK') {
                $status = 'success';
                $apiOrderId = $orderArray['Header']['Bond_Reference'];
                $msg = 'Order placed successfully.';
            } else {
                $msg = $orderArray['Line']['Line_Status']['Error_Text'] . ' ' . $orderArray['Line']['Product_Code'];
                $status = 'danger';
            }
        }

        return [
            'api_order_id' => $apiOrderId,
            'msg' => $msg,
            'status' => $status,
            'type' => 'api',
        ];
    }

    /**
     * Send a cURL request.
     *
     * @param string $url The API URL.
     * @param string $xml The XML payload.
     * @return string|false
     */
    private function sendCurlRequest(string $url, string $xml)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_HTTPHEADER => [
                "cache-control: no-cache",
                "content-type: text/xml",
            ],
        ]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            Log::channel('bond_api_ordering')->error('cURL Error:', ['error' => curl_error($curl)]);
            return false;
        }
        curl_close($curl);

        return $response;
    }
}