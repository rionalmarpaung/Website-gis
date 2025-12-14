<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;

try {
    $stmt = $conn->prepare("DELETE FROM lokasi_indomaret WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php');
} catch(PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
