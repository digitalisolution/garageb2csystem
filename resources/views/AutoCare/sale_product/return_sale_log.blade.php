{{-- /*
 *  File Name              :
 *  Type                   :   
 *  Description            :   
 *  Author                 : Ashtosh Kumar Choubey
 *  Contact                : 9658476170
 *  Email                  : contact@worldgyan.com
 *  Date                   : 12/12/2018  
 *  Modified By            :       
 *  Date of Modification   :     
 *  Purpose of Modification: 
 * 
 */ --}} 
@extends('samples') 
@section('content')

<section class="content" style="margin-left: 20px;margin-right: 20px;">
  <div class="row">
      <div class="col-sm-12" class="text-center">
         {{ Form::open(['url' => 'AutoCare/sale/sale_return','files' => 'true' ,'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) }} 
         <div class="table-responsive">
        <table class="table table-sm">
          <tr>
            <td>Return Id</td>
            <td>:</td>
            <td><!-- <input type="text" class="form-control-sm" name="id"> -->
                {{Form::text('id', isset($id)?$id: '', ['class' => 'form-control-sm form-control-sm ','id'=>'id', 'placeholder' => ' Return Id']  )}}
            </td>
            <td>&emsp;&emsp;</td>
            <td>Job Id</td>
            <td>:</td>
            <td>
              {{Form::text('supplier_name', isset($supplier_name)?$supplier_name: '', ['class' => 'form-control-sm form-control-sm ','id'=>'supplier_name', 'placeholder' => '  supplier Name']  )}}
            <td>&emsp;&emsp;</td>
           {{--  <td>Product Name</td>
            <td>:</td>
            <td><input type="text" class="form-control-sm" name="product_name"></td>
            <td></td>
          </tr> --}}
          <tr>
            <td>Form Date</td>
            <td>:</td>
            <td><input type="date" class="form-control-sm" name="created_at_to"></td>
            <td>&emsp;&emsp;</td>
            <td>To Date</td>
            <td>:</td>
            <td><input type="date" class="form-control-sm" name="created_at_from"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="13" class="text-center"><input type="submit" name="search" class="btn btn-primary" value="Search"></td>
          </tr>
        </table>
        </div>
         {{Form::close()}}
      </div>
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
            <div class="alert alert-{{ session('message.level') }} alert-dismissible" onload="javascript: Notify('You`ve got mail.', 'top-right', '5000', 'info', 'fa-envelope', true); return false;">
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
            <i class="fa fa-align-justify"></i> Purchase Details
          </div>
          <div class="card-body table-responsive" style="font-size: 13px;padding-left:10px;vertical-align:middle;">
            <table id="datable_1" class="table   table-hover  " style="font-size: 13px;display:table-cell;" >
              <thead class="thead-dark">
                <tr>
                  <th style="white-space: nowrap">Return ID</th>
                  <th style="white-space: nowrap">Job Id</th>
                  <th style="white-space: nowrap">Product Name</th>
                  <th style="white-space: nowrap">Quantity</th>
                  {{-- <th style="white-space: nowrap">Bill Date</th>
                  <th style="white-space: nowrap">Supplier Name</th>
                  <th style="white-space: nowrap">Bill Number</th>                  
                  <th style="white-space: nowrap">Spare Name</th>
                  <th style="white-space: nowrap">Brand Name</th>
                  <th style="white-space: nowrap">Model Number</th>
                  <th style="white-space: nowrap">HSN</th>
                  <th style="white-space: nowrap">Unit Price</th>
                  <th style="white-space: nowrap">Quantity</th> --}}
                  <th style="white-space: nowrap">Comment</th>
                   <th style="white-space: nowrap">Returned Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($purchase as $key => $value)
                <tr>
                  <td>{{ $value['id'] }}</td>
                  <td>{{ $value['job_id'] }}</td>
                   <td>{{ $value['ProductName'] }}</td>
                   <td>{{ $value['quantity'] }}</td>
                  {{-- <td>{{ $value['bill_date'] }}</td>
                  <td>{{ $value['supplier_name_from_supplier'] }}</td>
                  <td>{{ $value['bill_num'] }}</td>
                  <td>{{ $value['product_name'] }}</td>
                  <td>{{ $value['company_name_from_brand'] }}</td>
                  <td>{{ $value['model_number'] }}</td>
                  <td>{{ $value['hsn'] }}</td>
                  <td>{{ $value['unit_price'] }}</td>
                  <td>{{ $value['ReturnedQuantity'] }}</td> --}}
                  <td>{{ $value['comments'] }}</td>
                  <td>{{ $value['created_at'] }}</td>
                 
                 {{--  <td style="white-space: nowrap">
                       <a data-toggle="modal" id="{{ $value['id'] }}" data-target="#myModal"  class="btn btn-success openPayentModel btn-sm"><i class="fa fa-undo" aria-hidden="true"></i></a> 
                   <a href="{{ url('/')}}/AutoCare/purchase/add/{{ $value['id'] }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a> 
                      <a href="{{ url('/')}}/AutoCare/purchase/trash/{{ $value['id']}} " class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-remove"></i></a>
                  </td> --}}
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
                  
 


  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
           <h4 class="modal-title text-primary">Purchase Return</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
         
        </div>
        <div class="modal-body">
          {{-- <form  id="formId" action="{{  url('/') }}/ajax/submitSupplierDetail" method="POST"> --}}
             {{ csrf_field() }}
          <table class="table">
            <thead>
              <tr>
                <th>Quantity</th>
                <th>Discription</th>
              </tr>
            </thead>
            <tbody>
              <tr>
               
                  <input type="hidden" name="PurchaseId">
               
                <td><input type="number" class="form-control" step="any" name="amount"></td>
                <td >{{Form::textarea('comments', isset($comments)?$comments: '', ['class' => 'form-control ', 'id' => 'comments',"style"=>"height: 40px;"])}}
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
          {{-- </form> --}}
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

$(document).on('click', '.openPayentModel', function(){  
          var PurchaseId = $(this).attr('id');
          $('[name="PurchaseId"]').val(PurchaseId)
      }); 


  $(document).ready(function() {

   $(document).on("click","#payment",function(){
 
      var quantity = $('[name^=amount]').val();
      var PurchaseId = $('[name^=PurchaseId]').val();
      var comments = $('[name^=comments]').val();

      $.ajax({
        type:"POST",
        url: "{{  url('/') }}/ajax/submitPurchaseReturn",
        data:{
              "_token": "{{ csrf_token() }}",
              PurchaseId : PurchaseId,
              quantity : quantity,
              comments : comments,
        },
        dataType : 'html',
        cache: false,
        success: function(data){
           if(data==1)
                  {
                    swal("Good job!", "Purchase Returned Successfully . Now Quantity Has Been decrimented", "success");
                      $('[name^=PurchaseId]').val("");
                      $('[name^=comments]').val("");
                  }
                  else
                  {
                    swal("Somthing Wrong!", "OOHooooo!!!!!", "error");
                  }
                },
                error: function (data) {
                  swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
                }
        
        
      });
     });
      
  
} );

</script>
@endsection