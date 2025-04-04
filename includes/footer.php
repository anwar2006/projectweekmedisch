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

    <!-- Chatbot Widget -->
    <div id="chatbot-widget" class="fixed bottom-6 right-6 z-50">
        <!-- Chat Button -->
        <button id="chat-button" class="bg-primary hover:bg-dark text-white rounded-full p-4 shadow-lg flex items-center justify-center transition-all duration-300">
            <i id="chat-icon-open" class="fas fa-comment-dots text-xl"></i>
            <i id="chat-icon-close" class="fas fa-times text-xl hidden"></i>
        </button>
        
        <!-- Chat Window -->
        <div id="chat-window" class="hidden bg-white rounded-lg shadow-xl w-80 md:w-96 absolute bottom-20 right-0 overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-primary text-white p-4 flex justify-between items-center">
                <div class="flex items-center">
                    <div class="mr-3 bg-white rounded-full p-1 profile-picture-container">
                        <img src="assets/images/logo-small.png" alt="AphotheCare Bot" class="w-8 h-8">
                    </div>
                    <div>
                        <h3 class="font-bold">AphotheCare Assistant</h3>
                        <p class="text-xs opacity-75">Online</p>
                    </div>
                </div>
                <button id="chat-minimize" class="text-white hover:text-gray-200">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            
            <!-- Chat Messages -->
            <div id="chat-messages" class="p-4 h-80 overflow-y-auto">
                <!-- Messages will be added here dynamically -->
                <div class="chat-message bot">
                    <div class="flex items-start mb-4">
                        <div class="bg-primary text-white rounded-full p-1 mr-2">
                            <i class="fas fa-robot text-xs"></i>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-lg rounded-tl-none max-w-[80%]">
                            <p>Hello! Welcome to AphotheCare. How can I help you today?</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Replies -->
            <div id="quick-replies" class="px-4 pb-2 flex flex-wrap gap-2">
                <button class="quick-reply-btn text-xs bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 transition-colors">Prescription help</button>
                <button class="quick-reply-btn text-xs bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 transition-colors">Find medications</button>
                <button class="quick-reply-btn text-xs bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 transition-colors">Order status</button>
                <button class="quick-reply-btn text-xs bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 transition-colors">Contact support</button>
            </div>
            
            <!-- Chat Input -->
            <div class="border-t p-4">
                <form id="chat-form" class="flex">
                    <input type="text" id="chat-input" placeholder="Type your message..." 
                        class="flex-1 border rounded-l-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary">
                    <button type="submit" class="bg-primary hover:bg-dark text-white rounded-r-lg px-4 transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Chatbot Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatButton = document.getElementById('chat-button');
        const chatWindow = document.getElementById('chat-window');
        const chatIconOpen = document.getElementById('chat-icon-open');
        const chatIconClose = document.getElementById('chat-icon-close');
        const chatMinimize = document.getElementById('chat-minimize');
        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const quickReplyButtons = document.querySelectorAll('.quick-reply-btn');
        
        // Toggle chat window
        chatButton.addEventListener('click', function() {
            chatWindow.classList.toggle('hidden');
            chatIconOpen.classList.toggle('hidden');
            chatIconClose.classList.toggle('hidden');
            if (!chatWindow.classList.contains('hidden')) {
                chatInput.focus();
            }
        });
        
        // Minimize chat window
        chatMinimize.addEventListener('click', function() {
            chatWindow.classList.add('hidden');
            chatIconOpen.classList.remove('hidden');
            chatIconClose.classList.add('hidden');
        });
        
        // Handle chat form submission
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (message) {
                addUserMessage(message);
                chatInput.value = '';
                processUserMessage(message);
            }
        });
        
        // Handle quick reply buttons
        quickReplyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const message = this.textContent;
                addUserMessage(message);
                processUserMessage(message);
            });
        });
        
        // Add user message to chat
        function addUserMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.className = 'chat-message user';
            messageElement.innerHTML = `
                <div class="flex items-start justify-end mb-4">
                    <div class="bg-primary text-white p-3 rounded-lg rounded-tr-none max-w-[80%]">
                        <p>${message}</p>
                    </div>
                    <div class="bg-gray-200 rounded-full p-1 ml-2">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                </div>
            `;
            chatMessages.appendChild(messageElement);
            scrollToBottom();
        }
        
        // Add bot message to chat
        function addBotMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.className = 'chat-message bot';
            messageElement.innerHTML = `
                <div class="flex items-start mb-4">
                    <div class="bg-primary text-white rounded-full p-1 mr-2">
                        <i class="fas fa-robot text-xs"></i>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg rounded-tl-none max-w-[80%]">
                        <p>${message}</p>
                    </div>
                </div>
            `;
            chatMessages.appendChild(messageElement);
            scrollToBottom();
        }
        
        // Process user message and generate response
        function processUserMessage(message) {
            // Show typing indicator
            showTypingIndicator();
            
            // Try to use the API endpoint
            fetch('api/chatbot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                removeTypingIndicator();
                if (data.success) {
                    addBotMessage(data.response);
                } else {
                    // Fallback to client-side responses if API fails
                    const response = generateFallbackResponse(message.toLowerCase());
                    addBotMessage(response);
                }
            })
            .catch(error => {
                removeTypingIndicator();
                console.error('Error:', error);
                // Fallback to client-side responses if API fails
                const response = generateFallbackResponse(message.toLowerCase());
                addBotMessage(response);
            });
        }
        
        // Show bot typing indicator
        function showTypingIndicator() {
            const typingElement = document.createElement('div');
            typingElement.id = 'typing-indicator';
            typingElement.className = 'chat-message bot';
            typingElement.innerHTML = `
                <div class="flex items-start mb-4">
                    <div class="bg-primary text-white rounded-full p-1 mr-2">
                        <i class="fas fa-robot text-xs"></i>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg rounded-tl-none">
                        <p class="typing-animation">
                            <span class="dot"></span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                        </p>
                    </div>
                </div>
            `;
            chatMessages.appendChild(typingElement);
            scrollToBottom();
        }
        
        // Remove typing indicator
        function removeTypingIndicator() {
            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }
        
        // Generate fallback response based on keywords (used if API fails)
        function generateFallbackResponse(message) {
            if (message.includes('prescription') || message.includes('doctor')) {
                return 'To upload a prescription, go to your account page and select "Upload New Prescription". We accept prescriptions in PDF, JPG, or PNG format and our pharmacists will review it within 24 hours.';
            } else if (message.includes('order') && (message.includes('status') || message.includes('track'))) {
                return 'You can check your order status by logging into your account and visiting the "Orders" section. If you have any specific questions about an order, please contact our customer service.';
            } else if (message.includes('find') && (message.includes('medication') || message.includes('medicine') || message.includes('drug'))) {
                return 'You can search for medications using the search bar at the top of the page or browse our categories. If you need a specific medication, please let me know and I can help you find it.';
            } else if (message.includes('contact') || message.includes('support') || message.includes('help')) {
                return 'You can reach our customer support team at support@aphothecare.com or by calling +1-800-PHARMACY during business hours (9AM-6PM, Monday-Friday).';
            } else {
                return 'I\'m not sure I understand. Could you please rephrase your question or select one of the quick reply options below?';
            }
        }
        
        // Scroll to bottom of chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Save chat history to local storage when chat ends
        window.addEventListener('beforeunload', function() {
            const messages = chatMessages.innerHTML;
            localStorage.setItem('chat_history', messages);
        });
        
        // Load chat history from local storage on page load
        const savedChat = localStorage.getItem('chat_history');
        if (savedChat) {
            chatMessages.innerHTML = savedChat;
        }
    });
    </script>
</body>
</html> 