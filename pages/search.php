<?php
// Get the search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Prepare SQL query to search across both medications and health products
$sql = "SELECT * FROM products WHERE (name LIKE :search OR description LIKE :search)";
$search_param = '%' . $query . '%';

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search', $search_param);
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    // Count results by category
    $medication_count = 0;
    $health_product_count = 0;
    foreach ($results as $result) {
        if ($result['category'] === 'medications') {
            $medication_count++;
        } else if ($result['category'] === 'health-products') {
            $health_product_count++;
        }
    }
    
    // Redirect to the page with more results, or medications by default if equal
    $target_page = ($health_product_count > $medication_count) ? 'health-products' : 'medications';
} catch (PDOException $e) {
    // If there's an error, default to medications page
    $target_page = 'medications';
}
?>
<script>
    // Redirect to the appropriate page with search parameters
    window.location.href = 'index.php?page=<?php echo $target_page; ?>&search=<?php echo urlencode($query); ?>';
</script> 