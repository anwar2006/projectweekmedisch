    </main>
    
    <!-- Footer -->
    <footer class="bg-primary text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About Column -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Aphothe<span class="text-accent">Care</span></h3>
                    <p class="text-gray-200 mb-4">Your trusted online pharmacy providing high-quality medications and health products with professional pharmaceutical advice.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-white hover:text-accent transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-white hover:text-accent transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-white hover:text-accent transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-white hover:text-accent transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Customer Service -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Customer Service</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php?page=contact" class="text-gray-200 hover:text-accent transition-colors">Contact Us</a></li>
                        <li><a href="index.php?page=faq" class="text-gray-200 hover:text-accent transition-colors">FAQ</a></li>
                        <li><a href="index.php?page=shipping" class="text-gray-200 hover:text-accent transition-colors">Shipping Information</a></li>
                        <li><a href="index.php?page=returns" class="text-gray-200 hover:text-accent transition-colors">Returns & Refunds</a></li>
                        <li><a href="index.php?page=track-order" class="text-gray-200 hover:text-accent transition-colors">Track Your Order</a></li>
                    </ul>
                </div>
                
                <!-- Information -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Information</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php?page=about" class="text-gray-200 hover:text-accent transition-colors">About Us</a></li>
                        <li><a href="index.php?page=privacy" class="text-gray-200 hover:text-accent transition-colors">Privacy Policy</a></li>
                        <li><a href="index.php?page=terms" class="text-gray-200 hover:text-accent transition-colors">Terms & Conditions</a></li>
                        <li><a href="index.php?page=prescriptions-info" class="text-gray-200 hover:text-accent transition-colors">Prescription Information</a></li>
                        <li><a href="index.php?page=careers" class="text-gray-200 hover:text-accent transition-colors">Careers</a></li>
                    </ul>
                </div>
                
                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Information</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-accent"></i>
                            <span class="text-gray-200">123 Health Street, Medical District, Amsterdam, Netherlands</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-accent"></i>
                            <span class="text-gray-200">+31 20 123 4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-accent"></i>
                            <span class="text-gray-200">info@aphothecare.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-accent"></i>
                            <span class="text-gray-200">Mon-Sat: 8:00 AM - 8:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-gray-700 my-8">
            
            <!-- Bottom Footer -->
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-300 mb-4 md:mb-0">
                    &copy; <?php echo date('Y'); ?> ApotheCare. All rights reserved.
                </p>
                
                <div class="flex items-center">
                    <span class="text-sm text-gray-300 mr-4">Payment Methods:</span>
                    <div class="flex space-x-3">
                        <i class="fab fa-cc-visa text-xl text-gray-200"></i>
                        <i class="fab fa-cc-mastercard text-xl text-gray-200"></i>
                        <i class="fab fa-cc-paypal text-xl text-gray-200"></i>
                        <i class="fab fa-cc-amex text-xl text-gray-200"></i>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Close alert message
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('[role="alert"] svg');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('[role="alert"]').remove();
                });
            });
        });
    </script>
</body>
</html> 