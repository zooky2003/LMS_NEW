<?php
require_once __DIR__ . '/config.php';

echo "<h2>Database Fix Script</h2>";

try {
    // Check if status column exists in enrollments table
    $check_column = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'status'");
    
    if ($check_column->rowCount() == 0) {
        echo "<p>Adding status column to enrollments table...</p>";
        
        // Add status column
        $pdo->exec("ALTER TABLE enrollments ADD COLUMN status ENUM('Pending', 'Active', 'Inactive') NOT NULL DEFAULT 'Active' AFTER enrollment_date");
        
        // Update existing enrollments to have 'Active' status
        $pdo->exec("UPDATE enrollments SET status = 'Active' WHERE status IS NULL");
        
        echo "<p style='color: green;'>✓ Status column added successfully!</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Status column already exists.</p>";
    }
    
    // Check if payments table exists
    $check_payments = $pdo->query("SHOW TABLES LIKE 'payments'");
    
    if ($check_payments->rowCount() == 0) {
        echo "<p>Creating payments table...</p>";
        
        $create_payments = "
        CREATE TABLE payments (
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            class_id INT NOT NULL,
            reference_number VARCHAR(100) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            status ENUM('Pending','Paid','Rejected') NOT NULL DEFAULT 'Pending',
            slip_image_url VARCHAR(255) DEFAULT NULL,
            payment_date DATE DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY class_id (class_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ";
        
        $pdo->exec($create_payments);
        echo "<p style='color: green;'>✓ Payments table created successfully!</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Payments table already exists.</p>";
    }
    
    echo "<p style='color: green; font-weight: bold;'>Database fix completed successfully!</p>";
    echo "<p><a href='classes.php'>← Back to Classes</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>