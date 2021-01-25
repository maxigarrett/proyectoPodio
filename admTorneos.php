<?php
session_start();
if (!isset($_SESSION['tipo_usuario'])) {
    header('location:index.php');
}
if ($_SESSION['tipo_usuario'] != 'admin') {
    header('location:index.php');
}

if (isset($_POST['guardar-torneo'])) {
    require_once 'Conexion.php';
    $dbh = new Conexion;
    $sth = $dbh->prepare('select * from torneos where torneo = :torneo');
    $sth->execute([':torneo'=>strtoupper($_POST['torneo'])]);
    $datos_torneo = $sth->fetch(PDO::FETCH_ASSOC);
    if (isset($datos_torneo['torneo'])) {
        $sth = $dbh->prepare('update torneos set categoria = :categoria, anio = :anio, nombre = :nombre, fecha_inicio = :inicio, fecha_fin = :fin, fecha_cierre_lista_buena_fe = :cierre, prefijo_partidos = :prefijo where torneo = :torneo');
        $sth->execute([':categoria'=>$_POST['categoria'], ':anio'=>$_POST['anio'], ':nombre'=>$_POST['nombre'], ':inicio'=>$_POST['inicio'], ':fin'=>$_POST['fin'], ':cierre'=>$_POST['cierre-lista'], ':prefijo'=>$_POST['prefijo'], ':torneo'=>$_POST['torneo']]);
    } else {
        $sth = $dbh->prepare('insert into torneos (torneo, categoria, anio, nombre, fecha_inicio, fecha_fin, fecha_cierre_lista_buena_fe, prefijo_partidos) values (:torneo, :categoria, :anio, :nombre, :inicio, :fin, :cierre, :prefijo)');
        $sth->execute([':torneo'=>strtoupper($_POST['torneo']), ':categoria'=>$_POST['categoria'], ':anio'=>$_POST['anio'], ':nombre'=>$_POST['nombre'], ':inicio'=>$_POST['inicio'], ':fin'=>$_POST['fin'], ':cierre'=>$_POST['cierre-lista'], ':prefijo'=>$_POST['prefijo']]);
    }
}

// incluir head y metadata, links css
require_once 'include/header.php';
?>
<link rel="stylesheet" href="css/admTorneos.css">
<?php
// incluir cierre del head y apertura del body mas todo el navbar
require_once 'include/navbar.php';
?>

<section class="main-container">
    <h2>Torneo</h2>
    <p class="mensaje-info">Seleccionar torneo para modificar. Si el torneo no existe, completar todos los campos para darlo de alta.</p>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="card-container">
            <div class="form-group form-input selector-container">
                <label for="">torneo</label>
                <input type="text" name="torneo" id="torneo-torneo" required>
                <select name="selector" class="selector-filtro" id="sel" size="3"></select>
            </div>
            <div class="form-group">
                <label for="">categoria</label>
                <select name="categoria" id="categoria-torneo" class="selector">
                    <option value="MAXIVOLEY">MAXIVOLEY</option>
                </select>
            </div>
            <div class="form-group form-input">
                <label for="">año</label>
                <input type="text" name="anio" id="anio-torneo" required>
            </div>
            <div class="form-group form-input">
                <label for="">nombre</label>
                <input type="text" name="nombre" id="nombre-torneo" required>
            </div>
        </div>
        <div class="card-container">
            <div class="form-group form-input">
                <label for="">fecha inicio</label>
                <input type="date" name="inicio" id="inicio-torneo" required>
            </div>
            <div class="form-group form-input">
                <label for="">fecha fin</label>
                <input type="date" name="fin" id="fin-torneo" required>
            </div>
            <div class="form-group form-input">
                <label for="">cierre lista buena fe</label>
                <input type="date" name="cierre-lista" id="cierre-lista-torneo" required>
            </div>
            <div class="form-group form-input">
                <label for="">prefijo partidos</label>
                <input type="text" name="prefijo" id="prefijo-torneo" required>
            </div>
        </div>

        <div class="form-btn-container">
            <button type="submit" class="form-btn boton-guardar" name="guardar-torneo">guardar</button>
        </div>

    </form>
</section>

<script src="scripts/admTorneos.js"></script>
<?php
// incluir footer con js navbar y cierre de body y html
require_once 'include/footer.php';
?>