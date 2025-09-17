<?php
// This file is included by admin/index.php, so we have access to the $pdo connection.

// --- Handle Payment Status Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $payment_id = $_POST['payment_id'] ?? 0;
    $new_status = $_POST['new_status'] ?? '';
    $allowed_statuses_to_update = ['Paid', 'Rejected'];

    if ($payment_id && in_array($new_status, $allowed_statuses_to_update)) {
        try {
            $sql_update = "UPDATE payments SET status = :status WHERE id = :id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([':status' => $new_status, ':id' => $payment_id]);
        } catch (PDOException $e) {
            die("Database error: Could not update payment status.");
        }
    }
}

// 1. Get All Filters from URL
$status_filter = $_GET['status'] ?? 'Pending';
$search_term = trim($_GET['search'] ?? '');
$date_filter = $_GET['payment_date'] ?? '';
$allowed_statuses = ['Pending', 'Paid', 'Rejected', 'All'];
if (!in_array($status_filter, $allowed_statuses)) {
    $status_filter = 'Pending';
}

// 2. Dynamically Build the SQL Query
$sql = "SELECT p.id, p.reference_number, p.amount, p.status, p.payment_date,
               u.name as student_name, u.email as student_email, c.title as class_title
        FROM payments p
        JOIN users u ON p.user_id = u.id
        JOIN classes c ON p.class_id = c.id";
        
$params = [];
$where_clauses = [];

if ($status_filter !== 'All') {
    $where_clauses[] = "p.status = :status";
    $params[':status'] = $status_filter;
}
if (!empty($search_term)) {
    $where_clauses[] = "(u.name LIKE :search OR u.email LIKE :search OR c.title LIKE :search)";
    $params[':search'] = '%' . $search_term . '%';
}
if (!empty($date_filter)) {
    $where_clauses[] = "p.payment_date = :payment_date";
    $params[':payment_date'] = $date_filter;
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();

// 3. Get summary counts
$pending_count = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'Pending'")->fetchColumn();
$paid_count = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'Paid'")->fetchColumn();
$rejected_count = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'Rejected'")->fetchColumn();
$total_payments = $paid_count + $pending_count + $rejected_count;
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Payment Management</h1>
        <p class="text-gray-600 mt-1">Review and manage student payments</p>
    </div>
    <div class="flex items-center gap-2 mt-4 sm:mt-0">
        <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Export by Month</button>
        <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Export by Date</button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="p-4 bg-white rounded-lg shadow-md">
        <span class="text-sm font-semibold text-gray-500">Paid Classes</span>
        <p class="text-3xl font-bold text-green-600 mt-1"><?= $paid_count ?></p>
    </div>
    <div class="p-4 bg-white rounded-lg shadow-md">
        <span class="text-sm font-semibold text-gray-500">Pending</span>
        <p class="text-3xl font-bold text-yellow-600 mt-1"><?= $pending_count ?></p>
    </div>
    <div class="p-4 bg-white rounded-lg shadow-md">
        <span class="text-sm font-semibold text-gray-500">Rejected</span>
        <p class="text-3xl font-bold text-red-600 mt-1"><?= $rejected_count ?></p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md">
    <div class="p-4 border-b border-gray-200">
        <nav class="flex space-x-2">
            <a href="index.php?page=payments&status=Pending" class="px-3 py-1 text-sm font-medium rounded-full <?= $status_filter === 'Pending' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-100' ?>">Pending Review <span class="ml-2 px-2 py-0.5 text-xs bg-gray-200 rounded-full"><?= $pending_count ?></span></a>
            <a href="index.php?page=payments&status=Paid" class="px-3 py-1 text-sm font-medium rounded-full <?= $status_filter === 'Paid' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-100' ?>">Paid</a>
            <a href="index.php?page=payments&status=All" class="px-3 py-1 text-sm font-medium rounded-full <?= $status_filter === 'All' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-100' ?>">All Payments</a>
        </nav>
    </div>

    <div class="p-4">
        <form method="GET" action="index.php" class="flex flex-col sm:flex-row gap-4">
            <input type="hidden" name="page" value="payments">
            <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
            
            <input type="search" name="search" placeholder="Search by student, email, class..." value="<?= htmlspecialchars($search_term) ?>" class="w-full sm:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="date" name="payment_date" value="<?= htmlspecialchars($date_filter) ?>" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            
            <div class="flex gap-2">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700">Filter</button>
                <a href="index.php?page=payments" class="w-full sm:w-auto px-4 py-2 text-center text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Clear</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                <tr>
                    <th class="px-6 py-3">Student</th>
                    <th class="px-6 py-3">Class</th>
                    <th class="px-6 py-3">Reference / Date</th>
                    <th class="px-6 py-3">Amount</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($payments)): ?>
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No payments found for this filter.</td></tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-4"><div class="font-medium text-gray-900"><?= htmlspecialchars($payment['student_name']) ?></div><div class="text-gray-500"><?= htmlspecialchars($payment['student_email']) ?></div></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($payment['class_title']) ?></td>
                            <td class="px-6 py-4"><div><?= htmlspecialchars($payment['reference_number']) ?></div><div class="text-gray-500"><?= htmlspecialchars(date('Y-m-d', strtotime($payment['payment_date']))) ?></div></td>
                            <td class="px-6 py-4 font-medium text-gray-900">Rs. <?= number_format($payment['amount'], 2) ?></td>
                            <td class="px-6 py-4">
                                <?php $status_classes = ['Pending' => 'bg-yellow-100 text-yellow-800', 'Paid' => 'bg-green-100 text-green-800', 'Rejected' => 'bg-red-100 text-red-800']; ?>
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs <?= $status_classes[$payment['status']] ?? 'bg-gray-100 text-gray-800' ?>"><?= htmlspecialchars($payment['status']) ?></span>
                            </td>
                            <td class="px-6 py-4 flex items-center gap-4 text-xs font-medium">
                                <a href="#" class="text-indigo-600 hover:underline">View Slip</a>
                                <form method="POST" action="index.php?page=payments" class="inline"><input type="hidden" name="action" value="update_status"><input type="hidden" name="payment_id" value="<?= $payment['id'] ?>"><input type="hidden" name="new_status" value="Paid"><button type="submit" class="text-green-600 hover:underline">Approve</button></form>
                                <form method="POST" action="index.php?page=payments" class="inline"><input type="hidden" name="action" value="update_status"><input type="hidden" name="payment_id" value="<?= $payment['id'] ?>"><input type="hidden" name="new_status" value="Rejected"><button type="submit" class="text-red-600 hover:underline">Reject</button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>