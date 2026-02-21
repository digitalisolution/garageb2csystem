<div class="modal fade" id="addTyreModal" tabindex="-1" aria-labelledby="addTyreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTyreModalLabel">Add New Tyre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="bg-white p-3">
                <h5>{{ isset($tyre) ? 'Edit' : 'Add' }} Tyre Product</h5>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ isset($tyre) 
                    ? route('AutoCare.tyres.update', ['product_id' => $tyre->product_id]) . '?' . http_build_query($queryParams ?? [])
                    : route('AutoCare.tyres.store') }}" 
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($tyre))
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="ean">EAN:</label>
                            <input type="text" name="ean" id="ean" class="form-control"
                                value="{{ isset($tyre) ? $tyre->ean : old('ean') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="instock">Instock:</label>
                            <select name="instock" id="instock" class="form-control">
                                <option value="1" {{ isset($tyre) && $tyre->instock == 1 ? 'selected' : '' }}>yes</option>
                                <option value="0" {{ isset($tyre) && $tyre->instock == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="sku">SKU:</label>
                            <input type="text" name="sku" id="sku" class="form-control"
                                value="{{ isset($tyre) ? $tyre->sku : old('sku') }}" required>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="lead_time">Availability:</label>
                            <input type="text" name="lead_time" id="lead_time" class="form-control"
                                value="{{ isset($tyre) ? $tyre->lead_time : old('lead_time') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="brand_id">Select Brand:</label>
                            <select name="brand_id" id="brand" class="form-control">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->brand_id }}" {{ isset($tyre) && $tyre->tyre_brand_id ==
                                $brand->brand_id ? 'selected' : '' }}>{{ $brand->name }}
                                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="model">Model:</label>
                            <input type="text" name="model" id="model" class="form-control"
                                value="{{ isset($tyre) ? $tyre->model : old('model') }}" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_source">Tyre Source:</label>
                            <input type="text" name="tyre_source" id="tyre_source" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_source : old('tyre_source', 'ownstock') }}">
                                <input type="hidden" name="supplier_id" id="supplier_id" class="form-control"
                                value="{{ isset($tyre) ? $tyre->supplier_id : old('supplier_id', '1') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="status">Status:</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ isset($tyre) && $tyre->status == 1 ? 'selected' : '' }}>Enable</option>
                                <option value="0" {{ isset($tyre) && $tyre->status == 0 ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_width">Width:</label>
                            <select name="tyre_width" id="tyre_width" class="form-control" required>
                                <option value="">Select Width</option>
                                @foreach([11, 13, 18, 19, 20, 22, 23, 24, 25, 26, 27, 30, 31, 33, 35, 75, 80, 90, 100, 110, 115, 120, 125, 130, 135, 140, 145, 155, 160, 165, 175, 185, 195, 205, 215, 225, 230, 235, 245, 250, 255, 265, 275, 285, 295, 300, 305, 315, 325, 335, 345, 350, 355, 385, 400, 410, 425, 435, 500, 560, 650, 750, 1000, 1050] as $width)
                                    <option value="{{ $width }}" {{ (old('tyre_width', $tyre->tyre_width ?? '') == $width) ? 'selected' : '' }}>
                                        {{ $width }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_profile">Profile:</label>
                            <select name="tyre_profile" id="tyre_profile" class="form-control" required>
                                <option value="">Select Profile</option>
                                @foreach([0, 7, 8, 9, 10, 12, 15, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 82, 85, 88, 90, 95, 100, 105, 110, 120, 125, 130, 350, 800, 850, 900, 950] as $profile)
                                    <option value="{{ $profile }}" {{ (old('tyre_profile', $tyre->tyre_profile ?? '') == $profile) ? 'selected' : '' }}>{{ $profile }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_diameter">Diameter:</label>
                            <select name="tyre_diameter" id="tyre_diameter" class="form-control" required>
                                <option value="">Select Diameter</option>
                                @foreach([0, 1, 4, 8, 10, 11, 12, 13, 14, 15, 16, 17, 17.5, 18, 19, 20, 21, 22, 22.5, 23, 24, 39, 175, 195, 225] as $diameter)
                                    <option value="{{ $diameter }}" {{ (old('tyre_diameter', $tyre->tyre_diameter ?? '') == $diameter) ? 'selected' : '' }}>{{ $diameter }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_type">Season:</label>
                            <select name="tyre_type" id="tyre_type" class="form-control">
                                <option value="">Select Season</option>
                                <option value="All Season" {{ (old('tyre_type', $tyre->tyre_type ?? '') == 'All Season') ? 'selected' : '' }}>
                                    All
                                    Season</option>
                                <option value="Summer" {{ (old('tyre_type', $tyre->tyre_type ?? '') == 'Summer') ? 'selected' : '' }}>
                                    Summer
                                </option>
                                <option value="Winter" {{ (old('tyre_type', $tyre->tyre_type ?? '') == 'Winter') ? 'selected' : '' }}>
                                    Winter
                                </option>
                            </select>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_speed">Tyre Speed:</label>
                            <select name="tyre_speed" id="tyre_speed" class="form-control">
                                <option value="">Select Speed Rating</option>
                                @php
                                    $speedRatings = [
                                        'K' => 'K: up to: 110 Kmh',
                                        'L' => 'L: up to: 120 Kmh',
                                        'M' => 'M: up to: 130 Kmh',
                                        'N' => 'N: up to: 140 Kmh',
                                        'P' => 'P: up to: 150 Kmh',
                                        'Q' => 'Q: up to: 160 Kmh',
                                        'R' => 'R: up to: 170 Kmh',
                                        'S' => 'S: up to: 180 Kmh',
                                        'T' => 'T: up to: 190 Kmh',
                                        'U' => 'U: up to: 200 Kmh',
                                        'H' => 'H: up to: 210 Kmh',
                                        'V' => 'V: up to: 240 Kmh',
                                        'W' => 'W: up to: 270 Kmh',
                                        'Y' => 'Y: up to: 300 Kmh',
                                    ];
                                @endphp
                                @foreach ($speedRatings as $key => $label)
                                    <option value="{{ $key }}" {{ (isset($tyre) && $tyre->tyre_speed == $key) || old('tyre_speed') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_eco">Tyre Eco:</label>
                            <select name="tyre_eco" id="tyre_eco" class="form-control">
                                <option value="">Select Eco</option>
                                @foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $eco)
                                    <option value="{{ $eco }}" {{ (isset($tyre) && $tyre->tyre_eco == $eco) || old('tyre_eco') == $eco ? 'selected' : '' }}>
                                        {{ $eco }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_disfr">Tyre Disfr:</label>
                            <select name="tyre_disfr" id="tyre_disfr" class="form-control">
                                <option value="">Select Tyre Disfr</option>
                                @foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $disfr)
                                    <option value="{{ $disfr }}" {{ (isset($tyre) && $tyre->tyre_disfr == $disfr) || old('tyre_disfr') == $disfr ? 'selected' : '' }}>
                                        {{ $disfr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_db">Tyre DB:</label>
                            <select name="tyre_db" id="tyre_db" class="form-control">
                                <option value="">Select DB</option>
                                @for ($db = 60; $db <= 90; $db++) {{-- Loop to dynamically create options --}}
                                    <option value="{{ $db }}" {{ (isset($tyre) && $tyre->tyre_db == $db) || old('tyre_db') == $db ? 'selected' : '' }}>
                                        {{ $db }}
                                    </option>
                                @endfor
                            </select>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_loadindex">Tyre Load Index:</label>
                            <input type="text" name="tyre_loadindex" id="tyre_loadindex" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_loadindex : old('tyre_loadindex') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <div class="mt-4">
                                <label for="tyre_reinforced" class="input-group">
                                    <input type="hidden" name="tyre_reinforced" value="0">
                                    <!-- Hidden input for unchecked value -->
                                    <input type="checkbox" name="tyre_reinforced" id="tyre_reinforced" value="1" {{ (isset($tyre) && $tyre->tyre_reinforced) || old('tyre_reinforced') ? 'checked' : '' }}>
                                    <span class="ml-1">Extra Load Tyre</span>
                                </label>
                            </div>

                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <div class="input-group mt-4">
                                <label for="tyre_antiflat">
                                    <input type="hidden" name="tyre_antiflat" value="0">
                                    <!-- Hidden input for unchecked value -->
                                    <input type="checkbox" name="tyre_antiflat" id="tyre_antiflat" value="1" {{ (isset($tyre) && $tyre->tyre_antiflat) || old('tyre_antiflat') ? 'checked' : '' }}> <span
                                        class="ml-1">Runflat Tyre</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <div class="mt-4">
                                <label for="status" class="input-group">
                                    <input type="hidden" name="status" value="0">
                                    <!-- Hidden input for unchecked value -->
                                    <input type="checkbox" name="status" id="status" value="1" 
                                        {{ (!isset($tyre) || (isset($tyre) && $tyre->status)) || old('status') ? 'checked' : '' }}>
                                    <span class="ml-1">Display Status</span>
                                </label>
                            </div>
                        </div>
                    <div class="form-group col-lg-3 col-md-6 col-12">
                        <label for="vehicle_type">Vehicle Type:</label>
                        <select name="vehicle_type" id="vehicle_type" class="form-control">
                            <option value="">Select Vehicle Type</option>
                            @foreach (['Car', 'Van', '4x4', 'SUV', 'Commercial Truck'] as $type)
                            <option value="{{ $type }}" 
                                {{ (isset($tyre) && ucwords(strtolower($tyre->vehicle_type)) == $type) || ucwords(strtolower(old('vehicle_type'))) == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" class="form-control"
                                value="{{ isset($tyre) ? $tyre->quantity : old('quantity') }}" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price">Cost Price:</label>
                            <input type="text" name="price" id="price" class="form-control"
                                value="{{ isset($tyre) ? number_format($tyre->price ?? 0, 2) : number_format(old('price', 0.00), 2) }}"
                                required>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="margin">Margin:</label>
                            <input type="text" name="margin" id="margin" class="form-control"
                                value="{{ isset($tyre) ? $tyre->margin ?? '0.00' : old('margin', '0.00') }}" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="image">Image:</label>
                            <input type="file" name="image" id="image" class="form-control">
                            @if (isset($tyre) && $tyre->image)
                                <img src="{{ asset('storage/' . $tyre->image) }}" alt="Tyre Image" width="100">
                            @endif
                        </div>

                        <!-- Add the tax_class_id field -->
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tax_class_id">VAT:</label>
                            <select name="tax_class_id" id="tax_class_id" class="form-control">
                                <option value="9" {{ isset($tyre) && $tyre->tax_class_id == 9 ? 'selected' : '' }}>VAT</option>
                                <option value="0" {{ isset($tyre) && $tyre->tax_class_id == 0 ? 'selected' : '' }}>No VAT
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price_fullyfitted">Price Fullyfitted:</label>
                            <input type="text" name="price_fullyfitted" id="price_fullyfitted" class="form-control"
                                value="{{ isset($tyre) ? $tyre->price_fullyfitted ?? '0.00' : old('price_fullyfitted', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price_mobilefitted">Price Mobilefitted:</label>
                            <input type="text" name="price_mobilefitted" id="price_mobilefitted" class="form-control"
                                value="{{ isset($tyre) ? $tyre->price_mobilefitted ?? '0.00' : old('price_mobilefitted', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price_mailorder">Price Mailorder:</label>
                            <input type="text" name="price_mailorder" id="price_mailorder" class="form-control"
                                value="{{ isset($tyre) ? $tyre->price_mailorder ?? '0.00' : old('price_mailorder', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price_emergency">Price Emergency:</label>
                            <input type="text" name="price_emergency" id="price_emergency" class="form-control"
                                value="{{ isset($tyre) ? number_format($tyre->price_emergency ?? 0, 2) : number_format(old('price_emergency', 0.00), 2) }}"
                                required>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="trade_costprice">Trade Cost Price:</label>
                            <input type="text" name="trade_costprice" id="trade_costprice" class="form-control"
                                value="{{ isset($tyre) ? $tyre->trade_costprice ?? '0.00' : old('trade_costprice', '0.00') }}"
                                required>
                        </div>
                    </div>
                    <div class="text-right"><button type="submit" class="btn btn-primary">{{ isset($tyre) ? 'Update Tyre' : 'Add Tyre' }}</button></div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
            </div>
        </div>
    </div>
</div>
<script>
            function calculateFullyFitted() {
                let costPrice = parseFloat(document.getElementById('price').value) || 0;
                let margin = parseFloat(document.getElementById('margin').value) || 0;
                let tradeMargin = 4.17;
                // console.log(tradeMargin);

                let fullyFitted = costPrice + margin;
                let tradeprice = costPrice + tradeMargin;  // Using the trade margin value from the database

                document.getElementById('price_fullyfitted').value = fullyFitted.toFixed(2);
                document.getElementById('price_mobilefitted').value = fullyFitted.toFixed(2);
                document.getElementById('price_mailorder').value = fullyFitted.toFixed(2);
                document.getElementById('trade_costprice').value = tradeprice.toFixed(2);
            }

            document.getElementById('price').addEventListener('input', calculateFullyFitted);
            document.getElementById('margin').addEventListener('input', calculateFullyFitted);
</script>