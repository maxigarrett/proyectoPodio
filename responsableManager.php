<?php
session_start();
if (!isset($_SESSION['equipo'])) {
    header('location:index.php');
} else {
    require_once 'Conexion.php';
    $dbh = new Conexion;
    $equipo_torneo = $_SESSION['equipo'];

    if (isset($_POST['guardar-resp'])) {
        $documento = $_POST['dni'];
        $nombre = strtoupper($_POST['nombre']);
        $apellido = strtoupper($_POST['apellido']);
        $resp = $_POST['responsable'];

        $sth = $dbh->prepare('select * from personas where documento = :doc');
        $sth->execute([':doc' => $documento]);
        $data = $sth->fetch(PDO::FETCH_ASSOC);

        if (!isset($data['documento'])) {
            $sth = $dbh->prepare('insert into personas (documento, nombres, apellidos, clave, fecha_alta) values (:doc, :nombre, :apellido, :clave, :alta)');
            $sth->execute([':doc' => $documento, ':nombre' => $nombre, ':apellido' => $apellido, ':clave'=>md5($documento), ':alta'=>date('Y-m-d')]);
        }

        switch ($resp) {
            case 'responsable2':
                $sth = $dbh->prepare('update equipos set documento_delegada_2 = :doc, dni_ultima_modificacion = :dni where nombre_equipo = :equipo and torneo = :torneo');
                break;
            case 'responsable3':
                $sth = $dbh->prepare('update equipos set documento_delegada_3 = :doc, dni_ultima_modificacion = :dni where nombre_equipo = :equipo and torneo = :torneo');
                break;
            case 'entrenador':
                $sth = $dbh->prepare('update equipos set documento_entrenador = :doc, dni_ultima_modificacion = :dni where nombre_equipo = :equipo and torneo = :torneo');
                break;

            default:
                header('location:areaResponsables.php');
                break;
        }
        $sth->execute([':doc' => $documento, ':dni' => $_SESSION['dni'], ':equipo' => $equipo_torneo[0], ':torneo' => $equipo_torneo[1]]);
        header('location:responsablesEquipo.php');
    }
    if (isset($_GET['action'])) {
        // obtener fecha de cierre de modificacion de lista de buena fe y responsables
        $sth = $dbh->prepare('select fecha_fin from torneos where torneo = :torneo');
        $sth->execute([':torneo' => $equipo_torneo[1]]);
        $fecha_limite = $sth->fetch(PDO::FETCH_ASSOC);
        $fecha_limite = strtotime($fecha_limite['fecha_fin']);
        $fecha_actual = strtotime(date('d-m-Y', time()));

        if ($_GET['action'] == 'delete' && $fecha_actual < $fecha_limite) {
            switch ($_GET['id']) {
                case '1':
                    $sth = $dbh->prepare('update equipos set documento_delegada_1 = 0, dni_ultima_modificacion = :dni where nombre_equipo = :equipo and torneo = :torneo');
                    break;

                case '2':
                    $sth = $dbh->prepare('update equipos set documento_delegada_2 = 0, dni_ultima_modificacion = :dni where nombre_equipo = :equipo and torneo = :torneo');
                    break;

                case '3':
                    $sth = $dbh->prepare('update equipos set documento_delegada_3 = 0, dni_ultima_modificacion = :dni where nombre_equipo = :equipo and torneo = :torneo');
                    break;

                case '4':
                    $sth = $dbh->prepare('update equipos set documento_entrenador = 0, dni_ultima_modificacion = :dni where nombre_equipo = :equipo and torneo = :torneo');
                    break;

                default:
                    header('location:areaResponsables.php');
                    break;
            }
            $sth->execute([':dni' => $_SESSION['dni'], ':equipo' => $equipo_torneo[0], ':torneo' => $equipo_torneo[1]]);
            header('location:responsablesEquipo.php');
        } else {
            // no se especifica accion a realizar (entrada no valida a la pagina)
            header('location:areaResponsables.php');
        }
    }
    // else {
    //     // no existe la variable action en el array get
    //     header('location:areaResponsables.php');
    // }
}
