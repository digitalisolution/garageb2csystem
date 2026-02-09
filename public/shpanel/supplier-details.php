<!DOCTYPE html>
<html>
<head>
<title>Supplier Update</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap;" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
<?php
include 'http-auth.php';
error_reporting(0);
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>
<style>
ul
{
  display:flex;  
  list-style:none;
}
ul li{
  background: #ded7d7;
    padding: 6px 4px 6px 8px;
    margin-right: 10px;
}

body {
  font-family: "Roboto", sans-serif;
  background-color:#eee;
}
.invoice-body{background:#fff;box-shadow:0 0 20px rgba(0,0,0,0.15);padding:30px;border-radius:10px;}
.my-5{margin:50px 0;}
.bg-dark{background:#333;color:#fff;}
.mt-3{margin-top:30px;}
#garageModal label{display:block;}
#garageModal .form-control{margin-bottom:10px;}
</style>

<div class="container-fluid">
    <div class="text-center">
        <h3>Supplier List</h3>
    </div>
<div class="invoice-body my-5">
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $con = mysqli_connect(
        'localhost',
        'root',
        'PriDigital@#Ok'
    );
} catch (mysqli_sql_exception $e) {
    die('MySQL Connection Failed: ' . $e->getMessage());
}

$dbResult = mysqli_query($con, "SHOW DATABASES");
$allSuppliers = [];

while ($dbRow = mysqli_fetch_assoc($dbResult)) {
    $dbName = $dbRow['Database'];
    if (in_array($dbName, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
        continue;
    }

    $tableCheck = mysqli_query( $con, "SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = '$dbName' AND table_name = 'suppliers'" );
    $tableExists = mysqli_fetch_assoc($tableCheck)['cnt'];

    if (!$tableExists) {
        continue;
    }

    $supplierQuery = mysqli_query( $con, "SELECT '$dbName' AS database_name, supplier_name, file_path, api_order_enable, updated_at, status FROM `$dbName`.suppliers" );
    while ($supplier = mysqli_fetch_assoc($supplierQuery)) {
        $allSuppliers[] = $supplier;
    }
} ?>

    <table id="supplierTable" class="table table-bordered table-hover">
    <thead class="bg-dark">
    <tr>
        <th>#</th>
        <th>Database</th>
        <th>Supplier</th>
        <th>Path</th>
        <th>API Order</th>
        <th>Installed</th>
        <th>Last Update File</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    <?php $i=1; foreach ($allSuppliers as $row) { ?>
    <tr>
        <td><?= $i++ ?></td>

        <td><strong><?= htmlspecialchars($row['database_name']) ?></strong></td>

        <td><?= htmlspecialchars($row['supplier_name']) ?></td>

        <td><?= $row['file_path'] ?: '-' ?></td>

        <td>
            <?= $row['api_order_enable']
                ? '<span class="label label-success">Yes</span>'
                : '<span class="label label-danger">No</span>' ?>
        </td>

        <td>
            <?= $row['file_path']
                ? '<span class="label label-success">Installed</span>'
                : '<span class="label label-warning">Not Installed</span>' ?>
        </td>
        <td><?= $row['updated_at'] ?></td>

        <td>
            <?= $row['status']
                ? '<span class="label label-success">Enabled</span>'
                : '<span class="label label-danger">Disabled</span>' ?>
        </td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
</div>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</body>
</html>
<script>
    $(document).ready(function () {
        $('#supplierTable').DataTable({
            pageLength: 25,
            order: [[1, 'asc']], // default sort by Database
            columnDefs: [
                { orderable: false, targets: [6] } // disable sorting for Status if you want
            ]
        });
    });
</script>
<style>
table.dataTable thead th {position: relative;}
table.dataTable thead th:after, table.dataTable thead th:before {position: absolute;right: 8px;}
</style>


