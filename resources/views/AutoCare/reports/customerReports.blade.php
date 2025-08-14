@extends('samples')

@section('content')
<div class="container-fluid">
<div class="bg-white p-3">
    <!-- Report Filters -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="reportType">Report Type</label>
            <select id="reportType" class="form-control">
                <option value="customer">Customer Reports</option>
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
        <div id="reportResults" class="mt-5"></div>
        
    </div>
   
    
</div>

<!-- <canvas id="reportChart" width="400" height="200"></canvas> -->
</div>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
<script>
function renderChart(data) {
    // Ensure data is an array and has valid entries
    if (!Array.isArray(data) || data.length === 0) {
        console.error('No data available to render the chart.');
        return;
    }

    // Group data by status and count occurrences
    const statusCounts = {};
    data.forEach(item => {
        const status = item.status || 'Unknown'; // Use 'Unknown' as fallback
        if (!statusCounts[status]) {
            statusCounts[status] = 0;
        }
        statusCounts[status]++;
    });

    // Extract labels and values for the pie chart
    const labels = Object.keys(statusCounts); // Unique statuses
    const values = Object.values(statusCounts); // Counts for each status

    // Get the canvas context
    const ctx = document.getElementById('reportChart').getContext('2d');

    // Create the pie chart
    new Chart(ctx, {
        type: 'pie', // Use 'pie' for a pie chart
        data: {
            labels: labels,
            datasets: [{
                label: 'Invoice Status Distribution',
                data: values,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)', // Paid
                    'rgba(255, 99, 132, 0.6)', // Unpaid
                    'rgba(255, 206, 86, 0.6)', // Overdue
                    'rgba(182, 112, 15, 0.6)', // Unknown
                    'rgba(255, 102, 212, 0.6)', // Unknown
                    'rgba(255, 140, 102, 0.6)', // Unknown
                    'rgb(223, 26, 26)', // Unknown
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgb(207, 125, 195)',
                    'rgb(179, 255, 102)',
                    'rgb(188, 74, 64)',
                    'rgb(255, 69, 13)',
                ],
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top', // Position the legend at the top
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${value} invoices`;
                        },
                    },
                },
            },
        },
    });
}
</script> -->

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
        },
        success: function (response) {
            if (response.success) {
                if (response.data.length === 0) {
                    $('#reportResults').html('<p>No data found.</p>');
                    return;
                }

                // Create table header
                let html = '<table id="datable_1" class="table table-bordered">';
                html += '<thead><tr>';
                Object.keys(response.data[0]).forEach(key => {
                    html += `<th>${key.toUpperCase()}</th>`;
                });
                html += '</tr></thead>';

                // Create table body
                html += '<tbody>';
                response.data.forEach(item => {
                    html += '<tr>';
                    Object.values(item).forEach(value => {
                        html += `<td>${value}</td>`;
                    });
                    html += '</tr>';
                });
                html += '</tbody>';

                // Create table footer
                html += '<tfoot><tr>';
                Object.keys(response.data[0]).forEach(() => {
                    html += '<th></th>';
                });
                html += '</tr></tfoot>';

                html += '</table>';
                $('#reportResults').html(html);

                // List the column names you want totals for
                const totalColumns = ['total_amt', 'paid', 'discount', 'credit', 'due_amt','debit_amount','unit_price', 'cost_price', 'total_quantity'];

                // Initialize DataTable
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
                        let headers = Object.keys(response.data[0]); // column names

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

                        // Optional: label in first footer cell
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