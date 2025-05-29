<?php
session_start();
include('db.php'); // Connect to DB

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch brands from the database
$brands = [];
$brand_query = "SELECT brand_name FROM brands";
$result = $conn->query($brand_query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
} else {
    $error = "Error fetching brands: " . $conn->error;
}

// Process the form when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form inputs
    $vehicle_title = $conn->real_escape_string($_POST['vehicle_title']);
    $brand_name = $conn->real_escape_string($_POST['brand']);
    $vehicle_overview = $conn->real_escape_string($_POST['vehicle_overview']);
    $price_per_day = $conn->real_escape_string($_POST['price_per_day']);
    $fuel_type = $conn->real_escape_string($_POST['fuel_type']);
    $model_year = (int)$_POST['model_year'];
    $seating_capacity = (int)$_POST['seating_capacity'];
    $accessories = isset($_POST['accessories']) ? implode(',', $_POST['accessories']) : '';

    // Image Upload Handling
    $uploaded_images = [];
    $upload_dir = 'uploads/'; // Make sure this folder exists and is writable

    // Initialize uploaded images array
    $uploaded_images = [];
    
    // Check if upload directory exists and is writable
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $error = "Error: Could not create upload directory";
        }
    }
    if (!is_writable($upload_dir)) {
        $error = "Error: Upload directory is not writable";
    }

    if (empty($error)) {
        // Handle image uploads first
        for ($i = 1; $i <= 5; $i++) {
            $image_field = 'image' . $i;
            
            // Only require images 1-3, 4 and 5 are optional
            if ($i <= 3) {
                if (!isset($_FILES[$image_field]) || $_FILES[$image_field]['error'] != UPLOAD_ERR_OK) {
                    $error = "Error uploading required image $i";
                    break;
                }
                
                // Process required image
                $image_name = basename($_FILES[$image_field]['name']);
                $target_file = $upload_dir . $image_name;

                // Check if file already exists
                if (file_exists($target_file)) {
                    // Generate a new unique file name
                    $file_info = pathinfo($image_name);
                    $file_name_without_extension = $file_info['filename'];
                    $file_extension = $file_info['extension'];
                    $counter = 1;

                    // Generate a new name by appending a counter
                    while (file_exists($target_file)) {
                        $new_image_name = $file_name_without_extension . "_" . $counter . "." . $file_extension;
                        $target_file = $upload_dir . $new_image_name;
                        $counter++;
                    }
                    $image_name = $new_image_name;
                }

                // Move uploaded file
                if (move_uploaded_file($_FILES[$image_field]['tmp_name'], $target_file)) {
                    $uploaded_images[] = $image_name;
                } else {
                    $error = "Error moving uploaded file for image $i";
                    break;
                }
            } else {
                // Handle optional images 4 and 5
                if (isset($_FILES[$image_field]) && $_FILES[$image_field]['error'] == UPLOAD_ERR_OK) {
                    $image_name = basename($_FILES[$image_field]['name']);
                    $target_file = $upload_dir . $image_name;

                    // Check if file already exists
                    if (file_exists($target_file)) {
                        // Generate a new unique file name
                        $file_info = pathinfo($image_name);
                        $file_name_without_extension = $file_info['filename'];
                        $file_extension = $file_info['extension'];
                        $counter = 1;

                        // Generate a new name by appending a counter
                        while (file_exists($target_file)) {
                            $new_image_name = $file_name_without_extension . "_" . $counter . "." . $file_extension;
                            $target_file = $upload_dir . $new_image_name;
                            $counter++;
                        }
                        $image_name = $new_image_name;
                    }

                    // Move uploaded file
                    if (move_uploaded_file($_FILES[$image_field]['tmp_name'], $target_file)) {
                        $uploaded_images[] = $image_name;
                    } else {
                        $error = "Error moving uploaded file for image $i";
                        break;
                    }
                } else {
                    // No error for missing optional image
                    $uploaded_images[] = '';
                }
            }
        }

        if (empty($error)) {
            // Prepare the INSERT SQL
            $stmt = $conn->prepare("INSERT INTO vehicles (
                vehicle_title, brand_name, vehicle_overview, price_per_day, fuel_type, model_year,
                seating_capacity, image1, image2, image3, image4, image5, accessories
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Ensure all image values are strings
            $image1 = isset($uploaded_images[0]) ? $uploaded_images[0] : '';
            $image2 = isset($uploaded_images[1]) ? $uploaded_images[1] : '';
            $image3 = isset($uploaded_images[2]) ? $uploaded_images[2] : '';
            $image4 = isset($uploaded_images[3]) ? $uploaded_images[3] : '';
            $image5 = isset($uploaded_images[4]) ? $uploaded_images[4] : '';

            $stmt->bind_param("sssssiissssss",
                $vehicle_title,
                $brand_name,
                $vehicle_overview,
                $price_per_day,
                $fuel_type,
                $model_year,
                $seating_capacity,
                $image1,
                $image2,
                $image3,
                $image4,
                $image5,
                $accessories
            );

            if ($stmt->execute()) {
                // Log the action in the activity_log table
                try {
                    $admin_id = $_SESSION['admin_id'];
                    $action = "Posted a new vehicle: $vehicle_title";
                    $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
                    $log_stmt->bind_param("is", $admin_id, $action);
                    $log_stmt->execute();
                    $msg = "Vehicle posted successfully!";
                } catch (Exception $e) {
                    $error = "Error logging activity: " . $e->getMessage();
                }
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}

// Close database connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Post A Vehicle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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
            width: 100%;
            height: 100px;
            z-index: 999;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 100px; /* height of the header */
            left: 0;
            width: 250px;
            height: calc(100vh - 100px);
            background-color: #f1f1f1;
            padding: 15px;
            overflow-y: auto;
        }

        .main-content {
            position: absolute;
            top: 100px;
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 100px);
            overflow-y: auto;
            padding: 20px;
            background-color: #ffffff;
        }

        .required-star {
            color: red;
        }

        .alert {
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            background-color: #ffffff;
        }

        .errorWrap {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .succWrap {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        fieldset {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 8px;
        }

        .form-control, .form-select {
            margin-bottom: 15px;
        }
        h4{
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="main-content">
    <h4>Post A Vehicle</h4>

    <?php if (!empty($error)) { ?>
        <div class="alert errorWrap">
            <strong>ERROR</strong>: <?php echo htmlentities($error); ?>
        </div>
    <?php } elseif (!empty($msg)) { ?>
        <div class="alert succWrap">
            <strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?>
        </div>
    <?php } ?>

    <form action="post_vehicles.php" method="post" enctype="multipart/form-data">
        <!-- Basic Info Section -->
        <fieldset class="border p-3 mb-4">
            <legend class="float-none w-auto px-2 small text-muted border rounded bg-light">BASIC INFO</legend>

            <div class="row mb-3 align-items-center">
                <label for="vehicleTitle" class="col-sm-2 col-form-label">Vehicle Title<span class="required-star">*</span></label>
                <div class="col-sm-4">
                    <input type="text" id="vehicleTitle" name="vehicle_title" class="form-control" required />
                </div>

                <label for="selectBrand" class="col-sm-2 col-form-label">Select Brand<span class="required-star">*</span></label>
                <div class="col-sm-4">
                    <select id="selectBrand" name="brand" class="form-select" required>
                        <option value="" selected disabled>Select</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= htmlspecialchars($brand['brand_name']) ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="vehicleOverview" class="form-label">Vehicle Overview<span class="required-star">*</span></label>
                <textarea id="vehicleOverview" name="vehicle_overview" class="form-control" rows="3" required></textarea>
            </div>

            <div class="row mb-3 align-items-center">
                <label for="pricePerDay" class="col-sm-2 col-form-label">Price Per Day<span class="required-star">*</span></label>
                <div class="col-sm-4">
                    <input type="number" min="0" step="0.01" id="pricePerDay" name="price_per_day" class="form-control" required />
                </div>

                <label for="fuelType" class="col-sm-2 col-form-label">Select Fuel Type<span class="required-star">*</span></label>
                <div class="col-sm-4">
                    <select id="fuelType" name="fuel_type" class="form-select" required>
                        <option value="" selected disabled>Select</option>
                        <option value="petrol">Petrol</option>
                        <option value="diesel">Diesel</option>
                        <option value="electric">Electric</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label for="modelYear" class="col-sm-2 col-form-label">Model Year<span class="required-star">*</span></label>
                <div class="col-sm-4">
                    <input type="number" min="1900" max="2099" step="1" id="modelYear" name="model_year" class="form-control" required />
                </div>

                <label for="seatingCapacity" class="col-sm-2 col-form-label">Seating Capacity<span class="required-star">*</span></label>
                <div class="col-sm-4">
                    <input type="number" min="1" max="100" step="1" id="seatingCapacity" name="seating_capacity" class="form-control" required />
                </div>
            </div>
        </fieldset>

        <!-- Upload Images Section -->
        <fieldset class="border p-3 mb-4">
            <legend class="float-none w-auto px-2 small text-muted border rounded bg-light">Upload Images</legend>

            <div class="row g-3">
                <?php for (
                    $i = 1; $i <= 5; $i++): ?>
                    <div class="col-md-4">
                        <label for="image<?= $i ?>" class="form-label">
                            Image <?= $i ?>
                            <?php if ($i <= 3): ?>
                                <span style="color: red;">*</span>
                            <?php endif; ?>
                            <?php if ($i > 3): ?> (Optional)<?php endif; ?>
                        </label>
                        <input type="file" class="form-control" name="image<?= $i ?>" id="image<?= $i ?>" <?php if ($i <= 3) echo 'required'; ?> />
                    </div>
                <?php endfor; ?>
            </div>
        </fieldset>

        <!-- Accessories Section -->
        <fieldset class="border p-3 mb-4">
            <legend class="float-none w-auto px-2 small text-muted border rounded bg-light">Accessories</legend>

            <div class="row">
                <?php
                $accessories = [
                    'Air Conditioner', 'Power Door Locks', 'AntiLock Braking System', 
                    'Brake Assist', 'Power Steering', 'Driver Airbag', 
                    'Passenger Airbag', 'Power Windows', 'CD Player', 
                    'Central Locking', 'Crash Sensor', 'Leather Seats'
                ];

                foreach ($accessories as $accessory): ?>
                    <div class="col-sm-3">
                        <input type="checkbox" name="accessories[]" value="<?= $accessory ?>" />
                        <label><?= $accessory ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <button type="submit" name="submit" class="btn btn-primary">Post Vehicle</button>
    </form>
</div>

</body>
</html>
