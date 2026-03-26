<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['cos'])) {
    $_SESSION['cos'] = [];
}

if (isset($_GET['adauga'])) {
    $id_produs = (int)$_GET['adauga'];
    $text_personalizat = isset($_GET['text_personalizat']) ? trim($_GET['text_personalizat']) : '';
    $culoare_text = isset($_GET['culoare_text']) ? $_GET['culoare_text'] : '';
    $marime = isset($_GET['marime']) ? trim($_GET['marime']) : 'Standard';

    $cheie_cos = md5($id_produs . $text_personalizat . $culoare_text . $marime);

    if (isset($_SESSION['cos'][$cheie_cos])) {
        $_SESSION['cos'][$cheie_cos]['cantitate']++;
    } else {
        $_SESSION['cos'][$cheie_cos] = [
            'id' => $id_produs,
            'cantitate' => 1,
            'text' => $text_personalizat,
            'culoare' => $culoare_text,
            'marime' => $marime
        ];
    }
    header('Location: cos.php');
    exit;
}

if (isset($_GET['action']) && isset($_GET['cheie'])) {
    $cheie_cos = $_GET['cheie'];
    $actiune = $_GET['action'];

    if (isset($_SESSION['cos'][$cheie_cos])) {
        if ($actiune == 'plus') {
            $_SESSION['cos'][$cheie_cos]['cantitate']++;
        } elseif ($actiune == 'minus') {
            $_SESSION['cos'][$cheie_cos]['cantitate']--;
            if ($_SESSION['cos'][$cheie_cos]['cantitate'] <= 0) {
                unset($_SESSION['cos'][$cheie_cos]);
            }
        } elseif ($actiune == 'sterge') {
            unset($_SESSION['cos'][$cheie_cos]);
        }
    }
    header('Location: cos.php');
    exit;
}

if (isset($_GET['goleste'])) {
    $_SESSION['cos'] = [];
    unset($_SESSION['cupon']);
    header('Location: cos.php');
    exit;
}

$mesaj_cupon = '';
if (isset($_POST['aplica_cupon'])) {
    $cod = trim($_POST['cod_cupon']);
    $stmt = $pdo->prepare("SELECT * FROM cupoane WHERE COD = ? AND ACTIV = 1");
    $stmt->execute([$cod]);
    $cupon = $stmt->fetch();

    if ($cupon) {
        $_SESSION['cupon'] = [
            'cod' => $cupon['COD'],
            'procent' => $cupon['PROCENT_REDUCERE']
        ];
        $mesaj_cupon = "<div class='alert alert-success mt-2 p-2'>✅ Cupon aplicat: -" . $cupon['PROCENT_REDUCERE'] . "% reducere!</div>";
    } else {
        $mesaj_cupon = "<div class='alert alert-danger mt-2 p-2'>❌ Cupon invalid sau expirat!</div>";
        unset($_SESSION['cupon']);
    }
}

if (isset($_GET['sterge_cupon'])) {
    unset($_SESSION['cupon']);
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
        .badge-custom { background-color: #6f42c1; color: white; }
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
            <div class="table-responsive shadow-lg">
                <table class="table table-dark table-bordered border-secondary align-middle text-center mb-0">
                    <thead class="table-active">
                        <tr>
                            <th class="text-start">Produs</th>
                            <th>Detalii</th>
                            <th>Preț Unitar</th>
                            <th>Cantitate</th>
                            <th>Subtotal</th>
                            <th>Acțiune</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_cos = 0;
                        
                        foreach ($_SESSION['cos'] as $cheie_cos => $item) { 
                            $stmt = $pdo->prepare("SELECT NUME_PRODUS, PRET, IMAGINE FROM produse WHERE ID = ?");
                            $stmt->execute([$item['id']]);
                            $produs = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($produs) {
                                $subtotal = $produs['PRET'] * $item['cantitate'];
                                $total_cos += $subtotal;
                        ?>
                            <tr>
                                <td class="text-start">
                                    <img src="imagini/<?php echo $produs['IMAGINE']; ?>" width="50" height="50" style="object-fit: contain; background: white; border-radius: 5px; margin-right: 10px;">
                                    <span class="fw-bold"><?php echo htmlspecialchars($produs['NUME_PRODUS']); ?></span>
                                </td>
                                <td class="text-start">
                                    <?php if (!empty($item['text']) && $item['text'] != 'Textul tău aici') { ?>
                                        <span class="badge badge-custom p-2 mb-1">Text: "<?php echo htmlspecialchars($item['text']); ?>"</span><br>
                                        <span class="badge bg-secondary mb-1">Culoare: <span style="display:inline-block; width:10px; height:10px; background-color:<?php echo $item['culoare']; ?>; border-radius:50%; border:1px solid white;"></span> <?php echo $item['culoare']; ?></span>
                                    <?php } else { ?>
                                        <span class="text-muted fst-italic">Fără personalizare</span>
                                    <?php } ?>
                                    
                                    <?php if (!empty($item['marime']) && $item['marime'] != 'Standard') { ?>
                                        <br><span class="badge bg-info text-dark mt-1">Mărime: <?php echo htmlspecialchars($item['marime']); ?></span>
                                    <?php } ?>
                                </td>
                                <td><?php echo $produs['PRET']; ?> Lei</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="cos.php?action=minus&cheie=<?php echo $cheie_cos; ?>" class="btn btn-sm btn-outline-light fw-bold">-</a>
                                        <span class="fs-5 mx-3"><?php echo $item['cantitate']; ?></span>
                                        <a href="cos.php?action=plus&cheie=<?php echo $cheie_cos; ?>" class="btn btn-sm btn-outline-light fw-bold">+</a>
                                    </div>
                                </td>
                                <td class="fw-bold" style="color: #bb86fc;"><?php echo $subtotal; ?> Lei</td>
                                <td>
                                    <a href="cos.php?action=sterge&cheie=<?php echo $cheie_cos; ?>" class="btn btn-sm btn-danger" title="Șterge produsul">X</a>
                                </td>
                            </tr>
                        <?php } 
                        } ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4 align-items-center">
                <div class="col-md-5">
                    <form method="POST" class="d-flex">
                        <input type="text" name="cod_cupon" class="form-control bg-dark text-white border-secondary me-2" placeholder="Cod reducere..." value="<?php echo isset($_SESSION['cupon']) ? $_SESSION['cupon']['cod'] : ''; ?>" <?php echo isset($_SESSION['cupon']) ? 'disabled' : ''; ?>>
                        
                        <?php if(!isset($_SESSION['cupon'])) { ?>
                            <button type="submit" name="aplica_cupon" class="btn btn-warning fw-bold">Aplică</button>
                        <?php } else { ?>
                            <a href="cos.php?sterge_cupon=1" class="btn btn-danger fw-bold">Șterge</a>
                        <?php } ?>
                    </form>
                    <?php echo $mesaj_cupon; ?>
                </div>
                
                <div class="col-md-7 text-end bg-dark p-3 rounded border border-secondary shadow-sm">
                    <h5 class="text-muted mb-2">Subtotal: <?php echo $total_cos; ?> Lei</h5>
                    
                    <?php 
                    $total_final = $total_cos;
                    if (isset($_SESSION['cupon'])) { 
                        $valoare_reducere = ($total_cos * $_SESSION['cupon']['procent']) / 100;
                        $total_final = $total_cos - $valoare_reducere;
                    ?>
                        <h5 class="text-warning mb-2">Reducere (<?php echo $_SESSION['cupon']['procent']; ?>%): -<?php echo $valoare_reducere; ?> Lei</h5>
                    <?php } ?>

                    <h3 class="fw-bold text-success mt-3 border-top border-secondary pt-2">TOTAL PLĂTIT: <?php echo $total_final; ?> Lei</h3>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4 mb-5">
                <a href="cos.php?goleste=1" class="btn btn-outline-danger">Golește coșul</a>
                <a href="checkout.php" class="btn btn-success btn-lg fw-bold px-5">Finalizează comanda 💳</a>
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