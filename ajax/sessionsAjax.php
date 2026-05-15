<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

if (isset($_POST['id']) && isset($_POST['usuario'])) {
    // --------------------------- Instancia al Controlador cerrar sesion --------------------------- //
    echo logoutSession();
} else {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . SERVERURL . 'Login/');
    exit();
}
