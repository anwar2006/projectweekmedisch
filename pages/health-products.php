<?php
// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Prepare SQL query
$sql = "SELECT * FROM products WHERE category = 'health-products'";

// Add filters
if (!empty($search)) {
    $sql .= " AND (name LIKE :search OR description LIKE :search)";
}

if (!empty($category)) {
    $sql .= " AND category_id = :category";
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY name DESC";
        break;
    case 'newest':
        $sql .= " ORDER BY created_at DESC";
        break;
    default:
        $sql .= " ORDER BY name ASC";
}

try {
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    if (!empty($search)) {
        $search_param = '%' . $search . '%';
        $stmt->bindParam(':search', $search_param);
    }
    
    if (!empty($category)) {
        $stmt->bindParam(':category', $category);
    }
    
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    // Get categories for filter
    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $cat_stmt->fetchAll();
    
} catch (PDOException $e) {
    // If tables don't exist yet or there's an error, use sample data
    $products = [];
    $categories = [];
    
    // Log error
    error_log("Health Products Error: " . $e->getMessage());
}

// Sample health products if database doesn't have any yet
if (empty($products)) {
    $products = [
        [
            'id' => 7,
            'name' => 'Vitamin C 1000mg',
            'price' => 12.50,
            'description' => 'Supports immune system health. 60 tablets.',
            'image' => 'images/vitamin-c.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 65
        ],
        [
            'id' => 8,
            'name' => 'Multivitamin Daily',
            'price' => 16.95,
            'description' => 'Complete daily multivitamin. 90 tablets.',
            'image' => 'images/multivitamin.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 55
        ],
        [
            'id' => 9,
            'name' => 'Omega-3 Fish Oil',
            'price' => 14.25,
            'description' => 'Supports heart and brain health. 60 capsules.',
            'image' => 'images/omega-3.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 48
        ],
        [
            'id' => 10,
            'name' => 'Digital Thermometer',
            'price' => 15.75,
            'description' => 'Accurate digital thermometer for body temperature readings.',
            'image' => 'images/thermometer.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 35
        ],
        [
            'id' => 11,
            'name' => 'Blood Pressure Monitor',
            'price' => 49.99,
            'description' => 'Digital blood pressure monitor for home use.',
            'image' => 'images/bp-monitor.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 18
        ],
        [
            'id' => 12,
            'name' => 'First Aid Kit',
            'price' => 24.99,
            'description' => 'Comprehensive first aid kit for home use.',
            'image' => 'images/first-aid.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 25
        ]
    ];
}

// Sample categories if database doesn't have any yet
if (empty($categories)) {
    $categories = [
        ['id' => 6, 'name' => 'Vitamins & Supplements'],
        ['id' => 7, 'name' => 'Personal Care'],
        ['id' => 8, 'name' => 'Health Devices'],
        ['id' => 9, 'name' => 'First Aid'],
        ['id' => 10, 'name' => 'Nutrition']
    ];
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Health Products</h1>
    
    <!-- Breadcrumbs -->
    <nav class="flex mb-6">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-500 hover:text-primary">Home</a>
            </li>
            <li class="flex items-center">
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-primary">Health Products</span>
            </li>
        </ol>
    </nav>
    
    <!-- Filters and Products Container -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar Filters -->
        <div class="w-full md:w-64 flex-shrink-0">
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-lg mb-3">Search</h3>
                <form action="index.php" method="get" class="mb-4">
                    <input type="hidden" name="page" value="health-products">
                    <div class="flex">
                        <input type="text" name="search" placeholder="Search products..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="flex-grow px-3 py-2 border rounded-l focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <button type="submit" class="bg-primary text-white px-3 py-2 rounded-r hover:bg-dark transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <h3 class="font-semibold text-lg mb-3">Sort By</h3>
                <form id="sort-form" action="index.php" method="get" class="mb-4">
                    <input type="hidden" name="page" value="health-products">
                    <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>
                    <?php if (!empty($category)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    <?php endif; ?>
                    
                    <select name="sort" id="sort" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                        onchange="document.getElementById('sort-form').submit()">
                        <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    </select>
                </form>
                
                <h3 class="font-semibold text-lg mb-3">Categories</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="index.php?page=health-products<?php echo (!empty($search) ? '&search='.urlencode($search) : '') . '&sort=' . $sort; ?>" 
                            class="<?php echo empty($category) ? 'text-primary font-medium' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                            All Categories
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="index.php?page=health-products&category=<?php echo $cat['id']; ?><?php echo (!empty($search) ? '&search='.urlencode($search) : '') . '&sort=' . $sort; ?>" 
                            class="<?php echo $category == $cat['id'] ? 'text-primary font-medium' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                            <?php echo $cat['name']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Health Tips -->
            <div class="bg-primary text-white rounded-lg p-4">
                <h3 class="font-semibold text-lg mb-3">Health Tips</h3>
                <div class="space-y-3 text-sm">
                    <p>Supplements should complement a balanced diet, not replace it.</p>
                    <p>Always read the label for dosage instructions and ingredient information.</p>
                    <p>Consult with a healthcare professional before starting any new supplement regimen.</p>
                </div>
                <div class="mt-4">
                    <a href="index.php?page=contact" class="text-white underline hover:text-light">
                        Need advice? Contact our healthcare team
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="flex-grow">
            <?php if (empty($products)): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            No products found. Please try a different search or filter.
                        </p>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                    <a href="index.php?page=product&id=<?php echo $product['id']; ?>">
                        <?php if (isset($product['image']) && !empty($product['image'])): ?>
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-heartbeat text-gray-400 text-4xl"></i>
                        </div>
                        <?php endif; ?>
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">
                            <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="text-gray-800 hover:text-primary transition-colors">
                                <?php echo $product['name']; ?>
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                            <?php echo substr($product['description'], 0, 80) . (strlen($product['description']) > 80 ? '...' : ''); ?>
                        </p>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-bold"><?php echo formatPrice($product['price']); ?></span>
                            
                            <?php if ($product['stock_quantity'] > 0): ?>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="bg-accent hover:bg-primary text-white px-3 py-1 rounded-full text-sm transition-colors">
                                <i class="fas fa-cart-plus mr-1"></i> Add
                            </button>
                            <?php else: ?>
                            <span class="text-red-500 text-sm">Out of stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function addToCart(productId) {
    // Using the Fetch API to add product to cart
    fetch('actions/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Product added to cart!');
            // Reload the page to update cart count
            location.reload();
        } else {
            // Show error message
            alert(data.message || 'Error adding product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script> 