<?php

    session_start();
    if(isset($_SESSION["dni"]))
    {
        $dni=$_SESSION["dni"];
        require_once 'Conexion.php';
        $dbh = new Conexion;
        
        if($_SESSION['tipo_usuario'] == 'responsable')
        {
            if (!isset($_SESSION['equipo'])) {
                header('location:index.php');
            } else {
              
                $torneo = $_SESSION['equipo'][1];
                $equipo = $_SESSION['equipo'][0];             
            }


            $stm= $dbh->prepare("SELECT idcancha,descripcion FROM canchas_preferencias");
            $stm->execute();
        
            $contador=0;
            $error="";

            if(isset($_POST["guardar"]))
            {
                if(isset($_POST["prioridad"]))
                {
                    $p1=(int) $_POST["prioridad"];
                    if($p1==6)//si cada radio button elige el p6 el contador suma en 1
                    {
                        $contador++;
                    }
                }else//en caso de que no elija ninguno se pondra de peioridad 1
                {
                    $p1=1;
                }
        
                if(isset($_POST["prioridad_2"]))
                {
                    $p2=(int) $_POST["prioridad_2"];
                    if($p2==6)
                    {
                        $contador++;
                    }
                }
                else
                {
                    $p2=1;
                }
                if(isset($_POST["prioridad_3"]))
                {
                    $p3=(int) $_POST["prioridad_3"];
                    if($p3==6)
                    {
                        $contador++;
                    }
                }
                else
                {
                    $p3=1;
                }
                if(isset($_POST["prioridad_4"]))
                {
                    $p4=(int) $_POST["prioridad_4"];
                    if($p4==6)
                    {
                        $contador++;
                    }
                }else
                {
                    $p4=1;
                }
                if(isset($_POST["prioridad_5"]))
                {
                    $p5=(int) $_POST["prioridad_5"];
                    if($p5==6)
                    {
                        $contador++;
                    }
                }
                else
                {
                    $p5=1;
                }
                if(isset($_POST["prioridad_6"]))
                {
                    $p6=(int) $_POST["prioridad_6"];
                    if($p6==6)
                    {
                        $contador++;
                    }
                }else
                {
                    $p6=1;
                }
        
                if(isset($_POST["canchaHora"]))
                {
                    $canchaHora= $_POST["canchaHora"];
                }else
                {
                    $canchaHora="";
                }
        
                if(isset($_POST["tipoCancha"]))
                {      
                    $tipoCancha= $_POST["tipoCancha"];
                }else
                {
                    $tipoCancha="";
                }
        
                
        
                if($contador>=2)//si hay mas de dos p6 tira el mensaje de error resetea contador
                {
                    $contador=0;
                    $error="solo uno de P6";
                }
                else if($contador<=1)//si solo eligio 1 solo p6 inserta en la BBDD
                {
        
                    //insertar

                    //traemos todos los campo de la tabla del tporneo y equipo
                    $stmVerTorneo= $dbh->prepare("SELECT * FROM preferencias_horarias where torneo='$torneo' AND equipo='$equipo'");
                    $stmVerTorneo->execute();
                    $resul=$stmVerTorneo->fetch(PDO::FETCH_ASSOC);
                    
                    // verificamos si el toreno que rescatamos no existe entonces inserta
                    if(!isset($resul["torneo"]))
                    {
                        $stm2=$dbh->prepare ("INSERT INTO `preferencias_horarias`(id,categoria,torneo, equipo, turno_1,turno_2,turno_3,turno_4, turno_5,turno_6, quien_cambio,cancha_hora,idcancha) VALUES (null,'MAXIVOLEY','$torneo','$equipo',$p1,$p2,$p3,$p4,$p5,$p6,$dni,'$canchaHora','$tipoCancha')");
                        $stm2->execute();
                    }else
                    {
                        $stm2 = $dbh->prepare('update preferencias_horarias set turno_1=:p1, turno_2=:p2, turno_3=:p3, turno_4=:p4, turno_5=:p5, turno_6=:p6, quien_cambio=:dni, fecha_hora_cambio=default, cancha_hora=:ch, idcancha=:idcancha where torneo=:torneo and equipo=:equipo');
                        
                        $stm2->execute([':p1'=>$p1, ':p2'=>$p2, ':p3'=>$p3, ':p4'=>$p4, ':p5'=>$p5, ':p6'=>$p6, ':dni'=>$dni, ':ch'=>$canchaHora, ':idcancha'=>$tipoCancha, ':torneo'=>$torneo, ':equipo'=>$equipo]);
                    }
                
                }
            }

            $sth = $dbh->prepare('select * from preferencias_horarias where torneo = :torneo and equipo = :equipo');
            $sth->execute([':torneo'=>$torneo, ':equipo'=>$equipo]);
            $preferencias = $sth->fetch(PDO::FETCH_ASSOC);
            
        }else
        {
            // no es responsable, no tiene permiso a esta parte del sitio, redirige a index
            header('location:index.php');
        }
    }else
    {
        header('location:index.php');
    }
?> 
    
    <?php require_once 'include/header.php'; ?>
    
    <!-- css especifico de la pagina -->
    <link rel="stylesheet" href="css/areaResponsables.css">
    <link rel="stylesheet" href="css/preferenciaHoraria.css">


    <!-- incluir navbar -->
    <?php require_once 'include/navbar.php';?>
        
   <section class="main-container">
   <div class="container">
        <h1>Preferencias hora/cancha</h1>
        <h3>Equipo: <?php echo $equipo; ?></h3>
    </div>
        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" class="form" id="form">
                <div class="form__radiogrup" id="horario_10_hs">
                    <div class="horario">
                        <span>10:00</span>
                    </div>
                    <input type="radio" name="prioridad" value="1"  id="prioridad1" <?php if(isset($preferencias['turno_1'])&& $preferencias['turno_1']==1){echo 'checked';} ?>>
                    <label for="prioridad1">P1</label>
                    <input type="radio" name="prioridad" value="2"  id="prioridad2" <?php if(isset($preferencias['turno_1'])&& $preferencias['turno_1']==2){echo 'checked';} ?>>
                    <label for="prioridad2">P2</label> 
                    <input type="radio" name="prioridad" value="3" id="prioridad3" <?php if(isset($preferencias['turno_1'])&& $preferencias['turno_1']==3){echo 'checked';} ?>>
                    <label for="prioridad3">P3</label> 
                    <input type="radio" name="prioridad" value="4" id="prioridad4" <?php if(isset($preferencias['turno_1'])&& $preferencias['turno_1']==4){echo 'checked';} ?>>
                    <label for="prioridad4">P4</label> 
                    <input type="radio" name="prioridad" value="5" id="prioridad5" <?php if(isset($preferencias['turno_1'])&& $preferencias['turno_1']==5){echo 'checked';} ?>>
                    <label for="prioridad5">P5</label> 
                    <input type="radio" name="prioridad" value="6" id="prioridad6" <?php if(isset($preferencias['turno_1'])&& $preferencias['turno_1']==6){echo 'checked';} ?>>
                    <label for="prioridad6">P6</label> 
                </div>
                <div class="form__radiogrup" id="horario_11_hs">
                    <div class="horario">
                        <span>11:30</span>
                    </div>
                    <input type="radio" name="prioridad_2" value="1" id="prioridad1.2" <?php if(isset($preferencias['turno_2'])&&$preferencias['turno_2']==1){echo 'checked';} ?>>
                    <label for="prioridad1.2">P1</label>
                    <input type="radio" name="prioridad_2" value="2" id="prioridad2.2" <?php if(isset($preferencias['turno_2'])&&$preferencias['turno_2']==2){echo 'checked';} ?>>
                    <label for="prioridad2.2">P2</label> 
                    <input type="radio" name="prioridad_2" value="3" id="prioridad3.2" <?php if(isset($preferencias['turno_2'])&&$preferencias['turno_2']==3){echo 'checked';} ?>>
                    <label for="prioridad3.2">P3</label> 
                    <input type="radio" name="prioridad_2" value="4" id="prioridad4.2" <?php if(isset($preferencias['turno_2'])&&$preferencias['turno_2']==4){echo 'checked';} ?>>
                    <label for="prioridad4.2">P4</label> 
                    <input type="radio" name="prioridad_2" value="5" id="prioridad5.2" <?php if(isset($preferencias['turno_2'])&&$preferencias['turno_2']==5){echo 'checked';} ?>>
                    <label for="prioridad5.2">P5</label> 
                    <input type="radio" name="prioridad_2" value="6" id="prioridad6.2" <?php if(isset($preferencias['turno_2'])&&$preferencias['turno_2']==6){echo 'checked';} ?>>
                    <label for="prioridad6.2">P6</label> 
                </div>
                <div class="form__radiogrup" id="horario_13_hs">
                    <div class="horario">
                        <span>13:30 14:00</span>
                    </div>
                    <input type="radio" name="prioridad_3" value="1" id="prioridad1.3" <?php if(isset($preferencias['turno_3'])&&$preferencias['turno_3']==1){echo 'checked';} ?>>
                    <label for="prioridad1.3">P1</label>
                    <input type="radio" name="prioridad_3" value="2" id="prioridad2.3" <?php if(isset($preferencias['turno_3'])&&$preferencias['turno_3']==2){echo 'checked';} ?>>
                    <label for="prioridad2.3">P2</label> 
                    <input type="radio" name="prioridad_3" value="3" id="prioridad3.3" <?php if(isset($preferencias['turno_3'])&&$preferencias['turno_3']==3){echo 'checked';} ?>>
                    <label for="prioridad3.3">P3</label> 
                    <input type="radio" name="prioridad_3" value="4" id="prioridad4.3" <?php if(isset($preferencias['turno_3'])&&$preferencias['turno_3']==4){echo 'checked';} ?>>
                    <label for="prioridad4.3">P4</label> 
                    <input type="radio" name="prioridad_3" value="5" id="prioridad5.3" <?php if(isset($preferencias['turno_3'])&&$preferencias['turno_3']==5){echo 'checked';} ?>>
                    <label for="prioridad5.3">P5</label> 
                    <input type="radio" name="prioridad_3" value="6" id="prioridad6.3" <?php if(isset($preferencias['turno_3'])&&$preferencias['turno_3']==6){echo 'checked';} ?>>
                    <label for="prioridad6.3">P6</label> 
                </div>
                <div class="form__radiogrup" id="horario_15_hs">
                    <div class="horario">
                        <span>15:00 15:30</span>
                    </div>
                    <input type="radio" name="prioridad_4" value="1" id="prioridad1.4" <?php if(isset($preferencias['turno_4'])&&$preferencias['turno_4']==1){echo 'checked';} ?>>
                    <label for="prioridad1.4">P1</label>
                    <input type="radio" name="prioridad_4" value="2" id="prioridad2.4" <?php if(isset($preferencias['turno_4'])&&$preferencias['turno_4']==2){echo 'checked';} ?>>
                    <label for="prioridad2.4">P2</label> 
                    <input type="radio" name="prioridad_4" value="3" id="prioridad3.4" <?php if(isset($preferencias['turno_4'])&&$preferencias['turno_4']==3){echo 'checked';} ?>>
                    <label for="prioridad3.4">P3</label> 
                    <input type="radio" name="prioridad_4" value="4" id="prioridad4.4" <?php if(isset($preferencias['turno_4'])&&$preferencias['turno_4']==4){echo 'checked';} ?>>
                    <label for="prioridad4.4">P4</label> 
                    <input type="radio" name="prioridad_4" value="5" id="prioridad5.4" <?php if(isset($preferencias['turno_4'])&&$preferencias['turno_4']==5){echo 'checked';} ?>>
                    <label for="prioridad5.4">P5</label> 
                    <input type="radio" name="prioridad_4" value="6" id="prioridad6.4" <?php if(isset($preferencias['turno_4'])&&$preferencias['turno_4']==6){echo 'checked';} ?>>
                    <label for="prioridad6.4">P6</label> 
                </div>
                <div class="form__radiogrup" id="horario_17_hs">
                    <div class="horario">
                        <span>17:00 17:30</span>
                    </div>
                    <input type="radio" name="prioridad_5" value="1" id="prioridad1.5" <?php if(isset($preferencias['turno_5'])&&$preferencias['turno_5']==1){echo 'checked';} ?>>
                    <label for="prioridad1.5">P1</label>
                    <input type="radio" name="prioridad_5" value="2" id="prioridad2.5" <?php if(isset($preferencias['turno_5'])&&$preferencias['turno_5']==2){echo 'checked';} ?>>
                    <label for="prioridad2.5">P2</label> 
                    <input type="radio" name="prioridad_5" value="3" id="prioridad3.5" <?php if(isset($preferencias['turno_5'])&&$preferencias['turno_5']==3){echo 'checked';} ?>>
                    <label for="prioridad3.5">P3</label> 
                    <input type="radio" name="prioridad_5" value="4" id="prioridad4.5" <?php if(isset($preferencias['turno_5'])&&$preferencias['turno_5']==4){echo 'checked';} ?>>
                    <label for="prioridad4.5">P4</label> 
                    <input type="radio" name="prioridad_5" value="5" id="prioridad5.5" <?php if(isset($preferencias['turno_5'])&&$preferencias['turno_5']==5){echo 'checked';} ?>>
                    <label for="prioridad5.5">P5</label> 
                    <input type="radio" name="prioridad_5" value="6" id="prioridad6.5" <?php if(isset($preferencias['turno_5'])&&$preferencias['turno_5']==6){echo 'checked';} ?>>
                    <label for="prioridad6.5">P6</label> 
                </div>
                <div class="form__radiogrup" id="horario_18_hs">
                    <div class="horario">
                        <span>18:30 19:00</span>
                    </div>
                    <input type="radio" name="prioridad_6" value="1" id="prioridad1.6" <?php if(isset($preferencias['turno_6'])&&$preferencias['turno_6']==1){echo 'checked';} ?>>
                    <label for="prioridad1.6">P1</label>
                    <input type="radio" name="prioridad_6" value="2" id="prioridad2.6" <?php if(isset($preferencias['turno_6'])&&$preferencias['turno_6']==2){echo 'checked';} ?>>
                    <label for="prioridad2.6">P2</label> 
                    <input type="radio" name="prioridad_6" value="3" id="prioridad3.6" <?php if(isset($preferencias['turno_6'])&&$preferencias['turno_6']==3){echo 'checked';} ?>>
                    <label for="prioridad3.6">P3</label> 
                    <input type="radio" name="prioridad_6" value="4" id="prioridad4.6" <?php if(isset($preferencias['turno_6'])&&$preferencias['turno_6']==4){echo 'checked';} ?>>
                    <label for="prioridad4.6">P4</label> 
                    <input type="radio" name="prioridad_6" value="5" id="prioridad5.6" <?php if(isset($preferencias['turno_6'])&&$preferencias['turno_6']==5){echo 'checked';} ?>>
                    <label for="prioridad5.6">P5</label> 
                    <input type="radio" name="prioridad_6" value="6" id="prioridad6.6" <?php if(isset($preferencias['turno_6'])&&$preferencias['turno_6']==6){echo 'checked';} ?>>
                    <label for="prioridad6.6">P6</label> 
                </div>
                <div class="form__radiogrup" id="preferencia_hora_cancha">
                    <span>Priorizar:</span>
                    <input type="radio" name="canchaHora" value="C" id="cancha" <?php if(isset($preferencias['cancha_hora'])&&$preferencias['cancha_hora']=='C'){echo 'checked';} ?>>
                    <label for="cancha">cancha</label> 
                    <input type="radio" name="canchaHora" value="H" id="horario" <?php if(isset($preferencias['cancha_hora'])&&$preferencias['cancha_hora']=='H'){echo 'checked';} ?>>
                    <label for="horario">horario</label> 
                </div>
                <div class="form__selec">
                    <span>prioridad de cancha</span>
                    <select name="tipoCancha">
                        <?php while ($fila=$stm->fetch(PDO::FETCH_ASSOC)) 
                        {?>
                            <option value="<?php echo $fila["idcancha"];?>" <?php if(isset($preferencias['idcancha'])&&$fila['idcancha']==$preferencias["idcancha"]){echo 'selected';} ?>>
                            <?php echo $fila["descripcion"];?>
                            </option>
                        <?php }?>
                    </select>   
                </div>

                <input type="submit" class="form-btn"  name="guardar" value="guardar">
                <p class="error"><?php echo $error;?></p>
   </section> 

<?php require_once 'include/footer.php';?>
