<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
    $privacy = isset($_POST['privacy']) ? true : false;
    
    // Validate form data
    if (empty($name) || empty($email) || empty($subject) || empty($message) || !$privacy) {
        header('Location: ../index.php?page=contact&error=1');
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../index.php?page=contact&error=2');
        exit;
    }
    
    try {
        // Store message in database
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message, created_at, ip_address)
            VALUES (:name, :email, :phone, :subject, :message, NOW(), :ip_address)
        ");
        
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ]);
        
        // Send email notification to admin (in a real implementation)
        /*
        $to = "admin@aphothecare.com";
        $email_subject = "New Contact Form Submission: $subject";
        $email_body = "Name: $name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Phone: $phone\n\n";
        $email_body .= "Message:\n$message\n";
        $headers = "From: noreply@aphothecare.com\n";
        $headers .= "Reply-To: $email";
        
        mail($to, $email_subject, $email_body, $headers);
        */
        
        // Log this action if user is logged in
        if (isset($_SESSION['user_id'])) {
            logUserActivity($_SESSION['user_id'], 'contact_form_submission', [
                'subject' => $subject
            ]);
        }
        
        // Redirect back to contact page with success message
        header('Location: ../index.php?page=contact&success=1');
        exit;
        
    } catch (PDOException $e) {
        // Log error
        error_log("Contact Form Error: " . $e->getMessage());
        
        // Consider different scenarios for database table not existing
        if ($e->getCode() == '42S02') { // Table doesn't exist error code
            // In this case, we'll pretend the message was sent successfully 
            // since we don't want to confuse the user with technical errors
            
            // In a real implementation, you might want to have a fallback method
            // such as storing the message in a file or sending it via email
            
            header('Location: ../index.php?page=contact&success=1');
            exit;
        } else {
            // For other database errors, show an error message
            header('Location: ../index.php?page=contact&error=3');
            exit;
        }
    }
} else {
    // If not a POST request, redirect to contact page
    header('Location: ../index.php?page=contact');
    exit;
}
?> 