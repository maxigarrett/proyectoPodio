<?php
session_start();
if (!isset($_POST['imprimir-planilla'])) {
    header('location:areaResponsables.php');
}

require_once 'Conexion.php';
$dbh = new Conexion;

require_once 'fpdf/fpdf.php';
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 20);

$pdf->SetTextColor(64, 64, 64);
$pdf->SetDrawColor(100, 100, 100);
$pdf->SetFillColor(225, 225, 225);

$pdf->Cell(186, 8, 'PLANILLA DE ENTRENADOR', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60, 10, 'FORMACION DEL EQUIPO', 1, 0, 'C', true);
$pdf->Cell(102, 10, $_SESSION['equipo'][0], 1, 0, 'C');
$pdf->Cell(12, 10, 'A', 1, 0, 'C');
$pdf->Cell(12, 10, 'B', 1, 0, 'C');
$pdf->Ln();
$pdf->Cell(30, 10, 'EQUIPO "A"', 1, 0, 'C', true);
$pdf->Cell(63, 10, '', 1, 0);
$pdf->Cell(30, 10, 'EQUIPO "B"', 1, 0, 'C', true);
$pdf->Cell(63, 10, '', 1, 0);
$pdf->Ln();
$pdf->Cell(30, 10, 'FECHA', 1, 0, 'C', true);
$pdf->Cell(63, 10, '', 1, 0);
$pdf->Cell(30, 10, 'Nro. PARTIDO', 1, 0, 'C', true);
$pdf->Cell(30, 10, '', 1, 0);
$pdf->Cell(17, 10, 'PAGO', 1, 0, 'C', true);
$pdf->Cell(8, 10, 'SI', 1, 0, 'C');
$pdf->Cell(8, 10, 'NO', 1, 0, 'C');
$pdf->Ln(15);

// cabeceras de tabla
$cabeceras = ['C/L', 'Nro.', 'Carnet', 'Apellido, Nombre', 'Firma'];
$columnas = [12, 12, 28, 90, 44];

for ($i = 0; $i < count($cabeceras); $i++) {
    $pdf->Cell($columnas[$i], 8, $cabeceras[$i], 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
for ($i = 0; $i < 18; $i++) {
    $dni = $_POST['jugadora' . $i];
    if ($dni != ' ') {
        $sth = $dbh->prepare('select * from personas where documento = :dni');
        $sth->execute([':dni' => $dni]);
        $campo = $sth->fetch(PDO::FETCH_ASSOC);

        $carnet = $campo['carnet']!=0 ? $campo['carnet'] : $campo['documento'];
        $faltantes ='';
        if($campo['fecha_ticket']==NULL){$faltantes.=' T';}
        if($campo['foto_4x4_ok']!='OK'){$faltantes.=' F';}
        if($campo['dni_frente_ok']!='OK'){$faltantes.=' DF';}
        if($campo['dni_dorso_ok']!='OK'){$faltantes.=' DD';}
        if($faltantes!=''){$faltantes='('.$faltantes.')';}

        $pdf->Cell($columnas[0], 8, $_POST['tipo-jugadora' . $i], 1, 0, 'C');
        $pdf->Cell($columnas[1], 8, $_POST['numero-jugadora' . $i], 1, 0, 'C');
        $pdf->Cell($columnas[2], 8, $carnet, 1, 0, 'C');
        $pdf->Cell($columnas[3], 8, $campo['apellidos'] . ', ' . $campo['nombres'].' '.$faltantes, 1, 0);
        $pdf->Cell($columnas[4], 8, ' ', 1, 0);
    } else {
        $pdf->Cell($columnas[0], 8, '', 1, 0, 'C');
        $pdf->Cell($columnas[1], 8, '', 1, 0, 'C');
        $pdf->Cell($columnas[2], 8, '', 1, 0, 'C');
        $pdf->Cell($columnas[3], 8, '', 1, 0);
        $pdf->Cell($columnas[4], 8, '', 1, 0);
    }
    $pdf->Ln();
}
$pdf->Cell(24, 8, 'ENTRENADOR', 1, 0, 'C', true);
$sth = $dbh->prepare('select * from equipos where nombre_equipo = :equipo and torneo = :torneo');
$sth->execute([':equipo'=>$_SESSION['equipo'][0], ':torneo'=>$_SESSION['equipo'][1]]);
$equipo = $sth->fetch(PDO::FETCH_ASSOC);
$entrenador_dni = $equipo['documento_entrenador']!=0 ? $equipo['documento_entrenador']:'';
$entrenador_nombre = '';
if ($entrenador_dni!=''){
    $sth = $dbh->prepare('select * from personas where documento = :dni');
    $sth->execute([':dni'=>$entrenador_dni]);
    $datos_entrenador = $sth->fetch(PDO::FETCH_ASSOC);
    $entrenador_nombre = $datos_entrenador['apellidos'].', '.$datos_entrenador['nombres'];
    if ($datos_entrenador['carnet']!=0){$entrenador_dni = $datos_entrenador['carnet'];}
}

$pdf->Cell($columnas[2], 8, $entrenador_dni, 1, 0, 'C');
$pdf->Cell($columnas[3], 8, $entrenador_nombre, 1, 0);
$pdf->Cell($columnas[4], 8, '', 1, 0);
$pdf->Ln();
$pdf->Cell(24, 8, 'AUXILIAR', 1, 0, 'C', true);
$pdf->Cell($columnas[2], 8, '', 1, 0);
$pdf->Cell($columnas[3], 8, '', 1, 0);
$pdf->Cell($columnas[4], 8, '', 1, 0);
$pdf->Ln(12);

$pdf->Cell(33, 8, '1er juego', 1, 0, 'C', true);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(33, 8, '2do juego', 1, 0, 'C', true);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(33, 8, '3er juego', 1, 0, 'C', true);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(33, 8, '4to juego', 1, 0, 'C', true);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(33, 8, '5to juego', 1, 0, 'C', true);
$pdf->Ln();

$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Ln();
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Cell(11, 8, '', 1, 0);
$pdf->Ln(15);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(8,6,'C/L:', 'LT',0,'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(63, 6,'Indicar con la inicial quienes son capitan y liberos. ', 'T',0,'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(8,6,'Nro.:','T',0,'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(36, 6,'Indicar numero de camiseta.', 'T',0,'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(10,6,'Carnet:', 'T',0,'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(61, 6,'Indicar numero de carnet de PODIO. De no ', 'TR',0);
$pdf->Ln();
$pdf->Cell(186,6,'poseerlo, indicar numero de DNI. Se permite un maximo de 18 jugadoras en planilla. Con mas de 12 jugadoras es obligatoria la presentacion de 2','LR',0);
$pdf->Ln();
$pdf->Cell(186,6,'libero. Con hasta 12 jugadoras, puede haber ninguna, 1 o 2 liberos.','LRB',0);
$pdf->Output();
