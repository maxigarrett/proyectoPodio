<?php
session_start();
if (isset($_SESSION['dni'])) {
    require_once 'Conexion.php';
    $dbh = new Conexion;
    $dni = $_SESSION['dni'];

    if (isset($_POST['actualiza'])) {
        $ficha = (isset($_POST['condiciones'])) ? 'OK' : 'FALTA';

        $sth = $dbh->prepare('update personas set nombres=:nombre, apellidos=:apellido,
                        fecha_nacimiento=:fnac, telefono_particular=:particular,
                        telefono_celular=:celular, telefono_emergencias=:emergencias,
                        correo_electronico=:email, domicilio=:direccion,
                        localidad=:localidad, ficha_ok=:ficha, fecha_ultima_modificacion= :mod where documento = :dni');
        $sth->execute([
            ':nombre' => strtoupper($_POST['name']),
            ':apellido' => strtoupper($_POST['apel']),
            ':fnac' => $_POST['nac'],
            ':particular' => $_POST['part'],
            ':celular' => $_POST['celular'],
            ':emergencias' => $_POST['emergencia'],
            ':email' => $_POST['email'],
            ':direccion' => $_POST['direccion'],
            ':localidad' => $_POST['localidad'],
            ':ficha' => $ficha,
            ':dni' => $dni,
            ':mod' => date('Y-m-d h:i:s')
        ]);
        header('location:index.php');
    }


    $sth = $dbh->prepare('select * from personas where documento = :dni');
    $sth->execute([':dni' => $dni]);
    $campo_data = $sth->fetch(PDO::FETCH_ASSOC);
} else {
    // no hay session se redirige a index
    header('location:index.php');
}
?>

<?php
// incluir head
require_once 'include/header.php';
?>

<!-- css custom -->
<link rel="stylesheet" href="css/actualizarDatos.css">


<?php
// incluir cierre head, apertura body y navbar
require_once 'include/navbar.php';
?>

<!-- cuerpo de pagina -->
<form class="main-container" id="form-datos" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
    <h2>Actualizar información personal</h2>
    <div class="card-container">
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" name="name" id="name" autofocus value="<?= $campo_data['nombres'] ?>" 
                <?php if($campo_data['ficha_ok']=='OK'){echo 'readonly';} ?>
                >
        </div>
        <div class="form-group">
            <label for="apel">Apellido:</label>
            <input type="text" name="apel" id="apel" value="<?= $campo_data['apellidos'] ?>"
            <?php if($campo_data['ficha_ok']=='OK'){echo 'readonly';} ?>
                >
        </div>
        <div class="form-group">
            <label for="nac">Fecha de nacimiento</label>
            <input type="date" name="nac" id="nac" value="<?= $campo_data['fecha_nacimiento'] ?>"
            <?php if($campo_data['ficha_ok']=='OK'){echo 'readonly';} ?>
                >
        </div>
    </div>
    <p class="form-section-title">telefonos</p>
    <div class="card-container">
        <div class="form-group">
            <label for="part">Particular</label>
            <input type="text" name="part" id="part" value="<?= $campo_data['telefono_particular'] ?>">
        </div>
        <div class="form-group">
            <label for="celular">Celular</label>
            <input type="text" name="celular" id="celular" value="<?= $campo_data['telefono_celular'] ?>">
        </div>
        <div class="form-group">
            <label for="emergencia">Emergencias</label>
            <input type="text" name="emergencia" id="emergencia" value="<?= $campo_data['telefono_emergencias'] ?>">
        </div>
    </div>
    <p class="form-section-title">datos de contacto</p>
    <div class="card-container">
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" value="<?= $campo_data['correo_electronico'] ?>">
        </div>
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="<?= $campo_data['domicilio'] ?>">
        </div>
        <div class="form-group">
            <label for="localidad">Localidad</label>
            <input type="text" name="localidad" id="localidad" value="<?= $campo_data['localidad'] ?>">
        </div>
    </div>
    <div class="card-container"> 
    <div class="check-container">
        <label for="check">Acepto reglamento y condiciones</label> (<strong class="btn-leer" id="btn-leer">leer</strong>)
        <input type="checkbox" name="condiciones" id="check" required>
    </div>
    </div>
    <div class="form-btn-container">
        <button class="boton-guardar form-btn" type="submit" name="actualiza" id="btn-guardar">Guardar cambios</button>
    </div>
</form>
<!-- fin cuerpo de pagina -->

<!-- modal reglamento y condiciones -->
<section class="seccion-modal">
    <div id="modal-container">
        <div class="modal-form-container">
            <div class="form-card-container">
                <button id="btn-cerrar">
                    <div id="cerrarModal-line1"></div>
                    <div id="cerrarModal-line2"></div>
                </button>
                <p>Por el sólo hecho de presentarse a los partidos, las/los jugadoras/es, en cada oportunidad que asisten a las competencias,<br><br>
                
                <b>Declaran: </b>“Solicito la participación como jugador/a de toda competencia organizada o patrocinada por la ‘FEDERACIÓN METROPOLITANA DE VOLEIBOL Y LA FUNDACIÓN DEPORTIVA PODIO’ en cualquiera de las categorías que organice, aceptando en su totalidad los Estatutos de la ‘Federación Metropolitana de Voleibol’ y Reglamentos aplicables a las competencias y <br><br>
                
                DECLARO BAJO JURAMENTO: <br><br>
                
                Que me encuentro <b>APTO</b> y gozando de salud física y mental para desarrollar competencias y prácticas deportivas, dados los reconocimientos médicos a que he sido sometido y, por lo tanto, asumo personal y exclusivamente la total responsabilidad de participar en los torneos organizados o patrocinados por la ‘FEDERACIÓN METROPOLITANA DE VOLEIBOL y LA FUNDACIÓN DEPORTIVA PODIO’ eximiendo a la citada ‘FEDERACIÓN METROPOLITANA DE VOLEIBOL y LA FUNDACIÓN DEPORTIVA PODIO’, a sus organizadores, colaboradores y clubes que sirven como sede para el desarrollo de los encuentros de toda responsabilidad en caso de cualquier lesión, enfermedad y/o accidente que pudiera sufrir, ya que participo voluntariamente en tales competencias. Esta eximición alcanza a la actuación de los albaceas, herederos y todo aquel que pudiera actuar eventualmente en mi representación. Cada vez que el/la jugador/a se presenta a un encuentro de cualquier tipo, renueva y acepta esta declaración jurada.</p>
            </div>
        </div>
    </div>
</section>

<script src="scripts/actualizarDatos.js"></script>
<?php
// incluir js navbar y cierre d pagina
require_once 'include/footer.php';
?>