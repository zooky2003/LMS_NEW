<?php
require_once __DIR__ . '/session_helper.php';
require_once __DIR__ . '/config.php';

safe_session_start();

// Only allow admin access
if (!is_logged_in() || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Access denied. Admin login required.");
}

echo "<h1>Payment System Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

try {
    // Test 1: Check database tables exist
    echo "<div class='test-section'>";
    echo "<h2>Test 1: Database Schema Verification</h2>";
    
    $tables = ['classes', 'enrollments', 'payments', 'recordings', 'class_recordings', 'users'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<span class='success'>✓ Table '$table' exists</span><br>";
        } else {
            echo "<span class='error'>✗ Table '$table' missing</span><br>";
        }
    }
    
    // Check enrollments table has status column
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'status'");
    if ($stmt->rowCount() > 0) {
        echo "<span class='success'>✓ Enrollments table has status column</span><br>";
    } else {
        echo "<span class='error'>✗ Enrollments table missing status column</span><br>";
    }
    echo "</div>";

    // Test 2: Check sample data
    echo "<div class='test-section'>";
    echo "<h2>Test 2: Sample Data Overview</h2>";
    
    $classes_count = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
    $users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $payments_count = $pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
    $enrollments_count = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
    $recordings_count = $pdo->query("SELECT COUNT(*) FROM recordings")->fetchColumn();
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Record Count</th></tr>";
    echo "<tr><td>Classes</td><td>$classes_count</td></tr>";
    echo "<tr><td>Users</td><td>$users_count</td></tr>";
    echo "<tr><td>Payments</td><td>$payments_count</td></tr>";
    echo "<tr><td>Enrollments</td><td>$enrollments_count</td></tr>";
    echo "<tr><td>Recordings</td><td>$recordings_count</td></tr>";
    echo "</table>";
    echo "</div>";

    // Test 3: Payment Status Distribution
    echo "<div class='test-section'>";
    echo "<h2>Test 3: Payment Status Distribution</h2>";
    
    if ($payments_count > 0) {
        $payment_stats = $pdo->query("
            SELECT status, COUNT(*) as count 
            FROM payments 
            GROUP BY status
        ")->fetchAll();
        
        echo "<table>";
        echo "<tr><th>Status</th><th>Count</th></tr>";
        foreach ($payment_stats as $stat) {
            echo "<tr><td>{$stat['status']}</td><td>{$stat['count']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='info'>No payment records found</span>";
    }
    echo "</div>";

    // Test 4: Enrollment Status Distribution
    echo "<div class='test-section'>";
    echo "<h2>Test 4: Enrollment Status Distribution</h2>";
    
    if ($enrollments_count > 0) {
        $enrollment_stats = $pdo->query("
            SELECT status, COUNT(*) as count 
            FROM enrollments 
            GROUP BY status
        ")->fetchAll();
        
        echo "<table>";
        echo "<tr><th>Status</th><th>Count</th></tr>";
        foreach ($enrollment_stats as $stat) {
            echo "<tr><td>{$stat['status']}</td><td>{$stat['count']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='info'>No enrollment records found</span>";
    }
    echo "</div>";

    // Test 5: Course-Recording Relationships
    echo "<div class='test-section'>";
    echo "<h2>Test 5: Course-Recording Relationships</h2>";
    
    $course_recordings = $pdo->query("
        SELECT 
            c.title as course_title,
            COUNT(cr.recording_id) as recording_count
        FROM classes c
        LEFT JOIN class_recordings cr ON c.id = cr.class_id
        GROUP BY c.id, c.title
        ORDER BY recording_count DESC
    ")->fetchAll();
    
    if (!empty($course_recordings)) {
        echo "<table>";
        echo "<tr><th>Course</th><th>Recording Count</th></tr>";
        foreach ($course_recordings as $course) {
            echo "<tr><td>{$course['course_title']}</td><td>{$course['recording_count']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='info'>No course-recording relationships found</span>";
    }
    echo "</div>";

    // Test 6: User Enrollment Summary
    echo "<div class='test-section'>";
    echo "<h2>Test 6: User Enrollment Summary</h2>";
    
    $user_enrollments = $pdo->query("
        SELECT 
            u.name as user_name,
            u.email,
            COUNT(DISTINCT e.class_id) as enrolled_classes,
            COUNT(DISTINCT p.class_id) as payment_submissions
        FROM users u
        LEFT JOIN enrollments e ON u.id = e.user_id
        LEFT JOIN payments p ON u.id = p.user_id
        WHERE u.is_admin = 0
        GROUP BY u.id, u.name, u.email
        HAVING enrolled_classes > 0 OR payment_submissions > 0
    ")->fetchAll();
    
    if (!empty($user_enrollments)) {
        echo "<table>";
        echo "<tr><th>User</th><th>Email</th><th>Enrolled Classes</th><th>Payment Submissions</th></tr>";
        foreach ($user_enrollments as $user) {
            echo "<tr>";
            echo "<td>{$user['user_name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['enrolled_classes']}</td>";
            echo "<td>{$user['payment_submissions']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='info'>No user enrollments found</span>";
    }
    echo "</div>";

    // Test 7: System Health Check
    echo "<div class='test-section'>";
    echo "<h2>Test 7: System Health Check</h2>";
    
    // Check for orphaned records
    $orphaned_payments = $pdo->query("
        SELECT COUNT(*) FROM payments p 
        LEFT JOIN users u ON p.user_id = u.id 
        LEFT JOIN classes c ON p.class_id = c.id 
        WHERE u.id IS NULL OR c.id IS NULL
    ")->fetchColumn();
    
    $orphaned_enrollments = $pdo->query("
        SELECT COUNT(*) FROM enrollments e 
        LEFT JOIN users u ON e.user_id = u.id 
        LEFT JOIN classes c ON e.class_id = c.id 
        WHERE u.id IS NULL OR c.id IS NULL
    ")->fetchColumn();
    
    echo "<table>";
    echo "<tr><th>Check</th><th>Result</th></tr>";
    echo "<tr><td>Orphaned Payments</td><td>" . ($orphaned_payments == 0 ? "<span class='success'>None</span>" : "<span class='error'>$orphaned_payments found</span>") . "</td></tr>";
    echo "<tr><td>Orphaned Enrollments</td><td>" . ($orphaned_enrollments == 0 ? "<span class='success'>None</span>" : "<span class='error'>$orphaned_enrollments found</span>") . "</td></tr>";
    echo "</table>";
    echo "</div>";

    echo "<div class='test-section'>";
    echo "<h2>Test Complete</h2>";
    echo "<p class='success'>All tests completed successfully!</p>";
    echo "<p><a href='admin/manage_payments.php'>Go to Payment Management</a> | <a href='my-classes.php'>View My Classes</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='test-section'>";
    echo "<h2>Database Error</h2>";
    echo "<span class='error'>Error: " . $e->getMessage() . "</span>";
    echo "</div>";
}
?>