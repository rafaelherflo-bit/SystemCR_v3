<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/vista/formats/printLect_header.php';

$currYear = $_GET['year'];
$prevYear = $_GET['year'];
$zone = $_GET['zone'];

$currMonth = $_GET['month'];
$prevMonth = $_GET['month'] - 1;
if ($prevMonth == 0) {
  $prevMonth = 12;
  $prevYear = $currYear - 1;
}

// ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~= CONSULTA DE TODAS LAS RENTAS ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~ //
$rentasData = "SELECT * FROM Rentas
    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
    INNER JOIN catRegimenFiscal ON Clientes.cliente_regFis_id = catRegimenFiscal.regFis_id
    INNER JOIN catCFDI ON Clientes.cliente_cfdi_id = catCFDI.CFDI_id
    INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
    INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
    INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
    WHERE renta_estado = 'Activo'
    AND renta_finicio <= '$currYear-$currMonth-1'
    AND renta_zona_id = $zone
    ORDER BY renta_finicio ASC";
$rentasData = consultaData($rentasData);
// ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~=~ //

foreach ($rentasData['dataFetch'] as $renta) {
  $renta_id = $renta['renta_id'];

  $currDataLect = "SELECT * FROM Lecturas
            INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
            INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
            INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
            INNER JOIN Equipos ON Lecturas.lectura_equipo_id = Equipos.equipo_id
            INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
            WHERE lectura_renta_id = $renta_id
            AND  MONTH(lectura_fecha) = $currMonth
            AND  YEAR(lectura_fecha) = $currYear";
  $currDataLect = consultaData($currDataLect);

  $prevDataLect = "SELECT * FROM Lecturas
            INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
            INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
            INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
            INNER JOIN Equipos ON Lecturas.lectura_equipo_id = Equipos.equipo_id
            INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
            WHERE lectura_renta_id = $renta_id
            AND  MONTH(lectura_fecha) = $prevMonth
            AND  YEAR(lectura_fecha) = $prevYear";
  $prevDataLect = consultaData($prevDataLect);

  /*
        Type    Descripcion
        0       Sin datos de lectura actual y anterior.
        1       Unicamente con lectura actual.
        2       Unicamente con lectura anterior.
        3       Lectura completa.
        4       Sin lectura actual con ajuste por cambio.
        5       Lectura completa con ajuste por cambio.
  */

  $currLectData = NULL;
  $prevLectData = NULL;
  $adjuLectData = NULL;

  /* ======================== INICIO DE VALIDACION POR TIPO DE LECTURA ========================= */
  if ($currDataLect['numRows'] == 0 && $prevDataLect['numRows'] == 0) {
    $Type = 0;
  }
  if ($currDataLect['numRows'] == 1 && $prevDataLect['numRows'] == 0) {
    $Type = 1;
    $currLectData = $currDataLect['dataFetch'][0];
  }

  if ($currDataLect['numRows'] == 0 && $prevDataLect['numRows'] == 1) {
    $Type = 2;
    $prevLectData = $prevDataLect['dataFetch'][0];
    $cambEquQRY = consultaData("SELECT * FROM Cambios WHERE cambio_renta_id = $renta_id AND cambio_fecha BETWEEN '" . $prevLectData['lectura_fecha'] . "' AND '" . date("Y-n-d") . "'");

    if ($cambEquQRY['numRows'] > 0) {
      $Type = 4;
      $adjuLectData = $cambEquQRY['dataFetch'][0];
    }
  }

  if ($currDataLect['numRows'] == 1 && $prevDataLect['numRows'] == 1) {
    $Type = 3;
    $currLectData = $currDataLect['dataFetch'][0];
    $prevLectData = $prevDataLect['dataFetch'][0];

    $cambEquSQL = "SELECT * FROM Cambios WHERE cambio_renta_id = $renta_id AND cambio_fecha BETWEEN '" . $prevLectData['lectura_fecha'] . "' AND '" . $currLectData['lectura_fecha'] . "'";

    $cambEquQRY = consultaData($cambEquSQL);
    if ($cambEquQRY['numRows'] > 0) {
      $Type = 5;
      $adjuLectData = $cambEquQRY['dataFetch'][0];
    }
  }
  /* =========================================================================================== */

  /*
    ================= CRECION DE VARIABLES PARA LOS MESES EN CAPTURA DE CONTADORES ================
    */
  $prevMonthLetNum = (empty($prevLectData['lectura_fecha'])) ? date("Y-n-d") : $prevLectData['lectura_fecha'];
  $prevMonthLetNum = strtoupper(dateFormat($prevMonthLetNum, "diaNmesLcorto"));
  $currMonthLetNum = (empty($currLectData['lectura_fecha'])) ? date("Y-n-d") : $currLectData['lectura_fecha'];
  $currMonthLetNum = strtoupper(dateFormat($currMonthLetNum, "diaNmesLcorto"));

  $prevMonthLetter = strtoupper(dateFormat(date('d') . "-" . $prevMonth . "-" . $prevYear, "mesL"));
  $currMonthLetter = strtoupper(dateFormat(date('d') . "-" . $currMonth . "-" . $currYear, "mesL"));
  /*
    ===============================================================================================
    */

  if (empty($currLectData['lectura_id'])) {
    /* =========================== LLAMADO DEL HEADER PARA CADA RENTA ========================== */
    headerPDF($pdf, $renta, TRUE, FALSE);
    /* ========================================================================================= */

    if ($Type == 0) {
      /* ============================== CONSULTA PARA BUSCAR REPORTES ============================ */
      if (TRUE) {
        $SQLreps = "SELECT * FROM Reportes WHERE reporte_estado = 0 AND reporte_renta_id = " . $renta_id;
        $QRYreps = consultaData($SQLreps);
        if ($QRYreps['numRows'] >= 1) {

          $pdf->Ln(3);
          $pdf->SetFont('Arial', 'B', 14);
          $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
          $pdf->Cell(190, 8, 'REPORTES ACTIVOS', 0, 1, 'C');
          $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
          $pdf->Ln(2);

          $countReps = 0;
          foreach ($QRYreps['dataFetch'] as $ROWrep) {
            $countReps++;

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 7, 'FECHA DE REPORTE', 1, 0, 'C', true);
            $pdf->Cell(70, 7, utf8_decode(strtoupper(dateFormat(explode(" ", $ROWrep['reporte_fecha'])[0], "full"))), 1, 0, 'C');
            $pdf->Cell(35, 7, 'REPORTADOR', 1, 0, 'C', true);
            $pdf->Cell(45, 7, utf8_decode(strtoupper($ROWrep['reporte_wmakes'])), 1, 1, 'C');
            $pdf->MultiCell(190, 6, utf8_decode($ROWrep['reporte_reporte']), 1, 'L');
            if ($QRYreps['numRows'] >= 2 && $countReps < $QRYreps['numRows']) {
              $pdf->Ln(2);
              $pdf->SetFont('Arial', '', 11);
              $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
              $pdf->Ln(2);
            }
          }
        }
        $pdf->Ln(2);
      }
      /* ========================================================================================= */

      /* ================================= CAPTURA DE CONTADORES ================================= */
      if (TRUE) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(190, 1, '-------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
        $pdf->Cell(190, 6, 'PROCESO', 0, 1, 'C');
        $pdf->Cell(190, 0, '-------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(31);
        $pdf->Cell(31, 6, ($prevYear == $currYear) ? $currYear : $prevYear . "|" . $currYear, 0, 0, 'C');
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

        if ($renta["modelo_tipo"] == "Multicolor") {
          $pdf->Cell(31);
          $pdf->Cell(31, 8, 'COLOR', 1, 0, 'C');
          $pdf->Cell(31, 8, "", 1, 0, 'C');
          $pdf->Cell(31, 8, "", 1, 0, 'C');
          $pdf->Cell(31, 8, "", 1, 1, 'C');
        }
      }
      /* ========================================================================================= */
    } else if ($Type == 2) {
      /* ============================== CONSULTA PARA BUSCAR REPORTES ============================ */
      if (TRUE) {
        $SQLreps = "SELECT * FROM Reportes WHERE reporte_estado = 0 AND reporte_renta_id = " . $renta_id;
        $QRYreps = consultaData($SQLreps);
        if ($QRYreps['numRows'] >= 1) {

          $pdf->Ln(3);
          $pdf->SetFont('Arial', 'B', 14);
          $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
          $pdf->Cell(190, 8, 'REPORTES ACTIVOS', 0, 1, 'C');
          $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
          $pdf->Ln(2);

          $countReps = 0;
          foreach ($QRYreps['dataFetch'] as $ROWrep) {
            $countReps++;

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 7, 'FECHA DE REPORTE', 1, 0, 'C', true);
            $pdf->Cell(70, 7, utf8_decode(strtoupper(dateFormat(explode(" ", $ROWrep['reporte_fecha'])[0], "full"))), 1, 0, 'C');
            $pdf->Cell(35, 7, 'REPORTADOR', 1, 0, 'C', true);
            $pdf->Cell(45, 7, utf8_decode(strtoupper($ROWrep['reporte_wmakes'])), 1, 1, 'C');
            $pdf->MultiCell(190, 6, utf8_decode($ROWrep['reporte_reporte']), 1, 'L');
            if ($QRYreps['numRows'] >= 2 && $countReps < $QRYreps['numRows']) {
              $pdf->Ln(2);
              $pdf->SetFont('Arial', '', 11);
              $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
              $pdf->Ln(2);
            }
          }
        }
        $pdf->Ln(2);
      }
      /* ========================================================================================= */

      /* ================================= CAPTURA DE CONTADORES ================================= */
      if (TRUE) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(190, 1, '-------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
        $pdf->Cell(190, 6, 'PROCESO', 0, 1, 'C');
        $pdf->Cell(190, 0, '-------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(31);
        $pdf->Cell(31, 6, ($prevYear == $currYear) ? $currYear : $prevYear . "|" . $currYear, 0, 0, 'C');
        $pdf->Cell(31, 6, $prevMonthLetNum, 0, 0, 'C');
        $pdf->Cell(31, 6, $currMonthLetter, 0, 0, 'C');
        $pdf->Cell(31, 6, 'PROSC. TOTAL', 1, 1, 'C');

        $prev_lectura_esc = $prevLectData['lectura_esc'];
        $prev_lectura_bn = $prevLectData['lectura_bn'];
        $prev_lectura_col = $prevLectData['lectura_col'];

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

        if ($renta["modelo_tipo"] == "Multicolor") {
          $pdf->Cell(31);
          $pdf->Cell(31, 8, 'COLOR', 1, 0, 'C');
          $pdf->Cell(31, 8, $prev_lectura_col, 1, 0, 'C');
          $pdf->Cell(31, 8, "", 1, 0, 'C');
          $pdf->Cell(31, 8, "", 1, 1, 'C');
        }

        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'I', 11);
        $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
      }
      /* ========================================================================================= */
    } else if ($Type == 4) {
      /* ================================= CAPTURA DE CONTADORES ================================= */
      if (TRUE) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(190, 1, '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
        $pdf->Cell(135, 6, 'PROCESO DE ' . $prevMonthLetter . ' A ' . $currMonthLetter . ' CON AJUSTE POR CAMBIO', 0, 0, 'C');
        $pdf->Cell(1, 6, '|', 0, 0, 'C');
        $pdf->Cell(55, 6, 'COMENTARIOS', 0, 1, 'C');
        $pdf->Cell(190, 0, '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

        $pdf->Ln(2);

        $queryEquIng = "SELECT * FROM Equipos
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE equipo_id = " . $adjuLectData['cambio_equipoIng_id'];
        $dataEquIng = consultaData($queryEquIng)['dataFetch'][0];

        $queryEquRet = "SELECT * FROM Equipos
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE equipo_id = " . $adjuLectData['cambio_equipoRet_id'];
        $dataEquRet = consultaData($queryEquRet)['dataFetch'][0];

        $prev_lectura_fecha = $prevLectData['lectura_fecha'];
        $cambio_fecha = $adjuLectData['cambio_fecha'];

        $prev_lectura_esc = $prevLectData['lectura_esc'];
        $prev_lectura_bn = $prevLectData['lectura_bn'];
        $prev_lectura_col = $prevLectData['lectura_col'];

        $cambio_Ret_esc = $adjuLectData['cambio_Ret_esc'];
        $cambio_Ret_bn = $adjuLectData['cambio_Ret_bn'];
        $cambio_Ret_col = $adjuLectData['cambio_Ret_col'];

        $cambio_Ing_esc = $adjuLectData['cambio_Ing_esc'];
        $cambio_Ing_bn = $adjuLectData['cambio_Ing_bn'];
        $cambio_Ing_col = $adjuLectData['cambio_Ing_col'];

        $subtotal_lecturaRet_esc = $cambio_Ret_esc - $prev_lectura_esc;
        $subtotal_lecturaRet_bn = $cambio_Ret_bn - $prev_lectura_bn;
        $subtotal_lecturaRet_col = $cambio_Ret_col - $prev_lectura_col;

        $subtotal_lecturaIng_esc = $cambio_Ing_esc - $prev_lectura_esc;
        $subtotal_lecturaIng_bn = $cambio_Ing_bn - $prev_lectura_bn;
        $subtotal_lecturaIng_col = $cambio_Ing_col - $prev_lectura_col;

        $total_lectura_esc = $subtotal_lecturaIng_esc - $subtotal_lecturaRet_esc;
        $total_lectura_bn = $subtotal_lecturaIng_bn - $subtotal_lecturaRet_bn;
        $total_lectura_col = $subtotal_lecturaIng_col - $subtotal_lecturaRet_col;

        $pdf->SetFont('Arial', 'I', 11);
        $pdf->Cell(135, 6, 'PROCESO DEL EQUIPO RETIRADO | ' . $dataEquRet['modelo_linea'] . " " . $dataEquRet['modelo_modelo'] . " - " . $dataEquRet['equipo_serie'], 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(5);
        $pdf->Cell(31, 6, '', 0, 0, 'C');
        $pdf->Cell(31, 6, $prev_lectura_fecha, 0, 0, 'C');
        $pdf->Cell(31, 6, $cambio_fecha, 0, 0, 'C');
        $pdf->Cell(31, 6, 'SUBTOTAL', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(5);
        $pdf->Cell(31, 6, 'ESCANEO', 1, 0, 'C');
        $pdf->Cell(31, 6, $prev_lectura_esc, 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ret_esc, 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaRet_esc, 1, 1, 'C');

        $pdf->Cell(5);
        $pdf->Cell(31, 6, 'B&N', 1, 0, 'C');
        $pdf->Cell(31, 6, $prev_lectura_bn, 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ret_bn, 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaRet_bn, 1, 1, 'C');

        if ($renta["modelo_tipo"] == "Multicolor") {
          $pdf->Cell(5);
          $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
          $pdf->Cell(31, 6, $prev_lectura_col, 1, 0, 'C');
          $pdf->Cell(31, 6, $cambio_Ret_col, 1, 0, 'C');
          $pdf->Cell(31, 6, $subtotal_lecturaRet_col, 1, 1, 'C');
        }

        $pdf->Cell(135, 2, '-------------------------------------------------------------------------------------------------------', 0, 1, 'C');

        $pdf->SetFont('Arial', 'I', 11);
        $pdf->Cell(135, 6, 'PROCESO DEL EQUIPO INGRESADO | ' . $dataEquIng['modelo_linea'] . " " . $dataEquIng['modelo_modelo'] . " - " . $dataEquIng['equipo_serie'], 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(5);
        $pdf->Cell(31, 6, '', 0, 0, 'C');
        $pdf->Cell(31, 6, $cambio_fecha, 0, 0, 'C');
        $pdf->Cell(31, 6, "", 0, 0, 'C');
        $pdf->Cell(31, 6, 'SUBTOTAL', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(5);
        $pdf->Cell(31, 6, 'ESCANEO', 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ing_esc, 1, 0, 'C');
        $pdf->Cell(31, 6, "", 1, 0, 'C');
        $pdf->Cell(31, 6, "", 1, 1, 'C');

        $pdf->Cell(5);
        $pdf->Cell(31, 6, 'B&N', 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ing_bn, 1, 0, 'C');
        $pdf->Cell(31, 6, "", 1, 0, 'C');
        $pdf->Cell(31, 6, "", 1, 1, 'C');

        if ($renta["modelo_tipo"] == "Multicolor") {
          $pdf->Cell(5);
          $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
          $pdf->Cell(31, 6, $cambio_Ing_col, 1, 0, 'C');
          $pdf->Cell(31, 6, "", 1, 0, 'C');
          $pdf->Cell(31, 6, "", 1, 1, 'C');
        }

        $pdf->Cell(0, 2, '------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
      }
      /* ========================================================================================= */
    }

    /* =========================== LLAMADO DEL FOODER PARA CADA RENTA ========================== */
    fooderPDF($pdf, $renta, TRUE);
    /* ========================================================================================= */
  }
}

$output = "Toma de lectura.pdf";
$pdf->Output('I', $output);
