<?php
// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['flash_message'] = "Your cart is empty. Please add some items before checkout.";
    $_SESSION['flash_type'] = "yellow";
    header('Location: index.php?page=cart');
    exit;
}

// Calculate totals
$subtotal = getCartTotal();
$shipping = 5.99; // Fixed shipping cost
$total = $subtotal + $shipping;
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Checkout</h1>
    
    <!-- Breadcrumbs -->
    <nav class="flex mb-6">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-500 hover:text-primary">Home</a>
            </li>
            <li class="flex items-center">
                <span class="mx-2 text-gray-400">/</span>
                <a href="index.php?page=cart" class="text-gray-500 hover:text-primary">Cart</a>
            </li>
            <li class="flex items-center">
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-primary">Checkout</span>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Checkout Form -->
        <div>
            <form id="checkout-form" action="actions/process_order.php" method="POST" class="space-y-6">
                <!-- Shipping Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold mb-4">Shipping Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="first_name" name="first_name" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" id="address" name="address" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" id="city" name="city" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold mb-4">Payment Method</h2>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="radio" id="paypal" name="payment_method" value="paypal" class="h-4 w-4 text-primary focus:ring-primary" checked>
                            <label for="paypal" class="ml-3 flex items-center">
                                <img src="assets/images/paypal-logo.svg" alt="PayPal" class="h-6 mr-2">
                                <span class="text-gray-700">PayPal</span>
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="ideal" name="payment_method" value="ideal" class="h-4 w-4 text-primary focus:ring-primary">
                            <label for="ideal" class="ml-3 flex items-center">
                                <img src="assets/images/ideal-logo.svg" alt="iDEAL" class="h-6 mr-2">
                                <span class="text-gray-700">iDEAL</span>
                            </label>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 italic">Note: This is a test site. No actual payment will be processed.</p>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-dark text-white font-bold py-3 px-4 rounded-lg transition-colors">
                    Place Order
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                
                <!-- Cart Items -->
                <div class="space-y-4 mb-6">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="flex items-center justify-between">
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
                                <p class="text-gray-500 text-sm">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium"><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totals -->
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium"><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium"><?php echo formatPrice($shipping); ?></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-primary"><?php echo formatPrice($total); ?></span>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 bg-green-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-lock text-green-500 mr-2"></i>
                    <p class="text-sm text-green-700">Your payment information is secure and encrypted</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation and card number formatting
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{4})/g, '$1 ').trim();
    e.target.value = value;
});

document.getElementById('expiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0,2) + '/' + value.slice(2,4);
    }
    e.target.value = value;
});

document.getElementById('cvv').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '').slice(0,3);
});
</script> 