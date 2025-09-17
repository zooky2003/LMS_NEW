<?php
// This file is included by admin/index.php, so we have access to the $pdo connection.

$success_message = '';
$error_message = '';

// --- Handle Student Deletion ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_student') {
    $student_id_to_delete = $_POST['student_id'] ?? 0;
    if ($student_id_to_delete) {
        try {
            // NOTE: 'ON DELETE CASCADE' in the database schema will auto-delete associated payments.
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND is_admin = 0");
            $stmt->execute([':id' => $student_id_to_delete]);
            $success_message = "Student account and all associated data deleted successfully.";
        } catch (PDOException $e) {
            $error_message = "Database error: Could not delete the student.";
        }
    }
}

// --- Fetch all students ---
try {
    $stmt = $pdo->query("SELECT id, name, email, created_at FROM users WHERE is_admin = 0 ORDER BY created_at DESC");
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    $students = [];
    $error_message = "Error fetching students from the database: " . htmlspecialchars($e->getMessage());
}
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Student Management</h1>
    <p class="text-gray-600 mt-1">View all registered student accounts in the system.</p>
</div>

<?php if ($success_message): ?><div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>
<?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>


<div class="bg-white rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Registered On</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No student accounts have been created yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($student['name']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($student['email']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars(date('F j, Y', strtotime($student['created_at']))) ?></td>
                            <td class="px-6 py-4 flex items-center gap-4 text-xs font-medium">
                                <a href="edit_student.php?id=<?= $student['id'] ?>" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="index.php?page=students" onsubmit="return confirm('Are you sure you want to delete this student? All their payment records will also be permanently deleted.');" class="inline">
                                    <input type="hidden" name="action" value="delete_student">
                                    <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>