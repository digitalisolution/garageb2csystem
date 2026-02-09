<div class="shopping-cart-content">
    @if (count($cart) > 0)
        <ul id="cart-items-list">
            @foreach ($cart as $item)
                @php
                $defaultTyreImage = asset('frontend/themes/default/img/product/sample-tyre.png');
                $serviceFallback   = asset('frontend/themes/default/img/service-inner-img/no-img-service.jpg');

                $imageUrl = $defaultTyreImage;
                $onError  = '';

                $type = $item['type'] ?? '';

                if ($type === 'tyre') {
                    $cdnBase   = rtrim(config('cdn.tyre_cdn_url', ''), '/');
                    $imageName = $item['image'] ?? '';

                    if ($imageName && $imageName !== 'sample-tyre.png') {
                        $imageUrl = $cdnBase ? "{$cdnBase}/" . ltrim($imageName, '/') : $defaultTyreImage;
                        $onError  = "this.onerror=null;this.src='{$defaultTyreImage}';";
                    } else {
                        $imageUrl = $defaultTyreImage;
                    }
                } elseif ($type === 'service') {
                    $domain      = str_replace(['http://', 'https://'], '', request()->getHost());
                    $customPath  = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-inner-img/';
                    $defaultPath = 'frontend/themes/default/img/service-inner-img/';
                    $innerImage  = $item['image'] ?? '';

                    $customFull  = $innerImage ? public_path($customPath . $innerImage) : false;
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
            
                <li class="single-shopping-cart" id="cart-item-{{ $item['id'] }}">
                    <img src="{{ $imageUrl }}" alt="{{ $item['model'] }}" width="48" height="auto" 
                         @if($item['type'] === 'tyre')
                             onerror="this.onerror=null;this.src='{{ asset('frontend/themes/default/img/product/sample-tyre.png') }}';"
                         @endif>
                    <div class="shopping-cart-title">
                        <div class="d-flex justify-content-between mb-2">
                            <h4>{{ $item['model'] }}</h4>
                            <h6>£{{ number_format($item['price'] * $item['quantity'], 2) }}</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="item_width">
                                @if(isset($item['desc']))
                                    <h6>{{ $item['desc'] }}</h6>
                                @endif
                            </div>
                            <h4 class="quantity no-wrap">Qty: {{ $item['quantity'] }}</h4>
                        </div>
                    </div>
                    <div class="shopping-cart-delete">
                        <a class="delete-item" href="javascript:void(0);" data-id="{{ $item['id'] }}">
                            <i class="fa fa-times-circle"></i>
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="shopping-cart-total">
            <h4>Sub-Total: <span id="sub-total" class="sub-total">£{{ number_format($subTotal, 2) }}</span>
            </h4>
            @if ($shippingPricePerJob > 0 || $shippingPricePerTyre > 0)
                <h4>Callout Charges:
                        <span>£{{ number_format($shippingPricePerJob + $shippingPricePerTyre, 2) }}</span>
                </h4>
            @endif
            <h4>VAT (20%): <span id="vat-total">£{{ number_format($vatTotal, 2) }}</span></h4>
            <h4>Grand Total: <span id="grand-total">£{{ number_format($grandTotal, 2) }}</span></h4>
        </div>

        <div class="shopping-cart-btn btn-hover text-center">
            <a class="default-btn" href="{{ route('checkout') }}">Checkout</a>
        </div>
    @else
        <div class="text-center pb-3">Your Basket is Empty!</div>
    @endif
</div>

<style type="text/css">
    .item_width {
        width: 190px;
    }
</style>