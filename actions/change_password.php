<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?page=login&error=unauthorized');
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=profile');
    exit;
}

// Get and sanitize input
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate required fields
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['flash_message'] = "All password fields are required.";
    $_SESSION['flash_type'] = "red";
    header('Location: ../index.php?page=profile#change-password');
    exit;
}

// Validate password match
if ($new_password !== $confirm_password) {
    $_SESSION['flash_message'] = "New passwords do not match.";
    $_SESSION['flash_type'] = "red";
    header('Location: ../index.php?page=profile#change-password');
    exit;
}

// Validate password strength
if (strlen($new_password) < 8) {
    $_SESSION['flash_message'] = "Password must be at least 8 characters long.";
    $_SESSION['flash_type'] = "red";
    header('Location: ../index.php?page=profile#change-password');
    exit;
}

try {
    // Get current user's password hash
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $_SESSION['flash_message'] = "User not found.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=profile#change-password');
        exit;
    }
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['flash_message'] = "Current password is incorrect.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=profile#change-password');
        exit;
    }
    
    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $pdo->prepare("
        UPDATE users 
        SET password = :password,
            updated_at = NOW()
        WHERE id = :user_id
    ");
    
    $stmt->execute([
        'password' => $password_hash,
        'user_id' => $_SESSION['user_id']
    ]);
    
    // Log activity
    logUserActivity($_SESSION['user_id'], 'password_changed', 'Changed account password');
    
    $_SESSION['flash_message'] = "Password changed successfully.";
    $_SESSION['flash_type'] = "green";
    
} catch (PDOException $e) {
    // Log error
    error_log("Password Change Error: " . $e->getMessage());
    
    $_SESSION['flash_message'] = "An error occurred while changing your password. Please try again.";
    $_SESSION['flash_type'] = "red";
}

// Redirect back to profile page
header('Location: ../index.php?page=profile#change-password');
exit; 