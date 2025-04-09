<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    try {
        // Get product details
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        // Check if product requires prescription
        if ($product['requires_prescription']) {
            // Check if user has approved prescription
            $prescription_stmt = $pdo->prepare("
                SELECT id 
                FROM prescriptions 
                WHERE user_id = :user_id 
                AND product_id = :product_id 
                AND status = 'approved'
            ");
            $prescription_stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'product_id' => $product_id
            ]);
            
            if (!$prescription_stmt->fetch()) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'This medication requires an approved prescription',
                    'redirect' => 'prescription'
                ]);
                exit;
            }
        }
        
        // Check stock
        if ($product['stock_quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
            exit;
        }
        
        // Add to cart
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (user_id, product_id, quantity, created_at)
            VALUES (:user_id, :product_id, :quantity, NOW())
            ON DUPLICATE KEY UPDATE quantity = quantity + :quantity
        ");
        
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'product_id' => $product_id,
            'quantity' => $quantity
        ]);
        
        echo json_encode(['success' => true]);
        
    } catch (PDOException $e) {
        error_log("Add to Cart Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 