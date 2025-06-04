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
    <style> 
        html, body {  
            height: 100%;  
            margin: 0;  
            padding: 0;  
            overflow: hidden;  
        }  

        header {  
            position: fixed;  
            top: 0;  
            left: 0;  
            right: 0;  
            height: 80px;  
            background-color: #1a237e;  
            z-index: 1000;  
            padding: 1rem;  
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);  
            color: white;  
        }  

        .sidebar {  
            position: fixed;  
            top: 80px;  
            left: 0;  
            bottom: 0;  
            width: 250px;  
            background-color: #263238;  
            overflow-y: auto;  
            z-index: 999;  
            border-right: 1px solid #37474f;  
        }  

        .sidebar a {  
            color: white;  
            text-decoration: none;  
            padding: 1rem;  
            display: block;  
        }  

        .sidebar a:hover {  
            background-color: #37474f;  
        }  

        .main-content {  
            position: fixed;  
            top: 80px;  
            left: 250px;  
            right: 0;  
            bottom: 0;  
            padding: 2rem;  
            overflow-y: auto;  
            background-color: #f5f5f5;  
            z-index: 998;  
        }  

        .container {  
            max-width: 100%;  
            margin: 0 auto;  
            padding: 0;  
            background-color: white;  
            border-radius: 8px;  
            padding: 2rem;  
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);  
            width: 100%;  
        }  

        .container h2 {  
            color: #333;  
            margin-bottom: 2rem;  
            font-weight: 600;  
        }  

        .form-group {  
            margin-bottom: 1.5rem;  
        }  

        .form-control {  
            width: 100%;  
            padding: 0.75rem;  
        }  

        .btn {  
            padding: 0.75rem 1.5rem;  
            font-weight: 500;  
        }  

        .btn-primary {  
            background-color: #1a237e;  
            border-color: #1a237e;  
        }  

        .btn-primary:hover {  
            background-color: #151a64;  
            border-color: #151a64;  
        }  

        .btn-secondary {  
            background-color: #607d8b;  
            border-color: #607d8b;  
        }  

        .btn-secondary:hover {  
            background-color: #455a64;  
            border-color: #455a64;  
        }  

        label {  
            display: block;  
            margin-bottom: 0.5rem;  
            color: #333;  
            font-weight: 500;  
        }  

        textarea {  
            resize: vertical;  
            min-height: 100px;  
        }  

        .form-control:focus {  
            border-color: #1a237e;  
            box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.25);  
        }  

        .alert {  
            padding: 1rem;  
            margin-bottom: 1rem;  
            border-radius: 4px;  
        }  

        .alert-success {  
            background-color: #e8f5e9;  
            color: #1b5e20;  
            border-color: #c8e6c9;  
        }  

        .alert-danger {  
            background-color: #ffebee;  
            color: #c62828;  
            border-color: #ffcdd2;  
        }  

        @media (max-width: 768px) {  
            .sidebar {  
                display: none;  
            }  
            
            .main-content {  
                left: 0;  
                width: 100%;  
            }  
            
            .container {  
                padding: 1rem;  
            }  
        }  

        @media (max-width: 576px) {  
            .container {  
                padding: 0.5rem;  
            }  
        }  

        .form-control {  
            margin-bottom: 1rem;  
            border-radius: 4px;  
            border: 1px solid #ddd;  
        }  

        .btn {  
            margin-right: 0.5rem;  
            border-radius: 4px;  
        }  

        label {  
            color: #333;  
            font-weight: 500;  
        }  
    </style>
</head>
<body class="p-4">
<?php include('includes/header.php'); ?>  
<?php include('includes/sidebar.php'); ?>  


<div class="main-content">
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
