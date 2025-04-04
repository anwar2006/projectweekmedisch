<?php
// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$product = getProductById($product_id);

// If product not found, redirect to 404 page
if (!$product) {
    header('Location: index.php?page=404');
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
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Product Image -->
        <div class="w-full md:w-1/2">
            <?php if (isset($product['image']) && !empty($product['image'])): ?>
            <div class="rounded-lg overflow-hidden shadow-md">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-auto">
            </div>
            <?php else: ?>
            <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                <i class="fas <?php echo $product['category'] === 'medications' ? 'fa-pills' : 'fa-heartbeat'; ?> text-gray-400 text-6xl"></i>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="w-full md:w-1/2">
            <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo $product['name']; ?></h1>
            
            <div class="text-2xl font-bold text-primary mb-6">
                <?php echo formatPrice($product['price']); ?>
            </div>

            <?php if ($product['requires_prescription']): ?>
            <div class="flex items-center text-orange-500 text-sm mb-4">
                <i class="fas fa-file-prescription mr-2"></i> 
                This medication requires a prescription
            </div>
            <?php endif; ?>

            <div class="prose max-w-none mb-6">
                <p class="text-gray-600"><?php echo $product['description']; ?></p>
            </div>

            <?php if ($product['stock_quantity'] > 0): ?>
            <div class="mb-6">
                <span class="text-green-500">
                    <i class="fas fa-check-circle mr-2"></i> In Stock
                </span>
                <span class="text-gray-500 text-sm ml-2">(<?php echo $product['stock_quantity']; ?> available)</span>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-24">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $product['stock_quantity']; ?>" value="1"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <?php if ($product['requires_prescription']): ?>
                <a href="index.php?page=prescription&product_id=<?php echo $product['id']; ?>" 
                    class="flex-grow bg-primary hover:bg-dark text-white px-6 py-3 rounded-lg text-center transition-colors">
                    <i class="fas fa-prescription mr-2"></i> Request Prescription
                </a>
                <?php else: ?>
                <button onclick="addToCartWithQuantity(<?php echo $product['id']; ?>)" 
                    class="flex-grow bg-accent hover:bg-primary text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                </button>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="mb-6">
                <span class="text-red-500">
                    <i class="fas fa-times-circle mr-2"></i> Out of Stock
                </span>
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
function addToCartWithQuantity(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    if (isNaN(quantity) || quantity < 1) {
        alert('Please enter a valid quantity');
        return;
    }

    // Using the Fetch API to add product to cart
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

function addToCart(productId) {
    addToCartWithQuantity(productId);
}
</script> 