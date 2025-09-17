<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config.php';

$error_message = '';
$success_message = '';
$admin_id = $_SESSION['user_id'];

// --- Handle Form Submission to UPDATE the profile ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email)) {
        $error_message = 'Name and Email cannot be empty.';
    } else {
        // Fetch current user data to verify current password
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $admin_id]);
        $admin_user = $stmt->fetch();

        // Prepare the main update query
        $sql = "UPDATE users SET name = :name, email = :email ";
        $params = [':name' => $name, ':email' => $email, ':id' => $admin_id];

        // --- Handle Password Change (if fields are filled) ---
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $error_message = 'Please enter your current password to set a new one.';
            } elseif (!password_verify($current_password, $admin_user['password'])) {
                $error_message = 'The current password you entered is incorrect.';
            } elseif ($new_password !== $confirm_password) {
                $error_message = 'The new password and confirmation do not match.';
            } else {
                // All checks passed, add password to the update query
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql .= ", password = :password ";
                $params[':password'] = $new_hashed_password;
            }
        }
        
        // --- Execute the final query if no errors ---
        if (empty($error_message)) {
            try {
                $sql .= "WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                // Update session name immediately
                $_SESSION['user_name'] = $name;
                $success_message = 'Profile updated successfully!';
            } catch (PDOException $e) {
                $error_message = 'Database error: Could not update profile. ' . $e->getMessage();
            }
        }
    }
}

// Fetch the latest admin data to display in the form
try {
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
    $stmt->execute([':id' => $admin_id]);
    $admin = $stmt->fetch();
} catch (PDOException $e) {
    die("Error fetching admin data: " . $e->getMessage());
}

$page_title = "Admin Profile";
$page = 'profile'; // Defines the active page for the sidebar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - LMS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/components/sidebar.php'; ?>
        <div class="main-content-area">
            <?php include __DIR__ . '/components/header.php'; ?>

            <main class="content-wrapper">
                <div class="card" style="max-width: 700px; margin: auto;">
                    <div class="card-header">
                        <h2>Admin Profile</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?><div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>
                        <?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>

                        <form method="POST" action="profile.php">
                            <div class="form-group">
                                <label for="name">Name *</label>
                                <input type="text" id="name" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                            </div>

                            <hr style="margin: 24px 0;">
                            <p style="color: var(--text-muted);">Fill the fields below only if you want to change your password.</p>

                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password">
                            </div>
                            <div class="form-grid-modal">
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password">
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password">
                                </div>
                            </div>
                            
                            <div class="modal-footer" style="background:none; border:none; padding: 20px 0 0 0;">
                                <button type="submit" class="btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>