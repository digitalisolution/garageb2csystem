<div class="table-content table-responsive">
    <table>
        <thead>
            <tr>
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

                // Initialize shipping costs
                $shippingPricePerJob = 0; // For ship_type = 'job'
                $shippingPricePerTyre = 0; // For ship_type = 'tyre'

                foreach ($cartItems as $item) {
                    $vatRate = $item['tax_class_id'] == 9 ? 1.2 : 1; // Apply 20% VAT if tax_class_id is 9
                    $itemPriceWithVAT = $item['price'] * $vatRate; // Calculate price including VAT
                    $itemTotal = $itemPriceWithVAT * $item['quantity']; // Calculate total price for the item
                    $itemVAT = $item['tax_class_id'] == 9 ? $item['price'] * $item['quantity'] * 0.2 : 0; // Calculate VAT for the item
                    $subTotal += $item['price'] * $item['quantity']; // Subtotal without VAT
                    $vatTotal += $itemVAT; // Total VAT
                    $grandTotal += $itemTotal; // Grand total including VAT

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
                    
                $fittingType = strToUpper(str_replace('_', ' ', $item['fitting_type']));
                }

                // Add shipping costs to totals
                $shippingVAT = 0;
                if ($hasMobileFitting && $shippingData) {
                    $totalShippingPrice = $shippingPricePerJob + $shippingPricePerTyre;
                    $shippingVAT = $shippingData['includes_vat'] == 9 ? ($totalShippingPrice * 0.2) : 0; // Calculate VAT for shipping
                    $vatTotal += $shippingVAT; // Add shipping VAT to total VAT
                    $grandTotal += $totalShippingPrice + $shippingVAT; // Add shipping price and VAT to grand total
                }
            @endphp
            @foreach ($cartItems as $item)

                <tr id="cart-item-{{ $item['product_id'] }}" data-id="cart-item-{{ $item['product_id'] }}"
                    data-tax-class-id="{{ $item['tax_class_id'] }}">
                    <td class="product-name" width="50%">
                        @if ($item['type'] === 'tyre')
                            {{ $item['desc'] }}
                            <span class="badge bg-success">({{ $fittingType ?? 'Fully Fitted' }})</span>
                        @elseif ($item['type'] === 'service')
                            {{ $item['model'] }}
                            <span class="badge bg-info">(Service)</span>
                        @endif
                        <div class="clearfix"></div>
                        <a class="delete-item" href="javascript:void(0);" data-id="{{ $item['product_id'] }}">Delete</a>
                    </td>
                    <td class="price hidden">
                        <span class="amount">£{{ number_format($item['price'], 2) }}</span>
                    </td>
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