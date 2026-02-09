<?php
$conDb = mysqli_connect(
    '178.128.171.57',
    'vehicle_details_db',
    'VPM1PpJkTGS7HP',
    'vehicle_details_db'
);

if (isset($_GET['id'], $_GET['status'])) {
    $id = (int) $_GET['id'];
    $status = (int) $_GET['status'];

    $newStatus = ($status == 1) ? 0 : 1;

    mysqli_query(
        $conDb,
        "UPDATE garagewebsitelist 
         SET status = $newStatus, status_changed_on = NOW()
         WHERE id = $id"
    );
}

header("Location: create-invoice.php");
exit;
