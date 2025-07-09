<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');
$term = $_GET['term'] ?? '';
$results = [];

if (strlen($term) >= 2) {
    try {
        $stmt = $pdo->prepare("
            SELECT name, slug, 'Produk' as type
            FROM products
            WHERE name LIKE ?
            ORDER BY
                CASE
                    WHEN name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END, 
                popularity_score DESC
            LIMIT 7
        ");
        $searchTermContains = '%' . $term . '%';
        $searchTermStarts = $term . '%';
        $stmt->execute([$searchTermContains, $searchTermStarts, $searchTermContains]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Live search error: " . $e->getMessage());
    }
}

echo json_encode($results);
?>