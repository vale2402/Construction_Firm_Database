<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Raport C - Consum Vâlcea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Cerința c) Interogare complexă (3 tabele, 2 condiții)</h5>
        </div>
        <div class="card-body">
            <p class="card-text">
                <strong>Cerință:</strong> Să se afișeze numele contractorului, orașul, denumirea lucrării și tipul materialului utilizat, 
                pentru toți clienții din județul <code>'Valcea'</code> în cazul cărora s-a înregistrat un consum de materiale 
                mai mare de <code>10</code> unități pe o singură intrare.
            </p>
        </div>
    </div>

    <?php
    $conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
    if (!$conn) die("<div class='alert alert-danger'>Eroare conexiune</div>");

    $sql = "SELECT 
                c.NUME_CONTRACTOR,    
                c.ORAS,
                l.NUME_LUCRARE,       
                m.NUME_MATERIAL,      
                m.UNITATE_MASURA,
                cons.CANTITATE_MATERIAL, 
                TO_CHAR(cons.DATA_CONSUM, 'DD-MM-YYYY') AS DATA_OP
            FROM CLIENT c
            JOIN LUCRARE l ON c.CUI = l.CUI
            JOIN CONSUM cons ON l.ID_LUCRARE = cons.ID_LUCRARE
            JOIN MATERIAL m ON cons.ID_MATERIAL = m.ID_MATERIAL
            WHERE c.JUDET = 'Valcea' AND cons.CANTITATE_MATERIAL > 10";

    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    ?>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Client</th>
                        <th>Oraș</th>
                        <th>Lucrare</th>
                        <th>Material</th>
                        <th class="text-end">Cantitate</th>
                        <th>U.M.</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 0;
                    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)): 
                        $counter++;
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo $row['NUME_CONTRACTOR']; ?></td>
                        <td><?php echo $row['ORAS']; ?></td>
                        <td><?php echo $row['NUME_LUCRARE']; ?></td>
                        <td class="text-primary fw-bold"><?php echo $row['NUME_MATERIAL']; ?></td>
                        <td class="text-end fs-5 fw-bold"><?php echo number_format($row['CANTITATE_MATERIAL'], 0); ?></td>
                        <td class="text-muted"><?php echo $row['UNITATE_MASURA']; ?></td>
                        <td><?php echo $row['DATA_OP']; ?></td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if ($counter == 0): ?>
                    <tr><td colspan="7" class="text-center p-4 text-muted">Nu există înregistrări pentru Vâlcea cu cantitate > 10.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end text-muted small">
            Total rezultate: <?php echo $counter; ?>
        </div>
    </div>
</div>

</body>
</html>