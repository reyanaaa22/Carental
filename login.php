<?php
    session_start();
require_once('db.php');
require_once('phpmailer.php');
require_once('api/auth_api.php');
require_once('functions.php');

// API configuration
$api_key = 'ak_5a451330459ee6c400ce7efd37e39076';
$api_base_url = 'https://c705-122-54-183-231.ngrok-free.app';

// Initialize the API
$auth_api = new UserAuthAPI($api_key);

// reCAPTCHA configuration
$recaptcha_site_key = '6LeY3TwrAAAAAKyLLLsFmTXtDKvvLTeyUcNnUX5W';
$recaptcha_secret_key = '6LeY3TwrAAAAACzCiOrM_NrfuSlBfGYR5ml_orT5';

// Database connection
try {
    $conn = new PDO("mysql:host=localhost;dbname=ocrms", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add failed_attempts and lock_until columns if they don't exist
    $conn->exec("ALTER TABLE tblusers ADD COLUMN IF NOT EXISTS failed_attempts INT DEFAULT 0");
    $conn->exec("ALTER TABLE tblusers ADD COLUMN IF NOT EXISTS lock_until DATETIME DEFAULT NULL");
    $conn->exec("ALTER TABLE tblusers ADD COLUMN IF NOT EXISTS last_login DATETIME DEFAULT NULL");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function logEvent($conn, $userId, $event, $ipAddress, $userAgent) {
    $stmt = $conn->prepare("INSERT INTO user_logs (user_id, event, ip_address, user_agent, created_at) VALUES (:user_id, :event, :ip_address, :user_agent, NOW())");
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

    if ($user && $user['lock_until'] !== null) {
        if (strtotime($user['lock_until']) > time()) {
        return true;
        } else {
            // Lock period has expired, reset the counter
            resetFailedAttempts($conn, $email);
            return false;
        }
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
    if (empty($recaptcha_response)) {
        return false;
    }

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
    
    if ($result === false) {
        return false;
    }
    
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
    $stmt = $conn->prepare("SELECT failed_attempts FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['failed_attempts'] >= 3) {
        lockAccount($conn, $email);
    }
}

function updateLastLogin($conn, $userId) {
    $stmt = $conn->prepare("UPDATE tblusers SET last_login = NOW() WHERE UserID = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
}

// SESSION TIMEOUT SETTINGS
$session_lifetime = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_lifetime)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

// Show CAPTCHA after 3 failed attempts
$show_captcha = false;
if (isset($_SESSION['last_login_email'])) {
    $stmt = $conn->prepare("SELECT failed_attempts FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $_SESSION['last_login_email']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['failed_attempts'] >= 3) {
        $show_captcha = true;
    }
}

// Handle login form submission
if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    $_SESSION['last_login_email'] = $email;

    // Check if account is locked
    if (isAccountLocked($conn, $email)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Account is locked. Please try again after 5 minutes.'
        ];
    }
    // Verify reCAPTCHA if required
    else if ($show_captcha && !verifyRecaptcha($recaptcha_secret_key, $recaptcha_response)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Please complete the reCAPTCHA verification.'
        ];
    } else {
        try {
            // First verify with local database
            $stmt = $conn->prepare("SELECT * FROM tblusers WHERE EmailId = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['Password'])) {
                // If local verification succeeds, verify with API
                error_log("Local verification successful for email: " . $email);
                error_log("Attempting API verification...");

                // Prepare API call
                $api_endpoint = $api_base_url . '/api/login.php';
                $payload = json_encode([
                    'email' => $email,
                    'password' => $password
                ]);

                $ch = curl_init($api_endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'X-API-Key: ' . $api_key
                ]);

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                error_log("API Raw Response: " . $response);
                error_log("HTTP Code: " . $http_code);

                $api_response = json_decode($response, true);

                if ($http_code === 200 && isset($api_response['success']) && $api_response['success'] === true) {
                    error_log("API verification successful");
                    // Reset failed attempts
                    resetFailedAttempts($conn, $email);
                    
                    // Update last login time
                    updateLastLogin($conn, $user['UserID']);

                    // Set session variables
                    $_SESSION['login'] = $email;
                    $_SESSION['user_id'] = $user['UserID'];
                    $_SESSION['fname'] = $user['FullName'];
                    $_SESSION['role'] = $user['role'] ?? 'user';
                    
                    // Store API auth token
                    if (isset($api_response['data']['auth_token'])) {
                        $_SESSION['auth_token'] = $api_response['data']['auth_token'];
                    }
                    
                    // Log successful login
                    logEvent($conn, $user['UserID'], 'login_success', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                    
                    // Log to users_activity_log with proper connection
                    logUserActivity($conn, $user['UserID'], 'Logged in successfully');
                    
                    $_SESSION['alert'] = [
                        'type' => 'success',
                        'message' => 'Login successful!'
                    ];
                    
                    header('Location: index.php');
                    exit;
                } else {
                    // API verification failed
                    error_log("API verification failed");
                    error_log("Response: " . print_r($api_response, true));
                    
                    incrementFailedAttempts($conn, $email);
                    logEvent($conn, $user['UserID'], 'login_failed_api', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                    
                    $_SESSION['alert'] = [
                        'type' => 'error',
                        'message' => $api_response['message'] ?? 'Authentication failed. Please try again.'
                    ];
                }
            } else {
                // Local verification failed
                if ($user) {
                    incrementFailedAttempts($conn, $email);
                    logEvent($conn, $user['UserID'], 'login_failed_password', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                }
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => 'Invalid email or password.'
                ];
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'An error occurred. Please try again later.'
            ];
        }
    }
}

// Handle session timeout message
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Your session has expired. Please login again.'
    ];
}

// Handle logout message
if (isset($_GET['logged_out']) && $_GET['logged_out'] == 1) {
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'You have been successfully logged out.'
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login/Register</title>
    <!-- Add reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Add external CSS and JavaScript -->
    <link rel="stylesheet" href="style.css">
</head>
<body <?php if (!isset($_SESSION['login'])) echo 'onload="openModal()"'; ?>>

<!-- LOGIN MODAL -->
<div class="modern-login-modal" id="loginform">
  <div class="modern-login-content">
    <button class="close" onclick="closeModal()">&times;</button>
    <div class="modern-login-left">
      <img src="images/blue.png" alt="Welcome" class="login-illustration">
      <h2>Hello!<br><span style="font-weight:400;">Welcome</span></h2>
      <p style="margin-top: 20px; color: #fff; font-size: 1rem;">Login your account to get full user experience</p>
    </div>
    <div class="modern-login-right">
      <?php if (isset($_SESSION['alert'])): ?>
        <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
          <?php echo $_SESSION['alert']['message']; ?>
        </div>
        <?php unset($_SESSION['alert']); ?>
      <?php endif; ?>
      <form method="post">
        <h3 style="margin-bottom: 18px; color: #4B3F72;">Login your account</h3>
        <p style="margin-bottom: 10px; color: #7a7a7a;">Enter your credentials to access your account</p>
        <input type="email" name="email" placeholder="Username" required value="<?php echo isset($_SESSION['last_login_email']) ? htmlspecialchars($_SESSION['last_login_email']) : ''; ?>">
        <input type="password" name="password" placeholder="Password" required>
        <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>" style="margin: 10px 0;"></div>
        <a href="reset_password.php" style="font-size: 0.95rem; color: #7a7a7a; float:right; margin-bottom: 10px;">Forgot password?</a>
        <input type="submit" name="login" value="Login" class="modern-login-btn">
        <div style="margin-top: 18px; text-align: center;">
           <a href="register.php" style="color: #4B3F72;">Create Account</a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add external JavaScript -->
<script src="script.js"></script>

</body>
</html>
