@extends('samples')

@section('content')
<section class="container-fluid">
<div class="bg-white p-3 mb-3">
    <h5>Phone Call Clicks by Date</h5>

    <table id="datable" class="table table-bordered table-striped">
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
    <script>
$(document).ready(function () {
    if (!$.fn.DataTable.isDataTable('#datable')) { // Check if the table is already initialized
        $('#datable').DataTable({
            dom: 'lBfrtip', // Add 'l' for the length menu on the left
            buttons: [
                'csv', 'excel', 'pdf', 'print' // Include export buttons
            ],
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                // Example: Calculate the total of the second column
                var total = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                // Update footer
                $(api.column(1).footer()).html(total.toFixed(2));
            }
        });
    }
});
    </script>
@endsection

