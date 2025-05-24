<?php
session_start();
include 'db.php'; // Ensure the correct path to db.php
include('includes/header.php');
include('includes/sidebar.php');

if (!isset($conn)) {
    die("Database connection not established.");
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details
$sql = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$first_name = $row['first_name'];
$last_name = $row['last_name'];
$email = $row['email'];
$phone_number = $row['phone_number'];
$gender = $row['gender'];
$profile_image = $row['profile_image'] ?: 'uploads/default-profile.png'; // Default image if no profile picture
$status = $row['status'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $gender = htmlspecialchars($_POST['gender']);
    $status = htmlspecialchars($_POST['status']);

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = $target_file;
            } else {
                $error = "Failed to upload profile image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    // Update admin details
    $stmt = $conn->prepare("UPDATE admin SET first_name = ?, last_name = ?, email = ?, phone_number = ?, gender = ?, profile_image = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $phone_number, $gender, $profile_image, $status, $admin_id);

    if ($stmt->execute()) {
        $success = "Profile updated successfully.";

        // Log the action in the activity_log table
        $action = "Updated profile information, including profile image.";
        $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
        $log_stmt->bind_param("is", $admin_id, $action);
        $log_stmt->execute();

        // Refresh the page to show updated data
        echo "<script>
            alert('Profile updated successfully!');
            document.getElementById('headerProfileImage').src = '$profile_image';
        </script>";
    } else {
        $error = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            height: 60px; /* Adjusted header height */
            z-index: 999;
            background-color: #004153;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header .profile-container img {
            width: 40px; /* Fixed size for header profile image */
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .sidebar {
            position: fixed;
            top: 70px; /* Matches the new header height */
            left: 0;
            width: 250px;
            height: calc(100vh - 70px); /* Adjusted for the new header height */
            background-color: #f1f1f1;
            padding: 15px;
            overflow-y: auto;
        }

        .main-content {
            position: absolute;
            top: 70px; /* Matches the new header height */
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 70px); /* Adjusted for the new header height */
            overflow-y: auto;
            padding: 20px;
            background-color: #ffffff;
        }

        .profile-container {
            position: relative;
            width: 150px;
            margin: 0 auto 20px;
        }

        .profile-container img {
            width: 150px;
            height: 150px; /* Make the height equal to the width for a perfect circle */
            border-radius: 50%; /* This makes the image circular */
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .profile-container .camera-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #004153;
            color: #fff;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            font-size: 18px;
            border: 2px solid #fff;
        }

        .profile-container input[type="file"] {
            display: none;
        }
        h2{
          font-size: 40px;
          font-weight: bold;
          color: #004153;
        }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="text-center">Admin Profile</h2>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <!-- Profile Image -->
        <div class="profile-container">
            <img src="<?= htmlspecialchars($profile_image) ?>" alt="Profile Image" id="profilePreview">
            <label for="profile_image" class="camera-icon">
                <i class="fas fa-camera"></i>
            </label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)">
        </div>

        <!-- First Name -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
        </div>

        <!-- Last Name -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <!-- Phone Number -->
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($phone_number) ?>">
        </div>

        <!-- Gender -->
        <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender" required>
                <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<script>
    // Preview the uploaded profile image
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('profilePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

