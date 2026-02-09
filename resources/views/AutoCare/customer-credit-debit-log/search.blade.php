@extends('samples') 
@section('content')
<section class="content" style="margin-left: 20px;margin-right: 20px;">
  <div class="row">
      <div class="col-sm-12" class="text-center">
         {{ Form::open(['url' => '/CustomerCreditDebitLog/search','files' => 'true' ,'enctype' => 'multipart/form-data']) }} 
         <div class="table-responsive">
        <table class="table table-sm">
          <tr>
            <td>Customer Debit Log  Id</td>
            <td>:</td>
            <td><!-- <input type="text" class="form-control-sm" name="id"> -->
                {{Form::text('id', isset($id)?$id: '', ['class' => 'form-control-sm form-control-sm ','id'=>'id', 'placeholder' => 'Customer Debit Log  Id']  )}}
            </td>
            <td>&emsp;&emsp;</td>
            <td>Customer Id</td>
            <td>:</td>
            <td>
              {{Form::text('user_name', isset($user_name)?$user_name: '', ['class' => 'form-control-sm form-control-sm ','id'=>'user_name', 'placeholder' => ' Customer Id']  )}}
            <td>&emsp;&emsp;</td>
          </tr>
          <tr>
            <td>From Date</td>
            <td>:</td>
            <td><input type="date" class="form-control-sm" name="created_at_from"></td>
            <td>&emsp;&emsp;</td>
            <td>To Date</td>
            <td>:</td>
            <td><input type="date" class="form-control-sm" name="created_at_to"></td>
            <td></td>
          </tr>
          
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="8" class="text-center"><input type="submit" name="search" class="btn btn-primary" value="Search"></td>
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
            <i class="fa fa-align-justify"></i> Credit/Debit  Detail
          </div>
          <div class="card-body table-responsive" style="font-size: 13px;padding-left:10px;vertical-align:middle;">
            <table id="datable_1" class="table  table-hover  " style="font-size: 13px;display:table-cell;" >
              <thead class="thead-dark">
                <tr>
                    <th style="white-space: nowrap"> Id</th>
                    <th style="white-space: nowrap">Created Date</th>
                    <th style="white-space: nowrap">Customer Id</th>
                    <th style="white-space: nowrap">Customer Name</th>
                    <th style="white-space: nowrap">Customer Email</th>
                    <th style="white-space: nowrap">Customer Mobile umber</th>
                    <th style="white-space: nowrap">Workshop Id</th>
                    <th style="white-space: nowrap">Discription</th>
                    <th style="white-space: nowrap">Credit</th>
                </tr>
              </thead>
              <tbody>
                @php  $balance=0; $totalCredit=0;$totalDebit=0; @endphp
                @foreach($market as $key => $value)
                @php 
                  if($value['is_debit']==0)
                  {
                    $balance+=$value['credit'];
                    $totalCredit+=$value['credit'];
                  }
                  else
                  {
                    // $balance-=$value['debit_amount'];
                    // $totalDebit+=$value['debit_amount'];
                  }
                 @endphp
                
                
                <tr>
                  
                  <td>{{ $value['creditDetailId'] }}</td>
                  <td>{{ $value['created_at'] }}</td>
                  <td>{{ $value['customer_id'] }}</td>
                  <td>{{ $value['customer_name'] }}</td>
                  <td>{{ $value['customer_email'] }}</td>
                  <td>{{ $value['customer_contact_number'] }}</td>
                  <td>{{ $value['workshop_id'] }}</td>
                 
                  <td>{{ $value['comments'] }}</td>                  
                 
                  @php
                  if($value['is_debit']==0)
                  {
                    echo "<td style='border: 1px solid white; border-color:#21419c;'>".$value['credit']."</td>";
                    // echo "<td >--</td>";
                  }
                  else
                  {
                    echo "<td>--</td>";
                    // echo "<td style='border: 1px solid white; border-color:#ff0000;'> ".$value['debit_amount']."</td>";                    
                  }
                  @endphp
                   
                    {{-- <td>{{ $balance }}</td>                  --}}
                  <!-- <td>{{ $value['created_at'] }}</td> -->
                  {{-- <td>{{ $value['updated_at'] }}</td> --}}
                 {{--  <td style="white-space: nowrap">
                   <a href="{{ url('/')}}/credit-debit/add/{{ $value['id'] }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a> 
                      <a href="{{ url('/')}}/credit-debit/trash/{{ $value['id']}} " class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-remove"></i></a>
                  </td> --}}
                </tr>  
                @endforeach
                <tfoot>
                  <tr>              
                  <td colspan="7">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td><b>{{ $totalCredit }}</b></td>
                  {{-- <td><b>-{{ $totalDebit }}</b></td>                 --}}
                  {{-- <td><b>{{ $balance }}</b></td> --}}
                   
                    
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
             {{--  <div class="p-2 bd-highlight">Total Debit :</div>
              <div class="p-2 bd-highlight">{{ $totalDebit }}</div>
              <div class="p-2 bd-highlight">Total Balance :</div>
              <div class="p-2 bd-highlight">{{ $balance }}</div> --}}
            </div>
          </div>
        </div>
    </div>
    
  </div>
                  
  <div class="row">
   
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