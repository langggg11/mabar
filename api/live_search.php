<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$limit = intval($_GET['limit'] ?? 10);

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $search_term = '%' . $query . '%';
    $stmt = $pdo->prepare("
        SELECT id, name, slug, image, price 
        FROM products 
        WHERE name LIKE ? 
        ORDER BY name ASC 
        LIMIT ?
    ");
    $stmt->execute([$search_term, $limit]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
