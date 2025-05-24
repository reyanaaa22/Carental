<?php
include 'db.php';

$error = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand']);

    if (!empty($brand)) {
        $stmt = $conn->prepare("INSERT INTO brands (brand_name, creation_date, updation_date) VALUES (?, NOW(), NOW())");
        $stmt->bind_param("s", $brand);

        if ($stmt->execute()) {
            $msg = 'Brand created successfully!';

            // Log the action in the activity_log table
            session_start();
            $admin_id = $_SESSION['admin_id']; // Ensure admin_id is stored in the session
            $action = "Created a new brand: $brand";
            $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
            $log_stmt->bind_param("is", $admin_id, $action);
            $log_stmt->execute();
        } else {
            $error = 'Database error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = 'Brand name cannot be empty.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Brand</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>

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
        .form-container {
            max-width: 1200px;
            width: 100%;
            margin: 30px 0 30px 60px; /* Move container to the left */
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            box-shadow: 0 2px 8px #0001;
            padding: 0 40px; /* Reasonable horizontal padding */
        }
        .form-header {
            background: #faf7f2;
            color: #888;
            font-weight: 500;
            padding: 16px 20px;
            border-bottom: 1px solid #ddd;
            border-radius: 6px 6px 0 0;
            font-size: 1.1rem;
            letter-spacing: 1px;
        }
        .form-body {
            padding: 32px 20px 24px 20px;
        }
        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 32px;
            gap: 24px;
        }
        .form-row label {
            min-width: 160px;
            font-weight: bold;
            font-size: 1.2rem;
            text-align: right;
            margin-right: 16px;
        }
        .form-row input[type="text"] {
            flex: 1;
            font-size: 1.1rem;
            padding: 12px 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .form-actions {
            display: flex;
            align-items: center;
            margin-left: 176px; /* aligns with input */
        }
        button[type="submit"] {
            background-color: #2c5a85;
            color: #fff;
            padding: 14px 32px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background-color: #1a3a57;
        }
        .alert {
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .succWrap {
            background-color: #d4edda;
            color: #155724;
        }
        .errorWrap {
            background-color: #f8d7da;
            color: #721c24;
        }
        h2 {
    text-align: left;
    margin-left: 20px; /* Add some left margin for spacing */
}

    </style>
</head>
<body>

    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>

    <div class="main-content">
        <h2>Create Brand</h2>
        <div class="form-container">
            <div class="form-header">Create Brand</div>
            <div class="form-body">
                <?php if (!empty($error)) { ?>
                    <div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div>
                <?php } elseif (!empty($msg)) { ?>
                    <div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div>
                <?php } ?>

                <form class="form-body" id="brandForm" method="POST" action="create_brand.php">
                    <div class="form-row">
                        <label for="brand"><strong>Brand Name</strong></label>
                        <input type="text" name="brand" id="brand" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
