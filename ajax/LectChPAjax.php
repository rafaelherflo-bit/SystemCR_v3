<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

// Verificacion de existencia de algun post reconocido //
if (isset($_POST['tipo']) && isset($_POST['seccion']) && isset($_POST['valor0']) && isset($_POST['valor1']) && isset($_POST['valor2']) && isset($_POST['valor3'])) {
    $tipo = $_POST['tipo'];
    $seccion = $_POST['seccion'];
    $valor0 = $_POST['valor0'];
    $valor1 = $_POST['valor1'];
    $valor2 = $_POST['valor2'];
    $valor3 = $_POST['valor3'];

    if ($tipo == 0) {
        // Tipo 0 es de Consulta de Datos

        // Consulta para datos de Pagos Lecturas //
        if ($seccion == 'checkChPAjax') {
            $consulta = "SELECT * FROM LectChP
        WHERE LChP_renta_id = '" . decryption($valor0) . "'
        AND LChP_month = '" . $valor1 . "'
        AND LChP_year = '" . $valor2 . "'";
        }



        // Busqueda, validacion y recopilacion de datos //
        $datos = consultaData($consulta);
        if ($datos['numRows'] == 0) {
            $Estado = false;
            $Data = $datos['dataFetch'];
        }
        if ($datos['numRows'] > 0) {
            $Estado = true;
            $Data = $datos['dataFetch'];
        }
    } else if ($tipo == 1) {
        // Tipo 1 es de Insertcion, borrado o Actualizacion de Datos

    } else {
        // Si no hay tipo de dato reconocido, salta directo a error.
        $Estado = false;
        $Data = "Tipo de accion no reconocida";
    }

    // Lanzando array de datos en JSON a JacaScript //
    $data = [
        'Estado' => $Estado,
        'Data' => $Data
    ];
    echo json_encode($data);
} else {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . SERVERURL . 'Login/');
    exit();
}
