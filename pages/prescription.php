<?php
// Get prescriptions
try {
    // Get all prescriptions with product details
    $stmt = $pdo->prepare("
        SELECT p.*, pr.name as product_name 
        FROM prescriptions p
        LEFT JOIN products pr ON p.product_id = pr.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $prescriptions = $stmt->fetchAll();
    
    // Get product details if product_id is specified
    $product = null;
    if (isset($_GET['product_id'])) {
        $product_id = (int)$_GET['product_id'];
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND requires_prescription = 1");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            $_SESSION['flash_message'] = "The selected product does not require a prescription or was not found.";
            $_SESSION['flash_type'] = "red";
            header('Location: index.php?page=prescription');
            exit;
        }
    }
    
} catch (PDOException $e) {
    // Handle database error
    $prescriptions = [];
    $product = null;
    
    // Log error
    error_log("Prescription Error: " . $e->getMessage());
}

// Sample prescription data if database doesn't have any
if (empty($prescriptions)) {
    $prescriptions = [
        [
            'id' => 1,
            'product_id' => 3,
            'product_name' => 'Amoxicillin 250mg',
            'prescription_file' => 'uploads/prescriptions/prescription1.pdf',
            'doctor_name' => 'Dr. John Smith',
            'status' => 'approved',
            'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
            'expiry_date' => date('Y-m-d', strtotime('+6 months'))
        ],
        [
            'id' => 2,
            'product_id' => 5,
            'product_name' => 'Fluoxetine 20mg',
            'prescription_file' => 'uploads/prescriptions/prescription2.pdf',
            'doctor_name' => 'Dr. Sarah Johnson',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'expiry_date' => null
        ]
    ];
}
?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Prescription Management</h1>
    
    <!-- Breadcrumbs -->
    <nav class="flex mb-6">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-500 hover:text-primary">Home</a>
            </li>
            <li class="flex items-center">
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-primary">Prescription</span>
            </li>
        </ol>
    </nav>
    
    <!-- Prescription Information -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-blue-800 font-medium">About Our Prescription Service</h3>
                <div class="text-sm text-blue-700 mt-2">
                    <p class="mb-2">To order prescription medications, you need to upload a valid prescription from your doctor. Our pharmacists will review your prescription before processing your order.</p>
                    <p>For your security and to comply with regulations, we only accept prescriptions that are:</p>
                    <ul class="list-disc ml-5 mt-1">
                        <li>Valid and not expired</li>
                        <li>Issued by a licensed healthcare provider</li>
                        <li>Clear and legible</li>
                        <li>In PDF, JPG, or PNG format</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upload Prescription Form -->
    <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo isset($product) ? 'Upload Prescription for ' . $product['name'] : 'Upload New Prescription'; ?>
        </h2>
        
        <form action="actions/upload_prescription.php" method="post" enctype="multipart/form-data">
            <?php if (isset($product)): ?>
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <?php else: ?>
            <div class="mb-4">
                <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">Medication (Optional)</label>
                <select name="product_id" id="product_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">General Prescription</option>
                    <?php 
                    try {
                        $stmt = $pdo->query("SELECT id, name FROM products WHERE requires_prescription = 1 ORDER BY name");
                        $prescription_products = $stmt->fetchAll();
                        
                        foreach ($prescription_products as $prod): 
                    ?>
                    <option value="<?php echo $prod['id']; ?>"><?php echo $prod['name']; ?></option>
                    <?php 
                        endforeach;
                    } catch (PDOException $e) {
                        // Sample products if database error
                    ?>
                    <option value="3">Amoxicillin 250mg</option>
                    <option value="5">Fluoxetine 20mg</option>
                    <option value="6">Salbutamol Inhaler</option>
                    <?php } ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="mb-4">
                <label for="prescription_file" class="block text-gray-700 text-sm font-bold mb-2">Prescription File*</label>
                <input type="file" id="prescription_file" name="prescription_file" required 
                    class="block w-full text-gray-700 py-2 px-3 border border-gray-300 rounded cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    accept=".pdf,.jpg,.jpeg,.png">
                <p class="text-xs text-gray-500 mt-1">Upload your prescription file (PDF, JPG, PNG format)</p>
            </div>
            
            <div class="mb-4">
                <label for="doctor_name" class="block text-gray-700 text-sm font-bold mb-2">Doctor's Name*</label>
                <input type="text" id="doctor_name" name="doctor_name" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Dr. John Smith">
            </div>
            
            <div class="mb-4">
                <label for="doctor_license" class="block text-gray-700 text-sm font-bold mb-2">Doctor's License Number</label>
                <input type="text" id="doctor_license" name="doctor_license" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="License number">
            </div>
            
            <div class="mb-4">
                <label for="issue_date" class="block text-gray-700 text-sm font-bold mb-2">Issue Date*</label>
                <input type="date" id="issue_date" name="issue_date" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    max="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="mb-4">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Additional Notes</label>
                <textarea id="notes" name="notes" rows="3" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Any additional information for our pharmacists"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-dark text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition-colors">
                    Upload Prescription
                </button>
            </div>
        </form>
    </div>
    
    <!-- Prescriptions List -->
    <div>
        <h2 class="text-xl font-semibold mb-4">Prescriptions</h2>
        
        <?php if (empty($prescriptions)): ?>
        <div class="bg-gray-50 p-4 text-center rounded">
            <p class="text-gray-500">No prescriptions have been uploaded yet.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Prescription ID</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Medication</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Uploaded</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Expires</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Status</th>
                        <th class="py-2 px-4 text-left text-sm font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $prescription): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4">#<?php echo $prescription['id']; ?></td>
                        <td class="py-3 px-4">
                            <?php echo $prescription['product_name'] ? $prescription['product_name'] : 'General Prescription'; ?>
                        </td>
                        <td class="py-3 px-4"><?php echo date('M d, Y', strtotime($prescription['created_at'])); ?></td>
                        <td class="py-3 px-4">
                            <?php echo $prescription['expiry_date'] ? date('M d, Y', strtotime($prescription['expiry_date'])) : 'Pending Review'; ?>
                        </td>
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
                            <div class="flex space-x-2">
                                <a href="<?php echo $prescription['prescription_file']; ?>" target="_blank" class="text-blue-500 hover:underline" title="View Prescription">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($prescription['status'] === 'approved' && $prescription['product_id']): ?>
                                <a href="index.php?page=product&id=<?php echo $prescription['product_id']; ?>" class="text-green-500 hover:underline" title="Order Medication">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Prescription Guidelines -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold mb-4">Prescription Guidelines</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold text-lg mb-2">What Makes a Valid Prescription?</h3>
            <ul class="list-disc ml-5 space-y-1 text-gray-700">
                <li>Written by a licensed healthcare provider</li>
                <li>Contains complete patient information</li>
                <li>Includes clear medication name, strength, and dosage</li>
                <li>Has a recent issue date (usually within 6 months)</li>
                <li>Contains doctor's signature</li>
                <li>Not expired (typically valid for 6-12 months)</li>
            </ul>
        </div>
        
        <div>
            <h3 class="font-semibold text-lg mb-2">Prescription Process</h3>
            <ol class="list-decimal ml-5 space-y-1 text-gray-700">
                <li>Upload your prescription</li>
                <li>Our pharmacists review your prescription (usually within 24 hours)</li>
                <li>If approved, you can order the prescribed medication</li>
                <li>If rejected, you'll receive a notification with the reason</li>
                <li>Approved prescriptions are stored securely in your account</li>
            </ol>
        </div>
    </div>
    
    <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Notice</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Attempting to order prescription medications without a valid prescription is illegal. We reserve the right to report suspected fraudulent prescriptions to the appropriate authorities.</p>
                </div>
            </div>
        </div>
    </div>
</div> 