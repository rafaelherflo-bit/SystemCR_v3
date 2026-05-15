<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

if (isset($_POST['status'])) {
    session_start();
    // --------------------------- Instancia al Controlador cerrar sesion --------------------------- //
    if ($_POST['status'] == 0) {
        $_SESSION['navbarStatus'] = "show";
        $_SESSION['navbarBtn'] = "<i class='far fa-window-close'> &nbsp; OCULTAR</i></i>";
        sentenciaData("UPDATE Usuarios SET usuario_navbarStatus = 1 WHERE usuario_id = " . $_SESSION['id']);
        $status = true;
    } else {
        $_SESSION['navbarStatus'] = "";
        $_SESSION['navbarBtn'] = "<i class='far fa-check-square'> &nbsp; MANTENER</i></i>";
        $status = false;
        sentenciaData("UPDATE Usuarios SET usuario_navbarStatus = 0 WHERE usuario_id = " . $_SESSION['id']);
    }
    $result = [
        'status' => $status,
    ];
    echo json_encode($result);
} else {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . SERVERURL . 'Login/');
    exit();
}
