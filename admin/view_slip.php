<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_helper.php';
safe_session_start();

// Simple admin check. In a real app, this would be a more robust function.
// This relies on the admin login process setting $_SESSION['is_admin'] = 1.
if (!isset($_SESSION['user_id']) || ($_SESSION['is_admin'] ?? 0) != 1) {
    http_response_code(403);
    die('Access Denied. Admin access required.');
}

$payment_id = $_GET['id'] ?? 0;
if (!$payment_id || !is_numeric($payment_id)) {
    http_response_code(400);
    die("Invalid payment ID.");
}

try {
    $stmt = $pdo->prepare("SELECT slip_image_url FROM payments WHERE id = :id");
    $stmt->execute([':id' => $payment_id]);
    $slip_relative_url = $stmt->fetchColumn();

    if ($slip_relative_url) {
        $file_path = __DIR__ . '/../' . $slip_relative_url; // Path is relative to lms root
        if (file_exists($file_path)) {
            $mime_type = mime_content_type($file_path);
            header("Content-type: $mime_type");
            readfile($file_path);
            exit();
        }
    }
    http_response_code(404);
    die("Payment slip not found or file is missing from the server.");
} catch (PDOException $e) {
    http_response_code(500);
    die("Database error.");
}