<?php
ob_start();

$conn = oci_connect('STUDENT', 'STUDENT', 'localhost/orcl');

if (!$conn) {
    $e = oci_error();
    die("Eroare conexiune: " . $e['message']);
}

if (isset($_GET['table']) && isset($_GET['pk']) && isset($_GET['id'])) {
    
    $table = $_GET['table']; 
    $pk    = $_GET['pk'];    
    $id    = $_GET['id'];    

    // Executăm ștergerea
    $sql = "DELETE FROM " . $table . " WHERE " . $pk . " = :id";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":id", $id);
    
    // COMMIT automat
    $r = oci_execute($stid, OCI_COMMIT_ON_SUCCESS);

    if ($r) {
        ob_end_clean();
        
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            $url_inapoi = $_SERVER['HTTP_REFERER'];
            
            $url_inapoi = str_replace(['?msg=deleted', '&msg=deleted'], '', $url_inapoi);
            
            $separator = (strpos($url_inapoi, '?') === false) ? '?' : '&';
            
            header("Location: " . $url_inapoi . $separator . "msg=deleted");
        } else {
            header("Location: index.php?msg=deleted");
        }
        exit();
    } else {
        $e = oci_error($stid);
        echo "Eroare la ștergere: " . $e['message'];
        echo "<br><a href='index.php'>Înapoi</a>";
    }
}
?>