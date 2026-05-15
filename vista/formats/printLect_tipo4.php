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
    $pdf->Cell(190, 1, '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
    $pdf->Cell(135, 6, 'PROCESO DE ' . $prevMonthLetter . ' A ' . $currMonthLetter . ' CON AJUSTE POR CAMBIO', 0, 0, 'C');
    $pdf->Cell(1, 6, '|', 0, 0, 'C');
    $pdf->Cell(55, 6, 'COMENTARIOS', 0, 1, 'C');
    $pdf->Cell(190, 0, '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(2);

    $queryEquIng = "SELECT * FROM Equipos
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE equipo_id = " . $_POST['cambio_equipoIng_id'];
    $dataEquIng = consultaData($queryEquIng)['dataFetch'][0];

    $queryEquRet = "SELECT * FROM Equipos
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE equipo_id = " . $_POST['cambio_equipoRet_id'];
    $dataEquRet = consultaData($queryEquRet)['dataFetch'][0];

    $prev_lectura_fecha = $_POST['prev_lectura_fecha'];
    $cambio_fecha = $_POST['cambio_fecha'];

    $prev_lectura_esc = $_POST['prev_lectura_esc'];
    $prev_lectura_bn = $_POST['prev_lectura_bn'];
    $prev_lectura_col = $_POST['prev_lectura_col'];

    $cambio_Ret_esc = $_POST['cambio_Ret_esc'];
    $cambio_Ret_bn = $_POST['cambio_Ret_bn'];
    $cambio_Ret_col = $_POST['cambio_Ret_col'];

    $cambio_Ing_esc = $_POST['cambio_Ing_esc'];
    $cambio_Ing_bn = $_POST['cambio_Ing_bn'];
    $cambio_Ing_col = $_POST['cambio_Ing_col'];

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

    if ($_POST["modelo_tipo"] == "Multicolor") {
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

    if ($_POST["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(5);
        $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ing_col, 1, 0, 'C');
        $pdf->Cell(31, 6, "", 1, 0, 'C');
        $pdf->Cell(31, 6, "", 1, 1, 'C');
    }

    $pdf->Cell(0, 2, '------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
}
/* ============================================================================================= */

/* ===================================== LLAMADO DEL FOODER ==================================== */
fooderPDF($pdf, $_POST, TRUE);
/* ============================================================================================= */

// ==================================================================================================================================================================== //
if (file_exists(SERVERDIR . "DocsCR/Lecturas/" . $prevYear . "/" . $prevMonthNumber . "/Formatos" . "/" . $_POST['prev_lectura_id'] . ".jpg")) {
    $pdf->AddPage();
    $imgDir_actual = SERVERDIR . "DocsCR/Lecturas/" . $prevYear . "/" . $prevMonthNumber . "/Formatos" . "/" . $_POST['prev_lectura_id'] . ".jpg";
    $pdf->Image($imgDir_actual, 2, 10, 210, 290, "JPEG");
}
if (file_exists(SERVERDIR . "DocsCR/Lecturas/" . $currYear . "/" . $prevMonthNumber . "/PE" . "/" . $_POST['prev_lectura_id'] . ".jpg")) {
    $pdf->AddPage();
    $imgDir_actual = SERVERDIR . "DocsCR/Lecturas/" . $currYear . "/" . $prevMonthNumber . "/PE" . "/" . $_POST['prev_lectura_id'] . ".jpg";
    $pdf->Image($imgDir_actual, 2, 10, 210, 290, "JPEG");
}

// ==================================================================================================================================================================== //


$output = $currMonthNumber . "-" . $currYear . " - " . $_POST["cliente_rs"] . " (" . $_POST["contrato_folio"] . "-" . $_POST["renta_folio"] . ") " . $_POST["renta_depto"] . " - Toma de lectura.pdf";
$pdf->Output('I', $output);
