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

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Contul meu - CustomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5" style="max-width: 600px;">
        <h2 class="mb-4 text-center" style="color: #6f42c1;">Bună, <?php echo htmlspecialchars($utilizator['USERNAME']); ?>!</h2>
        <div class="card bg-dark border-secondary">
            <div class="card-body text-center" style="color: #6f42c1; align-content: center;">
                <h5 class="card-title" style="color: #6f42c1; align-content: center;">Informații cont</h5>
                <p class="card-text" style="color: #6f42c1; align-content: center;"><strong>Nume utilizator:</strong> <?php echo htmlspecialchars($utilizator['USERNAME']); ?></p>
                <p class="card-text" style="color: #6f42c1; align-content: center;"><strong>Email:</strong> <?php echo htmlspecialchars($utilizator['EMAIL']); ?></p>
                <a href="schimbare_parola.php" class="btn btn-outline-light me-2">Schimba parola</a>
                <a href="logout.php" class="btn btn-outline-light">Deconectare</a>
            </div>
        </div>
    </div>
</body>
</html>