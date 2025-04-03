<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = isset($_POST['first_name']) ? sanitizeInput($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitizeInput($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validate form data
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password) || !$terms) {
        header('Location: ../index.php?page=register&error=empty');
        exit;
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        header('Location: ../index.php?page=register&error=password_mismatch');
        exit;
    }
    
    // Check password strength
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        header('Location: ../index.php?page=register&error=password_weak');
        exit;
    }
    
    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            header('Location: ../index.php?page=register&error=email_exists');
            exit;
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Default role for new users
        $role = 'customer';
        
        // Insert new user
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password, role, created_at) 
            VALUES (:first_name, :last_name, :email, :phone, :password, :role, NOW())
        ");
        
        $stmt->execute([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'password' => $hashed_password,
            'role' => $role
        ]);
        
        $user_id = $pdo->lastInsertId();
        
        // Create shipping address record (empty initially)
        $stmt = $pdo->prepare("
            INSERT INTO user_addresses (user_id, type, created_at) 
            VALUES (:user_id, 'shipping', NOW())
        ");
        $stmt->execute(['user_id' => $user_id]);
        
        // Log registration
        logUserActivity($user_id, 'registration');
        
        // Redirect to login page with success message
        header('Location: ../index.php?page=login&success=register');
        exit;
        
    } catch (PDOException $e) {
        // Log error
        error_log("Registration Error: " . $e->getMessage());
        
        header('Location: ../index.php?page=register&error=server');
        exit;
    }
} else {
    // If not a POST request, redirect to register page
    header('Location: ../index.php?page=register');
    exit;
}
?> 