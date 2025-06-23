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

<section class="container-fluid" style="margin-left: 10px;margin-right: 10px;">
<div class="card border-info">
  {{ isset($id)?Form::open(['url' => 'AutoCare/sale/update','files' => 'true' ,'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) : Form::open(['url' => 'AutoCare/sale/create','files' => 'true' ,'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) }}
   {{ csrf_field() }}
   {{ Form::hidden('id', isset($id) ? $id :'', []) }} 
    <div class="card-header text-center">
            <h6 class="box-title text-primary ">Please Fill Up Spare  details</h6>
    </div>
    @php
     // $workshopId  isset($workshopId) ? $workshopId:null 
    @endphp
<div class="card-body">
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
              <h6><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h6>
              {!! session('message.content') !!}
              @if(isset($id))
              <div class="text-center text-primary"><a  target="blank" href="{{url('/')}}/AutoCare/workshop/view/{{ isset($id) ? $id : '' }}">Show Detail</a></div>
                <script type="text/javascript">
                $(document).ready(function(){
                  setTimeout(function(){
                   $('#openWorkshopDetail').trigger('click');
                   var newTab = window.open("{{url('/')}}/AutoCare/workshop/view/{{ isset($id) ? $id : '' }}", "_blank");
                      newTab.location;
                   console.log("Worshop Detail Opened In New Tab");
                  },1000)
                })
                
              </script>
              @else
               <div class="text-center text-primary"><a target="blank" href="{{url('/')}}/AutoCare/workshop/view/{{ isset($workshopId) ? $workshopId : '' }}">Show Detail</a></div>
                <script type="text/javascript">
                 $(document).ready(function(){
                  setTimeout(function(){
                   $('#openWorkshopDetail').trigger('click');
                    var newTab = window.open("{{url('/')}}/AutoCare/workshop/view/{{ isset($workshopId) ? $workshopId : '' }}", "_blank");
                      newTab.location;
                   console.log("Worshop Detail Opened In New Tab");
                  },1000)
                })
              </script>
               @endif
            </div>
           
            @endif
          </div>
        </div>
      </div>
    </div>
 
    <div class="box-header with-border text-center">
      {{-- <h6 class="box-title text-primary "></h6> --}}
    </div>
    <div class="card card-accent-info">
      <div class="card-header">
        <div class="box-header with-border ">
          <h6 class="box-title  ">Contact Detail</h6>
        </div>
      </div> 
      <div class="card-body"> 
        <div class=" form-group row">
          @if(!isset($id))
          <div class="col-md-3">            
              <label class="control-label"  for="name"> Select Customer:&emsp;</label>
              {{Form::select('customer_id', $customerNameSelect,isset($customer_id)?$customer_id: '', ['class' => 'form-control','id'=>'customer_id','placeholder'=>'Select Customer']  )}}
              <div class="invalid-feedback">
              {{ $errors->has('customer_id') ? $errors->first('customer_id', ':message') : '' }}
              </div>           
          </div>
          @endif
          <div class="col-md-3">
            
              <label class="control-label"  for="name"> Name:&emsp;</label>
              {{Form::text('name', isset($name)?$name: '', ['class' => 'form-control','id'=>'name','required', 'placeholder' => '  Name']  )}}
              <div class="invalid-feedback">
              {{ $errors->has('name') ? $errors->first('name', ':message') : '' }}
              </div>
           
          </div>
          
          <div class="col-md-3">
           
              <label class="control-label"  for="company">Company:&emsp;</label>
              {{Form::text('company',isset($company)?$company: '', ['class' => 'form-control ', 'placeholder' => 'Company'] )}}
              <div class="invalid-feedback">
                {{ $errors->has('company') ? $errors->first('company', ':message') : '' }}
              </div>
          
          </div> 
           
          
          <div class="col-md-3" style="display: none">           
              <label class="control-label"  for="reference">Reference:&emsp;</label>
              {{Form::text('reference',isset($reference)?$reference: '', ['class' => 'form-control', 'placeholder' => 'Reference'] )}}
              <div class="invalid-feedback">
                {{ $errors->has('reference') ? $errors->first('reference', ':message') : '' }}
              </div>           
          </div>
           <div class="col-md-3">
            <div class="form-group">
              <label class="control-label"  for="brand">Brand:&emsp;</label>
              {{Form::select('brand',$brand_select,isset($brand)?$brand: '', ['class' => 'form-control ', 'placeholder' => 'Brand'] )}}
              <div class="invalid-feedback">
              {{ $errors->has('brand') ? $errors->first('status', ':message') : '' }}
              </div>
            </div>
          </div> 
        </div>
         <div class="form-group row">
        <div class="col-md-3">
         
          <label class="control-label"  for="mobile">Contact Number:&emsp;</label>
          {{Form::number('mobile', isset($mobile)?$mobile: '', ['class' => 'form-control ','id'=>'mobile', 'placeholder' => ' mobile']  )}}
          <div class="invalid-feedback">
          {{ $errors->has('mobile') ? $errors->first('mobile', ':message') : '' }}
        
          </div>
        </div>
        <div class="col-md-3">
        
            <label class="control-label"  for="email">Email:&emsp;</label>
            {{Form::email('email',isset($email)?$email: '', ['class' => 'form-control ', 'placeholder' => 'Email'] )}}
            <div class="invalid-feedback">
            {{ $errors->has('email') ? $errors->first('email', ':message') : '' }}
            </div>
         
        </div> 
         <div class="col-md-3" style="display: none">
        
            <label class="control-label"  for="landline" >Alternate Mobile Number:&emsp;</label>
            {{Form::text('landline',isset($landline)?$landline: '', ['class' => 'form-control ', 'placeholder' => 'landline'] )}}
            <div class="invalid-feedback">
            {{ $errors->has('landline') ? $errors->first('landline', ':message') : '' }}
            </div>
         
        </div> 
        <div class="col-md-3">
                            <div class="form-group">
                              <label class="control-label"  for="model_number">Model :&emsp;</label>
                              {{Form::select('model_number',$model_select,isset($model_number)?$model_number: '', ['class' => 'form-control ', 'placeholder' => 'Model Name'] )}}
                              <div class="invalid-feedback">
                              {{ $errors->has('model_number') ? $errors->first('model_number', ':message') : '' }}
                              </div>
                            </div>
                          </div> 
      </div>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label"  for="address">Address:&emsp;</label>
              {{Form::textarea('address',isset($address)?$address: '', ['class' => 'form-control', 'placeholder' => 'Address','style' => 'height:50px'] )}}
              <div class="invalid-feedback">
              {{ $errors->has('address') ? $errors->first('address', ':message') : '' }}
              </div>
            </div>
          </div> 
     {{--  @if(isset($id)) --}}
     {{--  <div class="col-md-3">
        <div class="form-group">
          <label class="control-label"  for="status">Status:&emsp;</label>
          {{Form::text('status',isset($status)?$status: '', ['class' => 'form-control', 'placeholder' => 'Progress,Complete','style' => 'height:50px'] )}}
          <div class="invalid-feedback">
          {{ $errors->has('status') ? $errors->first('status', ':status') : '' }}
          </div>
        </div>
      </div>  --}}
      <div class="col-md-3" style="display:none">
        <div class="form-group">
          <label class="control-label"  for="status">Is Complete:&emsp;:&emsp;</label><br>
         <input type="radio" name="is_complete" checked  value="1">Yes<br>
          <div class="invalid-feedback">
          {{ $errors->has('status') ? $errors->first('status', ':status') : '' }}
          </div>
        </div>
      </div> 
      
      {{-- @endif --}}
      <div class="col-md-3">
            
              <label class="control-label"  for="gst_no">GST Number:&emsp;</label>
              {{Form::text('gst_no',isset($gst_no)?$gst_no: '', ['class' => 'form-control', 'placeholder' => 'Gst No'] )}}
              <div class="invalid-feedback">
                {{ $errors->has('gst_no') ? $errors->first('gst_no', ':message') : '' }}
              </div>
           
          </div> 
         <div class="col-md-3">
          <div class="form-group">
            <label class="control-label"  for="vehicle_reg_number">Vehicle Reg Number:&emsp;</label>
            {{Form::text('vehicle_reg_number',isset($vehicle_reg_number)?$vehicle_reg_number: '', ['class' => 'form-control ', 'placeholder' => 'Vehicle Reg Number','autocapitalize'=>'word','onkeyup'=>'this.value = this.value.toUpperCase()','style'=>'text-transform: uppercase;'] )}}
            <div class="invalid-feedback">
            {{ $errors->has('vehicle_reg_number') ? $errors->first('address', ':message') : '' }}
            </div>
          </div>
        </div> 
           <div class="col-md-3">
            <label class="control-label"  for="workshop_date">Workshop Date:&emsp;</label>
            {{Form::text('workshop_date',isset($workshop_date)?$workshop_date: '', ['class' => 'form-control','id'=>'created_at','placeholder' => 'Workshop Date','data-date-format'=>'DD-MM-YYYY HH:mm:ss'] )}}
            <div class="invalid-feedback">
              {{ $errors->has('workshop_date') ? $errors->first('workshop_date', ':message') : '' }}
            </div>
          </div>     
    </div>
       </div>
  
    </div>
 

          <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">Spare</div>
            <div class="card-body table-responsive">
              <table class="table">
               <thead>
                 <th style="white-space: nowrap" >Brand Name&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                 <th style="white-space: nowrap" >Modal Name&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                 <th style="white-space: nowrap" >Spare Name &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                 <th style="white-space: nowrap"> Quantity&emsp;&emsp;</th>
                 <th style="white-space: nowrap" >Unit Price&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                 <th style="white-space: nowrap" >Action</th>
               </thead>
               <tbody id="tBodyForProductTable">
                @if(isset($workshopProductData))
                @php
                  $incrimentedId=0
                @endphp
                  @foreach($workshopProductData as $key=> $value)
                  <tr id="AddRowForProduct{{ $incrimentedId }}">
                     <td>
                     {{Form::select('workshop_product_brand[]',$brand_select,isset($value['workshop_product_brand'])?$value['workshop_product_brand']: '', ['class' => 'form-control ',isset($id)?'disabled="true"':'', 'placeholder' => 'Brand'] )}}                        
                           {{ Form::hidden('workshop_product_brand[]', isset($id) ? $value['workshop_product_brand'] :'', []) }} 
                  </td>
                  <td>
                     {{Form::select('workshop_product_model[]]',$model_select,isset($value['workshop_product_model'])?$value['workshop_product_model']: '', ['class' => 'form-control ',isset($id)?'disabled="true"':'', 'placeholder' => 'Model Name'] )}}
                     {{ Form::hidden('workshop_product_model[]', isset($id) ? $value['workshop_product_model'] :'', []) }} 
                  </td>
                    <td  class="service_name">{{Form::select('product_id[]',$product, isset($value['product_id'])?$value['product_id']: '', ['class' =>  'form-control selectTo','required'=>'true','placeholder'=>'Select' ,isset($id)?'disabled="true"':'','id' => 'product_id'])}}
                       {{ Form::hidden('product_id[]', isset($id) ? $value['product_id'] :'', []) }} 
                    </td>
                    <td>
                       {{Form::number('product_quantity[]',isset($value['product_quantity'])?$value['product_quantity']:'',['class'=>'form-control','required',isset($id)?'disabled="true"':''])}}
                        {{ Form::hidden('product_quantity[]', isset($id) ? $value['product_quantity'] :'', []) }} 
                    </td>
                     <td >
                      {{Form::number('product_price[]',isset($value['product_price'])?$value['product_price']:'',['class'=>'form-control','required',isset($id)?'disabled="true"':''])}}
                       {{ Form::hidden('product_price[]', isset($id) ? $value['product_price'] :'', []) }} 
                      {{-- <div><input type="number" id="product_price" name="product_price[]" class="form-control" required="true"></div> --}}
                    </td>
                    <td><a href="javascript:void(0)" id="{{ $incrimentedId }}"   class="removeRowForProduct btn btn-danger btn-sm"><i class="fa fa-minus "></i></a></td>
                   
                  </tr>
                  @php
                  $incrimentedId++
                @endphp
                  @endforeach
                 @else
                  <tr class="estimatedCost" >
                    <td>
                       {{Form::select('workshop_product_brand[]',$brand_select,isset($workshop_product_brand)?$workshop_product_brand: '', ['class' => 'form-control selectToW1', 'placeholder' => 'Brand'] )}}
                    </td>
                    <td>
                       {{Form::select('workshop_product_model[]]',$model_select,isset($workshop_product_model)?$workshop_product_model: '', ['class' => 'form-control selectToW2', 'placeholder' => 'Model Name'] )}}
                    </td>
                      <td  class="service_name">{{Form::select('product_id[]',$product, isset($product_id)?$product_id: '', ['class' =>  'form-control selectToJ1','required'=>'true','placeholder'=>'Select' ,'id' => 'product_id'])}}
                      </td>
                      <td>
                         {{Form::number('product_quantity[]',isset($product_quantity)?$product_quantity:'',['class'=>'form-control','required'])}}
                      </td>
                       <td  ><input type="number" id="product_price" name="product_price[]" class="form-control" required="true"></div>
                      </td>
                      <td>
                      <a href="javascript:void(0)"  class="addMoreForProduct btn btn-primary btn-sm"><i class="fa fa-plus "></i></a>
                      </td>        
                  </tr> 
                @endif
               </tbody>
               @if(isset($id))
               <tfoot>
               <tr>
               <td>
                      <a href="javascript:void(0)"  class="addMoreForProduct btn btn-primary btn-sm"><i class="fa fa-plus "></i></a>
                      </td> 
                    </tr>
                  </tfoot>
               @else
                <tr>
            
                </tr>
               @endif
           
              </table>

            </div>
            <div class="card-footer ">
                <div class="row" >
                 
                  <div class="col-sm-4">Total Product Price : <b id="total_Product_amount"></b></div>
                  <div class="col-sm-4">&emsp;</div>
                   <div class="col-sm-4">Grand Total : <b id="total_grand_amount"></b></div>
                </div>
            </div>
          </div>
        </div>  
        </div>
                <div class="row">
                      @if(!isset($id))
                        <div class="col-md-3" >
                          <div class="form-group" >
                            <label class="control-label"  for="paid_price">Advance Received:&emsp;</label>
                             {{Form::number('paid_price',isset($paid_price)?$paid_price: 0, ['class' => 'form-control ','multiple'=>'' ,'step'=>'any','required'=>'true'])}}
                            <div class="invalid-feedback">
                            {{ $errors->has('paid_price') ? $errors->first('paid_price', ':message') : '' }}
                            </div>
                          </div>
                        </div>
                      @endif
                        <div class="col-md-3" >
                          <div class="form-group" >
                            <label class="control-label"  for="paid_price">Discount:&emsp;</label>
                             {{Form::number('discount_price',isset($discount_price)?$discount_price: 0, ['class' => 'form-control ','multiple'=>'' ,'step'=>'any','required'=>'true'])}}
                            <div class="invalid-feedback">
                            {{ $errors->has('paid_price') ? $errors->first('paid_price', ':message') : '' }}
                            </div>
                          </div>
                        </div>
                       
                          
                </div>

      </div>
    </div>
  </div>
  </div>
</div>
</div>
  <!-- <div class="row">
   
  </div> -->
  <div class="card-footer">
    <div class="row">
      <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-sm btn-primary" name=""> <i class="fa fa-dot-circle-o"></i>{{isset($id) ? 'Update' :'Add' }}</button> 
        <button type="reset" class="btn btn-sm btn-danger" name=""> <i class="fa fa-ban"></i> Reset</button> 
      </div>
    </div>
  </div>  
                  
   {{Form::close()}}
 </div>
</section>
<script type="text/javascript">
$(window).on('load', function() {
  var today = new Date();
    $('#created_at').datepicker({
       format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
      });      
})

  $(document).ready(function(){
     $('[name="brand"]').select2();
    $('[name="model_number"]').select2();

    setInterval(function(){
      var TotalProduct=0;
      var total_service_amount=0;
       var total_grand_amount=0;
     $("[name^='product_price']")
              .map(function(){
                if(!isNaN(parseFloat($(this).val())))
                {
                  // console.log($(this).parent().parent().find('[name^=product_quantity]').val())
                  TotalProduct+=parseFloat($(this).val()) * parseFloat($(this).parent().parent().find('[name^=product_quantity]').val());
                }
                return parseFloat($(this).val());
              }).get();

              $("[name^='service_price']")
              .map(function(){
                if(!isNaN(parseFloat($(this).val())))
                {
                  total_service_amount+=parseFloat($(this).val()) * parseFloat($(this).parent().parent().find('[name^=service_quantity]').val());
                }
                return parseFloat($(this).val());

              }).get();
              if(!isNaN(TotalProduct))
              {
                 $('[id=total_Product_amount]').html(TotalProduct);
              } 
               if(!isNaN(total_service_amount))
              {
                 $('[id=total_service_amount]').html(total_service_amount);
              } 
              total_grand_amount+=total_service_amount+TotalProduct;
                if(!isNaN(total_grand_amount))
              {
                 $('[id=total_grand_amount]').html(total_grand_amount);
              } 
    },1000)


     // $('[name^=customer_id]').selectize({
     //      create: false,
     //      sortField: 'text'
     //    });

     $('[name="brand"]').select2();
      $('[name="model_number"]').select2();
        $('[name="customer_id"]').select2();
var selectTo="{{ isset($id) ? $id:null }}";
      if(selectTo=="")
      {
        $('.selectToJ1').select2();
        $('.selectToW1').select2();
        $('.selectToW2').select2();
        $('.selectToJ2').select2();
        $('.selectToWS1').select2();
        $('.selectToWS2').select2();
      }
        
      // $('#supplier_name').select2();
    $(document).on("click","#selDocForProduct",function () {
 //   $('#docForProduct').show();
     var x = document.getElementById("docForProduct");
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  //  $('#docForProduct').toggle();
});

       $(document).on("click","#selDocForProduct",function () {
 //   $('#docForProduct').show();
     var x = document.getElementById("docForProduct");
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  //  $('#docForProduct').toggle();
});


    var i=0;
    var j=0;
    @if(isset($id))
    var i={{ $incrimentedId }};

    @endif

      $('.addMoreForProduct').on("click",function(){
        i=parseFloat(i)+1;
          $("#tBodyForProductTable").append('<tr id="AddRowForProduct'+i+'">\
          <td >{{Form::select("workshop_product_brand[]",$brand_select, isset($workshop_product_brand)?$workshop_product_brand: "", ["class"=>"form-control selectToW1","required"=>"true","placeholder"=>"Select" ])}}\
          </td>\
           <td >{{Form::select("workshop_product_model[]",$model_select, isset($workshop_product_model)?$workshop_product_model: "", ["class"=>"form-control selectToW2","required"=>"true","placeholder"=>"Select" ])}}\
          </td>\
          <td >{{Form::select("product_id[]",$product, isset($product_id)?$product_id: "", ["class"=>"form-control product_id selectToJ1","required"=>"true","placeholder"=>"Select" ])}}\
          </td>\
          <td>{{Form::number("product_quantity[]",isset($product_quantity)?$product_quantity:"",["class"=>"form-control ","required"])}}</td>\
          <td><input type="number" id="product_price" name="product_price[]" class="form-control" required="true"></div>\
          </td>\
          <td>\
          <a href="javascript:void(0)"  id="'+i+'"  class="removeRowForProduct btn btn-danger btn-sm"><i class="fa fa-minus "></i></a>\
          </td>   \
          </tr>');
     
     $('.selectToJ1').select2();
     $('.selectToW1').select2();
     $('.selectToW2').select2();
     
     // $('.selectTo').select2();
      });

      $('.addMoreForService').on("click",function(){
        j=parseFloat(j)+1;       
          $("#tBodyForServiceTable").append('<tr id="AddRowForService'+j+'">\
             <td >{{Form::select("workshop_service_brand[]",$brand_select, isset($workshop_service_brand)?$workshop_service_brand: "", ["class"=> "form-control selectToWS1","required"=>"true","placeholder"=>"Select" ])}}\
          </td>\
           <td >{{Form::select("workshop_service_model[]",$model_select, isset($workshop_service_model)?$workshop_service_model: "", ["class"=> "form-control selectToWS2 ","required"=>"true","placeholder"=>"Select" ])}}\
          </td>\
          <td class="service_type">{{Form::select("service_type_id[]",$ServiceType, isset($service_type)?$service_type: "", ["class"=> "form-control ","required"=>"true","placeholder"=>"Select" ,"id" => "service_type"])}}\
          </td>\
          <td class="service_name">{{Form::select("service_id[]",$service, isset($service_id)?$service_id: "", ["class"=> "form-control selectToJ2","required"=>"true","placeholder"=>"Select" ])}}\
          </td>\
           <td>{{Form::text("service_quantity[]",isset($service_quantity)?$service_quantity:"",["class"=>"form-control","required"])}}</td>\
          <td ><input type="number" id="service_price" name="service_price[]" class="form-control" required="true"></div>\
          </td>\
          <td>\
          <a href="javascript:void(0)" id="'+j+'"  class="removeRowForService  btn btn-danger btn-sm"><i class="fa fa-minus "></i></a>\
          </td>   \
          </tr>');
        
        $('.selectToJ2').select2();
        $('.selectToWS1').select2();
        $('.selectToWS2').select2();

          
      });

      $(document).on('click', '.removeRowForProduct', function(){  
          var button_id = $(this).attr("id");  
          $('#AddRowForProduct'+button_id+'').remove();  
      }); 

      $(document).on('click', '.removeRowForService', function(){  
          var button_id = $(this).attr("id");  
          $('#AddRowForService'+button_id+'').remove();  
      }); 

      //  $(document).on('change', '[name^=service_id]', function(){  
      //    var service_id=$(this).val();
      //    var thisSelf=$(this);
      //        $.ajax({
      //        type: "POST",
      //        url: "{{url('/')}}/ajax/getService",
      //        data: { 
      //                "_token": "{{ csrf_token() }}",
      //               service_id : service_id,
      //              },
      //        dataType : 'html',
      //        cache: false,
      //        success: function(data){
      //           var serviceDetail=JSON.parse(data);
      //           price=serviceDetail.price;
      //           thisSelf.parent().parent().find('[name="service_price"]').val(price);
                 
      //        }             
      //    }); 
      // }); 

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
        //  console.log(modalData.id);
        //  console.log(modalData.model_name);
            $('[name^=model_number]')
                .empty()
                .append('<option selected="selected" value="">-Select -</option>');
                for (index = 0; index < modalData.length; ++index) {
                $('[name^=model_number]').append(
                '<option value="'+modalData[index]['id']+'">'+modalData[index]['model_name']+'</option>'
              );   
            }
             if (typeof(Storage) != "undefined") {
            var ifModelAlreadyFromLocalSorage=localStorage.getItem("model_number_localStorage");
          }
          if(ifModelAlreadyFromLocalSorage)
          {
            // alert("loclvalue"+ifModelAlreadyFromLocalSorage)
             $("[name=model_number]").select2("val", ifModelAlreadyFromLocalSorage);
             localStorage.removeItem("model_number_localStorage");
          }
        }
      });
     }); 

// Start: Ajax call on change the for service_type
// $(document).on("change","[name^=model_number]",function(){
//       var thisSelf=$(this);
//       var model_number = $(this).val();
//       var brand = $('[name^=brand]').val();
//       $.ajax({
//         type:"POST",
//         url: "{{url('/')}}/ajax/getServiceTypeForWorkshopThroughModel",
//         data:{
//           "_token": "{{ csrf_token() }}",
//           model_number:model_number,
//           brand : brand
//         },
//         dataType : 'html',
//         cache: false,
//         success: function(data){
//           serviceTypeData=JSON.parse(data);
//           functionForGetServiceType(serviceTypeData);
//         }
//       });
    //  window.functionForGetServiceType=function(serviceTypeData)
    //  {
    //   $('[name^=service_type]')
    //            .empty()
    //            .append('<option selected="selected" value="">-Select -</option>');
    //            for (index = 0; index < serviceTypeData.length; ++index) {
    //            $('[name^=service_type]').append(
    //             '<option value="'+serviceTypeData[index]['id']+'">'+serviceTypeData[index]['service_type_name']+'</option>'
    //           );   
    //         }
    //  }
    // window.functionForGetProduct= function(productList)
    //  {
    //    $('[name^=product_id]')
    //            .empty()
    //            .append('<option selected="selected" value="">-Select -</option>');
    //            for (index = 0; index < productList.length; ++index) {
    //            $('[name^=product_id]').append(
    //             '<option value="'+productList[index]['id']+'">'+productList[index]['product_name']+'</option>'
    //           );   
    //         }
    //  }

     //  $.ajax({
     //    type:"POST",
     //    url: "{{url('/')}}/ajax/getProductThroughModelAndBrand",
     //    data:{
     //      "_token": "{{ csrf_token() }}",
     //      model_number:model_number,
     //      brand : brand
     //    },
     //    dataType : 'html',
     //    cache: false,
     //    success: function(data){
     //      productList=JSON.parse(data);
     //       functionForGetProduct(productList);
     //    }
     //  });


     // }); 
// End: Ajax call on change the for service_type 

// start: Ajax call on change the for service_name
// $(document).on("change","[name^=service_type]",function(){
//       var thisSelf=$(this);
//       var service_type = $(this).val();
//       var brand = thisSelf.parent().parent().find('[name^=brand]').val();
//       var model = thisSelf.parent().parent().find('[name^=model_number]').val();
//       $.ajax({
//         type:"POST",
//         url: "{{url('/')}}/ajax/getServiceTypeForWorkshop",
//         data:{
//           "_token": "{{ csrf_token() }}",
//           service_type : service_type,
//           brand : brand,
//           model : model,
//         },
//         dataType : 'html',
//         cache: false,
//         success: function(data){
//           serviceNameData=JSON.parse(data);
//             thisSelf.parent().parent().find('[name^=service_id]')
//                .empty()
//                .append('<option selected="selected" value="">-Select -</option>');
//                for (index = 0; index < serviceNameData.length; ++index) {
//                 thisSelf.parent().parent().find('[name^=service_id]').append(
//                 '<option value="'+serviceNameData[index]['id']+'">'+serviceNameData[index]['service_name']+'</option>'
//               );   
//             }
//         }
//       });
//      });
// End: Ajax call on change the for service_name












$(document).on('change', '[name^=workshop_product_brand]', function(){  
         var brand=$(this).val();
         var thisSelf=$(this);
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
           thisSelf.parent().parent().find('[name^=workshop_product_model]')
               .empty()
               .append('<option selected="selected" value="">-Select -</option>');
               for (index = 0; index < modalData.length; ++index) {
                thisSelf.parent().parent().find('[name^=workshop_product_model]').append(
                '<option value="'+modalData[index]['id']+'">'+modalData[index]['model_name']+'</option>'
              );   
            }
        }
      });
      }); 
$(document).on("change","[name^=workshop_product_model]",function(){
      var thisSelf=$(this);
      var model_number = $(this).val();
      var brand = thisSelf.parent().parent().find('[name^=workshop_product_brand]').val();
      $.ajax({
        type:"POST",
        url: "{{url('/')}}/ajax/getProductThroughModelAndBrand",
        data:{
          "_token": "{{ csrf_token() }}",
          model_number:model_number,
          brand : brand
        },
        dataType : 'html',
        cache: false,
        success: function(data){
           productList=JSON.parse(data);
          thisSelf.parent().parent().find('[name^=product_id]')
               .empty()
               .append('<option selected="selected" value="">-Select -</option>');
               for (index = 0; index < productList.length; ++index) {
               thisSelf.parent().parent().find('[name^=product_id]').append(
                '<option value="'+productList[index]['id']+'">'+productList[index]['product_name']+'</option>'
              );   
            }
        }
      });
     });

// $(document).on("change","[name^=service_type]",function(){
//       var thisSelf=$(this);
//       var service_type = $(this).val();
//       var brand = thisSelf.parent().parent().find('[name^=workshop_service_brand]').val();
//       var model = thisSelf.parent().parent().find('[name^=workshop_service_model]').val();
//       $.ajax({
//         type:"POST",
//         url: "{{url('/')}}/ajax/getServiceTypeForWorkshop",
//         data:{
//           "_token": "{{ csrf_token() }}",
//           service_type : service_type,
//           brand : brand,
//           model : model,
//         },
//         dataType : 'html',
//         cache: false,
//         success: function(data){
//           serviceNameData=JSON.parse(data);
//             thisSelf.parent().parent().find('[name^=service_id]')
//                .empty()
//                .append('<option selected="selected" value="">-Select -</option>');
//                for (index = 0; index < serviceNameData.length; ++index) {
//                 thisSelf.parent().parent().find('[name^=service_id]').append(
//                 '<option value="'+serviceNameData[index]['id']+'">'+serviceNameData[index]['service_name']+'</option>'
//               );   
//             }
//         }
//       });
//      });

// $(document).on('change', '[name^=service_id]', function(){  
//          var service_id=$(this).val();
//          var thisSelf=$(this);
//              $.ajax({
//              type: "POST",
//              url: "{{url('/')}}/ajax/getService",
//              data: { 
//                      "_token": "{{ csrf_token() }}",
//                     service_id : service_id,
//                    },
//              dataType : 'html',
//              cache: false,
//              success: function(data){
//                 var serviceDetail=JSON.parse(data);
//                 price=serviceDetail[0].price;
//                 thisSelf.parent().parent().find('[name="service_price"]').val(price);
                 
//              }             
//          }); 
//       }); 

   $(document).on('change', '[name^=product_id]', function(){  
         var product_id=$(this).val();
         var thisSelf=$(this);
             $.ajax({
             type: "POST",
             url: "{{url('/')}}/ajax/getProductForworkshop",
             data: { 
                     "_token": "{{ csrf_token() }}",
                    product_id : product_id,
                   },
             dataType : 'html',
             cache: false,
             success: function(data){
              var productDetail=JSON.parse(data)
              if(productDetail)
              {
                if (typeof(productDetail[0].unit_price_exit) == "undefined")
                {
                  alert("Product Quantity Not Available");
                }
                else
                {
                  unit_price_exit=productDetail[0].unit_price_exit;
                  unit_price=productDetail[0].unit_price;
                  gst=productDetail[0].gst;
                  unit_price_exit=parseFloat(unit_price_exit);
                  unit_price=parseFloat(unit_price);
                  gst=parseFloat(gst);
                  unit_price=unit_price+(unit_price*gst)/100;
                  unit_price_exit_with_gst=unit_price_exit+(unit_price_exit*gst)/100;
                  thisSelf.parent().parent().find('[name^=product_price]').val(unit_price_exit_with_gst);
                  thisSelf.parent().parent().find('[name^=product_price]').attr("min",unit_price);
                  thisSelf.parent().parent().find('[name^=product_quantity]').attr("max",parseFloat(productDetail[0].stock_in)-parseFloat(productDetail[0].stock_out));
                  thisSelf.parent().parent().find('[name^=product_quantity]').attr("min",0);
                  thisSelf.parent().parent().find('[name^=product_price]').attr("step","any");
                }
                
              }

             }
         }); 
      }); 

   //workshop_service_brand

    $(document).on('change', '[name^=workshop_service_brand]', function(){  
         var brand=$(this).val();
         var thisSelf=$(this);
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
           thisSelf.parent().parent().find('[name^=workshop_service_model]')
               .empty()
               .append('<option selected="selected" value="">-Select -</option>');
               for (index = 0; index < modalData.length; ++index) {
                thisSelf.parent().parent().find('[name^=workshop_service_model]').append(
                '<option value="'+modalData[index]['id']+'">'+modalData[index]['model_name']+'</option>'
              );   
            }
        }
      });
      }); 
    // Start: Ajax call on change the for service_type
$(document).on("change","[name^=workshop_service_model]",function(){
      var thisSelf=$(this);
      var model_number = $(this).val();
      var brand = thisSelf.parent().parent().find('[name^=workshop_service_brand]').val();
      $.ajax({
        type:"POST",
        url: "{{url('/')}}/ajax/getServiceTypeForWorkshopThroughModel",
        data:{
          "_token": "{{ csrf_token() }}",
          model_number:model_number,
          brand : brand
        },
        dataType : 'html',
        cache: false,
        success: function(data){
          serviceTypeData=JSON.parse(data);
           thisSelf.parent().parent().find('[name^=service_type_id]')
               .empty()
               .append('<option selected="selected" value="">-Select -</option>');
               for (index = 0; index < serviceTypeData.length; ++index) {
               thisSelf.parent().parent().find('[name^=service_type]').append(
                '<option value="'+serviceTypeData[index]['id']+'">'+serviceTypeData[index]['service_type_name']+'</option>'
              );   
            }
        }
      });
     });

$(document).on("change","[name^=service_type_id]",function(){
      var thisSelf=$(this);
      var service_type = $(this).val();
      var brand = thisSelf.parent().parent().find('[name^=workshop_service_brand]').val();
      var model = thisSelf.parent().parent().find('[name^=workshop_service_model]').val();
      $.ajax({
        type:"POST",
        url: "{{url('/')}}/ajax/getServiceTypeForWorkshop",
        data:{
          "_token": "{{ csrf_token() }}",
          service_type : service_type,
          brand : brand,
          model : model,
        },
        dataType : 'html',
        cache: false,
        success: function(data){
          serviceNameData=JSON.parse(data);
            thisSelf.parent().parent().find('[name^=service_id]')
               .empty()
               .append('<option selected="selected" value="">-Select -</option>');
               for (index = 0; index < serviceNameData.length; ++index) {
                thisSelf.parent().parent().find('[name^=service_id]').append(
                '<option value="'+serviceNameData[index]['id']+'">'+serviceNameData[index]['service_name']+'</option>'
              );   
            }
        }
      });
     });

$(document).on('change', '[name^=service_id]', function(){  
         var service_id=$(this).val();
         var thisSelf=$(this);
             $.ajax({
             type: "POST",
             url: "{{url('/')}}/ajax/getService",
             data: { 
                     "_token": "{{ csrf_token() }}",
                    service_id : service_id,
                   },
             dataType : 'html',
             cache: false,
             success: function(data){
                var serviceDetail=JSON.parse(data);
                price=serviceDetail[0].price;
                 price=parseFloat(price);
                thisSelf.parent().parent().find('[name^=service_price]').val(price);
                 
             }             
         }); 
      }); 

$(document).on('change', '[name^=customer_id]', function(){  
         var customer_id=$(this).val();
              $('[name=name]').removeAttr('readonly');
              $("[name=gst_no]").removeAttr('readonly');
              $('[name=mobile]').removeAttr('readonly');
              $("[name=landline]").removeAttr('readonly');
              $('[name=email]').removeAttr('readonly');
              $('[name=address]').removeAttr('readonly');
              $('[name=name]').val("");
              $("[name=gst_no]").val("");
              $('[name=mobile]').val("");
              $("[name=landline]").val("");
              $('[name=email]').val("");
              $('[name=address]').val("");
              $('#registered_vehicleHS').hide();
         var thisSelf=$(this);
             $.ajax({
             type: "POST",
             url: "{{url('/')}}/ajax/getCustomerForWorkshop",
             data: { 
                     "_token": "{{ csrf_token() }}",
                    customer_id : customer_id,
                   },
             dataType : 'html',
             cache: false,
             success: function(data){
               var customerDetail=JSON.parse(data);
              if(customerDetail.length==0 )
              {
                  $('[name=name]').removeAttr('readonly');
                  $("[name=gst_no]").removeAttr('readonly');
                  $('[name=mobile]').removeAttr('readonly');
                  $("[name=landline]").removeAttr('readonly');
                  $('[name=email]').removeAttr('readonly');
                  $('[name=address]').removeAttr('readonly');
                  $('[name=name]').val("");
                  $("[name=gst_no]").val("");
                  $('[name=mobile]').val("");
                  $("[name=landline]").val("");
                  $('[name=email]').val("");
                  $('[name=address]').val("");
                   // $('[name=registered_vehicle]').val('');
                   $('#registered_vehicleHS').hide();
              }
              else
              {
                  $('[name=name]').attr('readonly', 'readonly');
                  $("[name=gst_no]").attr('readonly', 'readonly');
                  $('[name=mobile]').attr('readonly', 'readonly');
                  $("[name=landline]").attr('readonly', 'readonly');
                  $('[name=email]').attr('readonly', 'readonly');
                  $('[name=address]').attr('readonly', 'readonly');
                  $('#registered_vehicleHS').show();
                   
                    customer_address=customerDetail[0].customer_address;
                    customer_alt_number=customerDetail[0].customer_alt_number;
                    customer_contact_number=customerDetail[0].customer_contact_number;
                    customer_email=customerDetail[0].customer_email;
                    customer_gstin=customerDetail[0].customer_gstin;
                    customer_name=customerDetail[0].customer_name;
                    $('[name^=name]').val(customer_name);
                    $('[name^=gst_no]').val(customer_gstin);
                    $('[name^=mobile]').val(customer_contact_number);
                    $('[name^=landline]').val(customer_alt_number);
                    $('[name^=email]').val(customer_email);
                    $('[name^=address]').val(customer_address);

              }
             
                // customer_gstin=customerDetail[0].customer_gstin;
                 // price=parseFloat(price);
                // thisSelf.parent().parent().find('[name^=service_price]').val(price);
                 
             }             
         }); 
      }); 

$('[name=registered_vehicle]').on("change",function(){
  var registered_vehicle=$(this).val();
//   $(selector).attr('readonly', 'readonly');
// $(selector).removeAttr('readonly');
   // $('[name=vehicle_reg_number]').attr('readonly', 'readonly');
   //  $('[name=model_year]').attr('readonly', 'readonly');
   //  $("[name=brand]").attr('readonly', 'readonly');
   //  $('[name=vin]').attr('readonly', 'readonly');
   //  $('[name=fuel_type]').attr('readonly', 'readonly');
   //  $('[name=engine_number ]').attr('readonly', 'readonly');
   //  $('[name=company_name]').attr('readonly', 'readonly');
   //  $('[name=reg_number]').attr('readonly', 'readonly');
   //  $('[name=odometer_reading]').attr('readonly', 'readonly');
   //  $('[name=color]').val(data[0].color);
   //  $('[name=key_number]').val(data[0].key_number);
   //  $("[name=model_number]")
   //  $('[name=notes]')

  $.ajax({
    type:"POST",
    url:"{{ url('/') }}/ajax/GetVehicleDetailFromWorkshop",
    data:{
      "_token":"{{ csrf_token() }}",
      registered_vehicle:registered_vehicle
    },
    dataType:'html',
    cache:false,
    success: function(data){
      data=JSON.parse(data);
        // console.log(data[0].vehicle_reg_number)

    $('[name=vehicle_reg_number]').val(data[0].vehicle_reg_number);
    $('[name=model_year]').val(data[0].model_year);
    $("[name=brand]").select2("val", data[0].brand.toString());
    $('[name=vin]').val(data[0].vin);
    $('[name=fuel_type]').val(data[0].fuel_type);
    $('[name=engine_number ]').val(data[0].engine_number);
    $('[name=company_name]').val(data[0].company_name);
    $('[name=reg_number]').val(data[0].reg_number);
    $('[name=odometer_reading]').val(data[0].odometer_reading);
    $('[name=color]').val(data[0].color);
    $('[name=key_number]').val(data[0].key_number);

     $("[name=model_number]").select2("val", data[0].model_number.toString());
     if (typeof(Storage) != "undefined") {
    localStorage.setItem("model_number_localStorage",data[0].model_number.toString());
   }
    $('[name=advisor]').val(data[0].advisor);
    $('[name=notes]').val(data[0].notes);
  
//          advisor: "ddgdfg"
// brand: 2
// color: "red"
// company_name: "dfgdfg"
// created_at: "2019-01-30 17:39:29"
// customer_id: 9
// deleted_at: null
// due_in: "2019-01-29"
// due_out: "2019-01-29"
// engine_number: "345345"
// fuel_type: "Petrol"
// id: 6
// key_number: null
// model_number: "46"
// model_year: "2013"
// notes: "fgdfgfdg
// ↵dfgdf"
// odometer_reading: "sdfgdfg"
// reg_number: "2019-01-30"
// updated_at: "2019-01-30 17:39:29"
// vehicle_id: null
// vehicle_reg_number

    
    }

  });

})
// $('[name=customer_id]').on("change",function(){
//   var customer_id=$(this).val();
//   $.ajax({
//     type:"POST",
//     url:"{{ url('/') }}/ajax/GetVehicleRegFromWorkshop",
//     data:{
//       "_token":"{{ csrf_token() }}",
//       customer_id:customer_id
//     },
//     dataType:'html',
//     cache:false,
//     success: function(data){
//         vehicalRegNum=JSON.parse(data);
//         $('#registered_vehicleHS').show();
//         $('#registered_vehicle')
//                .empty()
//                .append('<option selected="selected" value="">-Select -</option>');
//                for (index = 0; index < vehicalRegNum.length; ++index) {
//                  $('#registered_vehicle').append(
//                 '<option value="'+vehicalRegNum[index]['vehicle_reg_number']+'">'+vehicalRegNum[index]['vehicle_reg_number']+'</option>'
//               );   
//             }
  
//     }

//   });

// })


 var IdForUpdate="{{ isset($id) ? $id:null }}";
      if(IdForUpdate!="")
      {
        $('.estimatedCost').replaceWith("");
      }

    var workshopId="{{ isset($workshopId) ? $workshopId:null }}";
      if(workshopId!="")
      {
       // location.href="{{url('/')}}/AutoCare/workshop/view/"+workshopId;
      }
  });
</script>
@endsection