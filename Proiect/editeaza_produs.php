<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== 1) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id_produs = (int)$_GET['id'];

$stmt_produs = $pdo->prepare("SELECT * FROM produse WHERE ID = ?");
$stmt_produs->execute([$id_produs]);
$produs = $stmt_produs->fetch(PDO::FETCH_ASSOC);

if (!$produs) {
    die("Produsul nu a fost găsit!");
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
    $imagine = $produs['IMAGINE']; 
    if (!empty($_FILES['imagine']['name'])) {
        $nume_poza_noua = $_FILES['imagine']['name'];
        $temp_name = $_FILES['imagine']['tmp_name'];
        $folder_destinatie = "imagini/" . basename($nume_poza_noua);

        if (move_uploaded_file($temp_name, $folder_destinatie)) {
            $imagine = $nume_poza_noua; 
        }
    }

    $sql_update = "UPDATE produse SET NUME_PRODUS = ?, ID_SUBCATEGORIE = ?, PRET = ?, STOC = ?, MARIME = ?, IMAGINE = ?, DESCRIERE = ? WHERE ID = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([$nume, $id_subcat, $pret, $stoc, $marime, $imagine, $descriere, $id_produs]);
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editează Produs - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .navbar-admin { background-color: #000000 !important; border-bottom: 2px solid #dc3545; }
        .form-control, .form-select { background-color: #1f1f1f; color: white; border: 1px solid #333; }
        .form-control:focus, .form-select:focus { background-color: #2b2b2b; color: white; border-color: #0d6efd; box-shadow: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-admin mb-4 p-3 border-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="admin.php">⚙️ ADMIN<span class="text-white">PANEL</span></a>
            <a href="admin.php" class="btn btn-outline-light btn-sm">Înapoi la Dashboard</a>
        </div>
    </nav>

    <div class="container" style="max-width: 800px;">
        <h2 class="text-primary mb-4">Editează Produsul ✏️</h2>

        <div class="card bg-dark border-secondary p-4 shadow">
            <form action="editeaza_produs.php?id=<?php echo $produs['ID']; ?>" method="POST" enctype="multipart/form-data">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-light">Nume Produs</label>
                        <input type="text" name="nume_produs" class="form-control" value="<?php echo htmlspecialchars($produs['NUME_PRODUS']); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-light">Preț (Lei)</label>
                        <input type="number" step="0.01" name="pret" class="form-control" value="<?php echo $produs['PRET']; ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-light">Stoc (Buc)</label>
                        <input type="number" name="stoc" class="form-control" value="<?php echo $produs['STOC']; ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-light">Subcategorie</label>
                        <select name="id_subcategorie" class="form-select" required>
                            <?php foreach ($subcategorii as $sub) { ?>
                                <option value="<?php echo $sub['ID']; ?>" <?php if($sub['ID'] == $produs['ID_SUBCATEGORIE']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($sub['NUME_SUBCATEGORIE']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-light">Mărime (Opțional)</label>
                        <input type="text" name="marime" class="form-control" value="<?php echo htmlspecialchars($produs['MARIME'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <div class="col-md-3 text-center">
                        <p class="text-muted mb-1 small">Imaginea actuală</p>
                        <img src="imagini/<?php echo $produs['IMAGINE']; ?>" width="80" height="80" style="object-fit: contain; background: white; border-radius: 5px;">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-bold text-light">Schimbă imaginea (lasă gol pentru a o păstra pe cea veche)</label>
                        <input type="file" name="imagine" class="form-control bg-dark text-white" accept="image/*">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-light">Descriere Produs</label>
                    <textarea name="descriere" class="form-control" rows="4" required><?php echo htmlspecialchars($produs['DESCRIERE'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 fs-5 fw-bold">Salvează Modificările 💾</button>
            </form>
        </div>
    </div>

</body>
</html>