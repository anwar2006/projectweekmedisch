<?php
// Get all orders with user and status information
try {
    $stmt = $pdo->prepare("
        SELECT o.*, 
               u.first_name, u.last_name, u.email,
               os.name as status_name,
               COUNT(oi.id) as total_items,
               SUM(oi.quantity) as total_quantity
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_statuses os ON o.status_id = os.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
    error_log("Error fetching orders: " . $e->getMessage());
}

// Get all order statuses for the filter
try {
    $stmt = $pdo->prepare("SELECT * FROM order_statuses ORDER BY id");
    $stmt->execute();
    $statuses = $stmt->fetchAll();
} catch (PDOException $e) {
    $statuses = [];
    error_log("Error fetching order statuses: " . $e->getMessage());
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Order Management</h2>
        
        <div class="flex space-x-4">
            <div class="relative">
                <input type="text" id="searchOrder" placeholder="Search orders..." 
                    class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <select id="statusFilter" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $status): ?>
                <option value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <button onclick="exportOrders()" class="bg-primary hover:bg-dark text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-download mr-2"></i> Export
            </button>
        </div>
    </div>
    
    <?php if (empty($orders)): ?>
    <div class="text-center py-8">
        <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500">No orders found</p>
    </div>
    <?php else: ?>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Order</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Customer</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Items</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Total</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Status</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Date</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr class="border-t hover:bg-gray-50" data-status="<?php echo $order['status_id']; ?>">
                    <td class="py-4 px-4">
                        <div class="font-medium">#<?php echo $order['id']; ?></div>
                        <div class="text-sm text-gray-500">
                            <?php if ($order['tracking_number']): ?>
                            <div>Tracking: <?php echo $order['tracking_number']; ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="text-sm">
                            <div><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></div>
                            <div class="text-gray-500"><?php echo $order['email']; ?></div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <div class="text-sm">
                            <div><?php echo $order['total_items']; ?> items</div>
                            <div class="text-gray-500"><?php echo $order['total_quantity']; ?> units</div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <div class="font-medium"><?php echo formatPrice($order['total_amount']); ?></div>
                        <?php if ($order['discount_amount'] > 0): ?>
                        <div class="text-sm text-green-500">-<?php echo formatPrice($order['discount_amount']); ?> discount</div>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-2 py-1 rounded-full text-sm 
                            <?php 
                            switch ($order['status_id']) {
                                case 1: // Pending
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 2: // Processing
                                    echo 'bg-blue-100 text-blue-800';
                                    break;
                                case 3: // Shipped
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 4: // Delivered
                                    echo 'bg-gray-100 text-gray-800';
                                    break;
                                case 5: // Cancelled
                                    echo 'bg-red-100 text-red-800';
                                    break;
                            }
                            ?>">
                            <?php echo $order['status_name']; ?>
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        <div class="text-sm">
                            <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex justify-center space-x-2">
                            <button onclick="viewOrder(<?php echo $order['id']; ?>)" 
                                class="text-blue-500 hover:text-blue-700 transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="updateOrderStatus(<?php echo $order['id']; ?>)" 
                                class="text-green-500 hover:text-green-700 transition-colors" title="Update Status">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($order['status_id'] == 1): // Only show cancel button for pending orders ?>
                            <button onclick="cancelOrder(<?php echo $order['id']; ?>)" 
                                class="text-red-500 hover:text-red-700 transition-colors" title="Cancel Order">
                                <i class="fas fa-times"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
function viewOrder(orderId) {
    // Implement view order details
    alert('View order details functionality to be implemented');
}

function updateOrderStatus(orderId) {
    // Implement update order status
    alert('Update order status functionality to be implemented');
}

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        // Implement cancel order
        alert('Cancel order functionality to be implemented');
    }
}

function exportOrders() {
    // Implement export orders to CSV/Excel
    alert('Export orders functionality to be implemented');
}

// Search functionality
document.getElementById('searchOrder').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Status filter
document.getElementById('statusFilter').addEventListener('change', function(e) {
    const status = e.target.value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!status || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script> 