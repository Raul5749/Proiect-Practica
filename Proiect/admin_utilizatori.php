<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== 1) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'sterge' && isset($_GET['id'])) {
    $id_sters = (int)$_GET['id'];
    
    if ($id_sters !== $_SESSION['utilizator_id']) {
        $stmt_del = $pdo->prepare("DELETE FROM utilizatori WHERE ID = ?");
        $stmt_del->execute([$id_sters]);
    }
    header('Location: admin_utilizatori.php');
    exit;
}

$stmt_count = $pdo->query("SELECT COUNT(*) FROM comenzi WHERE STATUS = 'Noua' OR STATUS IS NULL");
$comenzi_noi = $stmt_count->fetchColumn();
$sql_users = "SELECT u.*, COUNT(c.ID) as nr_comenzi 
              FROM utilizatori u 
              LEFT JOIN comenzi c ON u.ID = c.ID_UTILIZATOR 
              GROUP BY u.ID 
              ORDER BY u.ID DESC";
$utilizatori = $pdo->query($sql_users)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Utilizatori - Admin</title>
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
                <a href="admin_comenzi.php">🛒 Comenzi primite 
                    <?php if ($comenzi_noi > 0) { ?>
                        <span class="badge bg-danger float-end"><?php echo $comenzi_noi; ?></span>
                    <?php } ?>
                </a>
                <a href="admin_utilizatori.php" class="active">👥 Utilizatori</a>
                <a href="#">⚙️ Setări magazin</a>
            </div>

            <div class="col-md-9 col-lg-10 p-4">
                
                <h2 class="text-danger mb-4">👥 Gestionare Utilizatori</h2>

                <div class="table-responsive shadow-lg">
                    <table class="table table-dark table-hover table-bordered border-secondary align-middle text-center">
                        <thead class="table-active">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Comenzi Plasate</th>
                                <th>Data Înregistrării</th>
                                <th>Acțiune</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilizatori as $u) { ?>
                                <tr>
                                    <td class="text-start fw-bold">#<?php echo $u['ID']; ?></td>
                                    <td class="text-start fw-bold"><?php echo htmlspecialchars($u['USERNAME']); ?></td>
                                    <td><?php echo htmlspecialchars($u['EMAIL']); ?></td>
                                    <td>
                                        <?php if (isset($u['ADMIN']) && $u['ADMIN'] == 1) { ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Client</span>
                                        <?php } ?>
                                    </td>
                                    <td class="fw-bold text-info"><?php echo $u['nr_comenzi']; ?> comenzi</td>
                                    <td><?php echo isset($u['CREATED_AT']) ? date('d.m.Y', strtotime($u['CREATED_AT'])) : '-'; ?></td>
                                    <td>
                                        <?php if ($u['ID'] == $_SESSION['utilizator_id']) { ?>
                                            <span class="badge bg-success">Ești tu!</span>
                                        <?php } else { ?>
                                            <a href="admin_utilizatori.php?action=sterge&id=<?php echo $u['ID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sigur vrei să ștergi acest utilizator definitiv?');">Șterge 🗑️</a>
                                        <?php } ?>
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