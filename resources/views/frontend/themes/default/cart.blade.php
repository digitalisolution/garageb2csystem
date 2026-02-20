<div class="table-content table-responsive">
    <table>
        <thead>
            <tr>
                <th class="hidden-xs"></th>
                <th>Item</th>
                <th class="hidden">Price</th>
                <th></th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subTotal = 0;
                $vatTotal = 0;
                $grandTotal = 0;
                $hasMobileFitting = false;
                $hasMailorderFitting = false;
                $hasGarageFittingCharge = false;
                $shippingPricePerJob = 0;
                $shippingPricePerTyre = 0;
                $garageFittingVAT = 0;
                $garageFittingCharge = 0;

                foreach ($cartItems as $item) {
                    $vatRate = $item['tax_class_id'] == 9 ? 1.2 : 1;
                    $itemPriceWithVAT = $item['price'] * $vatRate;
                    $itemTotal = $itemPriceWithVAT * $item['quantity'];
                    $itemVAT = $item['tax_class_id'] == 9 ? $item['price'] * $item['quantity'] * 0.2 : 0;
                    $subTotal += $item['price'] * $item['quantity'];
                    $vatTotal += $itemVAT;
                    $grandTotal += $itemTotal;
                    $garageFittingCharge =  $item['garageFittingCharges'];
                    $garageFittingVAT = $item['garageFittingVAT'];

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

                    $fittingType = strToUpper(str_replace('_', ' ', $item['fitting_type']));
                }
                $shippingVAT = 0;
                if ($hasMobileFitting && $shippingData) {
                    $totalShippingPrice = $shippingPricePerJob + $shippingPricePerTyre;
                    $shippingVAT = $shippingData['includes_vat'] == 9 ? ($totalShippingPrice * 0.2) : 0;
                    $vatTotal += $shippingVAT;
                    $grandTotal += $totalShippingPrice + $shippingVAT;
                }
                if ($hasMailorderFitting && $shippingData) {
                    $totalShippingPrice = $shippingPricePerJob + $shippingPricePerTyre;
                    $shippingVAT = $shippingData['includes_vat'] == 9 ? ($totalShippingPrice * 0.2) : 0;
                    $vatTotal += $shippingVAT;
                    $grandTotal += $totalShippingPrice + $shippingVAT;
                }
                 if ($hasGarageFittingCharge && $garageFittingCharge && $garageFittingVAT) {
                    $vatTotal += $garageFittingVAT;
                    $grandTotal += $garageFittingCharge + $garageFittingVAT;
                }

            @endphp
            @foreach ($cartItems as $item)
                @php
                    $defaultTyreImage = asset('frontend/themes/default/img/product/sample-tyre.png');
                    $serviceFallback = asset('frontend/themes/default/img/service-inner-img/no-img-service.jpg');

                    $imageUrl = $defaultTyreImage;
                    $onError = '';

                    $type = $item['type'] ?? '';

                    if ($type === 'tyre') {
                        $cdnBase = rtrim(config('cdn.tyre_cdn_url', ''), '/');
                        $imageName = $item['image'] ?? '';

                        if ($imageName && $imageName !== 'sample-tyre.png') {
                            $imageUrl = $cdnBase ? "{$cdnBase}/" . ltrim($imageName, '/') : $defaultTyreImage;
                            $onError = "this.onerror=null;this.src='{$defaultTyreImage}';";
                        } else {
                            $imageUrl = $defaultTyreImage;
                        }
                    } elseif ($type === 'service') {
                        $domain = str_replace(['http://', 'https://'], '', request()->getHost());
                        $customPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-inner-img/';
                        $defaultPath = 'frontend/themes/default/img/service-inner-img/';
                        $innerImage = $item['image'] ?? '';

                        $customFull = $innerImage ? public_path($customPath . $innerImage) : false;
                        $defaultFull = $innerImage ? public_path($defaultPath . $innerImage) : false;

                        if ($innerImage && $customFull && file_exists($customFull)) {
                            $imageUrl = asset($customPath . $innerImage);
                        } elseif ($innerImage && $defaultFull && file_exists($defaultFull)) {
                            $imageUrl = asset($defaultPath . $innerImage);
                        } else {
                            $imageUrl = $serviceFallback;
                        }
                    }
                @endphp
                <tr id="cart-item-{{ $item['product_id'] }}" data-id="cart-item-{{ $item['product_id'] }}"
                    data-tax-class-id="{{ $item['tax_class_id'] }}">
                    <td class="hidden-xs py-4 px-2"><img src="{{ $imageUrl }}" alt="{{ $item['model'] }}" class="cart_img"
                            width="auto" height="auto" @if($item['type'] === 'tyre')
                                onerror="this.onerror=null;this.src='{{ asset('frontend/themes/default/img/product/sample-tyre.png') }}';"
                            @endif></td>
                    <td class="product-name" width="50%">
                        @if ($item['type'] === 'tyre')
                            {{ $item['desc'] }}
                            <span class="badge bg-success">({{ $fittingType ?? 'Fully Fitted' }})</span>
                        @elseif ($item['type'] === 'service')
                            {{ strtoupper($item['model']) }}
                        @endif
                        <div class="clearfix"></div>
                        <a class="delete-item" href="javascript:void(0);" data-id="{{ $item['product_id'] }}">Delete</a>
                    </td>
                    <td class="price hidden">
                        <span class="amount">£{{ number_format($item['price'], 2) }}</span>
                    </td>
                    @if ($item['type'] === 'tyre')
                        <td class="product-quantity text-center" width="30%">
                            <div class="cart-plus-minus-qty">
                                <button class="dec qtybutton update-cart" data-id="{{ $item['product_id'] }}"
                                    data-action="decrease">-</button>
                                <span class="quantity">{{ $item['quantity'] }}</span>
                                <button class="inc qtybutton update-cart" data-id="{{ $item['product_id'] }}"
                                    data-action="increase">+</button>
                            </div>
                            <div id="stockavail" class="error-message text-danger mt-2" style="display: none;"></div>
                        </td>
                    @else
                        <td class="product-quantity text-center" width="30%">
                            <span class="badge bg-info">(Service)</span>
                        </td>
                    @endif
                    <td class="total text-right" width="20%">£{{ number_format($item['price'] * $item['quantity'], 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="text-right py-3 totalbill" id="totalbill">
    <h4>Sub-Total: <span id="sub-total">£{{ number_format($subTotal, 2) }}</span></h4>
    @if ($hasMobileFitting)
        <h4>Callout charges({{$shippingData['postcode'] ?? ''}}):<span
                id="shippingPrice">£{{ number_format($shippingPricePerJob + $shippingPricePerTyre, 2) }}</span></h4>
    @endif
    @if ($hasMailorderFitting)
        <h4>Shipping Charges({{$shippingData['postcode'] ?? ''}}):<span
                id="shippingPrice">£{{ number_format($shippingPricePerJob + $shippingPricePerTyre, 2) }}</span></h4>
    @endif
    @if ($hasGarageFittingCharge)
        <h4 class="d-flex align-items-center gap-2">
            Garage Fitting Charges
            <span data-bs-toggle="tooltip" data-bs-placement="top"
                title="Fitting charges are set by the selected garage and may vary between garages. This fee is charged by the garage, not TyreLab.">
                <i class="fa fa-info-circle"></i>
            </span>

            : <span id="garageFittingCharges">
                £{{ number_format($garageFittingCharge, 2) }}
            </span>
        </h4>
    @endif
    <h4>VAT (20%): <span id="vat-total">£{{ number_format($vatTotal, 2) }}</span></h4>
    <h4>Grand Total: <span id="grand-total">£{{ number_format($grandTotal, 2) }}</span></h4>
</div>

<style type="text/css">
    .delete-item {
        font-weight: 600;
        color: #ec1f27;
        text-decoration: underline;
        cursor: pointer;
    }
</style>