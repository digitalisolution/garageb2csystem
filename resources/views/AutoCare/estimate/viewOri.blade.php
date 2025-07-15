<!DOCTYPE html>
<html>
<head>
	<script src="{{ asset('js/jQuery.min.js') }}"></script>
	 <link rel="stylesheet" href="{{ asset('bootstrap-4.1.3/dist/css/bootstrap.css') }}">  
	    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	     <meta name="discription" content="Ashutosh Kumar Choubey (Full Stack Application Developer)" />
	   
<title>Workshop Detail</title>
</head>
<body>
	<section style="margin-left: 30px;margin-right: 30px; margin-top: 30px">
		<table  style="display: table-col; border: 1px solid #000;"    width="100%" style="width:100%;">
		
			<tr style="display: table-row; border: 1px solid #000;">
				<td colspan="12" class="text-primary text-center"> Sai Auto Care Workshop Detail</td>
			</tr>
			<tr>
				<td colspan="5" >
					<table    width="100%">
						<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Job Id</td>
					   		<td>:</td>
					   		<td>{{ $id }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Job Name</td>
					   		<td>:</td>
					   		<td>{{ $name }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Reference</td>
					   		<td>:</td>
					   		<td>{{ $reference }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Company</td>
					   		<td>:</td>
					   		<td>{{ $company }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					</table>
			   </td>
			   <td style="border-left:  1px solid #000;" colspan="2">&nbsp;</td>
			   <td colspan="5">
					<table    width="100%">
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Job/Workshop Date</td>
					   		<td>:</td>
					   		<td>{{ $created_at }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Email</td>
					   		<td>:</td>
					   		<td>{{ $email }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Contact Number</td>
					   		<td>:</td>
					   		<td>{{ $mobile }}{{ isset($landline)?"/".$landline:"" }}</td>
					   		<td><hr></td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Address</td>
					   		<td>:</td>
					   		<td style="max-width: 20px;"><small>{{ $address }}{{ isset($city)?", ".$city:"" }}{{ isset($state)?", ".$state:"" }}{{ isset($pin)?", ".$pin:"" }}</small></td>
					   		<td>&nbsp;</td>
					   	</tr>
				</table>
			   </td>	
			</tr>
			<tr>
				<td colspan="12">&nbsp;</td>
			</tr>
			<tr style="display: table-row; border: 1px solid #000;">
				<td colspan="12"  class="text-info text-center"> Vehical Detail</td>
			</tr>
			<tr>
				<td colspan="12">&nbsp;</td>
			</tr>
			<tr>

				<td    colspan="4">
					<table    width="100%" >
						<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">GST Number</td>
					   		<td>:</td>
					   		<td>{{ $gst_no }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Vehicle Reg Number</td>
					   		<td>:</td>
					   		<td>{{ $vehicle_reg_number }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Model Year</td>
					   		<td>:</td>
					   		<td>{{ $model_year }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Vehical Company Name</td>
					   		<td>:</td>
					   		<td>{{ $company_name }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Odometer Reading</td>
					   		<td>:</td>
					   		<td>{{ $odometer_reading }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Fuel Type</td>
					   		<td>:</td>
					   		<td>{{ $fuel_type }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">color</td>
					   		<td>:</td>
					   		<td>{{ $color }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Engine Number</td>
					   		<td>:</td>
					   		<td>{{ $engine_number }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Submited Part</td>
					   		<td>:</td>
					   		<td>{{ $submited_part }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Advisor</td>
					   		<td>:</td>
					   		<td>{{ $advisor }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					</table>
				</td>
				<td colspan="1" style="border-left:  1px solid #000;" >&nbsp;</td>
				<td    colspan="7">
					<table    width="100%">
						<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Model Name</td>
					   		<td>:</td>
					   		<td>{{ $model_number }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Brand</td>
					   		<td>:</td>
					   		<td>{{ $brand }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">VIN</td>
					   		<td>:</td>
					   		<td>{{ $vin }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Reg Number</td>
					   		<td>:</td>
					   		<td>{{ $reg_number }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Key Number</td>
					   		<td>:</td>
					   		<td>{{ $key_number }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Due In</td>
					   		<td>:</td>
					   		<td>{{ $due_in }}</td>
					   		<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Due Out</td>
					   		<td>:</td>
					   		<td>{{ $due_out }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Status</td>
					   		<td>:</td>
					   		<td>{{ $status }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	

					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Notes</td>
					   		<td>:</td>
					   		<td>{{ $notes }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					   	<tr>
					   		<td>&nbsp;</td>
					   		<td align="left">Down Payment Price</td>
					   		<td>:</td>
					   		<td>{{ $paid_price }}</td>
					   		<td>&nbsp;</td>
					   	</tr>	
					</table>
				</td>
				
			</tr>
			<tr style="display: table-row; border: 1px solid #000;">
				<td colspan="12" class="text-success text-center">{{ isset($is_complete) && $is_complete==1?'Total Price Discription':'Expected Price' }} </td>			
			</tr>
			<tr>
				<td  colspan="4">
					<table    width="100%" >

						<thead>
							<tr><td align="center" colspan="2">Services</td></tr>
							<tr>
								<th>Service Name</th>
								<th>Service Quantity</th>
								<th>Price</th>
								<th>Total Price</th>
							</tr>
						</thead>
						<tbody>
							@php
							$total_service_price=0;
							$total_product_price=0;
							$grandTotal=0;

							@endphp
							@foreach($WorkshopService as $value)
							<tr>
								<td>{{  $value->service_name }}</td>
								<td>{{  $value->service_quantity }}</td>													
								<td>{{  $value->service_price }}</td>
								<td>{{  $value->service_price*$value->service_quantity }}</td>
							</tr>
							@php
								$total_service_price+=$value->service_price*$value->service_quantity;
							@endphp
							@endforeach
						</tbody>
						<tfoot>
							<tr><td colspan="2" align="center" >Total Service Price : {{ $total_service_price }}</td></tr>
							
						</tfoot>
					</table>					
				</td>
				<td colspan="1" style="border-left:  1px solid #000;" >&nbsp;</td>
				<td  colspan="7">
					<table    width="100%">						
						<thead>
							<tr><td align="center" colspan="2">Products</td></tr>
							<tr>
								<th>Service Name</th>
								<th>Product Quantitty</th>
								<th>Unit Product Price</th>
								<th>Price</th>
							</tr>
						</thead>
						<tbody>
							@foreach($WorkshopProduct as $value)
							<tr>
								<td>{{ $value->product_name }}</td>
								<td>{{ $value->product_quantity }} </td>
								<td>{{ $value->product_price }} </td>
								<td>{{ $value->product_price*$value->product_quantity }}</td>
							</tr>
							@php

							$total_product_price+=$value->product_price*$value->product_quantity;
							@endphp
							@endforeach
						</tbody>
						<tfoot>
							<tr><td colspan="2" align="center"> Total Product Price : {{ $total_product_price }}</td></tr>
						</tfoot>
				</table>
				</td>
				
			</tr>
			<tr style="display: table-row; border: 1px solid #000;">
				<td align="center" colspan="12">
					Grand Total: {{ $total_product_price+$total_service_price }}
				</td>	
			</tr>
			<tr  >
				<td align="right" colspan="10">
					Signature:
				</td>
				<td colspan="2">&emsp;</td>	
			</tr>
			<tr  >
				<td align="right" colspan="8">
					&emsp;
				</td>
				<td colspan="2">&emsp;</td>	
			</tr>
			<tr >
				<td align="right" colspan="8">
					&emsp;
				</td>
				<td colspan="2">&emsp;</td>	
			</tr>
		</table>
	</section>
</body>
<script src="{{ asset('bootstrap-4.1.3/dist/js/bootstrap.js') }}"></script>
</html>
