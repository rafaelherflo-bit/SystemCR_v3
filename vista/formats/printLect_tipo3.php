<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/vista/formats/printLect_header.php';

// =============================================  DEFINICION DE FECHAS ======================================================= //

list($currYear, $currMonth, $currDay) = explode('-', $_POST['curr_lectura_fecha']);
list($prevYear, $prev_mes, $prev_dia) = explode('-', $_POST['prev_lectura_fecha']);

$currDayNumber = strtoupper(dateFormat($currDay . "-" . $currMonth . "-" . $currYear, "diaN"));
$currMonthLetter = strtoupper(dateFormat($currDay . "-" . $currMonth . "-" . $currYear, "mesL"));
$currDayNumMonthLetShort = strtoupper(dateFormat($currDay . "-" . $currMonth . "-" . $currYear, "diaNmesLcorto"));

$prevDayNumber = strtoupper(dateFormat($prev_dia . "-" . $prev_mes . "-" . $prevYear, "diaN"));
$prevMonthLetter = strtoupper(dateFormat($prev_dia . "-" . $prev_mes . "-" . $prevYear, "mesL"));
$prevDayNumMonthLetShort = strtoupper(dateFormat($prev_dia . "-" . $prev_mes . "-" . $prevYear, "diaNmesLcorto"));
// =========================================================================================================================== //

$currYear = $_POST['current_year'];
$prevYear = $_POST['current_year'];
$currMonthNumber = $_POST['current_month'];
$prevMonthNumber = $currMonthNumber - 1;
if ($prevMonthNumber == 0) {
  $prevMonthNumber = 12;
  $prevYear = $currYear - 1;
}

$prevMonthLetter = strtoupper(dateFormat(date('d') . "-" . $prevMonthNumber . "-" . $prevYear, "mesL"));
$currMonthLetter = strtoupper(dateFormat(date('d') . "-" . $currMonthNumber . "-" . $currYear, "mesL"));

/* ===================================== LLAMADO DEL HEADER ==================================== */
headerPDF($pdf, $_POST, "data", TRUE);
/* ============================================================================================= */

/* =================================== CAPTURA DE CONTADORES =================================== */
if (TRUE) {
  $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(190, 1, '--------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
  $pdf->Cell(190, 6, 'PROCESO', 0, 1, 'C');
  $pdf->Cell(190, 0, '--------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

  $pdf->Ln(3);

  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(31);
  $pdf->Cell(31, 6, ($currYear == $prevYear) ? $currYear : $prevYear . "|" . $currYear, 0, 0, 'C');
  $pdf->Cell(31, 6, $prevDayNumMonthLetShort, 0, 0, 'C');
  $pdf->Cell(31, 6, $currDayNumMonthLetShort,  0, 0, 'C');
  $pdf->Cell(31, 6, 'PROSC. TOTAL', 1, 1, 'C');

  $curr_lectura_esc = $_POST['curr_lectura_esc'];
  $curr_lectura_bn = $_POST['curr_lectura_bn'];
  $curr_lectura_col = $_POST['curr_lectura_col'];

  $prev_lectura_esc = $_POST['prev_lectura_esc'];
  $prev_lectura_bn = $_POST['prev_lectura_bn'];
  $prev_lectura_col = $_POST['prev_lectura_col'];

  $res_lectura_esc = $curr_lectura_esc - $prev_lectura_esc;
  $res_lectura_bn = $curr_lectura_bn - $prev_lectura_bn;
  $res_lectura_col = $curr_lectura_col - $prev_lectura_col;

  $pdf->SetFont('Arial', '', 13);
  $pdf->Cell(31);
  $pdf->Cell(31, 8, 'ESCANEO', 1, 0, 'C');
  $pdf->Cell(31, 8, $prev_lectura_esc, 1, 0, 'C');
  $pdf->Cell(31, 8, $curr_lectura_esc, 1, 0, 'C');
  $pdf->Cell(31, 8, $res_lectura_esc, 1, 1, 'C');

  $pdf->Cell(31);
  $pdf->Cell(31, 8, 'B&N', 1, 0, 'C');
  $pdf->Cell(31, 8, $prev_lectura_bn, 1, 0, 'C');
  $pdf->Cell(31, 8, $curr_lectura_bn, 1, 0, 'C');
  $pdf->Cell(31, 8, $res_lectura_bn, 1, 1, 'C');

  if ($_POST["modelo_tipo"] == "Multicolor") {
    $pdf->Cell(31);
    $pdf->Cell(31, 8, 'COLOR', 1, 0, 'C');
    $pdf->Cell(31, 8, $prev_lectura_col, 1, 0, 'C');
    $pdf->Cell(31, 8, $curr_lectura_col, 1, 0, 'C');
    $pdf->Cell(31, 8, $res_lectura_col, 1, 1, 'C');
  }

  $pdf->Ln(3);

  $pdf->SetFont('Arial', 'I', 11);
  $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

  $pdf->Ln(5);

  //--------------------------------------------------------------------------------
  $inc_esc = $_POST["renta_inc_esc"];
  $inc_bn = $_POST["renta_inc_bn"];
  $inc_col = $_POST["renta_inc_col"];

  $exc_esc = $_POST["renta_exc_esc"];
  $exc_bn = $_POST["renta_exc_bn"];
  $exc_col = $_POST["renta_exc_col"];

  $resta_inc_esc = $res_lectura_esc - $inc_esc;
  if ($resta_inc_esc < 0) {
    $resta_inc_esc = 0;
  }
  $total_exc_esc = $resta_inc_esc * $exc_esc;

  $resta_inc_bn = $res_lectura_bn - $inc_bn;
  if ($resta_inc_bn < 0) {
    $resta_inc_bn = 0;
  }
  $total_exc_bn = $resta_inc_bn * $exc_bn;

  $resta_inc_col = $res_lectura_col - $inc_col;
  if ($resta_inc_col < 0) {
    $resta_inc_col = 0;
  }
  $total_exc_col = $resta_inc_col * $exc_col;

  $subTotal = $total_exc_esc + $total_exc_bn + $total_exc_col + $_POST["renta_costo"];
  //--------------------------------------------------------------------------------

  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(28);
  $pdf->Cell(28);
  $pdf->Cell(28, 6, 'INCLUIDO', 0, 0, 'C');
  $pdf->SetFont('Arial', 'B', 9);
  $pdf->Cell(28, 6, 'TOTAL DE EXC', 0, 0, 'C');
  $pdf->Cell(28, 6, 'COSTO POR EXC', 0, 0, 'C');
  $pdf->Cell(28, 6, 'SUBTOTAL', 0, 1, 'C');

  $pdf->SetFont('Arial', '', 11);
  $pdf->Cell(28);
  $pdf->Cell(28, 6, 'ESCANEO', 1, 0, 'C');
  $pdf->Cell(28, 6, ($inc_esc == 0) ? "No Definido" : $inc_esc, 1, 0, 'C');
  $pdf->Cell(28, 6, ($inc_esc == 0) ? "No Definido" : $resta_inc_esc, 1, 0, 'C');
  $pdf->Cell(28, 6, ($inc_esc == 0) ? "No Definido" : "$" . $exc_esc, 1, 0, 'C');
  $pdf->Cell(28, 6, "$" . $total_exc_esc, 1, 1, 'C');

  $pdf->Cell(28);
  $pdf->Cell(28, 6, 'B&N', 1, 0, 'C');
  $pdf->Cell(28, 6, ($inc_bn == 0) ? "No Definido" : $inc_bn, 1, 0, 'C');
  $pdf->Cell(28, 6, ($inc_bn == 0) ? "No Definido" : $resta_inc_bn, 1, 0, 'C');
  $pdf->Cell(28, 6, ($inc_bn == 0) ? "No Definido" : "$" . $exc_bn, 1, 0, 'C');
  $pdf->Cell(28, 6, "$" . $total_exc_bn, 1, 1, 'C');

  if ($_POST["modelo_tipo"] == "Multicolor") {
    $pdf->Cell(28);
    $pdf->Cell(28, 6, 'COLOR', 1, 0, 'C');
    $pdf->Cell(28, 6, ($inc_col == 0) ? "No Definido" : $inc_col, 1, 0, 'C');
    $pdf->Cell(28, 6, ($inc_col == 0) ? "No Definido" : $resta_inc_col, 1, 0, 'C');
    $pdf->Cell(28, 6, ($inc_col == 0) ? "No Definido" : "$" . $exc_col, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $total_exc_col, 1, 1, 'C');
  }

  $pdf->Cell(112);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(28, 6, 'MENSUALIDAD', 1, 0, 'C');
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(28, 6, ($_POST["renta_costo"] == 0) ? "No Definido" : "$" . $_POST["renta_costo"], 1, 1, 'C');
  $pdf->Cell(112);
  $pdf->Cell(28, 6, 'TOTAL SIN IVA', 1, 0, 'C');
  $pdf->Cell(28, 6, "$" . $subTotal, 1, 1, 'C');

  /* =====================================================================================================================================
    ================================================================================================================================= */

  $pdf->Ln(3);
  $pdf->SetFont('Arial', 'B', 14);
  $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
  $pdf->Cell(190, 6, 'ABASTECIMIENTO', 0, 1, 'C');
  $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

  $pdf->Ln(5); // Salto de linea.

  // ABASTECIMIENTO
  $pdf->SetFont('Arial', 'I', 12);
  $pdf->Cell(95);
  $pdf->Cell(95, 7, "CANTIDAD EN EQUIPO", 0, 1, 'C');

  // ABASTECIMIENTO
  $pdf->SetFont('Arial', 'I', 11);
  $pdf->Cell(95);
  if ($_POST['modelo_tipo'] == "Multicolor") {
    $pdf->Cell(19, 7, 'K: %' . $_POST['equipo_nivel_K'], 1, 0, 'C');
    $pdf->Cell(19, 7, 'M: %' . $_POST['equipo_nivel_M'], 1, 0, 'C');
    $pdf->Cell(19, 7, 'C: %' . $_POST['equipo_nivel_C'], 1, 0, 'C');
    $pdf->Cell(19, 7, 'Y: %' . $_POST['equipo_nivel_Y'], 1, 0, 'C');
    $pdf->Cell(19, 7, 'R: %' . $_POST['equipo_nivel_R'], 1, 1, 'C');
  } else {
    $pdf->Cell(22);
    if ($_POST['modelo_modelo'] != 'M2040dn/L' && $_POST['modelo_modelo'] != 'M2035dn/L' && $_POST['modelo_modelo'] != 'M5521cdn' && $_POST['modelo_modelo'] != 'M5526cdw' && $_POST['modelo_modelo'] != 'M5526cdn') {
      $pdf->Cell(25, 7, 'K: %' . $_POST['equipo_nivel_K'], 1, 0, 'C');
      $pdf->Cell(25, 7, 'R: %' . $_POST['equipo_nivel_R'], 1, 0, 'C');
      $pdf->Cell(23, 7, '', 0, 1, 'C');
    } else {
      $pdf->Cell(11, 7, '', 0, 0, 'C');
      $pdf->Cell(30, 7, 'K: %' . $_POST['equipo_nivel_K'], 1, 0, 'C');
      $pdf->Cell(43, 7, '', 0, 1, 'C');
    }
  }

  // ABASTECIMIENTO
  $pdf->SetFont('Arial', 'I', 12);
  $pdf->Cell(95);
  $pdf->Cell(95, 7, "RESERVA EN STOCK", 0, 1, 'C');

  // ABASTECIMIENTO
  $pdf->SetFont('Arial', 'I', 11);
  $pdf->Cell(95);

  $stockK = ($_POST['renta_stock_K'] > 1) ? "K: " . $_POST['renta_stock_K'] . " Pzs" : "K: " . $_POST['renta_stock_K'] . " Pz";
  $stockM = ($_POST['renta_stock_M'] > 1) ? "M: " . $_POST['renta_stock_M'] . " Pzs" : "M: " . $_POST['renta_stock_M'] . " Pz";
  $stockC = ($_POST['renta_stock_C'] > 1) ? "C: " . $_POST['renta_stock_C'] . " Pzs" : "C: " . $_POST['renta_stock_C'] . " Pz";
  $stockY = ($_POST['renta_stock_Y'] > 1) ? "Y: " . $_POST['renta_stock_Y'] . " Pzs" : "Y: " . $_POST['renta_stock_Y'] . " Pz";
  $stockR = ($_POST['renta_stock_R'] > 1) ? "R: " . $_POST['renta_stock_R'] . " Pzs" : "R: " . $_POST['renta_stock_R'] . " Pz";

  if ($_POST['modelo_tipo'] == "Multicolor") {
    $pdf->Cell(19, 7, $stockK, 1, 0, 'C');
    $pdf->Cell(19, 7, $stockM, 1, 0, 'C');
    $pdf->Cell(19, 7, $stockC, 1, 0, 'C');
    $pdf->Cell(19, 7, $stockY, 1, 0, 'C');
    $pdf->Cell(19, 7, $stockR, 1, 1, 'C');
  } else {
    $pdf->Cell(22);
    if ($_POST['modelo_modelo'] != 'M2040dn/L' && $_POST['modelo_modelo'] != 'M2035dn/L' && $_POST['modelo_modelo'] != 'M5521cdn' && $_POST['modelo_modelo'] != 'M5526cdw' && $_POST['modelo_modelo'] != 'M5526cdn') {
      $pdf->Cell(25, 7, $stockK, 1, 0, 'C');
      $pdf->Cell(25, 7, $stockR, 1, 0, 'C');
      $pdf->Cell(23, 7, '', 0, 1, 'C');
    } else {
      $pdf->Cell(11, 7, '', 0, 0, 'C');
      $pdf->Cell(30, 7, $stockK, 1, 0, 'C');
      $pdf->Cell(43, 7, '', 0, 1, 'C');
    }
  }
}

// ==================================================================================================================================================================== //
// Helper: busca el archivo con varias extensiones posibles
function findLecturaFile($year, $month, $subdir, $id)
{
  $month = str_pad($month, 2, '0', STR_PAD_LEFT);
  $base = SERVERDIR . "DocsCR/Lecturas/{$year}/{$month}/{$subdir}/";
  $exts = ['.jpg', '.jpeg', '.JPG', '.JPEG', '.png', '.PNG'];
  foreach ($exts as $ext) {
    $p = $base . $id . $ext;
    if (file_exists($p)) return $p;
  }
  return false;
}

// Determinar year/month/ids de entrada (con fallbacks)
$getYearMonth = function ($dateField, $yearField, $monthField) {
  if (!empty($_POST[$dateField])) {
    $parts = explode('-', $_POST[$dateField]);
    if (count($parts) >= 2) return [ $parts[0], $parts[1] ];
  }
  $y = $_POST[$yearField] ?? date('Y');
  $m = $_POST[$monthField] ?? date('m');
  return [ $y, $m ];
};

list($cYear, $cMonth) = $getYearMonth('curr_lectura_fecha', 'current_year', 'current_month');
list($pYear, $pMonth) = $getYearMonth('prev_lectura_fecha', 'current_year', 'current_month');

$currId = isset($_POST['curr_lectura_id']) ? decryption($_POST['curr_lectura_id']) : null;
$prevId = isset($_POST['prev_lectura_id']) ? decryption($_POST['prev_lectura_id']) : null;

if (!empty($currId)) {
  $currRutaFormato = findLecturaFile($cYear, $cMonth, 'Formatos', $currId);
  $currRutaPE = findLecturaFile($cYear, $cMonth, 'PE', $currId);
  if ($currRutaFormato) {
    $pdf->AddPage();
    $pdf->Image($currRutaFormato, 2, 10, 210, 290);
  }
  if ($currRutaPE) {
    $pdf->AddPage();
    $pdf->Image($currRutaPE, 2, 10, 210, 290);
  }
}
// ==================================================================================================================================================================== //

$curr_reporte_id = $_POST['curr_reporte_id'];
if (!empty($curr_reporte_id)) {
  // Aquí debes usar tu función de consulta para traer el body_correo
  $sql = "SELECT body_correo FROM historial_reportes WHERE id = '$curr_reporte_id'";
  $data = consultaData($sql); // Usa tu función de BD

  if ($data['numRows'] > 0) {
    $pdf->AddPage();

    // Configuramos fuente Monoespaciada (Courier) para que las columnas alineen
    $pdf->SetFont('Courier', '', 9);

    $texto = $data['dataFetch'][0]['body_correo'];

    // MultiCell respeta los saltos de línea (\n) del campo de texto
    // Usamos utf8_decode para caracteres especiales si es necesario
    $pdf->MultiCell(0, 4, utf8_decode($texto == "" ? "Sin Body de lectura actual" : $texto));
    $impreso = true;
  }
}

// ==================================================================================================================================================================== //
if (!empty($prevId)) {
  $prevRutaFormato = findLecturaFile($pYear, $pMonth, 'Formatos', $prevId);
  $prevRutaPE = findLecturaFile($pYear, $pMonth, 'PE', $prevId);
  if ($prevRutaFormato) {
    $pdf->AddPage();
    $pdf->Image($prevRutaFormato, 2, 10, 210, 290);
  }
  if ($prevRutaPE) {
    $pdf->AddPage();
    $pdf->Image($prevRutaPE, 2, 10, 210, 290);
  }
}
// ==================================================================================================================================================================== //

$prev_reporte_id = $_POST['prev_reporte_id'];
if (!empty($prev_reporte_id)) {
  // Aquí debes usar tu función de consulta para traer el body_correo
  $sql = "SELECT body_correo FROM historial_reportes WHERE id = '$prev_reporte_id'";
  $data = consultaData($sql); // Usa tu función de BD

  if ($data['numRows'] > 0) {
    $pdf->AddPage();

    // Configuramos fuente Monoespaciada (Courier) para que las columnas alineen
    $pdf->SetFont('Courier', '', 9);

    $texto = $data['dataFetch'][0]['body_correo'];

    // MultiCell respeta los saltos de línea (\n) del campo de texto
    // Usamos utf8_decode para caracteres especiales si es necesario
    $pdf->MultiCell(0, 4, utf8_decode($texto == "" ? "Sin Body de lectura anterior" : $texto));
    $impreso = true;
  }
}

// ==================================================================================================================================================================== //

$output = $cMonth . "-" . $cYear . " - " . $_POST["cliente_rs"] . " (" . $_POST["contrato_folio"] . "-" . $_POST["renta_folio"] . ") " . $_POST["renta_depto"] . " - Toma de lectura.pdf";
$pdf->Output('I', $output);
