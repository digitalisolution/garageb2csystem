@extends('samples')
@section('content')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <h3>Search Tyres</h3>
            <!-- Add Tyre Button -->
            <div class="mb-3">
                <a href="{{ route('AutoCare.tyres.edit', ['product_id' => 'new']) }}" class="btn btn-primary">Add Tyre</a>
                <!-- Add Ownstock Button -->
                @if(get_option('add_ownstock_inventry'))
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ownstockModal">
                        Add Ownstock
                    </button>
                @endif
            </div>
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

            <!-- Search Form -->
            <form action="{{ route('AutoCare.tyres.search') }}" method="GET">
                <div class="row align-items-center">
                    <!-- Garage Name Field -->
                    <div class="col-md-2 grg_name_fitters">
                        <div class="form-group">
                            <label for="garage_id">Garage Name</label>
                            <select name="garage_id" id="garage_id" class="form-control">
                                <option value="">Select Garage Name</option>

                                @foreach($garages as $garage)
                                    <option value="{{ $garage->id }}"
                                        {{ request('garage_id') == $garage->id ? 'selected' : '' }}>
                                        {{ $garage->garage_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Width Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="width">Width</label>
                            <input type="text" name="width" id="width" class="form-control"
                                value="{{ $filters['width'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Profile Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="profile">Profile</label>
                            <input type="text" name="profile" id="profile" class="form-control"
                                value="{{ $filters['profile'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Diameter Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="diameter">Rim Size</label>
                            <input type="text" name="diameter" id="diameter" class="form-control"
                                value="{{ $filters['diameter'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Brand Name Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tyre_brand_name">Brand Name</label>
                            <select name="tyre_brand_name" id="tyre_brand_name" class="form-control">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->brand_id }}" {{ isset($filters['tyre_brand_name']) && $filters['tyre_brand_name'] == $brand->brand_id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- EAN Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="ean">EAN</label>
                            <input type="text" name="tyre_ean" id="ean" class="form-control"
                                value="{{ $filters['tyre_ean'] ?? '' }}">
                        </div>
                    </div>

                    <!-- SKU Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="sku">SKU</label>
                            <input type="text" name="tyre_sku" id="sku" class="form-control"
                                value="{{ $filters['tyre_sku'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Tyre Source Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tyre_supplier_name">Tyre Supplier</label>
                            <select name="tyre_supplier_name" id="tyre_supplier_name" class="form-control">
                                <option value="">Select Source</option>
                                @foreach ($suppliersWithGarage as $supplier)
                                    <option value="{{ $supplier->display_name }}"
                                        {{ isset($filters['tyre_supplier_name']) && $filters['tyre_supplier_name'] == $supplier->display_name ? 'selected' : '' }}>
                                        {{ $supplier->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tyre Type Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tyre_season">Tyre Type</label>
                            <select name="tyre_season" id="tyre_season" class="form-control">
                                <option value="">Select Type</option>
                                @foreach ($tyreTypes as $type)
                                    <option value="{{ $type }}" {{ isset($filters['tyre_season']) && $filters['tyre_season'] == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Season Type Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="season_type">Season Type</label>
                            <select name="season_type" id="season_type" class="form-control">
                                <option value="">Select Season</option>
                                @foreach ($seasonTypes as $season)
                                    <option value="{{ $season }}" {{ isset($filters['season_type']) && $filters['season_type'] == $season ? 'selected' : '' }}>
                                        {{ $season }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Vehicle Type Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="vehicle_type">Vehicle Type</label>
                            <select name="vehicle_type" id="vehicle_type" class="form-control">
                                <option value="">Select Vehicle</option>
                                @foreach ($vehicleTypes as $vehicle)
                                    <option value="{{ $vehicle }}" {{ isset($filters['vehicle_type']) && $filters['vehicle_type'] == $vehicle ? 'selected' : '' }}>
                                        {{ $vehicle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- RFT (Run-Flat) Checkbox -->
                    <div class="col-md-2">
                        <div class="form-group d-flex gap-2 mt-3">
                            <input type="checkbox" name="rft" id="rft" {{ isset($filters['rft']) ? 'checked' : '' }}>
                            <label for="rft">Run-Flat Tyres</label>
                        </div>
                    </div>

                    <!-- Stock Status Field -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="stock_status">Stock Status</label>
                            <select name="stock_status" id="stock_status" class="form-control">
                                <option value="instock" {{ isset($filters['stock_status']) && $filters['stock_status'] == 'instock' ? 'selected' : '' }}>Instock</option>
                                <option value="available" {{ isset($filters['stock_status']) && $filters['stock_status'] == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="all" {{ isset($filters['stock_status']) && $filters['stock_status'] == 'all' ? 'selected' : '' }}>All Tyres</option>
                            </select>
                        </div>
                    </div>

                </div>
                <!-- Submit Button -->
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
            </form>
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                @if (!empty($filters['tyre_supplier_name']) && strtolower($filters['tyre_supplier_name']) === 'ownstock')
                    @if(isset($totalQty) && isset($totalCost))
                        <div class="alert alert-primary font-weight-bold">
                            Total OWNSTOCK Quantity: <span class="text-success">{{ $totalQty }}</span><br>
                            Total OWNSTOCK Cost Price: <span class="text-success">£{{ number_format($totalCost, 2) }}</span>
                        </div>
                    @endif
                @endif
                <!-- Tyres Table -->
                <table class="table table-bordered table-hover mt-4">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'tyre_ean', 'order' => $sortBy === 'tyre_ean' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    EAN
                                    @if ($sortBy === 'tyre_ean')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th>
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'tyre_description', 'order' => $sortBy === 'tyre_description' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    Details
                                    @if ($sortBy === 'tyre_description')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th class="text-center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'tyre_brand_name', 'order' => $sortBy === 'tyre_brand_name' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    Brand
                                    @if ($sortBy === 'tyre_brand_name')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th align="center">Eco</th>
                            <th align="center">Disfr</th>
                            <th align="center">DB</th>
                            <th align="center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'vehicle_type', 'order' => $sortBy === 'vehicle_type' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    Vehicle Type
                                    @if ($sortBy === 'vehicle_type')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th class="text-center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'tyre_quantity', 'order' => $sortBy === 'tyre_quantity' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    Qty
                                    @if ($sortBy === 'tyre_quantity')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th class="text-center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'tyre_price', 'order' => $sortBy === 'tyre_price' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    Cost
                                    @if ($sortBy === 'tyre_price')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th class="text-center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'tyre_fullyfitted_price', 'order' => $sortBy === 'tyre_fullyfitted_price' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    SP <br><span class="no-wrap">ex VAT</span>
                                    @if ($sortBy === 'tyre_fullyfitted_price')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th class="text-center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'tyre_fullyfitted_price', 'order' => $sortBy === 'tyre_fullyfitted_price' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    SP <br><span class="no-wrap">inc VAT</span>
                                    @if ($sortBy === 'tyre_fullyfitted_price')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th class="text-center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'trade_costprice', 'order' => $sortBy === 'trade_costprice' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    <span class="no-wrap">Trade Price</span><br>ex VAT
                                    @if ($sortBy === 'trade_costprice')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th class="text-center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'trade_costprice', 'order' => $sortBy === 'trade_costprice' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    <span class="no-wrap">Trade Price</span><br>inc VAT
                                    @if ($sortBy === 'trade_costprice')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th align="center">Supplier</th>
                            <th align="center">
                                <a class="no-wrap"
                                    href="{{ route('AutoCare.tyres.search', array_merge(request()->except(['sort_by', 'order']), ['sort_by' => 'lead_time', 'order' => $sortBy === 'lead_time' && $order === 'asc' ? 'desc' : 'asc'])) }}">
                                    Lead Time
                                    @if ($sortBy === 'lead_time')
                                        {{ $order === 'asc' ? '' : '' }}
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <!-- <th>Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tyres as $tyre)
                            <tr>
                                <td align="center" class="no-wrap">
                                    <a href="javascript:void(0);" class="ean-link"
                                        data-ean="{{ $tyre->tyre_ean }}">{{ $tyre->tyre_ean }}</a>
                                        <div class="d-flex justify-content-between">
                                    <!-- Edit Button -->
                                    <a href="{{ route('AutoCare.tyres.edit', ['product_id' => $tyre->product_id]) . '?' . http_build_query(request()->query()) }}"
                                        class="btn btn-sm btn-warning m-2">Edit</a>
                                    <!-- Delete Button -->
                                    <form action="{{ route('AutoCare.tyres.delete', $tyre->product_id) }}" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('Are you sure you want to delete this tyre?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger m-2">Delete</button>
                                    </form>
                                    </div>

                                </td>
                                <td>{{ $tyre->tyre_description }}</td>
                                <td align="center">{{ $tyre->tyre_brand_name }}</td>
                                <!-- Replace with the actual brand name if available -->
                                <td align="center"><span class="{{ $tyre->tyre_fuel }}">{{ $tyre->tyre_fuel }}</span></td>
                                <td align="center"><span class="{{ $tyre->tyre_wetgrip }}">{{ $tyre->tyre_wetgrip }}</span></td>
                                <td align="center"><span class="db">{{ $tyre->tyre_noisedb }}</span></td>
                                <td align="center">{{ $tyre->vehicle_type }}</td>
                                <td align="center">
                                    <span
                                        style="background-color: @if($tyre->tyre_quantity < 4) red @elseif($tyre->tyre_quantity < 10) orange @else green  @endif;padding:5px;color:#fff;border-radius:4px;">
                                        {{ $tyre->tyre_quantity }}
                                    </span>
                                </td>

                                <td align="center">£{{ number_format($tyre->tyre_price, 2) }}</td>
                                <td align="center">£{{ number_format($tyre->tyre_fullyfitted_price, 2) }}</td>
                                <td align="center" class="trade_costprice">
                                    @if($tyre->tax_class_id == 9)
                                        £{{ number_format($tyre->tyre_fullyfitted_price * 1.20, 2) }}
                                    @else
                                        £{{ number_format($tyre->tyre_fullyfitted_price, 2) }}
                                    @endif
                                </td>

                                <td align="center">£{{ number_format($tyre->trade_costprice, 2) }}</td>
                                <td align="center" class="trade_costprice">£{{ number_format($tyre->trade_costprice * 1.2, 2) }}
                                </td>
                                <td align="center">
                                    <span class="{{ $tyre->tyre_supplier_name }}">{{ strtoupper($tyre->tyre_supplier_name) }}
                                        @if(!empty($tyre->garage_name))</span>
                                            <br>
                                            <span>({{ $tyre->garage_name }})</span>
                                        @endif
                                    
                                </td>
                                <td align="center" class="no-wrap"><span
                                        class="{{ $tyre->lead_time }}">{{ $tyre->lead_time }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center">No tyres found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-container">
                {{ $tyres->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @include('AutoCare/tyres/stock_history_modal')
        <div class="modal fade" id="ownstockModal" tabindex="-1" aria-labelledby="ownstockModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ownstockModalLabel">Add Ownstock</h5>
                        <button type="button" class="btn-close border-0 bg-transparent" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Ownstock Form -->
                        <form action="{{ route('tyres.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="supplier_name">Supplier Name:</label>
                                <input type="text" name="supplier_name" id="supplier_name" value='ownstock'
                                    class="form-control" required>
                            </div>
                             <!-- Garage Select -->
                            <div class="form-group mb-3 grg_name_fitters">
                                <label for="garage_id">Select Garage:</label>
                                <select name="garage_id" id="garage_id" class="form-control" required>
                                    <option value="">-- Select Garage --</option>
                                    @foreach($garages as $garage)
                                        <option value="{{ $garage->id }}">{{ $garage->garage_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="file_upload">Upload File:</label>
                                <input type="file" name="file_upload" id="file_upload" class="form-control"
                                    accept=".csv,.xlsx,.xls" required>
                            </div>
                            <div class="form-group mb-3">
                                <div class="form-check ownstock_check">
                                    <input type="checkbox" name="delete_existing" id="delete_existing" value="1">
                                    <label for="delete_existing" class="form-check-label">Delete existing ownstock before
                                        uploading</label>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection