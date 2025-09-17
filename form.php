<?php
require_once __DIR__ . '/config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        $domain = strtolower(substr(strrchr($email, '@') ?: '', 1));
        if ($domain !== 'gmail.com') {
            $errors['email'] = 'Please use a Gmail address (example@gmail.com).';
        }
    }

    if ($subject === '' || strlen($subject) < 3) {
        $errors['subject'] = 'Please enter a topic (at least 3 characters).';
    }

    if ($message === '' || strlen($message) < 10) {
        $errors['message'] = 'Please provide more details (at least 10 characters).';
    }

    if (empty($errors)) {
        $recipient = $config['contact_email'] ?? 'you@example.com';
        $mailSubject = "New message from contact form: {$subject}";
        $mailBody = "You have received a new message from the contact form.\n\n" .
                    "From: {$email}\n" .
                    "Topic: {$subject}\n" .
                    "Time: " . date('Y-m-d H:i:s') . "\n\n" .
                    "Message:\n{$message}\n";

        $sent = false;
        if (!empty($config['smtp']['enabled'])) {
            if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                $autoload = __DIR__ . '/vendor/autoload.php';
                if (file_exists($autoload)) { require_once $autoload; }
            }
            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                try {
                    $smtp = $config['smtp'];
                    $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
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
                    $mailer->Subject = $mailSubject;
                    $mailer->Body = $mailBody;
                    $mailer->send();
                    $sent = true;
                } catch (Exception $e) {
                    $sent = false;
                }
            }
        }

        if (!$sent) {
            $headers = "From: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'example.com') . "\r\n" .
                       "Reply-To: {$email}\r\n" .
                       "X-Mailer: PHP/" . phpversion();
            $sent = @mail($recipient, $mailSubject, $mailBody, $headers);
        }

        $success = (bool)$sent;
        if (!$success) {
            $errors['general'] = 'We could not send your message at this time. Please try again later.';
        } else {
            // Clear fields on success
            $email = '';
            $subject = '';
            $message = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — <?php echo htmlspecialchars($config['site_name'] ?? 'LMS'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #111827;
            --muted: #94a3b8;
            --text: #e5e7eb;
            --primary: #22d3ee;
            --primary-strong: #06b6d4;
            --error: #ef4444;
            --success: #22c55e;
            --ring: rgba(34, 211, 238, 0.35);
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: radial-gradient(1000px 600px at 10% 10%, rgba(34, 211, 238, 0.12), transparent 60%),
                        radial-gradient(800px 500px at 90% 20%, rgba(99, 102, 241, 0.10), transparent 60%),
                        var(--bg);
            color: var(--text);
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }
        .container {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 28px;
        }
        @media (max-width: 900px) {
            .container { grid-template-columns: 1fr; }
        }
        .card {
            background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.0));
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 16px;
            padding: 24px;
            backdrop-filter: blur(8px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        .header-icon {
            width: 36px;
            height: 36px;
        }
        h1 { font-size: 28px; margin: 0; letter-spacing: -0.02em; }
        p.sub { margin: 6px 0 18px; color: var(--muted); }

        form { display: grid; gap: 16px; }
        .field { position: relative; }
        .label { display: block; font-size: 13px; color: var(--muted); margin: 0 0 6px; }

        .input, .textarea {
            width: 100%;
            color: var(--text);
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 12px;
            padding: 14px 14px 14px 42px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.03s ease;
        }
        .textarea { min-height: 140px; resize: vertical; padding-left: 44px; }
        .input:focus, .textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 6px var(--ring);
        }
        .icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 20px; height: 20px; color: var(--muted);
        }
        .textarea + .icon { top: 22px; transform: none; }

        .error { color: var(--error); font-size: 12px; margin-top: 6px; }
        .note { color: var(--muted); font-size: 12px; margin-top: 6px; }

        .actions { display: flex; align-items: center; gap: 12px; justify-content: flex-end; margin-top: 6px; }
        .btn {
            appearance: none;
            border: 0;
            border-radius: 12px;
            padding: 12px 18px;
            background: linear-gradient(180deg, var(--primary), var(--primary-strong));
            color: #001219;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: transform 0.06s ease, filter 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 8px 20px rgba(34, 211, 238, 0.25);
        }
        .btn:hover { filter: brightness(1.05); }
        .btn:active { transform: translateY(1px); }

        .status {
            display: flex; align-items: center; gap: 10px; padding: 12px 14px;
            border-radius: 12px; border: 1px solid rgba(148,163,184,0.18);
            background: rgba(17,24,39,0.6);
            margin-bottom: 12px;
        }
        .status.success { border-color: rgba(34, 197, 94, 0.35); background: rgba(34, 197, 94, 0.12); }
        .status.error { border-color: rgba(239, 68, 68, 0.35); background: rgba(239, 68, 68, 0.12); }

        .side {
            display: grid; gap: 14px;
            background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.0));
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 16px; padding: 20px; backdrop-filter: blur(8px);
        }
        .stat {
            display: flex; align-items: center; gap: 12px; padding: 12px;
            border-radius: 12px; background: rgba(17,24,39,0.6);
            border: 1px solid rgba(148,163,184,0.14);
        }
        .stat svg { width: 22px; height: 22px; }
        .muted { color: var(--muted); font-size: 13px; }
        .brand { font-weight: 700; letter-spacing: 0.02em; }
        a { color: var(--primary); text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <svg class="header-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V6l-8-4-8 4v6c0 6 8 10 8 10z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M9 11l3 2 3-2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <div>
                    <h1>Contact Our Team</h1>
                    <p class="sub">We usually reply within a few hours. Tell us how we can help.</p>
                </div>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="status error">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5"/></svg>
                    <div><?php echo htmlspecialchars($errors['general']); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="status success">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5"/></svg>
                    <div>Thank you! Your message has been sent successfully.</div>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="field">
                    <label class="label" for="email">Gmail address</label>
                    <input class="input" type="email" id="email" name="email" placeholder="you@gmail.com" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M4 6l8 6 8-6" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="4" y="6" width="16" height="12" rx="2" ry="2" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <?php if (!empty($errors['email'])): ?><div class="error"><?php echo htmlspecialchars($errors['email']); ?></div><?php else: ?><div class="note">We only accept Gmail addresses for verification.</div><?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="subject">Topic</label>
                    <input class="input" type="text" id="subject" name="subject" placeholder="What is this about?" value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" required>
                    <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M4 7h16M4 12h10M4 17h8" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <?php if (!empty($errors['subject'])): ?><div class="error"><?php echo htmlspecialchars($errors['subject']); ?></div><?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="message">Message</label>
                    <textarea class="textarea" id="message" name="message" placeholder="Share more details..." required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                    <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M3 21l3-9 15-6-6 15-9 3z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <?php if (!empty($errors['message'])): ?><div class="error"><?php echo htmlspecialchars($errors['message']); ?></div><?php endif; ?>
                </div>

                <div class="actions">
                    <button class="btn" type="submit">
                        <span style="display:inline-flex;align-items:center;gap:10px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M5 12l14-7-7 14-1-6-6-1z" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                            Send Message
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <aside class="side">
            <div class="stat">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.5"/></svg>
                <div>
                    <div class="brand">Fast responses</div>
                    <div class="muted">Our team aims to respond within 2–6 hours.</div>
                </div>
            </div>
            <div class="stat">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3 7h7l-5.5 4 2 7L12 16l-6.5 4 2-7L2 9h7l3-7z" stroke="currentColor" stroke-width="1.5"/></svg>
                <div>
                    <div class="brand">Trusted by learners</div>
                    <div class="muted">We value every message. Your feedback shapes <?php echo htmlspecialchars($config['site_name'] ?? 'our platform'); ?>.</div>
                </div>
            </div>
            <div class="stat">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.5"/></svg>
                <div>
                    <div class="brand">Prefer email?</div>
                    <div class="muted">You can reach us at <a href="mailto:<?php echo htmlspecialchars($config['contact_email'] ?? 'you@example.com'); ?>"><?php echo htmlspecialchars($config['contact_email'] ?? 'you@example.com'); ?></a></div>
                </div>
            </div>
        </aside>
    </div>
</body>
</html>
