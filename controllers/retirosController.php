<?php

// ============================ Controladores de Rentas ============================ //
function retiroAdd()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|INICIO|~~~ Comprobacion de Formulario ~~~|INICIO|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //


    $retiro_motivo = limpiarCadena($_POST['retiro_motivo_add']);
    if ($retiro_motivo == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario seleccionar tipo de retiro. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($retiro_motivo != "Cancelacion") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se reconoce el tipo de Retiro. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $renta_estado = "Cancelado";
    }

    $retiro_fecha = limpiarCadena($_POST['retiro_fecha_add']);
    if ($retiro_fecha == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario agregar la Fecha de Retiro. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $retiro_fecha)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) La fecha NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        list($retiro_anio, $retiro_mes, $retiro_dia) = explode("-", $retiro_fecha);
    }

    $retiro_renta_id = limpiarCadena($_POST['retiro_renta_id_add']);
    if ($retiro_renta_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario seleccionar una Renta. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $retiro_renta_id)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El dato ingresado para identificar la renta no tiene el formato correcto, recarga la pagina y comienza de nuevo. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $SQLrentas = "SELECT * FROM Rentas
                    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                    INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                    INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                    INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                    WHERE renta_id = '$retiro_renta_id'";
        $checkRentaID = consultaData($SQLrentas);
        if ($checkRentaID['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) La Renta seleccionada no existe. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $rentaData = $checkRentaID['dataFetch'][0];
            $retiro_equipo_id = $rentaData['renta_equipo_id'];
            $contrato_folio = $rentaData['contrato_folio'];
            $renta_contrato_id = $rentaData['renta_contrato_id'];
            $renta_folio = $rentaData['renta_folio'];
            $cliente_rs = $rentaData['cliente_rs'];
            $cliente_rfc = $rentaData['cliente_rfc'];
            $renta_depto = $rentaData['renta_depto'];
            $equipo_serie = $rentaData['equipo_serie'];

            $retiro_file = $rentaData['cliente_rs'] . " (" . $contrato_folio . "-" . $renta_folio . " - " . $renta_depto . " - " . $equipo_serie . ")";
        }
    }

    $retiro_esc = limpiarCadena($_POST['retiro_esc_add']);
    if ($retiro_esc == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario agregar contador de Escaneo. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $retiro_esc)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Escaneo). (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    $retiro_bn = limpiarCadena($_POST['retiro_bn_add']);
    if ($retiro_bn == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario agregar contador de B&N. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $retiro_bn)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de B&N). (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    $retiro_col = limpiarCadena($_POST['retiro_col_add']);
    if ($retiro_col == "") {
        $retiro_col = 0;
    } else if (verificarDatos("^[0-9]+$", $retiro_col)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Color). (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    $retiro_comm = limpiarCadena($_POST['retiro_comm_add']);
    if ($retiro_comm == "") {
        $retiro_comm = "Sin Comentarios de retiro";
    }

    $equipo_estado = limpiarCadena($_POST['equipo_estado_add']);
    if ($equipo_estado == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario seleccionar estado del Equipo retirado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($equipo_estado != "Espera" && $equipo_estado != "Reparacion") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se reconoce el tipo de Estado de retiro del equipo. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }


    // --------------------------------------------- Verificando Input File --------------------------------------------- //
    if ($_FILES['retiro_file_add']['name'] == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No ingresaste evidencia. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($_FILES['retiro_file_add']['type'] != "application/pdf") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El formato del archivo debe ser PDF. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $tempDir = $_FILES['retiro_file_add']['tmp_name'];
    }
    // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|FIN|~~ Comprobacion de Formulario ~~~|FIN|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    $saveDoc = saveDoc("retiroEqu", $tempDir, $retiro_file, $retiro_anio, $retiro_mes, $retiro_dia);
    if ($saveDoc['status']) {
        $retiro_file = $saveDoc['result'];

        $insertRetiro = "INSERT INTO Retiros (retiro_fecha, retiro_renta_id, retiro_motivo, retiro_equipo_id, retiro_esc, retiro_bn, retiro_col, retiro_comm, retiro_file) VALUES ('$retiro_fecha', '$retiro_renta_id', '$retiro_motivo', '$retiro_equipo_id', '$retiro_esc', '$retiro_bn', '$retiro_col', '$retiro_comm', '$retiro_file')";
        if (sentenciaData($insertRetiro)) {
            $updateRenta = "UPDATE Rentas SET
            renta_ffin = '$retiro_fecha',
            renta_estado = '$renta_estado',
            renta_stock_K = 0,
            renta_stock_M = 0,
            renta_stock_C = 0,
            renta_stock_Y = 0,
            renta_stock_R = 0,
            renta_equipo_id = NULL
            WHERE renta_id = '$retiro_renta_id'";
            if (sentenciaData($updateRenta)) {
                $checkStatus = "SELECT * FROM Rentas WHERE renta_estado = 'Activo' AND renta_contrato_id = $renta_contrato_id";
                if (consultaData($checkStatus)['numRows'] == 0) {
                    $updateContrato = "UPDATE Contratos SET contrato_estado = 'Cancelado' WHERE contrato_id = $renta_contrato_id";
                    if (!sentenciaData($updateContrato)) {
                        $alerta = [
                            'Alerta' => 'simple',
                            'Titulo' => 'Ocurrio un Error inesperado',
                            'Texto' => '(TT _ TT) No se pudo actualizar el estatus del contrato. (TT _ TT)',
                            'Tipo' => 'error'
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
                }
                $updateEquipo = "UPDATE Equipos SET equipo_estado = '$equipo_estado' WHERE equipo_id = '$retiro_equipo_id'";
                if (sentenciaData($updateEquipo)) {
                    $alerta = [
                        'Alerta' => 'recargar',
                        'Titulo' => 'Registro Completado',
                        'Texto' => '\(- _ -)/ El retiro de  ' . $cliente_rs . ' - ' . $renta_depto . ' (' . $contrato_folio . '-' . $renta_folio . '), se realizo correctamente. \(- _ -)/',
                        'Tipo' => 'success'
                    ];
                } else {
                    $alerta = [
                        'Alerta' => 'simple',
                        'Titulo' => 'Ocurrio un Error inesperado',
                        'Texto' => '(o _ O) No se pudo actualizar el estado del Equipo retirado, pero se agrego el registro de retiro correctamente. (o _ O)',
                        'Tipo' => 'error'
                    ];
                }
            } else {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(o _ O) No se pudo actualizar el estado de la renta, pero se agrego el registro de retiro correctamente. (o _ O)',
                    'Tipo' => 'error'
                ];
            }
        } else {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(o _ O) No se pudo realizar correctamente el registro de retiro. (o _ O)',
                'Tipo' => 'error'
            ];
        }
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) ' . $saveDoc['result'] . '. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}
