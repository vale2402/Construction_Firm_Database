<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"> <title>Clienți</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.table th a {color:white; text-decoration:none; display:block;}</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container-fluid mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Tabel: CLIENT</h3>
        </div>

    <?php
    $conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
    if (!$conn) { $e = oci_error(); die("Eroare: " . $e['message']); }

    $col = isset($_GET['col']) ? $_GET['col'] : 'CUI';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
    $newDir = ($dir == 'ASC') ? 'DESC' : 'ASC';
    
    $allowed = ['CUI', 'NUME_CONTRACTOR', 'JUDET', 'ORAS', 'TELEFON'];
    if(!in_array($col, $allowed)) $col = 'CUI';

    $sql = "SELECT * FROM CLIENT ORDER BY $col $dir";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    ?>

    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th><a href="?col=CUI&dir=<?php echo $newDir; ?>">CUI ⇅</a></th>
                <th><a href="?col=NUME_CONTRACTOR&dir=<?php echo $newDir; ?>">Nume Contractor ⇅</a></th>
                <th>Telefon</th>
                <th>Email</th>
                <th><a href="?col=JUDET&dir=<?php echo $newDir; ?>">Județ ⇅</a></th>
                <th><a href="?col=ORAS&dir=<?php echo $newDir; ?>">Oraș ⇅</a></th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
            <tr>
                <td><?php echo $row['CUI']; ?></td>
                <td class="fw-bold"><?php echo $row['NUME_CONTRACTOR']; ?></td>
                <td><?php echo $row['TELEFON']; ?></td>
                <td><?php echo $row['EMAIL']; ?></td>
                <td><?php echo $row['JUDET']; ?></td>
                <td><?php echo $row['ORAS']; ?></td>
                <td>
                    <a href="edit.php?table=client&id=<?php echo $row['CUI']; ?>" class="btn btn-sm btn-success">
                        Editează
                    </a>

                    <a href="rapoarte_e.php?cui_analiza=<?php echo urlencode($row['CUI']); ?>&sursa=tabel" 
                       class="btn btn-sm btn-danger cascade-delete ignore-swal">
                        Șterge
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const params = new URLSearchParams(window.location.search);
    if (params.get('status') === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Ștergere Completă!',
            text: 'Clientul și toate datele asociate (Cascade) au fost eliminate.',
            timer: 3000,
            showConfirmButton: false
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
</script>

</body>
</html>