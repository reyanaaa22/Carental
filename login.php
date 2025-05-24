<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$host = 'localhost';
$db = 'ocrms'; 
$user = 'root';
$pass = '';

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
    $stmt = $conn->prepare("SELECT failed_attempts FROM tblusers WHERE EmailId = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['failed_attempts'] >= 3) {
        lockAccount($conn, $email);
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

// REGISTER
if (isset($_POST['register'])) {
    $fullname = htmlspecialchars($_POST['fullname']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $contact = htmlspecialchars($_POST['contact']);
    $dob = isset($_POST['dob']) ? $_POST['dob'] : null;
    $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : null;
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    if ($password !== $confirmpassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and include uppercase, lowercase, numbers, and symbols.');</script>";
    } else {
        $stmt = $conn->prepare("SELECT EmailId FROM tblusers WHERE EmailId = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email already registered!');</script>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO tblusers (FullName, EmailId, ContactNumber, dob, address, Password) VALUES (:fullname, :email, :contact, :dob, :address, :password)");
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':dob', $dob);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful. You can now login!');</script>";
                echo "<script>openModal();</script>";
            } else {
                echo "<script>alert('Registration failed. Try again.');</script>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login/Register</title>
    <!-- Add reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal.show {
            display: flex;
        }
        .modal-dialog {
            background: #fff;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .modal-content {
            padding: 20px;
        }
        .modal-header {
            position: relative;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .modal-title {
            font-size: 22px;
            font-weight: bold;
        }
        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 28px;
            cursor: pointer;
            background: none;
            border: none;
            z-index: 10;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-submit {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background: #0056b3;
        }
        .modal-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .modern-login-modal {
            display: none;
            justify-content: center;
            align-items: center;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.4);
        }
        .modern-login-modal.show {
            display: flex;
        }
        .modern-login-content {
            display: flex;
            width: 700px;
            min-height: 500px;
            max-width: 95vw;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            position: relative;
        }
        .modern-login-left {
            background: linear-gradient(135deg, #004153 0%, #00bcd4 100%);
            color: #fff;
            flex: 1.2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            min-width: 0;
        }
        .login-illustration {
            width: 180px;
            margin-bottom: 18px;
        }
        .modern-login-right {
            flex: 1.5;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }
        .modern-login-right form {
            display: flex;
            flex-direction: column;
        }
        .modern-login-right input[type='email'],
        .modern-login-right input[type='password'] {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 1.1rem;
            outline: none;
            transition: border 0.2s;
        }
        .modern-login-right input[type='email']:focus,
        .modern-login-right input[type='password']:focus {
            border: 1.5px solid #6a82fb;
        }
        /* Make all registration form inputs the same size and style */
        #registerform input[type='text'],
        #registerform input[type='email'],
        #registerform input[type='password'],
        #registerform input[type='date'] {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 1.1rem;
            outline: none;
            transition: border 0.2s;
        }
        #registerform input[type='text']:focus,
        #registerform input[type='email']:focus,
        #registerform input[type='password']:focus,
        #registerform input[type='date']:focus {
            border: 1.5px solid #6a82fb;
        }
        .modern-login-btn {
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
        .modern-login-btn:hover {
            background: linear-gradient(90deg, #4B3F72 0%, #6a82fb 100%);
        }
        @media (max-width: 700px) {
            .modern-login-content { flex-direction: column; }
            .modern-login-left, .modern-login-right { padding: 24px 12px; }
            .modern-login-left { align-items: flex-start; }
        }
        /* For single-column (register) modal, override flex */
        .modern-login-modal.single .modern-login-content {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 700px;
            min-height: 500px;
            max-width: 95vw;
        }
        .modern-login-modal.single .modern-login-right {
            flex: 1;
            width: 100%;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
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
      <form method="post">
        <h3 style="margin-bottom: 18px; color: #4B3F72;">Login your account</h3>
        <input type="email" name="email" placeholder="Username" required value="<?php echo isset($_SESSION['last_login_email']) ? htmlspecialchars($_SESSION['last_login_email']) : ''; ?>">
        <input type="password" name="password" placeholder="Password" required>
        <?php if ($show_captcha): ?>
        <!-- Show reCAPTCHA only after 3 failed attempts -->
        <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>" style="margin: 10px 0;"></div>
        <?php endif; ?>
        <a href="#" style="font-size: 0.95rem; color: #7a7a7a; float:right; margin-bottom: 10px;">Forgot password?</a>
        <input type="submit" name="login" value="Login" class="modern-login-btn">
        <div style="margin-top: 18px; text-align: center;">
          <a href="javascript:void(0);" onclick="closeModal(); openRegisterForm();" style="color: #4B3F72;">Create Account</a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- REGISTER MODAL -->
<div class="modern-login-modal single" id="registerform">
  <div class="modern-login-content">
    <div class="modern-login-right">
      <form method="post">
        <h3 style="margin-bottom: 18px; color: #4B3F72;">Create your account</h3>
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email address" required>
        <input type="text" name="contact" placeholder="Contact Number" required>
        <input type="date" name="dob" placeholder="Date of Birth" required>
        <input type="text" name="address" placeholder="Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirmpassword" placeholder="Confirm Password" required>
        <input type="submit" name="register" value="Register" class="modern-login-btn">
        <div style="margin-top: 18px; text-align: center;">
          <a href="javascript:void(0);" onclick="closeRegisterModal(); openModal();" style="color: #4B3F72;">Already have an account? Login</a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL SCRIPTS -->
<script>
function openModal() {
    document.getElementById('loginform').classList.add('show');
}
function closeModal() {
    document.getElementById('loginform').classList.remove('show');
}
function openRegisterForm() {
    document.getElementById('registerform').classList.add('show');
}
function closeRegisterModal() {
    document.getElementById('registerform').classList.remove('show');
}
// Logout confirmation
function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php?logout=1';
    }
}
<?php if (!isset($_SESSION['login'])): ?>
    openModal();
<?php endif; ?>
// Show logout success alert if redirected after logout
<?php if (isset($_GET['logged_out']) && $_GET['logged_out'] == 1): ?>
    alert('Logout successfully');
<?php endif; ?>
</script>

</body>
</html>
