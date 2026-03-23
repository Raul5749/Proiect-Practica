<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== 1) {
    header('Location: login.php');
    exit;
}

$stmt_subcat = $pdo->query("SELECT * FROM subcategorii");
$subcategorii = $stmt_subcat->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nume = trim($_POST['nume_produs']);
    $pret = (float)$_POST['pret'];
    $stoc = (int)$_POST['stoc'];
    $id_subcat = (int)$_POST['id_subcategorie'];
    $marime = trim($_POST['marime']);
    $descriere = trim($_POST['descriere']);
    $imagine = $_FILES['imagine']['name'];
    $temp_name = $_FILES['imagine']['tmp_name'];
    $folder_destinatie = "imagini/" . basename($imagine);

    if (move_uploaded_file($temp_name, $folder_destinatie)) {
        $sql = "INSERT INTO produse (NUME_PRODUS, ID_SUBCATEGORIE, PRET, STOC, MARIME, IMAGINE, DESCRIERE) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nume, $id_subcat, $pret, $stoc, $marime, $imagine, $descriere]);
        header('Location: admin.php');
        exit;
    } else {
        $eroare = "Eroare la încărcarea imaginii!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adaugă Produs - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .navbar-admin { background-color: #000000 !important; border-bottom: 2px solid #dc3545; }
        .form-control, .form-select { background-color: #1f1f1f; color: white; border: 1px solid #333; }
        .form-control:focus, .form-select:focus { background-color: #2b2b2b; color: white; border-color: #dc3545; box-shadow: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-admin mb-4 p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-danger" href="admin.php">⚙️ ADMIN<span class="text-white">PANEL</span></a>
            <a href="admin.php" class="btn btn-outline-light btn-sm">Înapoi la Dashboard</a>
        </div>
    </nav>

    <div class="container" style="max-width: 800px;">
        <h2 class="text-danger mb-4">Adaugă Produs Nou 📦</h2>

        <?php if(isset($eroare)) { echo "<div class='alert alert-danger'>$eroare</div>"; } ?>

        <div class="card bg-dark border-secondary p-4 shadow">
            <form action="adauga_produs.php" method="POST" enctype="multipart/form-data">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-light">Nume Produs</label>
                        <input type="text" name="nume_produs" class="form-control" placeholder="Ex: Tricou Oversized Alb" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-light">Preț (Lei)</label>
                        <input type="number" step="0.01" name="pret" class="form-control" placeholder="Ex: 85.50" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-light">Stoc (Buc)</label>
                        <input type="number" name="stoc" class="form-control" placeholder="Ex: 50" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-light">Subcategorie</label>
                        <select name="id_subcategorie" class="form-select" required>
                            <option value="">Alege o subcategorie...</option>
                            <?php foreach ($subcategorii as $sub) { ?>
                                <option value="<?php echo $sub['ID']; ?>">
                                    <?php echo htmlspecialchars($sub['NUME_SUBCATEGORIE']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-light">Mărime (Opțional)</label>
                        <input type="text" name="marime" class="form-control" placeholder="Ex: S, M, L / Universală">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-light">Imagine Produs</label>
                    <input type="file" name="imagine" class="form-control bg-dark text-white" accept="image/*" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-light">Descriere Produs</label>
                    <textarea name="descriere" class="form-control" rows="4" placeholder="Scrie aici o descriere atrăgătoare pentru produs..." required></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100 fs-5 fw-bold">Salvează Produsul ✅</button>
            </form>
        </div>
    </div>

</body>
</html>