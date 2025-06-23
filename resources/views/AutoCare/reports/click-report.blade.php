@extends('samples')

@section('content')
<section class="container-fluid">
<div class="bg-white p-3 mb-3">
    <h5>Phone Call Clicks by Date</h5>

    <table class="table table-bordered table-striped">
        <thead class="table-dark table-sm">
            <tr>
                <th>Telephone</th>
                <th>Date</th>
                <th>Total Call</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clicks as $click)
                <tr>
                    <td>{{ $click->value }}</td>
                    <td>{{ $click->date }}</td>
                    <td>{{ $click->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No data available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</section>
@endsection

