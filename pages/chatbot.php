<?php
$page_title = "Apothecare Assistant";
?>

<div class="chatbot-page" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div class="chatbot-header" style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2196F3; margin-bottom: 10px;">Apothecare Assistant</h1>
        <p style="color: #666;">Your personal pharmacy assistant, powered by Mistral AI</p>
    </div>

    <div class="chatbot-container" style="background-color: white; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); overflow: hidden;">
        <!-- Chat Messages -->
        <div id="chat-messages" style="height: 500px; overflow-y: auto; padding: 20px; background-color: #f9f9f9;">
            <div class="message bot-message" style="background-color: #f1f1f1; padding: 15px 20px; border-radius: 15px; margin-bottom: 15px; max-width: 80%; word-wrap: break-word;">
                Hello! I'm your Apothecare Assistant, powered by Mistral AI. I can help you with:
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Medication information and advice</li>
                    <li>Prescription queries</li>
                    <li>Health product recommendations</li>
                    <li>General pharmacy questions</li>
                </ul>
                How can I assist you today?
            </div>
        </div>

        <!-- Input Area -->
        <div style="padding: 20px; border-top: 1px solid #eee; background-color: white;">
            <div style="display: flex; gap: 15px; max-width: 800px; margin: 0 auto;">
                <input type="text" id="message-input" style="flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 25px; outline: none; font-size: 16px;" placeholder="Type your message here...">
                <button id="send-button" style="width: 50px; height: 50px; border-radius: 50%; background-color: #2196F3; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01 21L23 12L2.01 3L2 10L17 12L2 14L2.01 21Z" fill="white"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .message {
        margin-bottom: 15px;
        padding: 15px 20px;
        border-radius: 15px;
        max-width: 80%;
        word-wrap: break-word;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .user-message {
        background-color: #2196F3;
        color: white;
        margin-left: auto;
        border-radius: 15px 15px 0 15px;
    }
    
    .bot-message {
        background-color: #f1f1f1;
        color: #333;
        margin-right: auto;
        border-radius: 15px 15px 15px 0;
    }
    
    .error-message {
        background-color: #ffebee;
        color: #c62828;
        margin-right: auto;
        border-radius: 15px 15px 15px 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #message-input:focus {
        border-color: #2196F3;
        box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2);
    }

    #send-button:hover {
        background-color: #1976D2;
        transform: scale(1.05);
        transition: all 0.2s ease;
    }

    .typing-indicator {
        display: flex;
        gap: 5px;
        padding: 10px 15px;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background-color: #2196F3;
        border-radius: 50%;
        animation: typing 1s infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');

        function addMessage(message, type = 'bot') {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}-message`;
            messageDiv.textContent = message;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTypingIndicator() {
            const indicator = document.createElement('div');
            indicator.className = 'message bot-message typing-indicator';
            indicator.innerHTML = '<span></span><span></span><span></span>';
            chatMessages.appendChild(indicator);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            return indicator;
        }

        function removeTypingIndicator(indicator) {
            if (indicator && indicator.parentNode) {
                indicator.parentNode.removeChild(indicator);
            }
        }

        async function sendMessage() {
            const message = messageInput.value.trim();
            if (!message) return;

            // Disable input and button while sending
            messageInput.disabled = true;
            sendButton.disabled = true;

            addMessage(message, 'user');
            messageInput.value = '';

            // Show typing indicator
            const typingIndicator = showTypingIndicator();

            try {
                const response = await fetch('http://localhost:7860/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({ message })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                // Remove typing indicator
                removeTypingIndicator(typingIndicator);
                
                if (data.success) {
                    addMessage(data.response, 'bot');
                } else {
                    addMessage('Error: ' + (data.error || data.message), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                removeTypingIndicator(typingIndicator);
                addMessage('Error connecting to server. Please make sure the chatbot server is running on port 7860.', 'error');
            } finally {
                // Re-enable input and button
                messageInput.disabled = false;
                sendButton.disabled = false;
                messageInput.focus();
            }
        }

        // Send message on button click
        sendButton.addEventListener('click', sendMessage);

        // Send message on Enter key
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey && !messageInput.disabled) {
                sendMessage();
            }
        });
    });
</script> 