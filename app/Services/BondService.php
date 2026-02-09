<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class BondService
{
    private $apiCode;
    private $tradingPoint;
    private $supplierEmail;
    private $apiUrl;

    public function __construct(array $supplierDetails)
    {
        // Dynamically use supplier details passed from SupplierServiceFactory
        $this->tradingPoint = $supplierDetails['trading_point'] ?? '';
        $this->supplierEmail = $supplierDetails['supplier_email'] ?? '';
        $this->apiCode = $supplierDetails['api_code'] ?? '';

        // Determine API URL based on mode (live or test)
        $apiMode = $supplierDetails['api_mode'] ?? 'test';
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
        $responseDetails = []; // To hold additional details for each product
         $garage = getGarageDetails();
        $garageName = $garage->garage_name;

        Log::info('Proceed bond API order for ' .$garageName . ' ' . 'workshop_id:: ' . $reference . ' ' . $this->apiUrl . ' ' . $this->apiCode);

        if (empty($products)) {
            return [
                'api_order_id' => $apiOrderId,
                'msg' => 'No products provided.',
                'status' => 'danger',
                'type' => 'api',
            ];
        }

        $line1 = '';
        foreach ($products as $key => $product) {
            $code = $product['sku'] ?? $product['description'];
            $qty = $product['quantity'];
            $ean = $product['ean'] ?? 'N/A'; // Get EAN from the product data
            $line1 .= '<Line><Line_ref>' . $key . '</Line_ref><Product_Code>' . $code . '</Product_Code><requested_qty>' . $qty . '</requested_qty></Line>';

            // Initialize response details with EAN
            $responseDetails[$key] = [
                'sku' => $code,
                'quantity' => $qty,
                'ean' => $ean,
                'supplier' => 'bond',
            ];
        }

        $queryXml = '<?xml version="1.0"?><QueryStock><Header><Trading_Point>' . $this->tradingPoint . '</Trading_Point><Customer_Code>' . $this->apiCode . '</Customer_Code></Header>' . $line1 . '</QueryStock>';
        Log::info('Bond Query Request:', ['xml' => $queryXml]);

        $response = $this->sendCurlRequest($this->apiUrl, $queryXml);
        Log::info('Bond Query Stock Response:', ['response' => $response]);

        if ($response === false) {
            return [
                'api_order_id' => $apiOrderId,
                'msg' => 'Failed to send request.',
                'status' => 'danger',
                'type' => 'api',
            ];
        }

        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        if (isset($array['Error'])) {
            $error = $array['Error']['error'] ?? $array['Error'][0]['error'];
            return [
                'api_order_id' => $apiOrderId,
                'msg' => $error,
                'status' => 'danger',
                'type' => 'api',
            ];
        }

        $lineArray = isset($array['Line'][0]) ? $array['Line'] : [$array['Line']];
        $line = '';
        foreach ($lineArray as $data) {
            if ($data['available_stock'] >= $data['requested_qty'] && !isset($data['Error'])) {
                $line .= '<Line>
                <Line_ref>' . $data['line_ref'] . '</Line_ref>
                <Product_Code>' . $data['product_code'] . '</Product_Code>
                <Order_Quantity>' . $data['requested_qty'] . '</Order_Quantity>
            </Line>';

                // Update response details with additional API response data
                $responseDetails[$data['line_ref']]['api_order_id'] = $apiOrderId;
                $responseDetails[$data['line_ref']]['quantity'] = $data['requested_qty'];
            } else {
                return [
                    'api_order_id' => $apiOrderId,
                    'msg' => 'Quantity error or insufficient stock.',
                    'status' => 'danger',
                    'type' => 'api',
                ];
            }
        }

        if (!empty($line)) {
            $orderXml = '<?xml version="1.0"?><Standard_Order><Sales_Order><Header>
            <Operation_Type>Create</Operation_Type>
            <Order_Type>Sale</Order_Type>
            <Trading_Point>' . $this->tradingPoint . '</Trading_Point>
            <Sender_ID>GECTECH</Sender_ID>
            <Customer><Code>' . $this->apiCode . '</Code>
            <Customer_Reference>' . $reference . '</Customer_Reference>
            </Customer>
            </Header>' . $line . '</Sales_Order></Standard_Order>';

            Log::info('Bond Order Request XML:', ['xml' => $orderXml]);

            $orderResponse = $this->sendCurlRequest($this->apiUrl, $orderXml);
            Log::info('Bond Order Response XML:', ['response' => $orderResponse]);

            if ($orderResponse === false) {
                return [
                    'api_order_id' => $apiOrderId,
                    'msg' => 'Failed to send order request.',
                    'status' => 'danger',
                    'type' => 'api',
                ];
            }

            $orderXml = simplexml_load_string($orderResponse);
            $orderJson = json_encode($orderXml);
            $orderArray = json_decode($orderJson, true);

            if ($orderArray['Header']['Status']['Code'] == 'OK') {
                $status = 'success';
                $apiOrderId = $orderArray['Header']['Bond_Reference'];
                $msg = 'Order placed successfully.';

                foreach ($responseDetails as &$detail) {
                    $detail['api_order_id'] = $apiOrderId;
                    $detail['reference'] = 'JOB-' . $reference;
                }
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
            'details' => array_values($responseDetails), // Ensure the array is indexed numerically
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
            Log::error('cURL Error:', ['error' => curl_error($curl)]);
            return false;
        }
        curl_close($curl);

        return $response;
    }
}