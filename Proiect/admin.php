<?php
session_start();
require_once 'config.php';
require_once 'Statistici.php';

if (!isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== 1) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM produse ORDER BY ID DESC");
$produse = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt_comenzi = $pdo->query("SELECT * FROM comenzi ORDER BY DATA_COMANDA DESC");
$comenzi = $stmt_comenzi->fetchAll(PDO::FETCH_ASSOC);

$stats = new Statistici($pdo);
$comenzi_noi = $stats->getComenziNoiCount();

$date_vanzari = $stats->getDateGraficVanzari();
$zile_json = $date_vanzari['zile_json'];
$totaluri_json = $date_vanzari['totaluri_json'];

$date_status = $stats->getDateGraficStatus();
$nume_status_json = $date_status['nume_json'];
$numar_status_json = $date_status['numar_json'];
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - CustomShop</title>
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
                <h6 class="text-danger text-uppercase mb-3 mt-2">Meniu</h6>
                <a href="admin.php" class="active">📦 Produsele mele</a>
                <a href="admin_comenzi.php">🛒 Comenzi primite 
                <?php if ($comenzi_noi > 0) { ?>
                    <span class="badge bg-danger float-end"><?php echo $comenzi_noi; ?></span>
                <?php } ?>
                </a>
                <a href="admin_utilizatori.php">👥 Utilizatori</a>
                <a href="admin_setari.php">⚙️ Setări magazin</a>
            </div>
            
            <div class="col-md-9 col-lg-10 p-4">
                
                <div class="row mb-4">
                    <div class="col-lg-8 col-md-12 mb-3">
                        <div class="card bg-dark border-secondary shadow h-100">
                            <div class="card-header border-secondary text-info fw-bold">
                                📈 Încasări pe ultimele 7 zile
                            </div>
                            <div class="card-body">
                                <canvas id="chartVanzari" style="max-height: 250px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-12 mb-3">
                        <div class="card bg-dark border-secondary shadow h-100">
                            <div class="card-header border-secondary text-warning fw-bold">
                                📊 Proporție Status Comenzi
                            </div>
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <canvas id="chartStatus" style="max-height: 250px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-secondary mb-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-danger m-0">📦 Gestionare Produse</h2>
                    <a href="adauga_produs.php" class="btn btn-success fw-bold shadow-sm">+ Adaugă Produs Nou</a>
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
                                        <a href="editeaza_produs.php?id=<?php echo $p['ID']; ?>" class="btn btn-sm btn-primary">Editează ✏️</a>
                                        <a href="sterge_produs.php?id=<?php echo $p['ID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sigur vrei să ștergi acest produs?');">Șterge 🗑️</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxVanzari = document.getElementById('chartVanzari').getContext('2d');
        new Chart(ctxVanzari, {
            type: 'line',
            data: {
                labels: <?php echo $zile_json; ?>,
                datasets: [{
                    label: 'Încasări (Lei)',
                    data: <?php echo $totaluri_json; ?>,
                    borderColor: '#0dcaf0',
                    backgroundColor: 'rgba(13, 202, 240, 0.2)',
                    borderWidth: 3,
                    tension: 0.3, 
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: '#333' } },
                    x: { grid: { color: '#333' } }
                },
                plugins: { legend: { labels: { color: 'white' } } }
            }
        });
        const ctxStatus = document.getElementById('chartStatus').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: <?php echo $nume_status_json; ?>,
                datasets: [{
                    data: <?php echo $numar_status_json; ?>,
                    backgroundColor: [
                        '#ffc107', 
                        '#0d6efd', 
                        '#198754', 
                        '#0dcaf0', 
                        '#dc3545'  
                    ],
                    borderColor: '#1f1f1f',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: 'white' } } }
            }
        });
    </script>
    <footer style="background-color: #1f1f1f; border-top: 2px solid #6f42c1;" class="text-center text-light py-4 mt-auto w-100">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> <span style="color: #6f42c1; font-weight: bold;">CUSTOM</span><span class="text-white fw-bold">SHOP</span>. Toate drepturile rezervate.</p>
            <p class="small mb-0">Proiect de practică realizat de către Jarda Raul-Nicolae.</p>
        </div>
    </footer>
</body>
</html>