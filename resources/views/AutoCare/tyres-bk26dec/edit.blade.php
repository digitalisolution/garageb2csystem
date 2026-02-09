@extends('samples')
@section('content')

        <div class="container-fluid">
            <div class="bg-white p-3">
                <h5>{{ isset($tyre) ? 'Edit' : 'Add' }} Tyre Product</h5>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
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
                            <label for="ean">EAN<span class="text-red">*</span></label>
                            <input type="text" name="tyre_ean" id="ean" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_ean : old('tyre_ean') }}" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="instock">Instock:</label>
                            <select name="instock" id="instock" class="form-control">
                                <option value="1" {{ isset($tyre) && $tyre->instock == 1 ? 'selected' : '' }}>yes</option>
                                <option value="0" {{ isset($tyre) && $tyre->instock == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="sku">SKU<span class="text-red">*</span></label>
                            <input type="text" name="tyre_sku" id="sku" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_sku : old('tyre_sku') }}" required>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="lead_time">Availability:</label>
                            <input type="text" name="lead_time" id="lead_time" class="form-control"
                                value="{{ isset($tyre) ? $tyre->lead_time : old('lead_time') }}">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="brand_id">Select Brand<span class="text-red">*</span></label>
                            <select name="tyre_brand_id" id="brand" class="form-control" required>
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->brand_id }}" {{ isset($tyre) && $tyre->tyre_brand_id ==
                                $brand->brand_id ? 'selected' : '' }}>{{ $brand->name }}
                                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <input type="hidden" name="product_type" value="tyre">
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="model">Model<span class="text-red">*</span></label>
                            <input type="text" name="tyre_model" id="tyre_model" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_model : old('tyre_model') }}" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                        <label for="supplier_id">Tyre Source:</label>
                        <select name="supplier_id" id="supplier_id" class="form-control" required>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" 
                                    {{ (isset($tyre) && $tyre->supplier_id == $supplier->id) || (!isset($tyre) && $supplier->id == 1) ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="status">Status:</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ isset($tyre) && $tyre->status == 1 ? 'selected' : '' }}>Enable</option>
                                <option value="0" {{ isset($tyre) && $tyre->status == 0 ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>


                       <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_width">Width<span class="text-red">*</span></label>
                            <input 
                                type="text" 
                                name="tyre_width" 
                                id="tyre_width" 
                                class="form-control" 
                                value="{{ old('tyre_width', $tyre->tyre_width ?? '') }}" 
                                required
                                placeholder="e.g. 205"
                            >
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_profile">Profile<span class="text-red">*</span></label>
                            <input 
                                type="text" 
                                name="tyre_profile" 
                                id="tyre_profile" 
                                class="form-control" 
                                value="{{ old('tyre_profile', $tyre->tyre_profile ?? '') }}" 
                                required
                                placeholder="e.g. 55"
                            >
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_diameter">Diameter<span class="text-red">*</span></label>
                            <input 
                                type="text" 
                                name="tyre_diameter" 
                                id="tyre_diameter" 
                                class="form-control" 
                                value="{{ old('tyre_diameter', $tyre->tyre_diameter ?? '') }}" 
                                required
                                placeholder="e.g. 16 or 17.5"
                            >
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_season">Season:</label>
                            <select name="tyre_season" id="tyre_season" class="form-control">
                                <option value="">Select Season</option>
                                <option value="All Season" {{ (old('tyre_season', $tyre->tyre_season ?? '') == 'All Season') ? 'selected' : '' }}>
                                    All
                                    Season</option>
                                <option value="Summer" {{ (old('tyre_season', $tyre->tyre_season ?? '') == 'Summer') ? 'selected' : '' }}>
                                    Summer
                                </option>
                                <option value="Winter" {{ (old('tyre_season', $tyre->tyre_season ?? '') == 'Winter') ? 'selected' : '' }}>
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
                            <label for="tyre_fuel">Tyre Fuel:</label>
                            <select name="tyre_fuel" id="tyre_fuel" class="form-control">
                                <option value="">Select Eco</option>
                                @foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $eco)
                                    <option value="{{ $eco }}" {{ (isset($tyre) && $tyre->tyre_fuel == $eco) || old('tyre_fuel') == $eco ? 'selected' : '' }}>
                                        {{ $eco }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_wetgrip">Tyre Wetgrip:</label>
                            <select name="tyre_wetgrip" id="tyre_wetgrip" class="form-control">
                                <option value="">Select Tyre Wetgrip</option>
                                @foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $wetgrip)
                                    <option value="{{ $wetgrip }}" {{ (isset($tyre) && $tyre->tyre_wetgrip == $wetgrip) || old('tyre_wetgrip') == $wetgrip ? 'selected' : '' }}>
                                        {{ $wetgrip }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_noisedb">Tyre NoiseDB:</label>
                            <select name="tyre_noisedb" id="tyre_noisedb" class="form-control">
                                <option value="">Select NoiseDB</option>
                                @for ($db = 60; $db <= 90; $db++)
                                    <option value="{{ $db }}" {{ (isset($tyre) && $tyre->tyre_noisedb == $db) || old('tyre_noisedb') == $db ? 'selected' : '' }}>
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
                                <label for="tyre_extraload" class="input-group">
                                    <input type="hidden" name="tyre_extraload" value="0">
                                    <!-- Hidden input for unchecked value -->
                                    <input type="checkbox" name="tyre_extraload" id="tyre_extraload" value="1" {{ (isset($tyre) && $tyre->tyre_extraload) || old('tyre_extraload') ? 'checked' : '' }}>
                                    <span class="ml-1">Extra Load Tyre</span>
                                </label>
                            </div>

                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <div class="input-group mt-4">
                                <label for="tyre_runflat">
                                    <input type="hidden" name="tyre_runflat" value="0">
                                    <!-- Hidden input for unchecked value -->
                                    <input type="checkbox" name="tyre_runflat" id="tyre_runflat" value="1" {{ (isset($tyre) && $tyre->tyre_runflat) || old('tyre_runflat') ? 'checked' : '' }}> <span
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
                            @foreach (['Car', 'Van', '4x4', 'SUV','Motorbike','Commercial Truck'] as $type)
                            <option value="{{ $type }}" 
                                {{ (isset($tyre) && ucwords(strtolower($tyre->vehicle_type)) == $type) || ucwords(strtolower(old('vehicle_type'))) == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!isset($tyre))
                    <div class="form-group col-lg-3 col-md-6 col-12">
                    <label for="quantity">Quantity<span class="text-red">*</span></label>
                            <input type="number" name="tyre_quantity" id="quantity" class="form-control" required >
                            </div>
                        @endif
                      
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_image">Tyre Image:</label>
                            <input type="file" name="tyre_image" id="tyre_image" class="form-control">
                            @php
                                $cdnBaseUrl = config('cdn.tyre_cdn_url', ''); // fallback to empty string
                                $localDir = 'frontend/themes/img/tyre_images/';
                                $defaultImg = asset('frontend/themes/default/img/product/sample-tyre.png');
                                $imageUrl = $defaultImg;

                                if (!empty($tyre) && !empty($tyre->tyre_image)) {
                                    $imageFile = $tyre->tyre_image;
                                    $fullLocalPath = public_path($localDir . $imageFile);

                                    if (file_exists($fullLocalPath)) {
                                        $imageUrl = asset($localDir . $imageFile);
                                    } elseif (!empty($cdnBaseUrl)) {
                                        $imageUrl = rtrim($cdnBaseUrl, '/') . '/' . ltrim($imageFile, '/');
                                    }
                                }
                                @endphp
                                <img src="{{ $imageUrl }}" width="100"
                                     onerror="this.onerror=null;this.src='{{ $defaultImg }}';"
                                 alt="Tyre Image" class="mt-2 border rounded">
                        </div>


                        @if(isset($tyre))
                        <div class="clearfix"></div>
                        <div class="col-lg-3 col-12">
                            <div class="stock-availability">
                            Stock Current Quantity: @if($tyre){{ $tyre->tyre_quantity }}@endif
                        </div>
                        </div>
                        <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <!-- Table Header -->
                            <thead class="thead-dark table-sm">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody>
                                <tr>
                                    <td>
                                        <!-- Default Current Date -->
                                        <input type="date" name="date" id="date" >
                                    </td>
                                    <td>
                                        <select name="stock_type" >
                                            <option value="Increase">Increase</option>
                                            <option value="Decrease">Decrease</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="tyre_quantity" placeholder="Quantity" min="1" >
                                    </td>
                                    <td>
                                        <!-- Reason Dropdown -->
                                        <select name="reason" id="reason">
                                            <option value="Stock take adjustment" selected>Stock Adjustment</option>
                                            <option value="Item returned">Item Returned</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <!-- Additional Input Field for "Other" -->
                                        <div id="otherReasonField">
                                            <textarea type="text" name="other_reason" placeholder="Enter reason manually"></textarea>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_price">Cost Price:</label>
                            <input type="text" name="tyre_price" id="tyre_price" class="form-control"
                                value="{{ isset($tyre) ? number_format($tyre->tyre_price ?? 0, 2) : number_format(old('tyre_price', 0.00), 2) }}"
                                required>
                        </div>


                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_margin">Margin:</label>
                            <input type="text" name="tyre_margin" id="tyre_margin" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_margin ?? '0.00' : old('tyre_margin', '0.00') }}" required>
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
                            <label for="tyre_fullyfitted_price">Price Fullyfitted:</label>
                            <input type="text" name="tyre_fullyfitted_price" id="tyre_fullyfitted_price" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_fullyfitted_price ?? '0.00' : old('tyre_fullyfitted_price', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_mobilefitted_price">Price Mobilefitted:</label>
                            <input type="text" name="tyre_mobilefitted_price" id="tyre_mobilefitted_price" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_mobilefitted_price ?? '0.00' : old('tyre_mobilefitted_price', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_mailorder_price">Price Mailorder:</label>
                            <input type="text" name="tyre_mailorder_price" id="tyre_mailorder_price" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_mailorder_price ?? '0.00' : old('tyre_mailorder_price', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_collection_price">Price Collection:</label>
                           <input type="text" name="tyre_collection_price" id="tyre_collection_price" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_collection_price ?? '0.00' : old('tyre_collection_price', '0.00') }}"
                                required>
                        </div>

                        <div class="form-group col-lg-3 col-md-6 col-12">
                            <label for="tyre_delivery_price">Price Delivery:</label>
                        <input type="text" name="tyre_delivery_price" id="tyre_delivery_price" class="form-control"
                                value="{{ isset($tyre) ? $tyre->tyre_delivery_price ?? '0.00' : old('tyre_delivery_price', '0.00') }}"
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
<style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        .submit-btn {
            text-align: right;
        }
        /* Hide the "Other Reason" input field by default */
        #otherReasonField {
            display: none;
        }
        .hide {
            display: none;
        }
    </style>
<script>
    // Set the default date to today's date
    document.addEventListener('DOMContentLoaded', function () {
        const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
        document.getElementById('date').value = today;

        // Show/hide the "Other Reason" input field based on selection
        document.getElementById('reason').addEventListener('change', function () {
            const otherReasonField = document.getElementById('otherReasonField');
            if (this.value === 'Other') {
                otherReasonField.style.display = 'block';
            } else {
                otherReasonField.style.display = 'none';
            }
        });
    });
</script>
<script>
    function calculateFullyFitted() {
        let costPrice = parseFloat(document.getElementById('tyre_price').value) || 0;
        let margin = parseFloat(document.getElementById('tyre_margin').value) || 0;
        let tradeMargin = 4.84;
        let deliveryPriceValue = "{{ get_option('tyre_delivery_price') }}";

        let fullyFitted = costPrice + margin;
        let tradeprice = costPrice + tradeMargin;

         switch (deliveryPriceValue) {
            case 'trade_costprice':
                deliveryPrice = tradeprice;
                break;
                 case 'tyre_fullyfitted_price':
                deliveryPrice = fullyFitted;
                break;
                 case 'tyre_mobilefitted_price':
                deliveryPrice = fullyFitted;
                break;
            case 'tyre_mailorder_price':
                deliveryPrice = fullyFitted;
                break;
            case 'tyre_collection_price':
                deliveryPrice = fullyFitted;
                break;
            default:
                deliveryPrice = costPrice;
        }

        document.getElementById('tyre_fullyfitted_price').value = fullyFitted.toFixed(2);
        document.getElementById('tyre_mobilefitted_price').value = fullyFitted.toFixed(2);
        document.getElementById('tyre_mailorder_price').value = fullyFitted.toFixed(2);
        document.getElementById('tyre_collection_price').value = fullyFitted.toFixed(2);
        document.getElementById('tyre_delivery_price').value = deliveryPrice.toFixed(2);
        document.getElementById('trade_costprice').value = tradeprice.toFixed(2);
    }

    document.getElementById('tyre_price').addEventListener('input', calculateFullyFitted);
    document.getElementById('tyre_margin').addEventListener('input', calculateFullyFitted);
</script>
@endsection