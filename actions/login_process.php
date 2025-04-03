<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? $_POST['remember'] : false;
    
    // Validate form data
    if (empty($email) || empty($password)) {
        header('Location: ../index.php?page=login&error=empty');
        exit;
    }
    
    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Valid login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['is_admin'] = ($user['role'] === 'admin');
            
            // If remember me is checked, set cookie
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                
                // Store token in database
                $stmt = $pdo->prepare("
                    INSERT INTO remember_tokens (user_id, token, expires_at) 
                    VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 30 DAY))
                ");
                $stmt->execute([
                    'user_id' => $user['id'],
                    'token' => $token
                ]);
                
                // Set cookie
                setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
            }
            
            // Log login activity
            logUserActivity($user['id'], 'login');
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ../index.php?page=dashboard');
            } else {
                // Redirect to homepage or where they came from
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '../index.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
            }
            exit;
        } else {
            // Invalid login
            header('Location: ../index.php?page=login&error=invalid');
            exit;
        }
    } catch (PDOException $e) {
        // Log error
        error_log("Login Error: " . $e->getMessage());
        
        header('Location: ../index.php?page=login&error=server');
        exit;
    }
} else {
    // If not a POST request, redirect to login page
    header('Location: ../index.php?page=login');
    exit;
}
?> 