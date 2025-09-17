<?php
require_once __DIR__ . '/../session_helper.php';
require_once __DIR__ . '/../config.php';

safe_session_start();
require_login();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Handle payment approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $payment_id = $_POST['payment_id'] ?? null;
    $action = $_POST['action'];
    
    if ($payment_id && in_array($action, ['approve', 'reject'])) {
        try {
            if ($action === 'approve') {
                // Update payment status to Paid
                $update_payment = $pdo->prepare("UPDATE payments SET status = 'Paid' WHERE id = :id");
                $update_payment->execute([':id' => $payment_id]);
                
                // Get payment details
                $payment_details = $pdo->prepare("SELECT user_id, class_id FROM payments WHERE id = :id");
                $payment_details->execute([':id' => $payment_id]);
                $payment = $payment_details->fetch();
                
                if ($payment) {
                    // Update enrollment status to Active and set payment date
                    $update_enrollment = $pdo->prepare("
                        INSERT INTO enrollments (user_id, class_id, status, enrollment_date) 
                        VALUES (:user_id, :class_id, 'Active', NOW())
                        ON DUPLICATE KEY UPDATE status = 'Active', enrollment_date = NOW()
                    ");
                    $update_enrollment->execute([
                        ':user_id' => $payment['user_id'],
                        ':class_id' => $payment['class_id']
                    ]);
                    
                    // Update payment with approval date
                    $update_payment_date = $pdo->prepare("UPDATE payments SET payment_date = CURDATE() WHERE id = :id");
                    $update_payment_date->execute([':id' => $payment_id]);
                }
                
                $success_message = "Payment approved successfully!";
            } else {
                // Update payment status to Rejected
                $update_payment = $pdo->prepare("UPDATE payments SET status = 'Rejected' WHERE id = :id");
                $update_payment->execute([':id' => $payment_id]);
                
                $success_message = "Payment rejected successfully!";
            }
        } catch (PDOException $e) {
            $error_message = "Error processing payment: " . $e->getMessage();
        }
    }
}

// Get all payments with user and class details
$payments_sql = "
    SELECT 
        p.*,
        u.name as user_name,
        u.email as user_email,
        c.title as class_title,
        c.price as class_price
    FROM payments p
    JOIN users u ON p.user_id = u.id
    JOIN classes c ON p.class_id = c.id
    ORDER BY p.created_at DESC
";

$payments_stmt = $pdo->prepare($payments_sql);
$payments_stmt->execute();
$payments = $payments_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="index.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Admin
                        </a>
                        <a href="../logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <?php if (isset($success_message)): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="text-green-800"><?php echo htmlspecialchars($success_message); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                        <span class="text-red-800"><?php echo htmlspecialchars($error_message); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payments Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Payment Submissions</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Review and manage student payment submissions</p>
                </div>
                
                <?php if (empty($payments)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-credit-card text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No payment submissions yet</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($payment['user_name']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($payment['user_email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($payment['class_title']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">Rs. <?php echo number_format($payment['amount'], 2); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($payment['reference_number']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full status-<?php echo strtolower($payment['status']); ?>">
                                                <?php echo ucfirst($payment['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M j, Y g:i A', strtotime($payment['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <?php if ($payment['slip_image_url']): ?>
                                                    <a href="../<?php echo htmlspecialchars($payment['slip_image_url']); ?>" target="_blank" 
                                                       class="text-blue-600 hover:text-blue-900">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($payment['status'] === 'Pending'): ?>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to approve this payment?')">
                                                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject this payment?')">
                                                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-gray-400">
                                                        <?php echo $payment['status'] === 'Paid' ? 'Approved' : 'Rejected'; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>