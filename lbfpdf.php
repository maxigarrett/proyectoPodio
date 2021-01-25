<?php
session_start();
if (!isset($_SESSION['equipo'])) {
    header('location:areaResponsables.php');
} else {
    $equipo = $_SESSION['equipo'][0];
    $torneo = $_SESSION['equipo'][1];
    require_once 'Conexion.php';
    $dbh = new Conexion;

    // codigo que trae los datos de equipo y jugadoras
    $sth_lbf = $dbh->prepare('SELECT p.apellidos, p.nombres, p.documento, p.carnet, p.carnet_fmv, p.ficha_ok, p.foto_4x4_ok, p.dni_frente_ok, p.dni_dorso_ok, p.fecha_ticket FROM lista_buena_fe as t join personas as p on t.documento=p.documento WHERE t.nombre_equipo = :equipo and torneo = :torneo and t.marcado_baja is null');
    $sth_lbf->execute([':equipo' => $equipo, ':torneo' => $torneo]);

    // creacion de pdf
    require_once 'fpdf/fpdf.php';
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);

    // logo
    $pdf->Image('img/podioWhite.png', 6, 20, 60);
    $pdf->Ln(15);

    $pdf->Cell(42, 10, '');
    // titulo
    $pdf->SetFont('', 'B', 20);
    $pdf->Cell(102, 20, 'LISTA DE BUENA FE', 0, 0, 'C');

    $pdf->SetFillColor(225, 225, 225);
    $pdf->SetTextColor(64, 64, 64);
    $pdf->SetDrawColor(100, 100, 100);
    // lbf datos equipo y torneo
    $pdf->SetFont('', 'B', 10);

    $pdf->Cell(42,8,'FECHA',1,0,'C',true);
    $pdf->Ln();
    $pdf->Cell(144,8,'',0,0);
    $pdf->Cell(42, 8, date('d M yy'),1,0,'C');

    $pdf->Ln(25);

    $cabeceras = ['EQUIPO', 'TORNEO', 'CATEGORIA'];
    $columnas = [62, 62, 62];
    for ($i = 0; $i < count($cabeceras); $i++) {
        $pdf->Cell($columnas[$i], 8, $cabeceras[$i], 1, 0, 'C', true);
    }
    $pdf->Ln();
    $pdf->SetFont('');
    $pdf->Cell($columnas[0], 8, $equipo, 1, 0, 'C');
    $pdf->Cell($columnas[1], 8, $torneo, 1, 0, 'C');
    $pdf->Cell($columnas[0], 8, 'MAXIVOLEY', 1, 0, 'C');
    $pdf->Ln(20);

    // lbf cabeceras de tabla y ancho de colunas
    $cabeceras = ['DNI', 'Apellido, Nombre (C. PODIO - C. fmv)', 'ficha', 'foto', 'dni f', 'dni d', 'ticket'];
    $columnas = [26, 90, 14, 14, 14, 14, 14];

    $pdf->SetFont('', 'B', 10);

    for ($i = 0; $i < count($cabeceras); $i++) {
        $pdf->Cell($columnas[$i], 8, $cabeceras[$i], 1, 0, 'C', true);
    }
    $pdf->Ln();
    $pdf->SetFont('');

    while ($jugadora = $sth_lbf->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell($columnas[0], 8, $jugadora['documento'], 1, 0, 'C');
        $pdf->Cell($columnas[1], 8, $jugadora['apellidos'] . ', ' . $jugadora['nombres'] . ' (' . $jugadora['carnet'] . ' - ' . $jugadora['carnet_fmv'] . ')', 1, 0, 'L');
        $pdf->Cell($columnas[2], 8, $jugadora['ficha_ok'], 1, 0, 'C');
        $pdf->Cell($columnas[3], 8, $jugadora['foto_4x4_ok'], 1, 0, 'C');
        $pdf->Cell($columnas[4], 8, $jugadora['dni_frente_ok'], 1, 0, 'C');
        $pdf->Cell($columnas[5], 8, $jugadora['dni_dorso_ok'], 1, 0, 'C');
        $ticket = '';
        if ($jugadora['fecha_ticket']!=null){$ticket = 'OK';}
        $pdf->Cell($columnas[5], 8, $ticket, 1, 0, 'C');
        $pdf->Ln();
    }
    $pdf->Output();
}
