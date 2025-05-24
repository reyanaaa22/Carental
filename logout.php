<?php
// Start session
session_start();

// Log the logout event if user is logged in
if (isset($_SESSION['user_id'])) {
    // Database connection (same as login.php)
    $host = 'localhost';
    $db = 'ocrms';
    $user = 'root';
    $pass = '';
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("INSERT INTO user_logs (user_id, event, ip_address, user_agent) VALUES (:user_id, :event, :ip_address, :user_agent)");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $event = 'logout';
        $stmt->bindParam(':event', $event);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        $stmt->execute();
    } catch (PDOException $e) {
        // Optionally log error or ignore
    }
}

// Destroy session to log the user out
session_unset();
session_destroy();

// Redirect to home or login page
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    header("Location: login.php?logged_out=1");
} else {
    header("Location: index.php");
}
exit();
?>
