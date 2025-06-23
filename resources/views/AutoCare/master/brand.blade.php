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
	<div class="card">
    <div class="card-header">
      <h4 class="box-title text-primary ">Please Fill Up Brand Details</h4>
    </div>
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
              <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h4>
              {!! session('message.content') !!}
              </div>
              @endif
            </div>
          </div>
        </div>
      </div> 
	<div class="row" id="brandDiv">
        <div class="col-md-12" >
          <div class="card">
          {{ !isset($id)?Form::open(['url' => '/master/brands','files' => 'true' ,'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) : Form::open(['url' => '/master/brands/update','files' => 'true' ,'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) }} 
          {{ csrf_field() }}
          {{ Form::hidden('id', isset($id) ? $id :'', []) }} 

            <div class="card-header">Add Brand Name</div>
            <div class="card-body">
              {{Form::text('brand_name',isset($brand_name)?$brand_name: '', ['class' => 'form-control form-control ','id'=>'brand_name','required', 'placeholder' => '  Brand Name'] )}}
            </div>
            <div class="card-footer" align="center"><input class="btn btn-sm btn-primary" type="submit" id="add_model" value="Add"></div>
            {{ Form::close() }}
          </div>  
        </div>
      </div>
       <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">Brand List </div>
            <div class="card-body">
               
                 <table id="datable_1" class="table  table-hover  " style="font-size: 13px;display:table-cell;" >
              <thead class="thead-dark">
                <tr>
                  <th style="white-space: nowrap">Id</th>
                  <th style="white-space: nowrap">Brand Name</th>                    
                  <th style="white-space: nowrap">Created Date</th>
                  <th style="white-space: nowrap">Updated Date</th>
                  <th style="white-space: nowrap">Action</th>
                  <th style="white-space: nowrap">&nbsp;</th>
                </tr>
              </thead>
              <tbody>                      
                 
                @foreach($brand_list as $value) 
                <tr>
                  <td>{{ $value['id']  }}</td>
                  <td>{{ $value['brand_name']  }}</td>
                  <td>{{ $value['created_at']  }}</td>
                  <td>{{ $value['updated_at']  }}</td>
                  <td >
                  <a href="{{ url('/')}}/master/brands/update/{{ $value['id'] }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a> 

                  </td>
                  <td>&nbsp;</td>
                </tr>

                @endforeach
              </tbody>
            </table>
              </div>
            <div class="card-footer"></div>
          </div>  
        </div>
      </div> 
</section>
@endsection