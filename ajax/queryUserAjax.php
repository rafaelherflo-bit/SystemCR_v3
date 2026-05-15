<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

// Verificacion de existencia de algun post reconocido //
if (isset($_POST['usuario_id'])) {
    echo json_encode(consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . decryption($_POST['usuario_id']))['dataFetch'][0]);
} else {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . SERVERURL . 'login/');
    exit();
}
