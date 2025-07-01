<div class="shopping-cart-content">
    @if (count($cart) > 0)
        <ul id="cart-items-list">
            @foreach ($cart as $item)
            
                <li class="single-shopping-cart" id="cart-item-{{ $item['id'] }}">
                    <div class="shopping-cart-title">
                        <div class="d-flex justify-content-between mb-2">
                            <h4>{{ $item['model'] }}</h4>
                            <h6>£{{ number_format($item['price'] * $item['quantity'], 2) }}</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="item_width">
                                @if(isset($item['desc']))
                                    <h6>{{ $item['desc'] }}</h6>
                                @endif
                            </div>
                            <h4 class="quantity">Qty: {{ $item['quantity'] }}</h4>
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
            <h4><strong>Sub-Total: <span id="sub-total" class="sub-total">£{{ number_format($subTotal, 2) }}</span></strong>
            </h4>
            @if ($shippingPricePerJob > 0 || $shippingPricePerTyre > 0)
                <h4><strong>Callout Charges:
                        <span>£{{ number_format($shippingPricePerJob + $shippingPricePerTyre, 2) }}</span></strong>
                </h4>
            @endif
            <h4><strong>VAT (20%): <span id="vat-total">£{{ number_format($vatTotal, 2) }}</span></strong></h4>
            <h4><strong>Grand Total: <span id="grand-total">£{{ number_format($grandTotal, 2) }}</span></strong></h4>
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