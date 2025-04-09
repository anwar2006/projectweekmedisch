<?php
// Get cart items from database
try {
    $stmt = $pdo->prepare("
        SELECT ci.*, p.name, p.price, p.image 
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Cart Error: " . $e->getMessage());
    $cart_items = [];
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
    <div class="text-center py-8">
        <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500 mb-4">Your cart is empty</p>
        <a href="index.php?page=medications" class="inline-block bg-primary hover:bg-dark text-white font-bold py-2 px-6 rounded-full transition-colors">
            Continue Shopping
        </a>
    </div>
    <?php else: ?>
    
    <!-- Cart Items -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4 text-left">Product</th>
                    <th class="py-2 px-4 text-center">Quantity</th>
                    <th class="py-2 px-4 text-right">Price</th>
                    <th class="py-2 px-4 text-right">Subtotal</th>
                    <th class="py-2 px-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-4 px-4">
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
                                <p class="text-gray-500 text-sm">Item #<?php echo $item['product_id']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center">
                            <button onclick="updateCartItem(<?php echo $item['product_id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                                class="bg-gray-200 hover:bg-gray-300 rounded-l px-2 py-1 transition-colors">
                                <i class="fas fa-minus text-gray-600"></i>
                            </button>
                            <input type="number" value="<?php echo $item['quantity']; ?>" min="1" 
                                class="w-12 text-center border-t border-b border-gray-200 py-1"
                                onchange="updateCartItem(<?php echo $item['product_id']; ?>, this.value)">
                            <button onclick="updateCartItem(<?php echo $item['product_id']; ?>, <?php echo $item['quantity'] + 1; ?>)" 
                                class="bg-gray-200 hover:bg-gray-300 rounded-r px-2 py-1 transition-colors">
                                <i class="fas fa-plus text-gray-600"></i>
                            </button>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-right"><?php echo formatPrice($item['price']); ?></td>
                    <td class="py-4 px-4 text-right font-medium"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                    <td class="py-4 px-4 text-center">
                        <button onclick="removeCartItem(<?php echo $item['product_id']; ?>)" 
                            class="text-red-500 hover:text-red-700 transition-colors">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Cart Summary -->
    <div class="mt-8 flex flex-col md:flex-row justify-between items-start">
        <div class="w-full md:w-1/2 mb-6 md:mb-0">
            <div class="bg-gray-50 p-4 rounded">
                <h3 class="text-lg font-semibold mb-3">Order Notes</h3>
                <textarea class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" 
                    rows="3" placeholder="Special instructions for your order"></textarea>
            </div>
        </div>
        
        <div class="w-full md:w-1/2 md:pl-8">
            <div class="bg-gray-50 p-4 rounded">
                <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                
                <?php
                $subtotal = 0;
                foreach ($cart_items as $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                }
                ?>
                
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium"><?php echo formatPrice($subtotal); ?></span>
                </div>
                
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Shipping</span>
                    <span class="font-medium"><?php echo formatPrice($subtotal >= 50 ? 0 : 4.99); ?></span>
                </div>
                
                <?php if ($subtotal >= 50): ?>
                <div class="flex justify-between mb-2 text-green-600">
                    <span>Free Shipping Discount</span>
                    <span>-<?php echo formatPrice(4.99); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="border-t border-gray-200 my-2 pt-2"></div>
                
                <div class="flex justify-between mb-4">
                    <span class="text-lg font-bold">Total</span>
                    <span class="text-lg font-bold text-primary">
                        <?php echo formatPrice($subtotal + ($subtotal >= 50 ? 0 : 4.99)); ?>
                    </span>
                </div>
                
                <div class="flex flex-col space-y-2">
                    <a href="index.php?page=checkout" 
                        class="bg-primary hover:bg-dark text-center text-white font-bold py-2 px-4 rounded transition-colors">
                        Proceed to Checkout
                    </a>
                    <a href="index.php?page=medications" 
                        class="border border-primary text-center text-primary hover:bg-primary hover:text-white font-bold py-2 px-4 rounded transition-colors">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function updateCartItem(productId, quantity) {
    if (quantity <= 0) {
        removeCartItem(productId);
        return;
    }
    
    fetch('actions/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated cart
            location.reload();
        } else {
            // Show error message
            alert(data.message || 'Error updating cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function removeCartItem(productId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch('actions/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated cart
                location.reload();
            } else {
                // Show error message
                alert(data.message || 'Error removing item from cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}
</script> 