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
        $mail->Username = 'reynamarie.boyboy@evsu.edu.ph'; // Your email
        $mail->Password = 'vkgv mggt spwa qeka'; // Your app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('reynamarie.boyboy@evsu.edu.ph', 'Car Rental System');
        $mail->addAddress($to);

        // Content
        $mail->isHTML($isHtml);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// Function to generate OTP
function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $otp;
}

// Function to send OTP email
function sendOTPEmail($to, $otp, $purpose) {
    $subject = 'Car Rental System - Email Verification Code';
    $body = "
    <h2>Email Verification Code</h2>
    <p>Your verification code for Car Rental System is:</p>
    <h3 style='text-align: center; padding: 10px; background-color: #f0f0f0; border-radius: 5px;'>$otp</h3>
    <p>This code is valid for 15 minutes. Please do not share it with anyone.</p>
    <p>Thank you for using Car Rental System!</p>
    ";
    
    return sendEmail($to, $subject, $body);
}
