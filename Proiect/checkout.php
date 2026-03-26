<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['cos'])) {
    header('Location: proiect.php');
    exit;
}

$mesaj_succes = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nume = trim($_POST['nume']);
    $telefon = trim($_POST['telefon']);
    $adresa = trim($_POST['adresa']);
    
    $total = 0;
    foreach ($_SESSION['cos'] as $item) {
        $stmt = $pdo->prepare("SELECT PRET FROM produse WHERE ID = ?");
        $stmt->execute([$item['id']]);
        $p = $stmt->fetch();
        $total += $p['PRET'] * $item['cantitate'];
    }
    if (isset($_SESSION['cupon'])) {
        $reducere = ($total * $_SESSION['cupon']['procent']) / 100;
        $total = $total - $reducere;
    }
    $id_utilizator = isset($_SESSION['utilizator_id']) ? $_SESSION['utilizator_id'] : null;
    $stmt_cmd = $pdo->prepare("INSERT INTO comenzi (ID_UTILIZATOR, NUME_CLIENT, TELEFON, ADRESA, TOTAL_PLATIT) VALUES (?, ?, ?, ?, ?)");
    $stmt_cmd->execute([$id_utilizator, $nume, $telefon, $adresa, $total]);
    $id_comanda = $pdo->lastInsertId();

    foreach ($_SESSION['cos'] as $item) {
        $stmt_p = $pdo->prepare("SELECT PRET FROM produse WHERE ID = ?");
        $stmt_p->execute([$item['id']]);
        $p = $stmt_p->fetch();

        $marime = isset($item['marime']) ? $item['marime'] : ''; 
        $stmt_det = $pdo->prepare("INSERT INTO comenzi_produse (ID_COMANDA, ID_PRODUS, CANTITATE, PRET_UNITAR, TEXT_PERSONALIZAT, CULOARE_TEXT, MARIME_ALEASA) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_det->execute([$id_comanda, $item['id'], $item['cantitate'], $p['PRET'], $item['text'], $item['culoare'], $marime]);

        $stmt_stoc = $pdo->prepare("UPDATE produse SET STOC = STOC - ? WHERE ID = ?");
        $stmt_stoc->execute([$item['cantitate'], $item['id']]);
    }

    $_SESSION['cos'] = [];
    unset($_SESSION['cupon']);
    $mesaj_succes = "🎉 Comanda a fost plasată cu succes! ID Comandă: #$id_comanda";
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Finalizare Comandă</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #121212; color: white; }</style>
</head>
<body class="p-5" >
    <div class="container" style="max-width: 600px; background-color: #1f1f1f; padding: 30px; border-radius: 10px; border: 1px solid #6f42c1;">
        <h2 class="mb-4" style="color: #bb86fc;">Detalii Livrare</h2>
        
        <?php if($mesaj_succes): ?>
            <div class="alert alert-success fw-bold"><?php echo $mesaj_succes; ?></div>
            <a href="proiect.php" class="btn btn-outline-light mt-3">Întoarce-te la magazin</a>
        <?php else: ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nume și Prenume</label>
                    <input type="text" name="nume" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefon</label>
                    <input type="tel" name="telefon" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Adresa completă</label>
                    <textarea name="adresa" class="form-control bg-dark text-white border-secondary" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100 fs-5">Confirmă și Trimite Comanda</button>
            </form>
        <?php endif; ?>
    </div>
     <footer style="background-color: #1f1f1f; border-top: 2px solid #6f42c1;" class="text-center text-light py-4 mt-5">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> <span style="color: #6f42c1; font-weight: bold;">CUSTOM</span><span class="text-white fw-bold">SHOP</span>. Toate drepturile rezervate.</p>
            <p class="small mb-0">Proiect de practică realizat de către Jarda Raul-Nicolae.</p>
        </div>
    </footer>
</body>
</html>