<?php
require_once 'functions.php';
session_start();


if (isset($_POST['unsubscribe_email'])) {
    $email = trim($_POST['unsubscribe_email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        $_SESSION['unsubscribe_code'] = $code;
        $_SESSION['unsubscribe_email'] = $email;
        sendUnsubscribeVerificationEmail($email, $code);
        $msg = 'Unsubscribe verification code sent to your email.';
        $msg_type = 'success';
    } else {
        $msg = 'Invalid email address.';
        $msg_type = 'error';
    }
}


if (isset($_POST['unsubscribe_verification_code'])) {
    $input_code = trim($_POST['unsubscribe_verification_code']);
    if (isset($_SESSION['unsubscribe_code'], $_SESSION['unsubscribe_email'])) {
        if ($input_code === $_SESSION['unsubscribe_code']) {
            if (unsubscribeEmail($_SESSION['unsubscribe_email'])) {
                $msg = 'You have been unsubscribed.';
                $msg_type = 'success';
            } else {
                $msg = 'Failed to unsubscribe email.';
                $msg_type = 'error';
            }
            unset($_SESSION['unsubscribe_code'], $_SESSION['unsubscribe_email']);
        } else {
            $msg = 'Invalid verification code.';
            $msg_type = 'error';
        }
    } else {
        $msg = 'No unsubscribe code requested.';
        $msg_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <?php if (!empty($msg)) echo '<div class="msg' . ($msg_type === 'error' ? ' error' : '') . '">' . htmlspecialchars($msg) . '</div>'; ?>
    <h2>Unsubscribe from GitHub Timeline Updates</h2>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="unsubscribe_email" required>
        <button id="submit-unsubscribe">Unsubscribe</button>
    </form>
    <form method="post">
        <label>Unsubscribe Code:</label>
        <input type="text" name="unsubscribe_verification_code">
        <button id="verify-unsubscribe">Verify</button>
    </form>
    <p>Want to register again? <a href="index.php">Click here</a></p>
</div>
</body>
</html>
