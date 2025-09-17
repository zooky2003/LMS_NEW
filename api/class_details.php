<?php
require_once __DIR__ . '/../session_helper.php';
require_once __DIR__ . '/../config.php';

safe_session_start();

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

// Get class ID from request
$class_id = $_GET['id'] ?? null;

if (!$class_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Class ID required']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Get class details with user's enrollment/payment status
    $class_sql = "
        SELECT 
            c.*,
            e.status as enrollment_status,
            e.enrollment_date,
            p.status as payment_status,
            p.created_at as payment_date,
            p.reference_number,
            p.payment_date as approved_date,
            COUNT(DISTINCT cr.recording_id) as recording_count,
            u.name as instructor_name
        FROM classes c
        LEFT JOIN enrollments e ON c.id = e.class_id AND e.user_id = :user_id
        LEFT JOIN payments p ON c.id = p.class_id AND p.user_id = :user_id
        LEFT JOIN class_recordings cr ON c.id = cr.class_id
        LEFT JOIN users u ON c.instructor_id = u.id
        WHERE c.id = :class_id
        GROUP BY c.id, c.title, c.description, c.price, c.status, c.thumbnail_url, c.difficulty, 
                 e.status, e.enrollment_date, p.status, p.created_at, p.reference_number, p.payment_date, u.name
    ";
    
    $stmt = $pdo->prepare($class_sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':class_id' => $class_id
    ]);
    $class = $stmt->fetch();
    
    if (!$class) {
        http_response_code(404);
        echo json_encode(['error' => 'Class not found']);
        exit();
    }
    
    // Check if user has access to this class
    $has_access = ($class['enrollment_status'] === 'Active' || $class['payment_status'] === 'Paid');
    
    if (!$has_access) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied. Payment approval required.']);
        exit();
    }
    
    // Get class recordings if user has access
    $recordings_sql = "
        SELECT 
            r.id,
            r.title,
            r.video_type,
            r.video_url,
            r.duration_minutes,
            r.description,
            r.created_at
        FROM recordings r
        JOIN class_recordings cr ON r.id = cr.recording_id
        WHERE cr.class_id = :class_id
        ORDER BY r.created_at ASC
    ";
    
    $recordings_stmt = $pdo->prepare($recordings_sql);
    $recordings_stmt->execute([':class_id' => $class_id]);
    $recordings = $recordings_stmt->fetchAll();
    
    // Prepare response
    $response = [
        'id' => $class['id'],
        'title' => $class['title'],
        'description' => $class['description'],
        'price' => $class['price'],
        'exam_year' => $class['exam_year'],
        'difficulty' => $class['difficulty'],
        'thumbnail_url' => $class['thumbnail_url'],
        'instructor_name' => $class['instructor_name'],
        'recording_count' => $class['recording_count'],
        'enrollment_status' => $class['enrollment_status'],
        'payment_status' => $class['payment_status'],
        'approved_date' => $class['approved_date'],
        'recordings' => $recordings,
        'has_access' => true
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>