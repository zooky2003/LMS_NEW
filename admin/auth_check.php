<?php
/**
 * This file acts as a security guard for all protected admin pages.
 * We will include it at the very top of every page that requires an admin to be logged in.
 */

// Go up one directory to access the session helper from the root folder
require_once __DIR__ . '/../session_helper.php';

// Start the session safely
safe_session_start();

// Check if the 'is_admin' session variable is set and is true
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // If the check fails, the user is not a logged-in admin.
    // Redirect them to the login page immediately.
    header('Location: login.php');
    
    // Stop the script from running any further
    exit();
}

// If the script is allowed to continue past this point, it means the user is a verified admin.