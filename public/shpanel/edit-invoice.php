<!DOCTYPE html>
<html>
<head>
<title>Invoice - <?php echo $garage['garage_name']; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap;" rel="stylesheet">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


<?php
include 'http-auth.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conDb = mysqli_connect('178.128.171.57', 'vehicle_details_db', 'VPM1PpJkTGS7HP', 'vehicle_details_db');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$data = mysqli_query($conDb, "SELECT * FROM garagewebsitelist WHERE id = $id AND status = 1");
$garage = mysqli_fetch_assoc($data);

if (!$garage) {
    echo "Invalid ID or data not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $month = mysqli_real_escape_string($conDb, $_POST['month']);
    $seo_price = floatval($_POST['seo_price']);

    // Gather additional services
    $service_names = $_POST['service_name'] ?? [];
    $service_prices = $_POST['service_price'] ?? [];

    $additional_services = [];
    for ($i = 0; $i < count($service_names); $i++) {
        if (!empty($service_names[$i]) && is_numeric($service_prices[$i])) {
            $additional_services[] = [
                'name' => $service_names[$i],
                'price' => floatval($service_prices[$i])
            ];
        }
    }

    // Calculate total price
    $total_price = $seo_price;
    foreach ($additional_services as $svc) {
        $total_price += $svc['price'];
    }

    // Invoice + Due Dates
    $invoice_date_raw = $_POST['invoice_date'];
    $invoice_date = date("Y-m-d", strtotime($invoice_date_raw));
    $due_date = date("Y-m-d", strtotime($invoice_date . " +7 days"));

    // JSON encode the service list
    $additional_services_json = json_encode($additional_services);

    // Save to DB (ensure your table has `additional_services TEXT`)
    $stmt = $conDb->prepare("INSERT INTO garage_invoices_price (garage_id, garage_name, month, invoice_date, due_date, seo_price, total_price, additional_services) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssdds", $id, $garage['garage_name'], $month, $invoice_date, $due_date, $seo_price, $total_price, $additional_services_json);
    $stmt->execute();
    $invoice_id = $stmt->insert_id;
    $stmt->close();

    header("Location: print-invoice.php?id=$invoice_id");
    exit;
}

$currentDate = date("d/m/Y");
$dueDate = date("d/m/Y", strtotime("+7 days"));
?>


    
    <style>
        .invoice-box {
            border: 1px solid #eee;
            max-width: 800px;
            margin: auto;
        }
        .invoice-box h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        .form-group { margin: 10px 0; }
        label { display: block; }
        
        
        body {
  font-family: "Roboto", sans-serif;
  background-color:#eee;
}
.invoice-body{background:#fff;box-shadow:0 0 20px rgba(0,0,0,0.15);padding:30px;border-radius:10px;}
.my-5{margin:50px 0;}
.bg-dark{background:#333;color:#fff;}
.mt-3{margin-top:30px;}
.service-row{display:flex;gap:30px;margin-bottom:10px;background:#eee;padding:6px;border-radius:4px;border:solid 1px rgba(0,0,0,0.2);align-items:center;}
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="text-center"><img src="digital-ideas-logo.png" alt="Logo" height="150"></div>
    <div class="invoice-body my-5">
    <h1 style="margin-top:0;">Generate Invoice</h1>

    <form method="POST">
        <div class="row">
        <div class="form-group col-sm-6">
            <label>Garage Name:</label>
            <input class="form-control" type="text" value="<?php echo $garage['garage_name']; ?>" disabled>
        </div>
        <div class="form-group col-sm-6">
            <label>Select Invoice Month:</label>
            <select class="form-control" name="month" required>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthName = date('F', mktime(0, 0, 0, $m, 10));
                    echo "<option value='$monthName'>$monthName</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group col-sm-12">
            <label>SEO Service Price (GBP):</label>
            <input class="form-control" type="number" name="seo_price" step="0.01" required>
        </div>
        
        <div class="form-group col-sm-12">
    <label>Additional Services:</label>
    <div id="additional-services" style="display: none;">
        <!-- Rows will be added dynamically here -->
    </div>
    <button type="button" onclick="addService()" id="add-service-btn" class="btn btn-warning">+ Add Service Charge</button>
</div>


        <div class="form-group col-sm-6">
    <label>Invoice Date:</label>
    <input class="form-control" type="date" name="invoice_date" id="invoice_date" required onchange="calculateDueDate()">
</div>
<div class="form-group col-sm-6">
    <label>Due Date:</label>
    <input class="form-control" type="text" name="due_date_display" id="due_date_display" readonly>
</div>

        <div class="form-group col-sm-12 text-center" style="margin-top:20px;">
            <button type="submit" class="btn btn-success btn-lg">Save Invoice</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="create-invoice.php" class="btn btn-default btn-lg">Back on Listing</a>
        </div>
</div>
    </form>
</div>
</div>
<script>
function calculateDueDate() {
    const invoiceDateInput = document.getElementById("invoice_date");
    const dueDateDisplay = document.getElementById("due_date_display");

    const selectedDate = new Date(invoiceDateInput.value);
    if (isNaN(selectedDate)) return;

    const dueDate = new Date(selectedDate);
    dueDate.setDate(dueDate.getDate() + 7);

    const yyyy = dueDate.getFullYear();
    const mm = String(dueDate.getMonth() + 1).padStart(2, '0');
    const dd = String(dueDate.getDate()).padStart(2, '0');
    dueDateDisplay.value = `${dd}/${mm}/${yyyy}`;
}
</script>
<script>
function addService() {
    const container = document.getElementById("additional-services");

    // Show the container if it's hidden
    if (container.style.display === "none") {
        container.style.display = "block";
    }

    // Dynamically create a new row
    const div = document.createElement("div");
    div.className = "service-row";
    div.innerHTML = `
        <input class="form-control" type="text" name="service_name[]" placeholder="Service Name" />
        <input class="form-control" type="number" name="service_price[]" placeholder="Service Price (GBP)" step="0.01" />
        <button class="btn btn-danger" type="button" onclick="removeService(this)">🗑️</button>
    `;
    container.appendChild(div);
}

function removeService(btn) {
    btn.parentNode.remove();
}
</script>

</body>

</html>
