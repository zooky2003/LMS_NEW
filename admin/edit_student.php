<?php
// 1. Include necessary files and perform security checks
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config.php';

$error_message = '';
$success_message = '';

// 2. Get and Validate the Student ID from the URL
$student_id = $_GET['id'] ?? null;
if (!$student_id || !is_numeric($student_id)) {
    header('Location: index.php?page=students&error=invalid_id');
    exit();
}

// 3. Handle Form Submission to UPDATE the student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_student') {
    $student_id_post = $_POST['student_id'] ?? 0;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $error_message = 'Name and Email fields cannot be empty.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id AND is_admin = 0";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $name, ':email' => $email, ':id' => $student_id_post]);
            header('Location: index.php?page=students&status=updated');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                $error_message = 'This email address is already in use by another account.';
            } else {
                $error_message = "Database error: Could not update the student profile.";
            }
        }
    }
}

// 4. Fetch the Existing Student Data to display in the form
try {
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = :id AND is_admin = 0");
    $stmt->execute([':id' => $student_id]);
    $student = $stmt->fetch();
    if (!$student) {
        header('Location: index.php?page=students&error=not_found');
        exit();
    }
} catch (PDOException $e) {
    die("Database error: Could not fetch student data. " . $e->getMessage());
}

// Set variables for the header and sidebar
$page_title = "Edit Student";
$page = 'students'; // This tells the sidebar to highlight 'Students'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - LMS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        
        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <?php include __DIR__ . '/components/header.php'; ?>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    
                    <div class="bg-white rounded-lg shadow-md max-w-2xl mx-auto">
                        <div class="p-6 border-b">
                            <h2 class="text-xl font-semibold text-gray-800">Editing Student: <?= htmlspecialchars($student['name']) ?></h2>
                        </div>
                        <div class="p-6">
                            <?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>

                            <form method="POST" action="edit_student.php?id=<?= $student['id'] ?>">
                                <input type="hidden" name="action" value="update_student">
                                <input type="hidden" name="student_id" value="<?= $student['id'] ?>">

                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 px-6 py-3 mt-6 flex justify-end gap-3 -mx-6 -mb-6 rounded-b-lg">
                                    <a href="index.php?page=students" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</body>
</html>