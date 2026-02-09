@extends('samples')

@section('content')
<div class="container-fluid">
<div class="bg-white p-3">
    <!-- Report Filters -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="garageId">Garage Name</label>
            <select id="garageId" class="form-control">
                <option value="">All Garages</option>
                @foreach($garages as $garage)
                    <option value="{{ $garage->id }}">{{ $garage->garage_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="reportType">Report Type</label>
            <select id="reportType" class="form-control">
                <option value="apc">APC Reports</option>
                <option value="customer">Customer Reports</option>
                <option value="garage">Garage Reports</option>
                <option value="invoice">Invoice Reports</option>
                <option value="tyre">Tyre Reports</option>
                <option value="payment">Payment Reports</option>
            </select>
        </div>
        <div class="col-md-3">
        <label for="startDate">Start Date</label>
        <input type="date" id="startDate" class="form-control">
    </div>
    <div class="col-md-3">
        <label for="endDate">End Date</label>
        <input type="date" id="endDate" class="form-control">
    </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <button id="fetchReportBtn" class="btn btn-primary btn-sm btn-block">Fetch Report</button>
        </div> 
        <div id="reportResults" class="mt-5 overflow-auto"></div>
        
    </div>
   
    
</div>
</div>

<script>
$('#fetchReportBtn').on('click', function () {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    $.ajax({
        url: '{{ route("reports.fetch") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            report_type: $('#reportType').val(),
            start_date: startDate,
            end_date: endDate,
            garage_id: $('#garageId').val(),
        },
        success: function (response) {
            if (response.success) {
                if (response.data.length === 0) {
                    $('#reportResults').html('<p>No data found.</p>');
                    return;
                }
                let html = '<table id="datable_1" class="table table-bordered">';
                html += '<thead><tr>';
                Object.keys(response.data[0]).forEach(key => {
                    html += `<th>${key.toUpperCase()}</th>`;
                });
                html += '</tr></thead>';
                html += '<tbody>';
                response.data.forEach(item => {
                    html += '<tr>';
                    Object.values(item).forEach(value => {
                        html += `<td>${value}</td>`;
                    });
                    html += '</tr>';
                });
                html += '</tbody>';
                html += '<tfoot><tr>';
                Object.keys(response.data[0]).forEach(() => {
                    html += '<th></th>';
                });
                html += '</tr></tfoot>';

                html += '</table>';
                $('#reportResults').html(html);
                const totalColumns = ['total_amt', 'paid', 'discount', 'credit', 'due_amt','debit_amount','unit_price', 'cost_price', 'total_qty','total invoice amt','total due amt'];
                $('#datable_1').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    dom: 'Blfrtip',
                    buttons: ['csv', 'excel', 'pdf', 'print'],
                    footerCallback: function (row, data, start, end, display) {
                        let api = this.api();
                        let headers = Object.keys(response.data[0]);

                        let intVal = function (i) {
                            return typeof i === 'string'
                                ? parseFloat(i.replace(/[\$,]/g, '')) || 0
                                : typeof i === 'number'
                                ? i
                                : 0;
                        };

                        headers.forEach((colName, colIdx) => {
                            if (totalColumns.includes(colName)) {
                                let total = api
                                    .column(colIdx)
                                    .data()
                                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                                $(api.column(colIdx).footer()).html(total.toFixed(2));
                            } else {
                                $(api.column(colIdx).footer()).html('');
                            }
                        });
                        $(api.column(3).footer()).html('<b>TOTAL</b>');
                    }
                });

                renderChart(response.data);
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert('An error occurred while fetching the report.');
        },
    });
});

</script>


@endsection