<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Raport D - Pontaj Angajați</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Cerința d) Funcții grup și clauză HAVING</h5>
        </div>
        <div class="card-body">
            <p class="card-text">
                <strong>Cerință:</strong> Să se determine numărul de intrări în pontaj și suma totală a orelor lucrate 
                pentru fiecare angajat, afișând doar acei angajați care au acumulat un total de cel puțin 
                <code>10 ore</code> de muncă.
            </p>
        </div>
    </div>

    <?php
    $conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
    if (!$conn) die("<div class='alert alert-danger'>Eroare conexiune</div>");

    $sql = "SELECT 
                a.NUME, 
                a.PRENUME, 
                j.TITLU_JOB,
                COUNT(p.ID_PONTAJ) as NR_INTRARI,
                SUM(p.NR_ORE) as TOTAL_ORE
            FROM ANGAJAT a
            JOIN JOB j ON a.ID_JOB = j.ID_JOB
            JOIN PONTAJ p ON a.ID_ANGAJAT = p.ID_ANGAJAT
            GROUP BY a.ID_ANGAJAT, a.NUME, a.PRENUME, j.TITLU_JOB
            HAVING SUM(p.NR_ORE) >= 10";

    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    ?>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-success text-white">
                    <tr>
                        <th>Nume Angajat</th>
                        <th>Funcție (Job)</th>
                        <th class="text-center">Nr. Intrări Pontaj</th> <th class="text-end">Total Ore Lucrate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rows = 0;
                    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): 
                        $rows++;
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo $row['NUME'] . ' ' . $row['PRENUME']; ?></td>
                        <td><?php echo $row['TITLU_JOB']; ?></td>
                        <td class="text-center fw-bold"><?php echo $row['NR_INTRARI']; ?></td>
                        <td class="text-end fw-bold fs-5 text-success">
                            <?php echo $row['TOTAL_ORE']; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if ($rows == 0): ?>
                    <tr><td colspan="4" class="text-center p-4 text-muted">Nu există angajați cu >= 10 ore lucrate.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>