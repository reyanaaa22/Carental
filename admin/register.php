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
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
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
    }
    .container {
      background-color: #fff;
      width: 800px;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .title {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #333;
    }
    .user-details {
      margin-bottom: 20px;
    }
    .name-row {
      display: flex;
      gap: 20px;
      justify-content: space-between;
      margin-bottom: 15px;
    }
    .name-row .input-box {
      flex: 1;
    }
    .input-box {
      margin-bottom: 15px;
    }
    .input-box span {
      font-size: 14px;
      color: #555;
      display: block;
      margin-bottom: 5px;
      text-align: left;
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
      margin-top: 20px;
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
    p {
      text-align: center;
      font-size: 14px;
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>
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
        <div class="input-box">
          <span class="details">Email</span>
          <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="input-box">
          <span class="details">Phone Number</span>
          <input type="text" name="phone_number" placeholder="Enter your phone number" required>
        </div>
        <div class="input-box">
          <span class="details">Password</span>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="input-box">
          <span class="details">Confirm Password</span>
          <input type="password" name="confirm_password" placeholder="Confirm your password" required>
        </div>

        <div class="input-box">
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
    </form>
  </div>
</body>
</html>
