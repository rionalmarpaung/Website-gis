<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("SELECT * FROM lokasi_indomaret ORDER BY nama_toko ASC");
    $stmt->execute();
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($locations);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
