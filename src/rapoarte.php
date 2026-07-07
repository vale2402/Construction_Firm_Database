<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Centru Rapoarte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hover-card:hover { transform: translateY(-5px); transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .icon-box { font-size: 3rem; margin-bottom: 15px; }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">📊 Rapoarte Manageriale</h2>
        <p class="text-muted">Selectează tipul de raport pe care dorești să îl generezi.</p>
    </div>

    <div class="row g-4 justify-content-center">
        
        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 hover-card text-center p-4">
                <div class="card-body">
                    <div class="icon-box text-primary">
                        <i class="bi bi-building-gear"></i>
                    </div>
                    <h4 class="card-title fw-bold">Consumuri Materiale</h4>
                    <h6 class="text-muted mb-3">(Cerința C)</h6>
                    <p class="card-text text-secondary">
                        Raport complex cu filtre pe <strong>Județ</strong> și <strong>Cantitate</strong>. 
                        Analizează distribuția materialelor pe clienți.
                    </p>
                    <a href="rapoarte_c.php" class="btn btn-outline-primary w-75 fw-bold stretched-link">
                        Deschide Raport
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 hover-card text-center p-4">
                <div class="card-body">
                    <div class="icon-box text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h4 class="card-title fw-bold">Performanță Angajați</h4>
                    <h6 class="text-muted mb-3">(Cerința D)</h6>
                    <p class="card-text text-secondary">
                        Raport agregat folosind funcții de grup și <strong>HAVING</strong>.
                        Top angajați în funcție de orele lucrate.
                    </p>
                    <a href="rapoarte_d.php" class="btn btn-outline-success w-75 fw-bold stretched-link">
                        Deschide Raport
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>