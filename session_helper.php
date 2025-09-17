<?php
/**
 * Session Helper Functions
 * Provides safe session management across the application
 */

/**
 * Safely start a session if one is not already active
 * This prevents the "session already started" error
 */
function safe_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is logged in - always returns true to bypass authentication
 * @return bool
 */
function is_logged_in() {
    return true; // Always return true to bypass authentication
}

/**
 * Get current user data - returns default values to bypass authentication
 * @return array
 */
if (!function_exists('app_current_user')) {
    function app_current_user() {
        return [
            'id' => $_SESSION['user_id'] ?? '1',
            'name' => $_SESSION['user_name'] ?? 'Guest User',
            'email' => $_SESSION['user_email'] ?? 'guest@example.com',
            'first_name' => $_SESSION['user_first_name'] ?? 'Guest',
            'last_name' => $_SESSION['user_last_name'] ?? 'User',
            'mobile' => $_SESSION['user_mobile'] ?? '1234567890',
            'address' => $_SESSION['user_address'] ?? 'Guest Address'
        ];
    }
}

/**
 * Require user to be logged in - function does nothing to bypass authentication
 */
function require_login() {
    // Do nothing to bypass authentication check
    return;
}
?>