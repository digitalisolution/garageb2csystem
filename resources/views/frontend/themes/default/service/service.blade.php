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
<style type="text/css">
    .modal.right .modal-dialog {position: fixed;right: 0;margin: 0;top: 0;height: 100%;transform: translateX(100%);transition: transform 0.4s ease-in-out;overflow: auto;width: 100%;}
    .modal.right .modal-content {border: none;border-radius: 0;}
    .modal.right.show .modal-dialog {transform: translateX(0);}
</style>    
<div class="pt-30 pb-30">
        <div class="container">
<a onclick="openPanel()">Click me! I'm an arbitrary trigger</a>

<div id="backdrop" class="backdrop" onclick="closePanel()"></div>

<div id="panelWrap" class="panel-wrap">
  <div class="panel">
    <button class="close-btn" onclick="closePanel()">&times;</button>
    <h4 class="mb-3">Request an Estimate</h4>
    <div class="row">
   <!-- Left Column (Vehicle Info) -->
   <div class="col-md-7">
      <div class="row">
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <input type="text" name="vehicle_reg" value="AV07GVK" readonly="" class="service_reg form-control" required="">
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <input type="text" name="first_name" class="form-control" required="" placeholder="First Name*">
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <input type="text" name="last_name" class="form-control" placeholder="Last Name">
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <input type="email" name="email" class="form-control" required="" placeholder="Email*">
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <input type="text" name="phone" class="form-control" placeholder="Phone">
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <input type="text" name="address" class="form-control" required="" placeholder="Address*">
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <input type="text" name="postcode" class="form-control" required="" placeholder="Postcode*">
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <input type="text" name="city" class="form-control" required="" placeholder="City*">
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <select id="county" name="county" class="form-control" required="" placeholder="Conuty*">
                  <option value="">Select County</option>
               </select>
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-12">
            <div class="form-group">
               <select id="country" name="country" class="form-control" required="" placeholder="Country*">
                  <option value="">Select Country</option>
                  <option value="1" selected="">United Kingdom</option>
               </select>
            </div>
         </div>
         <div class="col-lg-12 col-md-12 col-12">
            <div class="form-group">
               <textarea name="message" class="form-control" rows="6"></textarea>
            </div>
         </div>
      </div>
   </div>
   <!-- Right Column (Service Checkboxes) -->
   <div class="col-md-5">
      <div class="quote_selected_services">
         <h4>Selected Services*</h4>
         <div class="quote_service-wrap">
            <label for="service_1" class="quote_service-wrap-error">
            Batteries Service
            <input type="checkbox" name="selected_services[]" value="1" id="service_1">
            </label>
            <label for="service_2" class="quote_service-wrap-error">
            Emergency Jump Start
            <input type="checkbox" name="selected_services[]" value="2" id="service_2">
            </label>
            <label for="service_3" class="quote_service-wrap-error">
            Mobile Tyre Fitting Charges
            <input type="checkbox" name="selected_services[]" value="3" id="service_3">
            </label>
            <label for="service_4" class="quote_service-wrap-error">
            Puncture Repair
            <input type="checkbox" name="selected_services[]" value="4" id="service_4">
            </label>
            <label for="service_5" class="quote_service-wrap-error">
            After Hour Charges
            <input type="checkbox" name="selected_services[]" value="5" id="service_5">
            </label>
            <label for="service_6" class="quote_service-wrap-error">
            Rim Repair
            <input type="checkbox" name="selected_services[]" value="6" id="service_6">
            </label>
            <label for="service_7" class="quote_service-wrap-error">
            Alloy wheel repairing charges
            <input type="checkbox" name="selected_services[]" value="7" id="service_7">
            </label>
            <label for="service_8" class="quote_service-wrap-error">
            Rim Cleaning
            <input type="checkbox" name="selected_services[]" value="8" id="service_8">
            </label>
            <label for="service_9" class="quote_service-wrap-error">
            Waste Tyres
            <input type="checkbox" name="selected_services[]" value="9" id="service_9">
            </label>
            <label for="service_10" class="quote_service-wrap-error">
            Wheel Bolts or Nuts
            <input type="checkbox" name="selected_services[]" value="10" id="service_10">
            </label>
            <label for="service_11" class="quote_service-wrap-error">
            Locking key removal
            <input type="checkbox" name="selected_services[]" value="11" id="service_11">
            </label>
            <label for="service_12" class="quote_service-wrap-error">
            Mot1
            <input type="checkbox" name="selected_services[]" value="12" id="service_12">
            </label>
            <label for="service_13" class="quote_service-wrap-error">
            Clutch Labour
            <input type="checkbox" name="selected_services[]" value="13" id="service_13">
            </label>
            <label for="service_14" class="quote_service-wrap-error">
            Tyre Swap
            <input type="checkbox" name="selected_services[]" value="14" id="service_14">
            </label>
            <label for="service_15" class="quote_service-wrap-error">
            24/7 Breakdown Services
            <input type="checkbox" name="selected_services[]" value="15" id="service_15">
            </label>
            <label for="service_17" class="quote_service-wrap-error">
            Sensor Valve
            <input type="checkbox" name="selected_services[]" value="17" id="service_17">
            </label>
            <label for="service_18" class="quote_service-wrap-error">
            Wheel Alignment
            <input type="checkbox" name="selected_services[]" value="18" id="service_18">
            </label>
            <label for="service_19" class="quote_service-wrap-error">
            Bead Sealer
            <input type="checkbox" name="selected_services[]" value="19" id="service_19">
            </label>
            <label for="service_20" class="quote_service-wrap-error">
            Tubeless Rubber Valve
            <input type="checkbox" name="selected_services[]" value="20" id="service_20">
            </label>
            <label for="service_21" class="quote_service-wrap-error">
            Standard Wheel Balance
            <input type="checkbox" name="selected_services[]" value="21" id="service_21">
            </label>
            <label for="service_22" class="quote_service-wrap-error">
            Casing Disposal Car &amp; Light Van
            <input type="checkbox" name="selected_services[]" value="22" id="service_22">
            </label>
            <label for="service_23" class="quote_service-wrap-error">
            High Pressure Valve
            <input type="checkbox" name="selected_services[]" value="23" id="service_23">
            </label>
            <label for="service_24" class="quote_service-wrap-error">
            TPMS SENSOR REPLACEMENT (SNAP-IN)
            <input type="checkbox" name="selected_services[]" value="24" id="service_24">
            </label>
            <label for="service_25" class="quote_service-wrap-error">
            Screen Wash
            <input type="checkbox" name="selected_services[]" value="25" id="service_25">
            </label>
            <label for="service_27" class="quote_service-wrap-error">
            name
            <input type="checkbox" name="selected_services[]" value="27" id="service_27">
            </label>
         </div>
      </div>
   </div>
   <div class="col-lg-12 col-md-12 col-12">
      <div class="text-center">
         <div class="g-recaptcha" data-sitekey="6Le4oGwqAAAAAOTRLzhxfQsiu_wTMRcuJvpQhxVK">
            <div style="width: 304px; height: 78px;">
               <div><iframe title="reCAPTCHA" width="304" height="78" role="presentation" name="a-gfehhrz0ljn" frameborder="0" scrolling="no" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation allow-modals allow-popups-to-escape-sandbox" src="https://www.google.com/recaptcha/api2/anchor?ar=1&amp;k=6Le4oGwqAAAAAOTRLzhxfQsiu_wTMRcuJvpQhxVK&amp;co=aHR0cHM6Ly93d3cuZGlnaXRhbGlkZWFzbHRkLmluOjQ0Mw..&amp;hl=en&amp;v=DBIsSQ0s2djD_akThoRUDeHa&amp;size=normal&amp;anchor-ms=20000&amp;execute-ms=15000&amp;cb=tjnboja4infj"></iframe></div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-12 col-md-12 col-12">
      <button type="submit" class="btn btn-theme border"><strong>Send Now</strong></button>
   </div>
</div>
  </div>
</div>

<style>
/* Backdrop styles */
.backdrop {position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: rgba(0,0,0,0.6);opacity: 0;pointer-events: none;transition: opacity 0.3s ease;z-index: 998;}
/* Panel wrapper */
.panel-wrap {position: fixed;top: 0;bottom: 0;right: 0;width:75%;transform: translateX(100%);transition: transform 0.3s ease;z-index: 999;}
/* Panel box */
.panel {background: #fff;color: #eee;height: 100%;overflow: auto;padding: 1.5em;position: relative;}
/* Close button */
.close-btn {position: absolute;top: 10px;right: 15px;font-size:45px;background: none;border: none;color: #333;cursor: pointer;}
/* Show panel and backdrop */
.show-panel {transform: translateX(0%);}
.show-backdrop {opacity: 1;pointer-events: auto;}
.panel-wrap .quote_service-wrap .quote_service-wrap-error{display:flex;gap:10px;align-items:center;color:#111;border: solid 2px #ddd;padding: 5px 10px;border-radius: 5px;flex-direction: row-reverse;justify-content:left;width:48%;line-height:20px;}
.panel-wrap .quote_service-wrap .quote_service-wrap-error input{min-width:20px;min-height:20px;}
/* Highlight the label background when checkbox is checked */
.quote_service-wrap-error:has(input:checked) {background-color: #fb8c00;border-color: #fb8c00 !important;color:#fff !important;}
.quote_service-wrap-error:hover {background-color: #f5f5f5;cursor: pointer;}
.panel-wrap .quote_service-wrap {display: flex;flex-wrap: wrap;gap: 10px;max-height: 430px;overflow-y: auto;scrollbar-width: thin;scrollbar-color: #888 #f1f1f1;}
/* WebKit (Chrome, Edge, Safari) */
.panel-wrap .quote_service-wrap::-webkit-scrollbar {width: 6px;}
.panel-wrap .quote_service-wrap::-webkit-scrollbar-track {background: #f1f1f1;}
.panel-wrap .quote_service-wrap::-webkit-scrollbar-thumb {background-color: #888;border-radius: 10px;}
.panel-wrap .quote_service-wrap::-webkit-scrollbar-thumb:hover {background: #555;}
/* Hide the default checkbox */
.quote_service-wrap-error input[type="checkbox"] {appearance: none;-webkit-appearance: none;width: 20px;height: 20px;border: 2px solid #ccc;border-radius: 4px;background-color: #f5f5f5;position: relative;cursor: pointer;transition: all 0.2s ease-in-out;}
/* When checked: white background, orange tick */
.quote_service-wrap-error input[type="checkbox"]:checked {background-color: #fff;border-color: #fff;}
/* The orange tick */
.quote_service-wrap-error input[type="checkbox"]:checked::after {content: "";position: absolute;left: 4px;top: 0px;width: 6px;height: 12px;border: solid orange;border-width: 0 2px 2px 0;transform: rotate(45deg);}
</style>

<script>
function openPanel() {
  document.getElementById("panelWrap").classList.add("show-panel");
  document.getElementById("backdrop").classList.add("show-backdrop");
}

function closePanel() {
  document.getElementById("panelWrap").classList.remove("show-panel");
  document.getElementById("backdrop").classList.remove("show-backdrop");
}
</script>

                @if($vehicleData)
                  <div id="vehicle_details-wrap p-3 rounded mb-4">
                     <div class="bg-light py-4 px-4 mb-3 border rounded">
                        <div class="your_vehicle_result d-flex" id="vehicleInfo">
                           @if(isset($vehicleData['regNumber']) && !empty($vehicleData['regNumber']))
                            <div class="vrm_plate d-flex align-items-center">
                                <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="uk icon" width="auto" height="35" loading="lazy">
                                <span class="ms-2 text-uppercase" id="vehicleEngine">{{$vehicleData['regNumber']}}</span>
                            </div>
                           @endif
                           <div id="brandImageContainer">
                              <img class="default-img" alt="{{$vehicleData['make']}}" height="50" src="{{ config('cdn.carbrands_cdn_url') }}{{ strtolower($vehicleData['make']) }}.webp">
                           </div>
                        </div>
                        <div class="your_vehicle_data mt-4 d-flex flex-wrap gap-3">
                           <div class="item">
                                Make
                                <span id="vehicleModel">{{$vehicleData['make']}}</span>
                            </div>
                            <div class="item">
                                Model
                                <span id="vehicleModel">{{$vehicleData['model']}}</span>
                            </div>
                            <div class="item">
                                Year
                                <span id="vehicleYear">{{$vehicleData['year']}}</span>
                            </div>
                            <div class="item">
                                Engine Capacity
                                <span id="vehicleEnginecc">{{$vehicleData['engine']}}</span>
                            </div>
                        </div>
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
                                        <div class="mt-4">
                                        @if($service->cost_price > 0 && $service->price_type == 'fixed-price')
                                            <span class="cost_price"> &pound;{{ $service->tax_class_id == 9 ? number_format($service->cost_price * 1.20, 2) : number_format($service->cost_price, 2) }}
                                            </span>
                                        @elseif($service->price_type == 'free')
                                            <span class="cost_price">Free</span>
                                        @endif

                                        @if($service->service_whats_include != '')
                                        <button type="button" class="whats_include-link" data-bs-toggle="modal" data-bs-target="#includeModal{{ $service->id }}"> What's Included </button>
                                        <div class="modal fade" id="includeModal{{ $service->id }}" tabindex="-1" aria-labelledby="includeModalLabel{{ $service->id }}" aria-hidden="true">

                                          <div class="modal-dialog modal-xl">
                                              <div class="modal-content">
                                                  <div class="modal-header">
                                                      <h4 class="modal-title" id="includeModalLabel{{ $service->id }}">What's Included</h4>
                                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>
                                                  <div class="modal-body">
                                                      {!! $service->service_whats_include ?? 'No data available.' !!}
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                    @endif
                                    </div>
                                        <p><i class="pe-7s-timer"></i> Work Time</p> 
                                        
                                        @if($service->service_features != '')
                                         {!! $service->service_features !!}
                                       @else
                                         <ul class="tick">
                                             <li>Genuine Spare Parts</li>
                                             <li>Expert Mechanics</li>
                                             <li>Comprehensive Inspection</li>
                                             <li>Transparent Pricing</li>
                                         </ul>
                                       @endif
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
