@extends('samples') 
@section('content')

<section class="container-fluid">
  <div class="bg-white p-3 mb-3">
    {{ Form::open(['url' => 'AutoCare/supplier/search', 'files' => 'true', 'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) }}
    <div class="row">
      <div class="form-group col-lg-3 col-md-6 col-12">
        <label>Supplier Id:</label>
        <!-- <input type="text" class="form-control-sm" name="id"> -->
        {{Form::text('id', isset($id) ? $id : '', ['class' => 'form-control', 'id' => 'id', 'placeholder' => 'Supplier Id'])}}
      </div>
      <div class="form-group col-lg-3 col-md-6 col-12">
        <label>Supplier Name:</label>
        {{Form::text('supplier_name', isset($supplier_name) ? $supplier_name : '', ['class' => 'form-control', 'id' => 'supplier_name', 'placeholder' => 'Supplier Name'])}}
      </div>
      <div class="form-group col-lg-3 col-md-6 col-12">
        <label>From Date:</label>
        <input type="date" class="form-control" name="created_at_from">
      </div>
      <div class="form-group col-lg-3 col-md-6 col-12">
        <label>To Date:</label>
        <input type="date" class="form-control" name="created_at_to">
      </div>
      <div class="form-group col-lg-12 col-md-12 col-12 text-right mt-2">
        <input type="submit" name="search" class="btn btn-primary" value="Search">
      </div>
    </div>
    {{Form::close()}}
  </div>
  <div class="row">
    <!-- left column -->
    <div class="col-md-12 col-sm-12">
      <!-- general form elements -->
      <div class="box box-primary">
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
          @if ($errors->any())
        <ul class="alert alert-danger" style="list-style:none">
        @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
        </ul>
      @endif
          @if(session()->has('message.level'))
        <div class="alert alert-{{ session('message.level') }} alert-dismissible"
        onload="javascript: Notify('You`ve got mail.', 'top-right', '5000', 'info', 'fa-envelope', true); return false;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h4>
        {!! session('message.content') !!}
        </div>
      @endif
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i>Supplier Detail
          <a href="AutoCare/supplier/add" class="btn btn-primary text-center float-right"><i class="fa fa-plus"></i> Add Supplier</a>
        </div>
        <div class="card-body table-responsive" style="font-size: 13px;padding-left:10px;vertical-align:middle;">
        <table id="datable_1" class="table table-hover">
            <thead class="thead-dark">
              <tr>
                <th style="white-space: nowrap">Id</th>
                <th style="white-space: nowrap">Garage Name</th>
                <th style="white-space: nowrap">Supplier</th>
                <th style="white-space: nowrap">Mobile</th>
                <th style="white-space: nowrap">Email</th>
                <th style="white-space: nowrap">Website Display</th>
                <th style="white-space: nowrap">Status</th>
                <th style="white-space: nowrap">Products</th>
                <th style="white-space: nowrap">Created Date</th>
                <th style="white-space: nowrap">Updated Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($supplier as $key => $value)
          <tr>
          <td>{{ $value['id'] }}</td>
          <td>{{ $value->garage ? $value->garage->garage_name : '-' }}</td>
          <td>{{ $value['supplier_name'] }}<br><a href="{{ route('download.csv', ['id' => $value->id]) }}" class="">Download</a></td>
          <td>{{ $value['mob_num'] }}</td>
          <td>{{ $value['email'] }}</td>
           <td>
            <label class="switch">
              <input type="checkbox"
                    class="toggleWebsiteStatus"
                    data-id="{{ $value['id'] }}"
                    {{ $value['website_display_status'] ? 'checked' : '' }}>
              <span class="slider round"></span>
            </label>
          </td>
          <td>
            <span class="badge {{ $value['status'] ? 'bg-success' : 'bg-danger' }}">
            {{ $value['status'] ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td>{{ $value->products_count }}</td>
          <td>{{ $value['created_at'] }}</td>
          <td>{{ $value['updated_at'] }}</td>
          <td style="white-space: nowrap">
            <a href="{{ route('supplier.install', $value['id']) }}" class="btn btn-success btn-sm">
            Install
            </a>
            @if($value['supplier_name'] != 'ownstock')
            <a href="{{ route('supplier.uninstall', $value['id']) }}" class="btn btn-danger btn-sm">
            Uninstall
            </a>
          @endif
                <a href="{{ url('/AutoCare/supplier/add/' . $value['id']) }}" class="btn btn-success btn-sm">
                <i class="fa fa-edit"></i>
                </a>
                @if($value['supplier_name'] != 'ownstock')
            <a href="{{ url('/')}}/AutoCare/supplier/delete/{{ $value['id']}}" class="btn btn-danger btn-sm"
             onclick="return confirm('Are you sure you want to delete this supplier?');">
            <i class="fa fa-remove"></i>                 
            @endif

              </td>
              </tr>
            @endforeach
            </tbody>
        </table>

          <div class="col-lg-12 text-center">

          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<script src="{{ asset('alerts-boxes/js/sweetalert.min.js') }}"></script>
<script type="text/javascript">


  $(document).ready(function () {
    $('#payementDate').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true,
      endDate: '+0d',
    });

    $(document).on('click', '.openPayentModel', function () {
      var supplierId = $(this).attr('id');
      $('[name="supplierId"]').val(supplierId)
    });



    //       $(document).on("click","#payment",function(){
    //   var frm = $('#formId');
    //         frm.submit(function (e) {
    //             e.preventDefault();
    //             $.ajax({
    //                 type: frm.attr('method'),
    //                 url:  frm.attr('action'),
    //                 data: frm.serialize(),
    //                 success: function (data) {
    //                   if(data==1)
    //                   {
    //                     swal("Good job!", "Payment Discription  Successfully Added. You Can Check in  Log Section", "success");
    //                     frm.reset();
    //                   }
    //                   else
    //                   {
    //                     swal("Somthing Wrong!", "OOHooooo!!!!!", "error");
    //                   }
    //                 },
    //                 error: function (data) {
    //                   swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
    //                 },
    //             });
    //         });
    // })


    $(document).on("click", "#payment", function () {

      var creditDebit = $('[name^=creditDebit]').val();
      var supplierId = $('[name^=supplierId]').val();
      var amount = $('[name^=amount]').val();
      var payment_date = $('[name^=payment_date]').val();
      var payment_type = $('[name^=payment_type]').val();
      var comments = $('[name^=comments]').val();
      if (comments == "") {
        swal("warning!", "Please enter Amount", "");
      }
      else {
        $.ajax({
          type: "POST",
          url: "{{  url('/') }}/ajax/submitSupplierDetail",
          data: {
            "_token": "{{ csrf_token() }}",
            creditDebit: creditDebit,
            supplierId: supplierId,
            amount: amount,
            payment_date: payment_date,
            payment_type: payment_type,
            comments: comments,
          },
          dataType: 'html',
          cache: false,
          success: function (data) {
            if (data == 1) {
              $('[name^=amount]').val("");
              swal("Good job!", "Payment Discription  Successfully Added. You Can Check in  Log Section", "success");
              // $('#formId')[0].reset();
            }
            else {
              swal("Somthing Wrong!", "OOHooooo!!!!!", "error");
            }
          },
          error: function (data) {
            swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
          }


        });
      }
    });
  });
</script>
<script>
  $(document).on('change', '.toggleWebsiteStatus', function () {
  let supplierId = $(this).data('id');
  let status = $(this).is(':checked') ? 1 : 0;

  $.ajax({
    url: "{{ url('/AutoCare/supplier/toggle-website-status') }}",
    type: "POST",
    data: {
      _token: "{{ csrf_token() }}",
      supplier_id: supplierId,
      website_display_status: status
    },
    success: function (response) {
      if (response.status) {
        swal("Success!", response.message, "success");
      } else {
        swal("Error!", "Something went wrong", "error");
      }
    },
    error: function () {
      swal("Error!", "Unable to update status", "error");
    }
  });
});
</script>
@endsection