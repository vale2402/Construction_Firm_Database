<?php
// --- LOGICA DE BACKEND (Rămâne neschimbată pentru că funcționează) ---
$conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
if (!$conn) { $e = oci_error(); die("Eroare DB: " . $e['message']); }

$stare_pagina = "demo"; 
$mesaj_succes = "";
$mesaj_eroare = "";

$cui_target = isset($_GET['cui_analiza']) ? $_GET['cui_analiza'] : '';
$sursa = isset($_GET['sursa']) ? $_GET['sursa'] : '';

if (!empty($cui_target)) {
    $stare_pagina = "avertisment";
}

if (isset($_POST['confirma_stergere'])) {
    $cui_de_sters = $_POST['cui_sters'];
    $nume_de_sters = $_POST['nume_sters'];
    $sursa_redirect = $_POST['sursa_redirect'];

    $sql = "DELETE FROM CLIENT WHERE CUI = :cui";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':cui', $cui_de_sters);
    
    if (@oci_execute($stid)) {
        if ($sursa_redirect == 'tabel') {
            header("Location: client.php?status=success");
            exit();
        }
        $stare_pagina = "succes";
        $mesaj_succes = "Clientul <strong>$nume_de_sters</strong> și toate datele operaționale au fost arhivate/șterse.";
    } else {
        $e = oci_error($stid);
        $stare_pagina = "eroare";
        $mesaj_eroare = $e['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Protocol Ștergere</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* DESIGN NOU - INDUSTRIAL / CLEAN */
        body { background-color: #e9ecef; }
        .card-protocol { border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 5px solid #fd7e14; } /* Portocaliu industrial */
        .list-group-item { border-left: none; border-right: none; padding: 1rem 1.5rem; }
        .badge-count { font-size: 1rem; background-color: #343a40; }
        .header-dark { background-color: #212529; color: #fff; padding: 20px; border-radius: 5px 5px 0 0; }
    </style>
</head>
<body class="py-5">

<?php include 'navbar.php'; ?>

<div class="container">

    <?php if (!empty($mesaj_eroare)): ?>
        <div class="alert alert-danger shadow-sm"><?php echo $mesaj_eroare; ?></div>
    <?php endif; ?>

    <?php if ($stare_pagina == 'succes'): ?>
        <div class="card border-0 shadow-sm mx-auto text-center p-5" style="max-width: 600px;">
            <div class="mb-3 text-success"><i class="bi bi-shield-check" style="font-size: 4rem;"></i></div>
            <h3 class="fw-bold">Operațiune Finalizată</h3>
            <p class="text-muted"><?php echo $mesaj_succes; ?></p>
            <hr>
            <div class="d-flex justify-content-center gap-2">
                <a href="rapoarte_e.php" class="btn btn-outline-dark">Resetare Demo</a>
                <a href="client.php" class="btn btn-dark">Registru Clienți</a>
            </div>
        </div>

    <?php elseif ($stare_pagina == 'avertisment'): ?>
        
        <?php
        $s1 = oci_parse($conn, "SELECT NUME_CONTRACTOR FROM CLIENT WHERE CUI = :c");
        oci_bind_by_name($s1, ':c', $cui_target); oci_execute($s1);
        $row_c = oci_fetch_assoc($s1);

        if (!$row_c) {
            echo "<div class='alert alert-warning text-center'>Client inexistent. <a href='client.php'>Înapoi</a></div>";
        } else {
            $nume = $row_c['NUME_CONTRACTOR'];
            
            // Aceleași funcții de numărare, dar le afișăm altfel
            function count_rows($c, $sql, $id) {
                $s = oci_parse($c, $sql); oci_bind_by_name($s, ':id', $id); oci_execute($s); 
                return oci_fetch_assoc($s)['N'];
            }
            $cnt_luc = count_rows($conn, "SELECT COUNT(*) N FROM LUCRARE WHERE CUI=:id", $cui_target);
            $cnt_con = count_rows($conn, "SELECT COUNT(*) N FROM CONSUM c JOIN LUCRARE l ON c.ID_LUCRARE=l.ID_LUCRARE WHERE l.CUI=:id", $cui_target);
            $cnt_pon = count_rows($conn, "SELECT COUNT(*) N FROM PONTAJ p JOIN LUCRARE l ON p.ID_LUCRARE=l.ID_LUCRARE WHERE l.CUI=:id", $cui_target);
        ?>

        <div class="card card-protocol mx-auto" style="max-width: 700px;">
            <div class="header-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-uppercase ls-1"><i class="bi bi-diagram-3-fill me-2"></i> Analiză Dependențe</h5>
                <span class="badge bg-warning text-dark">CASCADE ENABLED</span>
            </div>
            
            <div class="card-body p-4">
                <h4 class="card-title fw-bold mb-1"><?php echo $nume; ?></h4>
                <p class="text-muted small mb-4">CUI: <?php echo $cui_target; ?></p>

                <div class="alert alert-light border-start border-4 border-warning shadow-sm">
                    <p class="mb-0 fw-medium">
                        <i class="bi bi-exclamation-circle-fill text-warning me-2"></i> 
                        Sistemul a detectat înregistrări operaționale active. Confirmarea ștergerii va declanșa eliminarea recursivă a datelor.
                    </p>
                </div>

                <h6 class="text-uppercase text-muted fw-bold mt-4 mb-3 small">Raport Date Conexe (De Șters):</h6>
                
                <ul class="list-group mb-4 shadow-sm">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <span><i class="bi bi-building me-2 text-primary"></i> Contracte / Lucrări</span>
                        <span class="badge rounded-pill badge-count"><?php echo $cnt_luc; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-box-seam me-2 text-success"></i> Resurse Alocate (Consum)</span>
                        <span class="badge rounded-pill badge-count"><?php echo $cnt_con; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <span><i class="bi bi-clock-history me-2 text-info"></i> Manoperă (Pontaje)</span>
                        <span class="badge rounded-pill badge-count"><?php echo $cnt_pon; ?></span>
                    </li>
                </ul>

                <form method="POST" class="row g-2">
                    <input type="hidden" name="cui_sters" value="<?php echo $cui_target; ?>">
                    <input type="hidden" name="nume_sters" value="<?php echo $nume; ?>">
                    <input type="hidden" name="sursa_redirect" value="<?php echo $sursa; ?>">
                    
                    <div class="col-6">
                        <a href="<?php echo ($sursa == 'tabel') ? 'client.php' : 'rapoarte_e.php'; ?>" class="btn btn-light w-100 py-2 border">
                            Nu, Anulează
                        </a>
                    </div>
                    <div class="col-6">
                        <button type="submit" name="confirma_stergere" class="btn btn-danger w-100 py-2 fw-bold">
                            <i class="bi bi-trash3"></i> Șterge Datele
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-white text-muted small text-center py-3">
                Acțiune securizată prin protocolul de integritate referențială.
            </div>
        </div>
        <?php } ?>

    <?php else: ?>
        
        <div class="card card-protocol mx-auto" style="max-width: 600px;">
            <div class="header-dark">
                <h5 class="mb-0"><i class="bi bi-terminal"></i> Simulator Integritate (Demo)</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-4">Selectează un contractor pentru a simula procedura de ștergere în cascadă.</p>
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Contractor Țintă</label>
                        <select name="cui_analiza" class="form-select form-select-lg" required>
                            <option value="">-- Selectează din listă --</option>
                            <?php
                            $s = oci_parse($conn, "SELECT CUI, NUME_CONTRACTOR, (SELECT COUNT(*) FROM LUCRARE WHERE CUI=CLIENT.CUI) nr FROM CLIENT ORDER BY nr DESC");
                            oci_execute($s);
                            while ($r = oci_fetch_assoc($s)) {
                                echo "<option value='".$r['CUI']."'>".$r['NUME_CONTRACTOR']." (".$r['NR']." contracte active)</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-2">Inițiază Analiza</button>
                </form>
            </div>
        </div>

    <?php endif; ?>

</div>
</body>
</html>