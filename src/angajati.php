<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"> <title>Angajați</title>
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
    <h3>Tabel: ANGAJAT</h3>
    <?php
    $conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
    
    $col = isset($_GET['col']) ? $_GET['col'] : 'ID_ANGAJAT';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
    $newDir = ($dir == 'ASC') ? 'DESC' : 'ASC';

    // Lista coloanelor reale (inclusiv FK ID_JOB)
    $allowed = ['ID_ANGAJAT', 'NUME', 'PRENUME', 'TELEFON', 'IBAN', 'CNP', 'ID_JOB'];
    if(!in_array($col, $allowed)) $col = 'ID_ANGAJAT';

    // SELECT SIMPLU, FARA JOIN
    $sql = "SELECT * FROM ANGAJAT ORDER BY $col $dir";
            
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    ?>
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th><a href="?col=ID_ANGAJAT&dir=<?php echo $newDir; ?>">ID ⇅</a></th>
                <th><a href="?col=NUME&dir=<?php echo $newDir; ?>">Nume ⇅</a></th>
                <th><a href="?col=PRENUME&dir=<?php echo $newDir; ?>">Prenume ⇅</a></th>
                <th><a href="?col=TELEFON&dir=<?php echo $newDir; ?>">Telefon ⇅</a></th>
                <th><a href="?col=IBAN&dir=<?php echo $newDir; ?>">IBAN ⇅</a></th>
                <th><a href="?col=CNP&dir=<?php echo $newDir; ?>">CNP ⇅</a></th>
                <th><a href="?col=ID_JOB&dir=<?php echo $newDir; ?>">ID JOB (FK) ⇅</a></th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
            <tr>
                <td><?php echo $row['ID_ANGAJAT']; ?></td>
                <td><?php echo $row['NUME']; ?></td>
                <td><?php echo $row['PRENUME']; ?></td>
                <td><?php echo $row['TELEFON']; ?></td>
                <td style="font-size:0.8em"><?php echo $row['IBAN']; ?></td>
                <td><?php echo $row['CNP']; ?></td>
                <td><?php echo $row['ID_JOB']; ?></td>
                <td>
                    <a href="edit.php?table=angajat&id=<?php echo $row['ID_ANGAJAT']; ?>" class="btn btn-sm btn-success">Editează</a>
                    <a href="delete.php?table=angajat&pk=id_angajat&id=<?php echo $row['ID_ANGAJAT']; ?>" class="btn btn-sm btn-danger">Șterge</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>