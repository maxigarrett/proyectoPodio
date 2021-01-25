<?php
session_start();
if (!isset($_SESSION['tipo_usuario'])) {
    header('location:index.php');
} else if ($_SESSION['tipo_usuario'] != 'admin') {
    header('location:index.php');
}
// traer equipos del ultimo torneo
require_once 'Conexion.php';
$dbh = new Conexion;
$sth = $dbh->prepare("SELECT `torneo` FROM `torneos` ORDER BY id DESC LIMIT 1");
$sth->execute();
$torneo = $sth->fetch(PDO::FETCH_ASSOC);
$sth = $dbh->prepare("select nombre_equipo from equipos where torneo = :torneo");
$sth->execute([':torneo' => $torneo['torneo']]);
$equipos = $sth->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['equipo'])) {
    $_POST['listar-equipo'] = '';
    $_POST['selector-equipo'] = $_GET['equipo'];
}
// si se presiona el boton de listar equipo o viene de confirmar baja
if (isset($_POST['listar-equipo'])) {
    $sth = $dbh->prepare('select * from equipos where torneo = :torneo and nombre_equipo = :equipo');
    $sth->execute([':torneo' => $torneo['torneo'], ':equipo' => $_POST['selector-equipo']]);
    $dataEquipo = $sth->fetch(PDO::FETCH_ASSOC);

    // traer preferencias hora cancha
    $sth = $dbh->prepare('select * from preferencias_horarias where torneo = :torneo and equipo = :equipo');
    $sth->execute([':torneo' => $torneo['torneo'], ':equipo' => $_POST['selector-equipo']]);
    $preferencias = $sth->fetch(PDO::FETCH_ASSOC);

    // traer datos de jugadoras de la lista
    $sth_lbf = $dbh->prepare('SELECT p.apellidos, p.nombres, p.documento, p.carnet, p.carnet_fmv, p.ficha_ok, p.foto_4x4_ok, p.dni_frente_ok, p.dni_dorso_ok, p.fecha_ticket, t.marcado_baja FROM lista_buena_fe as t join personas as p on t.documento=p.documento WHERE t.nombre_equipo = :equipo and torneo = :torneo');
    $sth_lbf->execute([':equipo' => $_POST['selector-equipo'], ':torneo' => $torneo['torneo']]);
}

// incluir cabeceras
require_once 'include/header.php';
?>
<link rel="stylesheet" href="css/admIndex.css">
<?php
// incluir barra de navegacion y apertura de body
require_once 'include/navbar.php';
?>

<!-- inicio selector de equipo -->
<section class="main-container selector-equipo-container">
    <h2 id="torneo-js"><?= $torneo['torneo'] ?></h2>
    <div class="card-container group-links-equipo links-nuevos-equipos">
        <div class="group-links">
            <a class="form-btn" href="admManagerEquipos.php">+ nuevo equipo</a>
        </div>
        <div class="group-links">
            <a class="form-btn" href="admImportarEquipos.php">importar equipos</a>
        </div>
    </div>
    <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" class="form-selector">
        <select name="selector-equipo" class="selector-equipo" id="selector-equipo" required>
            <?php foreach ($equipos as $equipo) { ?>
                <option value="<?= $equipo['nombre_equipo'] ?>" <?php
                                                                if (isset($_POST['selector-equipo'])) {
                                                                    if ($equipo['nombre_equipo'] == $_POST['selector-equipo']) {
                                                                        echo 'selected';
                                                                    }
                                                                } ?>>
                    <?= $equipo['nombre_equipo'] ?>
                </option>
            <?php } ?>
        </select>
        <button type="submit" class="form-btn" name="listar-equipo">listar equipo</button>
    </form>

    <?php if (isset($_POST['listar-equipo'])) { ?>
        <div class="card-container">
            <div class="form-group">
                <label for="">equipo</label>
                <input type="text" readonly value="<?= $dataEquipo['nombre_equipo'] ?>">
            </div>
            <div class="form-group">
                <label for="">institucion</label>
                <input type="text" readonly value="<?= $dataEquipo['nombre_institucion'] ?>">
            </div>
            <div class="form-group">
                <label for="">localidad</label>
                <input type="text" readonly value="<?= $dataEquipo['localidad'] ?>">
            </div>

        </div>
        <div class="card-container group-links-equipo">
            <div class="group-links">
                <a class="form-btn" href="admManagerEquipos.php?equipo=<?=$dataEquipo['nombre_equipo'] ?>">Modificar información</a>
            </div>
            <div class="group-links">
                <a class="form-btn" href="admEliminarEquipo.php?torneo=<?=$torneo['torneo'] ?>&equipo=<?=$dataEquipo['nombre_equipo'] ?>">eliminar del torneo</a>
            </div>
        </div>
    <?php } ?>
</section>
<!-- fin selector equipo -->
<?php if (isset($_POST['listar-equipo'])) { ?>
    <!-- preferencias -->
    <section class="main-container">
        <h2>preferencias</h2>
        <h3>preferencias de turnos</h3>
        <div class="card-container preferencias-container">
            <div class="form-group preferencias-group">
                <p>Turno 1</p><b><?= $preferencias['turno_1'] ?></b>
            </div>
            <div class="form-group preferencias-group">
                <p>Turno 2</p><b><?= $preferencias['turno_2'] ?></b>
            </div>
            <div class="form-group preferencias-group">
                <p>Turno 3</p><b><?= $preferencias['turno_3'] ?></b>
            </div>
            <div class="form-group preferencias-group">
                <p>Turno 4</p><b><?= $preferencias['turno_4'] ?></b>
            </div>
            <div class="form-group preferencias-group">
                <p>Turno 5</p><b><?= $preferencias['turno_5'] ?></b>
            </div>
            <div class="form-group preferencias-group">
                <p>Turno 6</p><b><?= $preferencias['turno_6'] ?></b>
            </div>
        </div>
        <h3>hora - cancha</h3>
        <div class="card-container preferencias-container">
            <div class="form-group">
                <label for="">priorizar</label>
                <input type="text" class="input-pref" readonly value="<?php
                                                                        if ($preferencias['cancha_hora'] == 'C') {
                                                                            echo 'CANCHA';
                                                                        }
                                                                        if ($preferencias['cancha_hora'] == 'H') {
                                                                            echo 'HORA';
                                                                        }
                                                                        ?>">
            </div>
            <div class="form-group">
                <label>ID Cancha</label>
                <input type="text" class="input-pref" readonly value="<?= $preferencias['idcancha'] ?>">
            </div>
        </div>
    </section>

    <!-- lista de buena fe -->
    <section class="main-container">
        <h2>lista de buena fe</h2>
        <table id="tabla-lista">
            <thead>
                <tr>
                    <th>dni</th>
                    <th>cnt</th>
                    <th>apellido, nombre</th>
                </tr>
            </thead>
            <tbody>
                <?php

                while ($jugadora = $sth_lbf->fetch(PDO::FETCH_ASSOC)) {
                    $faltantes = '';
                    if ($jugadora['fecha_ticket'] == NULL) {
                        $faltantes .= ' T';
                    }
                    if ($jugadora['foto_4x4_ok'] != 'OK') {
                        $faltantes .= ' F';
                    }
                    if ($jugadora['dni_frente_ok'] != 'OK') {
                        $faltantes .= ' DF';
                    }
                    if ($jugadora['dni_dorso_ok'] != 'OK') {
                        $faltantes .= ' DD';
                    }
                    if ($faltantes != '') {
                        $faltantes = '(' . $faltantes . ')';
                    }
                ?>
                    <tr>
                        <td class="col-dni"><?= $jugadora['documento'] ?></td>
                        <td class="col-carnet"><?= $jugadora['carnet'] ?></td>
                        <td class="col-name"><?= $jugadora['apellidos'] ?>, <?= $jugadora['nombres'] ?> <?= $faltantes ?>
                            <?php if ($jugadora['marcado_baja'] != null) { ?>
                                <a href="admConfirmarBaja.php?torneo=<?=$torneo['torneo']?>&equipo=<?= $dataEquipo['nombre_equipo'] ?>&dni=<?= $jugadora['documento'] ?>" class="confirmar-baja">Confirmar baja</a>
                            <?php } ?></td>
                        <td class="col-link"><a href="admDatosJugadora.php?dni=<?= $jugadora['documento'] ?>" target="_blank" rel="noopener noreferrer">ver</a></td>
                    </tr>
                <?php
                }

                ?>
            </tbody>
        </table>
    </section>
<?php
// cierre del if
} ?> 

<?php
// incluir js de navbar y cierre de etiquetas
require_once 'include/footer.php';
?>