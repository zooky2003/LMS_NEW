<?php
// Simple email submission handler for newsletter/contact in footer
// Sends an email to configured recipient with the submitted address
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/config.php';

$recipient = $config['contact_email'] ?? 'you@example.com';
$email = trim($_POST['email'] ?? '');

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Invalid email address.';
    exit;
}

// Build message
$subject = 'New newsletter signup';
$message = "A user subscribed with the email: {$email}\nTime: " . date('Y-m-d H:i:s');

// Prefer SMTP via PHPMailer if configured, fall back to mail()
$sent = false;
if (!empty($config['smtp']['enabled'])) {
    // lightweight inline PHPMailer (only if available), else fallback
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        // Try to include PHPMailer if the project has it (vendor autoload)
        $autoload = __DIR__ . '/vendor/autoload.php';
        if (file_exists($autoload)) { require_once $autoload; }
    }
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        $smtp = $config['smtp'];
        $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mailer->isSMTP();
            $mailer->Host = $smtp['host'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $smtp['username'];
            $mailer->Password = $smtp['password'];
            $mailer->SMTPSecure = $smtp['secure'];
            $mailer->Port = $smtp['port'];
            $mailer->setFrom($smtp['from_email'], $smtp['from_name']);
            $mailer->addAddress($recipient);
            $mailer->addReplyTo($email);
            $mailer->Subject = $subject;
            $mailer->Body = $message;
            $mailer->send();
            $sent = true;
        } catch (Exception $e) {
            $sent = false; // will fallback below
        }
    }
}
if (!$sent) {
    $headers = "From: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'example.com') . "\r\n" .
               "Reply-To: {$email}\r\n" .
               "X-Mailer: PHP/" . phpversion();
    @mail($recipient, $subject, $message, $headers);
}

// Redirect back with a message
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
// Append a simple query param for a toast/snackbar if needed
$sep = strpos($redirect, '?') === false ? '?' : '&';
header('Location: ' . $redirect . $sep . 'subscribed=1');
exit;
?>


