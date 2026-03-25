<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = $_POST['nume'];
    $email = $_POST['email'];
    $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilizatori (USERNAME, EMAIL, PASSWORD, CREATED_AT) VALUES (:nume, :email, :parola, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nume' => $nume, 'email' => $email, 'parola' => $parola]);
    header('Location: login.php');
    exit();
    }
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Înregistrare - CustomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="text-white" style="background-color: #1f1f1f;">
    <div class="container mt-5" style="max-width: 500px; background-color: #1f1f1f;">
        <h2 class="mb-4 text-center" style="color: #6f42c1;">Creează un cont nou</h2>
        
        <form action="register.php" method="POST" class="p-4 border border-secondary rounded bg-dark">
            <div class="mb-3">
            <label class="form-label text-white">Nume utilizator</label>
            <input type="text" name="nume" class="form-control text-white border-secondary" style="background-color: #1f1f1f;" required>
            </div>

            <div class="mb-3">
            <label class="form-label text-white">Adresă de email</label>
            <input type="email" name="email" class="form-control text-white border-secondary" style="background-color: #1f1f1f;"required>
            </div>

            <div class="mb-3">
            <label class="form-label text-white">Parolă</label>
            <input type="password" name="parola" class="form-control text-white border-secondary" style="background-color: #1f1f1f;" required>
            </div>
            <button type="submit" class="btn w-100 mt-4" style="background-color: #6f42c1; color: white;">Înregistrare</button>
        </form>
        
    </div>
     <footer style="background-color: #1f1f1f; border-top: 2px solid #6f42c1;" class="text-center text-light py-4 mt-5">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> <span style="color: #6f42c1; font-weight: bold;">CUSTOM</span><span class="text-white fw-bold">SHOP</span>. Toate drepturile rezervate.</p>
            <p class="small mb-0">Proiect de practică realizat de către Jarda Raul-Nicolae.</p>
        </div>
    </footer>
</body>
</html>