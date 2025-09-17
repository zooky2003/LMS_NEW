<?php
// 1. Includes and login check
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config.php';

$error_message = '';
$success_message = '';

// 2. Get and Validate the Class ID from the URL
$class_id = $_GET['id'] ?? null;
if (!$class_id || !is_numeric($class_id)) {
    header('Location: index.php?page=classes&error=invalid_id');
    exit();
}

// 3. Fetch the Existing Class Data
try {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = :id");
    $stmt->execute([':id' => $class_id]);
    $class = $stmt->fetch();
    if (!$class) {
        header('Location: index.php?page=classes&error=not_found');
        exit();
    }
} catch (PDOException $e) {
    die("Database error: Could not fetch class data. " . $e->getMessage());
}

// 4. Handle Form Submission to UPDATE the class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_class') {
    $class_id_post = $_POST['class_id'] ?? 0;
    $title = trim($_POST['title'] ?? '');
    $exam_year = $_POST['exam_year'] ?? '';
    $price = $_POST['price'] ?? 0;
    $status = $_POST['status'] ?? 'Inactive';
    $description = trim($_POST['description'] ?? '');
    $new_thumbnail_file = $_FILES['thumbnail'] ?? null;
    // Add other fields from your form here
    $allow_payments = $_POST['allow_payments'] ?? $class['allow_payments'];
    $class_type = $_POST['class_type'] ?? $class['class_type'];
    $category = $_POST['category'] ?? $class['category'];
    $difficulty = $_POST['difficulty'] ?? $class['difficulty'];


    if (empty($title) || empty($exam_year) || !is_numeric($price)) {
        $error_message = 'Please fill in all required fields correctly.';
    } else {
        $thumbnail_db_path = $class['thumbnail_url']; 
        if ($new_thumbnail_file && $new_thumbnail_file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/thumbnails/';
            $file_extension = strtolower(pathinfo($new_thumbnail_file['name'], PATHINFO_EXTENSION));
            $unique_filename = 'class-' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $unique_filename;

            if (move_uploaded_file($new_thumbnail_file['tmp_name'], $upload_path)) {
                if ($thumbnail_db_path) {
                    $old_file_path = __DIR__ . '/../../' . substr($thumbnail_db_path, 3);
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
                $thumbnail_db_path = '../uploads/thumbnails/' . $unique_filename;
            } else { $error_message = 'Failed to upload the new thumbnail.'; }
        }

        if (empty($error_message)) {
            try {
                $sql = "UPDATE classes SET 
                            title = :title, exam_year = :exam_year, price = :price, status = :status, 
                            allow_payments = :allow_payments, class_type = :class_type, description = :description,
                            thumbnail_url = :thumbnail_url, category = :category, difficulty = :difficulty
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':title' => $title, ':exam_year' => $exam_year, ':price' => $price, ':status' => $status,
                    ':allow_payments' => $allow_payments, ':class_type' => $class_type, ':description' => $description,
                    ':thumbnail_url' => $thumbnail_db_path, ':category' => $category, ':difficulty' => $difficulty,
                    ':id' => $class_id_post
                ]);
                header('Location: index.php?page=classes&status=updated');
                exit();
            } catch (PDOException $e) {
                $error_message = "Database error: Could not update the class. " . $e->getMessage();
            }
        }
    }
}

// Set variables for the header and sidebar
$page_title = "Edit Class";
$page = 'classes';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class - LMS Admin</title>
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
                    
                    <div class="bg-white rounded-lg shadow-md max-w-3xl mx-auto">
                        <div class="p-6 border-b">
                            <h2 class="text-xl font-semibold text-gray-800">Editing: <?= htmlspecialchars($class['title']) ?></h2>
                        </div>
                        <div class="p-6">
                            <?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>
                            
                            <form method="POST" action="edit_class.php?id=<?= $class['id'] ?>" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_class">
                                <input type="hidden" name="class_id" value="<?= $class['id'] ?>">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2"><label for="title" class="block text-sm font-medium text-gray-700">Class Title *</label><input type="text" id="title" name="title" value="<?= htmlspecialchars($class['title']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                                    <div><label for="exam_year" class="block text-sm font-medium text-gray-700">Exam Year *</label><select id="exam_year" name="exam_year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required><?php for ($y = date('Y') + 2; $y >= date('Y') - 2; $y--): ?><option value="<?= $y ?>" <?= ($class['exam_year'] == $y) ? 'selected' : '' ?>><?= $y ?></option><?php endfor; ?></select></div>
                                    <div><label for="price" class="block text-sm font-medium text-gray-700">Price (Rs.) *</label><input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($class['price']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                                    <div><label for="status" class="block text-sm font-medium text-gray-700">Status *</label><select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required><option value="Active" <?= ($class['status'] == 'Active') ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= ($class['status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option></select></div>
                                    <div><label for="class_type" class="block text-sm font-medium text-gray-700">Class Type *</label><select id="class_type" name="class_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required><option value="Paid Class" <?= ($class['class_type'] == 'Paid Class') ? 'selected' : '' ?>>Paid Class</option><option value="Free Class" <?= ($class['class_type'] == 'Free Class') ? 'selected' : '' ?>>Free Class</option></select></div>
                                    <div class="md:col-span-2"><label for="description" class="block text-sm font-medium text-gray-700">Description</label><textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($class['description']) ?></textarea></div>
                                    <div class="md:col-span-2"><label for="thumbnail" class="block text-sm font-medium text-gray-700">Change Thumbnail (optional)</label><input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"><small class="text-xs text-gray-500 mt-1">Current: <?= htmlspecialchars(basename($class['thumbnail_url'])) ?></small></div>
                                </div>

                                <div class="bg-gray-50 px-6 py-3 mt-6 flex justify-end gap-3 -mx-6 -mb-6 rounded-b-lg">
                                    <a href="index.php?page=classes" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
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