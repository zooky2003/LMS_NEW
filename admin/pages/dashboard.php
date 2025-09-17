<?php
// This file is included by admin/index.php, so we have access to the $pdo connection.

// Fetch some simple statistics from the database.
try {
    $class_count = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
    $student_count = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();
    $pending_payments = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'Pending'")->fetchColumn();
    $total_recordings = $pdo->query("SELECT COUNT(*) FROM recordings")->fetchColumn();
} catch (PDOException $e) {
    // In case of an error, display 0.
    $class_count = 0;
    $student_count = 0;
    $pending_payments = 0;
    $total_recordings = 0;
}
?>

<div class="mb-8 p-6 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-lg shadow-lg text-white flex flex-col sm:flex-row justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>!</h1>
        <p class="mt-1 opacity-90">Here's a summary of your LMS at a glance.</p>
    </div>
    <div class="flex gap-4 mt-4 sm:mt-0">
        <div class="text-center bg-white/20 p-4 rounded-lg">
            <div class="flex items-center justify-center gap-2">
                <i class="fas fa-users"></i>
                <p class="text-2xl font-bold"><?= $student_count ?></p>
            </div>
            <p class="text-sm opacity-90">Students</p>
        </div>
        <div class="text-center bg-white/20 p-4 rounded-lg">
            <div class="flex items-center justify-center gap-2">
                <i class="fas fa-chalkboard-teacher"></i>
                <p class="text-2xl font-bold"><?= $class_count ?></p>
            </div>
            <p class="text-sm opacity-90">Classes</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <a href="index.php?page=payments" class="p-6 bg-white rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-all">
        <div class="flex items-start justify-between">
            <div class="p-3 bg-red-100 rounded-full">
                <i class="fas fa-dollar-sign text-xl text-red-600"></i>
            </div>
            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full"><?= $pending_payments ?> Pending</span>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-bold text-gray-800">Payments</h3>
            <p class="text-sm text-gray-500 mt-1">Review and manage student payments.</p>
        </div>
    </a>

    <a href="index.php?page=classes" class="p-6 bg-white rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-all">
        <div class="flex items-start justify-between">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-chalkboard-teacher text-xl text-blue-600"></i>
            </div>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-bold text-gray-800">Classes</h3>
            <p class="text-sm text-gray-500 mt-1">Create and manage your courses.</p>
        </div>
    </a>

    <a href="index.php?page=recordings" class="p-6 bg-white rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-all">
        <div class="flex items-start justify-between">
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-video text-xl text-purple-600"></i>
            </div>
             <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full"><?= $total_recordings ?> Total</span>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-bold text-gray-800">Recordings</h3>
            <p class="text-sm text-gray-500 mt-1">Manage video lessons and materials.</p>
        </div>
    </a>

    <a href="index.php?page=students" class="p-6 bg-white rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-all">
        <div class="flex items-start justify-between">
            <div class="p-3 bg-teal-100 rounded-full">
                <i class="fas fa-users text-xl text-teal-600"></i>
            </div>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-bold text-gray-800">Students</h3>
            <p class="text-sm text-gray-500 mt-1">View and manage student accounts.</p>
        </div>
    </a>
</div>