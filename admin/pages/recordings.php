<?php
// This file is included by admin/index.php, so we have access to the $pdo connection.

$error_message = '';
$success_message = '';

// --- Handle Deletion of a Recording ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_recording') {
    $recording_id_to_delete = $_POST['recording_id'] ?? 0;
    if ($recording_id_to_delete) {
        $pdo->beginTransaction();
        try {
            $stmt1 = $pdo->prepare("DELETE FROM class_recordings WHERE recording_id = :id");
            $stmt1->execute([':id' => $recording_id_to_delete]);
            $stmt2 = $pdo->prepare("DELETE FROM recordings WHERE id = :id");
            $stmt2->execute([':id' => $recording_id_to_delete]);
            $pdo->commit();
            $success_message = "Recording deleted successfully.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Database error: Could not delete the recording.";
        }
    }
}

// --- Handle Form Submission for Adding a New Recording ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_recording') {
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
            $sql_recording = "INSERT INTO recordings (title, video_type, duration_minutes, video_url, description) VALUES (:title, :video_type, :duration_minutes, :video_url, :description)";
            $stmt_recording = $pdo->prepare($sql_recording);
            $stmt_recording->execute([':title' => $title, ':video_type' => $video_type, ':duration_minutes' => $duration_minutes, ':video_url' => $video_url, ':description' => $description]);
            $new_recording_id = $pdo->lastInsertId();
            
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
                        $filename = 'recording_' . $new_recording_id . '_' . time() . '.' . $file_extension;
                        $file_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $file_path)) {
                            // Insert thumbnail record
                            $thumbnail_sql = "INSERT INTO recording_thumbnails (recording_id, thumbnail_url) VALUES (:recording_id, :thumbnail_url)";
                            $thumbnail_stmt = $pdo->prepare($thumbnail_sql);
                            $thumbnail_stmt->execute([
                                ':recording_id' => $new_recording_id,
                                ':thumbnail_url' => $file_path
                            ]);
                        }
                    }
                }
            }
            
            $sql_junction = "INSERT INTO class_recordings (class_id, recording_id) VALUES (:class_id, :recording_id)";
            $stmt_junction = $pdo->prepare($sql_junction);
            foreach ($class_ids as $class_id) {
                $stmt_junction->execute([':class_id' => (int)$class_id, ':recording_id' => $new_recording_id]);
            }
            $pdo->commit();
            $success_message = "Recording '" . htmlspecialchars($title) . "' was added successfully!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Database error: Could not add the recording. " . $e->getMessage();
        }
    }
}

// --- Fetch existing data for the page ---
try {
    $recordings_stmt = $pdo->query("SELECT * FROM recordings ORDER BY created_at DESC");
    $recordings = $recordings_stmt->fetchAll();
    $classes_stmt = $pdo->query("SELECT id, title FROM classes ORDER BY title ASC");
    $all_classes = $classes_stmt->fetchAll();

    // Efficiently fetch all class links for the table display
    $class_links_stmt = $pdo->query("SELECT cr.recording_id, c.title FROM class_recordings cr JOIN classes c ON cr.class_id = c.id");
    $class_links_by_rec_id = [];
    foreach ($class_links_stmt->fetchAll() as $link) {
        $class_links_by_rec_id[$link['recording_id']][] = $link['title'];
    }

} catch (PDOException $e) {
    $recordings = []; $all_classes = [];
    $error_message = "Error fetching data: " . htmlspecialchars($e->getMessage());
}
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Recordings</h1>
        <p class="text-gray-600 mt-1">Manage video recordings and course materials</p>
    </div>
    <div class="flex items-center gap-2 mt-4 sm:mt-0">
        <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Manage Lessons</button>
        <button id="add-recording-btn" class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 flex items-center gap-2"><i class="fas fa-plus"></i> Add Recording</button>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-6">
    <div class="p-4 bg-white rounded-lg shadow-md"><span class="text-sm font-semibold text-gray-500">Recordings</span><p class="text-3xl font-bold text-gray-800 mt-1"><?= count($recordings) ?></p></div>
    <div class="p-4 bg-white rounded-lg shadow-md"><span class="text-sm font-semibold text-gray-500">Total Lessons</span><p class="text-3xl font-bold text-gray-800 mt-1">7</p></div>
    <div class="p-4 bg-white rounded-lg shadow-md"><span class="text-sm font-semibold text-gray-500">Total Recordings</span><p class="text-3xl font-bold text-gray-800 mt-1">125</p></div>
    <div class="p-4 bg-white rounded-lg shadow-md"><span class="text-sm font-semibold text-gray-500">Avg per Lesson</span><p class="text-3xl font-bold text-gray-800 mt-1">1</p></div>
    <div class="p-4 bg-white rounded-lg shadow-md"><span class="text-sm font-semibold text-gray-500">Unassigned</span><p class="text-3xl font-bold text-gray-800 mt-1">2</p></div>
</div>

<div class="bg-white rounded-lg shadow-md">
    <div class="p-4 border-b border-gray-200">
        <form method="GET" action="index.php" class="flex flex-col sm:flex-row gap-4">
            <input type="hidden" name="page" value="recordings">
            <input type="search" name="search" placeholder="Search by title, description, class..." class="w-full sm:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="class_filter" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"><option value="">All Classes</option><?php foreach($all_classes as $class): ?><option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['title']) ?></option><?php endforeach; ?></select>
            <select name="type_filter" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"><option value="">All Types</option><option value="YouTube">YouTube</option><option value="Vimeo">Vimeo</option></select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                <tr>
                    <th class="px-6 py-3">Recording</th>
                    <th class="px-6 py-3">Classes</th>
                    <th class="px-6 py-3">Source</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($recordings)): ?>
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No recordings found.</td></tr>
                <?php else: ?>
                    <?php foreach ($recordings as $recording): ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($recording['title']) ?></div>
                                <div class="text-gray-500">
                                    <span class="font-semibold"><?= htmlspecialchars($recording['video_type']) ?></span> &bull; <?= htmlspecialchars($recording['duration_minutes']) ?> mins
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php $linked_classes = $class_links_by_rec_id[$recording['id']] ?? []; ?>
                                <?php if (!empty($linked_classes)): ?>
                                    <div class="font-medium text-gray-800"><?= htmlspecialchars($linked_classes[0]) ?></div>
                                    <?php if (count($linked_classes) > 1): ?>
                                        <div class="text-xs text-gray-500">+ <?= (count($linked_classes) - 1) ?> more</div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-xs text-gray-500">Not assigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4"><a href="<?= htmlspecialchars($recording['video_url']) ?>" target="_blank" class="text-indigo-600 hover:underline">View Link</a></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs bg-green-100 text-green-800">Active</span></td>
                            <td class="px-6 py-4 flex items-center gap-4 text-xs font-medium">
                                <a href="edit_recording.php?id=<?= $recording['id'] ?>" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="index.php?page=recordings" onsubmit="return confirm('Are you sure?');" class="inline"><input type="hidden" name="action" value="delete_recording"><input type="hidden" name="recording_id" value="<?= $recording['id'] ?>"><button type="submit" class="text-red-600 hover:underline">Delete</button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="add-recording-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center p-4 border-b"><h2 class="text-xl font-semibold text-gray-800">Add New Recording</h2><button id="close-modal-btn-recording" class="text-gray-500 hover:text-gray-800">&times;</button></div>
        <form method="POST" action="index.php?page=recordings" enctype="multipart/form-data" class="flex-grow overflow-y-auto">
            <input type="hidden" name="action" value="add_recording">
            <div class="p-6">
                <div class="space-y-4">
                    <div><label for="rec-title" class="block text-sm font-medium text-gray-700">Recording Title *</label><input type="text" id="rec-title" name="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                    <div><label class="block text-sm font-medium text-gray-700">Select Classes *</label><div class="mt-1 p-2 border border-gray-300 rounded-md max-h-32 overflow-y-auto space-y-1"><?php if (empty($all_classes)): ?><p class="text-sm text-gray-500">No classes available.</p><?php else: ?><?php foreach($all_classes as $class): ?><label class="flex items-center space-x-2 font-normal"><input type="checkbox" name="class_ids[]" value="<?= $class['id'] ?>" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span><?= htmlspecialchars($class['title']) ?></span></label><?php endforeach; ?><?php endif; ?></div></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label for="video-type" class="block text-sm font-medium text-gray-700">Video Type *</label><select id="video-type" name="video_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required><option value="YouTube">YouTube</option><option value="Vimeo">Vimeo</option></select></div>
                        <div><label for="duration" class="block text-sm font-medium text-gray-700">Duration (Minutes) *</label><input type="number" id="duration" name="duration_minutes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                    </div>
                    <div><label for="video-url" class="block text-sm font-medium text-gray-700">Video URL *</label><input type="url" id="video-url" name="video_url" placeholder="https://www.youtube.com/watch?v=..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                    <div><label for="thumbnail" class="block text-sm font-medium text-gray-700">Custom Thumbnail (Optional)</label><input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/jpg,image/png,image/webp" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"><p class="mt-1 text-xs text-gray-500">Upload a custom thumbnail image (JPG, PNG, WEBP, max 2MB). If not provided, YouTube thumbnail will be used.</p></div>
                    <div><label for="rec-description" class="block text-sm font-medium text-gray-700">Description</label><textarea id="rec-description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea></div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3"><button type="button" id="cancel-btn-recording" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button><button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700">Add Recording</button></div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('add-recording-modal');
    const addBtn = document.getElementById('add-recording-btn');
    const closeModalBtn = document.getElementById('close-modal-btn-recording');
    const cancelBtn = document.getElementById('cancel-btn-recording');
    if (modal && addBtn && closeModalBtn && cancelBtn) {
        const showModal = () => modal.classList.remove('hidden');
        const hideModal = () => modal.classList.add('hidden');
        addBtn.addEventListener('click', showModal);
        closeModalBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) { hideModal(); }
        });
    }
});
</script>