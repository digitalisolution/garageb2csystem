@extends('layouts.app')
@section('content')
    @php
        $domain = str_replace(['http://', 'https://'], '', request()->getHost());
        $customImagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service_icon/';
        $defaultImagePath = 'frontend/themes/default/img/service/';

        $custominnerImagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-inner-img/';
        $defaultinnerImagePath = 'frontend/themes/default/img/service-inner-img/';
    @endphp
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
<div class="pt-30 pb-30">
        <div class="container">

                @if($vehicleData)
                        <div class="vehicle_details-wrap p-3 rounded mb-4">
                            <h4 class="text-white">Your Vehicle</h4>
                            <div class="vehicle_bank" id="vehicleInfo">
                                
                                @if(isset($vehicleData['regNumber']) && !empty($vehicleData['regNumber']))
                                    <div class="service_reg"><span id="vehicleEngine">{{$vehicleData['regNumber']}}</span></div>
                                @endif
                                <p class="mb-0"><strong id="vehicleMake">{{$vehicleData['make']}}</strong> </p>
                                <p class="mb-0"><strong id="vehicleModel">{{$vehicleData['model']}}</strong> </p>
                                <p class="mb-0"><strong id="vehicleYear">{{$vehicleData['year']}}</strong> </p>
                                <p class="mb-0"><strong id="vehicleEnginecc">{{$vehicleData['engine']}}</strong></p>

                            </div>
                        </div>
                @endif
                    <div class="service_bank">
                        @foreach ($services as $service)
                            <div class="service_card">
                                

                                    @php
                                        $imageName = $service->image;
                                        $innerimageName = $service->inner_image;

                                        $customImage = $imageName ? public_path($customImagePath . $imageName) : null;
                                        $defaultImage = $imageName ? public_path($defaultImagePath . $imageName) : null;
                                        $fallbackNoImage = public_path($defaultImagePath . 'no-image.webp');

                                        $custominnerImage = $innerimageName ? public_path($custominnerImagePath . $innerimageName) : null;
                                        $defaultinnerImage = $innerimageName ? public_path($defaultinnerImagePath . $innerimageName) : null;
                                        $fallbackNoinnerImage = public_path($defaultinnerImagePath . 'no-image.webp');

                                        if ($imageName && file_exists($customImage)) {
                                            $finalImage = versioned_asset($customImagePath . $imageName);
                                        } elseif ($imageName && file_exists($defaultImage)) {
                                            $finalImage = versioned_asset($defaultImagePath . $imageName);
                                        } else {
                                            $finalImage = versioned_asset($defaultImagePath . 'no-image.webp');
                                        }

                                        if ($innerimageName && file_exists($custominnerImage)) {
                                            $finalinnerImage = versioned_asset($custominnerImagePath . $innerimageName);
                                        } elseif ($innerimageName && file_exists($defaultinnerImage)) {
                                            $finalinnerImage = versioned_asset($defaultinnerImagePath . $innerimageName);
                                        } else {
                                            $finalinnerImage = versioned_asset($defaultinnerImagePath . 'no-image.webp');
                                        }
                                    @endphp
                                        @if($service->inner_image)
                                            <img src="{{ $finalinnerImage }}" alt="{{ $service->name }}" class="service_image"
                                                onerror="this.onerror=null; this.src='{{ versioned_asset('frontend/themes/default/img/service/no-image.webp') }}';">
                                        @elseif($service->image)
                                            <img src="{{ $finalImage }}" alt="{{ $service->name }}" class="service_image"
                                                onerror="this.onerror=null; this.src='{{ versioned_asset('frontend/themes/default/img/service/no-image.webp') }}';">
                                        @else
                                            <img src="{{ versioned_asset('frontend/themes/default/img/service/no-image.webp') }}"
                                                alt="{{ $service->name }}" class="service_image">
                                        @endif

                                        <h4>{{ $service->name }}</h4>
                                        @if($service->cost_price > 0 && $service->price_type == 'fixed-price')
                                            <span class="cost_price"> &pound;{{ $service->tax_class_id == 9 ? number_format($service->cost_price * 1.20, 2) : number_format($service->cost_price, 2) }}
                                            </span>
                                        @elseif($service->price_type == 'free')
                                            <span class="cost_price">Free</span>
                                        @endif
                                        <p><i class="pe-7s-timer"></i> Work Time</p>
                                        <ul class="tick">
                                            <li>Genuine Spare Parts</li>
                                            <li>Expert Mechanics</li>
                                            <li>Comprehensive Inspection</li>
                                            <li>Transparent Pricing</li>
                                        </ul>
                                        <div class="mt-4">
                                            <a href="{{ route('slug.handle', $service->slug) }}" class="btn btn-dark">View
                                                Details</a>
                                            @if($service->cost_price > 0 && $service->price_type == 'fixed-price')
                                                <a href="javascript:void(0);" class="btn btn-theme-select add-to-cart"
                                                    data-id="{{ $service->service_id }}" data-name="{{ $service->name }}"
                                                    data-price="{{ $service->cost_price }}" data-type="service">Add to Cart</a>
                                            @elseif($service->price_type == 'call-now')
                                                <a href="tel:{{ $garage->mobile }}" class="btn btn-theme-select">Call Now</a>
                                            @elseif($service->price_type == 'quote-now')
                                                <a href="javascript:void(0);" class="btn btn-theme-select btn-enquiry-modal"
                                                    data-id="{{ $service->service_id }}" data-name="{{ $service->name }}" data-bs-target="#staticBackdrop">Quote
                                                    Now</a>
                                                     @include('service/quote-form')
                                            @elseif($service->cost_price = 0 && $service->price_type == 'free')
                                                <a href="javascript:void(0);" class="btn btn-theme-select add-to-cart"
                                                    data-id="{{ $service->service_id }}" data-name="{{ $service->name }}"
                                                    data-price="0" data-type="service">Add to Cart</a>
                                            @else
                                                <a href="tel:{{ $garage->mobile }}" class="btn btn-theme-select">Call Now</a>
                                            @endif
                                        </div>

                            </div>
                        @endforeach
                    </div>
        </div>
    </div>


<style type="text/css">
    .modal.right .modal-dialog {position: fixed;right: 0;margin: 0;top: 0;height: 100%;transform: translateX(100%);transition: transform 0.4s ease-in-out;max-width: 60%;overflow: auto;width: 100%;}
    .modal.right .modal-content {border: none;border-radius: 0;}
    .modal.right.show .modal-dialog {transform: translateX(0);}
</style> 
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
                            // console.log(data);
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
                                } else {
                                    location.reload();
                                }
                            });

                            let newTotalQuantity = data.totalQuantity;
                            $('.count-style').text(newTotalQuantity);

                            let newTotalPrice = data.cartTotalPrice;
                            $('.shop-total').text('£' + newTotalPrice);

                            let newCartItems = '';
                            let newSubTotal = 0;
                            let newVatTotal = 0;
                            let newGrandTotal = 0;

                            for (const key in data.product) {
                                if (data.product.hasOwnProperty(key)) {
                                    const item = data.product[key];
                                    // console.log(item);
                                    const itemTotalPrice = (item.price * item.quantity).toFixed(2);
                                    const itemVAT = item.tax_class_id == 9 ? itemTotalPrice * 0.20 : 0;

                                    newSubTotal += parseFloat(itemTotalPrice);
                                    newVatTotal += parseFloat(itemVAT);
                                    newGrandTotal += parseFloat(itemTotalPrice) + parseFloat(itemVAT);

                                    newCartItems += `
                                        <li class="single-shopping-cart" id="cart-item-${item.id}">
                                            <div class="shopping-cart-title">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <h4>${item.model}</h4>
                                                    <h6>£${itemTotalPrice}</h6>
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

                            $('#cart-items-list').html(newCartItems);

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
@endsection