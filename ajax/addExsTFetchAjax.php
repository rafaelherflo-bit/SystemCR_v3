<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

// Verificacion de existencia de algun post reconocido //
if (isset($_POST['tonerR_fecha']) && isset($_POST['tonerR_toner_id']) && isset($_POST['tonerR_cant']) && isset($_POST['tonerR_comm'])) {

    $tonerR_fecha = $_POST['tonerR_fecha'];
    $tonerR_toner_id = $_POST['tonerR_toner_id'];
    $tonerR_cant = $_POST['tonerR_cant'];
    $tonerR_comm = $_POST['tonerR_comm'];

    // INICIO ---- Insertcion a Registro de Entrada
    $sqlRegEntTon = "INSERT INTO TonersRegistrosE (tonerR_fecha, tonerR_toner_id, tonerR_cant, tonerR_comm) VALUES ('$tonerR_fecha', '$tonerR_toner_id', '$tonerR_cant', '$tonerR_comm')";
    $Status = sentenciaData($sqlRegEntTon);
    if ($Status) {
        $Result = "Completado.";
    } else {
        $Result = "Error al insertar registro de Entrada.";
    }

    $data = [
        'Status' => $Status,
        'Result' => $Result
    ];
    echo json_encode($data);
} else {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . SERVERURL . 'login/');
    exit();
}
