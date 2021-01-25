<?php
session_start();
if ($_SESSION['tipo_usuario'] != 'admin') {
    header('location:index.php');
}

// traer ultimo torneo
require_once 'Conexion.php';
$dbh = new Conexion;
$sth = $dbh->prepare("SELECT `torneo` FROM `torneos` ORDER BY id DESC LIMIT 1");
$sth->execute();
$torneo = $sth->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['guardar-equipo'])) {
    // se lleno el formulario y se hizo click en el boton guardar
    $equipo = strtoupper($_POST['nombre']);
    $categoria = $_POST['categoria'];
    $anio = $_POST['anio'];
    $institucion = strtoupper($_POST['institucion']);
    $direccion = strtoupper($_POST['direccion']);
    $localidad = strtoupper($_POST['localidad']);
    $delegada1 = strtoupper($_POST['delegada1']);
    $nivel = $_POST['nivel'];

    $sth = $dbh->prepare('select * from equipos where torneo = :torneo and nombre_equipo = :equipo');
    $sth->execute([':torneo'=>$torneo['torneo'], ':equipo'=>$equipo]);
    $datosEquipo = $sth->fetch(PDO::FETCH_ASSOC);

    if (isset($datosEquipo['id'])) {
        // el equipo ya existe, entonces se actualizan los datos
        $sth = $dbh->prepare('update equipos set categoria=:categoria, torneo=:torneo, anio=:anio, nombre_institucion=:institucion, nombre_equipo=:equipo, direccion=:direccion, localidad=:localidad, documento_delegada_1=:delegada1, nivel=:nivel where id = :id');
        $sth->execute([':categoria'=>$categoria, ':torneo'=>$torneo['torneo'], ':anio'=>$anio, ':institucion'=>$institucion, ':equipo'=>$equipo, ':direccion'=>$direccion, ':localidad'=>$localidad, ':delegada1'=>$delegada1, ':nivel'=>$nivel, ':id'=>$datosEquipo['id']]);    
    } else {
        // el equipo no existe, entonces se da de alta
        $sth = $dbh->prepare('insert into equipos (categoria, torneo, anio, nombre_institucion, nombre_equipo, direccion, localidad, documento_delegada_1, nivel) values (:categoria, :torneo, :anio, :institucion, :equipo, :direccion, :localidad, :delegada1, :nivel)');
        $sth->execute([':categoria'=>$categoria, ':torneo'=>$torneo['torneo'], ':anio'=>$anio, ':institucion'=>$institucion, ':equipo'=>$equipo, ':direccion'=>$direccion, ':localidad'=>$localidad, ':delegada1'=>$delegada1, ':nivel'=>$nivel]);
    
        // alta tabla de preferencias
        $sth = $dbh->prepare('insert into preferencias_horarias (categoria, torneo, equipo, turno_1, turno_2, turno_3, turno_4, turno_5, turno_6, cancha_hora, idcancha) values (:categoria, :torneo, :equipo, :t1, :t2, :t3, :t4, :t5, :t6, :canchaHora, :cancha)');
        $sth->execute([':categoria'=>$categoria, ':torneo'=>$torneo['torneo'], ':equipo'=>$equipo, ':t1'=>1, ':t2'=>1, ':t3'=>1, ':t4'=>1, ':t5'=>1, ':t6'=>1, ':canchaHora'=>'C', ':cancha'=>'IND']);
    }

    $_GET['equipo'] = $equipo;
}

if (isset($_GET['equipo'])) {
    $equipo = $_GET['equipo'];
    $sth = $dbh->prepare('select * from equipos where torneo = :torneo and nombre_equipo = :equipo');
    $sth->execute([':torneo'=>$torneo['torneo'], ':equipo'=>$equipo]);
    $datosEquipo = $sth->fetch(PDO::FETCH_ASSOC);
}
// incluir head
require_once 'include/header.php';
?>
<link rel="stylesheet" href="css/admManagerEquipos.css">
<?php
// incluir navbar
require_once 'include/navbar.php';
?>

<section class="main-container">
    <h2>alta/modificación de equipo</h2>
    <form action="<?=$_SERVER['PHP_SELF'] ?>" method="post">
    <div class="card-container">
        <div class="form-group">
            <label for="">categoría</label>
            <select name="categoria" class="selector" id="selector-equipo">
                <option value="MAXIVOLEY">MAXIVOLEY</option>
            </select>
        </div>
        <div class="form-group">
            <label for="">torneo</label>
            <input type="text" name="torneo" readonly value="<?=$torneo['torneo']?>" required>
        </div>
        <div class="form-group form-input">
            <label for="">año</label>
            <input type="text" name="anio" required 
                <?php if(isset($_GET['equipo'])) { ?> value="<?=$datosEquipo['anio'] ?>" <?php } ?>
            >
        </div>
        <div class="form-group form-input">
            <label for="">nombre</label>
            <input type="text" name="nombre" required
            <?php if(isset($_GET['equipo'])) { ?> value="<?=$datosEquipo['nombre_equipo'] ?>" <?php } ?>
            >
        </div>
        <div class="form-group form-input">
            <label for="">institucción</label>
            <input type="text" name="institucion" required
            <?php if(isset($_GET['equipo'])) { ?> value="<?=$datosEquipo['nombre_institucion'] ?>" <?php } ?>
            >
        </div>
        <div class="form-group form-input">
            <label for="">dirección</label>
            <input type="text" name="direccion"
            <?php if(isset($_GET['equipo'])) { ?> value="<?=$datosEquipo['direccion'] ?>" <?php } ?>
            >
        </div>
        <div class="form-group form-input">
            <label for="">localidad</label>
            <input type="text" name="localidad" required
            <?php if(isset($_GET['equipo'])) { ?> value="<?=$datosEquipo['localidad'] ?>" <?php } ?>
            >
        </div>
        <div class="form-group form-input">
            <label for="">dni primera delelgada</label>
            <input type="text" name="delegada1" id="dniDelegada" required
            <?php if(isset($_GET['equipo'])) { ?> value="<?=$datosEquipo['documento_delegada_1'] ?>" <?php } ?>
            >
            <select class="selector-delegada" id="selector-delegada" size="3"></select>
        </div>
        <div class="form-group">
            <label for="">nivel</label>
            <select name="nivel" class="selector">
                <option value="A" 
                <?php if(isset($_GET['equipo'])) {
                    if ($datosEquipo['nivel'] == 'A') {
                        echo 'selected';
                    }
                } ?>
                >A</option>

                <option value="B" 
                <?php if(isset($_GET['equipo'])) {
                    if ($datosEquipo['nivel'] == 'B') {
                        echo 'selected';
                    }
                } ?>
                >B</option>
                
                <option value="C" 
                <?php if(isset($_GET['equipo'])) {
                    if ($datosEquipo['nivel'] == 'C') {
                        echo 'selected';
                    }
                } ?>
                >C</option>

                <option value="D" 
                <?php if(isset($_GET['equipo'])) {
                    if ($datosEquipo['nivel'] == 'D') {
                        echo 'selected';
                    }
                } ?>
                >D</option>
            </select>
        </div>
    </div>
    <div class="btn-container">
        <button type="submit" class="form-btn boton-guardar" name="guardar-equipo">guardar cambios</button>
    </div>
    </form>
</section>

<script src="scripts/admManagerEquipos.js"></script>
<?php
require_once 'include/footer.php';
?>