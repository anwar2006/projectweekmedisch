<?php
// Set HTTP response code to 404
http_response_code(404);
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="text-center py-12">
        <div class="mb-6">
            <i class="fas fa-exclamation-circle text-red-500 text-6xl"></i>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-800 mb-4">404 - Page Not Found</h1>
        
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php" class="bg-primary hover:bg-dark text-white font-bold py-2 px-6 rounded-full transition-colors">
                <i class="fas fa-home mr-2"></i> Go to Homepage
            </a>
            
            <a href="javascript:history.back()" class="border border-primary text-primary hover:bg-primary hover:text-white font-bold py-2 px-6 rounded-full transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Go Back
            </a>
        </div>
    </div>
    
    <!-- Suggested Pages -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">You might be interested in:</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <a href="index.php?page=medications" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg transition-colors">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <i class="fas fa-pills text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Medications</h3>
                        <p class="text-gray-500 text-sm">Browse our medications</p>
                    </div>
                </div>
            </a>
            
            <a href="index.php?page=health-products" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg transition-colors">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <i class="fas fa-heartbeat text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Health Products</h3>
                        <p class="text-gray-500 text-sm">Explore health products</p>
                    </div>
                </div>
            </a>
            
            <a href="index.php?page=contact" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg transition-colors">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Contact Us</h3>
                        <p class="text-gray-500 text-sm">Get in touch with our team</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div> 