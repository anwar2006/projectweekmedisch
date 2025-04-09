<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to update cart']);
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    
    // Validate input
    if ($product_id <= 0 || $quantity < 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product or quantity'
        ]);
        exit;
    }
    
    try {
        // Check if product exists and get its details
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
            exit;
        }
        
        // Check stock
        if ($product['stock_quantity'] < $quantity) {
            echo json_encode([
                'success' => false,
                'message' => 'Not enough stock available'
            ]);
            exit;
        }
        
        if ($quantity == 0) {
            // Remove item from cart
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'product_id' => $product_id
            ]);
        } else {
            // Update or insert cart item
            $stmt = $pdo->prepare("
                INSERT INTO cart_items (user_id, product_id, quantity)
                VALUES (:user_id, :product_id, :quantity)
                ON DUPLICATE KEY UPDATE quantity = :quantity
            ");
            
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
        }
        
        // Log activity
        logUserActivity($_SESSION['user_id'], 'update_cart', [
            'product_id' => $product_id,
            'quantity' => $quantity
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart updated successfully'
        ]);
        
    } catch (PDOException $e) {
        error_log("Update Cart Error: " . $e->getMessage());
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