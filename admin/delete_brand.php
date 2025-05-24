<?php
// delete_brand.php
include 'db.php';
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid brand ID.");
}

// Fetch the brand name before deleting (for logging purposes)
$stmt = $conn->prepare("SELECT brand_name FROM brands WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$brand = $result->fetch_assoc();
$brand_name = $brand['brand_name'] ?? 'Unknown';

$stmt = $conn->prepare("DELETE FROM brands WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Log the action in the activity_log table
    $admin_id = $_SESSION['admin_id']; // Ensure admin_id is stored in the session
    $action = "Deleted a brand: $brand_name";
    $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
    $log_stmt->bind_param("is", $admin_id, $action);
    $log_stmt->execute();

    header("Location: manage_brand.php?status=deleted");
    exit();
} else {
    echo "Error deleting brand.";
}

$conn->close();
?>
