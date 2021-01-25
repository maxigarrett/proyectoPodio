<?php
session_start();
if ($_SESSION['tipo_usuario'] != 'admin') {
    header('location:index.php');
}

$torneo = $_GET['torneo'];
$equipo = $_GET['equipo'];

if(isset($_POST['eliminar-equipo'])) {
    require_once 'Conexion.php';
    $dbh = new Conexion;

    // eliminar de la tabla equipos
    $sth = $dbh->prepare('delete from equipos where torneo = :torneo and nombre_equipo = :equipo');
    $sth->execute([':torneo'=>$_POST['torneo'], ':equipo'=>$_POST['equipo']]);

    // eliminar preferencias horarias
    $sth = $dbh->prepare('delete from preferencias_horarias where torneo = :torneo and equipo = :equipo');
    $sth->execute([':torneo'=>$_POST['torneo'], ':equipo'=>$_POST['equipo']]);

    // eliminar lista de buena fe
    $sth = $dbh->prepare('delete from lista_buena_fe where torneo = :torneo and nombre_equipo = :equipo');
    $sth->execute([':torneo'=>$_POST['torneo'], ':equipo'=>$_POST['equipo']]);


    header('location:index.php');
}

require_once 'include/header.php';
?>
<link rel="stylesheet" href="css/admEliminarEquipo.css">
<?php
require_once 'include/navbar.php';
?>

<section class="main-container">
    <h2><?=$torneo ?></h2>
    <div class="form-group
    ">
            <div class="card-container">
                <label><?=$equipo ?></label>
            </div>
        
        
            <div class="card-container">
                <p class="mensaje-eliminar">
                    Está seguro de eliminar este equipo?
                    Además de quitar el equipo del torneo, también se eliminarán los registros de la lista de buena fe y las preferencias de la base de datos.
                </p>
            </div>
        
        <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="text" class="input-oculto" name="torneo" value="<?=$torneo ?>" readonly>
            <input type="text" class="input-oculto" name="equipo" value="<?=$equipo ?>" readonly>
            <div class="btn-container">
                <a href="admIndex.php" class="form-btn">cancelar</a>
                <button type="submit" class="form-btn" name="eliminar-equipo">eliminar</button>
            </div>
        </form>
    </div>
</section>