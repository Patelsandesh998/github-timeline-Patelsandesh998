<?php
require_once 'functions.php';
session_start();


$file = __DIR__ . '/registered_emails.txt';
if (!file_exists($file)) {
    touch($file);
}

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        $_SESSION['verification_code'] = $code;
        $_SESSION['pending_email'] = $email;
        sendVerificationEmail($email, $code);
        $msg = 'Verification code sent to your email.';
        $msg_type = 'success';
    } else {
        $msg = 'Invalid email address.';
        $msg_type = 'error';
    }
}


if (isset($_POST['verification_code'])) {
    $input_code = trim($_POST['verification_code']);
    if (isset($_SESSION['verification_code'], $_SESSION['pending_email'])) {
        if ($input_code === $_SESSION['verification_code']) {
            if (registerEmail($_SESSION['pending_email'])) {
                $msg = 'Email verified and registered!';
                $msg_type = 'success';
            } else {
                $msg = 'Failed to register email.';
                $msg_type = 'error';
            }
            unset($_SESSION['verification_code'], $_SESSION['pending_email']);
        } else {
            $msg = 'Invalid verification code.';
            $msg_type = 'error';
        }
    } else {
        $msg = 'No verification code requested.';
        $msg_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <?php if (!empty($msg)) echo '<div class="msg' . ($msg_type === 'error' ? ' error' : '') . '">' . htmlspecialchars($msg) . '</div>'; ?>
    <h2>Register for GitHub Timeline Updates</h2>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required>
        <button id="submit-email">Submit</button>
    </form>
    <form method="post">
        <label>Verification Code:</label>
        <input type="text" name="verification_code" maxlength="6" required>
        <button id="submit-verification">Verify</button>
    </form>
    <p>Want to unsubscribe? <a href="unsubscribe.php">Click here</a></p>
</div>
</body>
</html>
