<?php
// Database configuration
$host = 'localhost';
$dbname = 'aphothecare_db';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password (empty)

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Set character set to utf8mb4
    $pdo->exec("SET NAMES utf8mb4");
    
} catch (PDOException $e) {
    // Log the error and display a user-friendly message
    error_log("Database Connection Error: " . $e->getMessage());
    
    // For development only, in production you would show a generic error
    die("Database connection failed. Please try again later.");
}
?> 