<?php
session_start();
require_once 'config.php';
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: proiect.php');
    exit;
}
$id_produs = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM produse WHERE ID = ?");
$stmt->execute([$id_produs]);
$produs = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$produs) {
    die("Produsul nu a fost găsit!");
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Personalizare - <?php echo htmlspecialchars($produs['NUME_PRODUS']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .navbar { background-color: #1f1f1f !important; border-bottom: 2px solid #6f42c1; }
        .btn-purple { background-color: #6f42c1; color: white; border: none; }
        .btn-purple:hover { background-color: #59339d; color: white; }
        .preview-container {
            position: relative;
            display: inline-block;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        
        .preview-container img {
            width: 100%;
            display: block;
            object-fit: contain;
        }

        #text-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            font-weight: bold;
            color: #000000;
            white-space: pre-wrap;
            text-align: center;
            pointer-events: none;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.5); 
        }
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
        <div class="row">
            
            <div class="col-md-6 text-center mb-4">
                <h4 class="mb-3 text-purple" style="color: #bb86fc;">Previzualizare Live</h4>
                <div class="preview-container shadow-lg border border-secondary">
                    <img src="imagini/<?php echo $produs['IMAGINE']; ?>" alt="Produs">
                    <div id="text-overlay">Textul tău aici</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card bg-dark border-secondary p-4 shadow">
                    <h2 class="mb-3"><?php echo htmlspecialchars($produs['NUME_PRODUS']); ?></h2>
                    <h4 class="text-success mb-4"><?php echo $produs['PRET']; ?> Lei</h4>

                    <form action="cos.php" method="GET">
                        <input type="hidden" name="adauga" value="<?php echo $produs['ID']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Text personalizat:</label>
                            <input type="text" id="custom-text" name="text_personalizat" class="form-control bg-dark text-white border-secondary" placeholder="Ex: La mulți ani!" value="Textul tău aici">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Culoare text:</label>
                            <input type="color" id="custom-color" name="culoare_text" class="form-control form-control-color bg-dark border-secondary" value="#000000" title="Alege culoarea">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Mărime text: <span id="size-val">24</span>px</label>
                            <input type="range" id="custom-size" class="form-range" min="10" max="80" value="24">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-purple btn-lg">Adaugă în coș cu personalizare 🛒</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        const textInput = document.getElementById('custom-text');
        const colorInput = document.getElementById('custom-color');
        const sizeInput = document.getElementById('custom-size');
        const textOverlay = document.getElementById('text-overlay');
        const sizeVal = document.getElementById('size-val');
        textInput.addEventListener('input', function() {
            textOverlay.innerText = this.value;
        });
        colorInput.addEventListener('input', function() {
            textOverlay.style.color = this.value;
        });
        sizeInput.addEventListener('input', function() {
            textOverlay.style.fontSize = this.value + 'px';
            sizeVal.innerText = this.value;
        });
    </script>

</body>
</html>