<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/vista/formats/printLect_header.php';

$prevYear = $_POST['current_year'];
$currYear = $_POST['current_year'];
$currMonthNumber = $_POST['current_month'];
$prevMonthNumber = $currMonthNumber - 1;
if ($prevMonthNumber == 0) {
  $prevMonthNumber = 12;
  $prevYear = $currYear - 1;
}

$prevDayMonth = strtoupper(dateFormat(date('d') . "-" . $prevMonthNumber . "-" . $prevYear, "diaNmesLcorto"));
$currDayMonth = strtoupper(dateFormat(date('d') . "-" . $currMonthNumber . "-" . $currYear, "diaNmesLcorto"));
$prevMonthLetter = strtoupper(dateFormat(date('d') . "-" . $prevMonthNumber . "-" . $prevYear, "mesL"));
$currMonthLetter = strtoupper(dateFormat(date('d') . "-" . $currMonthNumber . "-" . $currYear, "mesL"));

/* ===================================== LLAMADO DEL HEADER ==================================== */
headerPDF($pdf, $_POST, TRUE, FALSE);
/* ============================================================================================= */

/* =================================== CAPTURA DE CONTADORES =================================== */
if (TRUE) {
  $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(190, 1, '-------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
  $pdf->Cell(190, 6, 'PROCESO', 0, 1, 'C');
  $pdf->Cell(190, 0, '-------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

  $pdf->Ln(2);

  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(31);
  $pdf->Cell(31, 6, ($currYear == $prevYear) ? $currYear : $prevYear . "|" . $currYear, 0, 0, 'C');
  $pdf->Cell(31, 6, $prevMonthLetter, 0, 0, 'C');
  $pdf->Cell(31, 6, $currMonthLetter, 0, 0, 'C');
  $pdf->Cell(31, 6, 'PROSC. TOTAL', 1, 1, 'C');

  $pdf->SetFont('Arial', '', 13);
  $pdf->Cell(31);
  $pdf->Cell(31, 8, 'ESCANEO', 1, 0, 'C');
  $pdf->Cell(31, 8, "", 1, 0, 'C');
  $pdf->Cell(31, 8, "", 1, 0, 'C');
  $pdf->Cell(31, 8, "", 1, 1, 'C');

  $pdf->Cell(31);
  $pdf->Cell(31, 8, 'B&N', 1, 0, 'C');
  $pdf->Cell(31, 8, "", 1, 0, 'C');
  $pdf->Cell(31, 8, "", 1, 0, 'C');
  $pdf->Cell(31, 8, "", 1, 1, 'C');

  if ($_POST["modelo_tipo"] == "Multicolor") {
    $pdf->Cell(31);
    $pdf->Cell(31, 8, 'COLOR', 1, 0, 'C');
    $pdf->Cell(31, 8, "", 1, 0, 'C');
    $pdf->Cell(31, 8, "", 1, 0, 'C');
    $pdf->Cell(31, 8, "", 1, 1, 'C');
  }
}
/* ============================================================================================= */

/* ===================================== LLAMADO DEL FOODER ==================================== */
fooderPDF($pdf, $_POST, TRUE);
/* ============================================================================================= */


$output = $currMonthLetter . "-" . $currYear . " - " . $_POST["cliente_rs"] . " (" . $_POST["contrato_folio"] . "-" . $_POST["renta_folio"] . ") " . $_POST["renta_depto"] . " - Toma de lectura.pdf";
$pdf->Output('I', $output);
