<?php
// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$requires_prescription = isset($_GET['prescription']) ? (int)$_GET['prescription'] : -1;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Prepare SQL query
$sql = "SELECT * FROM products WHERE category = 'medications'";

// Add filters
if (!empty($search)) {
    $sql .= " AND (name LIKE :search OR description LIKE :search)";
}

if (!empty($category)) {
    $sql .= " AND category_id = :category";
}

if ($requires_prescription >= 0) {
    $sql .= " AND requires_prescription = :requires_prescription";
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
    
    if ($requires_prescription >= 0) {
        $stmt->bindParam(':requires_prescription', $requires_prescription);
    }
    
    $stmt->execute();
    $medications = $stmt->fetchAll();
    
    // Get categories for filter
    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $cat_stmt->fetchAll();
    
} catch (PDOException $e) {
    // If tables don't exist yet or there's an error, use sample data
    $medications = [];
    $categories = [];
    
    // Log error
    error_log("Medications Error: " . $e->getMessage());
}

// Sample medications if database doesn't have any yet
if (empty($medications)) {
    $medications = [
        [
            'id' => 1,
            'name' => 'Paracetamol 500mg',
            'price' => 5.99,
            'description' => 'Pain relief tablets for headaches, pain and fever. Pack of 16 tablets.',
            'image' => 'assets/images/products/paracetamol.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 50
        ],
        [
            'id' => 2,
            'name' => 'Ibuprofen 400mg',
            'price' => 6.99,
            'description' => 'Anti-inflammatory pain relief. Pack of 24 tablets.',
            'image' => 'assets/images/products/ibuprofen.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 45
        ],
        [
            'id' => 3,
            'name' => 'Amoxicillin 250mg',
            'price' => 12.99,
            'description' => 'Antibiotic medication. Pack of 21 capsules.',
            'image' => 'assets/images/products/amoxicillin.jpg',
            'requires_prescription' => 1,
            'stock_quantity' => 30
        ],
        [
            'id' => 4,
            'name' => 'Cetirizine 10mg',
            'price' => 7.50,
            'description' => 'Antihistamine for allergy relief. Pack of 30 tablets.',
            'image' => 'assets/images/products/cetirizine.jpg',
            'requires_prescription' => 0,
            'stock_quantity' => 40
        ],
        [
            'id' => 5,
            'name' => 'Fluoxetine 20mg',
            'price' => 14.99,
            'description' => 'Antidepressant medication. Pack of 28 capsules.',
            'image' => 'assets/images/products/fluoxetine.jpg',
            'requires_prescription' => 1,
            'stock_quantity' => 25
        ],
        [
            'id' => 6,
            'name' => 'Salbutamol Inhaler',
            'price' => 18.75,
            'description' => 'Relieves asthma symptoms. 200 doses.',
            'image' => 'assets/images/products/salbutamol.jpg',
            'requires_prescription' => 1,
            'stock_quantity' => 20
        ]
    ];
}

// Sample categories if database doesn't have any yet
if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'Pain Relief'],
        ['id' => 2, 'name' => 'Antibiotics'],
        ['id' => 3, 'name' => 'Allergy Relief'],
        ['id' => 4, 'name' => 'Mental Health'],
        ['id' => 5, 'name' => 'Respiratory']
    ];
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Medications</h1>
    
    <!-- Breadcrumbs -->
    <nav class="flex mb-6">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-500 hover:text-primary">Home</a>
            </li>
            <li class="flex items-center">
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-primary">Medications</span>
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
                    <input type="hidden" name="page" value="medications">
                    <div class="flex">
                        <input type="text" name="search" placeholder="Search medications..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="flex-grow px-3 py-2 border rounded-l focus:outline-none focus:ring-2 focus:ring-primary">
                        <button type="submit" class="bg-primary text-white px-3 py-2 rounded-r hover:bg-dark transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <h3 class="font-semibold text-lg mb-3">Sort By</h3>
                <form id="sort-form" action="index.php" method="get" class="mb-4">
                    <input type="hidden" name="page" value="medications">
                    <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>
                    <?php if (!empty($category)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    <?php endif; ?>
                    <?php if ($requires_prescription >= 0): ?>
                    <input type="hidden" name="prescription" value="<?php echo $requires_prescription; ?>">
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
                <ul class="space-y-2 mb-4">
                    <li>
                        <a href="index.php?page=medications<?php echo (!empty($search) ? '&search='.urlencode($search) : '') . ($requires_prescription >= 0 ? '&prescription='.$requires_prescription : '') . '&sort=' . $sort; ?>" 
                            class="<?php echo empty($category) ? 'text-primary font-medium' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                            All Categories
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="index.php?page=medications&category=<?php echo $cat['id']; ?><?php echo (!empty($search) ? '&search='.urlencode($search) : '') . ($requires_prescription >= 0 ? '&prescription='.$requires_prescription : '') . '&sort=' . $sort; ?>" 
                            class="<?php echo $category == $cat['id'] ? 'text-primary font-medium' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                            <?php echo $cat['name']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <h3 class="font-semibold text-lg mb-3">Prescription</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="index.php?page=medications<?php echo (!empty($search) ? '&search='.urlencode($search) : '') . (!empty($category) ? '&category='.$category : '') . '&sort=' . $sort; ?>" 
                            class="<?php echo $requires_prescription < 0 ? 'text-primary font-medium' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                            All Medications
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=medications&prescription=0<?php echo (!empty($search) ? '&search='.urlencode($search) : '') . (!empty($category) ? '&category='.$category : '') . '&sort=' . $sort; ?>" 
                            class="<?php echo $requires_prescription === 0 ? 'text-primary font-medium' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                            Over the Counter
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=medications&prescription=1<?php echo (!empty($search) ? '&search='.urlencode($search) : '') . (!empty($category) ? '&category='.$category : '') . '&sort=' . $sort; ?>" 
                            class="<?php echo $requires_prescription === 1 ? 'text-primary font-medium' : 'text-gray-600 hover:text-primary'; ?> transition-colors">
                            Prescription Required
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Customer Support -->
            <div class="bg-primary text-white rounded-lg p-4">
                <h3 class="font-semibold text-lg mb-3">Need Help?</h3>
                <p class="text-sm mb-3">Our pharmacists are available to answer your questions about medications.</p>
                <div class="flex items-center mb-3">
                    <i class="fas fa-phone-alt mr-2"></i>
                    <span>+31 20 123 4567</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-envelope mr-2"></i>
                    <span>pharmacy@aphothecare.com</span>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="flex-grow">
            <?php if (empty($medications)): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            No medications found. Please try a different search or filter.
                        </p>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($medications as $medication): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                    <a href="index.php?page=product&id=<?php echo $medication['id']; ?>">
                        <?php if (isset($medication['image']) && !empty($medication['image'])): ?>
                        <img src="<?php echo $medication['image']; ?>" alt="<?php echo $medication['name']; ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-pills text-gray-400 text-4xl"></i>
                        </div>
                        <?php endif; ?>
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">
                            <a href="index.php?page=product&id=<?php echo $medication['id']; ?>" class="text-gray-800 hover:text-primary transition-colors">
                                <?php echo $medication['name']; ?>
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                            <?php echo substr($medication['description'], 0, 80) . (strlen($medication['description']) > 80 ? '...' : ''); ?>
                        </p>
                        
                        <?php if ($medication['requires_prescription']): ?>
                        <div class="flex items-center text-orange-500 text-sm mb-3">
                            <i class="fas fa-file-prescription mr-1"></i> Prescription required
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-bold"><?php echo formatPrice($medication['price']); ?></span>
                            
                            <?php if ($medication['stock_quantity'] > 0): ?>
                                <?php if ($medication['requires_prescription']): ?>
                                <a href="index.php?page=prescription&product_id=<?php echo $medication['id']; ?>" class="bg-primary hover:bg-dark text-white px-3 py-1 rounded-full text-sm transition-colors">
                                    <i class="fas fa-prescription mr-1"></i> Request
                                </a>
                                <?php else: ?>
                                <button onclick="addToCart(<?php echo $medication['id']; ?>)" class="bg-accent hover:bg-primary text-white px-3 py-1 rounded-full text-sm transition-colors">
                                    <i class="fas fa-cart-plus mr-1"></i> Add
                                </button>
                                <?php endif; ?>
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
            if (data.redirect) {
                if (data.redirect === 'login') {
                    window.location.href = 'index.php?page=login';
                } else if (data.redirect === 'prescription') {
                    window.location.href = 'index.php?page=prescription&product_id=' + productId;
                }
            } else {
                // Show error message
                alert(data.message || 'Error adding product to cart');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script> 