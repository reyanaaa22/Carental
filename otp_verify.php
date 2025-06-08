<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'db.php';          // Your PDO DB connection
require_once 'phpmailer.php';   // Email helper
require_once 'api/auth_api.php';

$api_key = 'ak_5a451330459ee6c400ce7efd37e39076';
$verify_otp_url = 'https://c705-122-54-183-231.ngrok-free.app/api/verify-email.php';
$request_otp_url = 'https://c705-122-54-183-231.ngrok-free.app/api/request-otp.php';

if (!isset($_SESSION['pending_email']) ||
    !isset($_SESSION['pending_otp']) ||
    !isset($_SESSION['otp_timestamp'])
) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['pending_email'];
$stored_otp = $_SESSION['pending_otp'];
$otp_timestamp = $_SESSION['otp_timestamp'];
$error = '';

function callApi($url, $postData) {
    $ch = curl_init($url);

    $jsonData = json_encode($postData);  // Convert to JSON
    $headers = [
        'Content-Type: application/json',
        'X-API-Key: ak_5a451330459ee6c400ce7efd37e39076',
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    // Prevent following redirects
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    // Log request details
    error_log("API Request to: " . $url);
    error_log("Request payload: " . $jsonData);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    // Log response details
    error_log("API Response HTTP Code: " . $httpCode);
    error_log("API Response Content-Type: " . $contentType);
    error_log("API Raw Response: " . $response);

    if ($response === false) {
        $curlError = curl_error($ch);
        error_log("Curl Error: " . $curlError);
        curl_close($ch);
        throw new Exception('Curl error: ' . $curlError);
    }

    curl_close($ch);

    // Check if response is HTML
    if (strpos($response, '<!DOCTYPE html>') !== false || strpos($contentType, 'text/html') !== false) {
        error_log("Received HTML response instead of JSON");
        throw new Exception("API returned HTML instead of JSON. The API endpoint might be incorrect or the server might be down.");
    }

    // Try to decode JSON response
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        error_log("Raw Response that caused JSON error: " . $response);
        throw new Exception("Invalid JSON returned from API. Please check if the API endpoint is correct and accessible.");
    }

    return $decoded;
}

if (isset($_POST['verify_otp']) && isset($_POST['otp'])) {
    $submitted_otp = trim($_POST['otp']);

    // Check expiry (15 mins)
    if (time() - $otp_timestamp > 900) {
        $error = 'OTP has expired. Please request a new one.';
    } else {
        try {
            // Log verification attempt
            error_log("OTP Verification attempt for email: " . $email);
            error_log("Submitted OTP: " . $submitted_otp);

            // Call external API to verify OTP
            $postData = [
                'email' => $email,
                'otp' => $submitted_otp,
                'api_key' => $api_key,
            ];

            error_log("Calling verify OTP API...");
            $api_response = callApi($verify_otp_url, $postData);
            error_log("API Response received: " . print_r($api_response, true));

            if (!empty($api_response['success']) && $api_response['success'] === true) {
                error_log("API verification successful, proceeding with local verification");

                // External API verified OTP successfully
                // Now sync with your local DB
                $stmt = $dbh->prepare("SELECT OTP FROM tblusers WHERE EmailId = ?");
                $stmt->execute([$email]);
                $db_otp = $stmt->fetchColumn();

                if ($db_otp === null) {
                    error_log("No OTP found in DB for email: " . $email);
                    throw new Exception("No OTP found in database for this email");
                }

                error_log("DB OTP: " . $db_otp . ", Submitted OTP: " . $submitted_otp);

                if ($submitted_otp === $db_otp) {
                    // Update verification status
                    $stmt = $dbh->prepare("UPDATE tblusers SET is_verified = 1, OTP = NULL WHERE EmailId = ?");
                    if ($stmt->execute([$email])) {
                        // Clear session variables related to OTP
                        unset($_SESSION['pending_email'], $_SESSION['pending_otp'], $_SESSION['otp_timestamp'], $_SESSION['pending_registration']);

                        $_SESSION['alert'] = [
                            'type' => 'success',
                            'message' => 'Email verified successfully! You can now login.'
                        ];
                        header('Location: login.php');
                        exit;
                    } else {
                        $error = 'Failed to update verification status. Please try again.';
                    }
                } else {
                    $error = 'OTP mismatch with database. Please request a new OTP.';
                }
            } else {
                $error = $api_response['message'] ?? 'OTP verification failed. Please try again.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        } catch (Exception $e) {
            $error = 'API error: ' . $e->getMessage();
        }
    }
}

// Handle OTP resend
if (isset($_POST['resend'])) {
    try {
        $postData = [
            'email' => $email,
            'purpose' => 'email-verification',
        ];

        $api_response = callApi($request_otp_url, $postData);

        if (!empty($api_response['success']) && $api_response['success'] === true) {
            $new_otp = $api_response['data']['otp'] ?? null;

            if ($new_otp) {
                // Update DB
                $stmt = $dbh->prepare("UPDATE tblusers SET OTP = ? WHERE EmailId = ?");
                if ($stmt->execute([$new_otp, $email])) {
                    $_SESSION['pending_otp'] = $new_otp;
                    $_SESSION['otp_timestamp'] = time();

                    if (sendOTPEmail($email, $new_otp, 'registration')) {
                        $_SESSION['alert'] = [
                            'type' => 'success',
                            'message' => 'A new verification code has been sent to your email.'
                        ];
                        header('Location: otp_verify.php');
                        exit;
                    } else {
                        $error = 'Failed to send email. Please try again.';
                    }
                } else {
                    $error = 'Failed to update OTP in database. Please try again.';
                }
            } else {
                $error = 'Invalid OTP received from API.';
            }
        } else {
            $error = $api_response['messa   ge'] ?? 'Failed to generate new OTP. Please try again.';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    } catch (Exception $e) {
        $error = 'API error: ' . $e->getMessage();
    }
}

?>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Verify OTP</title>
<style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
    .container { max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 5px; }
    h2 { text-align: center; }
    input[type="text"] { width: 100%; padding: 10px; margin-top: 10px; }
    button { width: 100%; padding: 10px; margin-top: 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
    button:hover { background-color: #0056b3; }
    .error { color: red; margin-top: 10px; }
    .success { color: green; margin-top: 10px; }
</style>
</head>
<body>
<div class="container">
    <h2>Email Verification</h2>
    <p>Please enter the verification code sent to <strong><?= htmlspecialchars($email) ?></strong></p>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <input type="text" name="otp" placeholder="Enter OTP" required maxlength="6" pattern="\d{6}" title="6 digit code" />
        <button type="submit" name="verify_otp">Verify OTP</button>
    </form>

    <form method="post" action="" style="margin-top: 10px;">
        <button type="submit" name="resend">Resend OTP</button>
    </form>
</div>
</body>
</html>
