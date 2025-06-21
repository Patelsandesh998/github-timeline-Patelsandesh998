<?php

$to = 'sandesh_patel@srmap.edu.in';
$code = str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
$subject = 'Your Verification Code';
$message = '<p>Your verification code is: <strong>' . htmlspecialchars($code) . '</strong></p>';
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: no-reply@example.com\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo 'Test verification email sent successfully to ' . $to . ' with code: ' . $code;
} else {
    echo 'Failed to send test verification email.';
    error_log('mail() failed');
}
