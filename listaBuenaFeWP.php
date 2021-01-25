<?php
require_once 'Conexion.php';
$dbh = new Conexion;

$sth = $dbh->prepare("SELECT `torneo` FROM `torneos` ORDER BY id DESC LIMIT 1");
$sth->execute();
$torneo = $sth->fetch(PDO::FETCH_ASSOC);
$sth = $dbh->prepare("select nombre_equipo from equipos where torneo = :torneo");
$sth->execute([':torneo' => $torneo['torneo']]);
$equipos = $sth->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['listar-equipo'])) {
    // se pidio listar un equipo
    $sth_lbf = $dbh->prepare('SELECT p.apellidos, p.nombres, p.documento, p.carnet, p.fecha_nacimiento FROM lista_buena_fe as t join personas as p on t.documento=p.documento WHERE t.nombre_equipo = :equipo and torneo = :torneo and t.marcado_baja is null');
    $sth_lbf->execute([':equipo' => $_POST['select-equipo'], ':torneo' => $torneo['torneo']]);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <section class="container">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
            <label for="">equipo: </label>
            <select name="select-equipo" id="select-equipo">
                <?php
                foreach ($equipos as $equipo) {
                ?>
                    <option value="<?= $equipo['nombre_equipo'] ?>"><?= $equipo['nombre_equipo'] ?></option>
                <?php
                }
                ?>
            </select>
            <button type="submit" class="form-btn" name="listar-equipo">listar</button>
        </form>
    </section>

    <?php
    if (isset($_POST['listar-equipo'])) {
    ?>
        <table>
            <thead>
                <tr>
                    <th>carnet</th>
                    <th>nombre</th>
                    <th>clase</th>
                    <th>equipo</th>
                    <!-- <th>foto</th> -->
                </tr>
            </thead>
            <tbody>
                <?php
                while ($jugadora = $sth_lbf->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?=$jugadora['carnet']?></td>
                    <td><?=$jugadora['apellidos']?>, <?=$jugadora['nombres']?></td>
                    <td><?=date('Y',strtotime($jugadora['fecha_nacimiento']))?></td>
                    <td><?=$_POST['select-equipo']?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    }
    ?>
</body>

</html>