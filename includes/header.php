<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aphothecare - <?php echo ucfirst($page); ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom configuration for Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E6091',
                        secondary: '#48CAE4',
                        accent: '#00B4D8',
                        dark: '#023E8A',
                        light: '#CAF0F8'
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .transition-bg { transition: background-color 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Announcement Bar -->
    <div class="bg-primary text-white text-center py-2 text-sm">
        Free delivery on orders over €50 | Same day delivery for orders before 2PM
    </div>
    
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="index.php" class="flex items-center">
                    <span class="text-2xl font-bold text-primary">Aphothe<span class="text-accent">Care</span></span>
                </a>
                
                <!-- Search Bar - Hidden on mobile -->
                <div class="hidden md:block w-1/3">
                    <form action="index.php" method="get" class="relative">
                        <input type="hidden" name="page" value="search">
                        <input type="text" name="query" placeholder="Search for medications, products..." 
                            class="w-full py-2 px-4 pr-10 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <a href="index.php?page=dashboard" class="text-gray-700 hover:text-primary transition-colors">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                <span class="hidden md:inline">Dashboard</span>
                            </a>
                        <?php endif; ?>
                        <a href="index.php?page=profile" class="text-gray-700 hover:text-primary transition-colors">
                            <i class="fas fa-user mr-2"></i>
                            <span class="hidden md:inline">My Account</span>
                        </a>
                        <a href="actions/logout.php" class="text-gray-700 hover:text-primary transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span class="hidden md:inline">Logout</span>
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=login" class="text-gray-700 hover:text-primary transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span class="hidden md:inline">Login</span>
                        </a>
                        <a href="index.php?page=register" class="text-gray-700 hover:text-primary transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>
                            <span class="hidden md:inline">Register</span>
                        </a>
                    <?php endif; ?>
                    
                    <!-- Cart -->
                    <a href="index.php?page=cart" class="relative flex items-center text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart text-xl mr-2"></i>
                        <span class="hidden md:inline">Cart</span>
                        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo getCartItemsCount(); ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Navigation -->
        <nav class="bg-primary text-white">
            <div class="container mx-auto px-4">
                <ul class="flex flex-wrap items-center justify-center md:justify-start py-2">
                    <li class="px-4 py-2">
                        <a href="index.php" class="hover:text-light transition-colors <?php echo $page === 'home' ? 'font-bold' : ''; ?>">Home</a>
                    </li>
                    <li class="px-4 py-2">
                        <a href="index.php?page=medications" class="hover:text-light transition-colors <?php echo $page === 'medications' ? 'font-bold' : ''; ?>">Medications</a>
                    </li>
                    <li class="px-4 py-2">
                        <a href="index.php?page=health-products" class="hover:text-light transition-colors <?php echo $page === 'health-products' ? 'font-bold' : ''; ?>">Health Products</a>
                    </li>
                    <li class="px-4 py-2">
                        <a href="index.php?page=prescription" class="hover:text-light transition-colors <?php echo $page === 'prescription' ? 'font-bold' : ''; ?>">Prescription</a>
                    </li>
                    <li class="px-4 py-2">
                        <a href="index.php?page=contact" class="hover:text-light transition-colors <?php echo $page === 'contact' ? 'font-bold' : ''; ?>">Contact</a>
                    </li>
                    <li class="px-4 py-2">
                        <a href="index.php?page=chatbot" class="hover:text-light transition-colors <?php echo $page === 'chatbot' ? 'font-bold' : ''; ?>">
                            <i class="fas fa-robot mr-1"></i>Assistant
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Mobile Search - Shown only on mobile -->
        <div class="md:hidden px-4 py-3 bg-gray-50">
            <form action="index.php" method="get" class="relative">
                <input type="hidden" name="page" value="search">
                <input type="text" name="query" placeholder="Search for medications, products..." 
                    class="w-full py-2 px-4 pr-10 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                <button type="submit" class="absolute right-3 top-3 text-gray-500">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-6">
        <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="bg-<?php echo $_SESSION['flash_type'] ?? 'green'; ?>-100 border border-<?php echo $_SESSION['flash_type'] ?? 'green'; ?>-400 text-<?php echo $_SESSION['flash_type'] ?? 'green'; ?>-700 px-4 py-3 rounded mb-4 relative" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['flash_message']; ?></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-<?php echo $_SESSION['flash_type'] ?? 'green'; ?>-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
        <?php 
        // Clear the flash message after displaying it
        unset($_SESSION['flash_message']); 
        unset($_SESSION['flash_type']); 
        endif; 
        ?> 