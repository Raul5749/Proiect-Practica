<?php
session_start();
require_once 'config.php';

$sql_categorii = "SELECT * FROM categorii";
$stmt_categorii = $pdo->query($sql_categorii);
$categorii = $stmt_categorii->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['subcategorie'])) {
    $sql = "SELECT * FROM produse WHERE ID_SUBCATEGORIE = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $_GET['subcategorie']]);
}
elseif(isset($_GET['categorie'])) {
    $sql = "SELECT p.* FROM produse p JOIN subcategorii s ON p.ID_SUBCATEGORIE = s.ID WHERE s.ID_CATEGORIE = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $_GET['categorie']]);
}
elseif (isset($_GET['cautare'])) {
    $termen = '%' . $_GET['cautare'] . '%';
    $sql = "SELECT * FROM produse WHERE NUME_PRODUS LIKE :termen";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['termen' => $termen]);
}
else {
    $sql = "SELECT * FROM produse";
    $stmt = $pdo->query($sql); 
}
$produse = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>CustomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .navbar { background-color: #1f1f1f !important; border-bottom: 2px solid #6f42c1; }
        .sidebar { background-color: #1f1f1f; min-height: 100vh; padding: 20px; border-right: 1px solid #333; }
        .sidebar a { color: #e0e0e0; text-decoration: none; display: block; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background-color: #6f42c1; color: white; }
        .card { background-color: #1f1f1f; border: 1px solid #333; color: white; transition: 0.3s; }
        .card:hover { border-color: #6f42c1; transform: translateY(-5px); }
        .btn-purple { background-color: #6f42c1; color: white; border: none; }
        .btn-purple:hover { background-color: #59339d; color: white; }
        .price-tag { color: #bb86fc; font-weight: bold; font-size: 1.2rem; }
        .dropdown-menu { background-color: #1f1f1f; border: 1px solid #6f42c1; }
        .dropdown-item { color: white; }
        .dropdown-item:hover { background-color: #6f42c1; }
        .sidebar .dropdown:hover .dropdown-menu { display: block; margin-top: 0; }
        .stea-galbena { color: #ffc107; }
        .recenzie-box { background-color: #2c2c2c; border-radius: 8px; padding: 10px; margin-bottom: 10px; border-left: 3px solid #6f42c1;}
    </style>
</head>
<body>

    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="proiect.php" style="color: #6f42c1;">CUSTOM<span class="text-white">SHOP</span></a>
                <div class="d-flex align-items-center">
                    <form class="d-flex me-4" action="proiect.php" method="GET">
                        <input class="form-control me-2 bg-dark text-white border-secondary" type="search" name="cautare" placeholder="Căutare...">
                        <button class="btn btn-purple" type="submit">Caută</button>
                    </form>
    
                    <?php if (isset($_SESSION['utilizator_id'])) { ?>
                        <a href="contul_meu.php" class="btn btn-outline-light me-2">Contul meu</a>
                        <a href="favorite.php" class="btn btn-outline-danger me-2">❤️</a>
                        <a href="cos.php" class="btn btn-success">🛒</a>
                    <?php } else { ?>
                        <a href="login.php" class="btn btn-outline-light me-2">Autentificare</a>
                        <a href="register.php" class="btn btn-purple">Înregistrare</a>
                    <?php } ?>
                </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2">
                <div class="sidebar shadow">
                    <h5 class="mb-3" style="color: #6f42c1;">Categorii</h5>
                    <?php foreach($categorii as $categorie) { 
                        $stmt_sub = $pdo->prepare("SELECT * FROM subcategorii WHERE ID_CATEGORIE = ?");
                        $stmt_sub->execute([$categorie['ID']]);
                        $subs = $stmt_sub->fetchAll();
                    ?>
                        <div class="dropdown mb-2">
                            <a class="dropdown-toggle text-decoration-none" href="proiect.php?categorie=<?php echo $categorie['ID']; ?>">
                                <?php echo $categorie['NUME_CATEGORIE']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach($subs as $s) { ?>
                                    <li><a class="dropdown-item" href="proiect.php?subcategorie=<?php echo $s['ID']; ?>"><?php echo $s['NUME_SUBCATEGORIE']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="row">
                    <?php foreach($produse as $p) { 
                        $stmt_rec = $pdo->prepare("SELECT * FROM recenzii WHERE ID_PRODUS = ? ORDER BY DATA_RECENZIE DESC");
                        $stmt_rec->execute([$p['ID']]);
                        $recenzii = $stmt_rec->fetchAll(PDO::FETCH_ASSOC);
                        $nr_recenzii = count($recenzii);
                        $medie_note = 0;
                        if ($nr_recenzii > 0) {
                            $suma = 0;
                            foreach($recenzii as $r) { $suma += $r['NOTA']; }
                            $medie_note = round($suma / $nr_recenzii, 1);
                        }
                    ?>
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card h-100 shadow">
                                <img src="imagini/<?php echo $p['IMAGINE']; ?>" class="card-img-top" alt="..." 
                                    style="height: 200px; object-fit: contain; background-color: white; cursor: pointer;"
                                    data-bs-toggle="modal" data-bs-target="#modalProdus<?php echo $p['ID']; ?>">
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title"><?php echo htmlspecialchars($p['NUME_PRODUS']); ?></h6>
                                    
                                    <div class="mb-2">
                                        <?php if ($nr_recenzii > 0) { ?>
                                            <span class="stea-galbena fw-bold">⭐ <?php echo $medie_note; ?></span> 
                                            <small class="text-start">(<?php echo $nr_recenzii; ?> păreri)</small>
                                        <?php } else { ?>
                                            <small class="text-start">Fără recenzii încă</small>
                                        <?php } ?>
                                    </div>

                                    <p class="price-tag mb-3"><?php echo $p['PRET']; ?> Lei</p>
                                    
                                    <form action="cos.php" method="GET" class="mt-auto">
                                        <input type="hidden" name="adauga" value="<?php echo $p['ID']; ?>">
                                        
                                        <?php if($p['ID_SUBCATEGORIE'] == 1 || $p['ID_SUBCATEGORIE']==2) { ?>
                                            <select name="marime" class="form-select form-select-sm mb-3 bg-dark text-white border-secondary">
                                                <option value="S">S</option>
                                                <option value="M" selected>M</option>
                                                <option value="L">L</option>
                                                <option value="XL">XL</option>
                                            </select>
                                        <?php } else { ?>
                                            <input type="hidden" name="marime" value="Standard">
                                        <?php } ?>

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <button type="submit" class="btn btn-success btn-sm w-100 me-2">Adaugă în coș 🛒</button>
                                            <a href="adauga_favorite.php?id=<?php echo $p['ID']; ?>" class="btn btn-outline-danger btn-sm" title="Adaugă la favorite">❤️</a>
                                        </div>
                                        <a href="personalizare.php?id=<?php echo $p['ID']; ?>" class="btn btn-purple btn-sm w-100">Personalizează</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modalProdus<?php echo $p['ID']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content bg-dark text-white border-secondary">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title" style="color: #bb86fc;"><?php echo htmlspecialchars($p['NUME_PRODUS']); ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Închide"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-5 mb-3 text-center">
                                                <img src="imagini/<?php echo $p['IMAGINE']; ?>" class="img-fluid rounded mb-3" style="background-color: white; width: 100%; max-height: 400px; object-fit: contain;">
                                                <h3 class="price-tag mb-2"><?php echo $p['PRET']; ?> Lei</h3>
                                                
                                                <form action="cos.php" method="GET">
                                                    <input type="hidden" name="adauga" value="<?php echo $p['ID']; ?>">
                                                    <?php if($p['ID_SUBCATEGORIE'] == 1 || $p['ID_SUBCATEGORIE'] == 2) { ?>
                                                        <select name="marime" class="form-select mb-3 bg-dark text-white border-secondary w-75 mx-auto">
                                                            <option value="S">Mărimea S</option>
                                                            <option value="M" selected>Mărimea M</option>
                                                            <option value="L">Mărimea L</option>
                                                            <option value="XL">Mărimea XL</option>
                                                        </select>
                                                    <?php } else { ?>
                                                        <input type="hidden" name="marime" value="Standard">
                                                    <?php } ?>
                                                    <div class="d-grid gap-2 w-75 mx-auto">
                                                        <button type="submit" class="btn btn-success">Adaugă în coș 🛒</button>
                                                        <a href="personalizare.php?id=<?php echo $p['ID']; ?>" class="btn btn-purple">Mergi la personalizare</a>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <div class="col-md-7 border-start border-secondary">
                                                <h5 class="text-info border-bottom border-secondary pb-2">Detalii Produs</h5>
                                                <p class="mb-4 text-start"><?php echo htmlspecialchars($p['DESCRIERE']); ?></p>

                                                <h5 class="text-warning mt-4">⭐ Părerile Clienților (<?php echo $nr_recenzii; ?>)</h5>
                                                
                                                <div style="max-height: 250px; overflow-y: auto; padding-right: 10px;" class="mb-3">
                                                    <?php if ($nr_recenzii > 0) { 
                                                        foreach ($recenzii as $r) { ?>
                                                            <div class="recenzie-box shadow-sm">
                                                                <div class="d-flex justify-content-between">
                                                                    <strong class="text-light">👤 <?php echo htmlspecialchars($r['NUME_CLIENT']); ?></strong>
                                                                    <span class="stea-galbena">
                                                                        <?php echo str_repeat('★', $r['NOTA']) . str_repeat('☆', 5 - $r['NOTA']); ?>
                                                                    </span>
                                                                </div>
                                                                <p class="mb-1 mt-1 small fst-italic text-white">"<?php echo htmlspecialchars($r['COMENTARIU']); ?>"</p>
                                                                <small class="text-muted" style="font-size: 0.7em;"><?php echo date('d.m.Y H:i', strtotime($r['DATA_RECENZIE'])); ?></small>
                                                            </div>
                                                        <?php } 
                                                    } else { ?>
                                                        <p class="text-muted small">Fii primul care lasă o recenzie!</p>
                                                    <?php } ?>
                                                </div>

                                                <div class="card bg-dark border-secondary p-3 mt-3">
                                                    <h6 class="text-light mb-3">Lasă o recenzie:</h6>
                                                    <form action="adauga_recenzie.php" method="POST">
                                                        <input type="hidden" name="id_produs" value="<?php echo $p['ID']; ?>">
                                                        <div class="row mb-2">
                                                            <div class="col-8">
                                                                <input type="text" name="nume_client" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Numele tău (Opțional)">
                                                            </div>
                                                            <div class="col-4">
                                                                <select name="nota" class="form-select form-select-sm bg-dark text-white border-secondary" required>
                                                                    <option value="5">5 Stele ⭐</option>
                                                                    <option value="4">4 Stele ⭐</option>
                                                                    <option value="3">3 Stele ⭐</option>
                                                                    <option value="2">2 Stele ⭐</option>
                                                                    <option value="1">1 Stea ⭐</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <textarea name="comentariu" class="form-control form-control-sm bg-dark text-white border-secondary mb-2" rows="2" placeholder="Scrie părerea ta aici..." required></textarea>
                                                        <button type="submit" class="btn btn-sm btn-outline-warning w-100">Trimite Recenzia</button>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>  
    <footer style="background-color: #1f1f1f; border-top: 2px solid #6f42c1;" class="text-center text-light py-4 mt-5">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> <span style="color: #6f42c1; font-weight: bold;">CUSTOM</span><span class="text-white fw-bold">SHOP</span>. Toate drepturile rezervate.</p>
            <p class="small mb-0">Proiect de practică realizat de către Jarda Raul-Nicolae.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>