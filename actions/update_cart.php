<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

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
    
    // Check if cart exists and has items
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Cart is empty'
        ]);
        exit;
    }
    
    // Find and update the product quantity in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            if ($quantity == 0) {
                // Remove item if quantity is 0
                $key = array_search($item, $_SESSION['cart']);
                if ($key !== false) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                }
            } else {
                // Update quantity
                $item['quantity'] = $quantity;
            }
            $found = true;
            break;
        }
    }
    
    if ($found) {
        // Log activity if user is logged in
        if (isset($_SESSION['user_id'])) {
            logUserActivity($_SESSION['user_id'], 'update_cart', [
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart updated successfully',
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