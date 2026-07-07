<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Valrob Transport</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Lucrări</a></li>
        <li class="nav-item"><a class="nav-link" href="angajati.php">Angajați</a></li>
        <li class="nav-item"><a class="nav-link" href="utilaje.php">Utilaje</a></li>
        <li class="nav-item"><a class="nav-link" href="job.php">Joburi</a></li>
        <li class="nav-item"><a class="nav-link" href="client.php">Clienți</a></li>
        <li class="nav-item"><a class="nav-link" href="material.php">Materiale</a></li>
        <li class="nav-item"><a class="nav-link" href="furnizor.php">Furnizori</a></li>
        <li class="nav-item"><a class="nav-link" href="pontaj.php">Pontaje</a></li>
        <li class="nav-item"><a class="nav-link" href="oferta.php">Oferte</a></li>
        <li class="nav-item"><a class="nav-link" href="consum.php">Consumuri</a></li>
        <li class="nav-item"><a class="nav-link" href="rapoarte.php">Rapoarte</a></li>
        <li class="nav-item"><a class="nav-link" href="vizualizari.php">Vizualizări</a></li>
      </ul>
      <span class="navbar-text text-white">
        Utilizator: STUDENT
      </span>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. DETECTARE MESAJ DE SUCCES (URL) ---
    const params = new URLSearchParams(window.location.search);
    if (params.get('status') === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Succes!',
            text: 'Modificările au fost salvate cu succes.',
            timer: 2000,
            showConfirmButton: false
        });
        // Curățăm URL-ul ca să nu apară mesajul la refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // --- 2. CONFIRMARE ȘTERGERE (Butoane Roșii) ---
    const deleteButtons = document.querySelectorAll('.btn-danger:not(.cascade-delete)');
    deleteButtons.forEach(button => {
        button.removeAttribute('onclick'); // Scoatem vechiul confirm simplu
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const linkDeStergere = this.getAttribute('href');
            
            Swal.fire({
                title: 'Ești sigur?',
                text: "Această acțiune este ireversibilă!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'DA, Șterge!',
                cancelButtonText: 'Anulează'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = linkDeStergere;
                }
            });
        });
    });
});
</script>