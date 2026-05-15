<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
    echo forceoutSession();
    exit();
} else {
    $datos = consultaData($_POST['sqlLecturasRentas'])['dataFetch'];
    echo json_encode($datos);
}
