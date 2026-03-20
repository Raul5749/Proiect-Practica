<?php
session_start();
require_once 'config.php';

// ATENȚIE: În viitor, aici vom pune o verificare ca doar tu (adminul) să poți accesa pagina.
// if (!isset($_SESSION['admin_logat'])) { header('Location: login.php'); exit; }

// Preluăm toate produsele din baza de date pentru a le afișa în tabel
$stmt = $pdo->query("SELECT * FROM produse ORDER BY ID DESC");
$produse = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - CustomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .navbar-admin { background-color: #000000 !important; border-bottom: 2px solid #dc3545; } /* Roșu pentru admin */
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
                <a href="admin.php" class="active">📦 Produsele mele</a>
                <a href="#">🛒 Comenzi primite <span class="badge bg-danger float-end">0</span></a>
                <a href="#">👥 Utilizatori</a>
                <a href="#">⚙️ Setări magazin</a>
            </div>

            <div class="col-md-9 col-lg-10 p-4">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-danger">Gestionare Produse</h2>
                    <a href="#" class="btn btn-success fw-bold">+ Adaugă Produs Nou</a>
                </div>

                <div class="table-responsive shadow-lg">
                    <table class="table table-dark table-hover table-bordered border-secondary align-middle text-center">
                        <thead class="table-active">
                            <tr>
                                <th>ID</th>
                                <th>Imagine</th>
                                <th>Nume Produs</th>
                                <th>Preț</th>
                                <th>Stoc</th>
                                <th>Mărime</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produse as $p) { ?>
                                <tr>
                                    <td class="text-start fw-bold">#<?php echo $p['ID']; ?></td>
                                    <td>
                                        <img src="imagini/<?php echo $p['IMAGINE']; ?>" width="50" height="50" style="object-fit: contain; background: white; border-radius: 5px;">
                                    </td>
                                    <td class="text-start fw-bold"><?php echo htmlspecialchars($p['NUME_PRODUS']); ?></td>
                                    <td class="text-success fw-bold"><?php echo $p['PRET']; ?> Lei</td>
                                    <td>
                                        <?php if ($p['STOC'] < 10) { ?>
                                            <span class="badge bg-danger"><?php echo $p['STOC']; ?> buc (Scăzut!)</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary"><?php echo $p['STOC']; ?> buc</span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo !empty($p['MARIME']) ? $p['MARIME'] : '-'; ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Editează ✏️</a>
                                        <a href="#" class="btn btn-sm btn-danger">Șterge 🗑️</a>
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