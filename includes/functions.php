<?php
// Format price with currency symbol
function formatPrice($price) {
    return '€' . number_format($price, 2);
}

// Get cart items count
function getCartItemsCount() {
    global $pdo;
    
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(quantity), 0) as total_items
            FROM cart_items
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error getting cart count: " . $e->getMessage());
        return 0;
    }
}

// Calculate cart total
function getCartTotal() {
    global $pdo;
    
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(ci.quantity * p.price), 0) as total
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        return (float)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error calculating cart total: " . $e->getMessage());
        return 0;
    }
}

// Get product details by ID
function getProductById($productId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    
    return $stmt->fetch();
}

// Check if a product requires prescription
function requiresPrescription($productId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT requires_prescription FROM products WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();
    
    return $product && $product['requires_prescription'] == 1;
}

// Get all products by category
function getProductsByCategory($category) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = :category");
    $stmt->execute(['category' => $category]);
    
    return $stmt->fetchAll();
}

// Get recent orders
function getRecentOrders($limit = 5) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT o.*, os.name as status_name 
        FROM orders o
        JOIN order_statuses os ON o.status_id = os.id
        ORDER BY o.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Get order details including items
function getOrderDetails($orderId) {
    global $pdo;
    
    // Get order
    $stmt = $pdo->prepare("
        SELECT o.*, os.name as status_name 
        FROM orders o
        JOIN order_statuses os ON o.status_id = os.id
        WHERE o.id = :order_id
    ");
    $stmt->execute(['order_id' => $orderId]);
    $order = $stmt->fetch();
    
    if (!$order) {
        return false;
    }
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute(['order_id' => $orderId]);
    $order['items'] = $stmt->fetchAll();
    
    return $order;
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Log user activity
function logUserActivity($user_id, $action, $details = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO user_activity_logs (user_id, action, details, ip_address, created_at)
            VALUES (:user_id, :action, :details, :ip_address, NOW())
        ");
        
        $stmt->execute([
            'user_id' => $user_id,
            'action' => $action,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Error logging user activity: " . $e->getMessage());
        return false;
    }
}

// Check if user is staff
function isStaff() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'staff');
}
?> 