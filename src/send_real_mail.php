<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';
require __DIR__ . '/PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    
    $mail->isSMTP();
    $mail->Host       = 'smtp.srmap.edu.in'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sandesh_patel@srmap.edu.in';
    $mail->Password   = 'Sandesh@123';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    
    $mail->setFrom('sandesh_patel@srmap.edu.in', 'Mailer');
    $mail->addAddress('sandesh_patel@srmap.edu.in');

  
    $code = str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code';
    $mail->Body    = '<p>Your verification code is: <strong>' . htmlspecialchars($code) . '</strong></p>';

    $mail->send();
    echo 'Message has been sent to sandesh_patel@srmap.edu.in with code: ' . $code;
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
} 