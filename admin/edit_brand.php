<?php
// edit_brand.php
include 'db.php';
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid brand ID.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brand_name = $_POST['brand_name'];

    $stmt = $conn->prepare("UPDATE brands SET brand_name = ?, updation_date = NOW() WHERE id = ?");
    $stmt->bind_param("si", $brand_name, $id);

    if ($stmt->execute()) {
        // Log the action in the activity_log table
        $admin_id = $_SESSION['admin_id']; // Ensure admin_id is stored in the session
        $action = "Updated brand: $brand_name";
        $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
        $log_stmt->bind_param("is", $admin_id, $action);
        $log_stmt->execute();

        header("Location: manage_brand.php?status=updated");
        exit();
    } else {
        echo "Error updating brand.";
    }
}

// Fetch brand details
$stmt = $conn->prepare("SELECT * FROM brands WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$brand = $result->fetch_assoc();

if (!$brand) {
    die("Brand not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            z-index: 999;
            background-color: #004153;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            position: fixed;
            top: 60px; /* height of the header */
            left: 0;
            width: 250px;
            height: calc(100vh - 60px);
            background-color: #f1f1f1;
            padding: 15px;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
            background-color: #ffffff;
            min-height: calc(100vh - 60px);
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h4 {
            font-size: 24px;
            font-weight: bold;
            color: #004153;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .btn-primary {
            background-color: #004153;
            border-color: #004153;
        }

        .btn-primary:hover {
            background-color: #003040;
            border-color: #003040;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="main-content">
    <div class="container">
        <h4>Edit Brand</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Brand Name</label>
                <input type="text" name="brand_name" class="form-control" value="<?php echo htmlspecialchars($brand['brand_name']); ?>" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Update Brand</button>
                <a href="manage_brand.php" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
