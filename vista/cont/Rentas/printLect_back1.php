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

function fooderPDF($pdf, $data)
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
}


// 1. OBTENCIÓN DEL ID DE LECTURA (Desde el botón de la tabla)
$lecturaID_enc = $_GET['lecturaid'] ?? '';
$lecturaID = decryption($lecturaID_enc);

if (empty($lecturaID)) {
  die("Error: ID de lectura no válido.");
}

// 2. CONSULTA DE LA LECTURA ACTUAL (La base de todo)
// Usamos LEFT JOIN en equipo por si la renta ya no lo tiene (renta cancelada)
$queryCurr = "SELECT le.*, re.*, co.*, cl.*, eq.*, mo.*, catR.*, catC.*,
              le.lectura_id as ID_LECTURA,
              le.lectura_equipo_id as lectura_equipo_id,
              le.lectura_reporte_id as lectura_reporte_id
              FROM Lecturas le
                INNER JOIN Rentas re ON le.lectura_renta_id = re.renta_id
                INNER JOIN Contratos co ON re.renta_contrato_id = co.contrato_id
                INNER JOIN Clientes cl ON co.contrato_cliente_id = cl.cliente_id
                INNER JOIN catRegimenFiscal catR ON cl.cliente_regFis_id = catR.regFis_id 
                INNER JOIN catCFDI catC ON cl.cliente_cfdi_id = catC.CFDI_id 
                LEFT JOIN Equipos eq ON re.renta_equipo_id = eq.equipo_id
                LEFT JOIN Modelos mo ON eq.equipo_modelo_id = mo.modelo_id
              WHERE le.lectura_id = '$lecturaID'";

$resCurr = consultaData($queryCurr);
if ($resCurr['numRows'] == 0) die("Lectura no encontrada.");
$data = $resCurr['dataFetch'][0];

// 3. BUSCAR LA LECTURA ANTERIOR AUTOMÁTICAMENTE
// Calculamos el mes y año anterior basados en la fecha de la lectura actual
$fecha_actual = $data['lectura_fecha'];
$data['curr_lectura_fecha'] = $data['lectura_fecha'];
$mesAnterior = date("m", strtotime($fecha_actual . " -1 month"));
$anioAnterior = date("Y", strtotime($fecha_actual . " -1 month"));
$renta_id = $data['lectura_renta_id'];

$queryPrev = "SELECT * FROM Lecturas 
              WHERE lectura_renta_id = '$renta_id' 
              AND MONTH(lectura_fecha) = '$mesAnterior' 
              AND YEAR(lectura_fecha) = '$anioAnterior' 
              LIMIT 1";
$resPrev = consultaData($queryPrev);
$dataPrev = ($resPrev['numRows'] > 0) ? $resPrev['dataFetch'][0] : null;

// 4. BUSCAR SI HUBO UN CAMBIO DE EQUIPO EN ESTE PERIODO
$dataCambio = null;
if ($dataPrev) {
  $queryCambio = "SELECT * FROM Cambios 
                    WHERE cambio_renta_id = '$renta_id' 
                    AND cambio_fecha BETWEEN '" . $dataPrev['lectura_fecha'] . "' AND '" . $data['lectura_fecha'] . "'";
  $resCambio = consultaData($queryCambio);
  if ($resCambio['numRows'] > 0) {
    $dataCambio = $resCambio['dataFetch'][0];
  }
}

/* ===================================== RENDERIZADO PDF ==================================== */

// HEADER (Datos del cliente y equipo actual de la lectura)
headerPDF($pdf, $data, "data", TRUE);

if ($dataCambio) {
  // --- LÓGICA DE CAMBIO DE EQUIPO ---
  $eqIng = consultaData("SELECT * FROM Equipos INNER JOIN Modelos ON equipo_modelo_id=modelo_id WHERE equipo_id=" . $dataCambio['cambio_equipoIng_id'])['dataFetch'][0];
  $eqRet = consultaData("SELECT * FROM Equipos INNER JOIN Modelos ON equipo_modelo_id=modelo_id WHERE equipo_id=" . $dataCambio['cambio_equipoRet_id'])['dataFetch'][0];

  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(190, 6, utf8_decode("PROCESO CON AJUSTE POR CAMBIO DE EQUIPO"), 1, 1, 'C', true);

  // Cálculos (Retirado)
  $sub_ret_esc = $dataCambio['cambio_Ret_esc'] - $dataPrev['lectura_esc'];
  $sub_ret_bn = $dataCambio['cambio_Ret_bn'] - $dataPrev['lectura_bn'];
  $sub_ret_col = $dataCambio['cambio_Ret_col'] - $dataPrev['lectura_col'];
  // Cálculos (Ingresado)
  $sub_ing_esc = $data['lectura_esc'] - $dataCambio['cambio_Ing_esc'];
  $sub_ing_bn = $data['lectura_bn'] - $dataCambio['cambio_Ing_bn'];
  $sub_ing_col = $data['lectura_col'] - $dataCambio['cambio_Ing_col'];

  $total_esc = $sub_ret_esc + $sub_ing_esc;
  $total_bn = $sub_ret_bn + $sub_ing_bn;
  $total_col = $sub_ret_col + $sub_ing_col;

  // Aquí dibujas las tablas que ya tenías para el equipo Retirado e Ingresado...
  $pdf->Cell(190, 6, "Total ESC Procesado: " . $total_esc, 0, 1);
  $pdf->Cell(190, 6, "Total B&N Procesado: " . $total_bn, 0, 1);
  $pdf->Cell(190, 6, "Total COL Procesado: " . $total_col, 0, 1);
} else {
  // --- LÓGICA LECTURA NORMAL ---
  $total_esc = ($dataPrev) ? ($data['lectura_esc'] - $dataPrev['lectura_esc']) : 0;
  $total_bn  = ($dataPrev) ? ($data['lectura_bn']  - $dataPrev['lectura_bn'])  : 0;
  $total_col = ($dataPrev) ? ($data['lectura_col'] - $dataPrev['lectura_col']) : 0;

  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(190, 6, utf8_decode("DETALLE DE LECTURAS MENSUALES"), 1, 1, 'C', true);

  $pdf->Cell(47, 7, 'CONCEPTO', 1, 0, 'C');
  $pdf->Cell(47, 7, 'ANTERIOR', 1, 0, 'C');
  $pdf->Cell(47, 7, 'ACTUAL', 1, 0, 'C');
  $pdf->Cell(49, 7, 'TOTAL', 1, 1, 'C');

  $pdf->SetFont('Arial', '', 11);
  $pdf->Cell(47, 7, 'ESC', 1, 0, 'C');
  $pdf->Cell(47, 7, ($dataPrev ? $dataPrev['lectura_esc'] : 'N/A'), 1, 0, 'C');
  $pdf->Cell(47, 7, $data['lectura_esc'], 1, 0, 'C');
  $pdf->Cell(49, 7, $total_esc, 1, 1, 'C');

  $pdf->Cell(47, 7, 'B&N', 1, 0, 'C');
  $pdf->Cell(47, 7, ($dataPrev ? $dataPrev['lectura_bn'] : 'N/A'), 1, 0, 'C');
  $pdf->Cell(47, 7, $data['lectura_bn'], 1, 0, 'C');
  $pdf->Cell(49, 7, $total_bn, 1, 1, 'C');

  $pdf->Cell(47, 7, 'COL', 1, 0, 'C');
  $pdf->Cell(47, 7, ($dataPrev ? $dataPrev['lectura_col'] : 'N/A'), 1, 0, 'C');
  $pdf->Cell(47, 7, $data['lectura_col'], 1, 0, 'C');
  $pdf->Cell(49, 7, $total_col, 1, 1, 'C');
}

// 5. EXCEDENTES Y TOTALES
$renta_costo = $data['renta_costo'];
$inc_esc = $data['renta_inc_esc'];
$inc_bn  = $data['renta_inc_bn'];
$inc_col = $data['renta_inc_col'];

$exc_esc = $data['renta_exc_esc'];
$exc_bn  = $data['renta_exc_bn'];
$exc_col = $data['renta_exc_col'];

$exc_total = ($total_esc > $inc_esc) ? ($total_esc - $inc_esc) * $exc_esc : 0;
$exc_total = $exc_total + ($total_bn > $inc_bn) ? ($total_bn - $inc_bn) * $exc_bn : 0;
$exc_total = $exc_total + ($total_col > $inc_col) ? ($total_col - $inc_col) * $exc_col : 0;

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(130);
$pdf->Cell(30, 6, 'SUBTOTAL EXC', 1, 0, 'R');
$pdf->Cell(30, 6, "$" . number_format($exc_total, 2), 1, 1, 'C');
$pdf->Cell(130);
$pdf->Cell(30, 6, 'RENTA BASE', 1, 0, 'R');
$pdf->Cell(30, 6, "$" . number_format($renta_costo, 2), 1, 1, 'C');
$pdf->Cell(130);
$pdf->Cell(30, 6, 'TOTAL', 1, 0, 'R', true);
$pdf->Cell(30, 6, "$" . number_format($exc_total + $renta_costo, 2), 1, 1, 'C', true);

// 6. EVIDENCIA FÍSICA (Imágenes JPG/PNG)
if (!empty($data['lectura_pdf'])) {
  $fld_actual = date("Y/n", strtotime($data['lectura_fecha']));
  $rutas = [
    SERVERDIR . "DocsCR/Lecturas/$fld_actual/Formatos/" . $data['lectura_pdf'],
    SERVERDIR . "DocsCR/Lecturas/$fld_actual/PE/" . $data['lectura_pdf']
  ];
  foreach ($rutas as $r) {
    if (file_exists($r) && !is_dir($r)) {
      $pdf->AddPage();
      $pdf->Image($r, 2, 10, 210, 290);
    }
  }
}

// 7. REPORTE DIGITAL (Historial Reportes - Body Correo)
if (!empty($data['lectura_reporte_id'])) {
  $repID = $data['lectura_reporte_id'];
  $sqlRep = "SELECT body_correo FROM historial_reportes WHERE id = '$repID'";
  $resRep = consultaData($sqlRep);
  if ($resRep['numRows'] > 0) {
    $pdf->AddPage();
    $pdf->SetFont('Courier', '', 9);
    $pdf->MultiCell(0, 4, utf8_decode($resRep['dataFetch'][0]['body_correo']));
  }
}

// 8. SALIDA
$pdf->Output('I', "Lectura_" . $data['renta_folio'] . ".pdf");
