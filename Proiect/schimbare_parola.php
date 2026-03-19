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
        <h2 class="mb-4 text-center" style="color: #6f42c1;">Schimbare parolă</h2>
        <div class="card bg-dark border-secondary">
            <div class="card-body text-center" style="color: #6f42c1; align-content: center;">
                <h5 class="card-title" style="color: #6f42c1; align-content: center;">Introduceți emailul sau Username-ul:</h5>
                <form method="POST" class="mb-3">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                <h5 class="card-title" style="color: #6f42c1; align-content: center;">Introduceți parola nouă:</h5>
                <form method="POST" class="mb-3">
                    <div class="mb-3">
                        <input type="password" name="parola_noua" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                <h5 class="card-title" style="color: #6f42c1; align-content: center;">Introduceți parola nouă din nou:</h5>
                <form method="POST" class="mb-3">
                    <div class="mb-3">
                        <input type="password" name="parola_noua_confirm" class="form-control bg-dark text-white border-secondary" required>
                    </div>  
                <form method="POST" class="mb-3">
                    <button type="submit" class="btn w-100 mt-4" style="background-color: #6f42c1; color: white;">Schimbă parola</button>
                    <?php if (isset($eroare)) { ?>
                        <div class="alert alert-danger mt-3"><?php echo $eroare; ?></div>
                    <?php } ?>
                  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') 
                  { 
                        $parola_noua = $_POST['parola_noua'];
                        $parola_noua_confirm = $_POST['parola_noua_confirm'];

                        if ($parola_noua !== $parola_noua_confirm) {
                            $eroare = "Parolele nu coincid!";
                        } else {
                            $sql = "UPDATE utilizatori SET PASSWORD = :parola WHERE EMAIL = :email";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(['parola' => password_hash($parola_noua, PASSWORD_DEFAULT), 'email' => $_POST['email']]);
                        echo '<div class="alert alert-success mt-3">Parola a fost modificată cu succes!</div>';
                    }
                  }
                    ?>
                </form>
                <a href="contul_meu.php" class="btn btn-outline-light">Înapoi la contul meu</a>
                <a href="proiect.php" class="btn btn-outline-light">Înapoi la magazin</a>
            </div>
        </div>
    </div>
</body>
</html>     