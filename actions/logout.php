<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Log the logout activity if user is logged in
if (isset($_SESSION['user_id'])) {
    logUserActivity($_SESSION['user_id'], 'logout');
    
    // Remove remember token if exists
    if (isset($_COOKIE['remember_token'])) {
        try {
            // Delete token from database
            $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token = :token");
            $stmt->execute(['token' => $_COOKIE['remember_token']]);
            
            // Delete cookie
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        } catch (PDOException $e) {
            error_log("Logout Error: " . $e->getMessage());
        }
    }
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: ../index.php');
exit;
?> 