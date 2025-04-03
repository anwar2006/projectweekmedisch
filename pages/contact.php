<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Contact Us</h1>
    
    <!-- Breadcrumbs -->
    <nav class="flex mb-6">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-500 hover:text-primary">Home</a>
            </li>
            <li class="flex items-center">
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-primary">Contact</span>
            </li>
        </ol>
    </nav>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Contact Form -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Send Us a Message</h2>
            
            <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <strong class="font-bold">Thank you!</strong>
                <span class="block sm:inline"> Your message has been sent successfully. We will get back to you as soon as possible.</span>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"> There was a problem sending your message. Please try again later.</span>
            </div>
            <?php endif; ?>
            
            <form action="actions/contact_process.php" method="post" class="space-y-4">
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Your Name*</label>
                    <input type="text" id="name" name="name" required 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="John Doe">
                </div>
                
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address*</label>
                    <input type="email" id="email" name="email" required 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="your@email.com">
                </div>
                
                <div>
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="+31 6 12345678">
                </div>
                
                <div>
                    <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Subject*</label>
                    <select id="subject" name="subject" required 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select a subject</option>
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Product Information">Product Information</option>
                        <option value="Prescription Question">Prescription Question</option>
                        <option value="Order Status">Order Status</option>
                        <option value="Technical Support">Technical Support</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message*</label>
                    <textarea id="message" name="message" rows="5" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="How can we help you?"></textarea>
                </div>
                
                <div class="flex items-start">
                    <input type="checkbox" id="privacy" name="privacy" required class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-1">
                    <label for="privacy" class="ml-2 block text-sm text-gray-700">
                        I agree to the <a href="index.php?page=privacy" class="text-accent hover:text-primary">Privacy Policy</a> and consent to the processing of my personal data.*
                    </label>
                </div>
                
                <div>
                    <button type="submit" class="bg-primary hover:bg-dark text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition-colors">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Contact Information -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Contact Information</h2>
            
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="bg-primary h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Address</h3>
                            <p class="text-gray-600">123 Health Street, Medical District<br>Amsterdam, 1012 AB<br>Netherlands</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-primary h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-phone-alt text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Phone Numbers</h3>
                            <p class="text-gray-600">
                                Customer Service: +31 20 123 4567<br>
                                Pharmacy Helpline: +31 20 123 4568<br>
                                Fax: +31 20 123 4569
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-primary h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Email Addresses</h3>
                            <p class="text-gray-600">
                                General Inquiries: info@aphothecare.com<br>
                                Customer Support: support@aphothecare.com<br>
                                Prescriptions: pharmacy@aphothecare.com
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-primary h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Business Hours</h3>
                            <p class="text-gray-600">
                                Monday - Friday: 8:00 AM - 8:00 PM<br>
                                Saturday: 9:00 AM - 6:00 PM<br>
                                Sunday: 10:00 AM - 4:00 PM
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h3 class="font-semibold text-lg mb-2">Connect With Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-blue-600 text-white h-10 w-10 rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="bg-blue-400 text-white h-10 w-10 rounded-full flex items-center justify-center hover:bg-blue-500 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="bg-pink-600 text-white h-10 w-10 rounded-full flex items-center justify-center hover:bg-pink-700 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="bg-blue-800 text-white h-10 w-10 rounded-full flex items-center justify-center hover:bg-blue-900 transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold mb-4">Location</h2>
    
    <div class="bg-gray-200 h-80 rounded-lg overflow-hidden">
        <!-- 
            Note: In a real implementation, you would embed a Google Maps iframe or another map service here.
            For example:
            <iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
        -->
        <div class="w-full h-full flex items-center justify-center bg-gray-300">
            <div class="text-center">
                <i class="fas fa-map-marked-alt text-gray-500 text-5xl mb-2"></i>
                <p class="text-gray-600">Map would be embedded here</p>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-center text-sm text-gray-500">
        <p>Easily accessible by public transportation. Nearby tram stops: Central Station, Dam Square.</p>
    </div>
</div> 