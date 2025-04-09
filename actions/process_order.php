<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['flash_message'] = "Your cart is empty. Please add some items before checkout.";
    $_SESSION['flash_type'] = "yellow";
    header('Location: ../index.php?page=cart');
    exit;
}

// Validate required fields
$required_fields = ['first_name', 'last_name', 'email', 'address', 'city', 'postal_code', 'payment_method'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $_SESSION['flash_message'] = "Please fill in all required fields.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=checkout');
        exit;
    }
}

// Validate payment method
$payment_method = $_POST['payment_method'];
if (!in_array($payment_method, ['paypal', 'ideal'])) {
    $_SESSION['flash_message'] = "Invalid payment method selected.";
    $_SESSION['flash_type'] = "red";
    header('Location: ../index.php?page=checkout');
    exit;
}

try {
    // Check database connection
    if (!isset($pdo)) {
        throw new Exception("Database connection not established");
    }

    // Start transaction
    $pdo->beginTransaction();

    // Calculate total amount
    $total_amount = getCartTotal() + 5.99; // Add shipping cost

    // Create orders table if not exists
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                address TEXT NOT NULL,
                city VARCHAR(100) NOT NULL,
                postal_code VARCHAR(20) NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                payment_method VARCHAR(50) NOT NULL,
                status_id INT DEFAULT 1,
                created_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } catch (PDOException $e) {
        error_log("Error creating orders table: " . $e->getMessage());
        throw $e;
    }

    // Create order_items table if not exists
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } catch (PDOException $e) {
        error_log("Error creating order_items table: " . $e->getMessage());
        throw $e;
    }

    // Create order_statuses table if not exists
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS order_statuses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } catch (PDOException $e) {
        error_log("Error creating order_statuses table: " . $e->getMessage());
        throw $e;
    }

    // Insert default order status if not exists
    try {
        $pdo->exec("
            INSERT IGNORE INTO order_statuses (id, name) VALUES (1, 'Pending')
        ");
    } catch (PDOException $e) {
        error_log("Error inserting default order status: " . $e->getMessage());
        throw $e;
    }

    // Insert order
    try {
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                user_id,
                first_name,
                last_name,
                email,
                address,
                city,
                postal_code,
                total_amount,
                payment_method,
                status_id,
                created_at
            ) VALUES (
                :user_id,
                :first_name,
                :last_name,
                :email,
                :address,
                :city,
                :postal_code,
                :total_amount,
                :payment_method,
                1,
                NOW()
            )
        ");

        $stmt->execute([
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'postal_code' => $_POST['postal_code'],
            'total_amount' => $total_amount,
            'payment_method' => $payment_method
        ]);

        $order_id = $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error inserting order: " . $e->getMessage());
        throw $e;
    }

    // Insert order items
    try {
        $stmt = $pdo->prepare("
            INSERT INTO order_items (
                order_id,
                product_id,
                quantity,
                price
            ) VALUES (
                :order_id,
                :product_id,
                :quantity,
                :price
            )
        ");

        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([
                'order_id' => $order_id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);

            // Update product stock if products table exists
            try {
                $update_stock = $pdo->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity - :quantity 
                    WHERE id = :product_id
                ");
                $update_stock->execute([
                    'quantity' => $item['quantity'],
                    'product_id' => $item['id']
                ]);
            } catch (PDOException $e) {
                // Ignore error if products table doesn't exist
                error_log("Stock update error (ignored): " . $e->getMessage());
            }
        }
    } catch (PDOException $e) {
        error_log("Error inserting order items: " . $e->getMessage());
        throw $e;
    }

    // Commit transaction
    $pdo->commit();

    // Clear cart
    unset($_SESSION['cart']);

    // Set success message
    $_SESSION['flash_message'] = "Order placed successfully! Thank you for your purchase.";
    $_SESSION['flash_type'] = "green";
    
    // Redirect to order confirmation
    header('Location: ../index.php?page=order-confirmation&id=' . $order_id);
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log error
    error_log("Order Processing Error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    
    // Set error message
    $_SESSION['flash_message'] = "An error occurred while processing your order. Please try again.";
    $_SESSION['flash_type'] = "red";
    
    // Redirect back to checkout
    header('Location: ../index.php?page=checkout');
    exit;
}
?> 