<?php
$auth_api = new UserAuthAPI('ak_46436e6ca705fa9e3ab6793a52c4cf0d');
class UserAuthAPI {
    private $apiKey;
    private $baseUrl;
    private $authToken;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->baseUrl = 'https://api.example.com'; // Replace with your actual API URL
        $this->authToken = isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : null;
    }

    private function sendRequest($endpoint, $data, $includeToken = false) {
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Api-Key: ' . $this->apiKey
        ];

        if ($includeToken && $this->authToken) {
            $headers[] = 'Authorization: Bearer ' . $this->authToken;
        }

        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'status' => 500,
                'data' => [
                    'success' => false,
                    'error' => 'connection_failed',
                    'message' => 'Connection failed: ' . $error
                ]
            ];
        }
        
        curl_close($ch);

        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => $httpCode,
                'data' => [
                    'success' => false,
                    'error' => 'invalid_response',
                    'message' => 'Invalid response from server'
                ]
            ];
        }
        
        return [
            'status' => $httpCode,
            'data' => $responseData
        ];
    }

    public function register($email, $password, $username) {
        $data = [
            'email' => $email,
            'password' => $password,
            'name' => $username
        ];
        return $this->sendRequest('/api/register.php', $data);
    }

    private function validateAndStoreToken($token) {
        error_log("\n=== TOKEN VALIDATION START ===");
        error_log("Attempting to validate token: " . $token);
        
        // Temporarily store the token to use it in the validation request
        $this->authToken = $token;
        
        // Validate the token
        $validationResponse = $this->validateToken();
        error_log("Token validation response: " . print_r($validationResponse, true));
        
        if ($validationResponse['status'] === 200) {
            // Token is valid, store it permanently
            $_SESSION['auth_token'] = $token;
            error_log("Token validated and stored in session");
            return true;
        } else {
            // Token is invalid, clear it
            $this->authToken = null;
            unset($_SESSION['auth_token']); // Also clear from session
            error_log("Token validation failed - clearing token");
            return false;
        }
    }

    public function verifyEmail($email, $otp) {
        error_log("\n=== VERIFY EMAIL REQUEST START ===");
        error_log("Verifying email: " . $email);
        error_log("Current session state before verification:");
        error_log("Auth token: " . (isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : 'none'));
        error_log("Email verified: " . (isset($_SESSION['email_verified']) ? 'true' : 'false'));
        
        $data = [
            'email' => $email,
            'otp' => $otp
        ];
        
        // First clear any existing tokens to ensure clean state
        $this->authToken = null;
        unset($_SESSION['auth_token']);
        
        $response = $this->sendRequest('/api/verify-email.php', $data, false); // Changed to false since we cleared the token
        
        error_log("Verify Email Raw Response: " . print_r($response, true));
        
        if ($response['status'] === 200) {
            error_log("Received 200 status from verify-email endpoint");
            
            // Extract token with detailed logging
            $token = null;
            if (isset($response['data']['token'])) {
                $token = $response['data']['token'];
                error_log("Found token directly in data.token: " . $token);
            } elseif (isset($response['data']['data']['token'])) {
                $token = $response['data']['data']['token'];
                error_log("Found token in data.data.token: " . $token);
            } elseif (isset($response['data']['success']) && $response['data']['success']) {
                error_log("Verification successful but no token found in response");
            }
            
            if ($token) {
                error_log("Attempting to validate and store token from email verification");
                if ($this->validateAndStoreToken($token)) {
                    error_log("Successfully validated and stored token from email verification");
                    $_SESSION['email_verified'] = true;
                    
                    // Double check our verification status
                    $verificationStatus = $this->isEmailVerified();
                    error_log("Double-checking verification status: " . ($verificationStatus ? "Verified" : "Not Verified"));
                    
                    // Update user data in session
                    $userStatusResponse = $this->sendRequest('/api/user-status.php', [], true);
                    if ($userStatusResponse['status'] === 200 && isset($userStatusResponse['data']['data'])) {
                        $_SESSION['user_data'] = $userStatusResponse['data']['data'];
                        error_log("Updated user data in session after verification");
                    }
                } else {
                    error_log("Failed to validate token from email verification");
                    $_SESSION['email_verified'] = false;
                }
            } else {
                error_log("No token found in verification response");
            }
        } else {
            error_log("Verification request failed with status: " . $response['status']);
            $_SESSION['email_verified'] = false;
        }
        
        error_log("Final session state after verification:");
        error_log("Auth token: " . (isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : 'none'));
        error_log("Email verified: " . (isset($_SESSION['email_verified']) ? 'true' : 'false'));
        error_log("=== VERIFY EMAIL REQUEST END ===\n");
        
        return $response;
    }

    public function requestOTP($email, $purpose) {
        $data = [
            'email' => $email,
            'purpose' => $purpose
        ];
        
        $response = $this->sendRequest('/api/request_otp.php', $data);
        
        // Add detailed error logging
        if ($response['status'] !== 200) {
            error_log("OTP Request Failed: Status " . $response['status']);
            error_log("Response: " . json_encode($response));
        }
        
        return $response;
    }

    public function changePassword($oldPassword, $newPassword) {
        return $this->sendRequest('/api/change-password.php', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'confirm_password' => $newPassword
        ], true);
    }

    public function resetPassword($otp, $newPassword) {
        // Get the stored auth token from session
        $authToken = isset($_SESSION['reset_auth_token']) ? $_SESSION['reset_auth_token'] : null;
        
        // Set up headers with the auth token
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Api-Key: ' . $this->apiKey,
            'Authorization: ' . $authToken
        ];

        $ch = curl_init($this->baseUrl . '/api/reset-password.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'otp' => $otp,
            'new_password' => $newPassword
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'status' => 500,
                'data' => [
                    'success' => false,
                    'error' => 'connection_failed',
                    'message' => 'Connection failed: ' . $error
                ]
            ];
        }
        
        curl_close($ch);

        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => $httpCode,
                'data' => [
                    'success' => false,
                    'error' => 'invalid_response',
                    'message' => 'Invalid response from server'
                ]
            ];
        }
        
        // Clear the reset token after successful password reset
        if ($httpCode === 200 && isset($responseData['success']) && $responseData['success']) {
            unset($_SESSION['reset_auth_token']);
        }
        
        return [
            'status' => $httpCode,
            'data' => $responseData
        ];
    }

    public function logout() {
        $response = $this->sendRequest('/api/logout.php', [], true);
        if ($response['status'] === 200) {
            $this->authToken = null;
            unset($_SESSION['auth_token']);
        }
        return $response;
    }

    public function refreshToken() {
        return $this->sendRequest('/api/refresh-token.php', [], true);
    }

    public function validateToken() {
        error_log("\n=== VALIDATE TOKEN REQUEST ===");
        error_log("Current auth token: " . ($this->authToken ?? 'no token'));
        error_log("Session auth token: " . (isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : 'no token in session'));
        
        $response = $this->sendRequest('/api/validate-token.php', [], true);
        error_log("Validation response: " . print_r($response, true));
        error_log("=== VALIDATE TOKEN END ===\n");
        return $response;
    }

    public function isEmailVerified() {
        error_log("\n=== CHECKING EMAIL VERIFICATION STATUS ===");
        if (!$this->authToken) {
            error_log("No auth token found - user not logged in");
            return false;
        }

        $response = $this->sendRequest('/api/user-status.php', [], true);
        error_log("User status response: " . print_r($response, true));

        if ($response['status'] === 200 && isset($response['data']['data']['email_verified'])) {
            $isVerified = (bool)$response['data']['data']['email_verified'];
            error_log("Email verification status: " . ($isVerified ? "Verified" : "Not Verified"));
            $_SESSION['email_verified'] = $isVerified;
            return $isVerified;
        }

        error_log("Could not determine email verification status");
        return false;
    }

    public function login($email, $password) {
        error_log("\n=== LOGIN REQUEST START ===");
        error_log("Attempting login for email: " . $email);
        
        $response = $this->sendRequest('/api/login.php', [
            'email' => $email,
            'password' => $password
        ]);
        
        error_log("=== FULL LOGIN RESPONSE ===");
        error_log(print_r($response, true));
        
        if ($response['status'] === 200) {
            error_log("Login status 200 received");
            
            // Clear any existing session data
            unset($_SESSION['auth_token']);
            unset($_SESSION['email_verified']);
            unset($_SESSION['user_data']);
            
            // First check if email is verified
            if (isset($response['data']['error_type'])) {
                error_log("Found error_type in response: " . $response['data']['error_type']);
                if ($response['data']['error_type'] === 'unverified_email') {
                    error_log("User needs email verification");
                    $_SESSION['email_verified'] = false;
                    return $response;
                }
            }
            
            // Check if the response indicates success
            if (isset($response['data']['success']) && $response['data']['success'] === false) {
                error_log("Response indicates failure despite 200 status");
                return $response;
            }
            
            // Try to get token from response
            $token = null;
            if (isset($response['data']['token'])) {
                $token = $response['data']['token'];
                error_log("Found token in data.token");
            } elseif (isset($response['data']['data']['token'])) {
                $token = $response['data']['data']['token'];
                error_log("Found token in data.data.token");
            }
            
            // If we found a token, validate it
            if ($token) {
                error_log("Found token, attempting validation");
                if ($this->validateAndStoreToken($token)) {
                    error_log("Token from login validated and stored successfully");
                    
                    // Store user data if available
                    if (isset($response['data']['data'])) {
                        $_SESSION['user_data'] = $response['data']['data'];
                        error_log("User data stored in session");
                        
                        // Explicitly check email verification status
                        $isVerified = $this->isEmailVerified();
                        error_log("Email verification check after login: " . ($isVerified ? "Verified" : "Not Verified"));
                    }
                } else {
                    error_log("Token validation failed during login");
                }
            } else {
                error_log("No token found in login response");
            }
        } else {
            error_log("Login failed with non-200 status: " . $response['status']);
        }
        
        error_log("=== LOGIN REQUEST END ===\n");
        return $response;
    }
}

// Initialize the API with your API key

?>