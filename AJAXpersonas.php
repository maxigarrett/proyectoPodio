<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || !isset($_GET['dni'])){
    header('location:index.php');
}

$dni = $_GET['dni'].'%';
require_once 'Conexion.php';
$dbh = new Conexion;
$sth = $dbh->prepare('select documento, apellidos, nombres, correo_electronico, fecha_nacimiento from personas where documento like :dni');
$sth->execute([':dni'=>$dni]);
$lista = $sth->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($lista);