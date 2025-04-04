<?php
// Get all customers
try {
    $stmt = $pdo->prepare("
        SELECT u.*, 
               COUNT(DISTINCT o.id) as total_orders,
               SUM(o.total_amount) as total_spent,
               MAX(o.created_at) as last_order_date
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id
        WHERE u.role = 'customer'
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    $customers = [];
    error_log("Error fetching customers: " . $e->getMessage());
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Customer Management</h2>
        
        <div class="flex space-x-4">
            <div class="relative">
                <input type="text" id="searchCustomer" placeholder="Search customers..." 
                    class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <button onclick="exportCustomers()" class="bg-primary hover:bg-dark text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-download mr-2"></i> Export
            </button>
        </div>
    </div>
    
    <?php if (empty($customers)): ?>
    <div class="text-center py-8">
        <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500">No customers found</p>
    </div>
    <?php else: ?>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Customer</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Contact</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Orders</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Total Spent</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Last Order</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-4 px-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <div>
                                <div class="font-medium"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></div>
                                <div class="text-sm text-gray-500">ID: #<?php echo $customer['id']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="text-sm">
                            <div><?php echo $customer['email']; ?></div>
                            <?php if ($customer['phone']): ?>
                            <div class="text-gray-500"><?php echo $customer['phone']; ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-sm">
                            <?php echo $customer['total_orders']; ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <span class="font-medium"><?php echo formatPrice($customer['total_spent'] ?? 0); ?></span>
                    </td>
                    <td class="py-4 px-4">
                        <?php if ($customer['last_order_date']): ?>
                        <div class="text-sm">
                            <?php echo date('M d, Y', strtotime($customer['last_order_date'])); ?>
                        </div>
                        <?php else: ?>
                        <span class="text-gray-500 text-sm">No orders yet</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex justify-center space-x-2">
                            <button onclick="viewCustomer(<?php echo $customer['id']; ?>)" 
                                class="text-blue-500 hover:text-blue-700 transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editCustomer(<?php echo $customer['id']; ?>)" 
                                class="text-green-500 hover:text-green-700 transition-colors" title="Edit Customer">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteCustomer(<?php echo $customer['id']; ?>)" 
                                class="text-red-500 hover:text-red-700 transition-colors" title="Delete Customer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
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
function viewCustomer(customerId) {
    // Implement view customer details
    alert('View customer details functionality to be implemented');
}

function editCustomer(customerId) {
    // Implement edit customer
    alert('Edit customer functionality to be implemented');
}

function deleteCustomer(customerId) {
    if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
        // Implement delete customer
        alert('Delete customer functionality to be implemented');
    }
}

function exportCustomers() {
    // Implement export customers to CSV/Excel
    alert('Export customers functionality to be implemented');
}

// Search functionality
document.getElementById('searchCustomer').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script> 