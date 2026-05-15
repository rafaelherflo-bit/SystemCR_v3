<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/vista/formats/printLect_header.php';

if (isset($_POST['print'])) {
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

    $pdf->Ln(3);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(31);
    $pdf->Cell(31, 6, ($currYear == $prevYear) ? $currYear : $prevYear . "|" . $currYear, 0, 0, 'C');
    $pdf->Cell(31, 6, $prevDayMonth, 0, 0, 'C');
    $pdf->Cell(31, 6, $currMonthLetter, 0, 0, 'C');
    $pdf->Cell(31, 6, 'PROSC. TOTAL', 1, 1, 'C');

    $prev_lectura_esc = $_POST['prev_lectura_esc'];
    $prev_lectura_bn = $_POST['prev_lectura_bn'];
    $prev_lectura_col = $_POST['prev_lectura_col'];

    $pdf->SetFont('Arial', '', 13);
    $pdf->Cell(31);
    $pdf->Cell(31, 8, 'ESCANEO', 1, 0, 'C');
    $pdf->Cell(31, 8, $prev_lectura_esc, 1, 0, 'C');
    $pdf->Cell(31, 8, "", 1, 0, 'C');
    $pdf->Cell(31, 8, "", 1, 1, 'C');

    $pdf->Cell(31);
    $pdf->Cell(31, 8, 'B&N', 1, 0, 'C');
    $pdf->Cell(31, 8, $prev_lectura_bn, 1, 0, 'C');
    $pdf->Cell(31, 8, "", 1, 0, 'C');
    $pdf->Cell(31, 8, "", 1, 1, 'C');

    if ($_POST["modelo_tipo"] == "Multicolor") {
      $pdf->Cell(31);
      $pdf->Cell(31, 8, 'COLOR', 1, 0, 'C');
      $pdf->Cell(31, 8, $prev_lectura_col, 1, 0, 'C');
      $pdf->Cell(31, 8, "", 1, 0, 'C');
      $pdf->Cell(31, 8, "", 1, 1, 'C');
    }
  }
  /* ============================================================================================= */

  /* ===================================== LLAMADO DEL FOODER ==================================== */
  fooderPDF($pdf, $_POST, TRUE);
  /* ============================================================================================= */
} else {

  list($currYear, $prevMonthNumber, $prevDay) = explode('-', $_POST['prev_lectura_fecha']);

  $pdf->AliasNbPages();
  $rutaFormato = SERVERDIR . "DocsCR/Lecturas/" . $currYear . "/" . $prevMonthNumber . "/Formatos" . "/" . decryption($_POST['prev_lectura_id']) . ".jpg";
  $rutaPE = SERVERDIR . "DocsCR/Lecturas/" . $currYear . "/" . $prevMonthNumber . "/PE" . "/" . decryption($_POST['prev_lectura_id']) . ".jpg";

  $fileFormatoExists = file_exists($rutaFormato);
  $filePEExists = file_exists($rutaPE);
  if (!$fileFormatoExists && !$filePEExists) {
    echo '
    <script>
      alert("No se encontraron archivos de lectura fisica para visualizar.");
      window.close();
    </script>    
    ';
    exit();
  }

  if ($fileFormatoExists) {
    $pdf->AddPage();
    $pdf->Image($rutaFormato, 2, 10, 210, 290, "JPEG");
  }
  if ($filePEExists) {
    $pdf->AddPage();
    $pdf->Image($rutaPE, 2, 10, 210, 290, "JPEG");
  }
}

// ==================================================================================================================================================================== //

$output = $prevMonthNumber . "-" . $currYear . " - " . $_POST["cliente_rs"] . " (" . $_POST["contrato_folio"] . "-" . $_POST["renta_folio"] . ") " . $_POST["renta_depto"] . " - Toma de lectura.pdf";
$pdf->Output("I", $output);
