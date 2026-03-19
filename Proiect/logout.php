<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['utilizator_id'])) {
    header('Location: login.php');
    exit();
}
$sql = "SELECT * FROM utilizatori WHERE ID = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_SESSION['utilizator_id']]);
$utilizator = $stmt->fetch();
session_destroy();
header('Location: login.php');
exit();
?>
