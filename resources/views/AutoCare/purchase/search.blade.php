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
@php
 $role_id=Auth::user()->role_id;
@endphp

<section class="content" style="margin-left: 20px;margin-right: 20px;">
  <div class="row">
      <div class="col-sm-12" class="text-center">
         {{ Form::open(['url' => 'AutoCare/purchase/search','files' => 'true' ,'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) }} 
         <div class="table-responsive">
        <table class="table table-sm">
          <tr>
            <td>Purchase Id</td>
            <td>:</td>
            <td>
                {{Form::text('id', isset($id)?$id: '', ['class' => 'form-control-sm form-control-sm ','id'=>'id', 'placeholder' => ' Purchase Id']  )}}
            </td>
            <td>&emsp;&emsp;</td>
            <td>supplier Name</td>
            <td>:</td>
            <td>
              {{Form::text('supplier_name', isset($supplier_name)?$supplier_name: '', ['class' => 'form-control-sm form-control-sm ','id'=>'supplier_name', 'placeholder' => '  supplier Name']  )}}
            <td>&emsp;&emsp;</td>
            <td>Product Name</td>
            <td>:</td>
            <td><input type="text" class="form-control-sm" name="product_name"></td>
            <td>Form Date</td>
            <td>:</td>           
            <td><input type="date" class="form-control-sm" name="created_at_to"></td>
            <td>&emsp;&emsp;</td>
          </tr>
          <tr>          
          <td>To Date</td>
          <td>:</td>
          <td><input type="date" class="form-control-sm" name="created_at_from"></td>
          <td>&emsp;&emsp;</td>
          <td  style="white-space: nowrap">Brand :</td>
          <td>:</td>
          <td> {{Form::select('brand',$brand_select,isset($brand)?$brand: '', ['class' => 'form-control ', 'placeholder' => 'Brand'] )}}</td>
          <td>&emsp;</td>
          <td  style="white-space: nowrap">Model </td>    
          <td>:</td>         
          <td> {{Form::select('model_number',$model_select,isset($model_number)?$model_number: '', ['class' => 'form-control ', 'placeholder' => 'Model Name'] )}}</td>
            </tr>
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
                  {{-- <th style="white-space: nowrap">Sl#</th> --}}
                  <th style="white-space: nowrap">Bill Date</th>
                   <th style="white-space: nowrap">Purchase ID</th>
                  {{-- <th style="white-space: nowrap">Created Date</th> --}}
                  <th style="white-space: nowrap">Supplier Name</th>
                  <th style="white-space: nowrap">Bill Number</th>
                  
                  <th style="white-space: nowrap">Spare Name</th>
                  <th style="white-space: nowrap">Brand Name</th>
                  <th style="white-space: nowrap">Model Number</th>
                  <th style="white-space: nowrap">HSN</th>
                  <th style="white-space: nowrap">Unit Price</th>
                  <th style="white-space: nowrap">Quantity</th>
                  <th style="white-space: nowrap">GST</th>
                  {{-- <th style="white-space: nowrap">Discount</th> --}}
                  <th style="white-space: nowrap">Total Amount</th>
                  {{-- <th style="white-space: nowrap">Created Date</th> --}}
                  {{-- <th style="white-space: nowrap">Updated Date</th> --}}
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($purchase as $key => $value)
                @php
                 if(isset($value['bill_date']))
                    {
                      $bill_date = strtotime($value['bill_date']);
                      $bill_date = date('d/m/Y', $bill_date);
                    }

                    else{
                      $bill_date ="";
                    } 
                @endphp
                <tr>
                  
                  {{-- <td>{{ $loop->iteration }}</td> --}}
                  <td>{{ $bill_date }}</td>
                  <td>{{ $value['id'] }}</td>
                  {{-- <td>{{ $value['created_at'] }}</td> --}}
                  <td>{{ $value['supplier_name_from_supplier'] }}</td>
                  <td>{{ $value['bill_num'] }}</td>                  
                  <td>{{ $value['product_name'] }}</td>
                  <td>{{ $value['company_name_from_brand'] }}</td>
                  <td>{{ $value['model_number'] }}</td>
                  <td>{{ $value['hsn'] }}</td>
                  <td>{{ number_format($value['unit_price'],2,'.','') }}</td>
                  <td>{{ number_format($value['quantity'],2,'.','') }}</td>
                  <td>{{  number_format($value['gst'],2,'.','') }}</td>
                  {{-- <td>{{ $value['discount'] }}</td> --}}
                  <td>{{ number_format($value['total_amount'],2,'.','') }}</td>
                
                  {{-- <td>{{ $value['updated_at'] }}</td> --}}
                  <td style="white-space: nowrap">
                       <a data-toggle="modal" id="{{ $value['id'] }}" data-target="#myModal"  class="btn btn-success openPayentModel btn-sm"><i class="fa fa-undo" aria-hidden="true"></i></a> 
                   <a href="{{ url('/')}}/AutoCare/purchase/add/{{ $value['id'] }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a> 
                   @if($role_id==1)
                      <a style="display: none" href="{{ url('/')}}/AutoCare/purchase/trash/{{ $value['id']}} " class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-remove"></i></a>
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

     $('[name="brand"]').select2();
    $('[name="model_number"]').select2();
    $('[name="customer_id"]').select2();

     $(document).on("change","[name^=brand]",function(){
          var thisSelf=$(this);
      var brand = $(this).val();
      $.ajax({
        type:"POST",
        url: "{{url('/')}}/ajax/getModal",
        data:{
          "_token": "{{ csrf_token() }}",
          brand : brand,
        },
        dataType : 'html',
        cache: false,
        success: function(data){
          modalData=JSON.parse(data);
          // console.log(modalData.id);
          // console.log(modalData.model_name);
             thisSelf.parent().parent().find('[name^=model_number]')
                .empty()
                .append('<option selected="selected" value="">-Select -</option>');
                for (index = 0; index < modalData.length; ++index) {
                $('[name^=model_number]').append(
                '<option value="'+modalData[index]['id']+'">'+modalData[index]['model_name']+'</option>'
              );   
            }
        }
      });
     }); 



   $(document).on("click","#payment",function(){
 
      var quantity = $('[name^=amount]').val();
      var PurchaseId = $('[name^=PurchaseId]').val();
      var comments = $('[name^=comments]').val();
       if(quantity=="")
           {
             swal("warning!", "Please enter Amount", "");
           }
           else
           {
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
            }
     });
      
  
} );

</script>
@endsection