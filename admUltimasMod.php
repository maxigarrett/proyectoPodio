<?php
session_start();
if ($_SESSION['tipo_usuario'] != 'admin') {
    header('location:index.php');
}

if (isset($_POST['buscar-mod'])) {
    $fechaMod = $_POST['fecha-mod'];
    require_once 'Conexion.php';
    $dbh = new Conexion;
    $sth = $dbh->prepare('select * from personas where fecha_ultima_modificacion > :fecha');
    $sth->execute([':fecha' => $fechaMod]);
    $listaJugadoras = $sth->fetchAll(PDO::FETCH_ASSOC);
}


// incluir head
require_once 'include/header.php';
?>
<link rel="stylesheet" href="css/admUltimaMod.css">
<?php
// incluir cierre head, apertura de body y navbar
require_once 'include/navbar.php';
?>

<section class="main-container">
    <h2>modificaciones desde fecha</h2>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="card-container">
            <input type="date" name="fecha-mod" <?php
                                                if (isset($_POST['fecha-mod'])) {
                                                ?> value="<?= $_POST['fecha-mod'] ?>" <?php
                                                    }
                                                        ?>>
        </div>
        <div class="btn-container">
            <button type="submit" class="form-btn" name="buscar-mod">ver modificaciones</button>
        </div>
    </form>
</section>

<?php
if (isset($_POST['buscar-mod'])) {
    if (count($listaJugadoras) > 0) {
?>
        <section class="main-container">
            <h2>personas que realizaron modificaciones</h2>
            <table class="tabla">
                <thead>
                    <tr>
                        <th class="fila-doc">documento</th>
                        <th class="fila-name">apellido, nombre</th>
                        <th class="fila-link"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($listaJugadoras as $jugadora) {
                    ?>
                        <tr>
                            <td class="fila-doc"><?= $jugadora['documento'] ?></td>
                            <td><?= $jugadora['apellidos'] ?>, <?= $jugadora['nombres'] ?></td>
                            <td class="fila-link"><a href="admDatosJugadora.php?dni=<?= $jugadora['documento'] ?>" target="_blank" rel="noopener noreferrer">ver</a></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </section>

<?php
    }
}
?>