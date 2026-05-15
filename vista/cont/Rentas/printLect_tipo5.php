<?php
define('orientacionPDF', 'Vertical');
define('fechaPDF', FALSE);
define('firmaPDF', FALSE);
define("factPDF", FALSE);

require_once $_SERVER["DOCUMENT_ROOT"] . '/vista/formats/printLect_header.php';

$reporte_id = $_POST['lectura_reporte_id'];

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
headerPDF($pdf, $_POST, "data", TRUE);
/* ============================================================================================= */

/* =================================== CAPTURA DE CONTADORES =================================== */
if (TRUE) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 1, '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
    $pdf->Cell(190, 6, 'PROCESO DE ' . strtoupper(dateFormat($_POST['prev_lectura_fecha'], "mes")) . " A " . strtoupper(dateFormat($_POST['curr_lectura_fecha'], "mes")) . " CON AJUSTE POR CAMBIO", 0, 1, 'C');
    $pdf->Cell(190, 0, '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(2);

    //--------------------------------------------------------------------------------

    $queryEquIng = "SELECT * FROM Equipos
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE equipo_id = " . $_POST['cambio_equipoIng_id'];
    $dataEquIng = consultaData($queryEquIng)['dataFetch'][0];

    $queryEquRet = "SELECT * FROM Equipos
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE equipo_id = " . $_POST['cambio_equipoRet_id'];
    $dataEquRet = consultaData($queryEquRet)['dataFetch'][0];

    //--------------------------------------------------------------------------------

    $cambio_fecha = $_POST['cambio_fecha'];
    $prev_lectura_fecha = $_POST['prev_lectura_fecha'];
    $curr_lectura_fecha = $_POST['curr_lectura_fecha'];

    $prev_lectura_esc = $_POST['prev_lectura_esc'];
    $prev_lectura_bn = $_POST['prev_lectura_bn'];
    $prev_lectura_col = $_POST['prev_lectura_col'];

    $curr_lectura_esc = $_POST['curr_lectura_esc'];
    $curr_lectura_bn = $_POST['curr_lectura_bn'];
    $curr_lectura_col = $_POST['curr_lectura_col'];

    $cambio_Ret_esc = $_POST['cambio_Ret_esc'];
    $cambio_Ret_bn = $_POST['cambio_Ret_bn'];
    $cambio_Ret_col = $_POST['cambio_Ret_col'];

    $cambio_Ing_esc = $_POST['cambio_Ing_esc'];
    $cambio_Ing_bn = $_POST['cambio_Ing_bn'];
    $cambio_Ing_col = $_POST['cambio_Ing_col'];

    $subtotal_lecturaRet_esc = $cambio_Ret_esc - $prev_lectura_esc;
    $subtotal_lecturaRet_bn = $cambio_Ret_bn - $prev_lectura_bn;
    $subtotal_lecturaRet_col = $cambio_Ret_col - $prev_lectura_col;

    $subtotal_lecturaIng_esc = $curr_lectura_esc - $cambio_Ing_esc;
    $subtotal_lecturaIng_bn = $curr_lectura_bn - $cambio_Ing_bn;
    $subtotal_lecturaIng_col = $curr_lectura_col - $cambio_Ing_col;

    $total_lectura_esc = $subtotal_lecturaIng_esc + $subtotal_lecturaRet_esc;
    $total_lectura_bn = $subtotal_lecturaIng_bn + $subtotal_lecturaRet_bn;
    $total_lectura_col = $subtotal_lecturaIng_col + $subtotal_lecturaRet_col;

    //--------------------------------------------------------------------------------

    $inc_esc = $_POST["renta_inc_esc"];
    $inc_bn = $_POST["renta_inc_bn"];
    $inc_col = $_POST["renta_inc_col"];

    $exc_esc = $_POST["renta_exc_esc"];
    $exc_bn = $_POST["renta_exc_bn"];
    $exc_col = $_POST["renta_exc_col"];

    // Operaciones para Total Excedente ESC
    if ($inc_esc == 0) {
        $resta_inc_esc = 0;
        $total_exc_esc = 0;
    } else {
        $resta_inc_esc = ($total_lectura_esc - $inc_esc <= 0) ? 0 : $total_lectura_esc - $inc_esc;
        $total_exc_esc = $resta_inc_esc * $exc_esc;
    }
    // Operaciones para Total Excedente BN
    if ($inc_bn == 0) {
        $resta_inc_bn = 0;
        $total_exc_bn = 0;
    } else {
        $resta_inc_bn = ($total_lectura_bn - $inc_bn <= 0) ? 0 : $total_lectura_bn - $inc_bn;
        $total_exc_bn = $resta_inc_bn * $exc_bn;
    }
    // Operaciones para Total Excedente COL
    if ($inc_col == 0) {
        $resta_inc_col = 0;
        $total_exc_col = 0;
    } else {
        $resta_inc_col = ($total_lectura_col - $inc_col <= 0) ? 0 : $total_lectura_col - $inc_col;
        $total_exc_col = $resta_inc_col * $exc_col;
    }

    $subTotal = $total_exc_esc + $total_exc_bn + $total_exc_col + $_POST["renta_costo"];

    //--------------------------------------------------------------------------------

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(135, 6, 'PROCESO DEL EQUIPO RETIRADO | ' . $dataEquRet['modelo_linea'] . " " . $dataEquRet['modelo_modelo'] . " - " . $dataEquRet['equipo_serie'], 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(20);
    $pdf->Cell(31, 6, '', 0, 0, 'C');
    $pdf->Cell(31, 6, $prev_lectura_fecha, 0, 0, 'C');
    $pdf->Cell(31, 6, $cambio_fecha, 0, 0, 'C');
    $pdf->Cell(31, 6, 'SUBTOTAL', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(20);
    $pdf->Cell(31, 6, 'ESCANEO', 1, 0, 'C');
    $pdf->Cell(31, 6, $prev_lectura_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $cambio_Ret_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaRet_esc, 1, 1, 'C');

    $pdf->Cell(20);
    $pdf->Cell(31, 6, 'B&N', 1, 0, 'C');
    $pdf->Cell(31, 6, $prev_lectura_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $cambio_Ret_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaRet_bn, 1, 1, 'C');

    if ($_POST["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(20);
        $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(31, 6, $prev_lectura_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ret_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaRet_col, 1, 1, 'C');
    }

    $pdf->Cell(135, 2, '-------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(135, 6, 'PROCESO DEL EQUIPO INGRESADO | ' . $dataEquIng['modelo_linea'] . " " . $dataEquIng['modelo_modelo'] . " - " . $dataEquIng['equipo_serie'], 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(42);
    $pdf->Cell(31, 6, '', 0, 0, 'C');
    $pdf->Cell(31, 6, $cambio_fecha, 0, 0, 'C');
    $pdf->Cell(31, 6, $curr_lectura_fecha, 0, 0, 'C');
    $pdf->Cell(31, 6, 'SUBTOTAL', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(42);
    $pdf->Cell(31, 6, 'ESCANEO', 1, 0, 'C');
    $pdf->Cell(31, 6, $cambio_Ing_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $curr_lectura_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaIng_esc, 1, 1, 'C');

    $pdf->Cell(42);
    $pdf->Cell(31, 6, 'B&N', 1, 0, 'C');
    $pdf->Cell(31, 6, $cambio_Ing_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $curr_lectura_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaIng_bn, 1, 1, 'C');

    if ($_POST["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(42);
        $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ing_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $curr_lectura_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaIng_col, 1, 1, 'C');
    }

    $pdf->Cell(135, 2, '-------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(135, 6, 'TOTAL PROCESADO POR LOS DOS EQUIPOS', 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(31);
    $pdf->Cell(31, 6, '', 0, 0, 'C');
    $pdf->Cell(31, 6, 'EQUIPO RET', 0, 0, 'C');
    $pdf->Cell(31, 6, 'EQUIPO ING', 0, 0, 'C');
    $pdf->Cell(31, 6, 'PROSC. TOTAL', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(31);
    $pdf->Cell(31, 6, 'ESCANEO', 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaRet_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaIng_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $total_lectura_esc, 1, 1, 'C');

    $pdf->Cell(31);
    $pdf->Cell(31, 6, 'B&N', 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaRet_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaIng_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $total_lectura_bn, 1, 1, 'C');

    if ($_POST["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(31);
        $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaRet_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaIng_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $total_lectura_col, 1, 1, 'C');
    }

    $pdf->Ln(1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 1, '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(1);

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
    $pdf->Cell(28, 6, $inc_esc, 1, 0, 'C');
    $pdf->Cell(28, 6, $resta_inc_esc, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $exc_esc, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $total_exc_esc, 1, 1, 'C');

    $pdf->Cell(28);
    $pdf->Cell(28, 6, 'B&N', 1, 0, 'C');
    $pdf->Cell(28, 6, $inc_bn, 1, 0, 'C');
    $pdf->Cell(28, 6, $resta_inc_bn, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $exc_bn, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $total_exc_bn, 1, 1, 'C');

    if ($_POST["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(28);
        $pdf->Cell(28, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(28, 6, $inc_col, 1, 0, 'C');
        $pdf->Cell(28, 6, $resta_inc_col, 1, 0, 'C');
        $pdf->Cell(28, 6, "$" . $exc_col, 1, 0, 'C');
        $pdf->Cell(28, 6, "$" . $total_exc_col, 1, 1, 'C');
    }

    $pdf->Cell(112);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(28, 6, 'MENSUALIDAD', 1, 0, 'C');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(28, 6, "$" . $_POST["renta_costo"], 1, 1, 'C');
    $pdf->Cell(112);
    $pdf->Cell(28, 6, 'TOTAL SIN IVA', 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $subTotal, 1, 1, 'C');
}

// ==================================================================================================================================================================== //
if (!empty($_POST['curr_lectura_pdf']) && file_exists(SERVERDIR . "DocsCR/Lecturas/" . $currYear . "/" . $currMonth . "/Formatos" . "/" . $_POST['curr_lectura_pdf'])) {
    $pdf->AddPage();
    $imgDir_actual = SERVERDIR . "DocsCR/Lecturas/" . $currYear . "/" . $currMonth . "/Formatos" . "/" . $_POST['curr_lectura_pdf'];
    $pdf->Image($imgDir_actual, 2, 10, 210, 290, "JPEG");
}
if (!empty($_POST['curr_lectura_pdf']) && file_exists(SERVERDIR . "DocsCR/Lecturas/" . $currYear . "/" . $currMonth . "/PE" . "/" . $_POST['curr_lectura_pdf'])) {
    $pdf->AddPage();
    $imgDir_actual = SERVERDIR . "DocsCR/Lecturas/" . $currYear . "/" . $currMonth . "/PE" . "/" . $_POST['curr_lectura_pdf'];
    $pdf->Image($imgDir_actual, 2, 10, 210, 290, "JPEG");
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

if (!empty($_POST['prev_lectura_pdf']) && file_exists(SERVERDIR . "DocsCR/Lecturas/" . $prevYear . "/" . $prev_mes . "/Formatos" . "/" . $_POST['prev_lectura_pdf'])) {
    $pdf->AddPage();
    $imgDir_actual = SERVERDIR . "DocsCR/Lecturas/" . $prevYear . "/" . $prev_mes . "/Formatos" . "/" . $_POST['prev_lectura_pdf'];
    $pdf->Image($imgDir_actual, 2, 10, 210, 290, "JPEG");
}
if (!empty($_POST['prev_lectura_pdf']) && file_exists(SERVERDIR . "DocsCR/Lecturas/" . $prevYear . "/" . $prev_mes . "/PE" . "/" . $_POST['prev_lectura_pdf'])) {
    $pdf->AddPage();
    $imgDir_actual = SERVERDIR . "DocsCR/Lecturas/" . $prevYear . "/" . $prev_mes . "/PE" . "/" . $_POST['prev_lectura_pdf'];
    $pdf->Image($imgDir_actual, 2, 10, 210, 290, "JPEG");
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

$output = $currMonth . "-" . $currYear . " - " . $_POST["cliente_rs"] . " (" . $_POST["contrato_folio"] . "-" . $_POST["renta_folio"] . ") " . $_POST["renta_depto"] . " - Toma de lectura.pdf";
$pdf->Output('I', $output);
