<?php
header('Content-Type: application/json');

// Get the request data
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// Default response
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'response' => ''
];

// Check if the request is valid
if (isset($data['message'])) {
    $user_message = strtolower(trim($data['message']));
    
    // Generate a response based on the user's message
    $bot_response = generate_response($user_message);
    
    $response = [
        'success' => true,
        'message' => 'Success',
        'response' => $bot_response
    ];
}

// Send the response
echo json_encode($response);

/**
 * Generate a response based on the user's message
 * 
 * @param string $message The user's message
 * @return string The bot's response
 */
function generate_response($message) {
    // Check for keywords and generate appropriate responses
    if (strpos($message, 'prescription') !== false || strpos($message, 'doctor') !== false) {
        return 'To upload a prescription, go to your account page and select "Upload New Prescription". We accept prescriptions in PDF, JPG, or PNG format and our pharmacists will review it within 24 hours.';
    } 
    
    if (strpos($message, 'order') !== false && (strpos($message, 'status') !== false || strpos($message, 'track') !== false)) {
        return 'You can check your order status by logging into your account and visiting the "Orders" section. If you have any specific questions about an order, please contact our customer service.';
    } 
    
    if (strpos($message, 'find') !== false && (strpos($message, 'medication') !== false || strpos($message, 'medicine') !== false || strpos($message, 'drug') !== false)) {
        return 'You can search for medications using the search bar at the top of the page or browse our categories. If you need a specific medication, please let me know and I can help you find it.';
    } 
    
    if (strpos($message, 'contact') !== false || strpos($message, 'support') !== false || strpos($message, 'help') !== false) {
        return 'You can reach our customer support team at support@aphothecare.com or by calling +1-800-PHARMACY during business hours (9AM-6PM, Monday-Friday).';
    } 
    
    if (strpos($message, 'delivery') !== false || strpos($message, 'shipping') !== false) {
        return 'We offer free shipping on orders over €50. Standard delivery takes 2-3 business days, while express delivery (€9.99 extra) takes 1 business day. We currently deliver to all EU countries.';
    } 
    
    if (strpos($message, 'return') !== false || strpos($message, 'refund') !== false) {
        return 'We accept returns of unopened products within 14 days of delivery. Prescription medications cannot be returned unless there was an error on our part. Please contact support to initiate a return.';
    } 
    
    if (strpos($message, 'thanks') !== false || strpos($message, 'thank you') !== false) {
        return 'You\'re welcome! Is there anything else I can help you with today?';
    } 
    
    if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false || strpos($message, 'hey') !== false) {
        return 'Hello! How can I assist you with your pharmacy needs today?';
    }

    if (strpos($message, 'payment') !== false || strpos($message, 'pay') !== false || strpos($message, 'card') !== false) {
        return 'We accept all major credit cards, PayPal, and bank transfers. All payments are processed securely through our payment gateway.';
    }

    if (strpos($message, 'account') !== false || strpos($message, 'register') !== false || strpos($message, 'login') !== false) {
        return 'You can create an account by clicking on the "Sign Up" button in the top right corner of the page. If you already have an account, you can log in using the "Login" button.';
    }

    if (strpos($message, 'hours') !== false || strpos($message, 'open') !== false || strpos($message, 'operation') !== false) {
        return 'Our online pharmacy operates 24/7, but our customer service team is available from 9AM to 6PM, Monday through Friday.';
    }
    
    // Default response
    return 'I\'m not sure I understand. Could you please rephrase your question or select one of the quick reply options below?';
}
?> 