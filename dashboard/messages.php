<?php
// Get messages from database
try {
    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            CASE 
                WHEN m.status = 'new' THEN 'bg-blue-100 text-blue-800'
                WHEN m.status = 'read' THEN 'bg-gray-100 text-gray-800'
                WHEN m.status = 'replied' THEN 'bg-green-100 text-green-800'
                WHEN m.status = 'spam' THEN 'bg-red-100 text-red-800'
            END as status_class
        FROM contact_messages m
        ORDER BY 
            CASE m.status
                WHEN 'new' THEN 1
                WHEN 'read' THEN 2
                WHEN 'replied' THEN 3
                WHEN 'spam' THEN 4
            END,
            m.created_at DESC
    ");
    $stmt->execute();
    $messages = $stmt->fetchAll();
    
    // Get message counts by status
    $stmt = $pdo->prepare("
        SELECT status, COUNT(*) as count
        FROM contact_messages
        GROUP BY status
    ");
    $stmt->execute();
    $status_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
} catch (PDOException $e) {
    error_log("Error fetching messages: " . $e->getMessage());
    $messages = [];
    $status_counts = [];
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Contact Messages</h2>
        
        <div class="flex space-x-4">
            <div class="flex space-x-2">
                <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                    New: <?php echo $status_counts['new'] ?? 0; ?>
                </span>
                <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                    Read: <?php echo $status_counts['read'] ?? 0; ?>
                </span>
                <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                    Replied: <?php echo $status_counts['replied'] ?? 0; ?>
                </span>
                <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                    Spam: <?php echo $status_counts['spam'] ?? 0; ?>
                </span>
            </div>
            
            <button onclick="exportMessages()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-download mr-2"></i> Export
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Date</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">From</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-500">Subject</th>
                    <th class="py-3 px-4 text-center text-sm font-medium text-gray-500">Status</th>
                    <th class="py-3 px-4 text-right text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-4 px-4">
                        <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
                    </td>
                    <td class="py-4 px-4">
                        <div class="font-medium"><?php echo $message['name']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo $message['email']; ?></div>
                        <?php if ($message['phone']): ?>
                        <div class="text-sm text-gray-500"><?php echo $message['phone']; ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-4">
                        <div class="font-medium"><?php echo $message['subject']; ?></div>
                        <div class="text-sm text-gray-500 truncate max-w-md">
                            <?php echo $message['message']; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-2 py-1 rounded-full text-sm <?php echo $message['status_class']; ?>">
                            <?php echo ucfirst($message['status']); ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <button onclick="viewMessage(<?php echo $message['id']; ?>)" 
                                class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="updateStatus(<?php echo $message['id']; ?>, 'read')" 
                                class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="updateStatus(<?php echo $message['id']; ?>, 'replied')" 
                                class="text-green-500 hover:text-green-700">
                                <i class="fas fa-reply"></i>
                            </button>
                            <button onclick="updateStatus(<?php echo $message['id']; ?>, 'spam')" 
                                class="text-red-500 hover:text-red-700">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Message View Modal -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold text-gray-800" id="modalSubject"></h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <div class="text-sm text-gray-500 mb-2">
                    From: <span id="modalFrom"></span><br>
                    Date: <span id="modalDate"></span>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p id="modalMessage" class="text-gray-700 whitespace-pre-wrap"></p>
                </div>
            </div>
            <div class="flex justify-end space-x-2">
                <button onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Close
                </button>
                <button onclick="replyToMessage()" class="bg-primary hover:bg-dark text-white px-4 py-2 rounded-lg transition-colors">
                    Reply
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentMessageId = null;

function viewMessage(id) {
    currentMessageId = id;
    const row = document.querySelector(`tr[data-message-id="${id}"]`);
    if (!row) return;
    
    document.getElementById('modalSubject').textContent = row.querySelector('.subject').textContent;
    document.getElementById('modalFrom').textContent = row.querySelector('.from').textContent;
    document.getElementById('modalDate').textContent = row.querySelector('.date').textContent;
    document.getElementById('modalMessage').textContent = row.querySelector('.message').textContent;
    
    document.getElementById('messageModal').classList.remove('hidden');
    document.getElementById('messageModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('messageModal').classList.add('hidden');
    document.getElementById('messageModal').classList.remove('flex');
    currentMessageId = null;
}

function updateStatus(id, status) {
    if (!confirm(`Are you sure you want to mark this message as ${status}?`)) return;
    
    fetch('actions/update_message_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `message_id=${id}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating message status: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating message status');
    });
}

function replyToMessage() {
    if (!currentMessageId) return;
    
    const email = document.getElementById('modalFrom').textContent;
    window.location.href = `mailto:${email}?subject=Re: ${document.getElementById('modalSubject').textContent}`;
}

function exportMessages() {
    window.location.href = 'actions/export_messages.php';
}
</script> 