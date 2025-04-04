<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is staff/admin
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?page=login');
    exit;
}

try {
    // Get all messages
    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            DATE_FORMAT(m.created_at, '%Y-%m-%d %H:%i:%s') as formatted_date
        FROM contact_messages m
        ORDER BY m.created_at DESC
    ");
    $stmt->execute();
    $messages = $stmt->fetchAll();
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contact_messages_' . date('Y-m-d') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, [
        'ID',
        'Date',
        'Name',
        'Email',
        'Phone',
        'Subject',
        'Message',
        'Status',
        'IP Address'
    ]);
    
    // Add message data
    foreach ($messages as $message) {
        fputcsv($output, [
            $message['id'],
            $message['formatted_date'],
            $message['name'],
            $message['email'],
            $message['phone'],
            $message['subject'],
            $message['message'],
            $message['status'],
            $message['ip_address']
        ]);
    }
    
    // Close the output stream
    fclose($output);
    
    // Log the export action
    logUserActivity($_SESSION['user_id'], 'export_messages', [
        'count' => count($messages)
    ]);
    
} catch (PDOException $e) {
    error_log("Error exporting messages: " . $e->getMessage());
    header('Location: ../index.php?page=dashboard&action=messages&error=export');
    exit;
}
?> 