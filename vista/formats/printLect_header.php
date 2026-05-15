<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
  echo forceoutSession();
  exit();
}


$pdf = new FPDF();

function headerPDF($pdf, $data, $fechaPDF, $factPDF)
{
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->Ln(5);

  $pdf->SetFillColor(225, 225, 225);


  // if ($data['cliente_emiFact'] == 1) {
  $pdf->AddLink();
  $pdf->Image(LOGOCR, 10, 10, 50, 0, '', WEBSITE);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(190, 4, COMPANYNAME, 0, 1, 'R');
  $pdf->SetFont('Arial', 'I', 6);
  $pdf->Cell(190, 2, dataRFC1, 0, 1, 'R');
  $pdf->Cell(190, 2, dataRFC2, 0, 1, 'R');
  $pdf->Cell(190, 2, dataRFC3, 0, 1, 'R');
  $pdf->Cell(190, 2, dataRFC4, 0, 1, 'R');
  $pdf->Ln(2);
  // } else {
  //   $pdf->AddLink();
  //   $pdf->Image(LOGOCR, 10, 10, 50, 0, '', WEBSITE);
  //   $pdf->SetFont('Arial', 'B', 10);
  //   $pdf->Cell(190, 4, COMPANYNAME, 0, 1, 'R');
  //   $pdf->SetFont('Arial', 'I', 6);
  //   $pdf->Cell(190, 2, "MIMI FLORES OLAN (MFO)", 0, 1, 'R');
  //   $pdf->Cell(190, 2, "DIR1", 0, 1, 'R');
  //   $pdf->Cell(190, 2, "DIR2", 0, 1, 'R');
  //   $pdf->Cell(190, 2, "DIR3", 0, 1, 'R');
  //   $pdf->Ln(2);
  // }

  if ($fechaPDF === TRUE) {
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(80);
    $pdf->Cell(170, 6, 'FECHA DE LECTURA', 0, 1, 'C');
    $pdf->Cell(80);
    $pdf->Cell(170, 6, "______ / ______ / ______", 0, 1, 'C');
  } else if ($fechaPDF == "data") {
    list($currYear, $currMonth, $currDay) = explode('-', $data['curr_lectura_fecha']);
    $fechaComp = utf8_decode(strtoupper(dateFormat($currDay . "-" . $currMonth . "-" . $currYear, 'full')));
    $fechaComp = str_replace("é", "E", $fechaComp);

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(320, 6, 'FECHA DE LECTURA', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(320, 6, $fechaComp, 0, 1, 'C');
  }

  // =========================================================================================================================== //
  $pdf->SetFont('Arial', 'B', 18);
  $pdf->Cell(70, 4, "HOJA DE LECTURA", 0, 1, 'C');

  $pdf->Ln(2);

  $pdf->SetFont('Arial', 'B', 14);
  $pdf->Cell(190, 6, 'DATOS DE LA RENTA', 1, 1, 'C', true);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(30, 6, 'RAZON SOCIAL:', 1, 0, 'C', true);
  $pdf->SetFont('Arial', '', (strlen($data['cliente_rs']) > 55) ? 8 : 10);
  $pdf->Cell(110, 6, utf8_decode($data['cliente_rs']), 1, 0, 'C');
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(10, 6, 'RFC:', 1, 0, 'C', true);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(40, 6, utf8_decode($data['cliente_rfc']), 1, 1, 'C');

  if ($factPDF) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 6, 'USO DE CFDI:', 1, 0, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(100, 6, utf8_decode($data['CFDI_codigo'] . " | " . $data["CFDI_descripcion"]), 1, 0, 'C');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(30, 6, 'CODIGO POSTAL:', 1, 0, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(30, 6, utf8_decode($data["cliente_cp"]), 1, 1, 'C');

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(30, 6, 'REGIMEN FISCAL:', 1, 0, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(160, 6, utf8_decode($data['regFis_codigo'] . " | " . $data["regFis_descripcion"]), 1, 1, 'C');
  }

  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(30, 6, 'SERVICIO:', 1, 0, 'C', true);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(80, 6, utf8_decode($data['renta_depto']), 1, 0, 'C');
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(25, 6, 'CONTACTO:', 1, 0, 'C', true);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(55, 6, ($data['renta_telefono'] == "") ? $data['renta_contacto'] : utf8_decode($data['renta_contacto'] . " | " . $data['renta_telefono']), 1, 1, 'C');

  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(30, 6, 'MODELO:', 1, 0, 'C', true);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(80, 6, utf8_decode($data['modelo_linea'] . " " . $data['modelo_modelo']), 1, 0, 'C');
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(25, 6, 'NO. SERIE:', 1, 0, 'C', true);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(55, 6, utf8_decode($data['equipo_serie']), 1, 1, 'C');
  // =========================================================================================================================== //
  $pdf->Ln(2);
}

function fooderPDF($pdf, $data, $firmaPDF)
{
  $pdf->Ln(5);

  //--------------------------------------------------------------------------------
  $inc_esc = ($data["renta_inc_esc"] == 0) ? "No Definido" : $data["renta_inc_esc"];
  $inc_bn = ($data["renta_inc_bn"] == 0) ? "No Definido"  : $data["renta_inc_bn"];
  $inc_col = ($data["renta_inc_col"] == 0) ? "No Definido"  : $data["renta_inc_col"];

  $exc_esc = (is_numeric($inc_esc)) ? "$" . $data["renta_exc_esc"] : "No Definido";
  $exc_bn = (is_numeric($inc_bn)) ? "$" . $data["renta_exc_bn"] : "No Definido";
  $exc_col = (is_numeric($inc_col)) ? "$" . $data["renta_exc_col"] : "No Definido";

  $renta_costo = ($data["renta_costo"] == 0) ? "No Definida" : "$" . $data["renta_costo"];
  //--------------------------------------------------------------------------------

  $pdf->Cell(52);
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(28, 6, 'INCLUIDO', 0, 0, 'C');
  $pdf->SetFont('Arial', 'B', 9);
  $pdf->Cell(28, 6, 'TOTAL DE EXC', 0, 0, 'C');
  $pdf->Cell(28, 6, 'COSTO POR EXC', 0, 0, 'C');
  $pdf->Cell(28, 6, 'SUBTOTAL', 0, 1, 'C');

  $pdf->SetFont('Arial', '', 11);
  $pdf->Cell(24);
  $pdf->Cell(28, 6, 'ESCANEO', 1, 0, 'C');
  $pdf->Cell(28, 6, $inc_esc, 1, 0, 'C');
  $pdf->Cell(28, 6, (is_numeric($inc_esc)) ? "" : $inc_esc, 1, 0, 'C');
  $pdf->Cell(28, 6, (is_numeric($exc_esc)) ? "$" . $exc_esc : $exc_esc, 1, 0, "C");
  $pdf->Cell(28, 6, "$", 1, 1, 'L');

  $pdf->Cell(24);
  $pdf->Cell(28, 6, 'B&N', 1, 0, 'C');
  $pdf->Cell(28, 6, $inc_bn, 1, 0, 'C');
  $pdf->Cell(28, 6, (is_numeric($inc_bn)) ? "" : $inc_bn, 1, 0, 'C');
  $pdf->Cell(28, 6, (is_numeric($exc_bn)) ? "$" . $exc_bn : $exc_bn, 1, 0, "C");
  $pdf->Cell(28, 6, "$", 1, 1, 'L');

  if ($data["modelo_tipo"] == "Multicolor") {
    $pdf->Cell(24);
    $pdf->Cell(28, 6, 'COLOR', 1, 0, 'C');
    $pdf->Cell(28, 6, $inc_col, 1, 0, 'C');
    $pdf->Cell(28, 6, (is_numeric($inc_col)) ? "" : $inc_col, 1, 0, 'C');
    $pdf->Cell(28, 6, (is_numeric($exc_col)) ? "$" . $exc_col : $exc_col, 1, 0, "C");
    $pdf->Cell(28, 6, "$", 1, 1, 'L');
  }


  $pdf->Cell(108);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(28, 6, 'MENSUALIDAD', 1, 0, 'C');
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(28, 6, $renta_costo, 1, 1, "C");

  $pdf->Cell(108);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(28, 6, 'TOTAL SIN IVA', 1, 0, 'C');
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(28, 6, "$", 1, 1, 'L');

  /* =============================================================================================================================== */

  $pdf->Ln(3);
  $pdf->SetFont('Arial', 'B', 14);
  $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
  $pdf->Cell(95, 6, 'ABASTECIMIENTO', 0, 0, 'C');
  $pdf->Cell(95, 6, 'COMENTARIOS', 0, 1, 'C');
  $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

  // ABASTECIMIENTO
  $pdf->SetFont('Arial', 'I', 12);
  $pdf->Cell(95, 7, "CANTIDAD EN EQUIPO", 0, 0, 'C');
  $pdf->Cell(95, 7, "", 0, 1, 'C');

  // ABASTECIMIENTO
  $pdf->SetFont('Arial', 'I', 11);
  if ($data['modelo_tipo'] == "Multicolor") {
    $pdf->Cell(19, 7, '%K', 1, 0, 'R');
    $pdf->Cell(19, 7, '%M', 1, 0, 'R');
    $pdf->Cell(19, 7, '%C', 1, 0, 'R');
    $pdf->Cell(19, 7, '%Y', 1, 0, 'R');
    $pdf->Cell(19, 7, '%R', 1, 0, 'R');
  } else {
    $pdf->Cell(22);
    if ($data['modelo_modelo'] != 'M2040dn/L' && $data['modelo_modelo'] != 'M2035dn/L' && $data['modelo_modelo'] != 'M5521cdn' && $data['modelo_modelo'] != 'M5526cdw' && $data['modelo_modelo'] != 'M5526cdn') {
      $pdf->Cell(25, 7, '%K', 1, 0, 'R');
      $pdf->Cell(25, 7, '%R', 1, 0, 'R');
      $pdf->Cell(23, 7, '', 0, 0, 'R');
    } else {
      $pdf->Cell(11, 7, '', 0, 0, 'R');
      $pdf->Cell(30, 7, '%K', 1, 0, 'R');
      $pdf->Cell(43, 7, '', 0, 0, 'R');
    }
  }
  $pdf->Cell(95, 7, "", 0, 1, 'C');

  // ABASTECIMIENTO
  $pdf->SetFont('Arial', 'I', 12);
  $pdf->Cell(95, 7, "RESERVA EN STOCK", 0, 0, 'C');
  $pdf->Cell(95, 7, "", 0, 1, 'C');

  // ABASTECIMIENTO
  $pdf->SetFont('Arial', 'I', 11);
  if ($data['modelo_tipo'] == "Multicolor") {
    $pdf->Cell(19, 7, 'K', 1, 0, 'L');
    $pdf->Cell(19, 7, 'M', 1, 0, 'L');
    $pdf->Cell(19, 7, 'C', 1, 0, 'L');
    $pdf->Cell(19, 7, 'Y', 1, 0, 'L');
    $pdf->Cell(19, 7, 'R', 1, 0, 'L');
  } else {
    $pdf->Cell(22);
    if ($data['modelo_modelo'] != 'M2040dn/L' && $data['modelo_modelo'] != 'M2035dn/L' && $data['modelo_modelo'] != 'M5521cdn' && $data['modelo_modelo'] != 'M5526cdw' && $data['modelo_modelo'] != 'M5526cdn') {
      $pdf->Cell(25, 7, 'K', 1, 0, 'L');
      $pdf->Cell(25, 7, 'R', 1, 0, 'L');
      $pdf->Cell(23, 7, '', 0, 0, 'L');
    } else {
      $pdf->Cell(11, 7, '', 0, 0, 'L');
      $pdf->Cell(30, 7, '', 1, 0, 'L');
      $pdf->Cell(43, 7, '', 0, 0, 'L');
    }
  }
  $pdf->Cell(95, 7, "", 0, 1, 'C');

  // ==================================================================================================================================================================== //

  if ($firmaPDF) {
    $pdf->SetY(260);
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(95, 5, utf8_decode('____________________________            '), 0, 0, 'C');
    $pdf->Cell(95, 5, utf8_decode('            ____________________________'), 0, 1, 'C');
    $pdf->Cell(110);
    $pdf->Cell(80, 3, utf8_decode('FIRMA CLIENTE'), 0, 0, 'C');
  }
}
