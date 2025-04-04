<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is staff/admin
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get and validate input
$message_id = filter_input(INPUT_POST, 'message_id', FILTER_VALIDATE_INT);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

if (!$message_id || !$status) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// Validate status
$valid_statuses = ['new', 'read', 'replied', 'spam'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

try {
    // Update message status
    $stmt = $pdo->prepare("
        UPDATE contact_messages 
        SET status = :status,
            updated_at = NOW()
        WHERE id = :id
    ");
    
    $stmt->execute([
        'status' => $status,
        'id' => $message_id
    ]);
    
    // Log the action
    logUserActivity($_SESSION['user_id'], 'update_message_status', [
        'message_id' => $message_id,
        'new_status' => $status
    ]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    error_log("Error updating message status: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?> 