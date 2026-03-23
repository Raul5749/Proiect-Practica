<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== 1) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id_comanda = (int)$_POST['id_comanda'];
    $status_nou = $_POST['status_nou'];

    $stmt_update = $pdo->prepare("UPDATE comenzi SET STATUS = ? WHERE ID = ?");
    $stmt_update->execute([$status_nou, $id_comanda]);
    
    header('Location: admin_comenzi.php');
    exit;
}

$stmt_comenzi = $pdo->query("SELECT * FROM comenzi ORDER BY DATA_COMANDA DESC");
$comenzi = $stmt_comenzi->fetchAll(PDO::FETCH_ASSOC);

$stmt_count = $pdo->query("SELECT COUNT(*) FROM comenzi WHERE STATUS = 'Noua' OR STATUS IS NULL");
$comenzi_noi = $stmt_count->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Comenzi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .navbar-admin { background-color: #000000 !important; border-bottom: 2px solid #dc3545; }
        .sidebar { background-color: #1f1f1f; min-height: 100vh; padding: 20px; border-right: 1px solid #333; }
        .sidebar a { color: #e0e0e0; text-decoration: none; display: block; padding: 12px; border-radius: 5px; margin-bottom: 5px; transition: 0.3s;}
        .sidebar a:hover, .sidebar a.active { background-color: #dc3545; color: white; }
        .table-dark { background-color: #1f1f1f; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-admin mb-0 p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-danger" href="admin.php">⚙️ ADMIN<span class="text-white">PANEL</span></a>
            <div class="d-flex">
                <span class="navbar-text me-3">Salut, Boss! 👋</span>
                <a href="proiect.php" class="btn btn-outline-light btn-sm">Înapoi la magazin</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-3 col-lg-2 sidebar shadow">
                <h6 class="text-muted text-uppercase mb-3 mt-2">Meniu</h6>
                <a href="admin.php">📦 Produsele mele</a>
                <a href="admin_comenzi.php" class="active">🛒 Comenzi primite 
                    <?php if ($comenzi_noi > 0) { ?>
                        <span class="badge bg-danger float-end"><?php echo $comenzi_noi; ?></span>
                    <?php } ?>
                </a>
                <a href="admin_utilizatori.php">👥 Utilizatori</a>
                <a href="#">⚙️ Setări magazin</a>
            </div>

            <div class="col-md-9 col-lg-10 p-4">
                
                <h2 class="text-danger mb-4">🛒 Gestionare Comenzi</h2>

                <div class="table-responsive shadow-lg">
                    <table class="table table-dark table-hover table-bordered border-secondary align-middle text-center">
                        <thead class="table-active">
                            <tr>
                                <th>ID</th>
                                <th>Client / Contact</th>
                                <th>Adresa</th>
                                <th>Total</th>
                                <th>Data</th>
                                <th>Status / Acțiune</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comenzi as $c) { 
                                $culoare_status = 'bg-warning text-dark';
                                $status_actual = $c['STATUS'] ?? 'Noua';
                                if ($status_actual == 'În Procesare') $culoare_status = 'bg-info text-dark';
                                if ($status_actual == 'Expediata') $culoare_status = 'bg-primary';
                                if ($status_actual == 'Finalizata') $culoare_status = 'bg-success';
                                if ($status_actual == 'Anulata') $culoare_status = 'bg-danger';
                            ?>
                                <tr>
                                    <td class="text-start fw-bold">#<?php echo $c['ID']; ?></td>
                                    <td class="text-start">
                                        <strong><?php echo htmlspecialchars($c['NUME_CLIENT']); ?></strong><br>
                                        <small class="text-start">📞 <?php echo htmlspecialchars($c['TELEFON']); ?></small>
                                    </td>
                                    <td class="text-start" style="font-size: 0.9em; max-width: 200px;">
                                        <?php echo htmlspecialchars($c['ADRESA']); ?>
                                    </td>
                                    <td class="fw-bold text-success"><?php echo $c['TOTAL_PLATIT']; ?> Lei</td>
                                    <td><?php echo date('d.m.Y - H:i', strtotime($c['DATA_COMANDA'])); ?></td>
                                    
                                    <td>
                                        <a href="admin_detalii_comanda.php?id=<?php echo $c['ID']; ?>" class="btn btn-sm btn-info w-100 fw-bold mb-2">👁️ Vezi Detalii & Produse</a>

                                        <form method="POST" class="d-flex flex-column align-items-center justify-content-center">
                                            <input type="hidden" name="id_comanda" value="<?php echo $c['ID']; ?>">
                                            <div class="input-group input-group-sm">
                                                <select name="status_nou" class="form-select bg-dark text-white border-secondary">
                                                    <option value="Noua" <?php if($status_actual=='Noua') echo 'selected'; ?>>Nouă</option>
                                                    <option value="În Procesare" <?php if($status_actual=='În Procesare') echo 'selected'; ?>>În Procesare</option>
                                                    <option value="Expediata" <?php if($status_actual=='Expediata') echo 'selected'; ?>>Expediată</option>
                                                    <option value="Finalizata" <?php if($status_actual=='Finalizata') echo 'selected'; ?>>Finalizată</option>
                                                    <option value="Anulata" <?php if($status_actual=='Anulata') echo 'selected'; ?>>Anulată</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-outline-light">Salvează</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</body>
</html>