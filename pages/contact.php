<?php
// Contact information
$address = "Groen van Prinsterersingel 52, 2805 TE Gouda";
$phone = "+31 (0)182 123 456";
$email = "info@aphothecare.com";
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Contact Us</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Contact Information -->
        <div>
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold mb-2">Address</h2>
                    <p class="text-gray-600">
                        <?php echo $address; ?>
                    </p>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold mb-2">Phone</h2>
                    <p class="text-gray-600">
                        <a href="tel:<?php echo $phone; ?>" class="hover:text-primary">
                            <?php echo $phone; ?>
                        </a>
                    </p>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold mb-2">Email</h2>
                    <p class="text-gray-600">
                        <a href="mailto:<?php echo $email; ?>" class="hover:text-primary">
                            <?php echo $email; ?>
                        </a>
                    </p>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold mb-2">Opening Hours</h2>
                    <div class="space-y-1 text-gray-600">
                        <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                        <p>Saturday: 9:00 AM - 5:00 PM</p>
                        <p>Sunday: Closed</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map -->
        <div class="h-96">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2454.827686715!2d4.7087!3d52.0167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5c9299519655f%3A0x7e8c6b3b3b3b3b3b!2sGroen%20van%20Prinsterersingel%2052%2C%202805%20TE%20Gouda!5e0!3m2!1sen!2snl!4v1620000000000!5m2!1sen!2snl"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
    
    <!-- Contact Form -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold mb-4">Send us a Message</h2>
        <form action="actions/process_contact.php" method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>
            </div>
            
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" id="subject" name="subject" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
            </div>
            
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea id="message" name="message" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"></textarea>
            </div>
            
            <button type="submit" class="bg-primary hover:bg-dark text-white font-bold py-2 px-4 rounded-lg transition-colors">
                Send Message
            </button>
        </form>
    </div>
</div> 