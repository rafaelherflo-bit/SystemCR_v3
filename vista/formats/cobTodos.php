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

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20);
$pdf->Cell(30, 6, 'FECHA', 0, 1, 'C');
$pdf->Cell(20);
$pdf->Cell(30, 6, "______ / ______ / ______", 0, 1, 'C');
$pdf->Image(LOGOCR, 230, 5, 40, 0, '', WEBSITE);
$pdf->Ln(2);

$SQL = "SELECT * FROM Rentas
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
WHERE renta_estado = 'Activo'
ORDER BY cliente_rs ASC";
$QUERY = consultaData($SQL);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(119, 4, 'CLIENTE', 1, 0, 'C', 0);
$pdf->Cell(49, 4, 'RENTA', 1, 0, 'C', 0);
$pdf->Cell(5, 4, 'T', 1, 0, 'C', 0);
$pdf->Cell(27, 4, 'MENSUALIDAD', 1, 0, 'C', 0);
$pdf->Cell(26, 4, 'ESCANEO', 1, 0, 'C', 0);
$pdf->Cell(26, 4, 'BN', 1, 0, 'C', 0);
$pdf->Cell(26, 4, 'COLOR', 1, 1, 'C', 0);

$pdf->SetFont('Arial', '', 10);
$TOTAL = 0;
$nCol = 255;
$no = 1;
$cli = 0;
foreach ($QUERY['dataFetch'] as $ROW) {
    // ---- INICIO ---- ALTERNA COLOR DEPENDIENDO DE ID CLIENTE ---- INICIO ---- //
    if ($ROW['cliente_id'] != $cli) {
        $no++;
    }
    $cli = $ROW['cliente_id'];
    if ($no % 2 === 0) {
        $nCol = 225;
    } else {
        $nCol = 255;
    }
    $pdf->SetFillColor($nCol, $nCol, $nCol);
    // ---- FIN ---- ALTERNA COLOR DEPENDIENDO DE ID CLIENTE ---- FIN ---- //

    // ---- INICIO ---- ALTERNA COLOR DEPENDIENDO DE ID CLIENTE ---- INICIO ---- //
    if ($ROW['modelo_tipo'] == "Monocromatico") {
        $renta_inc_col = "";
        $renta_exc_col = "";
        $modelo_tipo = "M";
    } else {
        $renta_inc_col = $ROW['renta_inc_col'];
        $renta_exc_col = "$" . $ROW['renta_exc_col'];
        $modelo_tipo = "C";
    }

    $TOTAL = $TOTAL + $ROW['renta_costo'];
    $pdf->Cell(119, 4, $ROW['cliente_rs'], 1, 0, 'C', true);
    $pdf->Cell(49, 4, $ROW['renta_depto'], 1, 0, 'C', true);
    $pdf->Cell(5, 4, $modelo_tipo, 1, 0, 'C', true);
    $pdf->Cell(27, 4, "$" . $ROW['renta_costo'], 1, 0, 'C', true);
    $pdf->Cell(13, 4, $ROW['renta_inc_esc'], 1, 0, 'C', true);
    $pdf->Cell(13, 4, "$" . $ROW['renta_exc_esc'], 1, 0, 'C', true);
    $pdf->Cell(13, 4, $ROW['renta_inc_bn'], 1, 0, 'C', true);
    $pdf->Cell(13, 4, "$" . $ROW['renta_exc_bn'], 1, 0, 'C', true);
    $pdf->Cell(13, 4, $renta_inc_col, 1, 0, 'C', true);
    $pdf->Cell(13, 4, $renta_exc_col, 1, 1, 'C', true);
}
$pdf->Cell(119);
$pdf->SetFillColor(125, 125, 125);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(54, 4, "TOTAL: ", 1, 0, 'C', true);
$pdf->Cell(27, 4, "$" . $TOTAL, 1, 1, 'C', true);


$pdf->Output('I', "Costos de Rentas - " . date("d-m-Y") . ".pdf");
