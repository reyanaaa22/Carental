<?php
session_start();
require_once('db.php'); // assumes $dbh is your PDO instance
require_once('phpmailer.php');
require_once('api/auth_api.php'); // optional if you're using direct curl instead of wrapper

// API configuration
$api_key = 'ak_5a451330459ee6c400ce7efd37e39076';
$api_base_url = 'https://c705-122-54-183-231.ngrok-free.app/api/register.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $profile_image = $_FILES['profile_image'] ?? null;

    // Validate input
    if (empty($fullname)) $errors[] = 'Full name is required';
    if (!preg_match("/^[A-Za-z ]{2,50}$/", $fullname)) $errors[] = 'Full name should only contain letters and spaces (2-50 characters)';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (!preg_match("/^[0-9]{11}$/", $contact)) $errors[] = 'Contact number must be exactly 11 digits';
    if (empty($dob)) $errors[] = 'Date of birth is required';
    if (empty($address)) $errors[] = 'Address is required';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters long';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match';

    // Profile image validation
    if (empty($profile_image['name'])) {
        $errors[] = 'Profile picture is required';
    } elseif ($profile_image['error'] !== 0) {
        $errors[] = 'Error uploading profile picture: ' . getFileUploadError($profile_image['error']);
    } elseif (!in_array(strtolower(pathinfo($profile_image['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
        $errors[] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
    } elseif ($profile_image['size'] > 5 * 1024 * 1024) {
        $errors[] = 'Profile picture size must be less than 5MB.';
    }

    if (empty($errors)) {
        try {
            // Check if email already exists in local DB
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM tblusers WHERE EmailId = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'Email address is already registered locally.';
            } else {
                // --- CALL EXTERNAL API REGISTER ENDPOINT ---
                $payload = json_encode([
                    "email" => $email,
                    "password" => $password
                ]);

                $ch = curl_init($api_base_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json",
                    "X-API-Key: $api_key"
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $api_response = curl_exec($ch);

                echo "<pre>";
print_r($api_response);
echo "</pre>";
//exit;


        
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $response_data = json_decode($api_response, true);

                if ($http_code === 200 && $response_data['success']) {
                    $otp = $response_data['data']['otp'] ?? null;
                    $auth_token = $response_data['data']['auth_token'] ?? null;

                    if ($otp) {
                        // Upload image
                        $image_name = uniqid() . '_' . basename($profile_image['name']);
                        $upload_path = 'uploads/' . $image_name;
                        if (!move_uploaded_file($profile_image['tmp_name'], $upload_path)) {
                            $errors[] = 'Failed to upload profile image.';
                        } else {
                            // Sync to local DB
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $dbh->prepare("INSERT INTO tblusers (FullName, EmailId, Password, ContactNumber, dob, address, profile_image, DateRegistered, is_verified, OTP) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 0, ?)");

                            $success = $stmt->execute([$fullname, $email, $hashed_password, $contact, $dob, $address, $image_name, $otp]);

                            if (!$success) {
                                $errorInfo = $stmt->errorInfo();
                                error_log("DB Insert failed: " . implode(' | ', $errorInfo));
                                $errors[] = 'Failed to save user information.';
                            }
                            
                            // Send OTP email
                            if (sendOTPEmail($email, $otp, 'registration')) {
                                $_SESSION['pending_registration'] = [
                                    'fullname' => $fullname,
                                    'email' => $email,
                                    'contact' => $contact,
                                    'dob' => $dob,
                                    'address' => $address,
                                    'profile_image' => $image_name,
                                    'otp_verified' => 0
                                ];
                                $_SESSION['pending_email'] = $email;
                                $_SESSION['pending_otp'] = $otp;
                                $_SESSION['otp_timestamp'] = time();

                                $_SESSION['alert'] = [
                                    'type' => 'success',
                                    'message' => 'Registration successful! Please check your email for OTP verification.'
                                ];
                                header('Location: otp_verify.php');
                                exit;
                            } else {
                                // Rollback local DB insert
                                $dbh->prepare("DELETE FROM tblusers WHERE EmailId = ?")->execute([$email]);
                                $errors[] = 'Failed to send OTP email. Please try again.';
                            }
                        }
                    } else {
                        $errors[] = 'Failed to retrieve OTP from API.';
                    }
                } else {
                    $errors[] = $response_data['message'] ?? 'API registration failed.';
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Server error: ' . $e->getMessage();
        }
    }
}

function getFileUploadError($code) {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE: return 'File exceeds upload_max_filesize in php.ini';
        case UPLOAD_ERR_FORM_SIZE: return 'File exceeds MAX_FILE_SIZE in form';
        case UPLOAD_ERR_PARTIAL: return 'File partially uploaded';
        case UPLOAD_ERR_NO_FILE: return 'No file uploaded';
        case UPLOAD_ERR_NO_TMP_DIR: return 'Missing temp folder';
        case UPLOAD_ERR_CANT_WRITE: return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION: return 'A PHP extension stopped the upload';
        default: return 'Unknown file upload error';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Ormoc Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #004153 0%, #006080 100%);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            position: relative;
            padding-top: 1px;
            margin-top: 0;
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
            top: 5%;
            right: 50%;
            background: rgba(0, 255, 204, 0.18);
            animation-delay: 0s;
        }

        .bubble:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 80%;
            background: rgba(0, 153, 255, 0.15);
            animation-delay: 2s;
        }

        .bubble:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 10%;
            right: 90%;
            background: rgba(255, 255, 255, 0.12);
            animation-delay: 4s;
        }

        .bubble:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 75%;
            left: 10%;
            background: rgba(0, 255, 153, 0.13);
            animation-delay: 4s;
        }

        .bubble:nth-child(5) {
            width: 90px;
            height: 90px;
            top: 10%;
            left: 20%;
            background: rgba(0, 255, 204, 0.10);
            animation-delay: 3s;
        }

        .bubble:nth-child(6) {
            width: 60px;
            height: 60px;
            top: 90%;
            right: 80%;
            background: rgba(0, 255, 204, 0.10);
            animation-delay: 4s;
        }

        .bubble:nth-child(7) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 90%;
            background: rgba(0, 255, 204, 0.10);
            animation-delay: 3s;
        }
        .bubble:nth-child(8) {
            width: 120px;
            height: 120px;
            top: 70%;
            right: 90%;
            background: rgba(0, 255, 204, 0.10);
            animation-delay: 1s;
        }

        .bubble:nth-child(9) {
            width: 60px;
            height: 60px;
            top: 50%;
            right: 84%;
            background: rgba(0, 255, 204, 0.10);
            animation-delay: 2s;
        }


        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-40px) scale(1.1);
            }
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            width: 900px;
            max-width: 95%;
            display: flex;
            position: relative;
            z-index: 1;
            overflow: hidden;
            margin-top: 20px;
            margin-bottom: -40px;
        }

        .auth-left {
            background: linear-gradient(135deg, rgba(0,65,83,0.95) 0%, rgba(0,96,128,0.95) 100%);
            padding: 20px;
            color: white;
            width: 40%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .auth-left img {
            width: 180px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .auth-left h2 {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .auth-left p {
            font-size: 15px;
        }

        .auth-right {
            padding: 25px;
            width: 60%;
            background: rgba(255, 255, 255, 0.95);
        }

        .form-title {
            color: #004153;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }

        .form-subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid rgba(0,65,83,0.2);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            height: 38px;
        }

        .form-control:focus {
            border-color: #004153;
            box-shadow: 0 0 0 2px rgba(0, 65, 83, 0.1);
            background: #fff;
        }

        .form-control.valid-password {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }

        .form-control.invalid-password {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(90deg, #6a82fb 0%, #4B3F72 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: linear-gradient(90deg, #4B3F72 0%, #6a82fb 100%);
        }

        .auth-links {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .auth-links a {
            color: #004153;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #006080;
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                max-width: 90%;
            }

            .auth-left, .auth-right {
                width: 100%;
                padding: 20px;
            }

            .auth-left img {
                width: 120px;
            }

            .auth-left h2 {
                font-size: 20px;
            }

            .auth-left p {
                font-size: 14px;
            }

            .form-title {
                font-size: 20px;
            }

            .form-subtitle {
                font-size: 13px;
            }

            .form-control {
                height: 36px;
            }
        }

        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.5s ease-out;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include('header.php'); ?>

    <div class="main-content">
        <!-- Animated Bubbles -->
        <div class="bubbles">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
        </div>

        <div class="auth-container">
            <div class="auth-left">
                <img src="images/blue.png" alt="Ormoc Car Rental">
                <h2>Create Account</h2>
                <p>Join us and start your car rental journey</p>
            </div>
            <div class="auth-right">
                <div class="auth-form">
                    <h3 class="form-title">Register New Account</h3>
                    <p class="form-subtitle">Fill in your details to create an account</p>

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="form-group">
                            <input type="text" name="fullname" class="form-control" placeholder="Full Name" required 
                                   pattern="[A-Za-z ]{2,50}" title="Name should only contain letters and spaces (2-50 characters)">
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="contact" class="form-control" placeholder="Contact Number" required
                                   pattern="[0-9]{11}" title="Please enter a valid 11-digit phone number">
                        </div>
                        <div class="form-group">
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="address" class="form-control" placeholder="Address" required>
                        </div>
                        <div class="form-group">
                            <input type="file" name="profile_image" class="form-control" accept="image/*" required
                                   title="Please select a profile picture">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="Password" required onkeyup="validatePassword()">
                        </div>
                        <div class="form-group">
                            <input type="password" name="confirm_password" id="confirm_password" 
                                   class="form-control" placeholder="Confirm Password" required onkeyup="checkPasswordMatch()">
                        </div>

                        <button type="submit" class="btn-submit">Create Account</button>
                    </form>

                    <div class="auth-links">
                        <a href="login.php">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert-container">
        <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
                <?php echo $_SESSION['alert']['message']; ?>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
    </div>

    <script>
        function validatePassword() {
            const password = document.getElementById('password').value;
            const passwordInput = document.getElementById('password');
            
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[@$!%*?&]/.test(password)
            };

            const isValid = Object.values(requirements).every(Boolean);
            
            if (password.length > 0) {
                if (isValid) {
                    passwordInput.classList.remove('invalid-password');
                    passwordInput.classList.add('valid-password');
                } else {
                    passwordInput.classList.remove('valid-password');
                    passwordInput.classList.add('invalid-password');
                }
            } else {
                passwordInput.classList.remove('valid-password', 'invalid-password');
            }

            return isValid;
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmInput = document.getElementById('confirm_password');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    confirmInput.classList.remove('invalid-password');
                    confirmInput.classList.add('valid-password');
                } else {
                    confirmInput.classList.remove('valid-password');
                    confirmInput.classList.add('invalid-password');
                }
            } else {
                confirmInput.classList.remove('valid-password', 'invalid-password');
            }
        }

        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const dob = document.querySelector('input[name="dob"]').value;

            if (!validatePassword()) {
                alert('Please ensure your password meets all requirements:\n- At least 8 characters\n- Contains uppercase and lowercase letters\n- Contains numbers\n- Contains special characters (@$!%*?&)');
                return false;
            }

            if (password !== confirmPassword) {
                alert('Passwords do not match.');
                return false;
            }

            const dobDate = new Date(dob);
            const today = new Date();
            const age = today.getFullYear() - dobDate.getFullYear();
            const monthDiff = today.getMonth() - dobDate.getMonth();
            
            if (age < 18 || (age === 18 && monthDiff < 0)) {
                alert('You must be at least 18 years old to register.');
                return false;
            }

            return true;
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html> 
