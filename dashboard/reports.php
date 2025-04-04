<?php
// Get date range from request or default to last 30 days
$end_date = date('Y-m-d');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : $end_date;

// Get report type from request or default to sales
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'sales';

// Initialize data arrays
$sales_data = [];
$inventory_data = [];
$customer_data = [];
$prescription_data = [];

try {
    // Sales Report Data
    if ($report_type == 'sales') {
        $stmt = $pdo->prepare("
            SELECT 
                DATE(o.created_at) as date,
                COUNT(DISTINCT o.id) as total_orders,
                SUM(oi.quantity) as total_items,
                SUM(oi.quantity * oi.price) as total_revenue,
                SUM(oi.quantity * oi.price * (1 - COALESCE(o.discount_percentage, 0) / 100)) as net_revenue
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            WHERE o.created_at BETWEEN :start_date AND :end_date
            AND o.status != 'cancelled'
            GROUP BY DATE(o.created_at)
            ORDER BY date
        ");
        $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
        $sales_data = $stmt->fetchAll();
    }
    
    // Inventory Report Data
    if ($report_type == 'inventory') {
        $stmt = $pdo->prepare("
            SELECT 
                p.id,
                p.name,
                p.sku,
                p.stock_level,
                p.price,
                c.name as category,
                COUNT(DISTINCT oi.id) as times_ordered,
                SUM(oi.quantity) as total_quantity_sold
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN order_items oi ON p.id = oi.product_id
            LEFT JOIN orders o ON oi.order_id = o.id
            AND o.created_at BETWEEN :start_date AND :end_date
            AND o.status != 'cancelled'
            GROUP BY p.id
            ORDER BY total_quantity_sold DESC
        ");
        $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
        $inventory_data = $stmt->fetchAll();
    }
    
    // Customer Report Data
    if ($report_type == 'customers') {
        $stmt = $pdo->prepare("
            SELECT 
                u.id,
                u.first_name,
                u.last_name,
                u.email,
                COUNT(DISTINCT o.id) as total_orders,
                SUM(oi.quantity) as total_items,
                SUM(oi.quantity * oi.price) as total_spent,
                MAX(o.created_at) as last_order_date
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.created_at BETWEEN :start_date AND :end_date
            AND o.status != 'cancelled'
            GROUP BY u.id
            ORDER BY total_spent DESC
        ");
        $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
        $customer_data = $stmt->fetchAll();
    }
    
    // Prescription Report Data
    if ($report_type == 'prescriptions') {
        $stmt = $pdo->prepare("
            SELECT 
                p.id,
                p.doctor_name,
                p.status,
                COUNT(DISTINCT o.id) as total_orders,
                SUM(oi.quantity) as total_items,
                SUM(oi.quantity * oi.price) as total_revenue
            FROM prescriptions p
            LEFT JOIN orders o ON p.id = o.prescription_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE p.created_at BETWEEN :start_date AND :end_date
            AND o.status != 'cancelled'
            GROUP BY p.id
            ORDER BY total_revenue DESC
        ");
        $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
        $prescription_data = $stmt->fetchAll();
    }
    
} catch (PDOException $e) {
    error_log("Error fetching report data: " . $e->getMessage());
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Reports & Analytics</h2>
        
        <div class="flex space-x-4">
            <form id="reportForm" class="flex space-x-4">
                <select name="report_type" id="reportType" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="sales" <?php echo $report_type == 'sales' ? 'selected' : ''; ?>>Sales Report</option>
                    <option value="inventory" <?php echo $report_type == 'inventory' ? 'selected' : ''; ?>>Inventory Report</option>
                    <option value="customers" <?php echo $report_type == 'customers' ? 'selected' : ''; ?>>Customer Report</option>
                    <option value="prescriptions" <?php echo $report_type == 'prescriptions' ? 'selected' : ''; ?>>Prescription Report</option>
                </select>
                
                <div class="flex space-x-2">
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" 
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" 
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                
                <button type="submit" class="bg-primary hover:bg-dark text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i> Generate
                </button>
                
                <button type="button" onclick="exportReport()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
            </form>
        </div>
    </div>
    
    <?php if ($report_type == 'sales'): ?>
    <!-- Sales Report -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-sm text-blue-600">Total Orders</div>
            <div class="text-2xl font-bold text-blue-800">
                <?php echo array_sum(array_column($sales_data, 'total_orders')); ?>
            </div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-sm text-green-600">Total Items</div>
            <div class="text-2xl font-bold text-green-800">
                <?php echo array_sum(array_column($sales_data, 'total_items')); ?>
            </div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="text-sm text-purple-600">Gross Revenue</div>
            <div class="text-2xl font-bold text-purple-800">
                $<?php echo number_format(array_sum(array_column($sales_data, 'total_revenue')), 2); ?>
            </div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-sm text-yellow-600">Net Revenue</div>
            <div class="text-2xl font-bold text-yellow-800">
                $<?php echo number_format(array_sum(array_column($sales_data, 'net_revenue')), 2); ?>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Date</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Orders</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Items</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Gross Revenue</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Net Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_data as $row): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-4 px-4"><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                    <td class="py-4 px-4 text-center"><?php echo $row['total_orders']; ?></td>
                    <td class="py-4 px-4 text-center"><?php echo $row['total_items']; ?></td>
                    <td class="py-4 px-4 text-right">$<?php echo number_format($row['total_revenue'], 2); ?></td>
                    <td class="py-4 px-4 text-right">$<?php echo number_format($row['net_revenue'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php elseif ($report_type == 'inventory'): ?>
    <!-- Inventory Report -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-sm text-blue-600">Total Products</div>
            <div class="text-2xl font-bold text-blue-800">
                <?php echo count($inventory_data); ?>
            </div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-sm text-green-600">Total Stock</div>
            <div class="text-2xl font-bold text-green-800">
                <?php echo array_sum(array_column($inventory_data, 'stock_level')); ?>
            </div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="text-sm text-purple-600">Total Value</div>
            <div class="text-2xl font-bold text-purple-800">
                $<?php echo number_format(array_sum(array_map(function($item) { 
                    return $item['stock_level'] * $item['price']; 
                }, $inventory_data)), 2); ?>
            </div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-sm text-yellow-600">Items Sold</div>
            <div class="text-2xl font-bold text-yellow-800">
                <?php echo array_sum(array_column($inventory_data, 'total_quantity_sold')); ?>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Product</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Category</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Stock</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Price</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Orders</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory_data as $row): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-4 px-4">
                        <div class="font-medium"><?php echo $row['name']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo $row['sku']; ?></div>
                    </td>
                    <td class="py-4 px-4"><?php echo $row['category']; ?></td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-2 py-1 rounded-full text-sm 
                            <?php echo $row['stock_level'] > 10 ? 'bg-green-100 text-green-800' : 
                                ($row['stock_level'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                            <?php echo $row['stock_level']; ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right">$<?php echo number_format($row['price'], 2); ?></td>
                    <td class="py-4 px-4 text-center"><?php echo $row['times_ordered']; ?></td>
                    <td class="py-4 px-4 text-center"><?php echo $row['total_quantity_sold']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php elseif ($report_type == 'customers'): ?>
    <!-- Customer Report -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-sm text-blue-600">Total Customers</div>
            <div class="text-2xl font-bold text-blue-800">
                <?php echo count($customer_data); ?>
            </div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-sm text-green-600">Total Orders</div>
            <div class="text-2xl font-bold text-green-800">
                <?php echo array_sum(array_column($customer_data, 'total_orders')); ?>
            </div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="text-sm text-purple-600">Total Items</div>
            <div class="text-2xl font-bold text-purple-800">
                <?php echo array_sum(array_column($customer_data, 'total_items')); ?>
            </div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-sm text-yellow-600">Total Revenue</div>
            <div class="text-2xl font-bold text-yellow-800">
                $<?php echo number_format(array_sum(array_column($customer_data, 'total_spent')), 2); ?>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Customer</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Orders</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Items</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Total Spent</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Last Order</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customer_data as $row): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-4 px-4">
                        <div class="font-medium"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo $row['email']; ?></div>
                    </td>
                    <td class="py-4 px-4 text-center"><?php echo $row['total_orders']; ?></td>
                    <td class="py-4 px-4 text-center"><?php echo $row['total_items']; ?></td>
                    <td class="py-4 px-4 text-right">$<?php echo number_format($row['total_spent'], 2); ?></td>
                    <td class="py-4 px-4"><?php echo date('M d, Y', strtotime($row['last_order_date'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php elseif ($report_type == 'prescriptions'): ?>
    <!-- Prescription Report -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-sm text-blue-600">Total Prescriptions</div>
            <div class="text-2xl font-bold text-blue-800">
                <?php echo count($prescription_data); ?>
            </div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-sm text-green-600">Total Orders</div>
            <div class="text-2xl font-bold text-green-800">
                <?php echo array_sum(array_column($prescription_data, 'total_orders')); ?>
            </div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="text-sm text-purple-600">Total Items</div>
            <div class="text-2xl font-bold text-purple-800">
                <?php echo array_sum(array_column($prescription_data, 'total_items')); ?>
            </div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-sm text-yellow-600">Total Revenue</div>
            <div class="text-2xl font-bold text-yellow-800">
                $<?php echo number_format(array_sum(array_column($prescription_data, 'total_revenue')), 2); ?>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Doctor</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Status</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Orders</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Items</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescription_data as $row): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-4 px-4">
                        <div class="font-medium"><?php echo $row['doctor_name']; ?></div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-2 py-1 rounded-full text-sm 
                            <?php 
                            switch ($row['status']) {
                                case 'pending':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'approved':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'rejected':
                                    echo 'bg-red-100 text-red-800';
                                    break;
                            }
                            ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-center"><?php echo $row['total_orders']; ?></td>
                    <td class="py-4 px-4 text-center"><?php echo $row['total_items']; ?></td>
                    <td class="py-4 px-4 text-right">$<?php echo number_format($row['total_revenue'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
// Handle form submission
document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    window.location.href = 'index.php?page=dashboard&action=reports&' + params.toString();
});

// Export report data
function exportReport() {
    const reportType = document.getElementById('reportType').value;
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    
    // Implement export functionality
    alert('Export functionality to be implemented for ' + reportType + ' report from ' + startDate + ' to ' + endDate);
}
</script> 