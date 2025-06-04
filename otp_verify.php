<?php
// otp_verify.php
session_start();
require_once 'api/auth_api.php';
$auth_api = new UserAuthAPI('ak_46436e6ca705fa9e3ab6793a52c4cf0d');
$host = 'localhost';
$db = 'ocrms';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

// If not coming from registration, redirect to login
if (!isset($_SESSION['pending_email']) || !isset($_SESSION['pending_otp'])) {
    header('Location: login.php');
    exit;
}

// Handle OTP form submission
if (isset($_POST['verify_otp']) && isset($_POST['otp'])) {
    $email = $_SESSION['pending_email'];
    $otp = $_POST['otp'];
    $response = $auth_api->verifyEmail($email, $otp);
    if ($response['status'] === 200 && $response['data']['success']) {
        // Mark user as verified in DB
        $stmt = $conn->prepare("UPDATE tblusers SET is_verified=1 WHERE EmailId=?");
        $stmt->execute([$email]);
        unset($_SESSION['pending_otp'], $_SESSION['pending_email']);
        echo '<script>alert("Email verified successfully! Please login.");window.location.href="login.php";</script>';
        exit;
    } else {
        echo '<script>alert("Invalid OTP. Please try again.");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .otp-container {
            background: #fff; max-width: 400px; margin: 60px auto; padding: 32px 24px;
            border-radius: 12px; box-shadow: 0 4px 24px #0002; position: relative;
        }
        .otp-container h2 { color: #004153; text-align: center; margin-bottom: 18px; }
        .otp-input {
            width: 100%; margin-bottom: 16px; padding: 12px; border: 1px solid #ccc;
            border-radius: 5px; font-size: 1.2rem; text-align: center;
        }
        .otp-btn {
            width: 100%; padding: 12px; background: #1976d2; color: #fff;
            border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer;
            transition: background 0.2s;
        }
        .otp-btn:hover { background: #004153; }
        .error { color: #c00; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="otp-container">
        <h2>Enter Verification Code</h2>
        <?php if (!empty($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; ?>
        <form method="POST">
            <input type="text" name="otp" maxlength="6" class="otp-input" placeholder="Enter OTP code" required autofocus>
            <button type="submit" name="verify_otp" class="otp-btn">Verify</button>
        </form>
        <p style="text-align:center;margin-top:18px;">Didn't receive the code? <a href="login.php">Resend</a></p>
    </div>
</body>
</html>
