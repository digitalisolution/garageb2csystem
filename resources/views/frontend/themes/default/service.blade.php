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

<div class="pt-70 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="service_bank">
                    @foreach ($services as $service)
                        <div class="service_card">
                            @if($service->cost_price > 0 && $service->price_type == 'fixed-price') 
                                <span class="cost_price"> Cost: &pound;{{ $service->tax_class_id == 9 ? number_format($service->cost_price * 1.20, 2) : number_format($service->cost_price, 2) }} </span>
                            @elseif($service->price_type == 'free')
                                <span class="cost_price">Free</span>
                            @endif

                            <div class="row align-items-center">
                            @php
                                $imageName = $service->image;
                                $innerimageName = $service->inner_image;

                                $customImage = $imageName ? public_path($customImagePath . $imageName) : null;
                                $defaultImage = $imageName ? public_path($defaultImagePath . $imageName) : null;
                                $fallbackNoImage = public_path($defaultImagePath . 'no-image.png');

                                $custominnerImage = $innerimageName ? public_path($custominnerImagePath . $innerimageName) : null;
                                $defaultinnerImage = $innerimageName ? public_path($defaultinnerImagePath . $innerimageName) : null;
                                $fallbackNoinnerImage = public_path($defaultinnerImagePath . 'no-image.png');

                                if ($imageName && file_exists($customImage)) {
                                    $finalImage = versioned_asset($customImagePath . $imageName);
                                } elseif ($imageName && file_exists($defaultImage)) {
                                    $finalImage = versioned_asset($defaultImagePath . $imageName);
                                } else {
                                    $finalImage = versioned_asset($defaultImagePath . 'no-image.png');
                                }

                                if ($innerimageName && file_exists($custominnerImage)) {
                                    $finalinnerImage = versioned_asset($custominnerImagePath . $innerimageName);
                                } elseif ($innerimageName && file_exists($defaultinnerImage)) {
                                    $finalinnerImage = versioned_asset($defaultinnerImagePath . $innerimageName);
                                } else {
                                    $finalinnerImage = versioned_asset($defaultinnerImagePath . 'no-image.png');
                                }
                            @endphp
                            <div class="col-lg-4 col-md-5 col-12">
                                @if($service->inner_image)
                                <img src="{{ $finalinnerImage }}" alt="{{ $service->name }}" class="img-bank" onerror="this.onerror=null; this.src='{{ versioned_asset('frontend/themes/default/img/service/no-image.png') }}';">
                                @elseif($service->image)
                                    <img src="{{ $finalImage }}" alt="{{ $service->name }}" class="img-bank" onerror="this.onerror=null; this.src='{{ versioned_asset('frontend/themes/default/img/service/no-image.png') }}';">
                                @else
                                <img src="{{ versioned_asset('frontend/themes/default/img/service/no-image.png') }}" alt="{{ $service->name }}" class="img-bank">
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
                                        @if($service->cost_price > 0 && $service->price_type == 'fixed-price')
                                                <a href="javascript:void(0);" class="btn btn-theme-select add-to-cart"
                                                data-id="{{ $service->service_id }}" data-name="{{ $service->name }}"
                                                data-price="{{ $service->cost_price }}" data-type="service">Add to Cart</a>
                                        @elseif($service->price_type == 'call-now')
                                            <a href="tel:{{ $garage->mobile }}" class="btn btn-theme-select">Call Now</a>
                                        @elseif($service->price_type == 'quote-now')
                                            <a href="javascript:void(0);" class="btn btn-theme-select btn-enquiry-modal" data-id="{{ $service->service_id }}" data-name="{{ $service->name }}">Quote Now</a>

                                        @elseif($service->cost_price = 0 && $service->price_type == 'free')
                                            <a href="javascript:void(0);" class="btn btn-theme-select add-to-cart" data-id="{{ $service->service_id }}" data-name="{{ $service->name }}" data-price="0" data-type="service">Add to Cart</a>
                                        @else
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
                <div class="vehicle_details-wrap p-4 rounded mb-4">
                    <h3 class="text-white mb-4">Your Vehicle</h3>
                    <div class="vehicle_bank" id="vehicleInfo">
                        <h4 class="text-uppercase"><strong id="vehicleReg"></strong></h4>
                        @if(isset($vehicleData['regNumber']) && !empty($vehicleData['regNumber']))
                        <p class="mb-0"><strong id="vehicleEngine" class="text-uppercase badge-dark rounded">{{$vehicleData['regNumber']}}</strong></p>
                       @endif
                        <p class="mb-0"><strong id="vehicleMake">{{$vehicleData['make']}}</strong></p>
                        <p class="mb-0"><strong id="vehicleModel">{{$vehicleData['model']}}</strong></p>
                        <p class="mb-0"><strong id="vehicleYear">{{$vehicleData['year']}}</strong></p>
                        <p class="mb-0"><strong id="vehicleEnginecc">{{$vehicleData['engine']}}</strong></p>
                    </div>
                </div>
                @endif
            </div>
        </div>



    </div>
</div>

@endsection
<!-- Enquiry Popup Modal -->
<div class="modal fade" id="quoteEnquiryModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <form id="quoteEnquiryForm" method="POST" action="{{ route('service.enquiry.submit') }}">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title" id="quoteModalLabel">Get a Quote</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <!-- Left Column (Vehicle Info) -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Vehicle Reg.No*</label>
                <input type="text" name="vehicle_reg" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Vehicle Mileage</label>
                <input type="text" name="mileage" class="form-control">
              </div>
              <div class="form-group">
                <label>First Name*</label>
                <input type="text" name="first_name" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control">
              </div>
              <div class="form-group">
                <label>Preferred Method of Contact</label>
                <select name="preferred_contact" class="form-control">
                  <option>By Email</option>
                  <option>By Phone</option>
                </select>
              </div>
              <div class="form-group">
                <label>E-mail Address*</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control">
              </div>
              <div class="form-group">
                <label>Address*</label>
                <input type="text" name="address" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Enter Postcode</label>
                <input type="text" name="postcode" class="form-control">
              </div>
              <div class="form-group">
                <label>Message</label>
                <textarea name="message" class="form-control" rows="3"></textarea>
              </div>
            </div>

            <!-- Right Column (Service Checkboxes) -->
            <div class="col-md-6">
              <label>Frequently Selected Services*</label>
              <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                @foreach($services as $s)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selected_services[]" value="{{ $s->name }}" id="service_{{ $s->service_id }}">
                    <label class="form-check-label" for="service_{{ $s->service_id }}">
                      {{ $s->name }}
                    </label>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Send Enquiry</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>

      </form>
    </div>
  </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-enquiry-modal').forEach(button => {
            button.addEventListener('click', function () {
                const serviceName = this.getAttribute('data-service');

                // Pre-select the service in the modal if needed
                document.querySelectorAll('#quoteEnquiryModal input[name="selected_services[]"]').forEach(input => {
                    input.checked = input.value === serviceName;
                });

                $('#quoteEnquiryModal').modal('show');
            });
        });
    });
</script>

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