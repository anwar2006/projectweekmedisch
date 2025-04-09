<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prescription_id = isset($_POST['prescription_id']) ? (int)$_POST['prescription_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $review_notes = isset($_POST['review_notes']) ? sanitizeInput($_POST['review_notes']) : '';
    
    // Validate status
    $valid_statuses = ['approved', 'rejected'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }
    
    try {
        // Update prescription status
        $stmt = $pdo->prepare("
            UPDATE prescriptions 
            SET status = :status,
                review_notes = :review_notes,
                reviewed_by = :reviewed_by,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            'status' => $status,
            'review_notes' => $review_notes,
            'reviewed_by' => $_SESSION['user_id'],
            'id' => $prescription_id
        ]);
        
        // If prescription is rejected, delete the prescription file
        if ($status === 'rejected') {
            // Get prescription file path
            $stmt = $pdo->prepare("SELECT prescription_file FROM prescriptions WHERE id = :id");
            $stmt->execute(['id' => $prescription_id]);
            $prescription = $stmt->fetch();
            
            if ($prescription && file_exists('../' . $prescription['prescription_file'])) {
                unlink('../' . $prescription['prescription_file']);
            }
            
            // Delete the prescription record
            $stmt = $pdo->prepare("DELETE FROM prescriptions WHERE id = :id");
            $stmt->execute(['id' => $prescription_id]);
        }
        
        echo json_encode(['success' => true]);
        
    } catch (PDOException $e) {
        error_log("Error updating prescription status: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 