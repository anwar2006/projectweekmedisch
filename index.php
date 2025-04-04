<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Define the current page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Include header
include 'includes/header.php';

// Load the appropriate page content
switch ($page) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'medications':
        include 'pages/medications.php';
        break;
    case 'health-products':
        include 'pages/health-products.php';
        break;
    case 'prescription':
        include 'pages/prescription.php';
        break;
    case 'contact':
        include 'pages/contact.php';
        break;
    case 'cart':
        include 'pages/cart.php';
        break;
    case 'checkout':
        include 'pages/checkout.php';
        break;
    case 'login':
        include 'pages/login.php';
        break;
    case 'register':
        include 'pages/register.php';
        break;
    case 'product':
        include 'pages/product.php';
        break;
    case 'profile':
        include 'pages/profile.php';
        break;
    case 'dashboard':
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $_SESSION['flash_message'] = "You must be logged in as an admin to access the dashboard.";
            $_SESSION['flash_type'] = "red";
            header('Location: index.php?page=login');
            exit;
        }
        include 'dashboard/index.php';
        break;
    default:
        include 'pages/404.php';
        break;
}

// Include footer
include 'includes/footer.php';
?>
