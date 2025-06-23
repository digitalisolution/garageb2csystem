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
          <i class="fa fa-align-justify"></i> supplier Detail
        </div>
        <div class="card-body table-responsive" style="font-size: 13px;padding-left:10px;vertical-align:middle;">
          <table id="datable_1" class="table table-hover">
            <thead class="thead-dark">
              <tr>
                <th style="white-space: nowrap">Id</th>
                <th style="white-space: nowrap">Supplier</th>
                <th style="white-space: nowrap">Mobile</th>
                <th style="white-space: nowrap">Email</th>
                <th style="white-space: nowrap">Address</th>
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
          <td>{{ $value['supplier_name'] }}<br><a href="{{ route('download.csv', ['id' => $value->id]) }}" class="">Download</a></td>
          <td>{{ $value['mob_num'] }}</td>
          <td>{{ $value['email'] }}</td>
          <td>{{ $value['address'] }}</td>
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
        <a href="{{ url('/AutoCare/supplier/trash/' . $value['id']) }}" class="btn btn-danger btn-sm"
        onclick="return confirm('Are you sure you want to delete this user?');">
        <i class="fa fa-remove"></i>
        </a>
      @endif

          </td>
          </tr>
        @endforeach
            </tbody>
          </table>

          <div class="col-lg-12 text-center">

          </div>
          <!-- <li><a href="#" id="json"> <i class="fa fa-print"></i> JSON</a></li>
                                <li><a href="#" onclick="$('#table').tableExport({type:'json',escape:'false'});"><img src="images/json.jpg" width="24px">JSON (ignoreColumn)</a></li> -->
          <!--  <p class="lead"><button id="json" class="btn btn-primary">TO JSON</button> <button id="csv" class="btn btn-info">TO CSV</button>  <button id="pdf" class="btn btn-danger">TO PDF</button></p> -->
        </div>
      </div>
    </div>

  </div>

  <div class="row">

  </div>



  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title text-primary">Supplier Payment</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body">
          {{-- <form id="formId" action="{{  url('/') }}/ajax/submitSupplierDetail" method="POST"> --}}
            {{ csrf_field() }}
            <table class="table">
              <thead>
                <tr>
                  <th>Credit/Debit</th>
                  <th>Amount</th>

                  <th>Payment Date</th>
                  <th>Payment Type</th>
                  <th>Payment Discription</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <select name="creditDebit" class="form-control">
                      <option value="0">Credit</option>
                      <option value="1">Debit</option>
                    </select>
                    <input type="hidden" name="supplierId">
                  </td>
                  <td><input type="number" class="form-control" step="any" name="amount"></td>
                  <td><input type="text" class="form-control" id="payementDate" name="payment_date"></td>
                  <td>
                    {{Form::select('payment_type', ['1' => 'By Cash', '2' => 'By Internate Banking', '3' => 'By Cheque'], isset($payment_type) ? $payment_type : '', ['class' => 'form-control form-control '])}}
                  </td>
                  <td>
                    {{Form::textarea('comments', isset($comments) ? $comments : '', ['class' => 'form-control ', 'id' => 'comments', "style" => "height: 40px;"])}}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td></td>
                  <td></td>
                  <td><input type="submit" id="payment" class="btn btn-sm btn-success"></td>
                  <td></td>
                </tr>
              </tfoot>

            </table>
            {{--
          </form> --}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  <!---- Model end --->

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
@endsection