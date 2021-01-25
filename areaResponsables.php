<?php
session_start();
if (isset($_SESSION['dni'])) {
    if ($_SESSION['tipo_usuario'] == 'responsable') {
        $dni = $_SESSION['dni'];
        require_once 'Conexion.php';
        $dbh = new Conexion;

        // traer equipos de los que fue/es responsable para selector-equipo
        $query = 'select * from equipos where documento_delegada_1 = :dni1 or documento_delegada_2 = :dni2 or documento_delegada_3 = :dni3 or documento_entrenador = :dni4';
        $sthEquipos = $dbh->prepare($query);
        $sthEquipos->execute([':dni1' => $dni, ':dni2' => $dni, ':dni3' => $dni, ':dni4'=>$dni]);

        // se comprueba si se selecciono un equipo del selector o si existe la variable de sesion
        if ((isset($_POST['seleccionar-equipo']) && $_POST['selector-equipo'] != 'default') || isset($_SESSION['equipo'])) {

            // separo en un array los datos de nombre de equipo y torneo
            // si se selecciono un equipo desde el selector, uso el selector, sino la variable de sesion
            $equipo_torneo = ((isset($_POST['seleccionar-equipo']) && $_POST['selector-equipo'] != 'default')) ? explode('/', $_POST['selector-equipo']) : $_SESSION['equipo'];

            require_once 'Equipo.php';
            $equipo = new Equipo($equipo_torneo[0], $equipo_torneo[1]);

            // obtener datos de equipo para mostrar
            $datos_equipo = $equipo->getDatosEquipo();

            // obtener datos de responsables del equipo
            $delegada1 = ($datos_equipo['documento_delegada_1'] > 0) ? $equipo->getDatosDelegado($datos_equipo['documento_delegada_1']) : ['documento' => 0, 'nombres' => '', 'apellidos' => ''];
            $delegada2 = ($datos_equipo['documento_delegada_2'] > 0) ? $equipo->getDatosDelegado($datos_equipo['documento_delegada_2']) : ['documento' => 0, 'nombres' => '', 'apellidos' => ''];
            $delegada3 = ($datos_equipo['documento_delegada_3'] > 0) ? $equipo->getDatosDelegado($datos_equipo['documento_delegada_3']) : ['documento' => 0, 'nombres' => '', 'apellidos' => ''];
            $entrenador = ($datos_equipo['documento_entrenador'] > 0) ? $equipo->getDatosDelegado($datos_equipo['documento_entrenador']) : ['documento' => 0, 'nombres' => '', 'apellidos' => ''];

            // genero variable de sesion para recuperar los datos del equipo 
            // cuando me mueva a cambiar responsables, lista de buena fe y/o preferencias
            $_SESSION['equipo'] = $equipo_torneo;
        }
    } else {
        // no es responsable, no tiene permiso a esta parte del sitio, redirige a index
        header('location:index.php');
    }
} else {
    // no hay sesion, redirige a index
    header('location:index.php');
}
?>

<?php
// incluir header
require_once 'include/header.php';
?>

<!-- css especifico de la pagina -->
<link rel="stylesheet" href="css/areaResponsables.css">

<?php
// incluir navbar
require_once 'include/navbar.php';
?>

<!-- inicio contenido de la pagina -->

<!-- selector de equipo -->
<section class="main-container selector-equipo-container">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" class="form-selector">
        <select name="selector-equipo" class="selector-equipo">
            <option value="default">-- Seleccionar equipo --</option>

            <?php while ($equipo = $sthEquipos->fetch(PDO::FETCH_ASSOC)) { ?>

                <option value="<?php echo $equipo['nombre_equipo'] . '/' . $equipo['torneo'] ?>">
                    <?php echo $equipo['nombre_equipo'] . ' - ' . $equipo['torneo'] ?>
                </option>

            <?php } ?>
        </select>
        <button type="submit" class="form-btn" name="seleccionar-equipo">ver info de equipo</button>
    </form>
</section>
<!-- fin selector de equipo -->

<!-- inicio tarjeta info de equipo -->
<?php if (isset($datos_equipo)) { ?>
    <section class="main-container">
        <h2>Equipo: <?= $datos_equipo['nombre_equipo'] ?></h2>
        <div class="card-container">
            <div class="form-group">
                <label for="">categoría</label>
                <input type="text" readonly value="<?= $datos_equipo['categoria'] ?>">
            </div>
            <div class="form-group">
                <label for="">torneo</label>
                <input type="text" readonly value="<?= $datos_equipo['torneo'] ?>">
            </div>
            <div class="form-group">
                <label for="">institución</label>
                <input type="text" readonly value="<?= $datos_equipo['nombre_institucion'] ?>">
            </div>
            <div class="form-group">
                <label for="">Dirección declarada</label>
                <input type="text" readonly value="<?= $datos_equipo['direccion'] ?>">
            </div>
            <div class="form-group">
                <label for="">localidad</label>
                <input type="text" readonly value="<?= $datos_equipo['localidad'] ?>">
            </div>
        </div>
        <p class="form-section-title">responsables</p>
        <div class="card-container">
            <!-- si existen, se imprimen los responsables -->
            <?php
            if ($datos_equipo['documento_delegada_1'] > 0) { ?>
                <div class="form-group">
                    <label for="">primer/a delegado/a</label>
                    <input type="text" readonly value="<?= $delegada1['apellidos'] ?>, <?= $delegada1['nombres'] ?>">
                </div>
            <?php
            }
            if ($datos_equipo['documento_delegada_2'] > 0) { ?>
                <div class="form-group">
                    <label for="">segundo/a delegado/a</label>
                    <input type="text" readonly value="<?= $delegada2['apellidos'] ?>, <?= $delegada2['nombres'] ?>">
                </div>
            <?php
            }
            if ($datos_equipo['documento_delegada_3'] > 0) { ?>
                <div class="form-group">
                    <label for="">tercer/a delegado/a</label>
                    <input type="text" readonly value="<?= $delegada3['apellidos'] ?>, <?= $delegada3['nombres'] ?>">
                </div>
            <?php
            }
            if ($datos_equipo['documento_entrenador'] > 0) { ?>
                <div class="form-group">
                    <label for="">entrenador/a</label>
                    <input type="text" readonly value="<?= $entrenador['apellidos'] ?>, <?= $entrenador['nombres'] ?>">
                </div>
            <?php
            } ?>
        </div>

        <div class="card-container group-links-equipo">
            <div class="group-links">
                <a class="form-btn links-equipo" href="responsablesEquipo.php">administrar responsables</a>
            </div>
            <div class="group-links">
                <a class="form-btn links-equipo" href="listaBuenaFe.php">lista de buena fe</a>
            </div>
            <div class="group-links">
                <a class="form-btn links-equipo" href="preferenciaHoraria.php">preferencias hora/cancha</a>
            </div>
        </div>
    </section>
<?php
} ?>
<!-- fin tarjeta info de equipo -->

<!-- fin contenido de la pagina -->

<?php
// incluir js navbar y cierre de pagina
require_once 'include/footer.php';
?>