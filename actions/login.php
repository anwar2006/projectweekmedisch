<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);
    $redirect = $_POST['redirect'] ?? 'index.php';

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['flash_message'] = "Please fill in all fields.";
        $_SESSION['flash_type'] = "red";
        header('Location: index.php?page=login');
        exit;
    }

    try {
        // Get user from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'] == 1;

            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (30 * 24 * 60 * 60); // 30 days

                // Store token in database
                $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
                $stmt->execute([
                    'user_id' => $user['id'],
                    'token' => $token,
                    'expires_at' => date('Y-m-d H:i:s', $expires)
                ]);

                // Set cookie
                setcookie('remember_token', $token, $expires, '/', '', true, true);
            }

            // Log successful login
            error_log("User {$user['id']} logged in successfully");

            // Redirect to requested page or dashboard for admins
            if ($redirect && $redirect !== 'index.php') {
                header("Location: $redirect");
            } else if ($user['is_admin']) {
                header('Location: index.php?page=dashboard');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            // Invalid credentials
            $_SESSION['flash_message'] = "Invalid email or password.";
            $_SESSION['flash_type'] = "red";
            header('Location: index.php?page=login');
            exit;
        }
    } catch (PDOException $e) {
        // Log error
        error_log("Login Error: " . $e->getMessage());
        
        $_SESSION['flash_message'] = "An error occurred. Please try again later.";
        $_SESSION['flash_type'] = "red";
        header('Location: index.php?page=login');
        exit;
    }
} else {
    // If not POST request, redirect to login page
    header('Location: index.php?page=login');
    exit;
}
?> 