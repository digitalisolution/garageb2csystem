@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
    <h5>API Orders</h5>
    <table id="datable_1" class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Workshop ID</th>
                <th>API Order ID</th>
                <th>Order Type</th>
                <th>Supplier</th>
                <!-- <th>Status</th> -->
                <th>EAN</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Order Date</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apiOrders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->workshop_id }}</td>
                    <td>{{ $order->api_order_id }}</td>
                    <td>{{ $order->order_type }}</td>
                    <td>{{ $order->supplier }}</td>
                    <!-- <td>{{ $order->status }}</td> -->
                    <td>{{ $order->ean }}</td>
                    <td>{{ $order->sku }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>{{ $order->date_added }}</td>
                    <td ><a class="btn btn-primary" href="AutoCare/workshop/view/{{ $order->workshop_id }}" target="_blank"> {{ $order->reference }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection