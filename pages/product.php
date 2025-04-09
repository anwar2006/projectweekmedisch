<?php
// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Get product details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch();
    
    // Check if user has approved prescription for this product
    $has_approved_prescription = false;
    if (isset($_SESSION['user_id']) && $product && $product['requires_prescription']) {
        $prescription_stmt = $pdo->prepare("
            SELECT id 
            FROM prescriptions 
            WHERE user_id = :user_id 
            AND product_id = :product_id 
            AND status = 'approved'
        ");
        $prescription_stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'product_id' => $product_id
        ]);
        $has_approved_prescription = $prescription_stmt->fetch() !== false;
    }
    
} catch (PDOException $e) {
    $product = null;
    $has_approved_prescription = false;
    error_log("Product Error: " . $e->getMessage());
}

// If product not found, redirect to home
if (!$product) {
    $_SESSION['flash_message'] = "Product not found.";
    $_SESSION['flash_type'] = "red";
    header('Location: index.php');
    exit;
}

// Get related products from the same category
$related_products = getProductsByCategory($product['category']);
// Remove current product from related products and limit to 4
$related_products = array_filter($related_products, function($p) use ($product_id) {
    return $p['id'] != $product_id;
});
$related_products = array_slice($related_products, 0, 4);
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Breadcrumbs -->
    <nav class="flex mb-6">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-500 hover:text-primary">Home</a>
            </li>
            <li class="flex items-center">
                <span class="mx-2 text-gray-400">/</span>
                <a href="index.php?page=<?php echo $product['category']; ?>" class="text-gray-500 hover:text-primary">
                    <?php echo ucfirst(str_replace('-', ' ', $product['category'])); ?>
                </a>
            </li>
            <li class="flex items-center">
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-primary"><?php echo $product['name']; ?></span>
            </li>
        </ol>
    </nav>

    <!-- Product Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div>
            <?php if (isset($product['image']) && !empty($product['image'])): ?>
            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full rounded-lg">
            <?php else: ?>
            <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                <i class="fas fa-image text-gray-400 text-4xl"></i>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Product Details -->
        <div>
            <h1 class="text-2xl font-bold mb-4"><?php echo $product['name']; ?></h1>
            
            <div class="mb-4">
                <span class="text-2xl font-bold text-primary"><?php echo formatPrice($product['price']); ?></span>
            </div>
            
            <?php if ($product['requires_prescription']): ?>
            <div class="mb-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($has_approved_prescription): ?>
                        <div class="text-green-600 mb-2">
                            <i class="fas fa-check-circle mr-1"></i> Your prescription has been approved
                        </div>
                    <?php else: ?>
                        <div class="text-orange-500 mb-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> This medication requires a prescription
                        </div>
                        <a href="index.php?page=prescription&product_id=<?php echo $product_id; ?>" 
                           class="inline-block bg-primary text-white px-4 py-2 rounded hover:bg-dark transition-colors">
                            Upload Prescription
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-orange-500 mb-2">
                        <i class="fas fa-exclamation-circle mr-1"></i> This medication requires a prescription
                    </div>
                    <a href="index.php?page=login" 
                       class="inline-block bg-primary text-white px-4 py-2 rounded hover:bg-dark transition-colors">
                        Login to Upload Prescription
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="mb-4">
                <h2 class="font-semibold mb-2">Description</h2>
                <p class="text-gray-600"><?php echo nl2br($product['description']); ?></p>
            </div>
            
            <?php if ($product['stock_quantity'] > 0): ?>
                <?php if (!$product['requires_prescription'] || (isset($_SESSION['user_id']) && $has_approved_prescription)): ?>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center border rounded">
                        <button onclick="updateQuantity(-1)" class="px-3 py-1 hover:bg-gray-100">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" 
                            class="w-16 text-center border-x">
                        <button onclick="updateQuantity(1)" class="px-3 py-1 hover:bg-gray-100">+</button>
                    </div>
                    
                    <button onclick="addToCart(<?php echo $product_id; ?>)" 
                        class="bg-primary text-white px-6 py-2 rounded hover:bg-dark transition-colors">
                        Add to Cart
                    </button>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-red-500">
                    <i class="fas fa-times-circle mr-1"></i> Out of stock
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">You might also like</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($related_products as $related): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                <a href="index.php?page=product&id=<?php echo $related['id']; ?>">
                    <?php if (isset($related['image']) && !empty($related['image'])): ?>
                    <img src="<?php echo $related['image']; ?>" alt="<?php echo $related['name']; ?>" class="w-full h-48 object-cover">
                    <?php else: ?>
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas <?php echo $related['category'] === 'medications' ? 'fa-pills' : 'fa-heartbeat'; ?> text-gray-400 text-4xl"></i>
                    </div>
                    <?php endif; ?>
                </a>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2">
                        <a href="index.php?page=product&id=<?php echo $related['id']; ?>" class="text-gray-800 hover:text-primary transition-colors">
                            <?php echo $related['name']; ?>
                        </a>
                    </h3>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-primary font-bold"><?php echo formatPrice($related['price']); ?></span>
                        
                        <?php if ($related['stock_quantity'] > 0): ?>
                            <?php if ($related['requires_prescription']): ?>
                            <a href="index.php?page=prescription&product_id=<?php echo $related['id']; ?>" class="bg-primary hover:bg-dark text-white px-3 py-1 rounded-full text-sm transition-colors">
                                <i class="fas fa-prescription mr-1"></i> Request
                            </a>
                            <?php else: ?>
                            <button onclick="addToCart(<?php echo $related['id']; ?>)" class="bg-accent hover:bg-primary text-white px-3 py-1 rounded-full text-sm transition-colors">
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
    </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(change) {
    const input = document.getElementById('quantity');
    const newValue = parseInt(input.value) + change;
    const max = parseInt(input.getAttribute('max'));
    
    if (newValue >= 1 && newValue <= max) {
        input.value = newValue;
    }
}

function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    
    fetch('actions/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart!');
            location.reload();
        } else {
            alert(data.message || 'Error adding product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script> 