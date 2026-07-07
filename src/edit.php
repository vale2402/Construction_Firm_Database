<?php
$conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');
if (!$conn) { $e = oci_error(); die($e['message']); }

if (!isset($_GET['table']) || !isset($_GET['id'])) { die("Parametri lipsă."); }

$table = strtoupper($_GET['table']);
$id = $_GET['id'];
$pk_col = ""; 

// HARTA REDIRECȚIONĂRILOR
$redirect_map = [
    'ANGAJAT'  => 'angajati.php',
    'LUCRARE'  => 'index.php', // Sau lucrari.php daca ai redenumit
    'UTILAJ'   => 'utilaje.php',
    'JOB'      => 'job.php',
    'CLIENT'   => 'client.php',
    'MATERIAL' => 'material.php',
    'FURNIZOR' => 'furnizor.php',
    'PONTAJ'   => 'pontaj.php',
    'CONSUM'   => 'consum.php',
    'OFERTA'   => 'oferta.php'
];
$base_redirect = isset($redirect_map[$table]) ? $redirect_map[$table] : "index.php";

// DETERMINARE PK
switch ($table) {
    case 'LUCRARE':   $pk_col = 'ID_LUCRARE'; break;
    case 'ANGAJAT':   $pk_col = 'ID_ANGAJAT'; break;
    case 'UTILAJ':    $pk_col = 'NR_INMATRICULARE'; break;
    case 'JOB':       $pk_col = 'ID_JOB'; break;
    case 'CLIENT':    $pk_col = 'CUI'; break;
    case 'MATERIAL':  $pk_col = 'ID_MATERIAL'; break;
    case 'FURNIZOR':  $pk_col = 'ID_FURNIZOR'; break;
    case 'PONTAJ':    $pk_col = 'ID_PONTAJ'; break;
    case 'CONSUM':    $pk_col = 'ID_CONSUM'; break;
    default: die("Tabel necunoscut.");
}

// LOGICA UPDATE
if (isset($_POST['save_changes'])) {
    $sql_update = "";
    
    // --- TABELE CU CHEI STRAINE ADAUGATE ---
    
    if ($table == 'ANGAJAT') {
        $nume = $_POST['nume']; $prenume = $_POST['prenume']; $tel = $_POST['telefon']; 
        $iban = $_POST['iban']; $cnp = $_POST['cnp'];
        $id_job = $_POST['id_job']; // FK
        $sql_update = "UPDATE ANGAJAT SET NUME='$nume', PRENUME='$prenume', TELEFON='$tel', IBAN='$iban', CNP='$cnp', ID_JOB=$id_job WHERE ID_ANGAJAT=$id";
    }
    elseif ($table == 'LUCRARE') {
        $nume = $_POST['nume']; $buget = $_POST['buget']; $status = $_POST['status'];
        $cui = $_POST['cui']; // FK Client
        $sql_update = "UPDATE LUCRARE SET NUME_LUCRARE='$nume', BUGET=$buget, STATUS='$status', CUI='$cui' WHERE ID_LUCRARE=$id";
    }
    elseif ($table == 'PONTAJ') {
        $nr_ore = $_POST['nr_ore'];
        $data_lucru = $_POST['data_lucru'];
        $id_ang = $_POST['id_angajat']; // FK
        $id_luc = $_POST['id_lucrare']; // FK
        $sql_update = "UPDATE PONTAJ SET NR_ORE=$nr_ore, DATA_LUCRU=TO_DATE('$data_lucru', 'YYYY-MM-DD'), ID_ANGAJAT=$id_ang, ID_LUCRARE=$id_luc WHERE ID_PONTAJ=$id";
    }
    elseif ($table == 'CONSUM') {
        $cant_mat = !empty($_POST['cant_mat']) ? $_POST['cant_mat'] : 0;
        $cant_util = !empty($_POST['cant_util']) ? $_POST['cant_util'] : 0;
        $data_consum = $_POST['data_consum'];
        
        $id_lucr = $_POST['id_lucrare']; // FK
        // Tratare NULL pentru Material/Utilaj daca sunt goale
        $id_mat = !empty($_POST['id_material']) ? $_POST['id_material'] : "NULL";
        $nr_inm = !empty($_POST['nr_inmatriculare']) ? "'".$_POST['nr_inmatriculare']."'" : "NULL";

        $sql_update = "UPDATE CONSUM SET CANTITATE_MATERIAL=$cant_mat, CANTITATE_UTILAJ=$cant_util, DATA_CONSUM=TO_DATE('$data_consum', 'YYYY-MM-DD'), ID_LUCRARE=$id_lucr, ID_MATERIAL=$id_mat, NR_INMATRICULARE=$nr_inm WHERE ID_CONSUM=$id";
    }

    // --- RESTUL TABELELOR (STANDARD) ---
    elseif ($table == 'UTILAJ') {
        $marca = $_POST['marca']; $denumire = $_POST['denumire']; $cost = $_POST['cost']; $um = $_POST['um'];
        $sql_update = "UPDATE UTILAJ SET MARCA='$marca', DENUMIRE='$denumire', COST_UNITATE=$cost, UNITATE_MASURA='$um' WHERE NR_INMATRICULARE='$id'";
    }
    elseif ($table == 'JOB') {
        $titlu = $_POST['titlu']; $salariu = $_POST['salariu'];
        $sql_update = "UPDATE JOB SET TITLU_JOB='$titlu', SALARIU_LUNAR=$salariu WHERE ID_JOB=$id";
    }
    elseif ($table == 'CLIENT') {
        $nume = $_POST['nume']; $tel = $_POST['telefon']; $email = $_POST['email']; $oras = $_POST['oras'];
        $sql_update = "UPDATE CLIENT SET NUME_CONTRACTOR='$nume', TELEFON='$tel', EMAIL='$email', ORAS='$oras' WHERE CUI='$id'";
    }
    elseif ($table == 'MATERIAL') {
        $nume = $_POST['nume']; $um = $_POST['um']; $pret = $_POST['pret'];
        $sql_update = "UPDATE MATERIAL SET NUME_MATERIAL='$nume', UNITATE_MASURA='$um', PRET_UNITATE=$pret WHERE ID_MATERIAL=$id";
    }
    elseif ($table == 'FURNIZOR') {
        $nume = $_POST['nume']; $cui = $_POST['cui']; $banca = $_POST['banca']; $iban = $_POST['iban']; $tel = $_POST['telefon']; $oras = $_POST['oras'];
        $sql_update = "UPDATE FURNIZOR SET NUME_FIRMA='$nume', CUI='$cui', NUME_BANCA='$banca', IBAN='$iban', TELEFON='$tel', ORAS='$oras' WHERE ID_FURNIZOR=$id";
    }

    if ($sql_update) {
        $stid = oci_parse($conn, $sql_update);
        // Prindem erorile (ex: ORA-02291 - Parent key not found)
        if (@oci_execute($stid)) {
            header("Location: " . $base_redirect . "?status=success");
            exit();
        } else {
            $e = oci_error($stid); 
            echo "<div class='alert alert-danger m-3'><strong>Eroare Oracle:</strong> " . $e['message'] . "</div>";
            if ($e['code'] == 2291) {
                echo "<div class='alert alert-warning m-3'>Verifică dacă ID-ul (Cheia Străină) introdus există în tabelul părinte!</div>";
            }
        }
    }
}

// CITIRE DATE
if (is_numeric($id)) { $sql = "SELECT * FROM $table WHERE $pk_col = $id"; } 
else { $sql = "SELECT * FROM $table WHERE $pk_col = '$id'"; }

$stid = oci_parse($conn, $sql);
oci_execute($stid);
$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

if (!$row) die("Înregistrarea nu a fost găsită.");

function safeEcho($arr, $key) { echo isset($arr[$key]) ? $arr[$key] : ''; }
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"> <title>Editare <?php echo $table; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    
    <div class="card mx-auto shadow" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">
            Editare: <strong><?php echo $table; ?></strong>
        </div>
        <div class="card-body">
            <form method="post">
                
                <div class="mb-3">
                    <label class="fw-bold text-secondary">ID / Cheie Primară (Fix)</label>
                    <input type="text" class="form-control bg-light" value="<?php echo $id; ?>" disabled>
                </div>
                <hr>

                <?php if ($table == 'ANGAJAT'): ?>
                    <div class="mb-3"><label>Nume</label><input type="text" name="nume" class="form-control" value="<?php safeEcho($row, 'NUME'); ?>"></div>
                    <div class="mb-3"><label>Prenume</label><input type="text" name="prenume" class="form-control" value="<?php safeEcho($row, 'PRENUME'); ?>"></div>
                    <div class="mb-3"><label>Telefon</label><input type="text" name="telefon" class="form-control" value="<?php safeEcho($row, 'TELEFON'); ?>"></div>
                    <div class="mb-3"><label>IBAN</label><input type="text" name="iban" class="form-control" value="<?php safeEcho($row, 'IBAN'); ?>"></div>
                    <div class="mb-3"><label>CNP</label><input type="text" name="cnp" class="form-control" value="<?php safeEcho($row, 'CNP'); ?>"></div>
                    <div class="mb-3 bg-warning p-2 rounded bg-opacity-10">
                        <label class="fw-bold">ID JOB (FK)</label>
                        <input type="number" name="id_job" class="form-control" value="<?php safeEcho($row, 'ID_JOB'); ?>">
                    </div>

                <?php elseif ($table == 'LUCRARE'): ?>
                    <div class="mb-3"><label>Nume Lucrare</label><input type="text" name="nume" class="form-control" value="<?php safeEcho($row, 'NUME_LUCRARE'); ?>"></div>
                    <div class="mb-3"><label>Buget</label><input type="number" name="buget" class="form-control" value="<?php safeEcho($row, 'BUGET'); ?>"></div>
                    <div class="mb-3"><label>Status</label>
                        <select name="status" class="form-select">
                            <option value="Activ" <?php if(isset($row['STATUS']) && $row['STATUS']=='Activ') echo 'selected'; ?>>Activ</option>
                            <option value="Finalizat" <?php if(isset($row['STATUS']) && $row['STATUS']=='Finalizat') echo 'selected'; ?>>Finalizat</option>
                            <option value="In asteptare" <?php if(isset($row['STATUS']) && $row['STATUS']=='In asteptare') echo 'selected'; ?>>In asteptare</option>
                        </select>
                    </div>
                    <div class="mb-3 bg-warning p-2 rounded bg-opacity-10">
                        <label class="fw-bold">CUI Client (FK)</label>
                        <input type="text" name="cui" class="form-control" value="<?php safeEcho($row, 'CUI'); ?>">
                    </div>

                <?php elseif ($table == 'PONTAJ'): ?>
                    <div class="mb-3"><label>Număr Ore</label><input type="number" name="nr_ore" class="form-control" value="<?php safeEcho($row, 'NR_ORE'); ?>"></div>
                    <div class="mb-3"><label>Data Lucru</label>
                        <input type="date" name="data_lucru" class="form-control" value="<?php echo isset($row['DATA_LUCRU']) ? date('Y-m-d', strtotime($row['DATA_LUCRU'])) : ''; ?>">
                    </div>
                    <div class="mb-3 bg-warning p-2 rounded bg-opacity-10">
                        <label class="fw-bold">ID Angajat (FK)</label>
                        <input type="number" name="id_angajat" class="form-control" value="<?php safeEcho($row, 'ID_ANGAJAT'); ?>">
                        <label class="fw-bold mt-2">ID Lucrare (FK)</label>
                        <input type="number" name="id_lucrare" class="form-control" value="<?php safeEcho($row, 'ID_LUCRARE'); ?>">
                    </div>

                <?php elseif ($table == 'CONSUM'): ?>
                    <div class="mb-3"><label>Cantitate Material</label><input type="number" name="cant_mat" class="form-control" value="<?php safeEcho($row, 'CANTITATE_MATERIAL'); ?>"></div>
                    <div class="mb-3"><label>Cantitate Utilaj</label><input type="number" name="cant_util" class="form-control" value="<?php safeEcho($row, 'CANTITATE_UTILAJ'); ?>"></div>
                    <div class="mb-3"><label>Data Consum</label>
                        <input type="date" name="data_consum" class="form-control" value="<?php echo isset($row['DATA_CONSUM']) ? date('Y-m-d', strtotime($row['DATA_CONSUM'])) : ''; ?>">
                    </div>
                    <div class="mb-3 bg-warning p-2 rounded bg-opacity-10">
                        <label class="fw-bold">ID Lucrare (FK)</label>
                        <input type="number" name="id_lucrare" class="form-control" value="<?php safeEcho($row, 'ID_LUCRARE'); ?>">
                        
                        <label class="fw-bold mt-2">ID Material (FK)</label>
                        <input type="number" name="id_material" class="form-control" value="<?php safeEcho($row, 'ID_MATERIAL'); ?>" placeholder="Lasă gol pentru NULL">

                        <label class="fw-bold mt-2">Nr Inmatriculare (FK)</label>
                        <input type="text" name="nr_inmatriculare" class="form-control" value="<?php safeEcho($row, 'NR_INMATRICULARE'); ?>" placeholder="Lasă gol pentru NULL">
                    </div>

                <?php elseif ($table == 'UTILAJ'): ?>
                    <div class="mb-3"><label>Marca</label><input type="text" name="marca" class="form-control" value="<?php safeEcho($row, 'MARCA'); ?>"></div>
                    <div class="mb-3"><label>Denumire</label><input type="text" name="denumire" class="form-control" value="<?php safeEcho($row, 'DENUMIRE'); ?>"></div>
                    <div class="mb-3"><label>Cost Unitar</label><input type="number" name="cost" class="form-control" value="<?php safeEcho($row, 'COST_UNITATE'); ?>"></div>
                    <div class="mb-3"><label>Unitate Masura</label><input type="text" name="um" class="form-control" value="<?php safeEcho($row, 'UNITATE_MASURA'); ?>"></div>

                <?php elseif ($table == 'JOB'): ?>
                    <div class="mb-3"><label>Titlu Job</label><input type="text" name="titlu" class="form-control" value="<?php safeEcho($row, 'TITLU_JOB'); ?>"></div>
                    <div class="mb-3"><label>Salariu Lunar</label><input type="number" name="salariu" class="form-control" value="<?php safeEcho($row, 'SALARIU_LUNAR'); ?>"></div>

                <?php elseif ($table == 'CLIENT'): ?>
                    <div class="mb-3"><label>Nume Contractor</label><input type="text" name="nume" class="form-control" value="<?php safeEcho($row, 'NUME_CONTRACTOR'); ?>"></div>
                    <div class="mb-3"><label>Telefon</label><input type="text" name="telefon" class="form-control" value="<?php safeEcho($row, 'TELEFON'); ?>"></div>
                    <div class="mb-3"><label>Email</label><input type="text" name="email" class="form-control" value="<?php safeEcho($row, 'EMAIL'); ?>"></div>
                    <div class="mb-3"><label>Oraș</label><input type="text" name="oras" class="form-control" value="<?php safeEcho($row, 'ORAS'); ?>"></div>
                
                <?php elseif ($table == 'MATERIAL'): ?>
                    <div class="mb-3"><label>Nume Material</label><input type="text" name="nume" class="form-control" value="<?php safeEcho($row, 'NUME_MATERIAL'); ?>"></div>
                    <div class="mb-3"><label>U.M.</label><input type="text" name="um" class="form-control" value="<?php safeEcho($row, 'UNITATE_MASURA'); ?>"></div>
                    <div class="mb-3"><label>Preț Unitar</label><input type="number" step="0.01" name="pret" class="form-control" value="<?php safeEcho($row, 'PRET_UNITATE'); ?>"></div>

                <?php elseif ($table == 'FURNIZOR'): ?>
                    <div class="mb-3"><label>Nume Firma</label><input type="text" name="nume" class="form-control" value="<?php safeEcho($row, 'NUME_FIRMA'); ?>"></div>
                    <div class="mb-3"><label>CUI</label><input type="text" name="cui" class="form-control" value="<?php safeEcho($row, 'CUI'); ?>"></div>
                    <div class="mb-3"><label>Banca</label><input type="text" name="banca" class="form-control" value="<?php safeEcho($row, 'NUME_BANCA'); ?>"></div>
                    <div class="mb-3"><label>IBAN</label><input type="text" name="iban" class="form-control" value="<?php safeEcho($row, 'IBAN'); ?>"></div>
                    <div class="mb-3"><label>Telefon</label><input type="text" name="telefon" class="form-control" value="<?php safeEcho($row, 'TELEFON'); ?>"></div>
                    <div class="mb-3"><label>Oraș</label><input type="text" name="oras" class="form-control" value="<?php safeEcho($row, 'ORAS'); ?>"></div>

                <?php endif; ?>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="save_changes" class="btn btn-success flex-grow-1">Salvează Modificările</button>
                    <a href="<?php echo $base_redirect; ?>" class="btn btn-secondary">Anulează</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>