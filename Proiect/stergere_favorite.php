<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['utilizator_id'])) {
    header("Location: login.php");
    exit;
}
if (isset($_GET['id'])) {
    $id_produs = $_GET['id'];
    $id_utilizator = $_SESSION['utilizator_id'];
    $sql = "DELETE FROM favorite WHERE ID_PRODUS = :id_produs AND ID_UTILIZATOR = :id_user";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_produs' => $id_produs, 'id_user' => $id_utilizator]);
}
header("Location: favorite.php");
exit;
?>