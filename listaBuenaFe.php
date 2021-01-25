<?php
session_start();
if (!isset($_SESSION['equipo'])) {
    header('location:index.php');
} else {
    $equipo_torneo = $_SESSION['equipo'];
    require_once 'Conexion.php';
    $dbh = new Conexion;

    // obtener fecha de cierre de modificacion de lista de buena fe y responsables
    $sth = $dbh->prepare('select * from torneos where torneo = :torneo');
    $sth->execute([':torneo' => $equipo_torneo[1]]);
    $datos_torneo = $sth->fetch(PDO::FETCH_ASSOC);
    $fecha_limite = strtotime($datos_torneo['fecha_cierre_lista_buena_fe']);
    $fecha_actual = strtotime(date('d-m-Y', time()));
    // comparando las fechas se comprueba si se pueden realizar modificaciones o no

    // codigo para eliminar jugadora de la lista
    if (isset($_GET['dni-delete'])) {
        $fecha_baja = date('Y-m-d');
        $sth = $dbh->prepare('update lista_buena_fe set marcado_baja = :baja where torneo = :torneo and nombre_equipo = :equipo and documento = :dni');
        $sth->execute([':baja' => $fecha_baja, ':torneo' => $equipo_torneo[1], ':equipo' => $equipo_torneo[0], ':dni' => $_GET['dni-delete']]);
        header('location:' . $_SERVER['PHP_SELF']);
    }

    // agregar jugadora a la lista
    if (isset($_POST['guardar-jugadora'])) {
        $documento = $_POST['dni'];
        $nombre = strtoupper($_POST['nombre']);
        $apellido = strtoupper($_POST['apellido']);
        $fecha = $_POST['fecha'];
        $email = $_POST['email'];

        $sth = $dbh->prepare('select * from personas where documento = :doc');
        $sth->execute([':doc' => $documento]);
        $data = $sth->fetch(PDO::FETCH_ASSOC);

        // si no existe la persona en la DDBB crea el registro
        if (!isset($data['documento'])) {
            $sth = $dbh->prepare('insert into personas (documento, apellidos, nombres, fecha_nacimiento, clave, fecha_alta, correo_electronico) values (:doc, :apellido, :nombre, :fecha, :clave, :alta, :email)');
            $sth->execute([':doc' => $documento, ':apellido' => $apellido, ':nombre' => $nombre, ':fecha'=>$fecha, ':clave'=>md5($documento), ':alta'=>date('Y-m-d'), ':email'=>$email]);
        } else {
            $sth = $dbh->prepare('update personas set apellidos = :apellido, nombres = :nombre, correo_electronico = :email, fecha_nacimiento = :fecha where documento = :dni');
            $sth->execute([':apellido'=>$apellido, ':nombre'=>$nombre, ':email'=>$email, ':fecha'=>$fecha, ':dni'=>$documento]);
        }

        $sth = $dbh->prepare('select documento from lista_buena_fe where torneo = :torneo and nombre_equipo = :equipo');
        $sth->execute([':torneo' => $equipo_torneo[1], ':equipo' => $equipo_torneo[0]]);
        
        // traemos los DNI que figura en la lista de buena fe en el torneo elegido en areaResponsable.php   
        $contador = 0;
        while ($lista = $sth->fetch(PDO::FETCH_ASSOC)) {
            if ($documento == $lista['documento']) {
                $contador += 1;
            }
        }
        if ($contador == 0) {
            $sth = $dbh->prepare('insert into lista_buena_fe (categoria, torneo, nombre_equipo, documento, documento_alta, fecha_alta, marcado_baja) values (:cat, :torneo, :equipo, :doc, :dni, :fecha, null)');
            $sth->execute([':cat'=>$datos_torneo['categoria'], ':torneo'=>$equipo_torneo[1], ':equipo'=>$equipo_torneo[0], ':doc' => $documento, ':dni' => $_SESSION['dni'], ':fecha' => date('Y-m-d')]);
        }else{
            $sth = $dbh->prepare('update lista_buena_fe set marcado_baja=null where torneo=:torneo and nombre_equipo=:equipo and documento=:doc');
            $sth->execute([':torneo'=>$equipo_torneo[1], ':equipo'=>$equipo_torneo[0], ':doc'=>$documento]);
        }
    }
}

// codigo que trae los datos de equipo y jugadoras
$sth_lbf = $dbh->prepare('SELECT p.apellidos, p.nombres, p.documento, p.carnet, p.carnet_fmv, p.ficha_ok, p.foto_4x4_ok, p.dni_frente_ok, p.dni_dorso_ok, p.fecha_ticket FROM lista_buena_fe as t join personas as p on t.documento=p.documento WHERE t.nombre_equipo = :equipo and torneo = :torneo and t.marcado_baja is null');
$sth_lbf->execute([':equipo' => $equipo_torneo[0], ':torneo' => $equipo_torneo[1]]);

// incluir header
require_once 'include/header.php';
?>
<link rel="stylesheet" href="css/listaBuenaFe.css">
<?php
// incluir navbar, cierre head y apertura body
require_once 'include/navbar.php';
?>
<section class="main-container">
    <h2>Lista de buena fe</h2>
    <div class="card-container">
        <div class="form-group">
            <label for="name">equipo</label>
            <input type="text" readonly value="<?= $equipo_torneo[0] ?>">
        </div>
        <div class="form-group">
            <label for="apel">categoría</label>
            <input type="text" readonly value="<?=$datos_torneo['categoria']?>">
        </div>
        <div class="form-group">
            <label for="nac">torneo</label>
            <input type="text" readonly value="<?= $equipo_torneo[1] ?>">
        </div>
    </div>
    <div class="card-container group-links-equipo">
        <div class="group-links">
            <a class="form-btn links-equipo" href="areaResponsables.php">información de equipo</a>
        </div>
        <div class="group-links">
            <a class="form-btn links-equipo" href="responsablesEquipo.php">ver responsables</a>
        </div>
        <div class="group-links">
            <a class="form-btn links-equipo" href="preferenciaHoraria.php">preferencias hora/cancha</a>
        </div>
    </div>
</section>
<section class="main-container">
    <table>
        <tr id="cabecera-tabla">
            <th>DNI</th>
            <th>Apellido, nombre (carnet - fmv)</th>
        </tr>
        <?php
        while ($jugadora = $sth_lbf->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <tr>
                <td class="columna-dni"><?= $jugadora['documento'] ?></td>
                <td class="columna-nombre"><?= $jugadora['apellidos'] ?>, <?= $jugadora['nombres'] ?> (<?= $jugadora['carnet'] ?> - <?= $jugadora['carnet_fmv'] ?>)</td>

                <!-- si se paso de la fecha no elimina -->
                <?php
                if ($fecha_actual < $fecha_limite) {
                ?>
                    <td class="columna-eliminar">
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?dni-delete=<?= $jugadora['documento'] ?>">
                            <img class="btn-eliminar-jugadora" src="img/borrar.png">
                        </a>
                    </td>
                <?php
                }
                ?>
            </tr>

        <?php
        }
        ?>
    </table>

    <div class="card-container group-links-equipo">
        <div class="group-links">
            <?php
            if ($fecha_actual < $fecha_limite) {
            ?>
                <button class="form-btn btn-agregar-jugadora" id="btn-agregar-jugadora">jugadora +</button>
            <?php
            }
            ?>
            <a href="lbfpdf.php" target="_blank" rel="noopener noreferrer" class="form-btn btn-agregar-jugadora">imprimir lista</a>
            <a href="planillaEntrenador.php" class="form-btn btn-agregar-jugadora">planilla entrenador</a>
        </div>
    </div>

</section>

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
                <form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
                    <div class="card-container">
                        <div class="form-group selector-container">
                            <label for="dni">dni</label>
                            <input type="text" name="dni" id="dniFiltro" required autofocus>
                            <select class="sel" id="sel" size="3"></select>
                        </div>
                        <div class="form-group">
                            <label for="nombre">nombre</label>
                            <input type="text" name="nombre" id="nombreFiltro" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">apellido</label>
                            <input type="text" name="apellido" id="apellidoFiltro" required>
                        </div>
                        <div class="form-group">
                            <label for="email">email</label>
                            <input type="text" name="email" id="emailFiltro" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha">fecha de nacimiento</label>
                            <input type="date" name="fecha" id="fechaFiltro" required>
                        </div>
                    </div>
                    <div class="form-btn-container">
                        <button type="submit" class="form-btn" name="guardar-jugadora">guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="scripts/listaBuenaFe.js"></script>
<?php
// incluir footer con js navbar y cierre de body y html
require_once 'include/footer.php';
?>