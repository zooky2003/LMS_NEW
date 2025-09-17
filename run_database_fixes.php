<?php
/**
 * Database Fix Script
 * Run this script once to apply all necessary database fixes
 */

require_once __DIR__ . '/config.php';

try {
    // Read the SQL file
    $sql_file = __DIR__ . '/database_fixes.sql';
    if (!file_exists($sql_file)) {
        die("Error: database_fixes.sql file not found.\n");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Split SQL statements (simple approach - assumes statements are separated by semicolons)
    $statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    $success_count = 0;
    $error_count = 0;
    
    echo "Starting database fixes...\n\n";
    
    foreach ($statements as $statement) {
        // Skip empty statements and comments
        if (empty($statement) || strpos(trim($statement), '--') === 0 || strpos(trim($statement), '/*') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $success_count++;
            echo "✓ Executed: " . substr(trim($statement), 0, 50) . "...\n";
        } catch (PDOException $e) {
            $error_count++;
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "  Statement: " . substr(trim($statement), 0, 100) . "...\n";
        }
    }
    
    echo "\n=== Database Fix Summary ===\n";
    echo "Successful operations: $success_count\n";
    echo "Failed operations: $error_count\n";
    
    if ($error_count === 0) {
        echo "\n🎉 All database fixes applied successfully!\n";
        echo "You can now:\n";
        echo "1. Test the payment system\n";
        echo "2. Check that paid cards display on My Classes page\n";
        echo "3. Access the paid.php page after payment approval\n";
        echo "4. Upload custom thumbnails for recordings in admin panel\n";
    } else {
        echo "\n⚠️  Some fixes failed. Please check the errors above.\n";
    }
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
}
?>