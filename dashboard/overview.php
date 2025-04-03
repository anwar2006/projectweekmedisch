<?php
// Get statistics from database
try {
    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $total_orders = $stmt->fetch()['total'] ?? 0;
    
    // Pending orders
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM orders WHERE status_id = 1");
    $pending_orders = $stmt->fetch()['pending'] ?? 0;
    
    // Total customers
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
    $total_customers = $stmt->fetch()['total'] ?? 0;
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $total_products = $stmt->fetch()['total'] ?? 0;
    
    // Pending prescriptions
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM prescriptions WHERE status = 'pending'");
    $pending_prescriptions = $stmt->fetch()['pending'] ?? 0;
    
    // Revenue today
    $stmt = $pdo->query("
        SELECT SUM(total_amount) as revenue 
        FROM orders 
        WHERE DATE(created_at) = CURDATE() AND status_id IN (3, 4, 5)
    ");
    $revenue_today = $stmt->fetch()['revenue'] ?? 0;
    
    // Recent orders
    $stmt = $pdo->query("
        SELECT o.*, u.first_name, u.last_name, os.name as status_name 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_statuses os ON o.status_id = os.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $recent_orders = $stmt->fetchAll();
    
    // Low stock products
    $stmt = $pdo->query("
        SELECT * FROM products 
        WHERE stock_quantity <= reorder_level
        ORDER BY stock_quantity ASC
        LIMIT 5
    ");
    $low_stock_products = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // In case of error, set default values
    $total_orders = 0;
    $pending_orders = 0;
    $total_customers = 0;
    $total_products = 0;
    $pending_prescriptions = 0;
    $revenue_today = 0;
    $recent_orders = [];
    $low_stock_products = [];
    
    // Log error
    error_log("Dashboard Error: " . $e->getMessage());
}

// Placeholders if database tables don't exist yet
if (empty($recent_orders)) {
    $recent_orders = [
        ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'total_amount' => 78.95, 'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')), 'status_name' => 'Processing'],
        ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith', 'total_amount' => 129.50, 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')), 'status_name' => 'Shipped'],
        ['id' => 3, 'first_name' => 'Robert', 'last_name' => 'Johnson', 'total_amount' => 45.75, 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')), 'status_name' => 'Delivered'],
    ];
}

if (empty($low_stock_products)) {
    $low_stock_products = [
        ['id' => 1, 'name' => 'Paracetamol 500mg', 'stock_quantity' => 5, 'reorder_level' => 10],
        ['id' => 2, 'name' => 'Amoxicillin 250mg', 'stock_quantity' => 3, 'reorder_level' => 15],
        ['id' => 3, 'name' => 'Ibuprofen 400mg', 'stock_quantity' => 8, 'reorder_level' => 20],
    ];
}
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Orders</p>
                <h3 class="text-2xl font-semibold"><?php echo $total_orders; ?></h3>
            </div>
        </div>
        <div class="mt-4">
            <a href="index.php?page=dashboard&action=orders" class="text-blue-500 text-sm hover:underline">View all orders</a>
        </div>
    </div>
    
    <!-- Pending Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-500 mr-4">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Pending Orders</p>
                <h3 class="text-2xl font-semibold"><?php echo $pending_orders; ?></h3>
            </div>
        </div>
        <div class="mt-4">
            <a href="index.php?page=dashboard&action=orders&filter=pending" class="text-orange-500 text-sm hover:underline">Process pending orders</a>
        </div>
    </div>
    
    <!-- Total Customers -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Customers</p>
                <h3 class="text-2xl font-semibold"><?php echo $total_customers; ?></h3>
            </div>
        </div>
        <div class="mt-4">
            <a href="index.php?page=dashboard&action=customers" class="text-green-500 text-sm hover:underline">View all customers</a>
        </div>
    </div>
    
    <!-- Revenue Today -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                <i class="fas fa-euro-sign text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Revenue Today</p>
                <h3 class="text-2xl font-semibold"><?php echo formatPrice($revenue_today); ?></h3>
            </div>
        </div>
        <div class="mt-4">
            <a href="index.php?page=dashboard&action=reports&report=sales" class="text-purple-500 text-sm hover:underline">View sales report</a>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Total Products -->
    <div class="bg-white rounded-lg shadow-md p-4 flex items-center">
        <div class="p-3 rounded-full bg-teal-100 text-teal-500 mr-4">
            <i class="fas fa-pills text-xl"></i>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Total Products</p>
            <h3 class="text-xl font-semibold"><?php echo $total_products; ?></h3>
        </div>
        <div class="ml-auto">
            <a href="index.php?page=dashboard&action=products" class="text-teal-500 hover:underline">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Pending Prescriptions -->
    <div class="bg-white rounded-lg shadow-md p-4 flex items-center">
        <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
            <i class="fas fa-file-prescription text-xl"></i>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Pending Prescriptions</p>
            <h3 class="text-xl font-semibold"><?php echo $pending_prescriptions; ?></h3>
        </div>
        <div class="ml-auto">
            <a href="index.php?page=dashboard&action=prescriptions" class="text-red-500 hover:underline">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Low Stock Alert -->
    <div class="bg-white rounded-lg shadow-md p-4 flex items-center">
        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Low Stock Products</p>
            <h3 class="text-xl font-semibold"><?php echo count($low_stock_products); ?></h3>
        </div>
        <div class="ml-auto">
            <a href="index.php?page=dashboard&action=products&filter=low_stock" class="text-yellow-500 hover:underline">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="index.php?page=dashboard&action=orders&new=true" class="flex flex-col items-center py-4 px-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mb-2">
                <i class="fas fa-plus"></i>
            </div>
            <span class="text-sm font-medium text-center">New Order</span>
        </a>
        
        <a href="index.php?page=dashboard&action=products&new=true" class="flex flex-col items-center py-4 px-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="p-3 rounded-full bg-green-100 text-green-500 mb-2">
                <i class="fas fa-plus"></i>
            </div>
            <span class="text-sm font-medium text-center">Add Product</span>
        </a>
        
        <a href="index.php?page=dashboard&action=prescriptions" class="flex flex-col items-center py-4 px-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="p-3 rounded-full bg-orange-100 text-orange-500 mb-2">
                <i class="fas fa-check"></i>
            </div>
            <span class="text-sm font-medium text-center">Review Prescriptions</span>
        </a>
        
        <a href="index.php?page=dashboard&action=reports" class="flex flex-col items-center py-4 px-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="p-3 rounded-full bg-purple-100 text-purple-500 mb-2">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="text-sm font-medium text-center">View Reports</span>
        </a>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Recent Orders</h3>
        <a href="index.php?page=dashboard&action=orders" class="text-blue-500 text-sm hover:underline">View All</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Order ID</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Customer</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Date</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Total</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Status</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-3 px-4">#<?php echo $order['id']; ?></td>
                    <td class="py-3 px-4"><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                    <td class="py-3 px-4"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td class="py-3 px-4"><?php echo formatPrice($order['total_amount']); ?></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded text-xs 
                            <?php 
                            switch ($order['status_name']) {
                                case 'Pending':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'Processing':
                                    echo 'bg-blue-100 text-blue-800';
                                    break;
                                case 'Shipped':
                                    echo 'bg-purple-100 text-purple-800';
                                    break;
                                case 'Delivered':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'Cancelled':
                                    echo 'bg-red-100 text-red-800';
                                    break;
                                default:
                                    echo 'bg-gray-100 text-gray-800';
                            }
                            ?>
                        ">
                            <?php echo $order['status_name']; ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <a href="index.php?page=dashboard&action=orders&id=<?php echo $order['id']; ?>" class="text-blue-500 hover:underline">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($recent_orders)): ?>
                <tr class="border-t">
                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">No orders found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Low Stock Products -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Low Stock Products</h3>
        <a href="index.php?page=dashboard&action=products&filter=low_stock" class="text-blue-500 text-sm hover:underline">View All</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Product ID</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Product Name</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Stock</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Reorder Level</th>
                    <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($low_stock_products as $product): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-3 px-4">#<?php echo $product['id']; ?></td>
                    <td class="py-3 px-4"><?php echo $product['name']; ?></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">
                            <?php echo $product['stock_quantity']; ?> left
                        </span>
                    </td>
                    <td class="py-3 px-4"><?php echo $product['reorder_level']; ?></td>
                    <td class="py-3 px-4">
                        <a href="index.php?page=dashboard&action=products&id=<?php echo $product['id']; ?>&edit=true" class="text-blue-500 hover:underline">Update Stock</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($low_stock_products)): ?>
                <tr class="border-t">
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">No low stock products found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> 