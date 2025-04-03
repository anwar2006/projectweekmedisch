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
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate input
    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product or quantity'
        ]);
        exit;
    }
    
    try {
        // Get product details
        $product = getProductById($product_id);
        
        if (!$product) {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
            exit;
        }
        
        // Check if product requires prescription
        if (requiresPrescription($product_id)) {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You need to login to purchase prescription medications',
                    'redirect' => 'login'
                ]);
                exit;
            }
            
            // Check if user has a valid prescription
            if (!hasValidPrescription($_SESSION['user_id'], $product_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'This product requires a valid prescription',
                    'redirect' => 'prescription'
                ]);
                exit;
            }
        }
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product already in cart
        $product_in_cart = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] += $quantity;
                $product_in_cart = true;
                break;
            }
        }
        
        // If product not in cart, add it
        if (!$product_in_cart) {
            $_SESSION['cart'][] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }
        
        // Log activity if user is logged in
        if (isset($_SESSION['user_id'])) {
            logUserActivity($_SESSION['user_id'], 'add_to_cart', [
                'product_id' => $product_id,
                'product_name' => $product['name'],
                'quantity' => $quantity
            ]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => getCartItemsCount(),
            'cart_total' => getCartTotal()
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred. Please try again.'
        ]);
        exit;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}
?> 