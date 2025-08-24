<?php
$host = "localhost";
$user = "root";
$pass = "reynaldo066512";
$dbname = "reporte_incidencias";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
