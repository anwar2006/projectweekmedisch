<?php
// Get all prescriptions with product and user information
try {
    $stmt = $pdo->prepare("
        SELECT p.*, 
               pr.name as product_name,
               u.first_name, u.last_name, u.email,
               COUNT(DISTINCT o.id) as total_orders
        FROM prescriptions p
        LEFT JOIN products pr ON p.product_id = pr.id
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN orders o ON p.id = o.prescription_id
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $prescriptions = $stmt->fetchAll();
} catch (PDOException $e) {
    $prescriptions = [];
    error_log("Error fetching prescriptions: " . $e->getMessage());
}

// Get all prescription statuses for the filter
$statuses = [
    'pending' => 'Pending Review',
    'approved' => 'Approved',
    'rejected' => 'Rejected'
];
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Prescription Management</h2>
        
        <div class="flex space-x-4">
            <div class="relative">
                <input type="text" id="searchPrescription" placeholder="Search prescriptions..." 
                    class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <select id="statusFilter" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $value => $label): ?>
                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
            
            <button onclick="exportPrescriptions()" class="bg-primary hover:bg-dark text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-download mr-2"></i> Export
            </button>
        </div>
    </div>
    
    <?php if (empty($prescriptions)): ?>
    <div class="text-center py-8">
        <i class="fas fa-file-prescription text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500">No prescriptions found</p>
    </div>
    <?php else: ?>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Prescription</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Patient</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Medication</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Doctor</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Status</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Date</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                <tr class="border-t hover:bg-gray-50" data-status="<?php echo $prescription['status']; ?>">
                    <td class="py-4 px-4">
                        <div class="font-medium">#<?php echo $prescription['id']; ?></div>
                        <div class="text-sm text-gray-500">
                            <?php if ($prescription['prescription_file']): ?>
                            <a href="<?php echo $prescription['prescription_file']; ?>" target="_blank" class="text-blue-500 hover:underline">
                                <i class="fas fa-file-pdf mr-1"></i> View File
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="text-sm">
                            <div><?php echo $prescription['first_name'] . ' ' . $prescription['last_name']; ?></div>
                            <div class="text-gray-500"><?php echo $prescription['email']; ?></div>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="text-sm">
                            <?php if ($prescription['product_name']): ?>
                            <div><?php echo $prescription['product_name']; ?></div>
                            <?php else: ?>
                            <div class="text-gray-500">General Prescription</div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <div class="text-sm">
                            <div><?php echo $prescription['doctor_name']; ?></div>
                            <?php if ($prescription['doctor_license']): ?>
                            <div class="text-gray-500">License: <?php echo $prescription['doctor_license']; ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-2 py-1 rounded-full text-sm 
                            <?php 
                            switch ($prescription['status']) {
                                case 'pending':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'approved':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'rejected':
                                    echo 'bg-red-100 text-red-800';
                                    break;
                            }
                            ?>">
                            <?php echo $statuses[$prescription['status']]; ?>
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        <div class="text-sm">
                            <div>Submitted: <?php echo date('M d, Y', strtotime($prescription['created_at'])); ?></div>
                            <?php if ($prescription['expiry_date']): ?>
                            <div class="text-gray-500">Expires: <?php echo date('M d, Y', strtotime($prescription['expiry_date'])); ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex justify-center space-x-2">
                            <button onclick="viewPrescription(<?php echo $prescription['id']; ?>)" 
                                class="text-blue-500 hover:text-blue-700 transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($prescription['status'] === 'pending'): ?>
                            <button onclick="approvePrescription(<?php echo $prescription['id']; ?>)" 
                                class="text-green-500 hover:text-green-700 transition-colors" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="rejectPrescription(<?php echo $prescription['id']; ?>)" 
                                class="text-red-500 hover:text-red-700 transition-colors" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
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

<script>
function viewPrescription(prescriptionId) {
    // Implement view prescription details
    alert('View prescription details functionality to be implemented');
}

function approvePrescription(prescriptionId) {
    if (confirm('Are you sure you want to approve this prescription?')) {
        // Implement approve prescription
        alert('Approve prescription functionality to be implemented');
    }
}

function rejectPrescription(prescriptionId) {
    if (confirm('Are you sure you want to reject this prescription?')) {
        // Implement reject prescription
        alert('Reject prescription functionality to be implemented');
    }
}

function exportPrescriptions() {
    // Implement export prescriptions to CSV/Excel
    alert('Export prescriptions functionality to be implemented');
}

// Search functionality
document.getElementById('searchPrescription').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Status filter
document.getElementById('statusFilter').addEventListener('change', function(e) {
    const status = e.target.value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!status || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script> 