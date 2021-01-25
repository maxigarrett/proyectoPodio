<?php
session_start();
if (!isset($_SESSION['equipo'])) {
    // si no existe la variable de sesion 'equipo' es porque no se eligio ningun equipo en el area responsables
    header('location:index.php');
} else {
    // existe la variable de sesion 'equipo'
    $equipo_torneo = $_SESSION['equipo'];
    require_once 'Equipo.php';
    $equipo = new Equipo($equipo_torneo[0], $equipo_torneo[1]);

    // obtener datos de equipo para mostrar
    $datos_equipo = $equipo->getDatosEquipo();

    // obtener datos de responsables del equipo
    $delegada1 = ($datos_equipo['documento_delegada_1'] > 0) ? $equipo->getDatosDelegado($datos_equipo['documento_delegada_1']) : ['documento' => 0, 'nombres' => '', 'apellidos' => ''];
    $delegada2 = ($datos_equipo['documento_delegada_2'] > 0) ? $equipo->getDatosDelegado($datos_equipo['documento_delegada_2']) : ['documento' => 0, 'nombres' => '', 'apellidos' => ''];
    $delegada3 = ($datos_equipo['documento_delegada_3'] > 0) ? $equipo->getDatosDelegado($datos_equipo['documento_delegada_3']) : ['documento' => 0, 'nombres' => '', 'apellidos' => ''];
    $entrenador = ($datos_equipo['documento_entrenador'] > 0) ? $equipo->getDatosDelegado($datos_equipo['documento_entrenador']) : ['documento' => 0, 'nombres' => '', 'apellidos' => ''];

    // obtener fecha de cierre de modificacion de responsables
    $sth = $dbh->prepare('select fecha_fin from torneos where torneo = :torneo');
    $sth->execute([':torneo' => $equipo_torneo[1]]);
    $fecha_limite = $sth->fetch(PDO::FETCH_ASSOC);
    $fecha_limite = strtotime($fecha_limite['fecha_fin']);
    $fecha_actual = strtotime(date('d-m-Y', time()));
}

// importar head
require_once 'include/header.php';
?>

<!-- css propio de pagina -->
<link rel="stylesheet" href="css/responsablesEquipo.css">

<?php
// importar navbar
require_once 'include/navbar.php';
?>

<!-- inicio cuerpo de pagina -->
<!-- tarjeta info equipo -->
<section class="main-container">
    <h2>Responsables Equipo <?= $datos_equipo['nombre_equipo'] ?></h2>
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
    </div>
    <div class="card-container group-links-equipo">
        <div class="group-links">
            <a class="form-btn links-equipo" href="areaResponsables.php">información de equipo</a>
        </div>
        <div class="group-links">
            <a class="form-btn links-equipo" href="listaBuenaFe.php">lista de buena fe</a>
        </div>
        <div class="group-links">
            <a class="form-btn links-equipo" href="preferenciaHoraria.php">preferencias hora/cancha</a>
        </div>
    </div>
</section>

<!-- tarjeta primer delegade -->
<section class="main-container">
    <h2>primer/a delegado/a</h2>
    <div class="card-container">
        <div class="form-group">
            <label for="">apellido</label>
            <input type="text" readonly value="<?= $delegada1['apellidos'] ?>">
        </div>
        <div class="form-group">
            <label for="">nombre</label>
            <input type="text" readonly value="<?= $delegada1['nombres'] ?>">
        </div>
        <div class="form-group">
            <label for="">documento</label>
            <input type="text" readonly value="<?= $delegada1['documento'] ?>">
        </div>
    </div>
</section>

<!-- tarjeta 2de delegade -->
<section class="main-container">
    <h2>segundo/a delegado/a</h2>
    <div class="card-container">
        <div class="form-group">
            <label for="">apellido</label>
            <input type="text" readonly value="<?= $delegada2['apellidos'] ?>">
        </div>
        <div class="form-group">
            <label for="">nombre</label>
            <input type="text" readonly value="<?= $delegada2['nombres'] ?>">
        </div>
        <div class="form-group">
            <label for="">documento</label>
            <input type="text" readonly value="<?= $delegada2['documento'] ?>">
        </div>
    </div>

    <?php
    // si todavia no se supera la fecha limite, se permite modificar
    if ($fecha_actual < $fecha_limite) {
    ?>
        <div class="card-container group-links-responsable">
            <div class="group-links">
                <button class="form-btn links-responsable" id="r2">cambiar</button>
                <a class="form-btn links-responsable" href="responsableManager.php?action=delete&id=2">eliminar</a>
            </div>
        </div>
    <?php
    }
    ?>
</section>

<!-- tarjeta tercer delegade -->
<section class="main-container">
    <h2>tercer/a delegado/a</h2>
    <div class="card-container">
        <div class="form-group">
            <label for="">apellido</label>
            <input type="text" readonly value="<?= $delegada3['apellidos'] ?>">
        </div>
        <div class="form-group">
            <label for="">nombre</label>
            <input type="text" readonly value="<?= $delegada3['nombres'] ?>">
        </div>
        <div class="form-group">
            <label for="">documento</label>
            <input type="text" readonly value="<?= $delegada3['documento'] ?>">
        </div>
    </div>

    <?php
    // si todavia no se supera la fecha limite, se permite modificar
    if ($fecha_actual < $fecha_limite) {
    ?>
        <div class="card-container group-links-responsable">
            <div class="group-links">
                <button class="form-btn links-responsable" id="r3">cambiar</button>
                <a class="form-btn links-responsable" href="responsableManager.php?action=delete&id=3">eliminar</a>
            </div>
        </div>
    <?php
    }
    ?>
</section>

<!-- tarjeta entrenador -->
<section class="main-container">
    <h2>entrenador/a</h2>
    <div class="card-container">
        <div class="form-group">
            <label for="">apellido</label>
            <input type="text" readonly value="<?= $entrenador['apellidos'] ?>">
        </div>
        <div class="form-group">
            <label for="">nombre</label>
            <input type="text" readonly value="<?= $entrenador['nombres'] ?>">
        </div>
        <div class="form-group">
            <label for="">documento</label>
            <input type="text" readonly value="<?= $entrenador['documento'] ?>">
        </div>
    </div>

    <?php
    // si todavia no se supera la fecha limite, se permite modificar
    if ($fecha_actual < $fecha_limite) {
    ?>
        <div class="card-container group-links-responsable">
            <div class="group-links">
                <button class="form-btn links-responsable" id="r4">cambiar</button>
                <a class="form-btn links-responsable" href="responsableManager.php?action=delete&id=4">eliminar</a>
            </div>
        </div>
    <?php
    }
    ?>
</section>
<!-- fin cuerpo pagina -->

<!-- form modal -->
<section class="seccion-modal">
    <div id="modal-container">
        <div class="modal-form-container">
            <div class="form-card-container">
                <button id="btn-cerrar">
                    <div id="cerrarModal-line1"></div>
                    <div id="cerrarModal-line2"></div>
                </button>
                <p>Buscar por DNI. Si la persona no existe en el sistema, completar todos los campos para dar de alta.</p>
                <form method="POST" action="responsableManager.php">
                    <div class="card-container">
                        <div class="form-group">
                            <label for="dni">dni</label>
                            <input type="text" name="dni" id="dniFiltro" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="nombre">nombre</label>
                            <input type="text" name="nombre" id="nombreFiltro" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">apellido</label>
                            <input type="text" name="apellido" id="apellidoFiltro" required>
                        </div>
                        <input type="text" name="responsable" id="responsable" readonly>
                    </div>
                    <div class="form-btn-container">
                        <button type="submit" class="form-btn" name="guardar-resp">guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="scripts/responsablesEquipo.js"></script>
<?php
// incluir js de navbar y cierre de pagina
require_once 'include/footer.php';
?>