<?php
session_start();
include_once('../db.php'); // Fix the path and use the same db connection as other files

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include('includes/sidebar.php');
include('includes/header.php');

if (!isset($dbh)) {
    die("Database connection not established.");
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details
$sql = "SELECT * FROM admin WHERE id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$admin_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Admin not found.");
}

$first_name = $row['first_name'];
$last_name = $row['last_name'];
$email = $row['email'];
$phone_number = $row['phone_number'];
$gender = $row['gender'];
$profile_image = $row['profile_image'];
if (empty($profile_image) || !file_exists($profile_image)) {
    $profile_image = 'uploads/default-profile.png';
}
$status = $row['status'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $gender = htmlspecialchars($_POST['gender']);
    $status = htmlspecialchars($_POST['status']);
    $update_image = false;

    try {
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $new_filename = 'profile_' . $admin_id . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_extension, $allowed_types)) {
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    // Delete old profile image if it exists and is not the default image
                    if (!empty($row['profile_image']) && file_exists($row['profile_image']) && 
                        strpos($row['profile_image'], 'default-profile.png') === false) {
                        unlink($row['profile_image']);
                    }
                    $profile_image = $target_file;
                    $update_image = true;
                } else {
                    throw new Exception("Failed to upload profile image.");
                }
            } else {
                throw new Exception("Only JPG, JPEG, PNG, and GIF files are allowed.");
            }
        }

        // Start transaction
        $dbh->beginTransaction();

        // Update admin details
        $sql = "UPDATE admin SET first_name = ?, last_name = ?, email = ?, phone_number = ?, gender = ?, status = ?";
        $params = [$first_name, $last_name, $email, $phone_number, $gender, $status];
        
        if ($update_image) {
            $sql .= ", profile_image = ?";
            $params[] = $profile_image;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $admin_id;

        $stmt = $dbh->prepare($sql);
        if ($stmt->execute($params)) {
            // Log the action
            $action = "Updated profile information" . ($update_image ? ", including profile image" : "");
            $log_stmt = $dbh->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
            $log_stmt->execute([$admin_id, $action]);

            $dbh->commit();
            $_SESSION['success_message'] = "Profile updated successfully!";
            
            // Update session variables
            $_SESSION['admin_profile_image'] = $profile_image;
            
            // Redirect to refresh the page
            header("Location: profile.php");
            exit();
        } else {
            throw new Exception("Failed to update profile.");
        }
    } catch (Exception $e) {
        $dbh->rollBack();
        $error = $e->getMessage();
    }
}

// Display success message if it exists in session
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
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
            background-color: #f8f9fa;
        }

        .main-content {
            position: absolute;
            top: 70px;
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 30px;
            background-color: #f8f9fa;
        }

        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            margin-bottom: -20px;
        }

        .profile-title {
            color: #004153;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .profile-container {
            position: relative;
            width: 150px;
            margin: 0 auto 30px;
        }

        .profile-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #004153;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .profile-container .camera-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background-color: #004153;
            color: #fff;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            font-size: 18px;
            border: 2px solid #fff;
            transition: all 0.3s ease;
        }

        .profile-container .camera-icon:hover {
            background-color: #005f7a;
            transform: scale(1.1);
        }

        .profile-container input[type="file"] {
            display: none;
        }

        .form-label {
            font-weight: 600;
            color: #004153;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 12px;
            height: auto;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #004153;
            box-shadow: 0 0 0 0.2rem rgba(0,65,83,0.25);
        }

        .btn-primary {
            background-color: #004153;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #005f7a;
            transform: translateY(-2px);
        }

        .form-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            color: #004153;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .alert {
            border-radius: 8px;
            padding: 15px 20px;
        }

        /* Override header profile image size */
        header .profile-container img {
            width: 45px !important;
            height: 45px !important;
            border-width: 1px !important;
            margin-bottom: -22px;
            margin-left: 20px
        }

        /* Align admin name with profile image */
        header .profile-container span {
            display: inline-block;
            vertical-align: middle;
            margin-top: 12px;
            margin-left: -5px;
            font-size: 16px;
        }

        header .profile-container {
            display: flex;
            align-items: center;
            height: 60px;
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="profile-card">
        <div class="profile-header">
            <h2 class="profile-title">Profile Settings</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
        </div>

        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <!-- Profile Image Section -->
            <div class="form-section">
                <div class="profile-container">
                    <img src="<?= htmlspecialchars($profile_image) ?>" alt="Profile Image" id="profilePreview">
                    <label for="profile_image" class="camera-icon" title="Change Profile Picture">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)" style="display: none;">
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section">
                <h3 class="section-title">Personal Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section">
                <h3 class="section-title">Contact Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($phone_number) ?>" placeholder="Enter your phone number">
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="form-section">
                <h3 class="section-title">Additional Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Account Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= $status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview the uploaded profile image
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            // Update the form preview
            const output = document.getElementById('profilePreview');
            output.src = reader.result;
            
            // Update the header profile image
            const headerImg = document.querySelector('header .profile-container img');
            if (headerImg) {
                headerImg.src = reader.result;
            }
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    // If there was a successful update, refresh all profile images
    <?php if (isset($success)): ?>
    window.onload = function() {
        const profileImage = "<?php echo htmlspecialchars($profile_image); ?>";
        // Update header profile image with cache-busting
        const headerImg = document.querySelector('header .profile-container img');
        if (headerImg) {
            headerImg.src = profileImage + '?t=' + new Date().getTime();
        }
    };
    <?php endif; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

