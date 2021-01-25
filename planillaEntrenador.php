<?php
session_start();
if(!isset($_SESSION['equipo'])) {
    header('location:areaResponsables.php');
}else{
    $equipo_torneo = $_SESSION['equipo'];
    require_once 'Conexion.php';
    $dbh = new Conexion;

    // codigo que trae los datos de equipo y jugadoras
    $sth = $dbh->prepare('SELECT p.apellidos, p.nombres, p.documento, p.carnet, p.carnet_fmv FROM lista_buena_fe as t join personas as p on t.documento=p.documento WHERE t.nombre_equipo = :equipo and torneo = :torneo and t.marcado_baja is null');
    $sth->execute([':equipo' => $equipo_torneo[0], ':torneo' => $equipo_torneo[1]]);
    $listaBF = $sth->fetchAll(PDO::FETCH_ASSOC);
}
// incluir header
require_once 'include/header.php';
?>
<!-- css propio de la pagina -->
<link rel="stylesheet" href="css/planillaEntrenador.css">
<?php
// incluir navbar, cierre head y apertura body
require_once 'include/navbar.php';
?>
<!-- info equipo -->
<section class="main-container">
<h2>Planilla de entrenador</h2>
    <div class="card-container">
        <div class="form-group">
            <label for="name">equipo</label>
            <input type="text" readonly value="<?= $equipo_torneo[0] ?>">
        </div>
        <div class="form-group">
            <label for="apel">categoría</label>
            <input type="text" readonly value="MAXIVOLEY">
        </div>
        <div class="form-group">
            <label for="nac">torneo</label>
            <input type="text" readonly value="<?= $equipo_torneo[1] ?>">
        </div>
    </div>
</section>

<!-- info para llenar planilla -->
<section class="main-container">
    <p><b>C/L: </b>Indicar con la inicial quiénes son capitán y líberos. <b>Nro.: </b>Indicar número de camiseta. Se permite un máximo de 18 jugadoras en planilla. Con más de 12 jugadoras es obligatoria la presentación de 2 líberos. Con hasta 12 jugadoras puede haber ninguna, 1 o 2 líberos.</p>
</section>

<!-- generador de lista de jugadoras para el partido -->
<section class="main-container">
    <form target="_blank" action="planillaEntrenadorPDF.php" method="POST">
        <table>
            <tr id="cabecera-tabla">
                <th></th>
                <th>apellido y nombre</th>
                <th>Nro.</th>
                <th>C/L</th>
            </tr>
            <?php
                for ($i=0; $i < 18; $i++) { 
            ?>
                    <tr>
                        <td class="cantidad-jugadora"><?=$i+1?></td>
                        <td class="columna-nombre">
                            <select class="select-nombre" name="jugadora<?=$i?>">
                                <option value=" ">** seleccionar jugadora **</option>
                                <?php
                                    foreach ($listaBF as $jugadora) {
                                        ?>
                                        <option value="<?=$jugadora['documento']?>"> <?=$jugadora['apellidos']?>, <?=$jugadora['nombres']?> (<?=$jugadora['carnet']?>) </option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="columna-nro">
                            <select class="select-numero" name="numero-jugadora<?=$i?>">
                                <?php
                                    for ($j=0; $j < 100; $j++) { 
                                ?>
                                        <option value="<?=$j?>"><?=$j?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="columna-tipo">
                            <select name="tipo-jugadora<?=$i?>">
                                <option value=" ">    </option>
                                <option value="C">C</option>
                                <option value="L">L</option>
                            </select>
                        </td>
                    </tr>
            <?php
                }
            ?>
        </table>
        <div class="btn-container">
            <button type="submit" name="imprimir-planilla" class="form-btn">imprimir planilla</button>
        </div>
    </form>
</section>

<?php
require_once 'include/footer.php';
?>