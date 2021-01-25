<?php
session_start();
if ($_SESSION['tipo_usuario'] != 'admin' || !isset($_GET['torneo']) || !isset($_GET['equipo']) || !isset($_GET['dni'])) {
    header('location:index.php');
}
$torneo = $_GET['torneo'];
$equipo = $_GET['equipo'];
$dni = $_GET['dni'];

require_once 'Conexion.php';
$dbh = new Conexion;

$sth = $dbh->prepare('delete from lista_buena_fe where torneo = :torneo and nombre_equipo = :equipo and documento = :dni');
$sth->execute([':torneo'=>$torneo, ':equipo'=>$equipo, ':dni'=>$dni]);

header("location:admIndex.php?equipo=$equipo");
?>