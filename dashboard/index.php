<?php
// Check if user is logged in and is staff
if (!isset($_SESSION['user_id']) || !isStaff($_SESSION['user_id'])) {
    header('Location: ../index.php?page=login&error=unauthorized');
    exit;
}

// Get dashboard action (default to overview)
$action = isset($_GET['action']) ? $_GET['action'] : 'overview';
?>

<div class="flex h-full">
    <!-- Dashboard Sidebar -->
    <div class="w-64 bg-gray-800 text-white min-h-screen flex-shrink-0">
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-xl font-bold">Aphothe<span class="text-accent">Care</span></h2>
            <p class="text-sm text-gray-400">Staff Dashboard</p>
        </div>
        
        <nav class="mt-4">
            <ul>
                <li>
                    <a href="index.php?page=dashboard&action=overview" 
                        class="flex items-center px-4 py-3 <?php echo $action === 'overview' ? 'bg-gray-900' : 'hover:bg-gray-700'; ?> transition-colors">
                        <i class="fas fa-tachometer-alt w-6"></i>
                        <span>Overview</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=dashboard&action=orders" 
                        class="flex items-center px-4 py-3 <?php echo $action === 'orders' ? 'bg-gray-900' : 'hover:bg-gray-700'; ?> transition-colors">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span>Orders</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=dashboard&action=products" 
                        class="flex items-center px-4 py-3 <?php echo $action === 'products' ? 'bg-gray-900' : 'hover:bg-gray-700'; ?> transition-colors">
                        <i class="fas fa-pills w-6"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=dashboard&action=prescriptions" 
                        class="flex items-center px-4 py-3 <?php echo $action === 'prescriptions' ? 'bg-gray-900' : 'hover:bg-gray-700'; ?> transition-colors">
                        <i class="fas fa-file-prescription w-6"></i>
                        <span>Prescriptions</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=dashboard&action=customers" 
                        class="flex items-center px-4 py-3 <?php echo $action === 'customers' ? 'bg-gray-900' : 'hover:bg-gray-700'; ?> transition-colors">
                        <i class="fas fa-users w-6"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li>
                    <a href="index.php?page=dashboard&action=staff" 
                        class="flex items-center px-4 py-3 <?php echo $action === 'staff' ? 'bg-gray-900' : 'hover:bg-gray-700'; ?> transition-colors">
                        <i class="fas fa-user-shield w-6"></i>
                        <span>Staff Management</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=dashboard&action=reports" 
                        class="flex items-center px-4 py-3 <?php echo $action === 'reports' ? 'bg-gray-900' : 'hover:bg-gray-700'; ?> transition-colors">
                        <i class="fas fa-chart-bar w-6"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=dashboard&action=settings" 
                        class="flex items-center px-4 py-3 <?php echo $action === 'settings' ? 'bg-gray-900' : 'hover:bg-gray-700'; ?> transition-colors">
                        <i class="fas fa-cog w-6"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    
    <!-- Dashboard Content -->
    <div class="flex-1 overflow-x-hidden">
        <!-- Top Navigation -->
        <div class="bg-white shadow-md p-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold">
                <?php
                switch ($action) {
                    case 'overview':
                        echo 'Dashboard Overview';
                        break;
                    case 'orders':
                        echo 'Manage Orders';
                        break;
                    case 'products':
                        echo 'Manage Products';
                        break;
                    case 'prescriptions':
                        echo 'Manage Prescriptions';
                        break;
                    case 'customers':
                        echo 'Customer Management';
                        break;
                    case 'staff':
                        echo 'Staff Management';
                        break;
                    case 'reports':
                        echo 'Reports & Analytics';
                        break;
                    case 'settings':
                        echo 'System Settings';
                        break;
                    default:
                        echo 'Dashboard';
                }
                ?>
            </h2>
            
            <div class="flex items-center">
                <div class="mr-6 relative">
                    <button class="flex items-center focus:outline-none">
                        <i class="fas fa-bell text-gray-500"></i>
                        <span class="bg-red-500 text-white rounded-full h-4 w-4 flex items-center justify-center text-xs absolute -top-1 -right-1">3</span>
                    </button>
                </div>
                
                <div class="relative group">
                    <button class="flex items-center focus:outline-none">
                        <span class="mr-2"><?php echo $_SESSION['user_name']; ?></span>
                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                    </button>
                    
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                        <a href="index.php?page=dashboard&action=profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="actions/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="p-6">
            <?php
            // Load the appropriate dashboard content based on action
            switch ($action) {
                case 'overview':
                    include 'dashboard/overview.php';
                    break;
                case 'orders':
                    include 'dashboard/orders.php';
                    break;
                case 'products':
                    include 'dashboard/products.php';
                    break;
                case 'prescriptions':
                    include 'dashboard/prescriptions.php';
                    break;
                case 'customers':
                    include 'dashboard/customers.php';
                    break;
                case 'staff':
                    if ($_SESSION['user_role'] === 'admin') {
                        include 'dashboard/staff.php';
                    } else {
                        include 'dashboard/unauthorized.php';
                    }
                    break;
                case 'reports':
                    if ($_SESSION['user_role'] === 'admin') {
                        include 'dashboard/reports.php';
                    } else {
                        include 'dashboard/unauthorized.php';
                    }
                    break;
                case 'settings':
                    if ($_SESSION['user_role'] === 'admin') {
                        include 'dashboard/settings.php';
                    } else {
                        include 'dashboard/unauthorized.php';
                    }
                    break;
                case 'profile':
                    include 'dashboard/profile.php';
                    break;
                default:
                    include 'dashboard/overview.php';
            }
            ?>
        </div>
    </div>
</div> 