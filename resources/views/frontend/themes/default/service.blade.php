@extends('layouts.app')

@section('content')

<div class="breadcrumb-area pt-35 pb-35 bg-gray-3">
    <div class="container">
        <div class="breadcrumb-content text-center">
            <ul>
                <li>
                    <a href="/">Home</a>
                </li>
                <li class="active">Book Your Service</li>
            </ul>
        </div>
    </div>
</div>

<div class="pt-70 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="service_bank">
                    @foreach ($services as $service)
                        <div class="service_card">
                            @if($service->cost_price > 0)
                            <span class="cost_price">  
                                Cost: &pound;{{$service->tax_class_id == 9 ? number_format($service->cost_price * 1.20, 2) : number_format($service->cost_price, 2)  }}
                            </span>
                            @endif

                            <div class="row align-items-center">
                            <div class="col-lg-4 col-md-5 col-12">
                                @if($service->inner_image)
                                    <img src="frontend/themes/default/img/service/{{ $service->inner_image ?? $service->image }}" class="img-bank">
                                @elseif(!$service->inner_image)
                                    <img src="frontend/themes/default/img/service/{{ $service->image ?? '' }}" class="img-bank">
                                @endif
                            </div>
                            <div class="col-lg-8 col-md-7 col-12">
                                <h4>{{ $service->name }}</h4>
                                        <p><i class="pe-7s-timer"></i> Work Time</p>
                                        <ul class="tick">
                                            <li>Genuine Spare Parts</li>
                                            <li>Expert Mechanics</li>
                                            <li>Comprehensive Inspection</li>
                                            <li>Transparent Pricing</li>
                                        </ul>
                                        <div class="mt-4">
                                            <a href="{{ route('slug.handle', $service->slug) }}" class="btn btn-dark">View Details</a>
                                            @if ($service->cost_price > 0)
                                                <a href="javascript:void(0);" class="btn btn-theme-select add-to-cart" data-id="{{ $service->service_id }}"
                                                    data-name="{{ $service->name }}" data-price="{{ $service->cost_price }}"
                                                    data-type="service">Add to Cart</a>
                                            @else
                                                <!-- <a href="contact" class="btn btn-theme-select">Quote Now</a> -->
                                                <a href="tel:{{ $garage->mobile }}" class="btn btn-theme-select">Call Now</a>
                                            @endif
                                    </div>
                            </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @if($vehicleData)
            <div class="col-lg-3">
                <div class="vehicle_details-wrap p-4 pb-0 rounded mb-4">
                    <h3 class="text-white mb-4">Your Vehicle</h3>
                    <div class="vehicle_bank" id="vehicleInfo">
                        <h4 class="text-uppercase"><strong id="vehicleReg"></strong></h4>
                        <p class="mb-0"><strong id="vehicleMake">{{$vehicleData['make']}}</strong></p>
                        <p class="mb-0"><strong id="vehicleModel">{{$vehicleData['model']}}</strong></p>
                        <p class="mb-0"><strong id="vehicleYear">{{$vehicleData['year']}}</strong></p>
                       @if($vehicleData['regNumber'])
                        <p class="mb-0"><strong id="vehicleEngine" class="text-uppercase badge-dark p-1 rounded px-2">{{$vehicleData['regNumber']}}</strong></p>
                       @endif
                        <p class="mb-0"><strong id="vehicleEnginecc">{{$vehicleData['engine']}}</strong></p>
                    </div>
                </div>
                @endif
                <!-- <div class="grand-totall">
                    <div class="title-wrap">
                        <h4 class="cart-bottom-title section-bg-gary-cart">Selected Service</h4>
                    </div>
                    <h5>MOT <span>£33.00</span></h5>
                    <h4 class="grand-totall-title">Grand Total <span>£33.00</span></h4>
                    <a href="#">Checkout</a>
                </div> -->
            </div>
        </div>





        <div class="row">
            <div class="col-md-12">

                <div class="">

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function () {
                const itemId = this.getAttribute('data-id');
                const itemType = this.getAttribute('data-type');
                const serviceName = this.getAttribute('data-name');
                const fitting_type = 'fully_fitted';
                const itemPrice = this.getAttribute('data-price');

                fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: itemId, type: itemType, qty: 1, serviceName: serviceName, fitting_type: fitting_type, itemPrice: itemPrice })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(data);
                            Swal.fire({
                                title: 'Product added to cart!',
                                text: 'Do you want to add more items or go to checkout?',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Go to Checkout',
                                cancelButtonText: 'Add More',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('checkout') }}";
                                }
                            });

                            // Update the cart count in the header (total quantity)
                            let newTotalQuantity = data.totalQuantity;
                            $('.count-style').text(newTotalQuantity); // Update the total quantity

                            // Update the total price dynamically in the header dropdown
                            let newTotalPrice = data.cartTotalPrice;
                            $('.shop-total').text('£' + newTotalPrice); // Update total price

                            // Dynamically generate the new cart items list
                            let newCartItems = '';
                            let newSubTotal = 0;
                            let newVatTotal = 0;
                            let newGrandTotal = 0;

                            for (const key in data.product) {
                                if (data.product.hasOwnProperty(key)) {
                                    const item = data.product[key];
                                    console.log(item);
                                    const itemTotalPrice = (item.price * item.quantity).toFixed(2); // Calculate total price for the item
                                    const itemVAT = item.tax_class_id == 9 ? itemTotalPrice * 0.20 : 0; // Calculate VAT for the item

                                    newSubTotal += parseFloat(itemTotalPrice);
                                    newVatTotal += parseFloat(itemVAT);
                                    newGrandTotal += parseFloat(itemTotalPrice) + parseFloat(itemVAT);

                                    newCartItems += `
                                        <li class="single-shopping-cart" id="cart-item-${item.id}">
                                            <div class="shopping-cart-title">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <h4>${item.model}</h4>
                                                    <h6>£${itemTotalPrice}</h6> <!-- Display total price for the item -->
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="item_width">
                                                        ${item.desc ? `<h6>${item.desc}</h6>` : ''}
                                                    </div>
                                                    <h4 class="quantity">Qty: ${item.quantity}</h4>
                                                </div>
                                            </div>
                                            <div class="shopping-cart-delete">
                                                <a class="delete-item" href="javascript:void(0);" data-id="${item.id}">
                                                    <i class="fa fa-times-circle"></i>
                                                </a>
                                            </div>
                                        </li>
                                    `;
                                }
                            }

                            // Replace the entire cart list with the updated items
                            $('#cart-items-list').html(newCartItems);

                            // Update the totals
                            $('#sub-total').text('£' + newSubTotal.toFixed(2));
                            $('#vat-total').text('£' + newVatTotal.toFixed(2));
                            $('#grand-total').text('£' + newGrandTotal.toFixed(2));
                        } else {
                            alert(data.message || 'Failed to add the product.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });
    });
</script>