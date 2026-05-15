<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

// Verificacion de existencia de algun post reconocido //
if (isset($_POST['renta_id']) && isset($_POST['custom_mes']) && isset($_POST['custom_anio'])) {

    $renta_id = decryption($_POST['renta_id']);
    $currMonth = $_POST['custom_mes'];
    $prevMonth = $_POST['custom_mes'];
    $currYear = $_POST['custom_anio'];
    $prevYear = $_POST['custom_anio'];

    $prevMonth = $_POST['custom_mes'] - 1;
    if ($prevMonth == 0) {
        $prevMonth = 12;
        $prevYear = $currYear - 1;
        $newYear = TRUE;
    }

    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ Lecturas ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    $currDataLect = "SELECT * FROM Lecturas
        INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Equipos ON Lecturas.lectura_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE lectura_renta_id = $renta_id
        AND  MONTH(lectura_fecha) = " . $currMonth . "
        AND  YEAR(lectura_fecha) = " . $currYear;
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

    $rentaData = "SELECT * FROM Rentas
    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
    INNER JOIN catRegimenFiscal ON Clientes.cliente_regFis_id = catRegimenFiscal.regFis_id
    INNER JOIN catCFDI ON Clientes.cliente_cfdi_id = catCFDI.CFDI_id
    INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
    LEFT JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
    LEFT JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
    WHERE renta_id = $renta_id";
    $rentaData = consultaData($rentaData)['dataFetch'][0];

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

    /* ================================================================================= */

    /* ================================================================================= */

    if ($currDataLect['numRows'] == 0 && $prevDataLect['numRows'] == 0) {
        $Type = 0;
    }

    if ($currDataLect['numRows'] == 1 && $prevDataLect['numRows'] == 0) {
        $Type = 1;
        $currLectData = $currDataLect['dataFetch'][0];
        $currLectData['lectura_id'] = encryption($currLectData['lectura_id']);
    }

    if ($currDataLect['numRows'] == 0 && $prevDataLect['numRows'] == 1) {
        $Type = 2;
        $prevLectData = $prevDataLect['dataFetch'][0];
        $prevLectData['lectura_id'] = encryption($prevLectData['lectura_id']);
        $cambEquQRY = consultaData("SELECT * FROM Cambios WHERE cambio_renta_id = $renta_id AND cambio_fecha BETWEEN '" . $prevLectData['lectura_fecha'] . "' AND '" . date("Y-n-d") . "'");

        if ($cambEquQRY['numRows'] > 0) {
            $Type = 4;
            $adjuLectData = $cambEquQRY['dataFetch'][0];
        }
    }

    if ($currDataLect['numRows'] == 1 && $prevDataLect['numRows'] == 1) {
        $Type = 3;
        $currLectData = $currDataLect['dataFetch'][0];
        $currLectData['lectura_id'] = encryption($currLectData['lectura_id']);
        $prevLectData = $prevDataLect['dataFetch'][0];
        $prevLectData['lectura_id'] = encryption($prevLectData['lectura_id']);

        $cambEquQRY = consultaData("SELECT * FROM Cambios WHERE cambio_renta_id = $renta_id AND cambio_fecha BETWEEN '" . $prevLectData['lectura_fecha'] . "' AND '" . $currLectData['lectura_fecha'] . "'");
        if ($cambEquQRY['numRows'] > 0) {
            $Type = 5;
            $adjuLectData = $cambEquQRY['dataFetch'][0];
        }
    }

    $data = [
        'Type' => $Type,
        'rentaData' => $rentaData,
        'currLectData' => $currLectData,
        'prevLectData' => $prevLectData,
        'adjuLectData' => $adjuLectData
    ];
    echo json_encode($data);
} else {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . SERVERURL . 'login/');
    exit();
}
