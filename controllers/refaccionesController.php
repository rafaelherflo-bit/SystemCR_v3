<?php

// ============================ Controladores de Refacciones ============================ //
function actualizarRefaccion()
{
    // ------------------- Comprobacion de Formulario ------------------- //

    $ref_id = limpiarCadena(decryption($_POST['ref_id_edit']));
    $ref_comp = limpiarCadena($_POST['ref_comp_edit']);
    $ref_provR_id = limpiarCadena(decryption($_POST['ref_provR_id_edit']));
    $ref_estado = limpiarCadena($_POST['ref_estado_edit']);

    if ($ref_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ No se agrego un ID de refaccion \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("[0-9]{1,15}$", $ref_id)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ La Refaccion no tiene el formato solicitado. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $QRY = consultaData("SELECT * FROM Refacciones WHERE ref_id = " . $ref_id);
        if ($QRY['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ La Refaccion seleccionada No existe o no se encuentra Activa. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $refData = $QRY['dataFetch'][0];
        }
    }

    if ($ref_provR_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ No se ingreso ningun proveedor de toner. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("[0-9]{1,15}", $ref_provR_id)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ El proveedor ingresado no tiene el formato solicitado. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($ref_comp == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ Debes ingresar una descripcion de compatibilidad. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("[a-zA-Z0-9 !-\/]{10,250}", $ref_comp)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ La descripcion de compatibilidad no tiene el formato correcto. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $ref_comp = strtoupper($ref_comp);
    }

    if ($ref_estado != 'Activo' && $ref_estado != 'Inactivo') {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ Estatus Incorrecto. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if (isset($_FILES['ref_image'])) {
        if ($_FILES['ref_image']['name'] == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) No Ingresaste Imagen. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if ($_FILES['ref_image']['type'] != "image/jpeg") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '(TT _ TT) El formato de la imagen debe ser en JPEG. (TT _ TT)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {

            $docDir = SERVERDIR . 'DocsCR/';
            // +======+ Verificar Carpeta DocsCR +======+ //
            if (!file_exists($docDir)) {
                mkdir($docDir, 0755, true);
                $docDir .=  'ALMACEN/';
            } else {
                $docDir .=  'ALMACEN/';
            }
            // +======+ Verificar ALMACEN +======+ //
            if (!file_exists($docDir)) {
                mkdir($docDir, 0755, true);
                $docDir .= 'REFACCIONES/';
            } else {
                $docDir .= 'REFACCIONES/';
            }
            // +======+ Verificar REFACCIONES +======+ //
            if (!file_exists($docDir)) {
                mkdir($docDir, 0755, true);
            }

            $temp_ref_image = $_FILES['ref_image']['tmp_name'];
            $dir_ref_image = $docDir . $refData['ref_codigo'] . ".jpg";

            // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
            if (file_exists($dir_ref_image)) {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(o _ O) La imagen, ya existe. (o _ O)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            }
            if (!move_uploaded_file($temp_ref_image, $dir_ref_image)) {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '(o _ O) No se pudo guardar la imagen. (o _ O)',
                    'Tipo' => 'error'
                ];
                echo json_encode($alerta);
                exit();
            }
        }
    }

    if (sentenciaData("UPDATE Refacciones SET ref_comp = '$ref_comp', ref_provR_id = '$ref_provR_id', ref_estado = '$ref_estado' WHERE ref_id = '$ref_id'")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'Registro Completado',
            'Texto' => '\(- _ -)/ Registro actualizado correctamente. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ No se pudo Actualizar el registro. \(- _ -)/',
            'Tipo' => 'error'
        ];
    }

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}

function entradaRefaccion()
{
    // ------------------- Comprobacion de Formulario ------------------- //

    $ref_codigo = limpiarCadena($_POST['ref_codigo_add']);
    $refRE_fecha = limpiarCadena($_POST['refRE_fecha']);
    $refRE_cantidad = limpiarCadena($_POST['refRE_cantidad']);
    $refRE_comm = limpiarCadena($_POST['refRE_comm']);


    if ($refRE_fecha == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ No se ingreso fecha. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $refRE_fecha)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ La fecha no tiene el formato adecuado (ej. 2025-01-13). \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }
    if ($ref_codigo == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ No se ingreso ningun codigo de Refaccion. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }
    if ($refRE_comm == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ No se ingresaron comentario, debes agregar folios de entrega o algun otro comentario sobre el ingreso. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[a-zA-Z0-9 ]+$", $refRE_comm)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ El comentario no tiene el formato adecuado. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($refRE_cantidad == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ No se ingreso ninguna Cantidad para agregar al stock. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($refRE_cantidad == 0) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ La cantidad no puede ser 0. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $refRE_cantidad)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ Formato de cantidad incorrecto. \(- _ -)/',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    // ======================== Agregar Nueva Refaccion al Almacen ================ //
    if (isset($_POST['ref_nuevo'])) {
        if (verificarDatos("^([0-9]{4,5})([A-Za-z]{0,2})$", $ref_codigo)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ El codigo deben ser solo numeros, de 4 a 5 digitos (ej. 1175) y si es a color agrega una letra (ej. 5521M). \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }

        $ref_catR_id = limpiarCadena($_POST['ref_catR_id']);
        $ref_provR_id = limpiarCadena($_POST['ref_provR_id']);
        $ref_comp = limpiarCadena($_POST['ref_comp']);

        if ($ref_catR_id == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ No se ingreso ninguna Categoria. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (!is_numeric($ref_catR_id)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ La categoria NO tiene el formato indicado. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }

        // ======================== Buscar Codigo de Categoria ============= //
        $consultaCategoria = "SELECT * FROM CategoriasR WHERE catR_id = '$ref_catR_id'";
        $catR_codigo = consultaData($consultaCategoria);
        if ($catR_codigo['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ La categoria solicitada no existe. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $catR_codigo = $catR_codigo['dataFetch'][0]['catR_codigo'];
        }

        if ($ref_provR_id == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ No se ingreso ningun proveedor de toner. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (verificarDatos("[0-9]{1,15}", $ref_provR_id)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ El proveedor ingresado no tiene el formato solicitado. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($ref_comp == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ Debes ingresar una descripcion de compatibilidad. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (verificarDatos("[a-zA-Z0-9 !-\/]{10,250}", $ref_comp)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ La descripcion de compatibilidad no tiene el formato correcto. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $ref_comp = strtoupper($ref_comp);
        }
        // Terminan las comprobaciones para el toner Nuevo //

        $ref_codigo = $catR_codigo . "-" . $ref_codigo . "-" . $ref_provR_id;

        $SQL_refCodigo = "SELECT ref_codigo FROM Refacciones WHERE ref_codigo = '$ref_codigo'";
        $check_refCodigo = consultaData($SQL_refCodigo);
        if ($check_refCodigo['numRows'] >= 1) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ El codigo de refaccion ya se encuentra registrada, verifica los datos ingresados. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }

        if (sentenciaData("INSERT INTO Refacciones (ref_codigo, ref_provR_id, ref_catR_id, ref_comp) VALUES ('$ref_codigo', '$ref_provR_id', '$ref_catR_id', '$ref_comp')")) {
            $ref_id = consultaData("SELECT * FROM Refacciones WHERE ref_codigo = '$ref_codigo'")['dataFetch'][0]['ref_id'];
            if (sentenciaData("INSERT INTO RefaccionesRegistrosE (refRE_fecha, refRE_ref_id, refRE_cant, refRE_comm) VALUES ('$refRE_fecha', '$ref_id', '$refRE_cantidad', '$refRE_comm')")) {
                $alerta = [
                    'Alerta' => 'recargar',
                    'Titulo' => 'Registro Completado',
                    'Texto' => '\(- _ -)/ Se agrego stock de la nueva Refaccion (' . $ref_codigo . ') correctamente. \(- _ -)/',
                    'Tipo' => 'success'
                ];
            } else {
                $alerta = [
                    'Alerta' => 'simple',
                    'Titulo' => 'Ocurrio un Error inesperado',
                    'Texto' => '\(- _ -)/ No se pudo agregar el registro. \(- _ -)/',
                    'Tipo' => 'error'
                ];
            }
        } else {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ No se pudo agregar la nueva Refaccion. \(- _ -)/',
                'Tipo' => 'error'
            ];
        }
        // Termina el registro del nuevo Toner //
    } else {
        // ======================== Agregar Stock de Refaccion al Almacen ============= //

        if ($ref_codigo == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ No se ingreso ningun codigo de Refaccion. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (verificarDatos("^([A-Z]{2})-([0-9]{4,5})-([0-9]{1,2})$", $ref_codigo)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ El codigo no tiene el formato correcto (ej. TK-5521M-1 o ES-4132-1). \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }


        $ref_id = limpiarCadena($_POST['ref_id']);
        if ($ref_id == "") {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ No se agrego un codigo de Refaccion \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (verificarDatos("[0-9]{1,15}$", $ref_id)) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ La Refaccion no tiene el formato solicitado. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else if (consultaData("SELECT * FROM Refacciones WHERE ref_estado = 'Activo' AND ref_id = " . $ref_id)['numRows'] == 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ La Refaccion seleccionada No existe o no se encuentra Activa. \(- _ -)/',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        }
        // Terminan las comprobaciones para agregar stock //

        $SQLRefReg = "INSERT INTO RefaccionesRegistrosE (refRE_fecha, refRE_ref_id, refRE_cant, refRE_comm) VALUES ('$refRE_fecha', '$ref_id', '$refRE_cantidad', '$refRE_comm')";
        if (sentenciaData($SQLRefReg)) {
            $alerta = [
                'Alerta' => 'recargar',
                'Titulo' => 'Registro Completado',
                'Texto' => '\(- _ -)/ Se agreso el stock correctamente. \(- _ -)/',
                'Tipo' => 'success'
            ];
        } else {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un Error inesperado',
                'Texto' => '\(- _ -)/ No se agreso el registro. \(- _ -)/',
                'Tipo' => 'error'
            ];
        }
        // Termina la insertsion a la base de Datos //
    }

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}

function salidaRefaccion()
{
    // ------------------- Comprobacion de Formulario ------------------- //

    $refRS_ref_id = limpiarCadena($_POST['ref_id_out']);
    if ($refRS_ref_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso ninguna Refaccion. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $refRS_ref_id)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El dato de la refaccion solicitada no tiene el formato correcto, refresca la pagina. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $check_ref = consultaData("SELECT * FROM Refacciones WHERE ref_estado = 'Activo' AND ref_id = '$refRS_ref_id'");
        if ($check_ref['numRows'] <= 0) {
            $alerta = [
                'Alerta' => 'simple',
                'Titulo' => 'Ocurrio un error',
                'Texto' => '(o _ O) No existe la refaccion solicitada. (o _ O)',
                'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $refaccionData = $check_ref['dataFetch'][0];

            $refE = consultaData("SELECT SUM(refRE_cant) AS refE FROM RefaccionesRegistrosE WHERE refRE_ref_id = " . $refRS_ref_id)['dataFetch'][0]['refE'];
            $refS = consultaData("SELECT SUM(refRS_cant) AS refS FROM RefaccionesRegistrosS WHERE refRS_ref_id = " . $refRS_ref_id)['dataFetch'][0]['refS'];
            $refStock = $refE - $refS;
        }
    }

    $refRS_fecha = limpiarCadena($_POST['refRS_fecha']);
    if ($refRS_fecha == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso ninguna Fecha de Salida. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $refRS_fecha)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El fecha de salida no tiene el formato correcto. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    $refRS_cant = limpiarCadena($_POST['refRS_cant']);
    if ($refRS_cant == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso ninguna Cantidad a retirar. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^[0-9]+$", $refRS_cant)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El dato de la refaccion solicitada no tiene el formato correcto, refresca la pagina. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($refRS_cant > $refStock) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) La cantidad solicitada superan las Existencias en el sistema, verifica en el almacen... (o _ O)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    $refRS_empleado = limpiarCadena($_POST['refRS_empleado']);
    if ($refRS_empleado == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se ingreso ninguna Empleado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($refRS_empleado != "Candy" && $refRS_empleado != "Renan" && $refRS_empleado != "Rafa") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El se reconoce el empleado que se ah ingresado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([A-Z]{1})([a-z]{3,4})$", $refRS_empleado)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El dato del Empleado solicitado no tiene el formato correcto, refresca la pagina. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    $refRS_comm = limpiarCadena($_POST['refRS_comm']);
    if ($refRS_comm == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Debes Agregar un comentario de retiro. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    // ------------------- Registro de Datos en la DB ------------------- //

    $SQL_RRS = "INSERT INTO RefaccionesRegistrosS (refRS_fecha, refRS_ref_id, refRS_empleado, refRS_cant, refRS_comm) VALUES ('$refRS_fecha', '$refRS_ref_id',  '$refRS_empleado', '$refRS_cant', '$refRS_comm')";
    if (sentenciaData($SQL_RRS)) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'Registro Completado',
            'Texto' => '\(- _ -)/ Registro Completado. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o\ _ /o) No se actualizo el stock. (o\ _ /o)',
            'Tipo' => 'error'
        ];
    }

    echo json_encode($alerta);
    exit();
}

function editCatR()
{
    $catR_id = limpiarCadena(decryption($_POST['catR_id_edit']));
    if ($catR_id == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un error',
            'Texto' => '(o _ O) El id de la categoria no es valido. (o _ O)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM CategoriasR WHERE catR_id = '$catR_id'")['numRows'] <= 0) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un error',
            'Texto' => '(o _ O) La categoria solicitada no existe. (o _ O)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    $catR_codigo = limpiarCadena($_POST['catR_codigo_edit']);
    if ($catR_codigo == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Espera',
            'Texto' => '(o _ O) Debes agregar un codigo. (o _ O)',
            'Tipo' => 'info'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("[A-Z]{2,3}", $catR_codigo)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Espera',
            'Texto' => '(TT _ TT) El codigo ingresado no cuenta con el formato correcto, deben ser 2 o 3 letras en MAYUSCULAS. (TT _ TT)',
            'Tipo' => 'info'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM CategoriasR WHERE catR_codigo = '$catR_codigo' AND catR_id != '$catR_id'")['numRows'] >= 1) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Espera',
            'Texto' => '(o _ O) El codigo ya esta en uso. (o _ O)',
            'Tipo' => 'info'
        ];
        echo json_encode($alerta);
        exit();
    }

    $catR_nombre = limpiarCadena($_POST['catR_nombre_edit']);
    if (strlen($catR_nombre) < 5) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Espera',
            'Texto' => '(o _ O) No se ingreso una descripcion valida. (o _ O)',
            'Tipo' => 'info'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $catR_nombre = ucwords($catR_nombre);
    }

    // ------------------- Sentencia de actualizacion en la DB ------------------- //
    if (sentenciaData("UPDATE CategoriasR SET catR_codigo = '$catR_codigo', catR_nombre = '$catR_nombre' WHERE catR_id = '$catR_id'")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'Exito',
            'Texto' => '\(- _ -)/ Actualizacion Completada. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se actualizo el registro. (TT _ TT)',
            'Tipo' => 'error'
        ];
    }

    echo json_encode($alerta);
    exit();
}

function addCatR()
{

    $catR_codigo = limpiarCadena($_POST['catR_codigo_add']);
    if ($catR_codigo == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Espera',
            'Texto' => '(o _ O) Debes agregar un codigo. (o _ O)',
            'Tipo' => 'info'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("[A-Z]{2,3}", $catR_codigo)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Espera',
            'Texto' => '(TT _ TT) El codigo ingresado no cuenta con el formato correcto, deben ser 2 o 3 letras en MAYUSCULAS. (TT _ TT)',
            'Tipo' => 'info'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM CategoriasR WHERE catR_codigo = '$catR_codigo'")['numRows'] >= 1) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Espera',
            'Texto' => '(o _ O) El codigo ya esta en uso. (o _ O)',
            'Tipo' => 'info'
        ];
        echo json_encode($alerta);
        exit();
    }

    $catR_nombre = limpiarCadena($_POST['catR_nombre_add']);
    if (strlen($catR_nombre) < 5) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Espera',
            'Texto' => '(o _ O) No se ingreso una descripcion valida. (o _ O)',
            'Tipo' => 'info'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $catR_nombre = ucwords($catR_nombre);
    }

    // ------------------- Sentencia de actualizacion en la DB ------------------- //
    if (sentenciaData("INSERT INTO CategoriasR (catR_codigo, catR_nombre) VALUES ('$catR_codigo', '$catR_nombre')")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'Exito',
            'Texto' => '\(- _ -)/ Registro Completo. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) No se realizo el registro. (TT _ TT)',
            'Tipo' => 'error'
        ];
    }

    echo json_encode($alerta);
    exit();
}
