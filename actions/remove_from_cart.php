<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to remove items from cart']);
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get product ID
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    // Validate input
    if ($product_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        exit;
    }
    
    try {
        // Remove item from cart
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'product_id' => $product_id
        ]);
        
        // Log activity
        logUserActivity($_SESSION['user_id'], 'remove_from_cart', [
            'product_id' => $product_id
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Product removed from cart'
        ]);
        
    } catch (PDOException $e) {
        error_log("Remove from Cart Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 