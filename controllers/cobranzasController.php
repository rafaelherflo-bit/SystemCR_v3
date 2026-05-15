<?php

// ============================ Controladores de Cobranzas ============================ //

/* --------------------- Funcion Guardar Archivos PDF --------------------- */
function saveFact($temp_cobM_archivo, $nameDoc, $cobM_anio, $cobM_mes, $cobM_dia)
{

    // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
    $docDir = SERVERDIR . 'DocsCR/Facturas/';
    // +======+ Verificar Carpeta Raiz +======+ //
    if (!file_exists($docDir)) {
        mkdir($docDir, 0755, true);
        $docDir .=  $cobM_anio . '/';
    } else {
        $docDir .=  $cobM_anio . '/';
    }
    // +======+ Verificar Carpeta Anio +======+ //
    if (!file_exists($docDir)) {
        mkdir($docDir, 0755, true);
        $docDir .= $cobM_mes . '/';
    } else {
        $docDir .= $cobM_mes . '/';
    }
    // +======+ Verificar Carpeta Mes +======+ //
    if (!file_exists($docDir)) {
        mkdir($docDir, 0755, true);
    }

    $archivo = $cobM_dia . '-' . $cobM_mes . '-' . $cobM_anio . ' - ' . $nameDoc . '.pdf';

    // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
    if (file_exists($docDir . $archivo)) {
        $result = [
            'status' => false,
            'result' => 'El archivo, ya existe'
        ];
    }
    if (move_uploaded_file($temp_cobM_archivo, $docDir . $archivo)) {
        $result = [
            'status' => true,
            'result' => $archivo
        ];
    } else {
        $result = [
            'status' => false,
            'result' => 'No se pudo guardar el PDF.'
        ];
    }
    return $result;
} // Fin del la Funcion

/*
            ESTATUS DE COBRANZAS
            0 ------------ Cancelado
            1 ------------ Activo
            2 ------------ Pagado
*/

function iniciarCobranza()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $cobM_fecha = limpiarCadena($_POST['cobM_fecha_add']);
    $cobM_cliente_id = limpiarCadena(decryption($_POST['cobM_cliente_id_add']));
    $cobM_comm = limpiarCadena($_POST['cobM_comm_add']);

    if ($cobM_fecha == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("\d{4}-\d{2}-\d{2}", $cobM_fecha)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) La fecha no tiene el formato adecuado (ej. ' . date("Y-m-d") . '). (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        list($cobM_anio, $cobM_mes, $cobM_dia) = explode("-", $cobM_fecha);
    }

    if ($cobM_cliente_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un Cliente. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $cobM_cliente_id)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El Cliente NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $checkCliID = consultaData("SELECT * FROM Clientes WHERE cliente_id = '$cobM_cliente_id'");
        if ($checkCliID['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El Cliente seleccionado no existe. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $checkCli = $checkCliID['dataFetch'][0];
        }
    }

    // Si esta activado el checkbox para agregar factura entonces se recibiran los POST para datos de factura.
    if (isset($_POST['excFact'])) {
        $cobM_iva = limpiarCadena($_POST['cobM_iva_add']);
        if ($cobM_iva == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El IVA es necesario. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (verificarDatos("^(\d|[1-9]\d|100)$", $cobM_iva)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El formato del IVA es incorrecto. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }

        $cobM_folio = limpiarCadena(strtoupper($_POST['cobM_folio_add']));
        if ($cobM_folio == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) Si existe folio de factura debes colocarlo, de lo contrario se deja en N/A. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (verificarDatos("[A-Z0-9]+", $cobM_folio)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) el numero de telefono no tiene el formato solicitado. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $checkFolio = consultaData("SELECT * FROM cobranzasM WHERE cobM_folio = '$cobM_folio'");
            if ($checkFolio['numRows'] >= 1) {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(TT _ TT) El Folio ingresado ya existe. (TT _ TT)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            } else {
                $nameDoc = "(" . $checkCli['cliente_rs'] . ") " . $checkCli['cliente_rs'] . " - " . $cobM_folio;
            }
        }

        if (isset($_POST['cobM_archivo'])) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) Formato incorrecto para el input de PDF. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            // --------------------------------------------- Verificando Input File --------------------------------------------- //
            // ***************** Evidencia en PDF ***************** //
            if (isset($_FILES['cobM_archivo'])) {
                if ($_FILES['cobM_archivo']['name'] == "") {
                    $alerta = [
                        'Alerta' => 'simple',
                        'Titulo' => 'Ocurrio un Error inesperado',
                        'Texto' => '(TT _ TT) No Ingresaste PDF de Factura. (TT _ TT)',
                        'Tipo' => 'error'
                    ];
                    echo json_encode($alerta);
                    exit();
                } else if ($_FILES['cobM_archivo']['type'] != "application/pdf") {
                    $alerta = [
                        'Alerta' => 'simple',
                        'Titulo' => 'Ocurrio un Error inesperado',
                        'Texto' => '(TT _ TT) El formato de la evidencia debe ser en PDF. (TT _ TT)',
                        'Tipo' => 'error'
                    ];
                    echo json_encode($alerta);
                    exit();
                } else {
                    $temp_cobM_archivo = $_FILES['cobM_archivo']['tmp_name'];
                    $saveDocs = saveFact($temp_cobM_archivo, $nameDoc, $cobM_anio, $cobM_mes, $cobM_dia);
                    if ($saveDocs['status']) {
                        $cobM_archivo = $saveDocs['result'];
                    } else {
                        $alerta = [
                            'Alerta' => 'simple',
                            'Titulo' => 'Ocurrio un Error inesperado',
                            'Texto' => '(TT _ TT) ' . $saveDocs['result'] . '. (TT _ TT)',
                            'Tipo' => 'error'
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
                }
            } else {
                $cobM_archivo = 0;
            }
            // **************************************************** //
            // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //
        }
        $start = "INSERT INTO cobranzasM (cobM_cliente_id, cobM_fecha, cobM_comm, cobM_iva, cobM_folio, cobM_archivo) VALUES ('$cobM_cliente_id', '$cobM_fecha', '$cobM_comm', '$cobM_iva', '$cobM_folio', '$cobM_archivo')";

        if (isset($_POST['excFactISR'])) {
            $cobM_isr = limpiarCadena($_POST['cobM_isr_add']);
            if ($cobM_isr == "") {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(TT _ TT) El ISR es necesario. (TT _ TT)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            } else if (verificarDatos("\d+(.\d+)?", $cobM_isr)) {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(TT _ TT) El formato del ISR es incorrecto. (TT _ TT)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            }
            $start = "INSERT INTO cobranzasM (cobM_cliente_id, cobM_fecha, cobM_comm, cobM_iva, cobM_isr, cobM_folio, cobM_archivo) VALUES ('$cobM_cliente_id', '$cobM_fecha', '$cobM_comm', '$cobM_iva', '$cobM_isr', '$cobM_folio', '$cobM_archivo')";
        }
    } else {

        for ($cobM_folio = "NC000001";; $cobM_folio++) {
            $check_folio = consultaData("SELECT cobM_folio FROM cobranzasM WHERE cobM_folio = '$cobM_folio'");
            if ($check_folio['numRows'] == 0) {
                break;
            }
        }

        $start = "INSERT INTO cobranzasM (cobM_cliente_id, cobM_fecha, cobM_comm, cobM_folio) VALUES ('$cobM_cliente_id', '$cobM_fecha', '$cobM_comm', '$cobM_folio')";
    }


    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    $response = insertID($start);
    if ($response['status']) {
        $alerta = [
            'Alerta' => 'redireccionar',
            'url' => SERVERURL . "Cobranzas/idD/" . encryption($response['ID'])
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo realizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
} // Fin del la Funcion

function editarCobranza()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $cobM_id = limpiarCadena(decryption($_POST['cobM_id_edit']));
    $cobM_fecha = limpiarCadena($_POST['cobM_fecha_edit']);
    $cobM_cliente_id = limpiarCadena(decryption($_POST['cobM_cliente_id_edit']));
    $cobM_comm = limpiarCadena($_POST['cobM_comm_edit']);

    $update = "UPDATE cobranzasM SET ";

    if ($cobM_fecha == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("\d{4}-\d{2}-\d{2}", $cobM_fecha)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) La fecha no tiene el formato adecuado (ej. ' . date("Y-m-d") . '). (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        list($cobM_anio, $cobM_mes, $cobM_dia) = explode("-", $cobM_fecha);
    }

    if ($cobM_cliente_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un Cliente. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $cobM_cliente_id)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El Cliente NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $checkCliID = consultaData("SELECT * FROM Clientes WHERE cliente_id = '$cobM_cliente_id'");
        if ($checkCliID['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El Cliente seleccionado no existe. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $checkCli = $checkCliID['dataFetch'][0];
        }
    }

    // Si esta activado el checkbox para agregar factura entonces se recibiran los POST para datos de factura.
    if (isset($_POST['excFactEdit'])) {
        $cobM_iva = limpiarCadena($_POST['cobM_iva_edit']);
        if ($cobM_iva == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El IVA es necesario. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (verificarDatos("^(\d|[1-9]\d|100)$", $cobM_iva)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El formato del IVA es incorrecto. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }

        $cobM_folio = limpiarCadena(strtoupper($_POST['cobM_folio_edit']));
        if ($cobM_folio == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) Si, existe folio de factura debes colocarlo, de lo contrario se deja en N/A. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (verificarDatos("[A-Z0-9]+", $cobM_folio)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El Folio no tiene el formato solicitado. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $checkFolio = consultaData("SELECT * FROM cobranzasM WHERE cobM_folio = '$cobM_folio' AND cobM_id != '$cobM_id'");
            if ($checkFolio['numRows'] >= 1) {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(TT _ TT) El Folio ingresado ya existe. (TT _ TT)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            } else {
                $nameDoc = "(" . $checkCli['cliente_rs'] . ") " . $checkCli['cliente_rs'] . " - " . $cobM_folio;
            }
        }

        $update .= "cobM_cliente_id = '$cobM_cliente_id', cobM_fecha = '$cobM_fecha', cobM_comm = '$cobM_comm', cobM_iva = '$cobM_iva', cobM_folio = '$cobM_folio'";

        // --------------------------------------------- Verificando Input File --------------------------------------------- //
        if (isset($_FILES['cobM_archivo_edit'])) {
            // ***************** Evidencia en PDF ***************** //
            if ($_FILES['cobM_archivo_edit']['name'] == "") {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(TT _ TT) No Ingresaste PDF de Factura. (TT _ TT)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            } else if ($_FILES['cobM_archivo_edit']['type'] != "application/pdf") {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(TT _ TT) El formato de la evidencia debe ser en PDF. (TT _ TT)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            } else {
                $temp_cobM_archivo = $_FILES['cobM_archivo_edit']['tmp_name'];
                $saveDocs = saveFact($temp_cobM_archivo, $nameDoc, $cobM_anio, $cobM_mes, $cobM_dia);
                if ($saveDocs['status']) {
                    $cobM_archivo = $saveDocs['result'];
                } else {
                    $alerta = [
                        'Alerta' => 'simple',
                        'Titulo' => 'Ocurrio un Error inesperado',
                        'Texto' => '(TT _ TT) ' . $saveDocs['result'] . '. (TT _ TT)',
                        'Tipo' => 'error'
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            }
            // **************************************************** //

            $update .= ", cobM_archivo = '$cobM_archivo'";
        }
        // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //

        if (isset($_POST['cobM_isr_edit'])) {
            $cobM_isr = limpiarCadena($_POST['cobM_isr_edit']);
            if ($cobM_isr == "") {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(TT _ TT) El ISR es necesario. (TT _ TT)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            } else if (verificarDatos("\d+(.\d+)?", $cobM_isr)) {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(TT _ TT) El formato del ISR es incorrecto. (TT _ TT)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            }
            $update .= ", cobM_isr = '$cobM_isr'";
        } else {
            $update .= ", cobM_isr = '0'";
        }
    } else {

        for ($cobM_folio = "NC000001";; $cobM_folio++) {
            $check_folio = consultaData("SELECT cobM_folio, cobM_id FROM cobranzasM WHERE cobM_folio = '$cobM_folio'");

            if ($check_folio['numRows'] == 1 && $check_folio['dataFetch'][0]['cobM_id'] == $cobM_id) {
                $cobM_folio = $check_folio['dataFetch'][0]['cobM_folio'];
                break;
            } else if ($check_folio['numRows'] == 0) {
                break;
            }
        }

        $update .= "cobM_cliente_id = '$cobM_cliente_id', cobM_fecha = '$cobM_fecha', cobM_comm = '$cobM_comm', cobM_folio = '$cobM_folio', cobM_isr = '0', cobM_iva = '0'";
        // $update .= ", cobM_archivo = '0', cobM_folio = '0', cobM_iva = '0', cobM_isr = '0'";
    }


    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //

    $update .= " WHERE cobM_id = $cobM_id";
    if (sentenciaData($update)) {
        $alerta = [
            'Alerta' => 'redireccionar',
            'url' => SERVERURL . "Cobranzas/idD/" . encryption($cobM_id)
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo actualizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
} // Fin del la Funcion


function agregarCobro()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $cobC_cobM_id = limpiarCadena(decryption($_POST['cobC_cobM_id']));
    $cobC_fecha = limpiarCadena($_POST['cobC_fecha']);
    $cobC_conc = limpiarCadena($_POST['cobC_conc']);
    $cobC_monto = limpiarCadena(round($_POST['cobC_monto'], 3));

    if ($cobC_cobM_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un registro. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $cobC_cobM_id)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El registro no cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $checkCliID = consultaData("SELECT * FROM cobranzasM WHERE cobM_id = '$cobC_cobM_id'");
        if ($checkCliID['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El registro seleccionado no existe. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }
    }

    if ($cobC_fecha == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("\d{4}-\d{2}-\d{2}", $cobC_fecha)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) La fecha no tiene el formato adecuado (ej. ' . date("Y-m-d") . '). (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($cobC_conc == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso concepto. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($cobC_monto == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^-?\d+(\.\d+)?$", $cobC_monto)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Monto ingresado no contiene el formato correcto. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    if (sentenciaData("INSERT INTO cobranzasC (cobC_cobM_id, cobC_fecha, cobC_conc, cobC_monto) VALUES ('$cobC_cobM_id', '$cobC_fecha', '$cobC_conc', '$cobC_monto')")) {
        $alerta = [
            'Alerta' => 'redireccionar',
            'url' => SERVERURL . "Cobranzas/idD/" . encryption($cobC_cobM_id)
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo realizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
} // Fin del la Funcion


function agregarPago()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $cobP_cobM_id = limpiarCadena(decryption($_POST['cobP_cobM_id']));
    $cobP_fecha = limpiarCadena($_POST['cobP_fecha']);
    $cobP_tipoPago = limpiarCadena($_POST['cobP_tipoPago']);
    $cobP_conc = limpiarCadena($_POST['cobP_conc']);
    $cobP_monto = limpiarCadena(round($_POST['cobP_monto'], 3));

    if ($cobP_cobM_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un registro. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $cobP_cobM_id)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El registro no cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $QRYcobM = consultaData("SELECT * FROM cobranzasM WHERE cobM_id = '$cobP_cobM_id'");
        if ($QRYcobM['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El registro seleccionado no existe. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $cobM_iva = $QRYcobM['dataFetch'][0]['cobM_iva'];
            $cobM_isr = $QRYcobM['dataFetch'][0]['cobM_isr'];
        }
    }

    if ($cobP_fecha == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Error al ingresar la fecha',
            'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("\d{4}-\d{2}-\d{2}", $cobP_fecha)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Error al ingresar la fecha',
            'Texto' => '(TT _ TT) La fecha no tiene el formato adecuado (ej. ' . date("Y-m-d") . '). (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($cobP_tipoPago == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Error al ingresar el tipo de pago',
            'Texto' => '(TT _ TT) No se ingreso tipo de pago. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("[0-9]{1}", $cobP_tipoPago)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Error al ingresar el tipo de pago',
            'Texto' => '(TT _ TT) El formato no es el correcto. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($cobP_tipoPago != 1 && $cobP_tipoPago != 2 && $cobP_tipoPago != 3) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Error al ingresar el tipo de pago',
            'Texto' => '(TT _ TT) Tipo de pago desconocido. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($cobP_conc == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Error al ingresar el concepto',
            'Texto' => '(TT _ TT) No se ingreso concepto. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($cobP_monto == "" || $cobP_monto == 0) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Error al ingresar el monto',
            'Texto' => '(TT _ TT) No se ingreso monto. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^-?\d+(\.\d+)?$", $cobP_monto)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Error al ingresar el monto',
            'Texto' => '(TT _ TT) Monto ingresado no contiene el formato correcto. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $QRYcobP = consultaData("SELECT SUM(cobP_monto) AS cobP_total FROM cobranzasP WHERE cobP_cobM_id = '$cobP_cobM_id'");
        if ($QRYcobP['numRows'] == 0) {
            $cobP_total = 0;
        } else {
            $cobP_total = $QRYcobP['dataFetch'][0]['cobP_total'];
        }
        $sumaPmonto = round($cobP_total + $cobP_monto, 3);

        $QRYcobC = consultaData("SELECT SUM(cobC_monto) AS cobC_total FROM cobranzasC WHERE cobC_cobM_id = '$cobP_cobM_id'");
        if ($QRYcobC['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) Debes agregar montos a cobrar. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $cobC_subtotal = $QRYcobC['dataFetch'][0]['cobC_total'];
            $cobC_total = $cobC_subtotal;
        }

        if ($cobM_iva > 0) {
            $IVA = $cobM_iva / 100;
            $IVA = $cobC_subtotal * $IVA;
            $cobC_total = $cobC_subtotal + $IVA;
        }

        if ($cobM_isr > 0) {
            $ISR = $cobM_isr / 100;
            $ISR = $cobC_subtotal * $ISR;
            $cobC_total = $cobC_total - $ISR;
        }

        $cobC_total = round($cobC_total, 3);
    }

    if ($sumaPmonto > $cobC_total) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El monto ingresado supera el total a cobrar. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }



    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //

    if ($sumaPmonto == $cobC_total) {
        sentenciaData("UPDATE cobranzasM SET cobM_status = 2 WHERE cobM_id = '$cobP_cobM_id'");
    }
    
    if (sentenciaData("INSERT INTO cobranzasP (cobP_cobM_id, cobP_fecha, cobP_tipoPago, cobP_conc, cobP_monto) VALUES ('$cobP_cobM_id', '$cobP_fecha', '$cobP_tipoPago', '$cobP_conc', '$cobP_monto')")) {
        $alerta = [
            'Alerta' => 'redireccionar',
            'url' => SERVERURL . "Cobranzas/idD/" . encryption($cobP_cobM_id)
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => "Ocurrio un error inesperado.",
            'Texto' => "No se pudo realizar el registro.",
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //
    // $alerta = [
    //     'Alerta' => 'simple',
    //     'Titulo' => $cobP_monto,
    //     'Texto' => "",
    //     'Tipo' => 'success'
    // ];

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
} // Fin del la Funcion