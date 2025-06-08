<?php
// Start session
session_start();
require_once('db.php');
require_once('functions.php');

// API configuration - using the same as login.php
$api_key = 'ak_5a451330459ee6c400ce7efd37e39076';
$api_base_url = 'https://c705-122-54-183-231.ngrok-free.app';

// Function to call API
function callApi($endpoint, $method = 'POST', $auth_token = null) {
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
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    error_log("Logout API Response: " . $response);
    error_log("HTTP Code: " . $http_code);
    
    curl_close($ch);
    
    return [
        'status' => $http_code,
        'data' => json_decode($response, true)
    ];
}

// Call the logout endpoint if user is logged in and has auth token
$api_response = null;
if (isset($_SESSION['auth_token'])) {
    error_log("Attempting API logout with token: " . $_SESSION['auth_token']);
    $api_response = callApi('/api/logout.php', 'POST', $_SESSION['auth_token']);
}

// Log the logout event if user is logged in
if (isset($_SESSION['user_id'])) {
    try {
        $conn = new PDO("mysql:host=localhost;dbname=ocrms", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Log the logout event in user_logs
        $stmt = $conn->prepare("INSERT INTO user_logs (user_id, event, ip_address, user_agent, created_at) VALUES (:user_id, :event, :ip_address, :user_agent, NOW())");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $event = 'logout';
        $stmt->bindParam(':event', $event);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        $stmt->execute();

        // Log to users_activity_log with proper connection
        logUserActivity($conn, $_SESSION['user_id'], 'Logged out');

        // Update last activity timestamp
        $stmt = $conn->prepare("UPDATE tblusers SET last_activity = NOW() WHERE UserID = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
    } catch (PDOException $e) {
        error_log("Failed to log logout event: " . $e->getMessage());
    }
}

// Clear all session data
session_unset();
session_destroy();

// Check API response and redirect accordingly
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    if (!$api_response || $api_response['status'] === 200) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'You have been successfully logged out.'
        ];
        header("Location: login.php?logged_out=1");
    } else {
        error_log("Logout API call failed: " . print_r($api_response, true));
        $_SESSION['alert'] = [
            'type' => 'warning',
            'message' => 'You have been logged out locally, but there was an issue with the remote session.'
        ];
        header("Location: login.php?logout_error=1");
    }
} else {
    header("Location: index.php");
}
exit();
?>
