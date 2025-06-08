<?php

// After successful review submission
if ($stmt->execute()) {
    // Get car details for logging
    $car_stmt = $dbh->prepare("SELECT brand_name, vehicle_title FROM vehicles WHERE id = ?");
    $car_stmt->execute([$vehicle_id]);
    $car = $car_stmt->fetch(PDO::FETCH_ASSOC);
    $carInfo = $car['brand_name'] . ' ' . $car['vehicle_title'];
    
    // Log the review submission
    logUserActivity($dbh, $_SESSION['user_id'], 'review_submit', ['car' => $carInfo]);
    
    $msg = "Review submitted successfully!";
    header('Location: vehicle-details.php?vhid=' . $vehicle_id . '&reviewed=1');
    exit();
} 