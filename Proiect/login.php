<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $parola_introdusa = $_POST['parola'];

    $sql = "SELECT * FROM utilizatori WHERE EMAIL = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($parola_introdusa, $user['PASSWORD'])) {
        $_SESSION['utilizator_id'] = $user['ID'];
        $_SESSION['nume_utilizator'] = $user['USERNAME'];
        header("Location: proiect.php");
        exit;
    } 
    else 
    {
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
</head>
<body class="bg-dark text-white">

    <div class="container mt-5" style="max-width: 500px;">
        <h2 class="mb-4 text-center" style="color: #6f42c1;">Intră în cont</h2>
        
        <?php if (isset($eroare)) { ?>
            <div class="alert alert-danger"><?php echo $eroare; ?></div>
        <?php } ?>

        <form action="login.php" method="POST" class="p-4 border border-secondary rounded bg-dark">
            <div class="mb-3">
                <label class="form-label text-white">Adresă de email</label>
                <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white">Parolă</label>
                <input type="password" name="parola" class="form-control bg-dark text-white border-secondary" required>
            </div>
            
            <button type="submit" class="btn w-100 mt-4" style="background-color: #6f42c1; color: white;">Autentificare</button>
        </form>
        
        <div class="mt-3 text-center">
            <p>Nu ai cont? <a href="register.php" style="color: #bb86fc;">Înregistrează-te aici</a></p>
            <p>Ai uitat parola? <a href="schimbare_parola.php" style="color: #bb86fc;">Schimba parola</a></p>
            <p><a href="proiect.php" style="color: #bb86fc;">Înapoi la magazin</a></p>
        </div>
    </div>

</body>
</html>