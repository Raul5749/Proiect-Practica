<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== 1) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id_produs = (int)$_GET['id'];
    $stmt_img = $pdo->prepare("SELECT IMAGINE FROM produse WHERE ID = ?");
    $stmt_img->execute([$id_produs]);
    $produs = $stmt_img->fetch(PDO::FETCH_ASSOC);

    if ($produs && !empty($produs['IMAGINE'])) {
        $cale_imagine = "imagini/" . $produs['IMAGINE'];
        if (file_exists($cale_imagine) && is_file($cale_imagine)) {
            unlink($cale_imagine);
        }
    }
    $stmt_del = $pdo->prepare("DELETE FROM produse WHERE ID = ?");
    $stmt_del->execute([$id_produs]);
}

header('Location: admin.php');
exit;
?>