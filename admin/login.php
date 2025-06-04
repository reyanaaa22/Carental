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
                        $log_sql = "INSERT INTO audit_log (action, user_email, ip_address, user_agent) VALUES (?, ?, ?, ?)";
                        $log_stmt = $conn->prepare($log_sql);
                        $log_stmt->bind_param("ssss", $action, $email, $ip_address, $user_agent);
                        $log_stmt->execute();

                        $error = "Incorrect password.";
                    }
                } else {
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
    }

    /* Container Styles */
    .container {
      background-color: #fff;
      width: 400px;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Title */
    .title {
      font-size: 24px;
      font-weight: bold;
      text-align: center;
      margin-bottom: 20px;
      color: #333;
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
    }

    .button input {
      width: 100%;
      padding: 12px;
      background-color: #0066cc;
      color: #fff;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .button input:hover {
      background-color: #005bb5;
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
      <a href="forgot-password.php">Forgot Password?</a> | 
      <a href="register.php">Register</a>
    </div>
  </div>
</body>
</html>
