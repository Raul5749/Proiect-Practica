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

$sql_comenzi = "SELECT * FROM comenzi WHERE ID_UTILIZATOR = :id ORDER BY DATA_COMANDA DESC";
$stmt_comenzi = $pdo->prepare($sql_comenzi);
$stmt_comenzi->execute(['id' => $_SESSION['utilizator_id']]);
$comenzi = $stmt_comenzi->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Contul meu - CustomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 text-white" style="background-color: #121212;">
    
    <div class="container mt-5 flex-grow-1" style="max-width: 800px;">
        
        <h2 class="mb-4 text-center" style="color: #6f42c1;">Bună, <?php echo htmlspecialchars($utilizator['USERNAME']); ?>!</h2>
        
        <div class="card border-secondary mb-5" style="background-color: #1f1f1f;">
            <div class="card-body text-center">
                <h5 class="card-title mb-4" style="color: #6f42c1;">Informații cont</h5>
                <p class="card-text text-light"><strong>Nume utilizator:</strong> <?php echo htmlspecialchars($utilizator['USERNAME']); ?></p>
                <p class="card-text text-light mb-4"><strong>Email:</strong> <?php echo htmlspecialchars($utilizator['EMAIL']); ?></p>

                <a href="schimbare_parola.php" class="btn btn-outline-light mb-2">Schimbă parola</a>
                <a href="logout.php" class="btn btn-outline-danger mb-2">Deconectare</a>
                <a href="proiect.php" class="btn btn-outline-light mb-2">Înapoi la magazin</a>
                
                <?php if (isset($_SESSION['ADMIN']) && $_SESSION['ADMIN'] == 1) { ?>
                    <a href="admin.php" class="btn btn-warning fw-bold mb-2">Panou Admin ⚙️</a>
                <?php } ?>
            </div>
        </div>

        <h4 class="mb-3" style="color: #bb86fc;">📦 Istoricul Comenzilor Tale</h4>
        
        <?php if (empty($comenzi)) { ?>
            <div class="alert alert-secondary bg-dark text-white border-secondary text-center">
                Nu ai plasat nicio comandă până acum. <a href="proiect.php" style="color: #bb86fc;">Mergi la magazin!</a>
            </div>
        <?php } else { ?>
            <div class="table-responsive shadow-sm mb-5">
                <table class="table table-dark table-hover table-bordered border-secondary align-middle text-center">
                    <thead class="table-active">
                        <tr>
                            <th>ID Comandă</th>
                            <th>Data Plasării</th>
                            <th>Total Plătit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comenzi as $c) { 
                            $badge_color = 'bg-warning text-dark';
                            if($c['STATUS'] == 'Finalizata') $badge_color = 'bg-success';
                            if($c['STATUS'] == 'Expediata') $badge_color = 'bg-info text-dark';
                            if($c['STATUS'] == 'Anulata') $badge_color = 'bg-danger';
                        ?>
                            <tr>
                                <td class="fw-bold text-start">#<?php echo $c['ID']; ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($c['DATA_COMANDA'])); ?></td>
                                <td class="fw-bold text-success"><?php echo $c['TOTAL_PLATIT']; ?> Lei</td>
                                <td>
                                    <span class="badge <?php echo $badge_color; ?>"><?php echo $c['STATUS'] ?? 'Noua'; ?></span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
        
    </div> <footer style="background-color: #1f1f1f; border-top: 2px solid #6f42c1;" class="text-center text-light py-4 mt-auto w-100">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> <span style="color: #6f42c1; font-weight: bold;">CUSTOM</span><span class="text-white fw-bold">SHOP</span>. Toate drepturile rezervate.</p>
            <p class="small mb-0">Proiect de practică realizat de către Jarda Raul-Nicolae.</p>
        </div>
    </footer>      
</body>
</html>