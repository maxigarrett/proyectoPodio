<?php
session_start();
if (!isset($_SESSION['dni'])){
    header('location:index.php');
}else{
    if ($_FILES['imagen']['name'] != '') {
         // existe el archivo entonces se generan las variables
         $dni = $_SESSION['dni'];
         $formato = $_FILES['imagen']['type'];
         $tipo = $_POST['tipo_imagen'];
         $tamanio = $_FILES['imagen']['size'];
     
        //  crear el blob
         $archivodestino = $_FILES['imagen']['tmp_name'];
         $imagen = fopen($archivodestino, 'r');
         $img_blob = fread($imagen, $tamanio);

         // se crea la conexion
        require_once 'Conexion.php';
        $dbh = new Conexion;

         // consulta para saber si es primera vez o un cambio de imagen
         $query_consulta = 'select * from personas_imagenes where documento= :dni and tipo_imagen = :tipo';
         $sth = $dbh->prepare($query_consulta);
         $sth->execute([':dni' => $dni, ':tipo' => $tipo]);
         $registro = $sth->fetch(PDO::FETCH_ASSOC);

         if ($registro['tipo_imagen'] == $tipo){
             $sth = $dbh->prepare('update personas_imagenes set imagen=:imagen, formato = :formato, estado = 0 where documento=:dni and tipo_imagen=:tipo');
             $sth->execute([':imagen'=> $img_blob, ':dni'=>$dni, ':tipo'=>$tipo, ':formato'=>$formato]);
             
            switch ($registro['tipo_imagen']) {
                case 'foto':
                    $sth = $dbh->prepare('update personas set foto_4x4_ok = 0 where documento = :dni');
                    break;
                    case 'dni_f':
                        $sth = $dbh->prepare('update personas set dni_frente_ok = 0 where documento = :dni');
                        break;
                        case 'dni_d':
                            $sth = $dbh->prepare('update personas set dni_dorso_ok = 0 where documento = :dni');
                            break;
            }
            $sth->execute([':dni'=>$dni]);

             $sth = $dbh->prepare('update personas set');
         }else{
             $sth = $dbh->prepare('insert into personas_imagenes(documento, tipo_imagen, imagen, formato, estado) values(:dni, :tipo, :img, :formato, 0)');
             $sth->execute([':dni' => $dni, ':tipo' => $tipo, ':img' => $img_blob, ':formato' => $formato]);
            //  echo 'alta ' . $registro['tipo_imagen'];
         }
    }
    header('location:index.php');
}