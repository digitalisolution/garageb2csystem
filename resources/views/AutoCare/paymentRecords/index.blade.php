@extends('samples')

@section('content')
<div class="container">
    <div class="m-3">
        <h1>Payment Record Details</h1>
    </div>

    <table id="datable_1" class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Workshop ID</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Transaction Id</th>
                <th>Date Recorded</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentRecords as $paymentRecord)
            <tr>
                <td>{{ $paymentRecord->id }}</td>
                <td>{{ $paymentRecord->workshop_id }}</td>
                <td>£{{ $paymentRecord->amount }}</td>
                <td>{{ strtoupper($paymentRecord->paymentmode) }}</td>
                <td>{{ $paymentRecord->transactionid }}</td>
                <td>{{ $paymentRecord->daterecorded }}</td>
                <td><a class="btn btn-primary" href="AutoCare/workshop/view/{{ $paymentRecord->workshop_id }}" target="_blank">JOB-{{ $paymentRecord->workshop_id }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection