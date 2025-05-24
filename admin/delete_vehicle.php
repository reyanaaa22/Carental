<?php
include('db.php'); // Database connection
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch vehicle details before deleting (for logging purposes)
    $sql_vehicle = "SELECT vehicle_title, image1 FROM vehicles WHERE id = $id";
    $result_vehicle = $conn->query($sql_vehicle);
    if ($result_vehicle->num_rows > 0) {
        $row_vehicle = $result_vehicle->fetch_assoc();
        $vehicle_title = $row_vehicle['vehicle_title'] ?? 'Unknown';

        // Optionally delete the image file from "uploads/" folder
        if (!empty($row_vehicle['image1']) && file_exists('uploads/' . $row_vehicle['image1'])) {
            unlink('uploads/' . $row_vehicle['image1']); // Delete image file
        }

        // Delete vehicle record
        $sql_delete = "DELETE FROM vehicles WHERE id = $id";
        if ($conn->query($sql_delete)) {
            // Log the action in the activity_log table
            $admin_id = $_SESSION['admin_id']; // Ensure admin_id is stored in the session
            $action = "Deleted a vehicle: $vehicle_title";
            $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
            $log_stmt->bind_param("is", $admin_id, $action);
            $log_stmt->execute();

            echo "<script>alert('Vehicle deleted successfully!'); window.location.href='manage_vehicles.php';</script>";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Vehicle not found!";
    }
} else {
    echo "Invalid request!";
}

$conn->close();
?>
