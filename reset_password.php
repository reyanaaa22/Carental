<?php
session_start();
require_once('db.php');
require_once('phpmailer.php');

// API configuration - using the same as login.php
$api_key = 'ak_5a451330459ee6c400ce7efd37e39076';
$api_base_url = 'https://c705-122-54-183-231.ngrok-free.app';

// Database connection
try {
    $conn = new PDO("mysql:host=localhost;dbname=ocrms", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Clear any existing reset session data when page is first loaded
if (!isset($_POST['submit_email']) && !isset($_POST['reset_password'])) {
    unset($_SESSION['reset_email'], $_SESSION['reset_step'], $_SESSION['reset_auth_token']);
}

$error = '';
$success = '';
$step = isset($_SESSION['reset_step']) ? $_SESSION['reset_step'] : 1;

function callApi($endpoint, $method = 'POST', $data = null, $auth_token = null) {
    global $api_key, $api_base_url;
    
    $ch = curl_init($api_base_url . $endpoint);
    
    $headers = [
        'Content-Type: application/json',
        'X-API-Key: ' . $api_key
    ];
    
    if ($auth_token) {
        $headers[] = 'Authorization: ' . $auth_token;
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    error_log("API Response ($endpoint): " . $response);
    error_log("HTTP Code: " . $http_code);
    
    curl_close($ch);
    
    return json_decode($response, true);
}

// Step 1: Email submission
if (isset($_POST['submit_email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Check if email exists locally
    $stmt = $conn->prepare("SELECT UserID FROM tblusers WHERE EmailId = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        // Request OTP through API
        $response = callApi('/api/request-otp.php', 'POST', [
            'email' => $email,
            'purpose' => 'password-reset'
        ]);
        
        if (isset($response['success']) && $response['success']) {
            $otp = $response['data']['otp'] ?? null;
            $auth_token = $response['data']['auth_token'] ?? null;
            
            if ($otp && $auth_token) {
                // Send OTP email
                if (sendOTPEmail($email, $otp, 'password-reset')) {
                    // Store OTP and auth token in session
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_auth_token'] = $auth_token;
                    $_SESSION['reset_step'] = 2;
                    $step = 2;
                    $success = 'OTP has been sent to your email address.';
                    
                    // Update local database
                    $stmt = $conn->prepare("UPDATE tblusers SET reset_otp = ? WHERE EmailId = ?");
                    $stmt->execute([$otp, $email]);
                } else {
                    $error = 'Failed to send OTP email. Please try again.';
                }
            } else {
                $error = 'Failed to generate OTP. Please try again.';
            }
        } else {
            $error = $response['message'] ?? 'Failed to request password reset. Please try again.';
        }
    } else {
        $error = 'Email address not found.';
    }
}

// Step 2: OTP verification and new password
if (isset($_POST['reset_password'])) {
    $otp = $_POST['otp'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8 || 
              !preg_match('/[A-Z]/', $password) || 
              !preg_match('/[a-z]/', $password) || 
              !preg_match('/[0-9]/', $password) || 
              !preg_match('/[!@#$%^&*]/', $password)) {
        $error = 'Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.';
    } else {
        // Call reset password API
        $response = callApi('/api/reset-password.php', 'POST', [
            'otp' => $otp,
            'new_password' => $password
        ], $_SESSION['reset_auth_token']);
        
        if (isset($response['success']) && $response['success']) {
            // Update password in local database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE tblusers SET Password = ?, reset_otp = NULL WHERE EmailId = ?");
            $stmt->execute([$hashed_password, $_SESSION['reset_email']]);
            
            // Get user ID for activity logging
            $stmt = $conn->prepare("SELECT UserID FROM tblusers WHERE EmailId = ?");
            $stmt->execute([$_SESSION['reset_email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Log the password reset activity
                require_once('functions.php');
                logUserActivity($conn, $user['UserID'], 'password_reset');
            }
            
            // Clear session variables
            unset($_SESSION['reset_email'], $_SESSION['reset_step'], $_SESSION['reset_auth_token']);
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Password has been reset successfully. You can now login with your new password.'
            ];
            header('Location: login.php');
            exit;
        } else {
            $error = $response['message'] ?? 'Failed to reset password. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Ormoc Car Rental</title>
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
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;
            margin-top: -10px;
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
        }

        .auth-left {
            background: linear-gradient(135deg, rgba(0,65,83,0.95) 0%, rgba(0,96,128,0.95) 100%);
            padding: 40px;
            color: white;
            width: 40%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .auth-left img {
            width: 180px;
            margin-bottom: 20px;
        }

        .auth-right {
            padding: 40px;
            width: 60%;
            background: rgba(255, 255, 255, 0.95);
        }

        .form-title {
            color: #004153;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(0,65,83,0.2);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #004153;
            box-shadow: 0 0 0 2px rgba(0, 65, 83, 0.1);
        }

        .btn-submit {
            width: 70%;
            background: linear-gradient(90deg, #6a82fb 0%, #4B3F72 100%);
            color: #fff;
            justify-content: center;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            margin-left: 70px;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: linear-gradient(90deg, #4B3F72 0%, #6a82fb 100%);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #ffe6e6;
            color: #dc3545;
            border: 1px solid #ffcccc;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .password-requirements {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }

            .auth-left, .auth-right {
                width: 100%;
                padding: 30px;
            }

            .auth-left img {
                width: 140px;
            }
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="main-content">
        <!-- Animated Bubbles -->
        <div class="bubbles">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
        </div>

        <div class="auth-container">
            <div class="auth-left">
                <img src="images/blue.png" alt="Ormoc Car Rental">
                <h2>Reset Password</h2>
                <p>Follow the steps to reset your password securely</p>
            </div>
            <div class="auth-right">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <?php if ($step == 1): ?>
                    <!-- Step 1: Email Form -->
                    <h3 class="form-title">Reset Your Password</h3>
                    <p class="form-subtitle">Enter your email address to receive a verification code</p>
                    <form method="post">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
                        </div>
                        <button type="submit" name="submit_email" class="btn-submit">Send Reset Code</button>
                    </form>

                <?php elseif ($step == 2): ?>
                    <!-- Step 2: OTP Verification and New Password -->
                    <h3 class="form-title">Reset Your Password</h3>
                    <p class="form-subtitle">Enter the verification code and your new password</p>
                    <form method="post" onsubmit="return validatePassword();">
                        <div class="form-group">
                            <input type="text" name="otp" class="form-control" placeholder="Enter 6-digit code" required pattern="[0-9]{6}" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="New password" required autocomplete="new-password">
                            <div class="password-requirements">
                                Password must be at least 8 characters long and include:
                                <ul>
                                    <li>Uppercase letters (A-Z)</li>
                                    <li>Lowercase letters (a-z)</li>
                                    <li>Numbers (0-9)</li>
                                    <li>Special characters (!@#$%^&*)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm new password" required autocomplete="new-password">
                        </div>
                        <button type="submit" name="reset_password" class="btn-submit">Reset Password</button>
                    </form>
                    <script>
                        // Remove any readonly attributes that might be automatically added
                        document.addEventListener('DOMContentLoaded', function() {
                            const inputs = document.querySelectorAll('input');
                            inputs.forEach(input => {
                                input.removeAttribute('readonly');
                            });
                        });
                    </script>
                <?php endif; ?>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="login.php" style="color: #004153; text-decoration: none;">Back to Login</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Password requirements regex
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChar = /[!@#$%^&*]/.test(password);
            const isLongEnough = password.length >= 8;
            
            if (!isLongEnough || !hasUpperCase || !hasLowerCase || !hasNumbers || !hasSpecialChar) {
                alert('Password must be at least 8 characters long and include uppercase letters, lowercase letters, numbers, and special characters.');
                return false;
            }
            
            if (password !== confirmPassword) {
                alert('Passwords do not match.');
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