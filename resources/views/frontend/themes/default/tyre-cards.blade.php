<div class="tyrelist_repeater">
    @foreach ($tyres as $tyre)
                            <div class="item product-wrap">
                                @if($tyre->tyre_supplier_name == 'bits')
                                <div class="fitting_tag">
                                    We Can Fit: <strong>Today</strong> in <strong>1</strong> hour<br>
                                    Last order by <strong>05:30</strong> PM
                                 </div>
                                 @endif
                                <div class="product-img p-3">
                                    @if($tyre->brand && $tyre->brand->promoted_text)
                                        <span class="pink">{{ $tyre->brand->promoted_text }}</span>
                                    @endif
                                    <div class="tyre_card">
                                    <div class="tyre-picture"><img class="default-img"
                                            src="{{ $tyre->tyre_image ?? 'frontend/themes/default/img/product/sample-tyre.png' }}"
                                            alt="{{ $tyre->tyre_model }}"></div>
                                        <div class="tyre-description mt-1">
                                            @if($tyre->brand && $tyre->brand->image)
                                                <img class="default-img" src="frontend/themes/default/img/brand-logo/{{ $tyre->brand->image }}"
                                                    alt="{{ $tyre->brand->brand_name }}" width="auto" height="50">
                                            @endif
                                            <p class="mb-0 tyre_model"><strong>{{ $tyre->tyre_model }}</strong></p>
                                            <p class="mb-0 tyre_description">
                                                {{ $tyre->tyre_width }}/{{ $tyre->tyre_profile }}R{{ $tyre->tyre_diameter }}
                                                {{ $tyre->tyre_loadindex }}{{ $tyre->tyre_speed }}
                                                @if($tyre->tyre_extraload) <b>Extraload</b> @endif
                                            </p>
                                            <div class="vehicle_type_badges">
                                                @if($tyre->vehicle_type)
                                                    <div class="badge  {{$tyre->vehicle_type }}">
                                                        {{$tyre->vehicle_type }}
                                                    </div>
                                                @endif
                                                @if($tyre->tyre_season)
                                                                        @php
            // Map abbreviations and full names to a unified format
            $tyreTypeMapping = [
                'Summer' => ['label' => 'Summer', 'badge' => 'pink'],
                'S' => ['label' => 'Summer', 'badge' => 'pink'],
                'Winter' => ['label' => 'Winter', 'badge' => 'purple'],
                'W' => ['label' => 'Winter', 'badge' => 'purple'],
                'All Season' => ['label' => 'All Season', 'badge' => 'purple'],
                'A' => ['label' => 'All Season', 'badge' => 'purple'],
            ];

            // Default fallback for unknown types
            $tyreType = $tyreTypeMapping[$tyre->tyre_season] ?? ['label' => ucfirst($tyre->tyre_season), 'badge' => 'purple'];
                                                                        @endphp

                                                                        <div class="badge {{ str_replace(' ', '', $tyreType['label']) }}">
                                                                            {{ $tyreType['label'] }}
                                                                        </div>

                                                @endif

                                                @if($tyre->tyre_runflat)
                                                    <div class="badge runflat">
                                                        Runflat
                                                    </div>
                                                @endif
                                                @if($tyre->brand && $tyre->brand->budget_type)
                                                    <div class="badge {{$tyre->brand->budget_type}}">
                                                        {{$tyre->brand->budget_type}}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="label_bank">
                                            <div class="label_badge">
                                                <div class="fuel_badge {{ $tyre->tyre_fuel }} tltp">
                                                    <img src="frontend/themes/default/img/icon-img/fuel_icon.png" height="14"
                                                        alt="Fuel Icon">
                                                    {{ $tyre->tyre_fuel }}
                                                    <div class="tooltip_hover">
                                                        <h5>Fuel Rating</h5>
                                                        Tyre fuel efficiency is rated between A and E. A rated tyres allow you to drive further with lower CO2 emissions, whilst an E rated tyre will emit more CO2 and offer a lower fuel efficiency.
                                                    </div>
                                                </div>
                                                <div class="wet_grip {{ $tyre->tyre_wetgrip }} tltp">
                                                    <img src="frontend/themes/default/img/icon-img/wet_grip_icon.png" height="14"
                                                        alt="Wet Grip">
                                                    {{ $tyre->tyre_wetgrip }}
                                                    <div class="tooltip_hover">
                                                        <h5>Wet Rating</h5>
                                                        Wet grip is measured in classes A to E. A rated tyres offer better wet grip and short stopping distances where as an E rated tyre has less wet grip and longer stopping distances.
                                                    </div>
                                                </div>
                                                <div class="noise_level tltp">
                                                    <img src="frontend/themes/default/img/icon-img/noise_icon.png" height="14"
                                                        alt="Noise Icon">
                                                    {{ $tyre->tyre_noisedb }}
                                                    <div class="tooltip_hover">
                                                        <h5>Decibel Rating</h5>
                                                        Noise levels of a tyre are measured in decibels ranging from class A to C. Low noise level tyres can range between 67 and 71 dB, where as high noise level tyres show 72 to 77dB.
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="#" class="fa fa-info-circle info_icon" data-bs-toggle="modal" data-bs-target="#exampleModal"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-action">
                                        <div class="pro-same-action pro-cart">
                                            <a id="add-to-cart" href="javascript:void(0);" data-type="tyre" class="btn-block"
                                                data-id="{{ $tyre->product_id }}" data-qty="1"><i class="pe-7s-cart"></i> Add to
                                                cart</a>
                                        </div>
                                    </div>
                                    <span class="text-danger qty-error" style="display: none;">Insufficient stock.</span>
                                </div>
                                <div class="product-content text-center bg-gray pt-4 mt-0">
                                    <div class="product-details-content">
                                        <div class="pro-details-quality m-0 mb-3">
                                            <div class="cart-plus-minus">
                                                <div class="dec qtybutton">-</div>
                                                <input class="cart-plus-minus-box" type="text" name="qtybutton" value="1">
                                                <div class="inc qtybutton">+</div>
                                            </div>

                                            <div class="product-price">
                                                <span>
                                                    £{{ number_format($tyre['tax_class_id'] == 9 ? $tyre['tyre_fullyfitted_price'] * 1.2 : $tyre['tyre_fullyfitted_price'], 2) }}
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="text-right d-flex justify-content-end">
                                        @php
    $today = new DateTime();
    $tomorrow = new DateTime('tomorrow');
    $dateAvailable = new DateTime($tyre->date_available);
    $leadtime = $tyre->lead_time;

    // Get the correct lead time from the array using tyre ID
    $Available_leadTime = $leadTimes[$tyre->product_id] ?? null;

    // Add 1 day to the availability date
    $dateAvailable->modify('+1 day');

    // if ($tyre->tyre_supplier_name === 'bits') {
    //     echo '<div class="text-center text-danger"><b>Except Sat & Sun</b></div>';
    // }
    @endphp

    @if ($tyre->tyre_supplier_name === 'ownstock' && $tyre->instock === 1)
    <div class="available-now">Available Now</div>
    @elseif(isset($leadtime))
    <div class="leadtime">{{$leadtime}}</div>
    @elseif(isset($leadTimes[$tyre->product_id]))
    <div class="leadtime">{{ $leadTimes[$tyre->product_id]['label'] }}</div>
    @else 
        <div class="dateAvailable">Available On {{$dateAvailable->format('d-m-Y') }}</div>
    @endif
                                    </div>

                                </div>
                            </div>

    @endforeach
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2">
                <img src="frontend/themes/default/img/D_D_71.png" class="img-adjust">
            </div>
        </div>
    </div>
</div>
<!-- Modal end -->
<script>
     var CartPlusMinus = $('.cart-plus-minus');
    CartPlusMinus.prepend('<div class="dec qtybutton">-</div>');
    CartPlusMinus.append('<div class="inc qtybutton">+</div>');
    $(".qtybutton").on("click", function() {
        var $button = $(this);
        var oldValue = $button.parent().find("input").val();
        if ($button.text() === "+") {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            // Don't allow decrementing below zero
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 1;
            }
        }
        $button.parent().find("input").val(newVal);
    });
 </script>