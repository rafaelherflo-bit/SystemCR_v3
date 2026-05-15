<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
  echo forceoutSession();
  exit();
}

$pdf = new FPDF(); // Tamaño Carta Vertical
$pdf->AddPage();

// --- ENCABEZADO FIJO ---
$pdf->SetFont('Arial', '', 10);
$pdf->Image(LOGOCR, 160, 10, 40, 0, '', WEBSITE); // Ajustado a la derecha
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('MODELOS DE EQUIPOS'), 0, 1, 'C');
$pdf->Ln(2);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->SetFillColor(50, 50, 50); // Gris oscuro profesional
  $pdf->SetTextColor(255);

  // Anchos ajustados para sumar ~196mm (ancho útil de hoja carta)
  $pdf->Cell(25, 7, 'TIPO', 1, 0, 'C', true);
  $pdf->Cell(25, 7, 'LINEA', 1, 0, 'C', true);
  $pdf->Cell(25, 7, 'MODELO', 1, 0, 'C', true);
  $pdf->Cell(25, 7, 'TONER', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'DK', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'DV', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'FK', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'DP', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'TR', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'DR', 1, 1, 'C', true);
  $pdf->SetTextColor(0);


$SQL = "SELECT * FROM Modelos M
        ORDER BY M.modelo_tipo ASC";
$QRY = consultaData($SQL);
foreach ($QRY['dataFetch'] as $modelo) {
  $pdf->Cell(25, 5, utf8_decode($modelo['modelo_tipo']), 1, 0, 'C');
  $pdf->Cell(25, 5, utf8_decode($modelo['modelo_linea']), 1, 0, 'C');
  $pdf->Cell(25, 5, utf8_decode($modelo['modelo_modelo']), 1, 0, 'C');
  $pdf->Cell(25, 5, utf8_decode($modelo['modelo_toner']), 1, 0, 'C');
  $pdf->Cell(15, 5, utf8_decode($modelo['modelo_DK']), 1, 0, 'C');
  $pdf->Cell(15, 5, utf8_decode($modelo['modelo_DV']), 1, 0, 'C');
  $pdf->Cell(15, 5, utf8_decode($modelo['modelo_FK']), 1, 0, 'C');
  $pdf->Cell(15, 5, utf8_decode($modelo['modelo_DP']), 1, 0, 'C');
  $pdf->Cell(15, 5, utf8_decode($modelo['modelo_TR']), 1, 0, 'C');
  $pdf->Cell(15, 5, utf8_decode($modelo['modelo_DR']), 1, 1, 'C');
}

$pdf->Output('I', "Modelos de Equipos.pdf");
