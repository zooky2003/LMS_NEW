<?php
// Go up one directory to access the main project files
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_helper.php';

safe_session_start();

// If the admin is already logged in, redirect them to the dashboard
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: index.php');
    exit();
}

$error_message = '';

// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_message = 'Please enter both email and password.';
    } else {
        // Prepare a query to find a user who is an admin
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND is_admin = 1");
        $stmt->execute(['email' => $email]);
        $admin_user = $stmt->fetch();

        // --- VERY IMPORTANT SECURITY NOTE ---
        // The demo SQL used a plain-text password ('admin123'). This is NOT secure.
        // In a real application, you must use password_hash() to store passwords
        // and password_verify() to check them.
        // The check would look like this:
        // if ($admin_user && password_verify($password, $admin_user['password'])) { ... }
        
        if ($admin_user && password_verify($password, $admin_user['password'])) {
            // Password is correct, set the session variables
            session_regenerate_id(true); // Prevents session fixation
            $_SESSION['user_id'] = $admin_user['id'];
            $_SESSION['user_name'] = $admin_user['name'];
            $_SESSION['is_admin'] = true;

            // Redirect to the main admin dashboard page
            header('Location: index.php');
            exit();
        } else {
            $error_message = 'Invalid credentials or not an admin account.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EduLearn</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #7c3aed; --primary-light: #f3e8ff; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); display: grid; place-items: center; min-height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .login-header { margin-bottom: 24px; }
        .login-header h1 { font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 8px; }
        .login-header p { color: #6b7280; margin: 0; }
        .form-group { text-align: left; margin-bottom: 16px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: #374151; }
        .form-input { width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; box-sizing: border-box; transition: border-color .2s, box-shadow .2s; }
        .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        .login-btn { width: 100%; background: var(--primary); color: white; border: none; padding: 12px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background-color 0.3s; }
        .login-btn:hover { background: #6d28d9; }
        .error-message { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h1><i class="fas fa-user-shield"></i> Admin Panel Login</h1>
            <p>Please enter your credentials to proceed.</p>
        </div>

        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="login-btn">Log In</button>
        </form>
    </div>
</body>
</html>