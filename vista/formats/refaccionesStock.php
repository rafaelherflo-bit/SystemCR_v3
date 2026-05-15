<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
    echo forceoutSession();
    exit();
}

$pdf = new FPDF("L");
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20);
$pdf->Cell(30, 6, 'FECHA', 0, 1, 'C');
$pdf->Cell(20);
$pdf->Cell(30, 6, "______ / ______ / ______", 0, 1, 'C');
$pdf->Image(LOGOCR, 120, 5, 40, 0, '', WEBSITE);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 8, 'EXISTENCIA DE REFACCIONES', 0, 1, 'C');

$pdf->Ln(1); // Salto de linea.

$sql = "SELECT * FROM Refacciones
INNER JOIN ProveedoresR ON Refacciones.ref_provR_id = ProveedoresR.provR_id
INNER JOIN CategoriasR ON Refacciones.ref_catR_id = CategoriasR.catR_id
WHERE ref_estado = 'Activo'
ORDER BY ref_codigo ASC";
$query = consultaData($sql);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 5, 'CODIGO', 1, 0, 'C', 0);
$pdf->Cell(80, 5, 'COMPATIBILIDAD', 1, 0, 'C', 0);
$pdf->Cell(45, 5, 'CATEGORIA', 1, 0, 'C', 0);
$pdf->Cell(45, 5, 'PROVEEDOR', 1, 0, 'C', 0);
$pdf->Cell(20, 5, 'STOCK', 1, 0, 'C', 0);
$pdf->Cell(20, 5, 'AJUSTE', 1, 0, 'C', 0);
$pdf->Cell(20, 5, 'COMPRA', 1, 1, 'C', 0);

$pdf->SetFont('Arial', '', 10);

foreach ($query['dataFetch'] as $Stock) {
    $refE = consultaData("SELECT SUM(refRE_cant) AS refE FROM RefaccionesRegistrosE WHERE refRE_ref_id = " . $Stock['ref_id'])['dataFetch'][0]['refE'];
    $refS = consultaData("SELECT SUM(refRS_cant) AS refS FROM RefaccionesRegistrosS WHERE refRS_ref_id = " . $Stock['ref_id'])['dataFetch'][0]['refS'];
    $refStock = $refE - $refS;

    $pdf->SetFillColor(255, 255, 255);
    if ($refStock == 0) {
        $pdf->SetFillColor(255, 227, 227);
    } elseif ($refStock == 1 || $refStock == 2) {
        $pdf->SetFillColor(254, 255, 227);
    }
    $pdf->Cell(30, 5, $Stock['ref_codigo'], 1, 0, 'C', true);
    $pdf->Cell(80, 5, $Stock['ref_comp'], 1, 0, 'C', true);
    $pdf->Cell(45, 5, $Stock['catR_nombre'], 1, 0, 'C', true);
    $pdf->Cell(45, 5, $Stock['provR_nombre'], 1, 0, 'C', true);
    $pdf->Cell(20, 5, $refStock, 1, 0, 'C', true);
    $pdf->Cell(20, 5, "", 1, 0, 'C', true);
    $pdf->Cell(20, 5, "", 1, 1, 'C', true);
}

$pdf->Output('I', "Stock de Toner en Almacen - " . date("d-m-Y") . ".pdf");
