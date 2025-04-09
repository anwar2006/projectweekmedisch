<!-- Hero Section -->
<section class="bg-gradient-to-r from-primary to-dark text-white py-12 md:py-20">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-8 md:mb-0">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Your Health, Our Priority</h1>
                <p class="text-xl mb-6">Get quality medications and healthcare products delivered to your doorstep.</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="index.php?page=medications" class="bg-accent hover:bg-secondary text-white font-bold py-3 px-6 rounded-full transition-colors text-center">
                        Browse Medications
                    </a>
                    <a href="index.php?page=prescription" class="bg-white hover:bg-gray-100 text-primary font-bold py-3 px-6 rounded-full transition-colors text-center">
                        Upload Prescription
                    </a>
                </div>
            </div>
            <div class="md:w-1/2">
                <img src="assets/images/hero-pharmacy.jpg" alt="Pharmacy Services" class="rounded-lg shadow-xl">
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-12 bg-light">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-dark">Our Services</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Service 1 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="bg-primary h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-pills text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-primary">Online Pharmacy</h3>
                <p class="text-gray-600">Order prescription and over-the-counter medications with doorstep delivery.</p>
            </div>
            
            <!-- Service 2 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="bg-primary h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-prescription text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-primary">Prescription Service</h3>
                <p class="text-gray-600">Upload your prescription and get medications delivered to your home.</p>
            </div>
            
            <!-- Service 3 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="bg-primary h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-primary">Pharmacist Consultation</h3>
                <p class="text-gray-600">Get expert advice from our qualified pharmacists online.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-dark">Featured Products</h2>
            <a href="index.php?page=medications" class="text-accent hover:text-primary transition-colors font-semibold flex items-center">
                View All <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            // Get featured products from the database
            $stmt = $pdo->query("SELECT * FROM products WHERE featured = 1 LIMIT 4");
            $featured_products = $stmt->fetchAll();
            
            // If there are no products in the database yet, display placeholders
            if (empty($featured_products)): 
                $placeholder_products = [
                    [
                        'id' => 1,
                        'name' => 'Paracetamol 500mg',
                        'price' => 5.99,
                        'image' => 'images/para.jpg',
                        'category' => 'medications'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Vitamin C Complex',
                        'price' => 12.50,
                        'image' => 'images/healthproducts.jpg',
                        'category' => 'health-products'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Digital Thermometer',
                        'price' => 15.75,
                        'image' => 'images/healthproducts.jpg',
                        'category' => 'health-products'
                    ],
                    [
                        'id' => 4,
                        'name' => 'Ibuprofen 400mg',
                        'price' => 6.99,
                        'image' => 'images/medications.jpg',
                        'category' => 'medications'
                    ]
                ];
                
                foreach ($placeholder_products as $product):
            ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                    <a href="index.php?page=product&id=<?php echo $product['id']; ?>">
                        <?php if (isset($product['image']) && !empty($product['image'])): ?>
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                        <?php endif; ?>
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2"><?php echo $product['name']; ?></h3>
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-bold"><?php echo formatPrice($product['price']); ?></span>
                            <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="text-accent hover:text-primary transition-colors">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </div>
                </div>
            <?php
                endforeach;
            else:
                foreach ($featured_products as $product):
            ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                    <a href="index.php?page=product&id=<?php echo $product['id']; ?>">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2"><?php echo $product['name']; ?></h3>
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-bold"><?php echo formatPrice($product['price']); ?></span>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="bg-accent hover:bg-primary text-white px-3 py-1 rounded-full text-sm transition-colors">
                                <i class="fas fa-cart-plus mr-1"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-dark">Shop by Category</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Category 1 -->
            <a href="index.php?page=medications" class="group">
                <div class="relative overflow-hidden rounded-lg shadow-md">
                    <div class="h-64 bg-gray-200 group-hover:opacity-90 transition-opacity">
                        <img src="images/medications.jpg" alt="Medications" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <h3 class="text-2xl font-bold text-white">Medications</h3>
                            <p class="text-gray-200 mt-2">Prescription and over-the-counter medications for your health needs</p>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- Category 2 -->
            <a href="index.php?page=health-products" class="group">
                <div class="relative overflow-hidden rounded-lg shadow-md">
                    <div class="h-64 bg-gray-200 group-hover:opacity-90 transition-opacity">
                        <img src="images/healthproducts.jpg" alt="Health Products" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <h3 class="text-2xl font-bold text-white">Health Products</h3>
                            <p class="text-gray-200 mt-2">Vitamins, supplements, and personal care products</p>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- Category 3 -->
            <a href="index.php?page=prescription" class="group">
                <div class="relative overflow-hidden rounded-lg shadow-md">
                    <div class="h-64 bg-gray-200 group-hover:opacity-90 transition-opacity">
                        <img src="images/doctor.jpg" alt="Prescription" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <h3 class="text-2xl font-bold text-white">Prescription</h3>
                            <p class="text-gray-200 mt-2">Upload your prescription and get your medications delivered</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-dark">What Our Customers Say</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-4">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Sarah Johnson</h4>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600">"The prescription service is excellent. I uploaded my prescription and received my medications the next day. Will definitely use again!"</p>
            </div>
            
            <!-- Testimonial 2 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-4">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Michael Smith</h4>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600">"Great selection of health products and the prices are very competitive. Delivery was prompt and well-packaged. Highly recommended!"</p>
            </div>
            
            <!-- Testimonial 3 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-4">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Emily Wilson</h4>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600">"The pharmacist consultation service was very helpful. I got expert advice on my medications and possible interactions. Thank you!"</p>
            </div>
        </div>
    </div>
</section>

<!-- Call To Action -->
<section class="py-12 bg-primary text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Order?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Get your medications and health products delivered right to your doorstep. Sign up now for exclusive offers and discounts.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php?page=register" class="bg-white hover:bg-gray-100 text-primary font-bold py-3 px-8 rounded-full transition-colors">
                Register Now
            </a>
            <a href="index.php?page=medications" class="bg-accent hover:bg-secondary text-white font-bold py-3 px-8 rounded-full transition-colors">
                Start Shopping
            </a>
        </div>
    </div>
</section>

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