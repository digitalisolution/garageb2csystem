<!DOCTYPE html>
<html>
<head>
<title>Digital Ideas Invoice</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap;" rel="stylesheet">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
<?php
include 'http-auth.php';
//error_reporting(0);
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
    <div class="text-center"><img src="algorise-logo.jpg" alt="Logo"></div>
<div class="invoice-body my-5">
<!-- Add Garage Button -->
<div class="text-right"><button onclick="document.getElementById('garageModal').style.display='block'" class="btn btn-success btn-lg">+ Add Garage</button></div>

<!-- Modal Form -->
<div id="garageModal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; border:1px solid #ccc; padding:30px; z-index:1000; width:600px;border-radius:6px;box-shadow:0 0 100px rgba(0,0,0,0.3);">
    <h3 style="margin-top:0;">Add Garage</h3>
    <form method="POST" action="create-invoice.php">
        <input class="form-control" type="hidden" name="add_garage" value="1">
        <label>Garage URL:</label>
        <input class="form-control" type="text" name="garage_url" required>
        <label>Garage Name:</label>
        <input class="form-control" type="text" name="garage_name" required>
        <label>Address:</label>
        <textarea class="form-control" name="garage_address" required></textarea>
        <label>Phone:</label>
        <input class="form-control" type="text" name="garage_phone">
        <label>Email:</label>
        <input class="form-control" type="email" name="garage_email">
        <label>Garage Live Date:</label>
        <input class="form-control" type="date" name="garage_live_date" required>
        <div style="margin-top:20px;text-align:center;">
        <button class="btn btn-success" type="submit">Save</button>
        <button class="btn btn-danger" type="button" onclick="document.getElementById('garageModal').style.display='none'">Cancel</button>
        </div>
    </form>
</div>

<?php
$conDb = mysqli_connect('178.128.171.57', 'vehicle_details_db', 'VPM1PpJkTGS7HP', 'vehicle_details_db');
//$queryDb = mysqli_query($conDb,"select * from garagewebsitelist where status=1");
$queryDb = mysqli_query($conDb,"SELECT * FROM garagewebsitelist ORDER BY status DESC, garage_name ASC");


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_garage'])) {
    $garage_url = mysqli_real_escape_string($conDb, $_POST['garage_url']);
    $garage_name = mysqli_real_escape_string($conDb, $_POST['garage_name']);
    $garage_address = mysqli_real_escape_string($conDb, $_POST['garage_address']);
    $garage_phone = mysqli_real_escape_string($conDb, $_POST['garage_phone']);
    $garage_email = mysqli_real_escape_string($conDb, $_POST['garage_email']);
    $garage_live_date = mysqli_real_escape_string($conDb, $_POST['garage_live_date']);

    $query = "INSERT INTO garagewebsitelist 
        (garage_url, garage_name, garage_address, garage_phone, garage_email, status, garage_live_date, website_created_on) 
        VALUES ('$garage_url', '$garage_name', '$garage_address', '$garage_phone', '$garage_email', 1, $garage_live_date, NOW())";
    
    if (mysqli_query($conDb, $query)) {
        echo "<script>alert('Garage added successfully'); window.location.href='create-invoice.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error adding garage: " . mysqli_error($conDb) . "');</script>";
    }
}

$i=1;
?>

<table id="create_InvoiceTable" class="table table-bordered table-hover">
<thead class="bg-dark">
      <tr>
        <th>#</th>
        <th>Domain Url</th>
        <th>Website Name</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Live Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
<?php $i=1; while($dataDb =  mysqli_fetch_array($queryDb)) { ?>

  <tr class="<?php echo ($dataDb['status'] == 0) ? 'garage-inactive' : ''; ?>">
    <td><?php echo $dataDb['id']; ?></td>
    <td><?php echo $dataDb['garage_url']; ?></td>
    <td><?php echo $dataDb['garage_name']; ?></td>
    <td><?php echo $dataDb['garage_address']; ?></td>
    <td><?php echo $dataDb['garage_phone']; ?></td>
    <td><?php echo $dataDb['garage_email']; ?></td>
    <td><?php echo !empty($dataDb['garage_live_date']) ? date('d M Y', strtotime($dataDb['garage_live_date'])) : '-';?></td>
    <td>
        <?php if ($dataDb['status'] == 1): ?>
            <span class="label label-success">
                Active
            </span>
        <?php else: ?>
            <span class="label label-danger">
                Inactive
            </span>
        <?php endif; ?>

        <?php if (!empty($dataDb['status_changed_on'])): ?>
            <br>
            <small class="text-muted">
                <?php echo date('d M Y', strtotime($dataDb['status_changed_on'])); ?>
            </small>
        <?php endif; ?>
    </td>


    <?php 
    $garage_id = $dataDb['id'];
    $invoices_query = mysqli_query($conDb, "SELECT id, month, invoice_date FROM garage_invoices_price WHERE garage_id = $garage_id ORDER BY invoice_date DESC"); ?>
    <td style="white-space:nowrap;"> <a href="edit-invoice.php?id=<?php echo $dataDb['id']; ?>" class="btn btn-primary btn-sm">New Invoice</a>
        <a href="toggle-status.php?id=<?php echo $dataDb['id']; ?>&status=<?php echo $dataDb['status']; ?>"
   class="btn btn-<?php echo ($dataDb['status'] == 1) ? 'danger' : 'success'; ?> btn-sm"
   onclick="return confirm('Are you sure you want to change status?');">
   <?php echo ($dataDb['status'] == 1) ? 'Deactivate' : 'Activate'; ?>
</a>
        <?php if (mysqli_num_rows($invoices_query) > 0): ?>
        <div class="dropdown" style="display:inline-block;">
            <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                View Invoices <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php while ($inv = mysqli_fetch_assoc($invoices_query)): ?>
                    <li><a href="print-invoice.php?id=<?php echo $inv['id']; ?>" target="_blank"><?php echo date('d M Y', strtotime($inv['invoice_date'])); ?></a></li>
                <?php endwhile; ?>
            </ul>
        </div>
        <?php endif; ?>
    </td>
    </tr>

<?php 
}
?>
</tbody>
</table>
</div>
</div>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</body>
</html>
<script>
    $(document).ready(function () {
        $('#create_InvoiceTable').DataTable({
            pageLength: 25,
            order: [[1, 'asc']], // default sort by Database
            columnDefs: [
                { orderable: false, targets: [7] } // disable sorting for Status if you want
            ]
        });
    });
</script>
<style>
table.dataTable thead th {position: relative;}
table.dataTable thead th:after, table.dataTable thead th:before {position: absolute;right: 8px;}
.garage-inactive {
    background-color: #ffe5e5 !important;
}
.garage-inactive td {
    color: #b30000;
    font-weight: 600;
}
</style>

