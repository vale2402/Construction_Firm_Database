<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"> <title>Pontaj</title>
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
<div class="container-fluid mt-4">
    <h3>Tabel: PONTAJ</h3>

    <?php
    $conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
    if (!$conn) { $e = oci_error(); die($e['message']); }

    // --- LOGICA DE SORTARE ---
    $col = isset($_GET['col']) ? $_GET['col'] : 'ID_PONTAJ';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : 'DESC'; // Default DESC sa vedem ultimele pontaje
    $newDir = ($dir == 'ASC') ? 'DESC' : 'ASC';

    // Coloane permise (inclusiv FK-uri)
    $allowed = ['ID_PONTAJ', 'ID_ANGAJAT', 'ID_LUCRARE', 'DATA_LUCRU', 'NR_ORE'];
    if(!in_array($col, $allowed)) $col = 'ID_PONTAJ';

    $sql = "SELECT * FROM PONTAJ ORDER BY $col $dir";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    ?>

    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th><a href="?col=ID_PONTAJ&dir=<?php echo $newDir; ?>">ID ⇅</a></th>
                <th><a href="?col=ID_ANGAJAT&dir=<?php echo $newDir; ?>">ID Angajat (FK) ⇅</a></th>
                <th><a href="?col=ID_LUCRARE&dir=<?php echo $newDir; ?>">ID Lucrare (FK) ⇅</a></th>
                <th><a href="?col=DATA_LUCRU&dir=<?php echo $newDir; ?>">Data Lucru ⇅</a></th>
                <th><a href="?col=NR_ORE&dir=<?php echo $newDir; ?>">Nr. Ore ⇅</a></th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
            <tr>
                <td><?php echo $row['ID_PONTAJ']; ?></td>
                <td><?php echo $row['ID_ANGAJAT']; ?></td>
                <td><?php echo $row['ID_LUCRARE']; ?></td>
                <td><?php echo isset($row['DATA_LUCRU']) ? date('d-m-Y', strtotime($row['DATA_LUCRU'])) : ''; ?></td>
                <td><?php echo $row['NR_ORE']; ?></td>
                <td>
                    <a href="edit.php?table=pontaj&id=<?php echo $row['ID_PONTAJ']; ?>" class="btn btn-sm btn-success">Editează</a>
                    <a href="delete.php?table=pontaj&pk=id_pontaj&id=<?php echo $row['ID_PONTAJ']; ?>" class="btn btn-sm btn-danger">Șterge</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>