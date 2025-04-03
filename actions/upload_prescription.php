<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?page=login&error=unauthorized');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Get form data
    $product_id = isset($_POST['product_id']) && !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $doctor_name = isset($_POST['doctor_name']) ? sanitizeInput($_POST['doctor_name']) : '';
    $doctor_license = isset($_POST['doctor_license']) ? sanitizeInput($_POST['doctor_license']) : null;
    $issue_date = isset($_POST['issue_date']) ? $_POST['issue_date'] : '';
    $notes = isset($_POST['notes']) ? sanitizeInput($_POST['notes']) : null;
    
    // Validate form data
    if (empty($doctor_name) || empty($issue_date) || !isset($_FILES['prescription_file'])) {
        $_SESSION['flash_message'] = "Please fill in all required fields and upload a prescription file.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=prescription');
        exit;
    }
    
    // Validate issue date
    $issue_date_obj = new DateTime($issue_date);
    $today = new DateTime();
    if ($issue_date_obj > $today) {
        $_SESSION['flash_message'] = "Issue date cannot be in the future.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=prescription');
        exit;
    }
    
    // Check if prescription file was uploaded successfully
    if ($_FILES['prescription_file']['error'] !== UPLOAD_ERR_OK) {
        $error_message = "Error uploading file: ";
        switch ($_FILES['prescription_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $error_message .= "The file exceeds the upload_max_filesize directive in php.ini.";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error_message .= "The file exceeds the MAX_FILE_SIZE directive in the HTML form.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message .= "The file was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message .= "No file was uploaded.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error_message .= "Missing a temporary folder.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error_message .= "Failed to write file to disk.";
                break;
            case UPLOAD_ERR_EXTENSION:
                $error_message .= "A PHP extension stopped the file upload.";
                break;
            default:
                $error_message .= "Unknown error.";
        }
        
        $_SESSION['flash_message'] = $error_message;
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=prescription');
        exit;
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    $file_type = $_FILES['prescription_file']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['flash_message'] = "Invalid file type. Please upload a PDF, JPG, or PNG file.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=prescription');
        exit;
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/prescriptions/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $filename = uniqid('prescription_') . '_' . $user_id . '_' . date('Ymd');
    
    // Add appropriate file extension
    switch ($file_type) {
        case 'image/jpeg':
        case 'image/jpg':
            $filename .= '.jpg';
            break;
        case 'image/png':
            $filename .= '.png';
            break;
        case 'application/pdf':
            $filename .= '.pdf';
            break;
    }
    
    $upload_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($_FILES['prescription_file']['tmp_name'], $upload_path)) {
        try {
            // Insert prescription into database
            $stmt = $pdo->prepare("
                INSERT INTO prescriptions (user_id, product_id, prescription_file, doctor_name, doctor_license, patient_name, issue_date, notes, status, created_at)
                VALUES (:user_id, :product_id, :prescription_file, :doctor_name, :doctor_license, :patient_name, :issue_date, :notes, 'pending', NOW())
            ");
            
            // Get user's name
            $user_stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = :id");
            $user_stmt->execute(['id' => $user_id]);
            $user = $user_stmt->fetch();
            $patient_name = $user ? $user['first_name'] . ' ' . $user['last_name'] : null;
            
            // Database path (relative to web root)
            $db_file_path = 'uploads/prescriptions/' . $filename;
            
            $stmt->execute([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'prescription_file' => $db_file_path,
                'doctor_name' => $doctor_name,
                'doctor_license' => $doctor_license,
                'patient_name' => $patient_name,
                'issue_date' => $issue_date,
                'notes' => $notes
            ]);
            
            // Log activity
            logUserActivity($user_id, 'prescription_upload', [
                'prescription_id' => $pdo->lastInsertId(),
                'product_id' => $product_id
            ]);
            
            $_SESSION['flash_message'] = "Prescription uploaded successfully. It will be reviewed by our pharmacists shortly.";
            $_SESSION['flash_type'] = "green";
            
            // Redirect based on where the user came from
            if ($product_id) {
                header('Location: ../index.php?page=product&id=' . $product_id);
            } else {
                header('Location: ../index.php?page=prescription');
            }
            exit;
            
        } catch (PDOException $e) {
            // Log error and show user-friendly message
            error_log("Prescription Upload Error: " . $e->getMessage());
            
            $_SESSION['flash_message'] = "An error occurred while saving your prescription. Please try again.";
            $_SESSION['flash_type'] = "red";
            header('Location: ../index.php?page=prescription');
            exit;
        }
    } else {
        $_SESSION['flash_message'] = "Error moving uploaded file. Please try again.";
        $_SESSION['flash_type'] = "red";
        header('Location: ../index.php?page=prescription');
        exit;
    }
} else {
    // If not a POST request, redirect to prescription page
    header('Location: ../index.php?page=prescription');
    exit;
}
?> 