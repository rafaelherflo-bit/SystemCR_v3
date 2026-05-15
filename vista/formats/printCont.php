<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
    echo forceoutSession();
    exit();
}
$pdf = new FPDF('L');

$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->Ln(-5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(20);
$pdf->Cell(30, 6, 'FECHA', 0, 1, 'C');
$pdf->Cell(20);
$pdf->Cell(30, 6, "______ / ______ / ______", 0, 1, 'C');

$sqlRenta = "SELECT * FROM Rentas
INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
WHERE renta_estado = 'Activo'
ORDER BY modelo_tipo ASC,
contrato_folio ASC,
renta_folio ASC,
modelo_tipo ASC";
$queryRenta = consultaData($sqlRenta);
$numRenta = $queryRenta['numRows'];

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 6, "RENTAS ACTIVAS", 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '#', 1, 0, 'C', 0);
$pdf->Cell(95, 5, 'CLIENTE', 1, 0, 'C', 0);
$pdf->Cell(25, 5, 'FOLIO', 1, 0, 'C', 0);
$pdf->Cell(40, 5, 'DEPARTAMENTO', 1, 0, 'C', 0);
$pdf->Cell(35, 5, 'MODELO', 1, 0, 'C', 0);
$pdf->Cell(25, 5, 'SERIE', 1, 0, 'C', 0);
$pdf->Cell(20, 5, 'TONER', 1, 0, 'C', 0);
$pdf->Cell(30, 5, 'ZONA', 1, 1, 'C', 0);

$pdf->SetFont('Arial', '', 8);
$NoCol = 0;
$NoBN = 0;
foreach ($queryRenta['dataFetch'] as $Renta) {
    if ($Renta['modelo_tipo'] == 'Monocromatico') {
        $pdf->SetFillColor(215, 215, 215);
        $NoBN++;
        $pdf->Cell(10, 5, $NoBN, 1, 0, 'C', true);
    } else {
        $pdf->SetFillColor(255, 255, 255);
        $NoCol++;
        $pdf->Cell(10, 5, $NoCol, 1, 0, 'C', true);
    }
    $pdf->Cell(95, 5, utf8_decode($Renta['cliente_rs']), 1, 0, 'C', true);
    $pdf->Cell(25, 5, utf8_decode($Renta['contrato_folio'] . '-' . $Renta['renta_folio']), 1, 0, 'C', true);
    $pdf->Cell(40, 5, utf8_decode($Renta['renta_depto']), 1, 0, 'C', true);
    $pdf->Cell(35, 5, utf8_decode($Renta['modelo_linea'] . " " . $Renta['modelo_modelo']), 1, 0, 'C', true);
    $pdf->Cell(25, 5, utf8_decode($Renta['equipo_serie']), 1, 0, 'C', true);
    $pdf->Cell(20, 5, utf8_decode($Renta['modelo_toner']), 1, 0, 'C', true);
    $pdf->Cell(30, 5, utf8_decode($Renta['zona_nombre']), 1, 1, 'C', true);
    $pdf->SetFillColor(255, 255, 255);
}

// Saltar Pagina
// $pdf->AddPage();
// $pdf->Ln(10);

// // Equipos A Color

// $pdf->SetFont('Arial', 'B', 20);
// $pdf->Cell(0, 10, "Equipos A Color", 0, 1, 'C');

// $sqlCOLOR = "SELECT * FROM Rentas
// INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
// INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
// INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
// INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
// INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
// WHERE modelo_tipo = 'Multicolor'
// AND renta_estado = 'Activo'
// ORDER BY contrato_folio ASC,
// renta_folio ASC,
// modelo_tipo ASC";
// $queryCOLOR = consultaData($sqlCOLOR);
// $numCOLOR = $queryCOLOR['numRows'];

// $pdf->SetFont('Arial', 'B', 10);
// $pdf->Cell(105, 5, 'CLIENTE', 1, 0, 'C', 0);
// $pdf->Cell(25, 5, 'FOLIO', 1, 0, 'C', 0);
// $pdf->Cell(40, 5, 'DEPARTAMENTO', 1, 0, 'C', 0);
// $pdf->Cell(35, 5, 'MODELO', 1, 0, 'C', 0);
// $pdf->Cell(25, 5, 'SERIE', 1, 0, 'C', 0);
// $pdf->Cell(20, 5, 'TONER', 1, 0, 'C', 0);
// $pdf->Cell(30, 5, 'ZONA', 1, 1, 'C', 0);

// $pdf->SetFont('Arial', '', 8);
// foreach ($queryCOLOR['dataFetch'] as $COLOR) {
//     $pdf->SetFillColor(255, 255, 255);
//     $pdf->Cell(105, 5, utf8_decode($COLOR['cliente_rs']), 1, 0, 'C', true);
//     $pdf->Cell(25, 5, utf8_decode($COLOR['contrato_folio'] . '-' . $COLOR['renta_folio']), 1, 0, 'C', true);
//     $pdf->Cell(40, 5, utf8_decode($COLOR['renta_depto']), 1, 0, 'C', true);
//     $pdf->Cell(35, 5, utf8_decode($COLOR['modelo_linea'] . " " . $COLOR['modelo_modelo']), 1, 0, 'C', true);
//     $pdf->Cell(25, 5, utf8_decode($COLOR['equipo_serie']), 1, 0, 'C', true);
//     $pdf->Cell(20, 5, utf8_decode($COLOR['modelo_toner']), 1, 0, 'C', true);
//     $pdf->Cell(30, 5, utf8_decode($COLOR['zona_nombre']), 1, 1, 'C', true);
//     $pdf->SetFillColor(255, 255, 255);
// }

// // Saltar Pagina
// $pdf->AddPage();

// // Total de Equipos Por modelo de toner

// $pdf->SetFont('Arial', 'B', 20);
// $pdf->Cell(130, 10, "Total de Equipos Por Zona", 0, 1, 'C');
// $pdf->Ln(5);


// $pdf->SetFont('Arial', 'B', 12);
// $pdf->Cell(35, 6, 'Equipo', 1, 0, 'C', 0);
// $pdf->Cell(30, 6, 'Mod Toner', 1, 0, 'C', 0);
// $pdf->Cell(30, 6, 'Total', 1, 1, 'C', 0);
// $pdf->SetFont('Arial', '', 9);

// $sql = "SELECT COUNT(*) AS total, zona_id, zona_nombre, modelo_toner FROM Rentas
// INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
// INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
// INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
// WHERE renta_estado = 'Activo'
// GROUP BY zona_id
// ORDER BY zona_nombre ASC";
// $result = consultaData($sql);

// for ($i=0; $i < $result['numRows']; $i++) {
//     if ($row['modelo_tipo'] == 'Monocromatico') {
//         $pdf->SetFillColor(215, 215, 215);
//     } else {
//         $pdf->SetFillColor(255, 255, 255);
//     }

//     if ($row['modelo_tipo'] == 'Monocromatico') {
//         $tmono = $row['total'] + $tmono;
//     } else if ($row['modelo_tipo'] == 'Multicolor') {
//         $tcolor = $row['total'] + $tcolor;
//     }

//     $pdf->Cell(35, 6, utf8_decode($data['modelo_linea']) . " " . utf8_decode($data['modelo_modelo']), 1, 0, 'C', true);
//     $pdf->Cell(30, 6, utf8_decode($row['modelo_toner']), 1, 0, 'C', true);
//     $pdf->Cell(30, 6, utf8_decode($row['total']), 1, 1, 'C', true);
//     $pdf->SetFillColor(255, 255, 255);
// }

// $pdf->Ln(1);

// $pdf->SetFont('Arial', 'B', 10);
// $pdf->Cell(65, 6, "Total de Equipos B&N: ", 1, 0, 'R', true);
// $pdf->Cell(30, 6, $tmono, 1, 1, 'C', true);

// $pdf->SetFont('Arial', 'B', 10);
// $pdf->Cell(65, 6, "Total de Equipos a COLOR: ", 1, 0, 'R', true);
// $pdf->Cell(30, 6, $tcolor, 1, 1, 'C', true);

// $total = $tmono + $tcolor;

// $pdf->SetFont('Arial', 'B', 12);
// $pdf->Cell(65, 6, "Total de Equipos: ", 1, 0, 'R', true);
// $pdf->Cell(30, 6, $total, 1, 1, 'C', true);









// Saltar Pagina
$pdf->AddPage();

// Total de Equipos Por modelo de toner

$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(130, 10, "Total de Equipos Por Modelo", 0, 1, 'C');
$pdf->Ln(5);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(35, 6, 'Equipo', 1, 0, 'C', 0);
$pdf->Cell(30, 6, 'Mod Toner', 1, 0, 'C', 0);
$pdf->Cell(30, 6, 'Total', 1, 1, 'C', 0);
$pdf->SetFont('Arial', '', 9);

$sql = "SELECT COUNT(*) AS total, modelo_toner FROM Rentas
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
WHERE renta_estado = 'Activo'
GROUP BY modelo_toner
ORDER BY modelo_toner ASC";
$result = consultaData($sql);

$tmono = 0;
$tcolor = 0;

foreach ($result['dataFetch'] as $row) {
    $sqlIn = "SELECT * FROM Modelos
    WHERE modelo_toner = '" . $row['modelo_toner'] . "'";
    $data = consultaData($sqlIn)['dataFetch'][0];
    if ($data['modelo_tipo'] == 'Monocromatico') {
        $pdf->SetFillColor(215, 215, 215);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }

    if ($data['modelo_tipo'] == 'Monocromatico') {
        $tmono = $row['total'] + $tmono;
    } else if ($data['modelo_tipo'] == 'Multicolor') {
        $tcolor = $row['total'] + $tcolor;
    }

    $pdf->Cell(35, 6, utf8_decode($data['modelo_linea']) . " " . utf8_decode($data['modelo_modelo']), 1, 0, 'C', true);
    $pdf->Cell(30, 6, utf8_decode($row['modelo_toner']), 1, 0, 'C', true);
    $pdf->Cell(30, 6, utf8_decode($row['total']), 1, 1, 'C', true);
    $pdf->SetFillColor(255, 255, 255);
}

$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(65, 6, "Total de Equipos B&N: ", 1, 0, 'R', true);
$pdf->Cell(30, 6, $tmono, 1, 1, 'C', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(65, 6, "Total de Equipos a COLOR: ", 1, 0, 'R', true);
$pdf->Cell(30, 6, $tcolor, 1, 1, 'C', true);

$total = $tmono + $tcolor;

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(65, 6, "Total de Equipos: ", 1, 0, 'R', true);
$pdf->Cell(30, 6, $total, 1, 1, 'C', true);


$date = date('d-m-Y - g-i-s-A');
$pdf->Output('I', "Rentas_Activas - " . date("d-m-Y") . ".pdf");
