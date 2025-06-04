<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include API configuration
require_once 'api/auth_api.php';

// Initialize the API
$auth_api = new UserAuthAPI('ak_46436e6ca705fa9e3ab6793a52c4cf0d');

// Function to request OTP
function requestOTP($email) {
    global $auth_api;
    return $auth_api->requestOTP($email, 'email_verification');
}

// Function to verify OTP
function verifyOTP($email, $otp) {
    global $auth_api;
    return $auth_api->verifyEmail($email, $otp);
}

// Function to register user
function registerUser($email, $password, $fullname, $contact, $dob, $address) {
    global $auth_api;
    
    // First register with API
    $response = $auth_api->register($email, $password, $fullname);
    
    if ($response['status'] === 200 && $response['data']['success']) {
        // Store user in local database
        $conn = new PDO("mysql:host=localhost;dbname=ocrms", "root", "");
        $stmt = $conn->prepare("
            INSERT INTO tblusers 
            (FullName, EmailId, Password, ContactNumber, DOB, Address, is_verified) 
            VALUES (?, ?, ?, ?, ?, ?, 0)
        ");
        
        if ($stmt->execute([
            $fullname,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $contact,
            $dob,
            $address
        ])) {
            return true;
        }
    }
    return false;
}

// Database connection (for fallback)
$host = 'localhost';
$db = 'ocrms';
$user = 'root';
$pass = '';

// Initialize the API
$auth_api = new UserAuthAPI('ak_46436e6ca705fa9e3ab6793a52c4cf0d');

// Handle OTP verification
if (isset($_POST['verify_otp']) && isset($_POST['email']) && isset($_POST['otp'])) {
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    
    // Call API to verify OTP
    $response = $auth_api->verifyEmail($email, $otp);
    
    if ($response['status'] === 200 && $response['data']['success']) {
        $_SESSION['success'] = 'Email verified successfully! Please login.';
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Invalid OTP. Please try again.';
    }
}

// Handle OTP request (send OTP email after API call)
require_once __DIR__ . '/phpmailer.php';
if (isset($_POST['request_otp']) && isset($_POST['email'])) {
    $email = $_POST['email'];
    $response = $auth_api->requestOTP($email, 'email_verification');
    if ($response['status'] === 200 && $response['data']['success']) {
        $otp = isset($response['data']['otp']) ? $response['data']['otp'] : null;
        if ($otp) {
            sendOTPEmail($email, $otp, 'email_verification');
        }
        $_SESSION['success'] = 'Verification code sent to your email. Please check your inbox.';
    } elseif ($response['status'] === 429) {
        // Rate limit exceeded
        $wait_time = isset($response['data']['wait_time']) ? $response['data']['wait_time'] : 0;
        $message = isset($response['data']['message']) ? $response['data']['message'] : 'Rate limit exceeded. Please try again later.';
        $_SESSION['error'] = $message;
        echo "<script>alert('{$message}');</script>";
        exit;
    } else {
        $_SESSION['error'] = 'Failed to send verification code. Please try again.';
    }
}

// Handle registration (send OTP email after registration and OTP generation)
if (isset($_POST['register'])) {
    $fullname = htmlspecialchars($_POST['fullname']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $contact = htmlspecialchars($_POST['contact']);
    $dob = isset($_POST['dob']) ? $_POST['dob'] : null;
    $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : null;
    $password = $_POST['password'];
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Password validation
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and include uppercase, lowercase, numbers, and symbols.');</script>";
        exit;
    } elseif ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
        exit;
    }

    // Check if email already exists
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT EmailId FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Email already registered!');</script>";
        exit;
    }

    // 1. Request OTP from API (API generates OTP)
    $otp_response = $auth_api->requestOTP($email, 'email_verification');
    
    // Handle different API response scenarios
    if ($otp_response['status'] === 429) {
        $message = isset($otp_response['data']['message']) ? $otp_response['data']['message'] : 'Rate limit exceeded. Please try again later.';
        echo "<script>alert('{$message}');</script>";
        exit;
    } elseif ($otp_response['status'] !== 200) {
        $error = isset($otp_response['data']['error']) ? $otp_response['data']['error'] : 'Unknown error';
        $message = isset($otp_response['data']['message']) ? $otp_response['data']['message'] : 'Failed to generate OTP. Please try again later.';
        error_log("OTP Generation Error: Status " . $otp_response['status'] . ", Error: " . $error);
        echo "<script>
            alert('{$message}\n\nPlease check:\n1. Your internet connection\n2. That you have entered a valid email address\n3. Try refreshing the page and trying again');
            window.location.href = 'login.php';
        </script>";
        exit;
    }
    
    // Check if OTP was actually generated
    if (empty($otp_response['data']['otp'])) {
        error_log("No OTP received in response: " . json_encode($otp_response));
        echo "<script>alert('Failed to generate OTP. Please try again later.');</script>";
        exit;
    }
    
    $otp = $otp_response['data']['otp'];
    
    // Log successful OTP generation
    error_log("Successfully generated OTP for email: " . $email);

    // 2. Store OTP and email in session for verification step
    $_SESSION['pending_otp'] = $otp;
    $_SESSION['pending_email'] = $email;

    // 3. Register user in DB (unverified)
    $stmt = $conn->prepare("
        INSERT INTO tblusers 
        (FullName, EmailId, Password, ContactNumber, DOB, Address, is_verified) 
        VALUES (?, ?, ?, ?, ?, ?, 0)
    ");
    $stmt->execute([
        $fullname,
        $email,
        password_hash($password, PASSWORD_DEFAULT),
        $contact,
        $dob,
        $address
    ]);

    // 4. Send OTP via PHPMailer (your own Gmail)
    require_once __DIR__ . '/phpmailer.php';
    sendOTPEmail($email, $otp, 'email_verification');

    // 5. Show success message and redirect to OTP verification page
    echo '<script>alert("Registration successful! Please check your email for the verification code.");window.location.href="otp_verify.php";</script>';
    exit;
}

// Database connection (for fallback)
$host = 'localhost';
$db = 'ocrms';
// Initialize the API
$auth_api = new UserAuthAPI('ak_46436e6ca705fa9e3ab6793a52c4cf0d');

// reCAPTCHA configuration
$recaptcha_site_key = '6LeY3TwrAAAAAKyLLLsFmTXtDKvvLTeyUcNnUX5W';
$recaptcha_secret_key = '6LeY3TwrAAAAACzCiOrM_NrfuSlBfGYR5ml_orT5';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add failed_attempts and lock_until columns if they don't exist
    $conn->exec("ALTER TABLE tblusers ADD COLUMN IF NOT EXISTS failed_attempts INT DEFAULT 0");
    $conn->exec("ALTER TABLE tblusers ADD COLUMN IF NOT EXISTS lock_until DATETIME DEFAULT NULL");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function logEvent($conn, $userId, $event, $ipAddress, $userAgent) {
    $stmt = $conn->prepare("INSERT INTO user_logs (user_id, event, ip_address, user_agent) VALUES (:user_id, :event, :ip_address, :user_agent)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':event', $event);
    $stmt->bindParam(':ip_address', $ipAddress);
    $stmt->bindParam(':user_agent', $userAgent);
    $stmt->execute();
}

function isAccountLocked($conn, $email) {
    $stmt = $conn->prepare("SELECT failed_attempts, lock_until FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['failed_attempts'] >= 5 && strtotime($user['lock_until']) > time()) {
        return true;
    }
    return false;
}

function lockAccount($conn, $email) {
    $lockUntil = date("Y-m-d H:i:s", strtotime("+5 minutes"));
    $stmt = $conn->prepare("UPDATE tblusers SET lock_until = :lock_until WHERE EmailId = :email");
    $stmt->bindParam(':lock_until', $lockUntil);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
}

function verifyRecaptcha($recaptcha_secret_key, $recaptcha_response) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result)->success;
}

function resetFailedAttempts($conn, $email) {
    $stmt = $conn->prepare("UPDATE tblusers SET failed_attempts = 0, lock_until = NULL WHERE EmailId = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
}

function incrementFailedAttempts($conn, $email) {
    $stmt = $conn->prepare("UPDATE tblusers SET failed_attempts = failed_attempts + 1 WHERE EmailId = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    // Check if we need to lock the account
    try {
        $stmt = $conn->prepare("SELECT failed_attempts FROM tblusers WHERE EmailId = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['failed_attempts']) && $result['failed_attempts'] >= 3) {
            lockAccount($conn, $email);
        }
    } catch (PDOException $e) {
        // Log the error but don't let it affect the login process
        error_log("Database error: " . $e->getMessage());
    }
}

// SESSION TIMEOUT SETTINGS
$timeout_duration = 900; // 15 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    echo "<script>alert('Session expired. Please login again.'); window.location.href='login.php';</script>";
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

// Determine if we need to show reCAPTCHA
$show_captcha = false;
$email_for_check = '';
if (isset($_POST['email'])) {
    $email_for_check = $_POST['email'];
} elseif (isset($_SESSION['last_login_email'])) {
    $email_for_check = $_SESSION['last_login_email'];
}

if (!empty($email_for_check)) {
    $stmt = $conn->prepare("SELECT failed_attempts FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $email_for_check);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Show CAPTCHA only if failed_attempts is 3 or more
    if ($user && $user['failed_attempts'] >= 3) {
        $show_captcha = true;
    }
}

// LOGIN
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $_SESSION['last_login_email'] = $email; // Save for next reload
    $password = $_POST['password'];
    $recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

    // Only require reCAPTCHA if $show_captcha is true
    if ($show_captcha && !verifyRecaptcha($recaptcha_secret_key, $recaptcha_response)) {
        echo "<script>alert('Please complete the reCAPTCHA verification');</script>";
        exit;
    }

    // Check if account is locked
    if (isAccountLocked($conn, $email)) {
        echo "<script>alert('Account locked. Please try again after 5 minutes.');</script>";
        exit;
    }

    // Fetch user from the database
    $stmt = $conn->prepare("SELECT * FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['Password'])) {
        // Reset failed attempts on successful login
        resetFailedAttempts($conn, $email);

        // Set session variables
        $_SESSION['login'] = $user['EmailId'];
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['fname'] = $user['FullName'];
        $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'user';
        echo "<script>alert('Login successful!'); window.location.href='index.php';</script>";
        exit;
    } else {
        // Increment failed attempts
        incrementFailedAttempts($conn, $email);
        echo "<script>alert('Invalid email or password');</script>";
    }
}

// REMOVE this duplicate registration block below (if present):
/*
if (isset($_POST['register'])) {
    // ...duplicate registration logic...
}
*/
?>

<!-- Login/Register Modal Form -->
<div class="modal-content login-modal" style="background:#fff;max-width:400px;margin:40px auto;padding:32px 24px;border-radius:12px;box-shadow:0 4px 24px #0002;position:relative;">
  <span onclick="document.getElementById('login-form-container').style.display='none'" style="position:absolute;top:12px;right:18px;font-size:24px;cursor:pointer;">&times;</span>
  
  <!-- Login Form -->
  <div id="login-form-section">
    <h2 style="margin-bottom:18px;text-align:center;color:#004153;">Login</h2>
    <form method="POST" action="login.php">
      <input type="email" name="email" placeholder="Email" required class="login-input">
      <input type="password" name="password" placeholder="Password" required class="login-input">
      <button type="submit" name="login" class="login-btn">Login</button>
    </form>
    <div style="text-align:center;margin-top:12px;">
      <span>Don't have an account yet? <a href="#" onclick="showRegister();return false;">Register</a></span>
    </div>
  </div>

  <!-- Register Form -->
  <div id="register-form-section" style="display:none;">
    <h2 style="margin-bottom:18px;text-align:center;color:#1976d2;">Register</h2>
    <form method="POST" action="login.php">
      <input type="text" name="fullname" placeholder="Full Name" required class="login-input">
      <input type="email" name="email" placeholder="Email" required class="login-input">
      <input type="text" name="contact" placeholder="Contact Number" required class="login-input">
      <input type="date" name="dob" placeholder="Date of Birth" class="login-input">
      <input type="text" name="address" placeholder="Address" class="login-input">
      <input type="password" name="password" placeholder="Password" required class="login-input">
      <input type="password" name="confirm_password" placeholder="Confirm Password" required class="login-input">
      <button type="submit" name="register" class="register-btn">Register</button>
    </form>
    <div style="text-align:center;margin-top:12px;">
      <span>Already have an account? <a href="#" onclick="showLogin();return false;">Login</a></span>
    </div>
  </div>
</div>
<script>
function showRegister() {
  document.getElementById('login-form-section').style.display = 'none';
  document.getElementById('register-form-section').style.display = 'block';
}
function showLogin() {
  document.getElementById('register-form-section').style.display = 'none';
  document.getElementById('login-form-section').style.display = 'block';
}
</script>
<style>
.login-modal { font-family: Arial, sans-serif; }
.login-input {
  width: 100%;
  margin-bottom: 12px;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1rem;
}
.login-btn {
  width: 100%;
  padding: 10px;
  background: #004153;
  color: #fff;
  border: none;
  border-radius: 5px;
  font-size: 1rem;
  margin-bottom: 8px;
  cursor: pointer;
  transition: background 0.2s;
}
.login-btn:hover { background: #1976d2; }
.register-btn {
  width: 100%;
  padding: 10px;
  background: #1976d2;
  color: #fff;
  border: none;
  border-radius: 5px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.register-btn:hover { background: #004153; }
</style>
<!-- End Modal Form -->
