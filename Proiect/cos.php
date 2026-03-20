<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['cos'])) {
    $_SESSION['cos'] = [];
}
if (isset($_GET['adauga'])) {
    $id_produs = (int)$_GET['adauga'];
    if (isset($_SESSION['cos'][$id_produs])) {
        $_SESSION['cos'][$id_produs]++;
    } else {
        $_SESSION['cos'][$id_produs] = 1;
    }
    header('Location: cos.php');
    exit;
}
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_produs = (int)$_GET['id'];
    $actiune = $_GET['action'];

    if (isset($_SESSION['cos'][$id_produs])) {
        if ($actiune == 'plus') {
            $_SESSION['cos'][$id_produs]++;
        } elseif ($actiune == 'minus') {
            $_SESSION['cos'][$id_produs]--;
            if ($_SESSION['cos'][$id_produs] <= 0) {
                unset($_SESSION['cos'][$id_produs]);
            }
        } elseif ($actiune == 'sterge') {
            unset($_SESSION['cos'][$id_produs]);
        }
    }
    header('Location: cos.php');
    exit;
}
if (isset($_GET['goleste'])) {
    $_SESSION['cos'] = [];
    header('Location: cos.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Coșul meu - CustomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .navbar { background-color: #1f1f1f !important; border-bottom: 2px solid #6f42c1; }
        .table-dark { background-color: #1f1f1f; }
        .btn-purple { background-color: #6f42c1; color: white; border: none; }
        .btn-purple:hover { background-color: #59339d; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark mb-4 p-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="proiect.php" style="color: #6f42c1;">CUSTOM<span class="text-white">SHOP</span></a>
            <a href="proiect.php" class="btn btn-outline-light">Înapoi la magazin</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4" style="color: #bb86fc;">Coșul de cumpărături 🛒</h2>

        <?php if (empty($_SESSION['cos'])) { ?>
            <div class="alert alert-secondary bg-dark text-white border-secondary">
                Coșul tău este gol. <a href="proiect.php" style="color: #bb86fc;">Întoarce-te la magazin</a> pentru a adăuga produse!
            </div>
        <?php } else { ?>
            <div class="table-responsive">
                <table class="table table-dark table-bordered border-secondary align-middle text-center">
                    <thead>
                        <tr>
                            <th class="text-start">Produs</th>
                            <th>Preț Unitar</th>
                            <th>Cantitate</th>
                            <th>Subtotal</th>
                            <th>Sterge produsul din cos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_cos = 0;
                        
                        foreach ($_SESSION['cos'] as $id => $cantitate) { 
                            $stmt = $pdo->prepare("SELECT NUME_PRODUS, PRET, IMAGINE FROM produse WHERE ID = ?");
                            $stmt->execute([$id]);
                            $produs = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($produs) {
                                $subtotal = $produs['PRET'] * $cantitate;
                                $total_cos += $subtotal;
                        ?>
                            <tr>
                                <td class="text-start">
                                    <img src="imagini/<?php echo $produs['IMAGINE']; ?>" width="50" height="50" style="object-fit: contain; background: white; border-radius: 5px; margin-right: 10px;">
                                    <?php echo htmlspecialchars($produs['NUME_PRODUS']); ?>
                                </td>
                                <td><?php echo $produs['PRET']; ?> Lei</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="cos.php?action=minus&id=<?php echo $id; ?>" class="btn btn-sm btn-outline-light fw-bold">-</a>
                                        <span class="fs-5 mx-3"><?php echo $cantitate; ?></span>
                                        <a href="cos.php?action=plus&id=<?php echo $id; ?>" class="btn btn-sm btn-outline-light fw-bold">+</a>
                                    </div>
                                </td>
                                <td class="fw-bold" style="color: #bb86fc;"><?php echo $subtotal; ?> Lei</td>
                                <td>
                                    <a href="cos.php?action=sterge&id=<?php echo $id; ?>" class="btn btn-sm btn-danger" title="Șterge produsul">X</a>
                                </td>
                            </tr>
                        <?php } 
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold fs-5">Total de plată:</td>
                            <td colspan="2" class="fw-bold fs-5 text-success text-start"><?php echo $total_cos; ?> Lei</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="cos.php?goleste=1" class="btn btn-outline-danger">Golește coșul</a>
                <button class="btn btn-success btn-lg">Finalizează comanda</button>
            </div>
        <?php } ?>
    </div>

</body>
</html>