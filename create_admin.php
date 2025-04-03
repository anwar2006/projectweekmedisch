<?php
require_once 'config/database.php';

// Admin credentials
$admin_email = 'admin@admin.com';
$admin_password = 'admin123';
$admin_first_name = 'Admin';
$admin_last_name = 'User';

try {
    // Hash the password
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $admin_email]);
    
    if ($stmt->fetch()) {
        echo "Admin user already exists!";
        exit;
    }
    
    // Create admin user
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, password, role, created_at) 
        VALUES (:first_name, :last_name, :email, :password, 'admin', NOW())
    ");
    
    $stmt->execute([
        'first_name' => $admin_first_name,
        'last_name' => $admin_last_name,
        'email' => $admin_email,
        'password' => $hashed_password
    ]);
    
    echo "Admin user created successfully!\n";
    echo "Email: " . $admin_email . "\n";
    echo "Password: " . $admin_password . "\n";
    
} catch (PDOException $e) {
    echo "Error creating admin user: " . $e->getMessage();
}
?> 