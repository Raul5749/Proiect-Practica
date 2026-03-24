<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_produs'])) {
    $id_produs = (int)$_POST['id_produs'];
    $nota = (int)$_POST['nota'];
    $comentariu = trim($_POST['comentariu']);
    $nume_client = !empty($_POST['nume_client']) ? trim($_POST['nume_client']) : 'Client Anonim';

    if ($nota >= 1 && $nota <= 5 && !empty($comentariu)) {
        $stmt = $pdo->prepare("INSERT INTO recenzii (ID_PRODUS, NUME_CLIENT, NOTA, COMENTARIU) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_produs, $nume_client, $nota, $comentariu]);
    }
}
header('Location: proiect.php');
exit;