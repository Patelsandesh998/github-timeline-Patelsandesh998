<?php

function generateVerificationCode(): string {
    return str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
}

function sendVerificationEmail(string $email, string $code): bool {
    $subject = 'Your Verification Code';
    $message = '<p>Your verification code is: <strong>' . htmlspecialchars($code) . '</strong></p>';
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    $result = mail($email, $subject, $message, $headers);
    if (!$result) {
        error_log('mail() failed for sendVerificationEmail to ' . $email);
    }
    return $result;
}


function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        touch($file);
    }
    $email = strtolower(trim($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    if (in_array($email, $emails)) return true;
    $emails[] = $email;
    return file_put_contents($file, implode("\n", $emails) . "\n") !== false;
}


function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        touch($file);
    }
    $email = strtolower(trim($email));
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $emails = array_filter($emails, function($e) use ($email) {
        return $e !== $email;
    });
    return file_put_contents($file, implode("\n", $emails) . "\n") !== false;
}


function fetchGitHubTimeline() {
    $url = 'https://www.github.com/timeline';
    $context = stream_context_create([
        'http' => [
            'user_agent' => 'PHP'
        ]
    ]);
    $json = @file_get_contents($url, false, $context);
    if ($json === false) return [];
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}


function formatGitHubData(array $data): string {
    $html = '<h2>GitHub Timeline Updates</h2>';
    $html .= '<table border="1">';
    $html .= '<tr><th>Event</th><th>User</th></tr>';
    foreach ($data as $event) {
        $type = isset($event['type']) ? htmlspecialchars($event['type']) : 'N/A';
        $user = isset($event['actor']['login']) ? htmlspecialchars($event['actor']['login']) : 'N/A';
        $html .= "<tr><td>$type</td><td>$user</td></tr>";
    }
    $html .= '</table>';
    return $html;
}


function sendGitHubUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        touch($file);
    }
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    if (empty($emails)) return;
    $data = fetchGitHubTimeline();
    $body = formatGitHubData($data);
    foreach ($emails as $email) {
        $unsubscribe_url = getUnsubscribeUrl($email);
        $full_body = $body . '<p><a href="' . htmlspecialchars($unsubscribe_url) . '" id="unsubscribe-button">Unsubscribe</a></p>';
        $subject = 'Latest GitHub Updates';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: no-reply@example.com\r\n";
        $result = mail($email, $subject, $full_body, $headers);
        if (!$result) {
            error_log('mail() failed for sendGitHubUpdatesToSubscribers to ' . $email);
        }
    }
}

function getUnsubscribeUrl($email) {
    $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . dirname($_SERVER['PHP_SELF']);
    $base = rtrim($base, '/');
    return $base . '/unsubscribe.php?email=' . urlencode($email);
}

function sendUnsubscribeVerificationEmail(string $email, string $code): bool {
    $subject = 'Confirm Unsubscription';
    $message = '<p>To confirm unsubscription, use this code: <strong>' . htmlspecialchars($code) . '</strong></p>';
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    $result = mail($email, $subject, $message, $headers);
    if (!$result) {
        error_log('mail() failed for sendUnsubscribeVerificationEmail to ' . $email);
    }
    return $result;
}
