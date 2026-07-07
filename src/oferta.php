<?php
// --- ZONA DE PROCESARE ---
$conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
if (!$conn) { $e = oci_error(); die($e['message']); }

// 1. DELETE
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $f = $_GET['f']; 
    $m = $_GET['m'];
    
    $sql = "DELETE FROM OFERTA WHERE ID_FURNIZOR = $f AND ID_MATERIAL = $m";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    
    // Redirect cu mesaj
    header("Location: oferta.php?msg=deleted");
    exit();
}

// 2. UPDATE
if (isset($_POST['btn_salveaza'])) {
    $f = $_POST['f']; $m = $_POST['m'];
    $pret_nou = $_POST['pret_oferit'];
    
    $sql = "UPDATE OFERTA SET PRET_OFERIT = $pret_nou WHERE ID_FURNIZOR = $f AND ID_MATERIAL = $m";
    $stid = oci_parse($conn, $sql);
    if(oci_execute($stid)) {
        header("Location: oferta.php?status=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"> <title>Gestiune Oferte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.table th a {color:white; text-decoration:none; display:block;}</style>
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: 'Succes!',
                text: 'Oferta a fost ștearsă.',
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
<div class="container-fluid mt-4">

    <?php if (isset($_GET['action']) && $_GET['action'] == 'edit'): 
        $f = $_GET['f']; $m = $_GET['m'];
        $sql_edit = "SELECT o.PRET_OFERIT, f.NUME_FIRMA, m.NUME_MATERIAL 
                     FROM OFERTA o
                     JOIN FURNIZOR f ON o.ID_FURNIZOR = f.ID_FURNIZOR
                     JOIN MATERIAL m ON o.ID_MATERIAL = m.ID_MATERIAL
                     WHERE o.ID_FURNIZOR = $f AND o.ID_MATERIAL = $m";
        $stid = oci_parse($conn, $sql_edit); oci_execute($stid);
        $row_edit = oci_fetch_array($stid, OCI_ASSOC);
    ?>
    
    <div class="card mx-auto shadow" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">Editare Ofertă</div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="f" value="<?php echo $f; ?>">
                <input type="hidden" name="m" value="<?php echo $m; ?>">
                <div class="mb-3">
                    <label class="fw-bold text-secondary">ID Furnizor / ID Material (Fix)</label>
                    <input type="text" class="form-control bg-light" value="<?php echo $f . ' / ' . $m; ?>" disabled>
                </div>
                <hr>
                <div class="mb-3"><label>Furnizor</label><input type="text" class="form-control" value="<?php echo $row_edit['NUME_FIRMA']; ?>" disabled></div>
                <div class="mb-3"><label>Material</label><input type="text" class="form-control" value="<?php echo $row_edit['NUME_MATERIAL']; ?>" disabled></div>
                <div class="mb-3"><label class="fw-bold">Preț Ofertat (RON)</label><input type="number" step="0.01" name="pret_oferit" class="form-control" value="<?php echo $row_edit['PRET_OFERIT']; ?>" required></div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="btn_salveaza" class="btn btn-success flex-grow-1">Salvează Modificarea</button>
                    <a href="oferta.php" class="btn btn-secondary">Anulează</a>
                </div>
            </form>
        </div>
    </div>

    <?php else: ?>
    
    <h3>Tabel: OFERTA</h3>
    
    <?php
    $col = isset($_GET['col']) ? $_GET['col'] : 'PRET_OFERIT';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
    $newDir = ($dir == 'ASC') ? 'DESC' : 'ASC';
    $allowed = ['PRET_OFERIT', 'NUME_FIRMA', 'NUME_MATERIAL'];
    if(!in_array($col, $allowed)) $col = 'PRET_OFERIT';

    $sql = "SELECT o.ID_FURNIZOR, o.ID_MATERIAL, o.PRET_OFERIT, 
                   f.NUME_FIRMA, m.NUME_MATERIAL, m.UNITATE_MASURA
            FROM OFERTA o
            JOIN FURNIZOR f ON o.ID_FURNIZOR = f.ID_FURNIZOR
            JOIN MATERIAL m ON o.ID_MATERIAL = m.ID_MATERIAL
            ORDER BY $col $dir";
    $stid = oci_parse($conn, $sql); oci_execute($stid);
    ?>

    <table class="table table-bordered table-striped table-hover mt-3 bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th><a href="?col=NUME_FIRMA&dir=<?php echo $newDir; ?>">Furnizor ⇅</a></th>
                <th><a href="?col=NUME_MATERIAL&dir=<?php echo $newDir; ?>">Material ⇅</a></th>
                <th><a href="?col=PRET_OFERIT&dir=<?php echo $newDir; ?>">Preț Oferit ⇅</a></th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
            <tr>
                <td><?php echo $row['NUME_FIRMA']; ?></td>
                <td><?php echo $row['NUME_MATERIAL']; ?></td>
                <td><?php echo number_format($row['PRET_OFERIT'], 2); ?> RON / <?php echo $row['UNITATE_MASURA']; ?></td>
                <td>
                    <a href="oferta.php?action=edit&f=<?php echo $row['ID_FURNIZOR']; ?>&m=<?php echo $row['ID_MATERIAL']; ?>" class="btn btn-sm btn-success">Editează</a>
                    <a href="oferta.php?action=delete&f=<?php echo $row['ID_FURNIZOR']; ?>&m=<?php echo $row['ID_MATERIAL']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ștergi oferta?')">Șterge</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php endif; ?>

</div>
</body>
</html>