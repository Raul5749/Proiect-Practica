<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== 1) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin_comenzi.php');
    exit;
}

$id_comanda = (int)$_GET['id'];

$stmt_cmd = $pdo->prepare("SELECT * FROM comenzi WHERE ID = ?");
$stmt_cmd->execute([$id_comanda]);
$comanda = $stmt_cmd->fetch(PDO::FETCH_ASSOC);

if (!$comanda) {
    die("Comanda nu a fost găsită!");
}

$sql_produse = "SELECT cp.*, p.NUME_PRODUS, p.IMAGINE 
                FROM comenzi_produse cp 
                JOIN produse p ON cp.ID_PRODUS = p.ID 
                WHERE cp.ID_COMANDA = ?";
$stmt_prod = $pdo->prepare($sql_produse);
$stmt_prod->execute([$id_comanda]);
$produse_comandate = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Detalii Comanda #<?php echo $id_comanda; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #121212; color: white; }</style>
</head>
<body class="p-4">

    <div class="container" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger">📦 Detalii Comandă #<?php echo $id_comanda; ?></h2>
            <a href="admin_comenzi.php" class="btn btn-outline-light">Înapoi la Comenzi</a>
        </div>

        <div class="card bg-dark border-secondary p-3 mb-4 shadow">
            <h5 class="text-primary border-bottom border-secondary pb-2">Informații Client</h5>
            <div class="row mt-3">
                <div class="col-md-4" style="color: #e0e0e0;"><strong>👤 Nume:</strong> <br><?php echo htmlspecialchars($comanda['NUME_CLIENT']); ?></div>
                <div class="col-md-4" style="color: #e0e0e0;"><strong>📞 Telefon:</strong> <br><?php echo htmlspecialchars($comanda['TELEFON']); ?></div>
                <div class="col-md-4" style="color: #e0e0e0;"><strong>📅 Data:</strong> <br><?php echo date('d.m.Y - H:i', strtotime($comanda['DATA_COMANDA'])); ?></div>
            </div>
            <div class="row mt-3">
                <div class="col-12" style="color: #e0e0e0;"><strong>📍 Adresă de livrare:</strong> <br><?php echo htmlspecialchars($comanda['ADRESA']); ?></div>
            </div>
        </div>

        <h4 class="text-info mb-3">🛒 Ce trebuie să pui în colet:</h4>
        <div class="table-responsive shadow-lg">
            <table class="table table-dark table-hover table-bordered border-secondary align-middle text-center">
                <thead class="table-active">
                    <tr>
                        <th>Imagine</th>
                        <th class="text-start">Produs</th>
                        <th>Personalizare</th>
                        <th>Cantitate</th>
                        <th>Preț Unitar</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produse_comandate as $p) { 
                        $subtotal = $p['CANTITATE'] * $p['PRET_UNITAR'];
                    ?>
                        <tr>
                            <td>
                                <img src="imagini/<?php echo $p['IMAGINE']; ?>" width="60" height="60" style="object-fit: contain; background: white; border-radius: 5px;">
                            </td>
                            <td class="text-start fw-bold"><?php echo htmlspecialchars($p['NUME_PRODUS']); ?></td>
                            <td class="text-start">
                                <?php if (!empty($p['TEXT_PERSONALIZAT']) && $p['TEXT_PERSONALIZAT'] != 'Textul tău aici') { ?>
                                    <span class="badge bg-primary p-2 mb-1">Text: "<?php echo htmlspecialchars($p['TEXT_PERSONALIZAT']); ?>"</span><br>
                                    <span class="badge bg-secondary">
                                        Culoare: <span style="display:inline-block; width:10px; height:10px; background-color:<?php echo $p['CULOARE_TEXT']; ?>; border-radius:50%; border:1px solid white;"></span> <?php echo $p['CULOARE_TEXT']; ?>
                                    </span>
                                    <?php if (!empty($p['MARIME_ALEASA'])) { ?>
                                        <br><span class="badge bg-info text-dark mt-1">Mărime: <?php echo htmlspecialchars($p['MARIME_ALEASA']); ?></span>
                                        <?php } ?>
                                <?php } else { ?>
                                    <span class="text-start fst-italic">Standard (fără text)</span>
                                <?php } ?>
                            </td>
                            <td class="fs-5 fw-bold"><?php echo $p['CANTITATE']; ?> buc</td>
                            <td><?php echo $p['PRET_UNITAR']; ?> Lei</td>
                            <td class="fw-bold text-success"><?php echo $subtotal; ?> Lei</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end fw-bold fs-5">TOTAL INCASAT:</td>
                        <td class="text-success fw-bold fs-4"><?php echo $comanda['TOTAL_PLATIT']; ?> Lei</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <footer style="background-color: #1f1f1f; border-top: 2px solid #6f42c1;" class="text-center text-light py-4 mt-auto w-100">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> <span style="color: #6f42c1; font-weight: bold;">CUSTOM</span><span class="text-white fw-bold">SHOP</span>. Toate drepturile rezervate.</p>
            <p class="small mb-0">Proiect de practică realizat de către Jarda Raul-Nicolae.</p>
        </div>
    </footer>
</body>
</html>