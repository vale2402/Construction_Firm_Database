<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"> <title>Lucrări</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.table th a {color:white; text-decoration:none; display:block;}</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: 'Șters!',
                text: 'Lucrarea a fost ștearsă cu succes.',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                // Curăță URL-ul
                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            });
        });
    </script>
<?php endif; ?>
<div class="container-fluid mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Tabel: LUCRARE</h3>
        </div>

    <?php
    $conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
    if (!$conn) { $e = oci_error(); die($e['message']); }
    
    // Sortare
    $col = isset($_GET['col']) ? $_GET['col'] : 'ID_LUCRARE';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
    $newDir = ($dir == 'ASC') ? 'DESC' : 'ASC';
    
    $allowed = ['ID_LUCRARE', 'NUME_LUCRARE', 'BUGET', 'STATUS', 'DATA_START', 'CUI'];
    if(!in_array($col, $allowed)) $col = 'ID_LUCRARE';

    // Query simplu (fără JOIN)
    $sql = "SELECT * FROM LUCRARE ORDER BY $col $dir";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    ?>
    
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th><a href="?col=ID_LUCRARE&dir=<?php echo $newDir; ?>">ID ⇅</a></th>
                <th><a href="?col=NUME_LUCRARE&dir=<?php echo $newDir; ?>">Nume Lucrare ⇅</a></th>
                <th><a href="?col=BUGET&dir=<?php echo $newDir; ?>">Buget (RON) ⇅</a></th>
                <th><a href="?col=DATA_START&dir=<?php echo $newDir; ?>">Data Start ⇅</a></th>
                <th><a href="?col=STATUS&dir=<?php echo $newDir; ?>">Status ⇅</a></th>
                <th><a href="?col=CUI&dir=<?php echo $newDir; ?>">CUI Client (FK) ⇅</a></th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
            <tr>
                <td><?php echo $row['ID_LUCRARE']; ?></td>
                <td><?php echo $row['NUME_LUCRARE']; ?></td>
                <td><?php echo number_format($row['BUGET'], 2); ?></td>
                <td><?php echo isset($row['DATA_START']) ? date('d-m-Y', strtotime($row['DATA_START'])) : '-'; ?></td>
                <td><?php echo $row['STATUS']; ?></td>
                <td><?php echo $row['CUI']; ?></td>
                <td>
                    <a href="edit.php?table=lucrare&id=<?php echo $row['ID_LUCRARE']; ?>" class="btn btn-sm btn-success">Editează</a>
                    <a href="delete.php?table=lucrare&pk=id_lucrare&id=<?php echo $row['ID_LUCRARE']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sigur vrei să ștergi?')">Șterge</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>