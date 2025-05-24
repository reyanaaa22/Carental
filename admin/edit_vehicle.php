<?php
include('db.php'); // Database connection
session_start();

// Check if ID is passed
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get the vehicle details
    $sql = "SELECT * FROM vehicles WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $vehicle = $result->fetch_assoc();
    } else {
        echo "Vehicle not found!";
        exit;
    }
} else {
    echo "Invalid Request!";
    exit;
}

// Update vehicle if form is submitted
if (isset($_POST['update'])) {
    $vehicle_title = $_POST['vehicle_title'];
    $brand_name = $_POST['brand_name'];
    $price_per_day = $_POST['price_per_day'];
    $fuel_type = $_POST['fuel_type'];
    $model_year = $_POST['model_year'];
    $accessories = $_POST['accessories'];

    $sql_update = "UPDATE vehicles SET 
                    vehicle_title = '$vehicle_title',
                    brand_name = '$brand_name',
                    price_per_day = '$price_per_day',
                    fuel_type = '$fuel_type',
                    model_year = '$model_year',
                    accessories = '$accessories'
                   WHERE id = $id";

    if ($conn->query($sql_update)) {
        // Log the action in the activity_log table
        $admin_id = $_SESSION['admin_id']; // Ensure admin_id is stored in the session
        $action = "Updated vehicle: $vehicle_title";
        $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
        $log_stmt->bind_param("is", $admin_id, $action);
        $log_stmt->execute();

        echo "<script>alert('Vehicle updated successfully!'); window.location.href='manage_vehicles.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Vehicle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
    <h2>Edit Vehicle</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Vehicle Title</label>
            <input type="text" name="vehicle_title" class="form-control" value="<?php echo htmlspecialchars($vehicle['vehicle_title']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Brand Name</label>
            <input type="text" name="brand_name" class="form-control" value="<?php echo htmlspecialchars($vehicle['brand_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Price Per Day</label>
            <input type="number" name="price_per_day" class="form-control" value="<?php echo htmlspecialchars($vehicle['price_per_day']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Fuel Type</label>
            <input type="text" name="fuel_type" class="form-control" value="<?php echo htmlspecialchars($vehicle['fuel_type']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Model Year</label>
            <input type="text" name="model_year" class="form-control" value="<?php echo htmlspecialchars($vehicle['model_year']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Accessories</label>
            <textarea name="accessories" class="form-control" rows="4"><?php echo htmlspecialchars($vehicle['accessories']); ?></textarea>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update Vehicle</button>
        <a href="manage_vehicles.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
