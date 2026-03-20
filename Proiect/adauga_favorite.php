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

    $sql_check = "SELECT * FROM favorite WHERE ID_UTILIZATOR = :id_user AND ID_PRODUS = :id_prod";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute(['id_user' => $id_utilizator, 'id_prod' => $id_produs]);
    
    if ($stmt_check->rowCount() == 0) {
        $sql_insert = "INSERT INTO favorite (ID_UTILIZATOR, ID_PRODUS, CREATED_AT) VALUES (:id_user, :id_prod, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute(['id_user' => $id_utilizator, 'id_prod' => $id_produs], );
    }
}
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>