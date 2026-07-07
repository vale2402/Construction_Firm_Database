<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"> <title>Utilaje</title>
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
                icon: 'success',     // Iconița verde cu bifă
                title: 'Succes!',    // Titlul mare
                text: 'Înregistrarea a fost ștearsă cu succes.', // Mesajul de sub titlu
                timer: 3000,              // Dispare automat după 3 secunde
                timerProgressBar: true,   // Arată o bară de progres jos
                showConfirmButton: false, // Nu arată buton de "OK", dispare singur
                position: 'center',       // Apare în mijlocul ecranului
                background: '#fff',       // Fundal alb
                customClass: {
                    popup: 'shadow-lg rounded-4' // Stiluri extra pentru aspect modern
                }
            }).then(() => {
                // Opțional: Curăță URL-ul de "?msg=deleted" după ce dispare alerta
                // Astfel, dacă dai refresh la pagină, nu mai apare popup-ul din nou.
                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            });
        });
    </script>
<?php endif; ?>
<div class="container-fluid">
    <h3>Tabel: UTILAJ</h3>
    <?php
    $conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
    
    $col = isset($_GET['col']) ? $_GET['col'] : 'NR_INMATRICULARE';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
    $newDir = ($dir == 'ASC') ? 'DESC' : 'ASC';
    
    $allowed = ['NR_INMATRICULARE', 'MARCA', 'DENUMIRE', 'UNITATE_MASURA', 'COST_UNITATE'];
    if(!in_array($col, $allowed)) $col = 'NR_INMATRICULARE';

    $sql = "SELECT * FROM UTILAJ ORDER BY $col $dir";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    ?>
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th><a href="?col=NR_INMATRICULARE&dir=<?php echo $newDir; ?>">Nr. Înmatriculare ⇅</a></th>
                <th><a href="?col=MARCA&dir=<?php echo $newDir; ?>">Marcă ⇅</a></th>
                <th><a href="?col=DENUMIRE&dir=<?php echo $newDir; ?>">Denumire ⇅</a></th>
                <th><a href="?col=UNITATE_MASURA&dir=<?php echo $newDir; ?>">U.M. ⇅</a></th>
                <th><a href="?col=COST_UNITATE&dir=<?php echo $newDir; ?>">Cost/Unitate ⇅</a></th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
            <tr>
                <td><?php echo $row['NR_INMATRICULARE']; ?></td>
                <td><?php echo $row['MARCA']; ?></td>
                <td><?php echo $row['DENUMIRE']; ?></td>
                <td><?php echo $row['UNITATE_MASURA']; ?></td>
                <td><?php echo $row['COST_UNITATE']; ?></td>
                <td>
                    <a href="edit.php?table=utilaj&id=<?php echo $row['NR_INMATRICULARE']; ?>" class="btn btn-sm btn-success">Editează</a>
                    <a href="delete.php?table=utilaj&pk=nr_inmatriculare&id=<?php echo $row['NR_INMATRICULARE']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ștergi?')">Șterge</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>