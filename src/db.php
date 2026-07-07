<?php
$username = "";
$password = "";
$db = "";

$conn = oci_connect($username, $password, $db, 'AL32UTF8');

if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    die("Nu m-am putut conecta la Oracle!");
}
?>