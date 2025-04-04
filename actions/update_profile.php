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
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

// Handle profile picture upload
$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profile_picture'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['flash_message'] = "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=profile');
        exit;
    }
    
    if ($file['size'] > $max_size) {
        $_SESSION['flash_message'] = "File too large. Maximum size is 5MB.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=profile');
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/profile_pictures/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('profile_') . '.' . $extension;
    $target_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $profile_picture = 'uploads/profile_pictures/' . $filename;
        
        // Delete old profile picture if exists
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $old_picture = $stmt->fetchColumn();
        
        if ($old_picture && file_exists('../' . $old_picture)) {
            unlink('../' . $old_picture);
        }
    }
}

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email)) {
    $_SESSION['flash_message'] = "First name, last name, and email are required.";
    $_SESSION['flash_type'] = "red";
    header('Location: ../index.php?page=profile');
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_message'] = "Please enter a valid email address.";
    $_SESSION['flash_type'] = "red";
    header('Location: ../index.php?page=profile');
    exit;
}

try {
    // Check if email is already taken by another user
    $stmt = $pdo->prepare("
        SELECT id FROM users 
        WHERE email = :email AND id != :user_id
    ");
    $stmt->execute([
        'email' => $email,
        'user_id' => $_SESSION['user_id']
    ]);
    
    if ($stmt->fetch()) {
        $_SESSION['flash_message'] = "This email address is already in use.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=profile');
        exit;
    }
    
    // Update user profile
    $stmt = $pdo->prepare("
        UPDATE users 
        SET first_name = :first_name,
            last_name = :last_name,
            email = :email,
            phone = :phone,
            address = :address" . 
            ($profile_picture ? ", profile_picture = :profile_picture" : "") . ",
            updated_at = NOW()
        WHERE id = :user_id
    ");
    
    $params = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'user_id' => $_SESSION['user_id']
    ];

    if ($profile_picture) {
        $params['profile_picture'] = $profile_picture;
    }

    $stmt->execute($params);
    
    // Update session variables
    $_SESSION['user_name'] = $first_name . ' ' . $last_name;
    $_SESSION['user_email'] = $email;
    
    // Log activity
    logUserActivity($_SESSION['user_id'], 'profile_updated', 'Updated profile information');
    
    $_SESSION['flash_message'] = "Profile updated successfully.";
    $_SESSION['flash_type'] = "green";
    
} catch (PDOException $e) {
    // Log error
    error_log("Profile Update Error: " . $e->getMessage());
    
    $_SESSION['flash_message'] = "An error occurred while updating your profile. Please try again.";
    $_SESSION['flash_type'] = "red";
}

// Redirect back to profile page
header('Location: ../index.php?page=profile');
exit; 