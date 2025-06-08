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
                if (!mkdir($target_dir, 0777, true)) {
                    throw new Exception("Failed to create uploads directory. Please check permissions.");
                }
            }
            
            if (!is_writable($target_dir)) {
                throw new Exception("Uploads directory is not writable. Please check permissions.");
            }
            
            $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $new_filename = 'profile_' . $admin_id . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Validate file type and MIME type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['profile_image']['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($file_extension, $allowed_types) || !in_array($mime_type, $allowed_mimes)) {
                throw new Exception("Only JPG, JPEG, PNG, and GIF files are allowed. Detected MIME type: " . $mime_type);
            }
            
            // Validate file size (max 5MB)
            if ($_FILES['profile_image']['size'] > 5 * 1024 * 1024) {
                throw new Exception("File is too large. Maximum size is 5MB.");
            }
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Delete old profile image if it exists and is not the default image
                if (!empty($row['profile_image']) && file_exists($row['profile_image']) && 
                    strpos($row['profile_image'], 'default-profile.png') === false) {
                    unlink($row['profile_image']);
                }
                $profile_image = $target_file;
                $update_image = true;
            } else {
                $upload_error = error_get_last();
                throw new Exception("Failed to upload profile image. Error: " . ($upload_error['message'] ?? 'Unknown error'));
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: linear-gradient(135deg, #004153 0%, #006080 100%);
        }

        .main-content {
            position: absolute;
            top: 70px;
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 40px;
            background: transparent;
            z-index: 1;
        }

        /* Animated bubbles */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 8s infinite ease-in-out;
            z-index: 0;
        }

        .bubble:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            background: rgba(0, 255, 204, 0.18);
            animation-delay: 0s;
        }

        .bubble:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 20%;
            background: rgba(0, 153, 255, 0.15);
            animation-delay: 2s;
        }

        .bubble:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 30%;
            left: 80%;
            background: rgba(255, 255, 255, 0.12);
            animation-delay: 4s;
        }

        .bubble:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 75%;
            left: 60%;
            background: rgba(0, 255, 153, 0.13);
            animation-delay: 1s;
        }

        .bubble:nth-child(5) {
            width: 90px;
            height: 90px;
            top: 10%;
            left: 50%;
            background: rgba(0, 255, 204, 0.10);
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-40px) scale(1.1);
            }
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-title {
            color: #004153;
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .profile-container {
            position: relative;
            width: 180px;
            margin: 0 auto 40px;
        }

        .profile-container img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #004153;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
        }

        .profile-container .camera-icon {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #004153;
            color: #fff;
            border-radius: 50%;
            padding: 12px;
            cursor: pointer;
            font-size: 22px;
            border: 3px solid #fff;
            transition: all 0.3s ease;
        }

        .form-label {
            font-weight: 600;
            color: #004153;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .form-control, .form-select {
            border: 1px solid #ced4da;
            border-radius: 10px;
            padding: 14px;
            height: auto;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus, .form-select:focus {
            border-color: #004153;
            box-shadow: 0 0 0 0.25rem rgba(0,65,83,0.25);
            background: #fff;
        }

        .btn-primary {
            background: linear-gradient(90deg, #004153 0%, #006080 100%);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #006080 0%, #004153 100%);
            transform: translateY(-2px);
        }

        .form-section {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .section-title {
            color: #004153;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(0,65,83,0.1);
        }

        .alert {
            border-radius: 10px;
            padding: 18px 25px;
            margin-bottom: 25px;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .main-content {
                left: 0;
                width: 100%;
                padding: 20px;
            }

            .profile-card {
                margin: 0 10px;
            }

            .profile-title {
                font-size: 28px;
            }

            .profile-container {
                width: 150px;
            }

            .profile-container img {
                width: 150px;
                height: 150px;
            }

            .form-section {
                padding: 20px;
            }
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
    <!-- Add bubbles -->
    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>
    
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
        const file = event.target.files[0];
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, or GIF)');
            event.target.value = ''; // Clear the file input
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File is too large. Maximum size is 5MB.');
            event.target.value = ''; // Clear the file input
            return;
        }
        
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
        reader.onerror = function() {
            alert('Error reading the image file. Please try again.');
            event.target.value = ''; // Clear the file input
        };
        reader.readAsDataURL(file);
    }

    // If there was a successful update, refresh all profile images
    <?php if (isset($success)): ?>
    window.onload = function() {
        const profileImage = "<?php echo htmlspecialchars($profile_image); ?>";
        const timestamp = new Date().getTime();
        
        // Update both profile images with cache-busting
        const images = [
            document.getElementById('profilePreview'),
            document.querySelector('header .profile-container img')
        ];
        
        images.forEach(img => {
            if (img) {
                img.src = profileImage + '?t=' + timestamp;
            }
        });
    };
    <?php endif; ?>

    // Add form submission handling
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving Changes...';
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

