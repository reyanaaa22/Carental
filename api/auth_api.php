<?php
/**
 * UserAuthAPI
 * A simple wrapper for interacting with an external authentication API
 * such as OTP request and login verification.
 */
$auth_api = new UserAuthAPI('ak_5a451330459ee6c400ce7efd37e39076');
class UserAuthAPI {
    private $apiKey;
    private $baseUrl;
    private $authToken;

    /**
     * Constructor to initialize API key and base URL
     *
     * @param string $api_key      Your API authentication key
     * @param string $base_url     The base URL of your external API
     */
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->baseUrl = 'https://c705-122-54-183-231.ngrok-free.app';
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->authToken = isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : null;
    }

    /**
     * Request an OTP for email verification
     *
     * @param string $email    The user's email
     * @param string $type     The type of OTP request (e.g., 'email_verification')
     * @return array           Response array with status and data
     * 
     */ 

    public function requestOTP($email, $purpose) {
        $url = $this->baseUrl . '/api/request-otp.php';
    
        $headers = [
            'Content-Type: application/json',
            'X-API-Key: ' . $this->apiKey
        ];
    
        $data = [
            'email' => $email,
            'purpose' => $purpose === 'email_verification' ? 'email-verification' : $purpose
        ];
    
        $ch = curl_init($url);
    
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 10
        ]);
    
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            return [
                'status' => $http_code,
                'data' => [
                    'success' => false,
                    'message' => 'cURL Error: ' . curl_error($ch)
                ]
            ];
        }
    
        curl_close($ch);
    
        $decoded = json_decode($response, true);
    
        return [
            'status' => $http_code,
            'data' => $decoded
        ];
    }
    

        /**
     * Register a new user with the external API.
     *
     * @param string $email     The user's email address.
     * @param string $password  The user's password.
     * @return array            Response from the API.
     */
    public function register($email, $password) {
        $url = $this->baseUrl . '/api/register.php';

        $headers = [
            'Content-Type: application/json',
            'X-API-Key: ' . $this->apiKey
        ];

        $payload = [
            'email' => $email,
            'password' => $password
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            return [
                'status' => $http_code,
                'data' => [
                    'success' => false,
                    'message' => 'cURL Error: ' . curl_error($ch)
                ]
            ];
        }

        curl_close($ch);

        $decoded = json_decode($response, true);

        return [
            'status' => $http_code,
            'data' => $decoded
        ];
    }

    /**
     * Verify an OTP code
     *
     * @param string $email   The user's email
     * @param string $otp     The OTP code to verify
     * @return array          Response from API
     */
    public function verifyOTP($email, $otp) {
        $url = $this->baseUrl . '/api/verify-email.php';  // Corrected endpoint
    
        $headers = [
            'Content-Type: application/json',
            'X-API-Key: ' . $this->apiKey
        ];
    
        $payload = [
            'email' => $email,
            'otp' => $otp
        ];
    
        $ch = curl_init($url);
    
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 10
        ]);
    
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            return [
                'status' => $http_code,
                'data' => [
                    'success' => false,
                    'message' => 'cURL Error: ' . curl_error($ch)
                ]
            ];
        }
    
        curl_close($ch);
    
        $decoded = json_decode($response, true);
    
        return [
            'status' => $http_code,
            'data' => $decoded
        ];
    }
    

    /**
     * Perform a login request to the external API
     *
     * @param string $email     User email
     * @param string $password  User password
     * @return array            Response from API
     */
    public function login($email, $password) {
        $url = $this->baseUrl . '/api/login.php';  // Ensure consistency with your base URL variable
    
        $headers = [
            'Content-Type: application/json',
            'X-API-Key: ' . $this->apiKey
        ];
    
        $payload = [
            'email' => $email,
            'password' => $password
        ];
    
        $ch = curl_init($url);
    
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 10
        ]);
    
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            return [
                'status' => $http_code,
                'data' => [
                    'success' => false,
                    'message' => 'cURL Error: ' . curl_error($ch)
                ]
            ];
        }
    
        curl_close($ch);
    
        $decoded = json_decode($response, true);
    
        return [
            'status' => $http_code,
            'data' => $decoded
        ];
    }
    

    /**
     * Send a POST request with the given payload and return the result.
     *
     * @param string $url      The API endpoint
     * @param array $payload   The request data
     * @return array           Response with status and decoded data
     */
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
    public function logout() {
        if (!$this->authToken) {
            return [
                'status' => 401,
                'data' => [
                    'success' => false,
                    'message' => 'No active session'
                ]
            ];
        }
    
        $response = $this->sendRequest('/api/logout.php', [], true);
        if ($response['status'] === 200) {
            $this->authToken = null;
            unset($_SESSION['auth_token']);
        }
        return $response;
    }    
}
