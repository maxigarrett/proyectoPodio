<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('location:index.php');
}
require_once 'Conexion.php';
$dbh = new Conexion;

// si se apreto el boton de guardar cambios de los datos de la jugadora:
if (isset($_POST['guardar-admOK'])) {
    $dni = $_POST['dni'];
    $foto4x4 = '';
    $dni_f = '';
    $dni_d = '';
    $ficha = '';

    if (isset($_POST['foto_4x4']) && $_POST['foto_4x4'] == 'on') {
        $foto4x4 = 'OK';
        $sth = $dbh->prepare('update personas_imagenes set estado = 1 where documento = :dni and tipo_imagen = :tipo');
        $sth->execute([':dni' => $dni, ':tipo' => 'foto']);
    }
    if (isset($_POST['dni_f']) && $_POST['dni_f'] == 'on') {
        $dni_f = 'OK';
        $sth = $dbh->prepare('update personas_imagenes set estado = 1 where documento = :dni and tipo_imagen = :tipo');
        $sth->execute([':dni' => $dni, ':tipo' => 'dni_f']);
    }
    if (isset($_POST['dni_d']) && $_POST['dni_d'] == 'on') {
        $dni_d = 'OK';
        $sth = $dbh->prepare('update personas_imagenes set estado = 1 where documento = :dni and tipo_imagen = :tipo');
        $sth->execute([':dni' => $dni, ':tipo' => 'dni_d']);
    }
    if (isset($_POST['ficha_ok']) && $_POST['ficha_ok'] == 'on') {
        $ficha = 'OK';
    }

    $sth = $dbh->prepare('update personas set apellidos = :apellido, nombres = :nombre, fecha_nacimiento = :fecha, correo_electronico = :email, ficha_ok = :ficha, foto_4x4_ok = :foto, dni_frente_ok = :dni_f, dni_dorso_ok = :dni_d, carnet = :podio, carnet_fmv = :fmv, fecha_ticket = :ticket where documento = :dni');
    $sth->execute([':apellido' => strtoupper($_POST['apellido']), ':nombre' => strtoupper($_POST['nombre']), ':fecha' => $_POST['fecha'], ':email' => $_POST['email'], ':ficha' => $ficha, ':foto' => $foto4x4, ':dni_f' => $dni_f, ':dni_d' => $dni_d, ':dni' => $dni, ':podio' => $_POST['carnet-podio'], ':fmv' => $_POST['carnet-fmv'], ':ticket' => $_POST['ticket']]);

    $_GET['dni'] = $dni; // para que vuelva a cargar los datos de la jugadora.
}

// si se confirma pase de equipo
if (isset($_POST['confirmar-pase'])){
    $dni = $_POST['dni'];
    $categoria = 'MAXIVOLEY';
    $torneo = $_POST['torneo'];
    $equipoActual = $_POST['equipo-actual'];
    $equipoNuevo = $_POST['equipo-nuevo'];

    // eliminar registro de equipo actual
    $sth = $dbh->prepare('delete from lista_buena_fe where documento = :dni and torneo = :torneo and nombre_equipo = :equipo');
    $sth->execute([':dni'=>$dni, ':torneo'=>$torneo, ':equipo'=>$equipoActual]);

    
    // agregar registro de equipo nuevo
    $sth = $dbh->prepare('insert into lista_buena_fe (categoria, torneo, nombre_equipo, documento) values (:categoria, :torneo, :equipo, :dni)');
    $sth->execute([':categoria'=>$categoria, ':torneo'=>$torneo, ':equipo'=>$equipoNuevo, ':dni'=>$dni]);

    $_GET['dni'] = $dni; // para que vuelva a cargar los datos de la jugadora.
}

// si existe la variable dni pasada por metodo get desde otras paginas o al apretar el boton de guardar
if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];
    $sth = $dbh->prepare('select * from personas where documento = :dni');
    $sth->execute([':dni' => $dni]);
    $campo_data = $sth->fetch(PDO::FETCH_ASSOC);

    // recuperar imagenes
    $res = $dbh->prepare('select * from personas_imagenes where documento= :dni');
    $res->execute([':dni' => $dni]);

    while ($campo_img = $res->fetch(PDO::FETCH_ASSOC)) {
        if ($campo_img['tipo_imagen'] == 'foto') {
            $img_foto = $campo_img['imagen'];
            $form_foto = $campo_img['formato'];
        }
        if ($campo_img['tipo_imagen'] == 'dni_f') {
            $img_dni_f = $campo_img['imagen'];
            $form_dni_f = $campo_img['formato'];
        }
        if ($campo_img['tipo_imagen'] == 'dni_d') {
            $img_dni_d = $campo_img['imagen'];
            $form_dni_d = $campo_img['formato'];
        }
    }
    $sth = $dbh->prepare('select max(carnet) from personas');
    $sth->execute();
    $ultimo_carnet = $sth->fetch(PDO::FETCH_ASSOC);

    // traer datos de donde está jugando en el torneo actual
    $sth = $dbh->prepare("SELECT `torneo` FROM `torneos` ORDER BY id DESC LIMIT 1");
    $sth->execute();
    $torneo = $sth->fetch(PDO::FETCH_ASSOC);

    $sth = $dbh->prepare('select * from lista_buena_fe where torneo = :torneo and documento = :dni');
    $sth->execute([':torneo' => $torneo['torneo'], ':dni' => $dni]);
    $datosLista = $sth->fetch(PDO::FETCH_ASSOC);

    // traer los equipos del torneo actual
    $sth = $dbh->prepare('select * from equipos where torneo = :torneo');
    $sth->execute([':torneo' => $torneo['torneo']]);
    $equipos = $sth->fetchAll(PDO::FETCH_ASSOC);
}

// incluir cabeceras
require_once 'include/header.php';
?>
<link rel="stylesheet" href="css/admDatosJugadora.css">
<?php
// incluir navbar
require_once 'include/navbar.php';
?>
<section class="main-container">
    <div class="btn-container btn-jugadora">
        <a href="admAgregarJugadora.php" class="form-btn">+ jugadora</a>
        <a href="admUltimasMod.php" class="form-btn">modificaciones por fecha</a>
    </div>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET" class="form-selector">
        <div class="card-container">
            <label for="dni">buscar por DNI:</label>
            <div class="form-input">
                <input type="text" name="dni" id="dni" autofocus required <?php if (isset($campo_data['documento'])) { ?>value="<?= $campo_data['documento'] ?>" <?php } ?>>
            </div>
            <button type="submit" class="form-btn">ver info jugadora</button>
        </div>
    </form>
</section>

<?php
if (isset($campo_data['documento'])) {
?>
    <section class="main-container">
        <h2>Datos personales</h2>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="card-container">
                <div class="form-group form-input">
                    <label for="name">Nombre</label>
                    <input type="text" name="nombre" value="<?= $campo_data['nombres'] ?>">
                </div>
                <div class="form-group form-input">
                    <label for="apel">Apellido</label>
                    <input type="text" name="apellido" value="<?= $campo_data['apellidos'] ?>">
                </div>
                <div class="form-group form-input">
                    <label for="nac">Fecha de nacimiento</label>
                    <input type="date" name="fecha" value="<?= $campo_data['fecha_nacimiento'] ?>">
                </div>
            </div>
            <p class="form-section-title">telefonos</p>
            <div class="card-container">
                <div class="form-group">
                    <label for="part">Particular</label>
                    <input type="text" readonly value="<?= $campo_data['telefono_particular'] ?>">
                </div>
                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" readonly value="<?= $campo_data['telefono_celular'] ?>">
                </div>
                <div class="form-group">
                    <label for="emergencia">Emergencias</label>
                    <input type="text" readonly value="<?= $campo_data['telefono_emergencias'] ?>">
                </div>
            </div>
            <p class="form-section-title">datos de contacto</p>
            <div class="card-container">
                <div class="form-group form-input">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" value="<?= $campo_data['correo_electronico'] ?>">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" readonly value="<?= $campo_data['domicilio'] ?>">
                </div>
                <div class="form-group">
                    <label for="localidad">Localidad</label>
                    <input type="text" readonly value="<?= $campo_data['localidad'] ?>">
                </div>
            </div>
    </section>

    <section class="main-container">
        <h2>ficha y carnets</h2>
        <div class="card-container">
            <div class="form-group">
                <div class="check-container">
                    <label for="ficha_ok">Ficha: </label>
                    <input type="checkbox" name="ficha_ok" id="ficha_ok" <?php if ($campo_data['ficha_ok'] == 'OK') {
                                                                                echo 'checked';
                                                                            } ?>>
                </div>
            </div>
            <div class="form-group form-input">
                <label for="carnet-podio">carnet PODIO</label>
                <input type="text" name="carnet-podio" id="carnet-podio" value="<?= $campo_data['carnet'] ?>">
            </div>
            <div class="form-group form-input">
                <label for="carnet-fmv">carnet FMV</label>
                <input type="text" name="carnet-fmv" id="carnet-fmv" value="<?= $campo_data['carnet_fmv'] ?>">
            </div>
            <div class="form-group form-input">
                <label for="ticket">Fecha ticket</label>
                <input type="date" name="ticket" value="<?= $campo_data['fecha_ticket'] ?>">
            </div>
        </div>
        <input type="text" class="ultimo-carnet" value="Ultimo carnet PODIO: <?= $ultimo_carnet['max(carnet)'] ?>" readonly>
    </section>

    <!-- seccion imagenes -->
    <section class="img-section">
        <h2>imagenes</h2>

        <input type="text" name="dni" class="input-dni-oculto" value="<?= $campo_data['documento'] ?>" readonly>
        <div class="img-container" id="img-container">
            <div class="img-card">
                <?php
                if (isset($img_foto)) {
                    echo "<img src='data:" . $form_foto . "; base64," . base64_encode($img_foto) . "' style='width:200px' id='foto1'>";

                    ?>
                        <a href="data:<?=$form_foto ?>;base64,<?=base64_encode($img_foto) ?>" download="foto4x4" class="form-btn btn-descargar">descargar</a>

                        <a href="admSubirFoto.php?dni=<?=$dni ?>&tipo-imagen=foto" class="form-btn btn-subir">subir foto</a>
                    <?php
                } else { ?>
                    <img src="img/avatar.jpg" alt="" style="width:220px">
                <?php
                }
                ?>
                <div class="check-container">
                    <label for="foto_4x4">Foto 4x4</label>
                    <input type="checkbox" name="foto_4x4" id="foto_4x4" <?php if ($campo_data['foto_4x4_ok'] == 'OK') {
                                                                                echo 'checked';
                                                                            } ?>>
                </div>
            </div>

            <div class="img-card">
                <?php
                if (isset($img_dni_f)) {
                    echo "<img src='data:" . $form_dni_f . "; base64," . base64_encode($img_dni_f) . "' style='width:200px' id='foto2'>";

                    ?>
                        <a href="data:<?=$form_dni_f ?>;base64,<?=base64_encode($img_dni_f) ?>" download="dniFrente" class="form-btn btn-descargar">descargar</a>

                        <a href="admSubirFoto.php?dni=<?=$dni ?>&tipo-imagen=dni_f" class="form-btn btn-subir">subir foto</a>
                    <?php
                } else { ?>
                    <img src="img/dni.png" alt="" style="width:220px">
                <?php
                }
                ?>
                <div class="check-container">
                    <label for="dni_f">DNI frente</label>
                    <input type="checkbox" name="dni_f" id="dni_f" <?php if ($campo_data['dni_frente_ok'] == 'OK') {
                                                                        echo 'checked';
                                                                    } ?>>
                </div>
            </div>

            <div class="img-card">
                <?php
                if (isset($img_dni_d)) {
                    echo "<img src='data:" . $form_dni_d . "; base64," . base64_encode($img_dni_d) . "' style='width:200px' id='foto3'>";
                    ?>
                        <a href="data:<?=$form_dni_d ?>;base64,<?=base64_encode($img_dni_d) ?>" download="dniDorso" class="form-btn btn-descargar">descargar</a>

                        <a href="admSubirFoto.php?dni=<?=$dni ?>&tipo-imagen=dni_d" class="form-btn btn-subir">subir foto</a>
                    <?php
                } else { ?>
                    <img src="img/dni.png" alt="" style="width:220px">
                <?php
                }
                ?>
                <div class="check-container">
                    <label for="dni_d">DNI dorso</label>
                    <input type="checkbox" name="dni_d" id="dni_d" <?php if ($campo_data['dni_dorso_ok'] == 'OK') {
                                                                        echo 'checked';
                                                                    } ?>>
                </div>
            </div>
            <button type="submit" class="form-btn" name="guardar-admOK">guardar cambios</button>
        </div>
        </form>
    </section>

    <?php
    if (isset($datosLista['nombre_equipo'])) {
    ?>
        <section class="main-container">
            <h2>pedido de pase</h2>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <div class="card-container">
                    <div class="form-group">
                        <input type="text" name="dni" class="input-oculto" value="<?=$dni ?>" readonly>
                        <input type="text" name="torneo" class="input-oculto" value="<?=$torneo['torneo'] ?>" readonly>
                        <label for="">equipo actual</label>
                        <input type="text" name="equipo-actual" value="<?= $datosLista['nombre_equipo'] ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">nuevo equipo</label>
                        <select name="equipo-nuevo">
                            <?php
                            foreach ($equipos as $equipo) {
                            ?>
                                <option value="<?= $equipo['nombre_equipo'] ?>"><?= $equipo['nombre_equipo'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="form-btn" name="confirmar-pase">confirmar pase</button>
                </div>
            </form>
        </section>
    <?php
    }
    ?>

<?php
    // cierre del if
}
?>

<script src="scripts/admDatosJugadora.js"></script>
<?php
// incluir script navbar y cierre de etiquetas body y html
require_once 'include/footer.php';
?>