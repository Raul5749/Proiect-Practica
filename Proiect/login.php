<?php
session_start();
require_once 'config.php';
if (isset($_SESSION['utilizator_id'])) {
    if ($_SESSION['ADMIN'] == 1) {
        header('Location: admin.php');
    } else {
        header('Location: proiect.php');
    }
    exit;
}

$eroare = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $parola = trim($_POST['parola']);

    $stmt = $pdo->prepare("SELECT * FROM utilizatori WHERE EMAIL = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && (password_verify($parola, $user['PASSWORD']) || $user['PASSWORD'] === $parola)) {
        $_SESSION['utilizator_id'] = $user['ID'];
        $_SESSION['ADMIN'] = $user['ADMIN'];
        if ($user['ADMIN'] == 1) {
            header('Location: admin.php');
        } 
        else 
        {
            header('Location: proiect.php');
        }
        exit;
    } else {
        $eroare = "Email sau parolă incorectă!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare - CustomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background-color: #1f1f1f; padding: 40px; border-radius: 10px; border: 1px solid #6f42c1; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3); }
        .btn-purple { background-color: #6f42c1; color: white; border: none; }
        .btn-purple:hover { background-color: #59339d; color: white; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: #6f42c1;">CUSTOM<span class="text-white">SHOP</span></h2>
            <p class="text-muted">Autentificare în cont</p>
        </div>

        <?php if($eroare): ?>
            <div class="alert alert-danger text-center p-2"><?php echo $eroare; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Adresă de Email</label>
                <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required placeholder="Nume@exemplu.ro">
            </div>
            <div class="mb-4">
                <label class="form-label">Parolă</label>
                <input type="password" name="parola" class="form-control bg-dark text-white border-secondary" required placeholder="********">
            </div>
            <button type="submit" class="btn btn-purple w-100 mb-3 fs-5">Intră în cont</button>
        </form>
        
        <div class="text-center">
            <a href="proiect.php" class="text-muted text-decoration-none">Înapoi la magazin</a>
        </div>
    </div>

</body>
</html>