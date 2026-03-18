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
} else {
    $sql = "SELECT * FROM produse";
    $stmt = $pdo->query($sql); 
}
$produse = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Magazin Custom</title>
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
                    <form class="d-flex me-4">
                        <input class="form-control me-2 bg-dark text-white border-secondary" type="search" placeholder="Căutare...">
                        <button class="btn btn-purple" type="submit">Caută</button>
                    </form>
    
                    <?php if (isset($_SESSION['utilizator_id'])) { ?>
        
                        <a href="contul_meu.php" class="btn btn-outline-light me-2">Contul meu</a>
                        <a href="favorite.php" class="btn btn-outline-danger me-2">❤️ Favorite</a>
                        <a href="cos.php" class="btn btn-success">🛒 Coș</a>
        
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
                            <a class="dropdown-toggle" href="#">
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
                                <img src="imagini/<?php echo $p['IMAGINE']; ?>" class="card-img-top" alt="..." style="height: 200px; object-fit: contain; background-color: #1f1f1f;">
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title"><?php echo htmlspecialchars($p['NUME_PRODUS']); ?></h6>
                                    <p class="price-tag mb-3"><?php echo $p['PRET']; ?> Lei</p>
                                    
                                    <?php if($p['ID_SUBCATEGORIE'] == 1) { // 1 = Tricouri (ID-ul din baza ta) ?>
                                        <select class="form-select form-select-sm mb-3 bg-dark text-white border-secondary">
                                            <option>Alege mărimea</option>
                                            <option>S</option>
                                            <option>M</option>
                                            <option>L</option>
                                            <option>XL</option>
                                        </select>
                                    <?php } ?>

                                    <a href="personalizare.php?id=<?php echo $p['ID']; ?>" class="btn btn-purple btn-sm mt-auto">Personalizează</a>
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