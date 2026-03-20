<?php
session_start();
require_once 'config.php';

// 1. Inițializăm coșul dacă nu există
if (!isset($_SESSION['cos'])) {
    $_SESSION['cos'] = [];
}

// 2. Adăugarea în coș (inclusiv datele de personalizare)
if (isset($_GET['adauga'])) {
    $id_produs = (int)$_GET['adauga'];
    
    // Preluăm textul și culoarea, dacă există
    $text_personalizat = isset($_GET['text_personalizat']) ? trim($_GET['text_personalizat']) : '';
    $culoare_text = isset($_GET['culoare_text']) ? $_GET['culoare_text'] : '';

    // Generăm o cheie unică pentru acest produs + configurația lui
    // md5 generează un cod unic bazat pe aceste 3 elemente
    $cheie_cos = md5($id_produs . $text_personalizat . $culoare_text);

    if (isset($_SESSION['cos'][$cheie_cos])) {
        // Dacă exact aceeași configurație e deja în coș, creștem cantitatea
        $_SESSION['cos'][$cheie_cos]['cantitate']++;
    } else {
        // Altfel, adăugăm configurația nouă
        $_SESSION['cos'][$cheie_cos] = [
            'id' => $id_produs,
            'cantitate' => 1,
            'text' => $text_personalizat,
            'culoare' => $culoare_text
        ];
    }
    header('Location: cos.php');
    exit;
}

// 3. Logica pentru modificarea cantității (+, -, ștergere) folosind cheia unică
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

// 4. Golirea totală a coșului
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
            <div class="table-responsive">
                <table class="table table-dark table-bordered border-secondary align-middle text-center">
                    <thead>
                        <tr>
                            <th class="text-start">Produs</th>
                            <th>Personalizare</th>
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
                                    <?php echo htmlspecialchars($produs['NUME_PRODUS']); ?>
                                </td>
                                <td>
                                    <?php if (!empty($item['text']) && $item['text'] != 'Textul tău aici') { ?>
                                        <span class="badge badge-custom p-2">
                                            Text: "<?php echo htmlspecialchars($item['text']); ?>"
                                        </span><br>
                                        <span class="badge bg-secondary mt-1">
                                            Culoare: <span style="display:inline-block; width:12px; height:12px; background-color:<?php echo $item['culoare']; ?>; border-radius:50%; border:1px solid white;"></span> <?php echo $item['culoare']; ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="text-muted fst-italic">Fără personalizare</span>
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
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold fs-5">Total de plată:</td>
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