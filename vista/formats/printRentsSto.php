<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario'])) {
    exit();
}

$pdf = new FPDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();

// Cabecera inicial
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 6, 'FECHA: ' . date("d/m/Y"), 0, 1, 'L');
$pdf->Image(LOGOCR, 120, 5, 40, 0, '', WEBSITE);
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, utf8_decode("ABASTECIMIENTO POR RENTA Y CONTRATO"), 0, 1, 'C');
$pdf->Ln(5);

// Consulta optimizada
$sqlRenta = "SELECT * FROM Rentas
INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
WHERE renta_estado = 'Activo'
ORDER BY contrato_folio ASC, renta_folio ASC";

$queryRenta = consultaData($sqlRenta);

// Acumulador dinámico de tóners
$totalesToner = [];
$ultimoContrato = "";

foreach ($queryRenta['dataFetch'] as $Renta) {

    // --- Lógica de Separación por Contrato ---
    if ($ultimoContrato != $Renta['contrato_folio']) {
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(40, 40, 40);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(280, 7, utf8_decode(" CONTRATO: " . $Renta['contrato_folio'] . " - " . $Renta['cliente_rs']), 1, 1, 'L', true);

        // Encabezados de tabla
        $pdf->SetTextColor(0);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(22, 6, 'FOLIO', 1, 0, 'C', true);
        $pdf->Cell(95, 6, 'DEPARTAMENTO', 1, 0, 'C', true);
        $pdf->Cell(35, 6, 'MODELO', 1, 0, 'C', true);
        $pdf->Cell(30, 6, 'SERIE', 1, 0, 'C', true);
        $pdf->Cell(98, 6, 'STOCKS Y NIVELES EN EQUIPO', 1, 1, 'C', true);
        $ultimoContrato = $Renta['contrato_folio'];
    }

    // --- Lógica de Acumulación de Totales ---
    $toner = $Renta['modelo_toner'];
    if (!isset($totalesToner[$toner])) {
        $totalesToner[$toner] = ['K' => 0, 'M' => 0, 'C' => 0, 'Y' => 0];
    }
    $totalesToner[$toner]['K'] += $Renta['renta_stock_K'];
    $totalesToner[$toner]['M'] += $Renta['renta_stock_M'];
    $totalesToner[$toner]['C'] += $Renta['renta_stock_C'];
    $totalesToner[$toner]['Y'] += $Renta['renta_stock_Y'];

    // --- Renderizado de Fila ---
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(22, 6, $Renta['contrato_folio'] . '-' . $Renta['renta_folio'], 1, 0, 'C');
    $pdf->Cell(95, 6, utf8_decode($Renta['renta_depto']), 1, 0, 'L');
    $pdf->Cell(35, 6, utf8_decode($Renta['modelo_modelo']), 1, 0, 'C');
    $pdf->Cell(30, 6, $Renta['equipo_serie'], 1, 0, 'C');

    if ($Renta['modelo_tipo'] == 'Monocromatico') {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(49, 6, "STOCK K: " . $Renta['renta_stock_K'] . " pz", 1, 0, 'C', true);
        $pdf->Cell(49, 6, "NIVEL: " . $Renta['equipo_nivel_K'] . "%", 1, 1, 'C');
    } else {
        // Celdas de colores para tóners a color
        $w = 98 / 4;
        $pdf->SetFillColor(200);
        $pdf->Cell($w, 6, "K:" . $Renta['renta_stock_K'] . " / " . $Renta['equipo_nivel_K'] . "%", 1, 0, 'C', true);
        $pdf->SetFillColor(255, 200, 200);
        $pdf->Cell($w, 6, "M:" . $Renta['renta_stock_M'] . " / " . $Renta['equipo_nivel_M'] . "%", 1, 0, 'C', true);
        $pdf->SetFillColor(200, 230, 255);
        $pdf->Cell($w, 6, "C:" . $Renta['renta_stock_C'] . " / " . $Renta['equipo_nivel_C'] . "%", 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 200);
        $pdf->Cell($w, 6, "Y:" . $Renta['renta_stock_Y'] . " / " . $Renta['equipo_nivel_Y'] . "%", 1, 1, 'C', true);
    }
}

// --- SECCIÓN DE TOTALES GENERALES ---
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode("RESUMEN TOTAL DE TÓNERS EN CAMPO"), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230);
$pdf->Cell(50, 7, "MODELO TONER", 1, 0, 'C', true);
$pdf->Cell(30, 7, "NEGRO (K)", 1, 0, 'C', true);
$pdf->Cell(30, 7, "MAGENTA (M)", 1, 0, 'C', true);
$pdf->Cell(30, 7, "CYAN (C)", 1, 0, 'C', true);
$pdf->Cell(30, 7, "AMARILLO (Y)", 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
ksort($totalesToner); // Ordenar por nombre de tóner

foreach ($totalesToner as $modelo => $cant) {
    $pdf->Cell(50, 7, $modelo, 1, 0, 'C');
    $pdf->Cell(30, 7, $cant['K'], 1, 0, 'C');
    // Solo mostrar colores si tienen stock o el modelo no es monocromático
    $pdf->Cell(30, 7, ($cant['M'] > 0 ? $cant['M'] : '-'), 1, 0, 'C');
    $pdf->Cell(30, 7, ($cant['C'] > 0 ? $cant['C'] : '-'), 1, 0, 'C');
    $pdf->Cell(30, 7, ($cant['Y'] > 0 ? $cant['Y'] : '-'), 1, 1, 'C');
}

$pdf->Output('I', "Reporte_Abastecimiento.pdf");
