<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';
require_once '../controllers/tonersController.php';

// Verificacion de existencia de algun post reconocido //
if (isset($_POST['tonerRO_fecha']) && isset($_POST['tonerRO_toner_id']) && isset($_POST['tonerRO_cantidad']) && isset($_POST['tonerRO_comm']) && isset($_POST['tonerRO_tipo']) && isset($_POST['tonerRO_empleado']) && isset($_POST['tonerRO_identificador'])) {
    salidaToner();
} else {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . SERVERURL . 'login/');
    exit();
}
