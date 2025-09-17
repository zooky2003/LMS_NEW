<?php
// This file is included by admin/index.php, so we have access to the $pdo connection.

// Initialize feedback messages for the user.
$error_message = '';
$success_message = '';

// --- Handle Deletion of a Class ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_class') {
    $class_id_to_delete = $_POST['class_id'] ?? 0;
    if ($class_id_to_delete) {
        try {
            $stmt = $pdo->prepare("SELECT thumbnail_url, payment_info_url FROM classes WHERE id = :id");
            $stmt->execute([':id' => $class_id_to_delete]);
            $class = $stmt->fetch();

            if ($class) {
                $delete_stmt = $pdo->prepare("DELETE FROM classes WHERE id = :id");
                $delete_stmt->execute([':id' => $class_id_to_delete]);

                if ($delete_stmt->rowCount() > 0) {
                    if (!empty($class['thumbnail_url'])) {
                        $file_path = __DIR__ . '/../../' . substr($class['thumbnail_url'], 3);
                        if (file_exists($file_path)) unlink($file_path);
                    }
                    if (!empty($class['payment_info_url'])) {
                        $file_path = __DIR__ . '/../../' . substr($class['payment_info_url'], 3);
                        if (file_exists($file_path)) unlink($file_path);
                    }
                    $success_message = "Class was deleted successfully.";
                }
            } else { $error_message = "Could not find the class to delete."; }
        } catch (PDOException $e) { $error_message = "Database error during deletion: " . $e->getMessage(); }
    }
}

// --- Handle Form Submission for Creating a New Class ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_class') {
    $title = trim($_POST['title'] ?? '');
    $exam_year = $_POST['exam_year'] ?? '';
    $price = $_POST['price'] ?? 0;
    $status = $_POST['status'] ?? 'Inactive';
    $allow_payments = $_POST['allow_payments'] ?? 'Yes - Allow payments';
    $class_type = $_POST['class_type'] ?? 'Paid Class';
    $description = trim($_POST['description'] ?? '');
    $thumbnail_file = $_FILES['thumbnail'] ?? null;
    $payment_info_file = $_FILES['payment_info_image'] ?? null;
    $category = $_POST['category'] ?? 'uncategorized';
    $difficulty = $_POST['difficulty'] ?? 'all-levels';

    if (empty($title) || empty($exam_year) || !is_numeric($price)) {
        $error_message = 'Please fill in all required fields correctly.';
    } elseif (!$thumbnail_file || $thumbnail_file['error'] !== UPLOAD_ERR_OK) {
        $error_message = 'A valid thumbnail image is required.';
    } else {
        $upload_dir = __DIR__ . '/../../uploads/thumbnails/';
        $file_extension = strtolower(pathinfo($thumbnail_file['name'], PATHINFO_EXTENSION));
        $unique_filename = 'class-' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $unique_filename;
        $db_path_thumbnail = '../uploads/thumbnails/' . $unique_filename;
        $db_path_payment_info = null;

        if ($payment_info_file && $payment_info_file['error'] === UPLOAD_ERR_OK) {
            $payment_info_ext = strtolower(pathinfo($payment_info_file['name'], PATHINFO_EXTENSION));
            $payment_info_filename = 'payment-info-' . uniqid() . '.' . $payment_info_ext;
            $payment_info_upload_path = $upload_dir . $payment_info_filename;
            if (move_uploaded_file($payment_info_file['tmp_name'], $payment_info_upload_path)) {
                $db_path_payment_info = '../uploads/thumbnails/' . $payment_info_filename;
            }
        }

        if (move_uploaded_file($thumbnail_file['tmp_name'], $upload_path)) {
            try {
                $sql = "INSERT INTO classes (title, exam_year, price, status, allow_payments, class_type, description, thumbnail_url, payment_info_url, category, difficulty) 
                        VALUES (:title, :exam_year, :price, :status, :allow_payments, :class_type, :description, :thumbnail_url, :payment_info_url, :category, :difficulty)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':title' => $title, ':exam_year' => $exam_year, ':price' => $price, ':status' => $status,
                    ':allow_payments' => $allow_payments, ':class_type' => $class_type, ':description' => $description,
                    ':thumbnail_url' => $db_path_thumbnail, ':payment_info_url' => $db_path_payment_info,
                    ':category' => $category, ':difficulty' => $difficulty
                ]);
                $success_message = "Class '" . htmlspecialchars($title) . "' was created successfully!";
            } catch (PDOException $e) { $error_message = "Database error: Could not create the class. " . $e->getMessage(); }
        } else { $error_message = 'There was an error uploading the thumbnail image.'; }
    }
}

// --- Fetch all existing classes from the database ---
try {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY created_at DESC");
    $classes = $stmt->fetchAll();
} catch (PDOException $e) {
    $classes = [];
    $error_message = "Error fetching classes: " . htmlspecialchars($e->getMessage());
}
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Classes Management</h1>
        <p class="text-gray-600 mt-1">Create and manage class listings</p>
    </div>
    <button id="create-class-btn" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 flex items-center gap-2">
        <i class="fas fa-plus"></i> Create Class
    </button>
</div>

<?php if ($success_message): ?><div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>
<?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php if (empty($classes)): ?>
        <p class="text-gray-500 col-span-full text-center mt-8">No classes found. Click "Create Class" to add your first one.</p>
    <?php else: ?>
        <?php foreach ($classes as $class): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <img src="<?= htmlspecialchars($class['thumbnail_url'] ?? '../assets/images/placeholder.jpg') ?>" alt="Class Thumbnail" class="h-40 w-full object-cover">
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 truncate" title="<?= htmlspecialchars($class['title']) ?>"><?= htmlspecialchars($class['title']) ?></h3>
                    <div class="flex justify-between items-center mt-2 text-sm">
                        <span class="font-semibold text-gray-700">Rs. <?= number_format($class['price'], 2) ?></span>
                        <?php $status_classes = ['Active' => 'bg-green-100 text-green-800', 'Inactive' => 'bg-gray-100 text-gray-800']; ?>
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs <?= $status_classes[$class['status']] ?? '' ?>"><?= htmlspecialchars($class['status']) ?></span>
                    </div>
                </div>
                <div class="px-4 pb-4 border-t border-gray-200 mt-2 pt-4 flex justify-end gap-2">
                    <a href="edit_class.php?id=<?= $class['id'] ?>" class="px-3 py-1 text-sm text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200">Edit</a>
                    <form method="POST" action="index.php?page=classes" onsubmit="return confirm('Are you sure you want to delete this class?');" class="inline">
                        <input type="hidden" name="action" value="delete_class">
                        <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                        <button type="submit" class="px-3 py-1 text-sm text-red-600 bg-red-100 rounded-full hover:bg-red-200">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="create-class-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Create New Class</h2>
            <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800">&times;</button>
        </div>
        <form method="POST" action="index.php?page=classes" enctype="multipart/form-data" class="flex-grow overflow-y-auto">
            <input type="hidden" name="action" value="create_class">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2"><label for="title" class="block text-sm font-medium text-gray-700">Class Title *</label><input type="text" id="title" name="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                    <div><label for="exam_year" class="block text-sm font-medium text-gray-700">Exam Year *</label><select id="exam_year" name="exam_year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required><?php for ($y = date('Y') + 2; $y >= date('Y') - 2; $y--): ?><option value="<?= $y ?>" <?= ($y == date('Y')) ? 'selected' : '' ?>><?= $y ?></option><?php endfor; ?></select></div>
                    <div><label for="price" class="block text-sm font-medium text-gray-700">Price (Rs.) *</label><input type="number" id="price" name="price" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></div>
                    <div><label for="status" class="block text-sm font-medium text-gray-700">Status *</label><select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required><option value="Active">Active</option><option value="Inactive">Inactive</option></select></div>
                    <div><label for="class_type" class="block text-sm font-medium text-gray-700">Class Type *</label><select id="class_type" name="class_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required><option value="Paid Class">Paid Class</option><option value="Free Class">Free Class</option></select></div>
                    <div class="md:col-span-2"><label for="description" class="block text-sm font-medium text-gray-700">Description</label><textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea></div>
                    <div class="md:col-span-2"><label for="thumbnail" class="block text-sm font-medium text-gray-700">Class Thumbnail *</label><input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required></div>
                    <div class="md:col-span-2"><label for="payment_info_image" class="block text-sm font-medium text-gray-700">Payment Information Image</label><input type="file" id="payment_info_image" name="payment_info_image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"></div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3">
                <button type="button" id="cancel-btn" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700">Create Class</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('create-class-modal');
    const createBtn = document.getElementById('create-class-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    if (modal && createBtn && closeModalBtn && cancelBtn) {
        const showModal = () => modal.classList.remove('hidden');
        const hideModal = () => modal.classList.add('hidden');
        createBtn.addEventListener('click', showModal);
        closeModalBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideModal();
            }
        });
    }

    // Enhance Create Class modal UI with dropzones and counters (CSS-driven look)
    (function enhanceCreateClassUI(){
        const root = document.getElementById('create-class-modal');
        if (!root) return;

        // Scope for css: add class to main panel
        const panel = root.querySelector('.bg-white.rounded-lg');
        if (panel) panel.classList.add('admin-modal');

        // Dropzone helper
        function makeDropzone(input, opts){
            if (!input || input.dataset.enhanced === '1') return;
            input.dataset.enhanced = '1';

            const zone = document.createElement('div');
            zone.className = 'file-dropzone';
            const info = document.createElement('div');
            info.className = 'file-dropzone-info';
            info.innerHTML = '<i class="fa-solid fa-upload" aria-hidden="true"></i>\n                              <p><strong>Drop image here</strong> or click to select</p>\n                              <small>Max size: 5 MB</small>';

            // Insert wrapper before input and move input inside
            input.parentNode.insertBefore(zone, input);
            zone.appendChild(input);
            zone.appendChild(info);

            // Native input overlay
            input.classList.add('file-input-native');

            // UI updates on file picked
            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                const p = info.querySelector('p');
                if (file) p.textContent = file.name; else p.innerHTML = '<strong>Drop image here</strong> or click to select';
            });

            // Drag & drop feedback
            ['dragenter','dragover'].forEach(evt => zone.addEventListener(evt, (e)=>{ e.preventDefault(); e.stopPropagation(); zone.classList.add('is-dragover'); }));
            ['dragleave','drop'].forEach(evt => zone.addEventListener(evt, (e)=>{ e.preventDefault(); e.stopPropagation(); zone.classList.remove('is-dragover'); }));
            zone.addEventListener('drop', (e)=>{ const dt=e.dataTransfer; if(dt&&dt.files&&dt.files.length){ input.files=dt.files; input.dispatchEvent(new Event('change',{bubbles:true})); }});
            zone.addEventListener('click', ()=> input.click());

            if (opts && opts.helperText) {
                const helper = document.createElement('div');
                helper.className = 'file-helper-text';
                helper.textContent = opts.helperText;
                zone.parentNode.appendChild(helper);
            }
        }

        makeDropzone(document.getElementById('thumbnail'), {
            helperText: 'Upload class thumbnail image (recommended: 16:9 ratio, 720p)'
        });
        makeDropzone(document.getElementById('payment_info_image'), {
            helperText: 'Upload payment info image (bank details, QR code, etc.)'
        });

        // Description counter
        const desc = document.getElementById('description');
        if (desc) {
            const counter = document.createElement('div');
            counter.className = 'char-counter';
            desc.parentNode.appendChild(counter);
            const max = 1000;
            const update = () => { const len=(desc.value||'').length; counter.textContent = `${len}/${max}`; counter.classList.toggle('over', len>max); };
            desc.addEventListener('input', update); update();
        }
    })();
});
</script>
