<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"> <title>Furnizori</title>
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
    <h3>Tabel: FURNIZOR</h3>
    <?php
    $conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
    
    $col = isset($_GET['col']) ? $_GET['col'] : 'ID_FURNIZOR';
    $dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
    $newDir = ($dir == 'ASC') ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM FURNIZOR ORDER BY $col $dir";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    ?>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th><a href="?col=ID_FURNIZOR&dir=<?php echo $newDir; ?>">ID ⇅</a></th>
                <th><a href="?col=NUME_FIRMA&dir=<?php echo $newDir; ?>">Nume Firmă ⇅</a></th>
                <th><a href="?col=CUI&dir=<?php echo $newDir; ?>">CUI ⇅</a></th>
                <th><a href="?col=NUME_BANCA&dir=<?php echo $newDir; ?>">Banca ⇅</a></th>
                <th><a href="?col=IBAN&dir=<?php echo $newDir; ?>">IBAN ⇅</a></th>
                <th><a href="?col=TELEFON&dir=<?php echo $newDir; ?>">Telefon ⇅</a></th>
                <th><a href="?col=ORAS&dir=<?php echo $newDir; ?>">Oraș ⇅</a></th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
            <tr>
                <td><?php echo $row['ID_FURNIZOR']; ?></td>
                <td><?php echo $row['NUME_FIRMA']; ?></td>
                <td><?php echo $row['CUI']; ?></td>
                <td><?php echo $row['NUME_BANCA']; ?></td>
                <td><?php echo $row['IBAN']; ?></td>
                <td><?php echo $row['TELEFON']; ?></td>
                <td><?php echo $row['ORAS']; ?></td>
                <td>
                    <a href="edit.php?table=furnizor&id=<?php echo $row['ID_FURNIZOR']; ?>" class="btn btn-sm btn-success">Editează</a>
                    <a href="delete.php?table=furnizor&pk=id_furnizor&id=<?php echo $row['ID_FURNIZOR']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ștergi?')">Șterge</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>