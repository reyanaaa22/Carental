<?php
session_start();
require_once 'db.php';

// API configuration
$api_key = 'ak_5a451330459ee6c400ce7efd37e39076';
$api_base_url = 'https://c705-122-54-183-231.ngrok-free.app';

function authenticateApiKey($api_key) {
    // Define your API key
    $valid_api_key = 'ak_5a451330459ee6c400ce7efd37e39076';
    
    // Check if API key is provided
    if (empty($api_key)) {
        return [
            'success' => false,
            'message' => 'API key is required',
            'status_code' => 401
        ];
    }

    // Validate API key
    if ($api_key !== $valid_api_key) {
        return [
            'success' => false,
            'message' => 'Invalid API key',
            'status_code' => 403
        ];
    }

    return [
        'success' => true,
        'message' => 'API key is valid',
        'status_code' => 200
    ];
}

// Function to validate API request
function validateApiRequest() {
    // Get headers
    $headers = getallheaders();
    
    // Check for API key in headers
    $api_key = isset($headers['X-API-Key']) ? $headers['X-API-Key'] : null;
    
    // If no API key in headers, check GET/POST parameters
    if (!$api_key) {
        $api_key = isset($_REQUEST['api_key']) ? $_REQUEST['api_key'] : null;
    }
    
    // Authenticate the API key
    $auth_result = authenticateApiKey($api_key);
    
    if (!$auth_result['success']) {
        http_response_code($auth_result['status_code']);
        echo json_encode([
            'success' => false,
            'message' => $auth_result['message']
        ]);
        exit;
    }
    
    return true;
}

// Function to send JSON response
function sendJsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Example usage in an API endpoint:
/*
require_once 'authenticate.php';

// Validate API request
validateApiRequest();

// Your API logic here
$response_data = [
    'success' => true,
    'data' => [
        // Your response data
    ]
];

sendJsonResponse($response_data);
*/
?> 