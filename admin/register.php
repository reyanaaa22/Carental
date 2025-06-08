<?php
// Include the database connection file
include('db.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = isset($_POST['gender']) ? implode(", ", $_POST['gender']) : "";

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p>Invalid email format.</p>";
        exit;
    }

    // Validate password complexity
    $password = trim($password);
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        echo "<p>Password must be at least 8 characters long and include uppercase, lowercase, numbers, and symbols.</p>";
        exit;
    }


    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<p>Passwords do not match.</p>";
        exit;
    }

    // Check for duplicate email
    $check_email = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    if ($result->num_rows > 0) {
        echo "<p>Email is already registered.</p>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle image upload
    $targetDir = "uploads/";
    $imageName = basename($_FILES["profile_image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
        // Insert into database
        $sql = "INSERT INTO admin (first_name, last_name, email, phone_number, password, gender, profile_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $first_name, $last_name, $email, $phone_number, $hashed_password, $gender, $imageName);

        if ($stmt->execute()) {
            // Get the admin ID of the newly registered user
            $admin_id = $conn->insert_id;

            // Log the registration event in the audit log
            $action = "User Registration";
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $log_sql = "INSERT INTO audit_log (admin_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("isss", $admin_id, $action, $ip_address, $user_agent);
            $log_stmt->execute();

            // Send a welcome email
            $subject = "Welcome to Ormoc Car Rental!";
            $message = "Hi $first_name,\n\nThank you for registering with Ormoc Car Rental. We're excited to have you on board!";
            $headers = "From: noreply@ormoc_carental.com";
            mail($email, $subject, $message, $headers);

            // Display success alert and redirect to login page
            echo "<script>
                alert('Registration successfully');
                window.location.href = 'login.php';
            </script>";
            exit();
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p>Failed to upload image.</p>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registration Form</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
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

    .bubble:nth-child(6) {
      width: 90px;
      height: 90px;
      top: 50%;
      left: 90%;
      background: rgba(0, 255, 204, 0.10);
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

    .container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      width: 450px;
      padding: 13px;
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      position: relative;
      z-index: 1;
    }
    .title {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 30px;
      color: #004153;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .user-details {
      margin-bottom: 20px;
    }
    .name-row {
      display: flex;
      gap: 40px;
      justify-content: relative;
      margin-bottom: 15px;
    }
    .name-row .input-box .input-box1 {
      flex: 1;
    }
    .input-box, .input-box1 {
      margin-bottom: 15px;
    }
    .input-box span, .input-box1 span {
      font-size: 14px;
      color: #555;
      display: block;
      margin-bottom: 5px;
      text-align: left;
    }
    .input-box1 input {
      width: 80%;
      padding: 8px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 5px;
      outline: none;
    }
    .input-box input {
      width: 100%;
      padding: 8px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 5px;
      outline: none;
    }
    .input-box input:focus, .input-box1 input:focus {
      border-color: #0066cc;
    }
    .gender-details {
      margin-bottom: 20px;
      text-align: left;
    }
    .gender-title {
      font-size: 14px;
      color: #555;
      margin-bottom: 10px;
    }
    .checkbox-group {
      display: flex;
      gap: 20px;
    }
    .checkbox-group label {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 14px;
      color: #555;
    }
    .button {
      margin-top: 25px;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }
    .button input {
      width: 70%;
      padding: 12px;
      background: linear-gradient(90deg, #004153 0%, #006080 100%);
      color: #fff;
      font-size: 16px;
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
    p {
      text-align: center;
      font-size: 14px;
      color: red;
      margin-top: 10px;
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
    <div class="bubble"></div>
  </div>

  <div class="container">
    <div class="title">Registration</div>
    <form method="POST" action="" enctype="multipart/form-data">
      <div class="user-details">
        <div class="name-row">
          <div class="input-box">
            <span class="details">First Name</span>
            <input type="text" name="first_name" placeholder="Enter your First Name" required>
          </div>
          <div class="input-box">
            <span class="details">Last Name</span>
            <input type="text" name="last_name" placeholder="Enter your Last Name" required>
          </div>
        </div>
        <div class="input-box1">
          <span class="details">Email</span>
          <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="input-box1">
          <span class="details">Phone Number</span>
          <input type="text" name="phone_number" placeholder="Enter your phone number" required>
        </div>
        <div class="input-box1">
          <span class="details">Password</span>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="input-box1">
          <span class="details">Confirm Password</span>
          <input type="password" name="confirm_password" placeholder="Confirm your password" required>
        </div>

        <div class="input-box1">
          <span class="details">Profile Picture</span>
          <input type="file" name="profile_image" accept="image/*" required>
        </div>
      </div>

      <div class="gender-details">
        <span class="gender-title">Gender</span>
        <div class="checkbox-group">
          <label><input type="checkbox" name="gender[]" value="Male"> Male</label>
          <label><input type="checkbox" name="gender[]" value="Female"> Female</label>
        </div>
      </div>

      <div class="button">
        <input type="submit" value="Register">
      </div>

      <div class="auth-links">
          <a href="login.php">Already have an account? Login</a>
      </div>
    </form>
  </div>
</body>
</html>
