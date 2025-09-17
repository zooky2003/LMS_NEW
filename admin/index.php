<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config.php';

$page = $_GET['page'] ?? 'dashboard';
$allowed_pages = ['dashboard', 'payments', 'classes', 'recordings', 'students'];
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}
$page_file_path = __DIR__ . '/pages/' . $page . '.php';
$page_title = ucfirst($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - LMS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        
        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <?php include __DIR__ . '/components/header.php'; ?>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <?php
                    if (file_exists($page_file_path)) {
                        include $page_file_path;
                    } else {
                        echo "<h1 class='text-2xl font-semibold text-gray-700'>Error 404</h1>";
                        echo "<p class='text-gray-600'>Page content for '<strong>" . htmlspecialchars($page) . "</strong>' not found.</p>";
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>