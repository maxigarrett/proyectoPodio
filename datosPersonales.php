<?php
session_start();
if (isset($_SESSION['dni'])) {
    require_once 'Conexion.php';
    $dbh = new Conexion;
    $dni = $_SESSION['dni'];
    $query_data = 'select * from personas where documento = :dni';
    $sth = $dbh->prepare($query_data);
    $sth->execute([':dni' => $dni]);
    $campo_data = $sth->fetch(PDO::FETCH_ASSOC);

    $query_img = 'select * from personas_imagenes where documento = :dni';
    $res = $dbh->prepare($query_img);
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
} else {
    header('location:index.php');
}

// incluir cabeceras
require_once 'include/header.php';
?>
<!-- css especifico de pagina -->
<link rel="stylesheet" href="css/datosPersonales.css">

<?php
// include de cierre del head, apertura del body y navbar
include_once 'include/navbar.php';
?>

<!-- seccion pagina datos personales -->
<section class="main-container">
    <h2>Carnet Podio: <?= $campo_data['carnet'] ?> - Carnet fmv: <?= $campo_data['carnet_fmv'] ?> </h2>
    <div class="card-container">
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" readonly value="<?= $campo_data['nombres'] ?>">
        </div>
        <div class="form-group">
            <label for="apel">Apellido</label>
            <input type="text" readonly value="<?= $campo_data['apellidos'] ?>">
        </div>
        <div class="form-group">
            <label for="nac">Fecha de nacimiento</label>
            <input type="date" readonly value="<?= $campo_data['fecha_nacimiento'] ?>">
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
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" readonly value="<?= $campo_data['correo_electronico'] ?>">
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
    <div class="form-btn-container">
        <a class="form-btn" href="actualizarDatos.php">modificar datos</a>
    </div>
</section>

<section class="img-section">
    <h2>imagenes</h2>
    <div class="img-container" id="img-container">
        <div class="img-card">
            <?php
            if (isset($img_foto)) {
                echo "<img src='data:" . $form_foto . "; base64," . base64_encode($img_foto) . "' style='width:200px'>";
            } else { ?>
                <img src="img/avatar.jpg" alt="" style="width:220px">
            <?php
            }
            ?>
            <span>Foto</span>
            <button id="btn-foto">cambiar</button>
        </div>

        <div class="img-card">
            <?php
            if (isset($img_dni_f)) {
                echo "<img src='data:" . $form_dni_f . "; base64," . base64_encode($img_dni_f) . "' style='width:200px'>";
            } else { ?>
                <img src="img/dni.png" alt="" style="width:220px">
            <?php
            }
            ?>
            <span>DNI frente</span>
            <button id="btn-dni-f">cambiar</button>
        </div>

        <div class="img-card">
            <?php
            if (isset($img_dni_d)) {
                echo "<img src='data:" . $form_dni_d . "; base64," . base64_encode($img_dni_d) . "' style='width:200px'>";
            } else { ?>
                <img src="img/dni.png" alt="" style="width:220px">
            <?php
            }
            ?>
            <span>DNI dorso</span>
            <button id="btn-dni-d">cambiar</button>
        </div>
    </div>
</section>

<section class="seccion-modal">
    <div id="modal-container">
        <div class="modal-form-container">
            <div class="form-image">
                <button for="btn-cerrar" id="btn-cerrar">
                    <div id="cerrarModal-line1"></div>
                    <div id="cerrarModal-line2"></div>
                </button>
                <div id="vista-previa"></div>
                <form id="formIMG" action="subirFoto.php" method="post" enctype="multipart/form-data">
                    <input type="file" accept="image/*" name="imagen" class="input-foto" id="input-foto">
                    <input type="text" name="tipo_imagen" id="input-tipo-imagen">
                    <input type="submit" class="boton-guardar" value="guardar">
                </form>
            </div>
        </div>
    </div>
</section>

<!-- js especifico -->
<script src="scripts/datosPersonales.js"></script>

<?php
// incluir js de navbar y cierre de body y html
require_once 'include/footer.php';
?>