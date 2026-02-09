@foreach ($recommendedTyres as $tyre)
    <div class="item product-wrap border-orange mb-15 flex-wrap">
        <div class="product-img p-3">
            <div class="recommended_ribbon"><img src="frontend/themes/default/img/icon-img/recommended_ribbon.webp" alt="recommended ribbon"></div>
            <div class="tyre_card row">
                @php
                    $cdnBase = config('cdn.tyre_cdn_url');
                    $localPath = 'frontend/themes/img/tyre_images/';
                    $imageName = $tyre->tyre_image ?? 'sample-tyre.png';
                    $localFullPath = public_path($localPath . $imageName);

                    if (!empty($imageName) && file_exists($localFullPath)) {
                    $imageUrl = asset($localPath . $imageName);
                    } elseif (!empty($imageName)) {
                    $imageUrl = $cdnBase . $imageName;
                    } else {
                    $imageUrl = asset('frontend/themes/default/img/product/'.$imageName);
                    }
                @endphp

                @php
                    $cdnLogoBase = config('cdn.brandlogo_cdn_url');
                    $localPath = 'frontend/themes/default/img/brand-logo/';
                    $Brandimage = $tyre->brand_logo ?? '';
                    $localFullPath = public_path($localPath . $Brandimage);
                    if (!empty($Brandimage) && file_exists($localFullPath)) {
                    $cdnLogoUrl = asset($localPath . $Brandimage);
                    } elseif (!empty($Brandimage)) {
                    $cdnLogoUrl = $cdnLogoBase . $Brandimage;
                    } else {
                    $cdnLogoUrl = asset('frontend/themes/default/img/brand-logo/no-image.png');
                    }
                    @endphp
                <div class="tyre-description">
                    <img src="{{ $cdnLogoUrl }}"alt="{{ $tyre->tyre_brand_name }}" class="default-img" width="auto" height="40" onerror="this.onerror=null;this.src='{{ asset('frontend/themes/default/img/brand-logo/no-image.png') }}';">
                    <p class="mb-0 tyre_model"><strong>{{ $tyre->tyre_model }}</strong></p>
                        <p class="mb-0 tyre_description">
                            {{ $tyre->tyre_width }}/{{ $tyre->tyre_profile }}R{{ $tyre->tyre_diameter }}
                            {{ $tyre->tyre_loadindex }}{{ $tyre->tyre_speed }}
                            @if($tyre->tyre_extraload) <b><img src="frontend/themes/default/img/icon-img/reinforced-xl-icon.webp" alt="Reinforced" loading="lazy" height="15"></b> @endif
                        </p>
                        <div class="vehicle_type_badges">
                    @if($tyre->vehicle_type)
                        <div class="badge">
                            <img src="frontend/themes/default/img/icon-img/{{$tyre->vehicle_type }}-icon.webp" alt="{{$tyre->vehicle_type }}" loading="lazy" height="20">
                        </div>
                        @endif
                        @if($tyre->tyre_season)
                            @php
                            $tyreTypeMapping = [
                                'S' => ['label' => 'Summer', 'badge' => 'pink'],
                                'Summer' => ['label' => 'Summer', 'badge' => 'purple'],
                                'W' => ['label' => 'Winter', 'badge' => 'purple'],
                                'Winter' => ['label' => 'Winter', 'badge' => 'purple'],
                                'A' => ['label' => 'All Season', 'badge' => 'purple'],
                                'All Season' => ['label' => 'All Season', 'badge' => 'purple'],
                            ];

                            $tyreType = $tyreTypeMapping[$tyre->tyre_season] ?? ['label' => ucfirst($tyre->tyre_season), 'badge' => 'purple'];
                            @endphp
                            <div class="badge">
                                <img src="frontend/themes/default/img/icon-img/{{ strtolower(str_replace(' ', '', $tyreType['label'])) }}-icon.webp" alt="{{ $tyreType['label'] }}" loading="lazy" height="25">
                            </div>
                        @endif
                        @if($tyre->tyre_runflat)
                            <div class="badge">
                                <img src="frontend/themes/default/img/icon-img/rft-icon.webp" alt="runflat" loading="lazy" height="15">
                            </div>
                        @endif
                        @if($tyre->budget_type)
                            <div class="badge">
                                <img src="frontend/themes/default/img/icon-img/{{ strtolower(str_replace(' ', '', $tyre->budget_type)) }}-icon.webp" alt="{{$tyre->budget_type}}" loading="lazy" height="15">
                            </div>
                        @endif
                    </div>
                </div>
                
                    <div class="d-flex gap-3 align-items-center justify-content-between mb-3">
                    <div class="tyre-picture">
                        <img src="{{ $imageUrl }}" alt="{{ $tyre->tyre_model }}" width="150" onerror="this.onerror=null;this.src='{{ asset('frontend/themes/default/img/product/'.$imageName) }}';">
                    </div>
                    
                    <div class="label_bank">
                        <div class="label_badge">
                            <div class="fuel_badge {{ $tyre->tyre_fuel }} tltp">
                                <img src="frontend/themes/default/img/icon-img/fuel_icon.png" height="16">
                                {{ $tyre->tyre_fuel }}
                                <div class="tooltip_hover">
                                    <h5>Fuel Rating</h5>
                                    Tyre fuel efficiency is rated between A and E. A rated tyres allow you to drive further with lower CO2 emissions, whilst an E rated tyre will emit more CO2 and offer a lower fuel efficiency.
                                </div>
                            </div>
                            <div class="wet_grip {{ $tyre->tyre_wetgrip }} tltp">
                                <img src="frontend/themes/default/img/icon-img/wet_grip_icon.png" height="16">
                                {{ $tyre->tyre_wetgrip }}
                                <div class="tooltip_hover">
                                    <h5>Wet Rating</h5>
                                    Wet grip is measured in classes A to E. A rated tyres offer better wet grip and short stopping distances where as an E rated tyre has less wet grip and longer stopping distances.
                                </div>
                            </div>
                            <div class="noise_level tltp">
                                <img src="frontend/themes/default/img/icon-img/noise_icon.png" height="16">
                                {{ $tyre->tyre_noisedb }}
                                <div class="tooltip_hover">
                                    <h5>Decibel Rating</h5>
                                    Noise levels of a tyre are measured in decibels ranging from class A to C. Low noise level tyres can range between 67 and 71 dB, where as high noise level tyres show 72 to 77dB.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <div class="">
            <div class="product-details-content">
                <div class="pro-details-quality m-0 mb-3">
                    <div class="product-price">
                            £{{ number_format($tyre->tax_class_id == 9 ? $tyre->tyre_fullyfitted_price * 1.2 : $tyre->tyre_fullyfitted_price, 2) }}
                            <div class="f-type">Fully Fitted</div>
                    </div>
                    <div class="cart-plus-minus">
                        <small>Quantity</small>
                        <div class="dec qtybutton">-</div>
                        <input class="cart-plus-minus-box" type="text" name="qtybutton" value="1" min="1">
                        <div class="inc qtybutton">+</div>
                    </div>
                </div>
                <?php
                $today = new DateTime();
                $tomorrow = new DateTime('tomorrow');
                $dateAvailable = new DateTime($tyre->date_available);
                $leadtime = $tyre->lead_time;
                // Add 1 day to the availability date
                $dateAvailable->modify('+1 day');

                if ($tyre->tyre_supplier_name === 'ownstock' && $tyre->instock === 1) {
                    echo '<div class="available-now w-100 rounded">We can fit Now</div>';
                } elseif (!empty($leadtime)) {
                if (is_array($leadtime)) {
                    echo '<div class="leadtime"> <i class="fa fa-calendar-check-o"></i> '.htmlspecialchars($leadtime['label']). '</div>';
                } else {
                    echo '<div class="leadtime"> <i class="fa fa-calendar-check-o"></i>We can fit: ' . htmlspecialchars($leadtime) . '</div>';
                }

                } else {
                    echo '<div class="dateAvailable"> <i class="fa fa-calendar-check-o"></i>We can fit: ' . $dateAvailable->format('d-m-Y') . '</div>';
                }  ?>
            </div>
            <div class="product-action mt-2">
                <div class="pro-same-action pro-cart">
                    <a id="add-to-cart" href="javascript:void(0);" class="btn-block"
                        data-id="{{ $tyre->product_id }}" data-qty="1">Add to booking</a>
                </div>
            </div>
            
        </div>
        </div>
    </div>
@endforeach

<!-- Modal -->
<div class="modal fade" id="tyreLabelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2">
                <div class="tlWrap">
                    <div class="tyreLabelOuter">
                        <div id="tyreDetailsWrap">
                            <div class="detailsRow">
                                <div id="supName"></div> <!-- brand -->
                            </div>
                            <div class="detailsRow">
                                <div id="tyreSize"></div> <!-- size -->
                            </div>
                        </div>

                        <div id="fuelwetWrap">
                            <div class="tyreRating" id="fuelRating">
                                <div class="scaleMarker" id="fuelMarker">B</div>
                            </div>
                            <div class="tyreRating" id="wetRating">
                                <div class="scaleMarker" id="wetMarker">A</div>
                            </div>
                        </div>

                        <div id="nsiWrap">
                            <div class="nsiItem" id="noise">
                                <div class="nField" id="nValue">70</div>
                                <div id="db">db</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.info_icon').on('click', function () {
            const brand = $(this).data('brand');
            const size = $(this).data('size');
            const fuel = $(this).data('fuel');
            const wet = $(this).data('wet');
            const noise = $(this).data('noise');

            $('#supName').text(brand);
            $('#tyreSize').text(size);

            $('#fuelMarker')
                .text(fuel)
                .removeClass().addClass('scaleMarker scale' + fuel);

            $('#wetMarker')
                .text(wet)
                .removeClass().addClass('scaleMarker scale' + wet);

            $('#nValue').text(noise);
        });
    });
</script>
<!-- Modal end -->