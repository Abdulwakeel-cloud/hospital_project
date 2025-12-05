<?php
/**
 * Secure Session Configuration
 * Include this file instead of calling session_start() directly
 */

// Configure secure session settings before starting session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 when using HTTPS in production
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600); // 1 hour session timeout

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    // Regenerate session ID every 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

