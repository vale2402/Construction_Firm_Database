<?php
// --- LOGICA DE UPDATE (LMD PE VIEW) ---
$conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
if (!$conn) { $e = oci_error(); die($e['message']); }

// 1. SALVARE MODIFICĂRI (UPDATE)
if (isset($_POST['btn_update_view'])) {
    $id_ang  = $_POST['id_angajat'];
    $nume    = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $telefon = $_POST['telefon'];

    // Executăm UPDATE pe VIEW
    $sql_up = "UPDATE V_ANGAJATI_INFO 
               SET NUME = :n, PRENUME = :p, TELEFON = :t 
               WHERE ID_ANGAJAT = :id";
    
    $stid = oci_parse($conn, $sql_up);
    oci_bind_by_name($stid, ':n', $nume);
    oci_bind_by_name($stid, ':p', $prenume);
    oci_bind_by_name($stid, ':t', $telefon);
    oci_bind_by_name($stid, ':id', $id_ang);
    
    if (oci_execute($stid)) {
        header("Location: vizualizari.php?msg=updated");
        exit();
    } else {
        $error = oci_error($stid);
        $err_msg = $error['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Vizualizări (Views)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .view-header { background: #2c3e50; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
        .bg-complex { background: #1abc9c; color: white; }
    </style>
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<?php if (isset($_GET['msg'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let title = '<?php echo ($_GET['msg'] == 'deleted') ? "Șters!" : "Actualizat!"; ?>';
            let text  = '<?php echo ($_GET['msg'] == 'deleted') ? "Înregistrarea a fost ștearsă prin View." : "Datele au fost modificate prin View."; ?>';
            
            Swal.fire({
                icon: 'success',
                title: title,
                text: text,
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            });
        });
    </script>
<?php endif; ?>

<div class="container mt-4 mb-5">

    <?php if (isset($_GET['action']) && $_GET['action'] == 'edit'): 
        $id_edit = $_GET['id'];
        $stid_edit = oci_parse($conn, "SELECT * FROM V_ANGAJATI_INFO WHERE ID_ANGAJAT = :id");
        oci_bind_by_name($stid_edit, ':id', $id_edit);
        oci_execute($stid_edit);
        $row_edit = oci_fetch_assoc($stid_edit);
    ?>
    
    <div class="card shadow mx-auto mb-5" style="max-width: 800px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Editare prin Vizualizare (LMD)</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">ID Angajat (Cheie Primară)</label>
                    <input type="text" class="form-control bg-light fw-bold" value="<?php echo $row_edit['ID_ANGAJAT']; ?>" readonly>
                    <input type="hidden" name="id_angajat" value="<?php echo $row_edit['ID_ANGAJAT']; ?>">
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3">Date Personale (Editabile)</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nume</label>
                        <input type="text" name="nume" class="form-control" value="<?php echo $row_edit['NUME']; ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prenume</label>
                        <input type="text" name="prenume" class="form-control" value="<?php echo $row_edit['PRENUME']; ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="telefon" class="form-control" value="<?php echo $row_edit['TELEFON']; ?>" required>
                    </div>
                </div>

                <h6 class="text-secondary border-bottom pb-2 mb-3 mt-2">Detalii Job (Read-Only - din tabela JOB)</h6>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label text-muted">Funcție</label>
                        <input type="text" class="form-control bg-light fst-italic" value="<?php echo $row_edit['TITLU_JOB']; ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Salariu Lunar</label>
                        <input type="text" class="form-control bg-light fst-italic text-end" value="<?php echo $row_edit['SALARIU_LUNAR']; ?> RON" readonly>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="btn_update_view" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-save"></i> Salvează
                    </button>
                    <a href="vizualizari.php" class="btn btn-secondary px-4">Anulează</a>
                </div>
            </form>
        </div>
    </div>

    <?php else: ?>

    <div class="card shadow-sm mb-5">
        <div class="view-header">
            <h4 class="mb-0"><i class="bi bi-person-badge"></i> 1. Vizualizare Compusă: V_ANGAJATI_INFO</h4>
            <small>View format din `ANGAJAT` și `JOB`. Permite INSERT/UPDATE/DELETE pe datele angajatului.</small>
        </div>
        <div class="card-body">
            
            <?php if(isset($err_msg)) echo "<div class='alert alert-danger'>$err_msg</div>"; ?>

            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nume</th>
                        <th>Prenume</th>
                        <th>Telefon</th>
                        <th class="text-muted">Funcție (Job)</th>
                        <th class="text-muted">Salariu</th>
                        <th class="text-center" style="width: 180px;">Acțiuni LMD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stid = oci_parse($conn, "SELECT * FROM V_ANGAJATI_INFO ORDER BY ID_ANGAJAT");
                    oci_execute($stid);
                    
                    while ($row = oci_fetch_assoc($stid)): 
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo $row['ID_ANGAJAT']; ?></td>
                        <td><?php echo $row['NUME']; ?></td>
                        <td><?php echo $row['PRENUME']; ?></td>
                        <td><?php echo $row['TELEFON']; ?></td>
                        <td class="text-muted fst-italic"><?php echo $row['TITLU_JOB']; ?></td>
                        <td class="text-muted text-end"><?php echo $row['SALARIU_LUNAR']; ?></td>
                        
                        <td class="text-center">
                            <a href="vizualizari.php?action=edit&id=<?php echo $row['ID_ANGAJAT']; ?>" 
                               class="btn btn-sm btn-success">
                                Editează
                            </a>
                            <a href="delete.php?table=V_ANGAJATI_INFO&pk=ID_ANGAJAT&id=<?php echo $row['ID_ANGAJAT']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Ștergi angajatul prin intermediul View-ului?')">
                                Șterge
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="view-header bg-complex">
            <h4 class="mb-0"><i class="bi bi-graph-up"></i> 2. Vizualizare Complexă: V_COSTURI_LUCRARI</h4>
            <small>Conține funcții de grup (SUM, COUNT). Nu permite modificări (Read-Only).</small>
        </div>
        <div class="card-body">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nume Lucrare</th>
                        <th>Client</th>
                        <th class="text-center">Nr. Intrări</th>
                        <th class="text-end">Cost Total Materiale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stid2 = oci_parse($conn, "SELECT * FROM V_COSTURI_LUCRARI ORDER BY COST_TOTAL_MATERIALE DESC");
                    oci_execute($stid2);
                    while ($row = oci_fetch_assoc($stid2)): 
                    ?>
                    <tr>
                        <td><?php echo $row['NUME_LUCRARE']; ?></td>
                        <td><?php echo $row['CLIENT']; ?></td>
                        <td class="text-center"><?php echo $row['NR_INTRARI_MATERIALE']; ?></td>
                        <td class="text-end fw-bold text-success">
                            <?php echo number_format($row['COST_TOTAL_MATERIALE'], 2); ?> RON
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>

</div>
</body>
</html>