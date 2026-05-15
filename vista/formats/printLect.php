<?php
define('orientacionPDF', 'Vertical');
if ($_GET['tipo'] == "0") {
    define('fechaPDF', TRUE);
    define('firmaPDF', TRUE);
} else {
    define('fechaPDF', FALSE);
    define('firmaPDF', FALSE);
}

require_once $_SERVER["DOCUMENT_ROOT"] . '/vista/formats/headerFPDF.php';


$renta_id = decryption($_GET['renta_id']);
$SQLrenta = "SELECT * FROM Rentas
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE renta_id = '$renta_id'";
$rentaData = consultaData($SQLrenta);
$rentaData = $rentaData['dataFetch'][0];

if ($_GET['tipo'] != '0') {
    $lectura_id_Actual = $_GET['lecturaActual'];
    $SQL_lectura_actual = "SELECT * FROM Lecturas
            INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
            INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
            INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
            INNER JOIN Equipos ON Lecturas.lectura_equipo_id = Equipos.equipo_id
            INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
            WHERE lectura_id = '$lectura_id_Actual'";
    $lecturaData_actual = consultaData($SQL_lectura_actual);
    $lecturaData_actual = $lecturaData_actual['dataFetch'][0];
    list($anio, $idmes_actual, $dia) = explode('-', $lecturaData_actual['lectura_fecha']);
} else {
    $idmes_actual = $_GET['custom_mes'];
    $anio = $_GET['custom_anio'];
    $dia = date('d');
}

// =========================================================================================================================== //
$idmes_anterior = $idmes_actual - 1;
$anio1 = $anio;
if ($idmes_anterior == 0) {
    $idmes_anterior = 12;
}
$mes_actual = strtoupper(dateFormat(date('d') . "-" . $idmes_actual . "-" . date('Y'), "mes"));
$mes_anterior = strtoupper(dateFormat(date('d') . "-" . $idmes_anterior . "-" . date('Y'), "mes"));
// =========================================================================================================================== //

if ($_GET['tipo'] != "0") {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(50, 6, 'FECHA DE LECTURA', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 6, utf8_decode(strtoupper(dateFormat($dia . "-" . $idmes_actual . "-" . $anio, 'completa'))), 0, 1, 'C');
}

basicinfo($pdf, "HOJA DE LECTURA", "DATOS DEL CONTRATO (" . $rentaData["contrato_folio"] . "-" . $rentaData["renta_folio"] . ") ", utf8_decode($rentaData["cliente_rs"]), utf8_decode($rentaData["cliente_rfc"]), utf8_decode($rentaData["renta_depto"]), $rentaData["zona_nombre"], $rentaData["renta_contacto"], $rentaData["renta_telefono"], $rentaData["modelo_linea"] . " " . $rentaData["modelo_modelo"] . " (" . $rentaData['modelo_toner'] . ")", $rentaData["equipo_codigo"] . " (" . $rentaData["equipo_serie"] . ")");

// ==================================================================================================================================================================== //

$renta_costo = $rentaData['renta_costo'];

$renta_inc_esc = $rentaData['renta_inc_esc'];
$renta_inc_bn = $rentaData['renta_inc_bn'];
$renta_inc_col = $rentaData['renta_inc_col'];

$renta_exc_esc = $rentaData['renta_exc_esc'];
$renta_exc_bn = $rentaData['renta_exc_bn'];
$renta_exc_col = $rentaData['renta_exc_col'];

if ($_GET['tipo'] != "0") {
    $actual_lectura_esc = $lecturaData_actual['lectura_esc'];
    $actual_lectura_bn = $lecturaData_actual['lectura_bn'];
    $actual_lectura_col = $lecturaData_actual['lectura_col'];
} else {
    $actual_lectura_esc = 0;
    $actual_lectura_bn = 0;
    $actual_lectura_col = 0;
}

// ==================================================================================================================================================================== //


$pdf->Ln(2);

if ($_GET['tipo'] == "1" || $_GET['tipo'] == "2" || $_GET['tipo'] == "0") {

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
    $pdf->Cell(190, 6, 'PROCESO DE ' . $mes_anterior . ' A ' . $mes_actual, 0, 1, 'C');
    $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(3);

    if ($_GET['tipo'] == "1") {
        $lectura_id_Anterior = $_GET['lecturaAnterior'];
        $SQL_lectura_anterior = "SELECT * FROM Lecturas
            INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
            INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
            INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
            INNER JOIN Equipos ON Lecturas.lectura_equipo_id = Equipos.equipo_id
            INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
            WHERE lectura_id = '$lectura_id_Anterior'";
        $lecturaData_anterior = consultaData($SQL_lectura_anterior);
        $lecturaData_anterior = $lecturaData_anterior['dataFetch'][0];

        $anterior_lectura_esc = $lecturaData_anterior['lectura_esc'];
        $anterior_lectura_bn = $lecturaData_anterior['lectura_bn'];
        $anterior_lectura_col = $lecturaData_anterior['lectura_col'];
        list($lectura_anio_anterior, $lectura_mes_anterior, $lectura_dia_anterior) = explode("-", $lecturaData_anterior['lectura_fecha']);
    } else if ($_GET['tipo'] == "2") {

        $anterior_lectura_esc = 0;
        $anterior_lectura_bn = 0;
        $anterior_lectura_col = 0;
    } else if ($_GET['tipo'] == "0") {

        $SQL_lectura_anterior = "SELECT * FROM Lecturas
            WHERE lectura_renta_id = $renta_id
            AND MONTH(lectura_fecha) = $idmes_anterior
            AND YEAR(lectura_fecha) = " . $anio1;
        $lecturaData_anterior = consultaData($SQL_lectura_anterior);
        if ($lecturaData_anterior['numRows'] == 0) {
            $anterior_lectura_esc = 0;
            $anterior_lectura_bn = 0;
            $anterior_lectura_col = 0;
        } else {
            $lecturaData_anterior = $lecturaData_anterior['dataFetch'][0];

            $anterior_lectura_esc = $lecturaData_anterior['lectura_esc'];
            $anterior_lectura_bn = $lecturaData_anterior['lectura_bn'];
            $anterior_lectura_col = $lecturaData_anterior['lectura_col'];
            list($lectura_anio_anterior, $lectura_mes_anterior, $lectura_dia_anterior) = explode("-", $lecturaData_anterior['lectura_fecha']);
        }
    }

    $total_lectura_esc = $actual_lectura_esc - $anterior_lectura_esc;
    $total_lectura_bn = $actual_lectura_bn - $anterior_lectura_bn;
    $total_lectura_col = $actual_lectura_col - $anterior_lectura_col;

    $resta_inc_esc = $total_lectura_esc - $renta_inc_esc;
    if ($resta_inc_esc < 0) {
        $resta_inc_esc = 0;
    }
    $total_exc_esc = $resta_inc_esc * $renta_exc_esc;

    $resta_inc_bn = $total_lectura_bn - $renta_inc_bn;
    if ($resta_inc_bn < 0) {
        $resta_inc_bn = 0;
    }
    $total_exc_bn = $resta_inc_bn * $renta_exc_bn;

    $resta_inc_col = $total_lectura_col - $renta_inc_col;
    if ($resta_inc_col < 0) {
        $resta_inc_col = 0;
    }
    $total_exc_col = $resta_inc_col * $renta_exc_col;

    $subTotal = $total_exc_esc + $total_exc_bn + $total_exc_col + $renta_costo;

    if ($_GET['tipo'] == "0") {
        $actual_lectura_esc = "";
        $actual_lectura_bn = "";
        $actual_lectura_col = "";
        $total_lectura_esc = "";
        $total_lectura_bn = "";
        $total_lectura_col = "";
    }

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(31);
    $pdf->Cell(31, 6, $anio, 0, 0, 'C');
    $pdf->Cell(31, 6, $mes_anterior, 0, 0, 'C');
    $pdf->Cell(31, 6, $mes_actual, 0, 0, 'C');
    $pdf->Cell(31, 6, 'PROSC. TOTAL', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 13);
    $pdf->Cell(31);
    $pdf->Cell(31, 8, 'ESCANEO', 1, 0, 'C');
    $pdf->Cell(31, 8, $anterior_lectura_esc, 1, 0, 'C');
    $pdf->Cell(31, 8, $actual_lectura_esc, 1, 0, 'C');
    $pdf->Cell(31, 8, $total_lectura_esc, 1, 1, 'C');

    $pdf->Cell(31);
    $pdf->Cell(31, 8, 'B&N', 1, 0, 'C');
    $pdf->Cell(31, 8, $anterior_lectura_bn, 1, 0, 'C');
    $pdf->Cell(31, 8, $actual_lectura_bn, 1, 0, 'C');
    $pdf->Cell(31, 8, $total_lectura_bn, 1, 1, 'C');

    if ($rentaData["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(31);
        $pdf->Cell(31, 8, 'COLOR', 1, 0, 'C');
        $pdf->Cell(31, 8, $anterior_lectura_col, 1, 0, 'C');
        $pdf->Cell(31, 8, $actual_lectura_col, 1, 0, 'C');
        $pdf->Cell(31, 8, $total_lectura_col, 1, 1, 'C');
    }

    /* =================================================================================================================================
    =============================== En esta seccion tenemos que consultar los datos del cambio realizado ===============================
    ================================================================================================================================= */
} else {

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
    $pdf->Cell(190, 6, 'PROCESO DE ' . $mes_anterior . ' A ' . $mes_actual . ' CON AJUSTE POR CAMBIO DE EQUIPO', 0, 1, 'C');
    $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(2);
    $lectura_id_Anterior = $_GET['lecturaAnterior'];
    $SQL_lectura_anterior = "SELECT * FROM Lecturas
        INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Equipos ON Lecturas.lectura_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE lectura_id = '$lectura_id_Anterior'";
    $lecturaData_anterior = consultaData($SQL_lectura_anterior);
    $lecturaData_anterior = $lecturaData_anterior['dataFetch'][0];

    $anterior_lectura_esc = $lecturaData_anterior['lectura_esc'];
    $anterior_lectura_bn = $lecturaData_anterior['lectura_bn'];
    $anterior_lectura_col = $lecturaData_anterior['lectura_col'];

    $actual_lectura_fecha = $lecturaData_actual['lectura_fecha'];
    $anterior_lectura_fecha = $lecturaData_anterior['lectura_fecha'];

    list($lectura_anio_anterior, $lectura_mes_anterior, $lectura_dia_anterior) = explode("-", $lecturaData_anterior['lectura_fecha']);

    $equipoIng_id = $lecturaData_actual['lectura_equipo_id'];
    $equipoRet_id = $lecturaData_anterior['lectura_equipo_id'];

    $equipoIng = $lecturaData_actual['modelo_linea'] . " " . $lecturaData_actual['modelo_modelo'] . " | " . $lecturaData_actual['equipo_codigo'] . " - " . $lecturaData_actual['equipo_serie'];
    $equipoRet = $lecturaData_anterior['modelo_linea'] . " " . $lecturaData_anterior['modelo_modelo'] . " | " . $lecturaData_anterior['equipo_codigo'] . " - " . $lecturaData_anterior['equipo_serie'];

    $checkCambioSQL = "SELECT * FROM Cambios
                    WHERE cambio_renta_id = '$renta_id'
                    AND cambio_equipoRet_id = '$equipoRet_id'
                    AND cambio_equipoIng_id = '$equipoIng_id'";
    $checkCambio = consultaData($checkCambioSQL);
    $checkCambio = $checkCambio['dataFetch'][0];

    $cambio_fecha = $checkCambio['cambio_fecha'];

    $cambio_Ret_esc = $checkCambio['cambio_Ret_esc'];
    $cambio_Ret_bn = $checkCambio['cambio_Ret_bn'];
    $cambio_Ret_col = $checkCambio['cambio_Ret_col'];

    $cambio_Ing_esc = $checkCambio['cambio_Ing_esc'];
    $cambio_Ing_bn = $checkCambio['cambio_Ing_bn'];
    $cambio_Ing_col = $checkCambio['cambio_Ing_col'];

    $subtotal_lecturaRet_esc = $cambio_Ret_esc - $anterior_lectura_esc;
    $subtotal_lecturaRet_bn = $cambio_Ret_bn - $anterior_lectura_bn;
    $subtotal_lecturaRet_col = $cambio_Ret_col - $anterior_lectura_col;

    $subtotal_lecturaIng_esc = $actual_lectura_esc - $cambio_Ing_esc;
    $subtotal_lecturaIng_bn = $actual_lectura_bn - $cambio_Ing_bn;
    $subtotal_lecturaIng_col = $actual_lectura_col - $cambio_Ing_col;

    $total_proceso_esc = $subtotal_lecturaIng_esc - $subtotal_lecturaRet_esc;
    $total_proceso_bn = $subtotal_lecturaIng_bn - $subtotal_lecturaRet_bn;
    $total_proceso_col = $subtotal_lecturaIng_col - $subtotal_lecturaRet_col;

    //--------------------------------------------------------------------------------
    $resta_inc_esc = $total_proceso_esc - $renta_inc_esc;
    if ($resta_inc_esc < 0) {
        $resta_inc_esc = 0;
    }
    $total_exc_esc = $resta_inc_esc * $renta_exc_esc;

    $resta_inc_bn = $total_proceso_bn - $renta_inc_bn;
    if ($resta_inc_bn < 0) {
        $resta_inc_bn = 0;
    }
    $total_exc_bn = $resta_inc_bn * $renta_exc_bn;

    $resta_inc_col = $total_proceso_col - $renta_inc_col;
    if ($resta_inc_col < 0) {
        $resta_inc_col = 0;
    }
    $total_exc_col = $resta_inc_col * $renta_exc_col;

    //--------------------------------------------------------------------------------

    $subTotal = $total_exc_esc + $total_exc_bn + $total_exc_col + $renta_costo;

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(190, 6, 'PROCESO DEL EQUIPO RETIRADO - ' . $equipoRet, 0, 1, 'C');
    $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(20);
    $pdf->Cell(31, 6, '', 0, 0, 'C');
    $pdf->Cell(31, 6, $anterior_lectura_fecha . ' -', 0, 0, 'C');
    $pdf->Cell(31, 6, '- ' . $cambio_fecha, 0, 0, 'C');
    $pdf->Cell(31, 6, 'SUBTOTAL', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(20);
    $pdf->Cell(31, 6, 'ESCANEO', 1, 0, 'C');
    $pdf->Cell(31, 6, $anterior_lectura_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $cambio_Ret_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaRet_esc, 1, 1, 'C');

    $pdf->Cell(20);
    $pdf->Cell(31, 6, 'B&N', 1, 0, 'C');
    $pdf->Cell(31, 6, $anterior_lectura_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $cambio_Ret_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaRet_bn, 1, 1, 'C');

    if ($rentaData["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(20);
        $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(31, 6, $anterior_lectura_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ret_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaRet_col, 1, 1, 'C');
    }

    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(190, 6, 'PROCESO DEL EQUIPO INGRESADO - ' . $equipoIng, 0, 1, 'C');
    $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(42);
    $pdf->Cell(31, 6, '', 0, 0, 'C');
    $pdf->Cell(31, 6, $cambio_fecha . ' -', 0, 0, 'C');
    $pdf->Cell(31, 6, '- ' . $actual_lectura_fecha, 0, 0, 'C');
    $pdf->Cell(31, 6, 'SUBTOTAL', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(42);
    $pdf->Cell(31, 6, 'ESCANEO', 1, 0, 'C');
    $pdf->Cell(31, 6, $cambio_Ing_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $actual_lectura_esc, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaIng_esc, 1, 1, 'C');

    $pdf->Cell(42);
    $pdf->Cell(31, 6, 'B&N', 1, 0, 'C');
    $pdf->Cell(31, 6, $cambio_Ing_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $actual_lectura_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaIng_bn, 1, 1, 'C');

    if ($rentaData["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(42);
        $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(31, 6, $cambio_Ing_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $actual_lectura_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaIng_col, 1, 1, 'C');
    }

    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(190, 6, 'TOTAL PROCESADO POR LOS DOS EQUIPOS', 0, 1, 'C');
    $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(2);

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
    $pdf->Cell(31, 6, $total_proceso_esc, 1, 1, 'C');

    $pdf->Cell(31);
    $pdf->Cell(31, 6, 'B&N', 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaRet_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $subtotal_lecturaIng_bn, 1, 0, 'C');
    $pdf->Cell(31, 6, $total_proceso_bn, 1, 1, 'C');

    if ($rentaData["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(31);
        $pdf->Cell(31, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaRet_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $subtotal_lecturaIng_col, 1, 0, 'C');
        $pdf->Cell(31, 6, $total_proceso_col, 1, 1, 'C');
    }
}

if ($_GET['tipo'] != "0") {
    $pdf->Ln(3);

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(5);

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
    $pdf->Cell(28, 6, $renta_inc_esc, 1, 0, 'C');
    $pdf->Cell(28, 6, $resta_inc_esc, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $renta_exc_esc, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $total_exc_esc, 1, 1, 'C');

    $pdf->Cell(28);
    $pdf->Cell(28, 6, 'B&N', 1, 0, 'C');
    $pdf->Cell(28, 6, $renta_inc_bn, 1, 0, 'C');
    $pdf->Cell(28, 6, $resta_inc_bn, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $renta_exc_bn, 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $total_exc_bn, 1, 1, 'C');

    if ($rentaData["modelo_tipo"] == "Multicolor") {
        $pdf->Cell(28);
        $pdf->Cell(28, 6, 'COLOR', 1, 0, 'C');
        $pdf->Cell(28, 6, $renta_inc_col, 1, 0, 'C');
        $pdf->Cell(28, 6, $resta_inc_col, 1, 0, 'C');
        $pdf->Cell(28, 6, "$" . $renta_exc_col, 1, 0, 'C');
        $pdf->Cell(28, 6, "$" . $total_exc_col, 1, 1, 'C');
    }

    $pdf->Cell(112);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(28, 6, 'MENSUALIDAD', 1, 0, 'C');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(28, 6, "$" . $renta_costo, 1, 1, 'C');
    $pdf->Cell(112);
    $pdf->Cell(28, 6, 'TOTAL SIN IVA', 1, 0, 'C');
    $pdf->Cell(28, 6, "$" . $subTotal, 1, 1, 'C');

    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    // ==================================================================================================================================================================== //

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(190, 5,  utf8_decode(strtoupper(dateFormat($dia . "-" . $idmes_actual . "-" . $anio, 'completa'))), 0, 1, 'C');
    $lectura_mes_actual = $idmes_actual;
    $imgDir_actual = SERVERDIR . "DocsCR/Lecturas/" . $anio . "/" . $lectura_mes_actual . "/PE" . "/" . $lecturaData_actual['lectura_id'] . ".jpg";
    $pdf->Image($imgDir_actual, 10, 40, 190, 0, "JPEG");

    // ==================================================================================================================================================================== //
}

if ($_GET['tipo'] == "1" || $_GET['tipo'] == "3") {
    // ==================================================================================================================================================================== //

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(190, 5,  utf8_decode(strtoupper(dateFormat($lecturaData_anterior['lectura_fecha'], 'completa'))), 0, 1, 'C');
    $imgDir_anterior = SERVERDIR . "DocsCR/Lecturas/" . $lectura_anio_anterior . "/" . $lectura_mes_anterior . "/PE" . "/" . $lecturaData_anterior['lectura_id'] . ".jpg";
    $pdf->Image($imgDir_anterior, 10, 40, 190, 0, "JPEG");

    // ==================================================================================================================================================================== //
}

if ($_GET['tipo'] == "0") {

    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
    $pdf->Cell(95, 6, 'COMENTARIOS', 0, 0, 'C');
    $pdf->Cell(95, 6, 'ABASTECIMIENTO', 0, 1, 'C');
    $pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    $pdf->Ln(5); // Salto de linea.

    // ABASTECIMIENTO
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(95);
    $pdf->Cell(95, 7, "CANTIDAD EN EQUIPO", 0, 1, 'C');

    // ABASTECIMIENTO
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(95);
    if ($rentaData['modelo_tipo'] == "Multicolor") {
        $pdf->Cell(19, 7, '%K', 1, 0, 'R');
        $pdf->Cell(19, 7, '%M', 1, 0, 'R');
        $pdf->Cell(19, 7, '%C', 1, 0, 'R');
        $pdf->Cell(19, 7, '%Y', 1, 0, 'R');
        $pdf->Cell(19, 7, '%R', 1, 1, 'R');
    } else {
        $pdf->Cell(22);
        if ($rentaData['modelo_modelo'] != 'M2040dn/L' && $rentaData['modelo_modelo'] != 'M2035dn/L' && $rentaData['modelo_modelo'] != 'M5521cdn' && $rentaData['modelo_modelo'] != 'M5526cdw' && $rentaData['modelo_modelo'] != 'M5526cdn') {
            $pdf->Cell(25, 7, '%K', 1, 0, 'R');
            $pdf->Cell(25, 7, '%R', 1, 0, 'R');
            $pdf->Cell(23, 7, '', 0, 1, 'R');
        } else {
            $pdf->Cell(11, 7, '', 0, 0, 'R');
            $pdf->Cell(30, 7, '%K', 1, 0, 'R');
            $pdf->Cell(43, 7, '', 0, 1, 'R');
        }
    }

    // ABASTECIMIENTO
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(95);
    $pdf->Cell(95, 7, "RESERVA EN STOCK", 0, 1, 'C');

    // ABASTECIMIENTO
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(95);
    if ($rentaData['modelo_tipo'] == "Multicolor") {
        $pdf->Cell(19, 7, 'K', 1, 0, 'R');
        $pdf->Cell(19, 7, 'M', 1, 0, 'R');
        $pdf->Cell(19, 7, 'C', 1, 0, 'R');
        $pdf->Cell(19, 7, 'Y', 1, 0, 'R');
        $pdf->Cell(19, 7, 'R', 1, 1, 'R');
    } else {
        $pdf->Cell(22);
        if ($rentaData['modelo_modelo'] != 'M2040dn/L' && $rentaData['modelo_modelo'] != 'M2035dn/L' && $rentaData['modelo_modelo'] != 'M5521cdn' && $rentaData['modelo_modelo'] != 'M5526cdw' && $rentaData['modelo_modelo'] != 'M5526cdn') {
            $pdf->Cell(25, 7, 'K', 1, 0, 'R');
            $pdf->Cell(25, 7, 'R', 1, 0, 'R');
            $pdf->Cell(23, 7, '', 0, 1, 'R');
        } else {
            $pdf->Cell(11, 7, '', 0, 0, 'R');
            $pdf->Cell(30, 7, 'K', 1, 0, 'R');
            $pdf->Cell(43, 7, '', 0, 1, 'R');
        }
    }
}

$output = $dia . "-" . $idmes_actual . "-" . $anio . " - " . $rentaData["cliente_rs"] . " (" . $rentaData["contrato_folio"] . "" . $rentaData["renta_folio"] . ") - Toma de lectura.pdf";
$pdf->Output('I', $output);
