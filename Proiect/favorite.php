<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['utilizator_id'])) {
    header("Location: login.php");
    exit;
}

$id_utilizator = $_SESSION['utilizator_id'];

$sql = "SELECT produse.* FROM produse 
        JOIN favorite ON produse.ID = favorite.ID_PRODUS 
        WHERE favorite.ID_UTILIZATOR = :id_user";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_user' => $id_utilizator]);
$produse_favorite = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Favorite - CustomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .navbar { background-color: #1f1f1f !important; border-bottom: 2px solid #6f42c1; }
        .card { background-color: #1f1f1f; border: 1px solid #333; color: white; transition: 0.3s; }
        .card:hover { border-color: #6f42c1; transform: translateY(-5px); }
        .btn-purple { background-color: #6f42c1; color: white; border: none; }
        .btn-purple:hover { background-color: #59339d; color: white; }
        .price-tag { color: #bb86fc; font-weight: bold; font-size: 1.2rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="proiect.php" style="color: #6f42c1;">CUSTOM<span class="text-white">SHOP</span></a>
            <div class="d-flex align-items-center">
                <a href="proiect.php" class="btn btn-outline-light me-2">Înapoi la magazin</a>
                <a href="contul_meu.php" class="btn btn-outline-light me-2">Contul meu</a>
                <a href="cos.php" class="btn btn-success">🛒 Coș</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4" style="color: #6f42c1;">❤️ Produsele tale favorite</h2>
        
        <?php if (count($produse_favorite) > 0) { ?>
            <div class="row">
                <?php foreach($produse_favorite as $p) { ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 shadow">
                            <img src="imagini/<?php echo $p['IMAGINE']; ?>" class="card-img-top p-2" alt="..." style="height: 200px; object-fit: contain; background-color: white;">
                            
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?php echo htmlspecialchars($p['NUME_PRODUS']); ?></h6>
                                <p class="price-tag mb-3"><?php echo $p['PRET']; ?> Lei</p>
                                
                                <div class="d-flex justify-content-between mt-auto">
                                    <a href="personalizare.php?id=<?php echo $p['ID']; ?>" class="btn btn-purple btn-sm">Personalizează</a>        
                                    <a href="stergere_favorite.php?id=<?php echo $p['ID']; ?>" class="btn btn-outline-danger btn-sm" title="Șterge din favorite">❌</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-secondary bg-dark text-white border-secondary">
                Nu ai adăugat niciun produs la favorite încă. 
                <a href="proiect.php" style="color: #bb86fc;">Întoarce-te la magazin</a> pentru a explora.
            </div>
        <?php } ?>
    </div>
     <footer style="background-color: #1f1f1f; border-top: 2px solid #6f42c1;" class="text-center text-light py-4 mt-5">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> <span style="color: #6f42c1; font-weight: bold;">CUSTOM</span><span class="text-white fw-bold">SHOP</span>. Toate drepturile rezervate.</p>
            <p class="small mb-0">Proiect de practică realizat de către Jarda Raul-Nicolae.</p>
        </div>
    </footer>
</body>
</html>