@if($moduleWheelEnabled)
<div class="modal fade" id="addWheelModal" tabindex="-1" aria-labelledby="addWheelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWheelModalLabel">Add New Wheel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="bg-white p-3">
                <h5>{{ isset($wheel) ? 'Edit' : 'Add' }} Wheel Product</h5>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ isset($wheel) 
                    ? route('AutoCare.wheels.update', ['wheel_id' => $wheel->wheel_id]) . '?' . http_build_query($queryParams ?? [])
                    : route('AutoCare.wheels.store') }}" 
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($wheel))
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="ean">EAN:</label>
                            <input type="text" name="ean" id="ean" class="form-control"
                                value="{{ isset($wheel) ? $wheel->ean : old('ean') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="instock">Instock:</label>
                            <select name="instock" id="instock" class="form-control">
                                <option value="1" {{ isset($wheel) && $wheel->instock == 1 ? 'selected' : '' }}>yes</option>
                                <option value="0" {{ isset($wheel) && $wheel->instock == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="sku">SKU:</label>
                            <input type="text" name="sku" id="sku" class="form-control"
                                value="{{ isset($wheel) ? $wheel->sku : old('sku') }}" required>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="lead_time">Availability:</label>
                            <input type="text" name="lead_time" id="lead_time" class="form-control"
                                value="{{ isset($wheel) ? $wheel->lead_time : old('lead_time') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="brand_id">Select Brand:</label>
                            <select name="brand_id" id="brand" class="form-control">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                <option value="{{ $brand->brand_id }}" {{ isset($wheel) && $wheel->wheel_brand_id == $brand->brand_id ? 'selected' : '' }}>{{ $brand->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="model">Model:</label>
                            <input type="text" name="model" id="model" class="form-control"
                                value="{{ isset($wheel) ? $wheel->model : old('model') }}" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_source">wheel Source:</label>
                            <input type="text" name="wheel_source" id="wheel_source" class="form-control"
                                value="{{ isset($wheel) ? $wheel->wheel_source : old('wheel_source', 'ownstockwheel') }}">
                                <input type="hidden" name="supplier_id" id="supplier_id" class="form-control"
                                value="{{ isset($wheel) ? $wheel->supplier_id : old('supplier_id', '1') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="status">Status:</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ isset($wheel) && $wheel->status == 1 ? 'selected' : '' }}>Enable</option>
                                <option value="0" {{ isset($wheel) && $wheel->status == 0 ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_width">Width:</label>
                            <select name="wheel_width" id="wheel_width" class="form-control" required>
                                <option value="">Select Width</option>
                                @foreach([5,5.5,6,6.5,7,7.5,8,8.5,9,9.5,10,10.5,11,11.5,12,12.5,13,13.5,14,14.5,15,15.5,16,16.5,17,17.5,18,18.5,19,19.5,20,20.5,21,21.5,22,22.5,23,23.5,24,24.5,25,25.5,26,26.5,27,27.5,28,28.5,29,29.5,30] as $width)
                                    <option value="{{ $width }}" {{ (old('wheel_width', $wheel->wheel_width ?? '') == $width) ? 'selected' : '' }}>
                                        {{ $width }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_diameter">Diameter:</label>
                            <select name="wheel_diameter" id="wheel_diameter" class="form-control" required>
                                <option value="">Select Diameter</option>
                                @foreach([10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30] as $diameter)
                                    <option value="{{ $diameter }}" {{ (old('wheel_diameter', $wheel->wheel_diameter ?? '') == $diameter) ? 'selected' : '' }}>{{ $diameter }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_cb">Centrebore:</label>
                            <select name="wheel_cb" id="wheel_cb" class="form-control" required>
                                <option value="">Select Centrebore</option>
                                @foreach([50,54.1,56.6,57,57.1,58.1,60.1,63.3,63.34,63.4,64.1,64.2,65.1,66.1,66.5,66.6,67.1,70,70.1,70.2,70.6,71.1,71.5,71.6,72.6,74.1,75.1,78.1,84.1,89.1,92.4,93.1,100.1,106.1,110.1,114.3] as $centrebore)
                                    <option value="{{ $centrebore }}" {{ (old('wheel_cb', $wheel->wheel_cb ?? '') == $centrebore) ? 'selected' : '' }}>{{ $centrebore }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_offset">wheel Offset:</label>
                            <select name="wheel_offset" id="wheel_offset" class="form-control">
                                <option value="">Select Offset</option>
                                @foreach ([10,18,20,21,22,23,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,66,68,69,70] as $offset)
                                    <option value="{{ $offset }}" {{ (isset($wheel) && $wheel->wheel_offset == $offset) || old('wheel_offset') == $offset ? 'selected' : '' }}>
                                        {{ $offset }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_studs">wheel Studs:</label>
                            <select name="wheel_studs" id="wheel_studs" class="form-control">
                                <option value="">Select Studs</option>
                                @foreach ([4,5,6,7,8] as $studs)
                                    <option value="{{ $studs }}" {{ (isset($wheel) && $wheel->wheel_studs == $studs) || old('wheel_studs') == $studs ? 'selected' : '' }}>
                                        {{ $studs }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_pcd">wheel PCD:</label>
                            <select name="wheel_pcd" id="wheel_pcd" class="form-control">
                                <option value="">Select PCD</option>
                                @foreach ([95,96,97,98,100,105,108,110,112,114,114.3,115,118,120,120.65,127,130,135,139.7,150,160,165] as $pcd)
                                    <option value="{{ $pcd }}" {{ (isset($wheel) && $wheel->wheel_pcd == $pcd) || old('wheel_pcd') == $pcd ? 'selected' : '' }}>
                                        {{ $pcd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_colour">wheel Color:</label>
                            <input type="text" name="wheel_colour" id="wheel_colour" class="form-control"
                                value="{{ isset($wheel) ? $wheel->wheel_colour : old('wheel_colour') }}">
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="wheel_loadrating">Load Index:</label>
                            <input type="text" name="wheel_loadrating" id="wheel_loadrating" class="form-control"
                                value="{{ isset($wheel) ? $wheel->wheel_loadrating : old('wheel_loadrating') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <div class="mt-4">
                                <label for="wheel_weight">wheel Weight(kg):</label>
                                <input type="text" name="wheel_weight" id="wheel_weight" class="form-control"
                                value="{{ isset($wheel) ? $wheel->wheel_weight : old('wheel_weight') }}">
                            </div>

                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <div class="input-group mt-4">
                                <label for="wheel_vehicle_type">Vehicle Type:</label>
                                <select name="wheel_vehicle_type" id="wheel_vehicle_type" class="form-control">
                                    <option value="">Select Vehicle Type</option>
                                    @foreach (['Car', 'Van', '4x4', 'SUV', 'Commercial Truck'] as $type)
                                    <option value="{{ $type }}" 
                                        {{ (isset($wheel) && ucwords(strtolower($wheel->wheel_vehicle_type)) == $type) || ucwords(strtolower(old('wheel_vehicle_type'))) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <div class="mt-4">
                                <label for="status" class="input-group">
                                    <input type="hidden" name="status" value="0">
                                    <!-- Hidden input for unchecked value -->
                                    <input type="checkbox" name="status" id="status" value="1" 
                                        {{ (!isset($wheel) || (isset($wheel) && $wheel->status)) || old('status') ? 'checked' : '' }}>
                                    <span class="ml-1">Display Status</span>
                                </label>
                            </div>
                        </div>
                    <div class="form-group col-lg-3 col-md-6 col-12">
                        
                    </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" class="form-control"
                                value="{{ isset($wheel) ? $wheel->quantity : old('quantity') }}" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price">Cost Price:</label>
                            <input type="text" name="price" id="price" class="form-control"
                                value="{{ isset($wheel) ? number_format($wheel->price ?? 0, 2) : number_format(old('price', 0.00), 2) }}"
                                required>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="margin">Margin:</label>
                            <input type="text" name="margin" id="margin" class="form-control"
                                value="{{ isset($wheel) ? $wheel->margin ?? '0.00' : old('margin', '0.00') }}" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="image">Image:</label>
                            <input type="file" name="image" id="image" class="form-control">
                            @if (isset($wheel) && $wheel->image)
                                <img src="{{ asset('storage/' . $wheel->image) }}" alt="wheel Image" width="100">
                            @endif
                        </div>

                        <!-- Add the tax_class_id field -->
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tax_class_id">VAT:</label>
                            <select name="tax_class_id" id="tax_class_id" class="form-control">
                                <option value="9" {{ isset($wheel) && $wheel->tax_class_id == 9 ? 'selected' : '' }}>VAT</option>
                                <option value="0" {{ isset($wheel) && $wheel->tax_class_id == 0 ? 'selected' : '' }}>No VAT
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price_fullyfitted">Price Fullyfitted:</label>
                            <input type="text" name="price_fullyfitted" id="price_fullyfitted" class="form-control"
                                value="{{ isset($wheel) ? $wheel->price_fullyfitted ?? '0.00' : old('price_fullyfitted', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price_mobilefitted">Price Mobilefitted:</label>
                            <input type="text" name="price_mobilefitted" id="price_mobilefitted" class="form-control"
                                value="{{ isset($wheel) ? $wheel->price_mobilefitted ?? '0.00' : old('price_mobilefitted', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price_mailorder">Price Mailorder:</label>
                            <input type="text" name="price_mailorder" id="price_mailorder" class="form-control"
                                value="{{ isset($wheel) ? $wheel->price_mailorder ?? '0.00' : old('price_mailorder', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="price_emergency">Price Emergency:</label>
                            <input type="text" name="price_emergency" id="price_emergency" class="form-control"
                                value="{{ isset($wheel) ? number_format($wheel->price_emergency ?? 0, 2) : number_format(old('price_emergency', 0.00), 2) }}"
                                required>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="trade_costprice">Trade Cost Price:</label>
                            <input type="text" name="trade_costprice" id="trade_costprice" class="form-control"
                                value="{{ isset($wheel) ? $wheel->trade_costprice ?? '0.00' : old('trade_costprice', '0.00') }}"
                                required>
                        </div>
                    </div>
                    <div class="text-right"><button type="submit" class="btn btn-primary">{{ isset($wheel) ? 'Update wheel' : 'Add wheel' }}</button></div>
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
@endif