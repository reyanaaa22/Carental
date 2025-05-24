<?php
include('db.php');
$vhid = isset($_GET['vhid']) ? intval($_GET['vhid']) : 0;
$booked = [];
if ($vhid) {
    $stmt = $dbh->prepare("SELECT FromDate, ToDate FROM bookings WHERE VehicleId = :vhid AND Status != 2");
    $stmt->bindParam(':vhid', $vhid, PDO::PARAM_INT);
    $stmt->execute();
    $booked = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
header('Content-Type: application/json');
echo json_encode($booked);
