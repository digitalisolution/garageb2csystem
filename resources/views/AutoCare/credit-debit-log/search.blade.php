@extends('samples') 
@section('content')
<section class="container-fluid">
  <div class="bg-white p-3 mb-3">
      {{ Form::open(['url' => '/credit-debit/search','files' => 'true' ,'enctype' => 'multipart/form-data']) }} 
         <div class="row">
         <div class="col-lg-3 col-md-6 col-12 form-group">
          <label>Credit-Debit Id:</label>
          <!-- <input type="text" class="form-control-sm" name="id"> -->
                {{Form::text('id', isset($id)?$id: '', ['class' => 'form-control','id'=>'id', 'placeholder' => 'Credit-Debit Id']  )}}
         </div>
         <div class="col-lg-3 col-md-6 col-12 form-group">
          <label>User Name:</label>
          {{Form::text('user_name', isset($user_name)?$user_name: '', ['class' => 'form-control','id'=>'user_name', 'placeholder' => '  User Name']  )}}
         </div>
         <div class="col-lg-2 col-md-6 col-12 form-group">
          <label>User Id:</label>
          <input type="text" class="form-control" name="user_id">
         </div>
         <div class="col-lg-2 col-md-6 col-12 form-group">
          <label>From Date:</label>
          <input type="date" class="form-control" name="created_at_from">
         </div>
         <div class="col-lg-2 col-md-6 col-12 form-group">
          <label>To Date:</label>
          <input type="date" class="form-control" name="created_at_to">
         </div>
         </div>
         <div class="text-right"><input type="submit" name="search" class="btn btn-primary mt-2" value="Search"></div>
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
            <i class="fa fa-align-justify"></i> Credit/Debit  Detail
          </div>
          <div class="card-body table-responsive">
            <table id="datable_1" class="table table-hover table-bordered">
              <thead class="thead-dark">
                <tr>
                  
                  <th style="white-space: nowrap">Created Date</th>
                  <th style="white-space: nowrap">credit-debit Id</th>
                  <th style="white-space: nowrap">User Name</th>
                  {{-- <th style="white-space: nowrap">User Email</th> --}}
                  <th style="white-space: nowrap">Item</th>
                  <th style="white-space: nowrap">Item Discription</th>
                  <th style="white-space: nowrap">Credit</th>
                  <th style="white-space: nowrap">Debit</th>
                  
                  <th style="white-space: nowrap">Balance</th>
                  <!-- <th style="white-space: nowrap">Created Date</th> -->
                  {{-- <th style="white-space: nowrap">Updated Date</th> --}}
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php  $balance=0; $totalCredit=0;$totalDebit=0; @endphp
                @foreach($market as $key => $value)
                @php 
                  if($value['is_credit']==0)
                  {
                    $balance+=$value['amount'];
                    $totalCredit+=$value['amount'];
                  }
                  else
                  {
                    $balance-=$value['amount'];
                    $totalDebit+=$value['amount'];
                  }
                   if(isset($value['created_at']))
                    {
                      $created_at = strtotime($value['created_at']);
                      $created_at = date('d/m/Y', $created_at);
                    }

                    else{
                      $created_at ="";
                    } 
                 @endphp
                
                
                <tr>

                  <td>{{ $created_at }}</td>
                   <td>{{ $value['id'] }}</td>
                  {{-- <td>{{ $value['id'] }}</td> --}}
                  <td>{{ $value['user_name'] }}</td>
                  {{-- <td>{{ $value['user_email'] }}</td> --}}
                  <td>{{ $value['item'] }}</td>
                  <td>{{ $value['item_discription'] }}</td>                  
                 
                  @php
                  if($value['is_credit']==0)
                  {
                    echo "<td style='border: 1px solid white; border-color:#21419c;'>".$value['amount']."</td>";
                    echo "<td >--</td>";
                  }
                  else
                  {
                    echo "<td>--</td>";
                    echo "<td style='border: 1px solid white; border-color:#ff0000;'> ".$value['amount']."</td>";                    
                  }
                  @endphp
                   
                    <td>{{ $balance }}</td>                 
                  <!-- <td>{{ $value['created_at'] }}</td> -->
                  {{-- <td>{{ $value['updated_at'] }}</td> --}}
                  <td style="white-space: nowrap">
                   <a href="{{ url('/')}}/credit-debit/add/{{ $value['id'] }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a> 
                      <a href="{{ url('/')}}/credit-debit/trash/{{ $value['id']}} " class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-remove"></i></a>
                  </td>
                </tr>  
                @endforeach
                <tfoot>
                  <tr>              
                  <td colspan="3">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td><b>{{ $totalCredit }}</b></td>
                  <td><b>-{{ $totalDebit }}</b></td>                
                  <td><b>{{ $balance }}</b></td>
                   <td>&nbsp;</td>
                    
                   {{--  <td colspan="1"></td>
                    <td colspan="1">{{ $totalCredit }}</td>
                    <td colspan="2">Total Debit</td>
                    <td colspan="1">{{ $totalDebit }}</td>
                    <td colspan="1">Total Balance</td>
                    <td colspan="1">{{ $balance }}</td>
                    <td colspan="3">null</td> --}}
                  </tr>
                </tfoot>
                
              </tbody>
              
             
            </table>
            <div class="col-lg-12 text-center">
              
            </div>
            <!-- <li><a href="#" id="json"> <i class="fa fa-print"></i> JSON</a></li>
                                <li><a href="#" onclick="$('#table').tableExport({type:'json',escape:'false'});"><img src="images/json.jpg" width="24px">JSON (ignoreColumn)</a></li> -->
           <!--  <p class="lead"><button id="json" class="btn btn-primary">TO JSON</button> <button id="csv" class="btn btn-info">TO CSV</button>  <button id="pdf" class="btn btn-danger">TO PDF</button></p> -->
          </div>
          <div class="card-footer">
            <div class="d-flex justify-content-center flex-row bd-highlight mb-">
              <div class="p-2 bd-highlight">Total Credit :</div>
              <div class="p-2 bd-highlight">{{ $totalCredit }}</div>
              <div class="p-2 bd-highlight">Total Debit :</div>
              <div class="p-2 bd-highlight">{{ $totalDebit }}</div>
              <div class="p-2 bd-highlight">Total Balance :</div>
              <div class="p-2 bd-highlight">{{ $balance }}</div>
            </div>
          </div>
        </div>
    </div>
    
  </div>
                  
</section>


<script type="text/javascript">


  $(document).ready(function() {

   // $('#datable').DataTable();

     //alert("fine");
  //  $("#table").tableHTMLExport({
  // // csv, txt, json, pdf
  // type:'json',
  // // file name
  // filename:'sample.json'
  // });
  //    $('#json').on('click',function(){
  //     alert("fine");
  //   $("#table").tableHTMLExport({type:'json',filename:'sample.json'});
  // })
  // $('#csv').on('click',function(){
  //      alert("fine");
  //   $("#table").tableHTMLExport({type:'csv',filename:'sample.csv'});
  // })
  // $('#pdf').on('click',function(){
  //      alert("fine");
  //   $("#table").tableHTMLExport({type:'pdf',filename:'sample.pdf'});
  // })
  // $('#json').on('click',function(){
  //     alert("fine");
  //  $('#table').tableExport({type:'csv'});
  // })
  
} );

</script>
@endsection