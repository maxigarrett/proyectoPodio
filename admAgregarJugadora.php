<?php
session_start();
if ($_SESSION['tipo_usuario'] != 'admin') {
    header('location:index.php');
}

if(isset($_POST['nueva-jugadora'])){
   require_once 'Conexion.php';
   $dbh = new Conexion;
   
    $dni = $_POST['documento'];
    $apellido = $_POST['apellido'];
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $email = $_POST['email'];

   $sth = $dbh->prepare('insert into personas (documento, apellidos, nombres, fecha_nacimiento, correo_electronico) values (:dni, :apellido, :nombre, :fecha, :email)');
   $sth->execute([':dni'=>$dni, ':apellido'=>$apellido, ':nombre'=>$nombre, ':fecha'=>$fecha, ':email'=>$email]);

   header("location:admDatosJugadora.php?dni=$dni");
}

// incluir head, metadata
require_once 'include/header.php';
?>
<link rel="stylesheet" href="css/admAgregarJugadora.css">
<?php
// incluir cierre de head, apertura de body y navbar completo
require_once 'include/navbar.php';
?>

<section class="main-container">
    <h2>agregar jugadora o entrenador</h2>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="card-container">
            <div class="form-group">
                <label for="">documento</label>
                <input type="text" name="documento" required autofocus>
            </div>
            <div class="form-group">
                <label for="">apellido</label>
                <input type="text" name="apellido" required>
            </div>
            <div class="form-group">
                <label for="">nombre</label>
                <input type="text" name="nombre" required>
            </div>
        </div>
        <div class="card-container">
            <div class="form-group">
                <label for="">fecha de nacimiento</label>
                <input type="date" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="">email</label>
                <input type="text" name="email" required>
            </div>
        </div>
        </div>
        <div class="btn-container">
            <button type="submit" name="nueva-jugadora" class="form-btn">guardar</button>
        </div>
    </form>

</section>

<?php
// incluir cierre de etiquetas y js de navbar
require_once 'include/footer.php';
?>