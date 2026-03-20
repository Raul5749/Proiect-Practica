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
        .sidebar .dropdown:hover .dropdown-menu 
        {
            display: block;
            margin-top: 0; 
        }
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
                    <?php foreach($produse as $p) { ?>
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card h-100 shadow">
                                <img src="imagini/<?php echo $p['IMAGINE']; ?>" class="card-img-top" alt="..." 
                                    style="height: 200px; object-fit: contain; background-color: white; cursor: pointer;"
                                    data-bs-toggle="modal" data-bs-target="#modalProdus<?php echo $p['ID']; ?>">
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title"><?php echo htmlspecialchars($p['NUME_PRODUS']); ?></h6>
                                    <p class="price-tag mb-3"><?php echo $p['PRET']; ?> Lei</p>
                                    
                                    <?php if($p['ID_SUBCATEGORIE'] == 1 || $p['ID_SUBCATEGORIE']==2) { ?>
                                        <select class="form-select form-select-sm mb-3 bg-dark text-white border-secondary">
                                            <option>Alege mărimea</option>
                                            <option>S</option>
                                            <option>M</option>
                                            <option>L</option>
                                            <option>XL</option>
                                        </select>
                                    <?php } ?>

                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <a href="personalizare.php?id=<?php echo $p['ID']; ?>" class="btn btn-purple btn-sm">Personalizează</a>
                                        <a href="adauga_favorite.php?id=<?php echo $p['ID']; ?>" class="btn btn-outline-danger btn-sm" title="Adaugă la favorite">❤️</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modalProdus<?php echo $p['ID']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content bg-dark text-white border-secondary">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title text-purple" style="color: #6f42c1;"><?php echo htmlspecialchars($p['NUME_PRODUS']); ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Închide"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <img src="imagini/<?php echo $p['IMAGINE']; ?>" class="img-fluid rounded" style="background-color: white; width: 100%; object-fit: contain;" alt="...">
                                            </div>
                                            <div class="col-md-6">
                                                <h3 class="price-tag mb-4"><?php echo $p['PRET']; ?> Lei</h3>
                                                <p><?php echo htmlspecialchars($p['DESCRIERE']); ?></p>
                                                
                                                <?php if($p['ID_SUBCATEGORIE'] == 1 || $p['ID_SUBCATEGORIE'] == 2) { ?>
                                                    <label class="mb-2">Mărime:</label>
                                                    <select class="form-select mb-4 bg-dark text-white border-secondary">
                                                        <option>S</option>
                                                        <option>M</option>
                                                        <option>L</option>
                                                        <option>XL</option>
                                                    </select>
                                                <?php } ?>
                                                
                                                <div class="d-grid gap-2">
                                                    <a href="cos.php?adauga=<?php echo $p['ID']; ?>" class="btn btn-success btn-lg">Adaugă în coș 🛒</a>
                                                    <a href="personalizare.php?id=<?php echo $p['ID']; ?>" class="btn btn-purple">Mergi la personalizare</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>