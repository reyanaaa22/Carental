<?php
session_start();
include('db.php');

// Initialize variables
$error = "";
$max_attempts = 5; // Maximum failed login attempts
$lockout_time = 300; // Lockout time in seconds (5 minutes)

// reCAPTCHA keys
$recaptcha_site_key = '6LeY3TwrAAAAAKyLLLsFmTXtDKvvLTeyUcNnUX5W';
$recaptcha_secret_key = '6LeY3TwrAAAAACzCiOrM_NrfuSlBfGYR5ml_orT5';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // Verify reCAPTCHA first
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    $recaptcha_options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_success = json_decode($recaptcha_result)->success ?? false;

    if (!$recaptcha_success) {
        $error = "Please complete the reCAPTCHA verification.";
    } else {
        // Check if the account is locked
        $check_lock = $conn->prepare("SELECT failed_attempts, last_failed_login FROM admin WHERE email = ?");
        $check_lock->bind_param("s", $email);
        $check_lock->execute();
        $lock_result = $check_lock->get_result();

        if ($lock_result->num_rows == 1) {
            $admin = $lock_result->fetch_assoc();

            // Check if the account is locked
            if ($admin['failed_attempts'] >= $max_attempts && (time() - strtotime($admin['last_failed_login'])) < $lockout_time) {
                $error = "Account locked. Please try again after 5 minutes.";
            } else {
                // Verify email and password
                $sql = "SELECT * FROM admin WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $admin = $result->fetch_assoc();

                    if (password_verify($password, $admin['password'])) {
                        // Reset failed attempts on successful login
                        $reset_attempts = $conn->prepare("UPDATE admin SET failed_attempts = 0 WHERE email = ?");
                        $reset_attempts->bind_param("s", $email);
                        $reset_attempts->execute();

                        // Store admin info in session
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_name'] = $admin['first_name'] . " " . $admin['last_name'];
                        $_SESSION['admin_email'] = $admin['email'];

                        // Create login notification
                        $notification_sql = "INSERT INTO notifications (message, is_read, created_at) 
                                          VALUES (?, 0, NOW())";
                        $notification_stmt = $conn->prepare($notification_sql);
                        $login_message = "Admin " . $admin['first_name'] . " " . $admin['last_name'] . " logged in";
                        $notification_stmt->bind_param("s", $login_message);
                        $notification_stmt->execute();

                        // Log successful login in audit log
                        $action = "Successful Login";
                        $ip_address = $_SERVER['REMOTE_ADDR'];
                        $user_agent = $_SERVER['HTTP_USER_AGENT'];
                        $log_sql = "INSERT INTO audit_log (admin_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)";
                        $log_stmt = $conn->prepare($log_sql);
                        $log_stmt->bind_param("isss", $admin['id'], $action, $ip_address, $user_agent);
                        $log_stmt->execute();

                        header("Location: dashboard.php"); // Redirect to dashboard
                        exit();
                    } else {
                        // Increment failed attempts
                        $update_attempts = $conn->prepare("UPDATE admin SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE email = ?");
                        $update_attempts->bind_param("s", $email);
                        $update_attempts->execute();

                        // Log failed login attempt
                        $action = "Failed Login";
                        $ip_address = $_SERVER['REMOTE_ADDR'];
                        $user_agent = $_SERVER['HTTP_USER_AGENT'];
                        $log_sql = "INSERT INTO audit_log (admin_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)";
                        $log_stmt = $conn->prepare($log_sql);
                        $log_stmt->bind_param("isss", $admin['id'], $action, $ip_address, $user_agent);
                        $log_stmt->execute();

                        $error = "Incorrect password.";
                    }
                } else {
                    // Log failed login attempt for non-existent admin
                    $action = "Failed Login - Admin Not Found";
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    $log_sql = "INSERT INTO audit_log (action, ip_address, user_agent) VALUES (?, ?, ?)";
                    $log_stmt = $conn->prepare($log_sql);
                    $log_stmt->bind_param("sss", $action, $ip_address, $user_agent);
                    $log_stmt->execute();
                    
                    $error = "Admin not found.";
                }
            }
        } else {
            $error = "Admin not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    /* General Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #004153;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      position: relative;
      overflow: hidden;
      background: linear-gradient(135deg, #004153 0%, #006080 100%);
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

    /* Container Styles */
    .container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      width: 400px;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      position: relative;
      z-index: 1;
    }

    /* Title */
    .title {
      font-size: 28px;
      font-weight: bold;
      text-align: center;
      margin-bottom: 30px;
      color: #004153;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    /* Form Styles */
    .user-details {
      margin-bottom: 20px;
    }

    .input-box {
      margin-bottom: 15px;
    }

    .input-box span {
      font-size: 14px;
      color: #555;
      display: block;
      margin-bottom: 5px;
    }

    .input-box input {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
      outline: none;
    }

    .input-box input:focus {
      border-color: #0066cc;
    }

    /* Button Styles */
    .button {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 25px;
      width: 100%;
    }

    .button input {
      width: 60%;
      padding: 14px;
      background: linear-gradient(90deg, #004153 0%, #006080 100%);
      color: #fff;
      font-size: 18px;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .button input:hover {
      background: linear-gradient(90deg, #006080 0%, #004153 100%);
      transform: translateY(-2px);
    }

    /* Center reCAPTCHA */
    .g-recaptcha {
      display: flex;
      justify-content: center;
      margin: 20px 0;
    }

    /* Error Message */
    p {
      text-align: center;
      font-size: 14px;
      color: red;
      margin-top: 10px;
    }

    /* Additional Links Styles */
    .links {
      text-align: center;
      margin-top: 15px;
    }

    .links a {
      color: #0066cc;
      text-decoration: none;
      font-size: 14px;
      margin: 0 10px;
    }

    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <!-- Add bubbles -->
  <div class="bubbles">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
  </div>

  <div class="container">
    <div class="title">Login</div>
    <form method="POST" action="">
      <div class="user-details">
        <div class="input-box">
          <span class="details">Email</span>
          <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="input-box">
          <span class="details">Password</span>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
      </div>

      <!-- Google reCAPTCHA -->
      <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>"></div>

      <div class="button">
        <input type="submit" value="Login">
      </div>

      <?php
      if (!empty($error)) {
          echo "<p>$error</p>";
      }
      ?>

    </form>

    <!-- Forgot Password and Register Links -->
    <div class="links">
      <a href="register.php">Register</a>
    </div>
  </div>
</body>
</html>
