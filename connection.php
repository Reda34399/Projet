<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "tpdata";

$connection = mysqli_connect($host, $user, $password, $database);

if (!$connection) {
    die("Erreur de connection   : " . mysqli_connect_error());
}
?>
