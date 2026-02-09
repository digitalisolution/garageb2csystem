@extends('samples')
@section('content')
                <div class="container-fluid">
                    <div class="bg-white p-3">
        <form id="shipping-form" action="{{ route('save.shipping.charges') }}" method="POST">
            @csrf

            <h5 class="mb-4">Shipping Charges</h5>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="shippingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="miles-tab" data-bs-toggle="tab" data-bs-target="#miles-pane"
                            type="button" role="tab" aria-controls="miles-pane" aria-selected="true"><h6 class="m-0"><strong>Charges by
                            Miles</strong></h6></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="postcode-tab" data-bs-toggle="tab" data-bs-target="#postcode-pane"
                            type="button" role="tab" aria-controls="postcode-pane" aria-selected="false"><h6 class="m-0"><strong>Charges by
                            Postcode</strong></h6></button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="shippingTabsContent">

                    <!-- Charges by Miles Tab -->
                    <div class="tab-pane fade show active" id="miles-pane" role="tabpanel" aria-labelledby="miles-tab">
                        <div id="miles-section">
                            @if (!empty($milesData))
                                @foreach ($milesData as $index => $mile)
                                <div class="mile-row shipping_charges_wrap">
                                <div class="item">
                                    <label>Miles Range:</label>
                                    <input type="text" class="form-control" name="miles[{{ $index }}][valueq]"
                                        value="{{ $mile['valueq'] }}" placeholder="Miles Range">
                                </div>
                                <div class="item">
                                    <label>Price:</label>
                                    <input type="text" class="form-control" name="miles[{{ $index }}][valuep]"
                                        value="{{ $mile['valuep'] }}" placeholder="Price">
                                </div>
                                <div class="item">
                                    <label>Ship Type:</label>
                                    <select class="form-control" name="miles[{{ $index }}][ship_type]">
                                        <option value="job" {{ $mile['ship_type'] === 'job' ? 'selected' : '' }}>Job</option>
                                        <option value="tyre" {{ $mile['ship_type'] === 'tyre' ? 'selected' : '' }}>Per
                                            Tyre</option>
                                    </select>
                                </div>
                                <div class="item">
                                    <label>Day:</label>
                                    <select class="form-control" name="miles[{{ $index }}][day]">
                                        <option value="">All Day</option>
                                        @php
                                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                        @endphp
                                        @foreach ($days as $day)
                                                                                                                                                                                                                                                <option value="{{ $day }}" {{ $mile['day'] === $day ? 'selected' : '' }}>
                                                {{ ucfirst($day) }}
                                            </option>
                                        @endforeach
                                                                                                                                                                                                                            </select>
                                </div>
                                <div class="item">
                                    <label>Callout From Time:</label>
                                    <select class="form-control" name="miles[{{ $index }}][callout_from_time]">
                                        @for ($i = 0; $i <= 23; $i++)
                                            @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                                                                                                                                                                                                                <option value="{{ $hour }}" {{ $mile['callout_from_time'] == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="item">
                                    <label>Callout To Time:</label>
                                    <select class="form-control" name="miles[{{ $index }}][callout_to_time]">
                                        @for ($i = 0; $i <= 23; $i++)
                                            @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                                                                                                                                                                                                                <option value="{{ $hour }}" {{ $mile['callout_to_time'] == $hour ? 'selected' : '' }}>
                                                {{ $hour }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="item">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm remove-mile btn-block">Remove</button>
                                </div>
                            </div>
                                @endforeach
                            @endif
                            <button type="button" id="add-mile" class="btn btn-primary btn-sm">Add Row</button>
                        </div>
                    </div>

                    <!-- Charges by Postcode Tab -->
                    <div class="tab-pane fade" id="postcode-pane" role="tabpanel" aria-labelledby="postcode-tab">
                        <div id="postcode-section">
                            @if (!empty($postcodeData))
                                                @foreach ($postcodeData as $index => $postcode)
                                                                    <div class="postcode-row shipping_charges_wrap">
                                                                        <div class="item">
                                                                            <label>Postcode:</label>
                                                                            <input type="text" class="form-control" name="postcodes[{{ $index }}][post_code]"
                                                                                value="{{ $postcode['post_code'] }}" placeholder="Postcode">
                                                                        </div>
                                                                        <div class="item">
                                                                            <label>Price:</label>
                                                                            <input type="text" class="form-control" name="postcodes[{{ $index }}][price]"
                                                                                value="{{ $postcode['price'] }}" placeholder="Price">
                                                                        </div>
                                                                        <div class="item">
                                                                            <label>Ship Type:</label>
                                                                            <select class="form-control" name="postcodes[{{ $index }}][ship_type]">
                                                                                <option value="job" {{ $postcode['ship_type'] === 'job' ? 'selected' : '' }}>Job
                                                                                </option>
                                                                                <option value="tyre" {{ $postcode['ship_type'] === 'tyre' ? 'selected' : '' }}>Per
                                                                                    Tyre</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="item">
                                                                            <label>Day:</label>
                                                                            <select class="form-control" name="postcodes[{{ $index }}][day]">
                                                                                <option value="">All Day</option>
                                                                                @php
                                                                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                                                                @endphp
                                                                                @foreach ($days as $day)
                                                                                                                                                                                                                                                                                        <option value="{{ $day }}" {{ $postcode['day'] === $day ? 'selected' : '' }}>
                                                                                        {{ ucfirst($day) }}
                                                                                    </option>
                                                                                @endforeach
                                                                                                                                                                                                                                                                    </select>
                                                                        </div>
                                                                        <div class="item">
                                                                            <label>Callout From Time:</label>
                                                                            <select class="form-control" name="postcodes[{{ $index }}][callout_from_time]">
                                                                                @for ($i = 0; $i <= 23; $i++)
                                                                                    @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                                                                                                                                                                                                                                                        <option value="{{ $hour }}" {{ $postcode['callout_from_time'] == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                                                                                @endfor
                                                                            </select>
                                                                        </div>
                                                                        <div class="item">
                                                                            <label>Callout To Time:</label>
                                                                            <select class="form-control" name="postcodes[{{ $index }}][callout_to_time]">
                                                                                @for ($i = 0; $i <= 23; $i++)
                                                                                    @php $hour = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                                                                                                                                                                                                                                                                        <option value="{{ $hour }}" {{ $postcode['callout_to_time'] == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                                                                                @endfor
                                                                            </select>
                                                                        </div>
                                                                        <div class="item text-end">
                                                                            <label>&nbsp;</label>
                                                                            <button type="button" class="btn btn-danger btn-sm remove-postcode btn-block">Remove</button>
                                                                        </div>
                                                                    </div>
                                                @endforeach
                            @endif




                            <button type="button" id="add-postcode" class="btn btn-primary btn-sm">Add Row</button>
                        </div>
                    </div>
                    
                </div>
                <div class="row mt-4">
                <!-- Shipping Charge Type -->
                <div class="col-md-6">
                    <h5>Shipping Charge Type</h5>
                    <div class="border p-3 rounded bg-light">
                    
                    <div class="radio_wrap">
                        <input type="radio" name="charge_type" value="1" id="chargeTypeMile" {{ $selectedChargeType == 1 ? 'checked' : '' }}>
                        <label for="chargeTypeMile">By Mile</label>
                    </div>
                    <div class="radio_wrap">
                        <input type="radio" name="charge_type" value="2" id="chargeTypePostcode" {{ $selectedChargeType == 2 ? 'checked' : '' }}>
                        <label for="chargeTypePostcode">By Postcode</label>
                    </div>
                </div>
            </div>

                <!-- VAT Settings -->
                <div class="col-md-6">
                    <h5>VAT</h5>
                    <div class="border p-3 rounded bg-light">
                    
                    <div class="radio_wrap">
                        <input type="radio" name="vat" value="9" id="vat20" {{ $shippingbyproduct_tax == 9 ? 'checked' : '' }}>
                        <label for="vat20">20% VAT</label>
                    </div>
                    <div class="radio_wrap">
                        <input type="radio" name="vat" value="0" id="vat0" {{ $shippingbyproduct_tax == 0 ? 'checked' : '' }}>
                        <label for="vat0">No VAT</label>
                    </div>
                </div>
            </div>
                </div>
                <!-- Save Button -->
                <div class="mt-4 text-right">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
        </form>
</div>
    </div>
    <script>
        // Add Row for Miles
        document.getElementById('add-mile').addEventListener('click', function () {
            const milesSection = document.getElementById('miles-section');
            const newRow = document.createElement('div');
            newRow.classList.add('mile-row', 'shipping_charges_wrap');
            newRow.innerHTML = `
                                    <div class="item">
                                        <label>Miles Range:</label>
                                        <input type="text" class="form-control" name="miles[new][valueq]" placeholder="Miles Range">
                                    </div>
                                    <div class="item">
                                        <label>Price:</label>
                                        <input type="text" class="form-control" name="miles[new][valuep]" placeholder="Price">
                                    </div>
                                    <div class="item">
                                        <label>Ship Type:</label>
                                        <select class="form-control" name="miles[new][ship_type]">
                                            <option value="job">Job</option>
                                            <option value="tyre">Per Tyre</option>
                                        </select>
                                    </div>
                                    <div class="item">
                                        <label>Day:</label>
                                        <select class="form-control" name="miles[new][day]">
                                            <option value="">All Day</option>
                                            <option value="monday">Monday</option>
                                            <option value="tuesday">Tuesday</option>
                                            <option value="wednesday">Wednesday</option>
                                            <option value="thursday">Thursday</option>
                                            <option value="friday">Friday</option>
                                            <option value="saturday">Saturday</option>
                                            <option value="sunday">Sunday</option>
                                        </select>
                                    </div>
                                    <div class="item">
                                        <label>Callout From Time:</label>
                                        <select class="form-control" name="miles[new][callout_from_time]">
                                            ${Array.from({ length: 24 }, (_, i) => `<option value="${String(i).padStart(2, '0')}">${String(i).padStart(2, '0')}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="item">
                                        <label>Callout To Time:</label>
                                        <select class="form-control" name="miles[new][callout_to_time]">
                                            ${Array.from({ length: 24 }, (_, i) => `<option value="${String(i).padStart(2, '0')}">${String(i).padStart(2, '0')}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="item text-end">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm remove-mile btn-block">Remove</button>
                                    </div>
                                `;
            milesSection.appendChild(newRow);
        });

        // Remove Row for Miles
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-mile')) {
                e.target.closest('.mile-row').remove();
            }
        });

        // Add Row for Postcodes
        document.getElementById('add-postcode').addEventListener('click', function () {
            const postcodeSection = document.getElementById('postcode-section');
            const newRow = document.createElement('div');
            newRow.classList.add('postcode-row' , 'shipping_charges_wrap');
            newRow.innerHTML = `
                                <div class="item">
                                    <label>Postcode:</label>
                                    <input type="text" class="form-control" name="postcodes[new][post_code]" placeholder="Postcode">
                                </div>
                                <div class="item">
                                    <label>Price:</label>
                                    <input type="text" class="form-control" name="postcodes[new][price]" placeholder="Price">
                                </div>
                                <div class="item">
                                    <label>Ship Type:</label>
                                    <select class="form-control" name="postcodes[new][ship_type]">
                                        <option value="job">Job</option>
                                        <option value="tyre">Per Tyre</option>
                                    </select>
                                </div>
                                <div class="item">
                                    <label>Day:</label>
                                    <select class="form-control" name="postcodes[new][day]">
                                        <option value="">All Day</option>
                                        <option value="monday">Monday</option>
                                        <option value="tuesday">Tuesday</option>
                                        <option value="wednesday">Wednesday</option>
                                        <option value="thursday">Thursday</option>
                                        <option value="friday">Friday</option>
                                        <option value="saturday">Saturday</option>
                                        <option value="sunday">Sunday</option>
                                    </select>
                                </div>
                                <div class="item">
                                    <label>Callout From Time:</label>
                                    <select class="form-control" name="postcodes[new][callout_from_time]">
                                        ${Array.from({ length: 24 }, (_, i) => `<option value="${String(i).padStart(2, '0')}">${String(i).padStart(2, '0')}</option>`).join('')}
                                    </select>
                                </div>
                                <div class="item">
                                    <label>Callout To Time:</label>
                                    <select class="form-control" name="postcodes[new][callout_to_time]">
                                        ${Array.from({ length: 24 }, (_, i) => `<option value="${String(i).padStart(2, '0')}">${String(i).padStart(2, '0')}</option>`).join('')}
                                    </select>
                                </div>
                                <div class="item text-end">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm remove-postcode btn-block">Remove</button>
                                </div>
                            `;
            postcodeSection.appendChild(newRow);
        });

        // Remove Row for Postcodes
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-postcode')) {
                e.target.closest('.postcode-row').remove();
            }
        });
    </script>

@endsection