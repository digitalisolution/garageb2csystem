@extends('samples')

@section('content')
<section class="container-fluid">
    @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
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
      <div class="col-sm-12" id="HideForShowProduct">
         <div class="card">
            <div class="card-header">
               <i class="fa fa-align-justify"></i> Vrm Credit
            </div>
            <div class="bg-white p-3">
                <form action="{{ route('AutoCare.vrmsmscredit.index') }}" method="get" class="mb-0">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <input class="form-control" type="date" name="start_date" value="{{ $start_date }}">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                        <input class="form-control" type="date" name="end_date" value="{{ $end_date }}">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                        <select class="form-control" name="reference">
                        <option value="vrm" {{ $reference == 'vrm' ? 'selected' : '' }}>VRM</option>
                        <option value="sms" {{ $reference == 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="autotech" {{ $reference == 'autotech' ? 'selected' : '' }}>Autotech</option>
                    </select>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>

                    </div>
                </form>
            </div>
            <div class="bg-white p-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="alert alert-info text-center">
                        <strong>VRM Credits Purchased:</strong> {{ $total_vrm_credit }} <br>
                        <strong>VRM Used:</strong> {{ $total_vrm_used }} <br>
                        <strong>Remaining:</strong> {{ $remaining_vrm }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info text-center">
                        <strong>SMS Credits Purchased:</strong> {{ $total_sms_credit }} <br>
                        <strong>SMS Used:</strong> {{ $total_sms_used }} <br>
                        <strong>Remaining:</strong> {{ $remaining_sms }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info text-center">
                        <strong>Autodata Credits Purchased:</strong> {{ $total_autodata_credit }} <br>
                        <strong>Autodata Used:</strong> {{ $total_autodata_used }} <br>
                        <strong>Remaining:</strong> {{ $remaining_autodata }}
                    </div>
                </div>
            </div>
            </div>
            @if($show_data)
            <div class="bg-white p-3">
            <div class="table-responsive" style="max-height:236px;overflow-y:auto;">
                <table class="table table-bordered table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                            @if($reference == 'vrm')
                                VRM
                            @elseif($reference == 'sms')
                                Mobile No.
                            @elseif($reference == 'autotech')
                                Vehicle
                            @else
                                Name
                            @endif
                        </th>
                            <th>Credit Used</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->quantity }}</td>
                        <td>{{ $row->date }}</td>
                    </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            </div>
            @endif
        </div>
    </div>
        @php
        $role_id = Auth::user()->role_id;
        @endphp
        @if ($role_id == 1)
        <div class="col-sm-12" id="HideForShowProduct">
         <div class="card">
            <div class="card-header">
            Vrm Credit Buy
            </div>
            <div class="bg-white p-3">
            <form action="{{ route('AutoCare.vrmsmscredit.buy') }}" method="POST">
                @csrf
                <div class="row">
                <div class="col-md-3">
                    <label><b>Credit Type *</b></label>
                    <select class="form-control" name="type" id="credit_type">
                        <option value="">Select Credit Type</option>
                        <option value="vrm">VRM</option>
                        <option value="sms">SMS</option>
                        <option value="autodata">AUTODATA</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label><b>Total Credit *</b></label>
                    <select class="form-control" name="quantity" id="quantity">
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="500">500</option>
                        <option value="600">600</option>
                        <option value="700">700</option>
                        <option value="800">800</option>
                        <option value="900">900</option>
                        <option value="1000">1000</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label><b>Subtotal</b></label>
                    <input type="text" readonly class="form-control" id="subtotal">
                </div>

                <div class="col-md-2">
                    <label><b>Amount including VAT</b></label>
                    <input type="text" readonly class="form-control" id="total_with_vat">
                </div>

                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary">Buy Now</button>
                </div>
                </div>

                <input type="hidden" name="price" id="price">
            </form>
            <br>
            <h6>Purchase History</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Credit Type</th>
                        <th>Total Credit</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseHistory as $key => $p)
                    <tr>
                        <td>{{ strtoupper($p->id) }}</td>
                        <td>{{ strtoupper($p->type) }}</td>
                        <td>{{ $p->quantity }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->created_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach

                    @if(count($purchaseHistory) == 0)
                    <tr>
                        <td colspan="4" class="text-center">No Purchase Found</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            </div>
         </div>
        </div>
        @endif

</section>
<script>
    $("#credit_type, #quantity").on("change", function () {

        let type = $("#credit_type").val();
        let qty = parseInt($("#quantity").val());

        if (!type || !qty) return;

        // PRICE PER CREDIT
        let price = 0;

        if (type === "vrm") price = 0.08;
        if (type === "sms") price = 0.07;
        if (type === "autodata") price = 0.20;

        let subtotal = qty * price;
        let total = subtotal + (subtotal * 0.20);

        $("#price").val(price);
        $("#subtotal").val("£" + subtotal.toFixed(2));
        $("#total_with_vat").val("£" + total.toFixed(2));
    });
</script>

@endsection
