<?php
session_start();
if ($_SESSION['tipo_usuario'] != 'admin') {
    header('location:index.php');
}

$torneo = $_GET['torneo'];

require_once 'Conexion.php';
$dbh = new Conexion;

$sth = $dbh->prepare('select * from equipos where torneo = :torneo');
$sth->execute([':torneo'=>$torneo]);

$equipos = $sth->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($equipos);