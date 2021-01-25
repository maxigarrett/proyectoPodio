<?php
session_start();
if (!isset($_SESSION['tipo_usuario'])) {header('location:index.php');}
if ($_SESSION['tipo_usuario'] != 'admin') {header('location:index.php');}

$filtroTorneo = $_GET['torneo'].'%';

require_once 'Conexion.php';
$dbh = new Conexion;
$sth = $dbh->prepare('select * from torneos where torneo like :filtro order by id desc');
$sth->execute([':filtro'=>$filtroTorneo]);
$listaTorneos = $sth->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($listaTorneos);
?>