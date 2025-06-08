<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendEmail($to, $subject, $body, $isHtml = true) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'rubytinunga@gmail.com'; // Your email
        $mail->Password = 'ytfcrgmrkkpgvznq'; // Your app-specific password (consider env vars)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';  // Set charset

        // Recipients
        $mail->setFrom('ruby.tinunga@evsu.edu.ph', 'Car Rental System');
        $mail->addAddress($to);
        // Optional: $mail->addReplyTo('no-reply@carrentalsystem.com', 'No Reply');

        // Content
        $mail->isHTML($isHtml);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Plain text fallback
        if ($isHtml) {
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// Function to generate OTP (secure)
function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $otp;
}

// Function to send OTP email
function sendOTPEmail($to, $otp, $purpose) {
    if ($purpose === 'password-reset') {
        $subject = 'Car Rental System - Password Reset Code';
        $body = "
        <h2>Password Reset Code</h2>
        <p>You have requested to reset your password. Your verification code is:</p>
        <h3 style='text-align: center; padding: 10px; background-color: #f0f0f0; border-radius: 5px;'>$otp</h3>
        <p>This code is valid for 15 minutes. Please do not share it with anyone.</p>
        <p>If you did not request a password reset, please ignore this email.</p>
        <p>Thank you for using Car Rental System!</p>
        ";
    } else {
        $subject = 'Car Rental System - Email Verification Code';
        $body = "
        <h2>Email Verification Code</h2>
        <p>Your verification code for Car Rental System is:</p>
        <h3 style='text-align: center; padding: 10px; background-color: #f0f0f0; border-radius: 5px;'>$otp</h3>
        <p>This code is valid for 15 minutes. Please do not share it with anyone.</p>
        <p>Thank you for using Car Rental System!</p>
                 ";
    }
    
    return sendEmail($to, $subject, $body);
}
