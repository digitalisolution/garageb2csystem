@extends('samples') 
@section('content')

<section class="content" style="margin-left: 20px;margin-right: 20px;">
   <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="box box-primary">
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
            <i class="fa fa-align-justify"></i> User List
             <a class="btn btn-primary text-center float-right"
              href="{{ asset('/AutoCare/employee') }}"><i class="fa fa-plus"></i> Add User</a>
          </div>
          <div class="card-body table-responsive" style="font-size: 13px;padding-left:10px;vertical-align:middle;">
            <table id="datable_1" class="table  table-hover" style="font-size: 13px;" >
              <thead class="thead-dark">
                <tr>
                  <th style="white-space: nowrap">User Id</th>
                  <th style="white-space: nowrap">User Name</th>
                  <th style="white-space: nowrap">User Email</th>
                  <th style="white-space: nowrap">Role Id</th>
                  <th style="white-space: nowrap">Role Name</th>
                  <th style="white-space: nowrap">User Phone</th>
                  <th style="white-space: nowrap">Address</th>
                  <th style="white-space: nowrap">Created Date</th>
                  <th style="white-space: nowrap">Updated Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tableData as $key => $value)
                @if($value['role_id'] != 1 || auth()->user()->role_id == 1 )
                <tr>
                  <td>{{ $value['UserId'] }}
                  </td>
                  <td>{{ $value['name'] }}</td>
                  <td>{{ $value['email'] }}</td>
                  <td>{{ $value['role_id'] }}</td>
                  <td>{{ $value['role_name'] }}</td>
                  <td>{{ $value['mobile_number'] }}</td>
                  <td>{{ $value['Address'] }}</td>
                  <td>{{ $value['created_at'] }}</td>
                  <td>{{ $value['updated_at'] }}</td>
                  <td style="white-space: nowrap">
                  <a href="{{ url('/') }}/AutoCare/employee-edit/{{ $value['UserId'] }}"
                  class="btn btn-success btn-sm">
                  <i class="fa fa-edit"></i>
                  </a>
                  </td>
                </tr>  
                 @endif
                @endforeach
              </tbody>
            </table>
            <div class="col-lg-12 text-center">
            </div>
          </div>
        </div>
    </div>
    
  </div>
                  
  <div class="row">
   
  </div>
</section>
@endsection