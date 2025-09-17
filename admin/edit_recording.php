<?php
// 1. Includes and login check
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config.php';

$error_message = '';
$success_message = '';

// 2. Get and Validate the Recording ID from the URL
$recording_id = $_GET['id'] ?? null;
if (!$recording_id || !is_numeric($recording_id)) {
    header('Location: index.php?page=recordings&error=invalid_id');
    exit();
}

// 3. Handle Form Submission to UPDATE the recording
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_recording') {
    $recording_id_post = $_POST['recording_id'] ?? 0;
    $title = trim($_POST['title'] ?? '');
    $class_ids = $_POST['class_ids'] ?? [];
    $video_type = $_POST['video_type'] ?? 'YouTube';
    $duration_minutes = $_POST['duration_minutes'] ?? 0;
    $video_url = trim($_POST['video_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($video_url) || !is_numeric($duration_minutes) || empty($class_ids)) {
        $error_message = 'Please fill in all required fields and select at least one class.';
    } elseif (!filter_var($video_url, FILTER_VALIDATE_URL)) {
        $error_message = 'Please enter a valid video URL.';
    } else {
        $pdo->beginTransaction();
        try {
            // Update recording details
            $sql_recording = "UPDATE recordings SET title = :title, video_type = :video_type, duration_minutes = :duration_minutes, video_url = :video_url, description = :description WHERE id = :id";
            $stmt_recording = $pdo->prepare($sql_recording);
            $stmt_recording->execute([':title' => $title, ':video_type' => $video_type, ':duration_minutes' => $duration_minutes, ':video_url' => $video_url, ':description' => $description, ':id' => $recording_id_post]);

            // Handle thumbnail upload
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/thumbnails/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    // Check file size (2MB max)
                    if ($_FILES['thumbnail']['size'] <= 2 * 1024 * 1024) {
                        // Generate unique filename
                        $filename = 'recording_' . $recording_id_post . '_' . time() . '.' . $file_extension;
                        $file_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $file_path)) {
                            // Update or insert thumbnail record
                            $thumbnail_sql = "INSERT INTO recording_thumbnails (recording_id, thumbnail_url) VALUES (:recording_id, :thumbnail_url) ON DUPLICATE KEY UPDATE thumbnail_url = :thumbnail_url, uploaded_at = NOW()";
                            $thumbnail_stmt = $pdo->prepare($thumbnail_sql);
                            $thumbnail_stmt->execute([
                                ':recording_id' => $recording_id_post,
                                ':thumbnail_url' => $file_path
                            ]);
                        }
                    }
                }
            }

            // Update class associations
            $stmt_delete_links = $pdo->prepare("DELETE FROM class_recordings WHERE recording_id = :id");
            $stmt_delete_links->execute([':id' => $recording_id_post]);

            $sql_junction = "INSERT INTO class_recordings (class_id, recording_id) VALUES (:class_id, :recording_id)";
            $stmt_junction = $pdo->prepare($sql_junction);
            foreach ($class_ids as $class_id) {
                $stmt_junction->execute([':class_id' => $class_id, ':recording_id' => $recording_id_post]);
            }
            
            $pdo->commit();
            header('Location: index.php?page=recordings&status=updated');
            exit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Database error: Could not update the recording. " . $e->getMessage();
        }
    }
}

// 4. Fetch Data for the Form Display
try {
    $stmt = $pdo->prepare("SELECT * FROM recordings WHERE id = :id");
    $stmt->execute([':id' => $recording_id]);
    $recording = $stmt->fetch();
    if (!$recording) {
        header('Location: index.php?page=recordings&error=not_found');
        exit();
    }
    $all_classes_stmt = $pdo->query("SELECT id, title FROM classes ORDER BY title ASC");
    $all_classes = $all_classes_stmt->fetchAll();
    $linked_classes_stmt = $pdo->prepare("SELECT class_id FROM class_recordings WHERE recording_id = :id");
    $linked_classes_stmt->execute([':id' => $recording_id]);
    $linked_class_ids = $linked_classes_stmt->fetchAll(PDO::FETCH_COLUMN, 0); 
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Set variables for the header and sidebar
$page_title = "Edit Recording";
$page = 'recordings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recording - LMS Admin</title>
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
                            <h2 class="text-xl font-semibold text-gray-800">Editing: <?= htmlspecialchars($recording['title']) ?></h2>
                        </div>
                        <div class="p-6">
                            <?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>

                            <form method="POST" action="edit_recording.php?id=<?= $recording['id'] ?>" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_recording">
                                <input type="hidden" name="recording_id" value="<?= $recording['id'] ?>">
                                
                                <div class="space-y-4">
                                    <div class="md:col-span-2"><label for="rec-title" class="block text-sm font-medium text-gray-700">Recording Title *</label><input type="text" id="rec-title" name="title" value="<?= htmlspecialchars($recording['title']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                                    <div><label class="block text-sm font-medium text-gray-700">Select Classes *</label><div class="mt-1 p-2 border border-gray-300 rounded-md max-h-32 overflow-y-auto space-y-1"><?php foreach($all_classes as $class): ?><?php $is_checked = in_array($class['id'], $linked_class_ids); ?><label class="flex items-center space-x-2 font-normal"><input type="checkbox" name="class_ids[]" value="<?= $class['id'] ?>" <?= $is_checked ? 'checked' : '' ?> class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span><?= htmlspecialchars($class['title']) ?></span></label><?php endforeach; ?></div></div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div><label for="video-type" class="block text-sm font-medium text-gray-700">Video Type *</label><select id="video-type" name="video_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required><option value="YouTube" <?= ($recording['video_type'] == 'YouTube') ? 'selected' : '' ?>>YouTube</option><option value="Vimeo" <?= ($recording['video_type'] == 'Vimeo') ? 'selected' : '' ?>>Vimeo</option></select></div>
                                        <div><label for="duration" class="block text-sm font-medium text-gray-700">Duration (Minutes) *</label><input type="number" id="duration" name="duration_minutes" value="<?= htmlspecialchars($recording['duration_minutes']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                                    </div>
                                    <div><label for="video-url" class="block text-sm font-medium text-gray-700">Video URL *</label><input type="url" id="video-url" name="video_url" value="<?= htmlspecialchars($recording['video_url']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                                    <div><label for="thumbnail" class="block text-sm font-medium text-gray-700">Custom Thumbnail (Optional)</label><input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/jpg,image/png,image/webp" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"><p class="mt-1 text-xs text-gray-500">Upload a custom thumbnail image (JPG, PNG, WEBP, max 2MB). If not provided, YouTube thumbnail will be used.</p></div>
                                    <div><label for="rec-description" class="block text-sm font-medium text-gray-700">Description</label><textarea id="rec-description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($recording['description']) ?></textarea></div>
                                </div>

                                <div class="bg-gray-50 px-6 py-3 mt-6 flex justify-end gap-3 -mx-6 -mb-6 rounded-b-lg">
                                    <a href="index.php?page=recordings" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
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