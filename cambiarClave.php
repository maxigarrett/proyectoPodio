<?php
session_start();
if (isset($_SESSION['dni'])) {
    $dni = $_SESSION['dni'];

    if (isset($_POST['actualizaPass'])) {

        require_once 'Conexion.php';
        $dbh = new Conexion;
        $sth = $dbh->prepare('select documento, clave from personas where documento = :dni');
        $sth->execute([':dni' => $_SESSION['dni']]);
        $campo = $sth->fetch(PDO::FETCH_ASSOC);

        $old_pass_md5 = md5($_POST['old-pass']);

        if ($old_pass_md5 === ($campo['clave'])) {

            if ($_POST['new-pass'] === $_POST['new-pass-confirm']) {
                $new_pass_md5 = md5($_POST['new-pass']);
                $sth = $dbh->prepare('update personas set clave = :pass where documento = :dni');
                $sth->execute([':pass' => $new_pass_md5, ':dni' => $dni]);
                $confirmacion = 'Contraseña actualizada.';
            } else {
                // no coinciden las contrasenias nuevas
                $errores = 'Las nuevas contraseñas no coinciden.';
            }
        } else {
            // pass actual no coincide
            $errores = 'Contraseña actual incorrecta';
        }
    }
} else {
    // no hay sesion redirige al index
    header('location:index.php');
}
?>
    
    <?php
    // incluir header
    require_once 'include/header.php';
    ?>

    <!-- css especifico de pagina -->
    <link rel="stylesheet" href="css/cambiarClave.css">

    <?php
    // incluir cierre del head, apertura de body y navbar
    require_once 'include/navbar.php';
    ?>


    <!-- cuerpo de pagina -->
    <section class="main-container" id="pass-container">
        <h2>cambiar contraseña</h2>

        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">

            <?php
            if (isset($confirmacion)) {
                echo '<div id="confirmacion">';
                echo '<span>' . $confirmacion . '</span>';
                echo '<span id="cerrar-confirmacion">x</span>';
                echo '</div>';
            }
            ?>

            <div class="form-group">
                <label for="old-pass">contraseña actual</label>
                <input type="password" name="old-pass" id="old-pass" required>
            </div>

            <!-- si hay errores en las claves ya sea la actual como las nuevas -->
            <?php
            if (isset($errores)) {
                echo '<div id="errores">';
                echo '<span>' . $errores . '</span>';
                echo '<span id="cerrar-error">x</span>';
                echo '</div>';
            }
            ?>

            <div class="form-group">
                <label for="new-pass">nueva contraseña</label>
                <input type="password" name="new-pass" id="new-pass" required>
            </div>

            <div class="form-group">
                <label for="new-pass-confirm">confirmar contraseña</label>
                <input type="password" name="new-pass-confirm" id="new-pass-confirm" required>
            </div>
            <div class="form-group" id="cambiarClave-btn-container">
                <button type="submit" name="actualizaPass">guardar</button>
            </div>
        </form>
    </section>
    <!-- fin cuerpo de pagina -->
    
    <!-- js propio -->
    <script src="scripts/cambiarClave.js"></script>
    
    <?php
    // incluir js navbar y cierre de pagina
    require_once 'include/footer.php'
    ?>