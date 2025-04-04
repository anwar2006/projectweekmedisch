<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

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
    
    // Check if cart exists and has items
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Cart is empty'
        ]);
        exit;
    }
    
    // Find and remove the product from cart
    $found = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            $found = true;
            break;
        }
    }
    
    // Reindex the cart array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    
    if ($found) {
        // Log activity if user is logged in
        if (isset($_SESSION['user_id'])) {
            logUserActivity($_SESSION['user_id'], 'remove_from_cart', [
                'product_id' => $product_id
            ]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Product removed from cart',
            'cart_count' => getCartItemsCount(),
            'cart_total' => getCartTotal()
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found in cart'
        ]);
    }
    exit;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}
?> 