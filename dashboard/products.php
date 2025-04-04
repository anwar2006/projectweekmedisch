<?php
// Get all products
try {
    $stmt = $pdo->prepare("
        SELECT p.*, 
               c.name as category_name,
               COUNT(DISTINCT oi.id) as total_orders,
               SUM(oi.quantity) as total_sold
        FROM products p
        LEFT JOIN categories c ON p.category = c.slug
        LEFT JOIN order_items oi ON p.id = oi.product_id
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    error_log("Error fetching products: " . $e->getMessage());
}

// Get all categories for the filter
try {
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
    error_log("Error fetching categories: " . $e->getMessage());
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Product Management</h2>
        
        <div class="flex space-x-4">
            <div class="relative">
                <input type="text" id="searchProduct" placeholder="Search products..." 
                    class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <select id="categoryFilter" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['slug']; ?>"><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <button onclick="addProduct()" class="bg-primary hover:bg-dark text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i> Add Product
            </button>
        </div>
    </div>
    
    <?php if (empty($products)): ?>
    <div class="text-center py-8">
        <i class="fas fa-box text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500">No products found</p>
    </div>
    <?php else: ?>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Product</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Category</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Price</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Stock</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Orders</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Status</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr class="border-t hover:bg-gray-50" data-category="<?php echo $product['category']; ?>">
                    <td class="py-4 px-4">
                        <div class="flex items-center">
                            <div class="h-16 w-16 bg-gray-100 rounded overflow-hidden mr-3 flex-shrink-0">
                                <?php if (isset($product['image']) && !empty($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="h-full w-full object-cover">
                                <?php else: ?>
                                <div class="h-full w-full flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="font-medium"><?php echo $product['name']; ?></div>
                                <div class="text-sm text-gray-500">SKU: <?php echo $product['sku']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <span class="text-sm"><?php echo $product['category_name']; ?></span>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <div class="font-medium"><?php echo formatPrice($product['price']); ?></div>
                        <?php if ($product['sale_price']): ?>
                        <div class="text-sm text-red-500">Sale: <?php echo formatPrice($product['sale_price']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-2 py-1 rounded-full text-sm <?php echo $product['stock_quantity'] > $product['reorder_level'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo $product['stock_quantity']; ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <div class="text-sm">
                            <div><?php echo $product['total_orders']; ?> orders</div>
                            <div class="text-gray-500"><?php echo $product['total_sold']; ?> units sold</div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-2 py-1 rounded-full text-sm 
                            <?php 
                            switch ($product['status']) {
                                case 'active':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'inactive':
                                    echo 'bg-gray-100 text-gray-800';
                                    break;
                                case 'discontinued':
                                    echo 'bg-red-100 text-red-800';
                                    break;
                            }
                            ?>">
                            <?php echo ucfirst($product['status']); ?>
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex justify-center space-x-2">
                            <button onclick="viewProduct(<?php echo $product['id']; ?>)" 
                                class="text-blue-500 hover:text-blue-700 transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editProduct(<?php echo $product['id']; ?>)" 
                                class="text-green-500 hover:text-green-700 transition-colors" title="Edit Product">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteProduct(<?php echo $product['id']; ?>)" 
                                class="text-red-500 hover:text-red-700 transition-colors" title="Delete Product">
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
function viewProduct(productId) {
    // Implement view product details
    alert('View product details functionality to be implemented');
}

function editProduct(productId) {
    // Implement edit product
    alert('Edit product functionality to be implemented');
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        // Implement delete product
        alert('Delete product functionality to be implemented');
    }
}

function addProduct() {
    // Implement add new product
    alert('Add new product functionality to be implemented');
}

// Search functionality
document.getElementById('searchProduct').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Category filter
document.getElementById('categoryFilter').addEventListener('change', function(e) {
    const category = e.target.value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!category || row.dataset.category === category) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script> 