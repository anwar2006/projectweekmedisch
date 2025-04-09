<?php
// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order details
try {
    $stmt = $pdo->prepare("
        SELECT o.*, os.name as status_name
        FROM orders o
        JOIN order_statuses os ON o.status_id = os.id
        WHERE o.id = :order_id
    ");
    $stmt->execute(['order_id' => $order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        $_SESSION['flash_message'] = "Order not found.";
        $_SESSION['flash_type'] = "red";
        header('Location: index.php');
        exit;
    }

    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute(['order_id' => $order_id]);
    $items = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Order Confirmation Error: " . $e->getMessage());
    $_SESSION['flash_message'] = "An error occurred while retrieving your order.";
    $_SESSION['flash_type'] = "red";
    header('Location: index.php');
    exit;
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="text-center mb-8">
        <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
        <h1 class="text-2xl font-bold text-gray-800">Order Confirmed!</h1>
        <p class="text-gray-600 mt-2">Thank you for your purchase. Your order has been received.</p>
    </div>

    <!-- Order Details -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold mb-4">Order Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Order Number</p>
                    <p class="font-medium">#<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Order Date</p>
                    <p class="font-medium"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="font-medium text-green-600"><?php echo $order['status_name']; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Amount</p>
                    <p class="font-medium"><?php echo formatPrice($order['total_amount']); ?></p>
                </div>
            </div>
        </div>

        <!-- Shipping Information -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold mb-4">Shipping Information</h2>
            <div class="space-y-2">
                <p class="font-medium"><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></p>
                <p><?php echo $order['address']; ?></p>
                <p><?php echo $order['city'] . ', ' . $order['postal_code']; ?></p>
                <p class="text-gray-600"><?php echo $order['email']; ?></p>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Order Items</h2>
            <div class="space-y-4">
                <?php foreach ($items as $item): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-16 w-16 bg-gray-100 rounded overflow-hidden mr-4 flex-shrink-0">
                            <?php if (isset($item['image']) && !empty($item['image'])): ?>
                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="h-full w-full object-cover">
                            <?php else: ?>
                            <div class="h-full w-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800"><?php echo $item['name']; ?></h3>
                            <p class="text-gray-500 text-sm">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-medium"><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="max-w-2xl mx-auto mt-8 flex justify-center space-x-4">
        <a href="index.php" class="bg-primary hover:bg-dark text-white font-bold py-2 px-4 rounded-lg transition-colors">
            Continue Shopping
        </a>
        <a href="index.php?page=profile" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
            View Orders
        </a>
    </div>
</div> 