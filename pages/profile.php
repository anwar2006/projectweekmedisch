<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login&redirect=profile');
    exit;
}

// Get user information
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // User not found, log them out
        session_destroy();
        header('Location: index.php?page=login&error=invalid_user');
        exit;
    }
    
    // Get user's orders
    $stmt = $pdo->prepare("
        SELECT o.*, COUNT(oi.id) as item_count, SUM(oi.quantity) as total_items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = :user_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $recent_orders = $stmt->fetchAll();
    
    // Get user's prescriptions
    $stmt = $pdo->prepare("
        SELECT p.*, pr.name as product_name
        FROM prescriptions p
        LEFT JOIN products pr ON p.product_id = pr.id
        WHERE p.user_id = :user_id
        ORDER BY p.created_at DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $prescriptions = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // Handle database error
    error_log("Profile Error: " . $e->getMessage());
    $user = null;
    $recent_orders = [];
    $prescriptions = [];
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="flex mb-6">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="index.php" class="text-gray-500 hover:text-primary">Home</a>
                </li>
                <li class="flex items-center">
                    <span class="mx-2 text-gray-400">/</span>
                    <span class="text-primary">My Account</span>
                </li>
            </ol>
        </nav>
        
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row items-center md:items-start">
                <div class="mb-4 md:mb-0 md:mr-6">
                    <div class="relative h-24 w-24 profile-picture-container">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" 
                                class="h-24 w-24 rounded-full object-cover">
                        <?php else: ?>
                            <div class="h-24 w-24 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-4xl text-gray-500"></i>
                            </div>
                        <?php endif; ?>
                        <label for="profile_picture" class="absolute bottom-0 right-0 bg-primary text-white p-1 rounded-full cursor-pointer hover:bg-dark transition-colors">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-2xl font-bold text-gray-800"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h1>
                    <p class="text-gray-600"><?php echo $user['email']; ?></p>
                    <p class="text-gray-500 text-sm mt-1">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="#profile-details" class="bg-primary hover:bg-dark text-white px-4 py-2 rounded-md text-sm transition-colors">
                            Edit Profile
                        </a>
                        <a href="#change-password" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm transition-colors">
                            Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Account Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Orders</p>
                        <h3 class="text-2xl font-semibold">
                            <?php 
                            try {
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = :user_id");
                                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                                echo $stmt->fetchColumn();
                            } catch (PDOException $e) {
                                echo '0';
                            }
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <i class="fas fa-prescription text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Prescriptions</p>
                        <h3 class="text-2xl font-semibold">
                            <?php 
                            try {
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM prescriptions WHERE user_id = :user_id");
                                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                                echo $stmt->fetchColumn();
                            } catch (PDOException $e) {
                                echo '0';
                            }
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                        <i class="fas fa-euro-sign text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Spent</p>
                        <h3 class="text-2xl font-semibold">
                            <?php 
                            try {
                                $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE user_id = :user_id");
                                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                                $total_spent = $stmt->fetchColumn();
                                echo $total_spent ? '€' . number_format($total_spent, 2) : '€0.00';
                            } catch (PDOException $e) {
                                echo '€0.00';
                            }
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Details Form -->
        <div id="profile-details" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Profile Details</h2>
            
            <form action="actions/update_profile.php" method="post" class="space-y-4" enctype="multipart/form-data">
                <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo $user['phone'] ?? ''; ?>"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                    <textarea id="address" name="address" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo $user['address'] ?? ''; ?></textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-primary hover:bg-dark text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition-colors">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Change Password Form -->
        <div id="change-password" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Change Password</h2>
            
            <form action="actions/change_password.php" method="post" class="space-y-4">
                <div>
                    <label for="current_password" class="block text-gray-700 text-sm font-bold mb-2">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                    <input type="password" id="new_password" name="new_password" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-primary hover:bg-dark text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition-colors">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Recent Orders</h2>
                <a href="index.php?page=orders" class="text-primary hover:text-dark text-sm">View All Orders</a>
            </div>
            
            <?php if (empty($recent_orders)): ?>
            <div class="text-center py-8">
                <div class="text-gray-400 mb-2">
                    <i class="fas fa-shopping-bag text-4xl"></i>
                </div>
                <p class="text-gray-500">You haven't placed any orders yet.</p>
                <a href="index.php?page=medications" class="inline-block mt-4 text-primary hover:text-dark">Browse Products</a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Order ID</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Date</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Items</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Total</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Status</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-3 px-4">#<?php echo $order['id']; ?></td>
                            <td class="py-3 px-4"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td class="py-3 px-4"><?php echo $order['total_items']; ?> items</td>
                            <td class="py-3 px-4">€<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded text-xs 
                                    <?php 
                                    switch ($order['status']) {
                                        case 'completed':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'processing':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'shipped':
                                            echo 'bg-purple-100 text-purple-800';
                                            break;
                                        case 'cancelled':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>
                                ">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <a href="index.php?page=order&id=<?php echo $order['id']; ?>" class="text-primary hover:text-dark">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Prescriptions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">My Prescriptions</h2>
                <a href="index.php?page=prescription" class="text-primary hover:text-dark text-sm">Upload New Prescription</a>
            </div>
            
            <?php if (empty($prescriptions)): ?>
            <div class="text-center py-8">
                <div class="text-gray-400 mb-2">
                    <i class="fas fa-prescription text-4xl"></i>
                </div>
                <p class="text-gray-500">You haven't uploaded any prescriptions yet.</p>
                <a href="index.php?page=prescription" class="inline-block mt-4 text-primary hover:text-dark">Upload a Prescription</a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Prescription ID</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Medication</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Doctor</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Date</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Status</th>
                            <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prescriptions as $prescription): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-3 px-4">#<?php echo $prescription['id']; ?></td>
                            <td class="py-3 px-4"><?php echo $prescription['product_name'] ?? 'General Prescription'; ?></td>
                            <td class="py-3 px-4"><?php echo $prescription['doctor_name']; ?></td>
                            <td class="py-3 px-4"><?php echo date('M d, Y', strtotime($prescription['created_at'])); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded text-xs 
                                    <?php 
                                    switch ($prescription['status']) {
                                        case 'approved':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'pending':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'rejected':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>
                                ">
                                    <?php echo ucfirst($prescription['status']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <a href="<?php echo $prescription['prescription_file']; ?>" target="_blank" class="text-primary hover:text-dark mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <?php if ($prescription['status'] === 'approved' && $prescription['product_id']): ?>
                                <a href="index.php?page=product&id=<?php echo $prescription['product_id']; ?>" class="text-green-500 hover:text-green-700">
                                    <i class="fas fa-shopping-cart"></i> Order
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('.profile-picture-container img') || 
                       document.createElement('img');
            img.src = e.target.result;
            img.alt = 'Profile Picture';
            img.className = 'h-24 w-24 rounded-full object-cover';
            
            const container = document.querySelector('.profile-picture-container');
            if (!container.querySelector('img')) {
                container.innerHTML = '';
                container.appendChild(img);
            }
        };
        reader.readAsDataURL(file);
    }
});
</script> 