<?php

// ============================ Controladores de Lecturas ============================ //


/* --------------------- Funcion Guardar Archivos PDF --------------------- */
function saveReporte($temp_reporte_archivo, $nameDoc, $fecha_anio, $fecha_mes, $fecha_dia)
{

  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
  $docDir = SERVERDIR . 'DocsCR/ReportesCR/';
  // +======+ Verificar Carpeta Raiz +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $docDir .=  $fecha_anio . '/';
  } else {
    $docDir .=  $fecha_anio . '/';
  }
  // +======+ Verificar Carpeta Anio +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $docDir .= $fecha_mes . '/';
  } else {
    $docDir .= $fecha_mes . '/';
  }
  // +======+ Verificar Carpeta de Formatos +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  }

  $archivo = $fecha_dia . '-' . $fecha_mes . '-' . $fecha_anio . ' - ' . $nameDoc . ' - Reporte de Servicio.pdf';

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  if (file_exists($docDir . $archivo)) {
    $result = [
      'status' => false,
      'result' => 'La evidencia, ya existe'
    ];
  } else if (move_uploaded_file($temp_reporte_archivo, $docDir . $archivo)) {
    $result = [
      'status' => true,
      'result' => $archivo
    ];
  } else {
    $result = [
      'status' => false,
      'result' => 'No se pudo guardar la evidencia.'
    ];
  }

  return $result;
} // Fin del la Funcion


function agregarReporte()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $reporte_estado = limpiarCadena($_POST['reporte_estado']);
  $reporte_fecha = limpiarCadena($_POST['reporte_fecha']);
  $reporte_fecha_inicio = limpiarCadena($_POST['reporte_fecha_inicio']);
  $reporte_fecha_fin = limpiarCadena($_POST['reporte_fecha_fin']);
  $reporte_wmakes = ($_POST['reporte_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporte_wmakes']);
  $reporte_renta_id = limpiarCadena(decryption($_POST['reporte_renta_id']));
  $reporte_reporte = limpiarCadena($_POST['reporte_reporte']);
  $reporte_resolucion = limpiarCadena($_POST['reporte_resolucion']);
  $comments = limpiarCadena($_POST['comments']);

  if ($reporte_estado != 1) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Estado del reporte incorrecto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Inicial. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    list($reporte_anio, $reporte_mes, $reporte_dia) = explode("-", explode("T", $reporte_fecha)[0]);
  }

  if ($reporte_fecha_inicio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha de inicio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (dateCompare($reporte_fecha_inicio, "menor", $reporte_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Fecha de Inicio no puede ser menor que a Fecha de Reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_fecha_fin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso fecha de finalizacion. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (dateCompare($reporte_fecha_fin, "igualOmenor", $reporte_fecha_inicio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Fecha Final no puede ser menor o igual que a Fecha de Inicio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_renta_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar una Renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporte_renta_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $rentaIdSQL = "SELECT * FROM Rentas
                    INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                    INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                    WHERE renta_id = '$reporte_renta_id' AND renta_estado = 'Activo'";
    $rentaQRY = consultaData($rentaIdSQL);
    if ($rentaQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) La renta seleccionada No existe o no se encuentra activa. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $rentaData = $rentaQRY['dataFetch'][0];
      $reporte_equipo_id = $rentaData['renta_equipo_id'];

      $reporte_archivo = $rentaData['cliente_rs'] . " (" . $rentaData['contrato_folio'] . "-" . $rentaData['renta_folio'] . " - " . $rentaData['renta_depto'] . " - " . $rentaData['equipo_serie'] . ")";
    }
  }

  if ($reporte_reporte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_resolucion == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso la resolucion. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // --------------------------------------------- Agregamos el Pendiente --------------------------------------------- //
  if ($comments != "") {
  }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el pendiente ^^^ FIN ^^^ --------------------------------- //


  // ---------------------------------------------- DATOS DE CONSUMIBLES ---------------------------------------------- //
  $renta_stock_K = limpiarCadena($_POST['renta_stock_K']);
  if (verificarDatos("^[0-9]+$", $renta_stock_K)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK color Negro, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $renta_stock_M = limpiarCadena($_POST['renta_stock_M']);
  if (verificarDatos("^[0-9]+$", $renta_stock_M)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK color Magenta, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $renta_stock_C = limpiarCadena($_POST['renta_stock_C']);
  if (verificarDatos("^[0-9]+$", $renta_stock_C)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK color Cyan, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $renta_stock_Y = limpiarCadena($_POST['renta_stock_Y']);
  if (verificarDatos("^[0-9]+$", $renta_stock_Y)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK color Amarillo, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $renta_stock_R = limpiarCadena($_POST['renta_stock_R']);
  if (verificarDatos("^[0-9]+$", $renta_stock_R)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK Residual, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $equipo_nivel_K = limpiarCadena($_POST['equipo_nivel_K']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_K)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel color Negro, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $equipo_nivel_M = limpiarCadena($_POST['equipo_nivel_M']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_M)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel color Magenta, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $equipo_nivel_C = limpiarCadena($_POST['equipo_nivel_C']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_C)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel color Cyan, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $equipo_nivel_Y = limpiarCadena($_POST['equipo_nivel_Y']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_Y)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel color Amarillo, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $equipo_nivel_R = limpiarCadena($_POST['equipo_nivel_R']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_R)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel Residual, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // ---------------------------------- ^^^ FIN ^^^ DATOS DE CONSUMIBLES ^^^ FIN ^^^ ---------------------------------- //

  // --------------------------------------------- Verificando Input File --------------------------------------------- //
  // ***************** Evidencia en PDF ***************** //
  if ($_FILES['reporte_archivo']['name'] == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No Ingresaste la evidencia. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($_FILES['reporte_archivo']['type'] != "application/pdf") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El formato de la evidencia debe ser PDF. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $temp_reporte_archivo = $_FILES['reporte_archivo']['tmp_name'];
  }
  // **************************************************** //
  // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //


  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //

  $saveDoc = saveReporte($temp_reporte_archivo, $reporte_archivo, $reporte_anio, $reporte_mes, $reporte_dia);
  if ($saveDoc['status']) {
    $reporte_archivo = $saveDoc['result'];
    $updateNiveles = "UPDATE Equipos SET equipo_nivel_K = '$equipo_nivel_K', equipo_nivel_M = '$equipo_nivel_M', equipo_nivel_C = '$equipo_nivel_C', equipo_nivel_Y = '$equipo_nivel_Y', equipo_nivel_R = '$equipo_nivel_R' WHERE equipo_id = '$reporte_equipo_id'";
    if (sentenciaData($updateNiveles)) {
      $updateStock = "UPDATE Rentas SET renta_stock_K = '$renta_stock_K', renta_stock_M = '$renta_stock_M', renta_stock_C = '$renta_stock_C', renta_stock_Y = '$renta_stock_Y', renta_stock_R = '$renta_stock_R' WHERE renta_id = '$reporte_renta_id'";
      if (sentenciaData($updateStock)) {
        $insert = "INSERT INTO Reportes (reporte_fecha, reporte_fecha_fin, reporte_fecha_inicio, reporte_wmakes, reporte_renta_id, reporte_equipo_id, reporte_archivo, reporte_reporte, reporte_resolucion) VALUES ('$reporte_fecha', '$reporte_fecha_fin', '$reporte_fecha_inicio', '$reporte_wmakes', '$reporte_renta_id', '$reporte_equipo_id', '$reporte_archivo', '$reporte_reporte', '$reporte_resolucion')";
        if (sentenciaData($insert)) {
          $alerta = [
            'Alerta' => 'redireccionar',
            'url' => SERVERURL . 'ReportesR/CustomMonth/' . date("Y") . '/' . ucfirst(dateFormat(date("Y") . "-" . date("n") . "-" . date("d"), "mesL"))
          ];
        } else {
          $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo realizar el registro. (o _ O)',
            'Tipo' => 'error'
          ];
        }
      } else {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) No se pudo actualizar El Stock de la Renta. (o _ O)',
          'Tipo' => 'error'
        ];
      }
    } else {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(o _ O) No se pudo actualizar los niveles de toner en el equipo. (o _ O)',
        'Tipo' => 'error'
      ];
    }
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) ' . $saveDoc['result'] . '. (TT _ TT)',
      'Tipo' => 'error'
    ];
  }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


function actualizarCompletoReporte()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $reporte_id = limpiarCadena(decryption($_POST['reporte_completo_update']));
  $reporte_fecha = limpiarCadena($_POST['reporte_fecha']);
  $reporte_fecha_fin = limpiarCadena($_POST['reporte_fecha_fin']);
  $reporte_fecha_inicio = limpiarCadena($_POST['reporte_fecha_inicio']);
  $reporte_wmakes = ($_POST['reporte_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporte_wmakes']);
  $reporte_renta_id = limpiarCadena(decryption($_POST['reporte_renta_id']));
  $reporte_reporte = limpiarCadena($_POST['reporte_reporte']);
  $reporte_resolucion = limpiarCadena($_POST['reporte_resolucion']);
  $comments = limpiarCadena($_POST['comments']);


  if ($reporte_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Reporte no puede estar vacio, recarga la pagina. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporte_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Reporte no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $SQL = "SELECT * FROM Reportes WHERE reporte_id = '$reporte_id'";
    $checkRepActExist = consultaData($SQL);
    if ($checkRepActExist['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El reporte no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }

    $checkRepActEstado = consultaData($SQL .= " AND reporte_estado = '1'");
    if ($checkRepActEstado['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El reporte existe pero el Estatus no corresponde con el tipo de actualizacion. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if ($reporte_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Inicial. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    list($reporte_anio, $reporte_mes, $reporte_dia) = explode("-", explode("T", $reporte_fecha)[0]);
  }

  if ($reporte_fecha_fin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Final. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_fecha_inicio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Final. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_renta_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar una Renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporte_renta_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $rentaSQL = "SELECT * FROM Rentas
                  INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                  INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                  INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                  INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                  WHERE renta_id = '$reporte_renta_id'";
    $rentaCheck = consultaData($rentaSQL);
    if ($rentaCheck['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) La renta seleccionada No existe o no se encuentra activa. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $rentaData = $rentaCheck['dataFetch'][0];

      $reporte_equipo_id = $rentaData['renta_equipo_id'];
      $reporte_archivo = $rentaData['cliente_rs'] . " (" . $rentaData['contrato_folio'] . "-" . $rentaData['renta_folio'] . " - " . $rentaData['renta_depto'] . " - " . $rentaData['equipo_serie'] . ")";
    }
  }

  if ($reporte_reporte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_resolucion == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso la resolucion. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // --------------------------------------------- Agregamos el Pendiente --------------------------------------------- //
  if ($comments != "") {
  }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el pendiente ^^^ FIN ^^^ --------------------------------- //


  // ---------------------------------------------- DATOS DE CONSUMIBLES ---------------------------------------------- //
  $renta_stock_K = limpiarCadena($_POST['renta_stock_K']);
  if (verificarDatos("^[0-9]+$", $renta_stock_K)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK color Negro, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $renta_stock_M = limpiarCadena($_POST['renta_stock_M']);
  if (verificarDatos("^[0-9]+$", $renta_stock_M)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK color Magenta, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $renta_stock_C = limpiarCadena($_POST['renta_stock_C']);
  if (verificarDatos("^[0-9]+$", $renta_stock_C)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK color Cyan, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $renta_stock_Y = limpiarCadena($_POST['renta_stock_Y']);
  if (verificarDatos("^[0-9]+$", $renta_stock_Y)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK color Amarillo, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $renta_stock_R = limpiarCadena($_POST['renta_stock_R']);
  if (verificarDatos("^[0-9]+$", $renta_stock_R)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en STOCK Residual, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $equipo_nivel_K = limpiarCadena($_POST['equipo_nivel_K']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_K)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel color Negro, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $equipo_nivel_M = limpiarCadena($_POST['equipo_nivel_M']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_M)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel color Magenta, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $equipo_nivel_C = limpiarCadena($_POST['equipo_nivel_C']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_C)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel color Cyan, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $equipo_nivel_Y = limpiarCadena($_POST['equipo_nivel_Y']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_Y)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel color Amarillo, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $equipo_nivel_R = limpiarCadena($_POST['equipo_nivel_R']);
  if (verificarDatos("^[0-9]+$", $equipo_nivel_R)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel Residual, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // ---------------------------------- ^^^ FIN ^^^ DATOS DE CONSUMIBLES ^^^ FIN ^^^ ---------------------------------- //

  // --------------------------------------------- Verificando Input File --------------------------------------------- //
  // ***************** Evidencia en PDF ***************** //


  if (isset($_FILES['reporte_archivo'])) {
    if ($_FILES['reporte_archivo']['name'] == "") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) No Ingresaste la evidencia. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($_FILES['reporte_archivo']['type'] != "application/pdf") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El formato de la evidencia debe ser PDF. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $temp_reporte_archivo = $_FILES['reporte_archivo']['tmp_name'];
    }

    $saveDoc = saveReporte($temp_reporte_archivo, $reporte_archivo, $reporte_anio, $reporte_mes, $reporte_dia);
    if ($saveDoc['status']) {
      $reporte_archivo = $saveDoc['result'];
    } else {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) ' . $saveDoc['result'] . '. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
    $updateReporte = "UPDATE Reportes SET
                    reporte_fecha = '$reporte_fecha',
                    reporte_fecha_inicio = '$reporte_fecha_inicio',
                    reporte_fecha_fin = '$reporte_fecha_fin',
                    reporte_wmakes = '$reporte_wmakes',
                    reporte_renta_id = '$reporte_renta_id',
                    reporte_renta_id = '$reporte_renta_id',
                    reporte_equipo_id = '$reporte_equipo_id',
                    reporte_archivo = '$reporte_archivo',
                    reporte_reporte = '$reporte_reporte',
                    reporte_resolucion = '$reporte_resolucion'
                    WHERE reporte_id = '$reporte_id'";
  } else {
    $updateReporte = "UPDATE Reportes SET
                    reporte_fecha = '$reporte_fecha',
                    reporte_fecha_inicio = '$reporte_fecha_inicio',
                    reporte_fecha_fin = '$reporte_fecha_fin',
                    reporte_wmakes = '$reporte_wmakes',
                    reporte_renta_id = '$reporte_renta_id',
                    reporte_renta_id = '$reporte_renta_id',
                    reporte_equipo_id = '$reporte_equipo_id',
                    reporte_reporte = '$reporte_reporte',
                    reporte_resolucion = '$reporte_resolucion'
                    WHERE reporte_id = '$reporte_id'";
  }
  // **************************************************** //
  // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //

  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
  $updateNiveles = "UPDATE Equipos SET equipo_nivel_K = '$equipo_nivel_K', equipo_nivel_M = '$equipo_nivel_M', equipo_nivel_C = '$equipo_nivel_C', equipo_nivel_Y = '$equipo_nivel_Y', equipo_nivel_R = '$equipo_nivel_R' WHERE equipo_id = '$reporte_equipo_id'";
  if (sentenciaData($updateNiveles)) {
    $updateStock = "UPDATE Rentas SET renta_stock_K = '$renta_stock_K', renta_stock_M = '$renta_stock_M', renta_stock_C = '$renta_stock_C', renta_stock_Y = '$renta_stock_Y', renta_stock_R = '$renta_stock_R' WHERE renta_id = '$reporte_renta_id'";
    if (sentenciaData($updateStock)) {
      if (sentenciaData($updateReporte)) {
        $alerta = [
          'Alerta' => 'recargar',
          'Titulo' => 'Registro Completado',
          'Texto' => '\(- _ -)/ Se actualizo el reporte correctamente. \(- _ -)/',
          'Tipo' => 'success'
        ];
      } else {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) No se pudo actualizar el registro. (o _ O)',
          'Tipo' => 'error'
        ];
      }
    } else {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(o _ O) No se pudo actualizar El Stock de la Renta. (o _ O)',
        'Tipo' => 'error'
      ];
    }
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) No se pudo actualizar los niveles de toner en el equipo. (o _ O)',
      'Tipo' => 'error'
    ];
  }

  // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


function iniciarReporte()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $reporte_estado = limpiarCadena($_POST['reporte_estado']);
  $reporte_fecha = limpiarCadena($_POST['reporte_fecha']);
  $reporte_renta_id = limpiarCadena(decryption($_POST['reporte_renta_id']));
  $reporte_wmakes = ($_POST['reporte_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporte_wmakes']);
  $reporte_reporte = limpiarCadena($_POST['reporte_reporte']);

  if ($reporte_estado != 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Estado del reporte incorrecto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Inicial. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_renta_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar una Renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporte_renta_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $SQL_renta_id_DB = "SELECT * FROM Rentas
                            INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                            INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                            INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                            INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                            WHERE renta_id = '$reporte_renta_id' AND renta_estado = 'Activo'";
    $check_renta_id_db = consultaData($SQL_renta_id_DB);
    if ($check_renta_id_db['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) La renta seleccionada No existe o no se encuentra activa. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $rentaData = $check_renta_id_db['dataFetch'][0];
      $reporte_equipo_id = $rentaData['renta_equipo_id'];
    }
  }

  if ($reporte_reporte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
  $Insert = "INSERT INTO Reportes (reporte_fecha, reporte_renta_id, reporte_wmakes, reporte_equipo_id, reporte_reporte, reporte_estado) VALUES ('$reporte_fecha', '$reporte_renta_id', '$reporte_wmakes', '$reporte_equipo_id', '$reporte_reporte', 0)";
  if (sentenciaData($Insert)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'ReportesR/Activos'
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
}


function ActualizarIniciarReporte()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $reporte_id = limpiarCadena(decryption($_POST['reporte_activo_update']));
  $reporte_fecha = limpiarCadena($_POST['reporte_fecha']);
  $reporte_renta_id = limpiarCadena(decryption($_POST['reporte_renta_id']));
  $reporte_wmakes = ($_POST['reporte_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporte_wmakes']);
  $reporte_reporte = limpiarCadena($_POST['reporte_reporte']);


  if ($reporte_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Reporte no puede estar vacio, recarga la pagina. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporte_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Reporte no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $SQL = "SELECT * FROM Reportes WHERE reporte_id = '$reporte_id'";
    $checkRepActExist = consultaData($SQL);
    if ($checkRepActExist['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El reporte no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }

    $checkRepActEstado = consultaData($SQL .= " AND reporte_estado = '0'");
    if ($checkRepActEstado['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El reporte existe pero el Estatus no corresponde con el tipo de actualizacion. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if ($reporte_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Inicial. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_renta_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar una Renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporte_renta_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $rentaSQL = "SELECT * FROM Rentas
                    INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                    INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                    WHERE renta_id = '$reporte_renta_id' AND renta_estado = 'Activo'";
    $rentaQRY = consultaData($rentaSQL);
    if ($rentaQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) La renta seleccionada No existe o no se encuentra activa. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $reporte_equipo_id = $rentaQRY['dataFetch'][0]['renta_equipo_id'];
    }
  }

  if ($reporte_reporte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
  $update = "UPDATE Reportes SET
                reporte_fecha = '$reporte_fecha',
                reporte_renta_id = '$reporte_renta_id',
                reporte_wmakes = '$reporte_wmakes',
                reporte_equipo_id = '$reporte_equipo_id',
                reporte_reporte = '$reporte_reporte'
                WHERE reporte_id = '$reporte_id'";
  if (sentenciaData($update)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Se actualizo el reporte inicial correctamente. \(- _ -)/',
      'Tipo' => 'success'
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
}


function completarInicioReporte()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $reporte_id = limpiarCadena(decryption($_POST['reporte_activo_completar']));
  $reporte_fecha = limpiarCadena($_POST['reporte_fecha']);
  $reporte_fecha_fin = limpiarCadena($_POST['reporte_fecha_fin']);
  $reporte_fecha_inicio = limpiarCadena($_POST['reporte_fecha_inicio']);
  $reporte_wmakes = ($_POST['reporte_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporte_wmakes']);
  $reporte_renta_id = limpiarCadena(decryption($_POST['reporte_renta_id']));
  $reporte_reporte = limpiarCadena($_POST['reporte_reporte']);
  $reporte_resolucion = limpiarCadena($_POST['reporte_resolucion']);
  $comments = limpiarCadena($_POST['comments']);


  if ($reporte_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Reporte no puede estar vacio, recarga la pagina. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporte_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Reporte no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $SQL = "SELECT * FROM Reportes WHERE reporte_id = '$reporte_id'";
    $checkRepActExist = consultaData($SQL);
    if ($checkRepActExist['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El reporte no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }

    $checkRepActEstado = consultaData($SQL .= " AND reporte_estado = '0'");
    if ($checkRepActEstado['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El reporte existe pero ya se encuentra completado. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if ($reporte_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Inicial. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    list($reporte_anio, $reporte_mes, $reporte_dia) = explode("-", explode("T", $reporte_fecha)[0]);
  }

  if ($reporte_fecha_fin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Final. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_fecha_inicio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha Final. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_renta_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar una Renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporte_renta_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $rentaSQL = "SELECT * FROM Rentas
                    INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                    INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                    WHERE renta_id = '$reporte_renta_id' AND renta_estado = 'Activo'";
    $checkRenta = consultaData($rentaSQL);
    if ($checkRenta['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) La renta seleccionada No existe o no se encuentra activa. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $rentaData = $checkRenta['dataFetch'][0];
      $reporte_equipo_id = $rentaData['renta_equipo_id'];

      $reporte_archivo = $rentaData['cliente_rs'] . " (" . $rentaData['contrato_folio'] . "-" . $rentaData['renta_folio'] . " - " . $rentaData['renta_depto'] . " - " . $rentaData['equipo_serie'] . ")";
    }
  }

  if ($reporte_reporte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporte_resolucion == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso la resolucion. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // --------------------------------------------- Agregamos el Pendiente --------------------------------------------- //
  if ($comments != "") {
  }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el pendiente ^^^ FIN ^^^ --------------------------------- //


  // ---------------------------------------------- DATOS DE CONSUMIBLES ---------------------------------------------- //
  // 1. Definimos los nombres de los colores y sus etiquetas legibles
  $campos = [
    'K' => 'Negro',
    'M' => 'Magenta',
    'C' => 'Cyan',
    'Y' => 'Amarillo',
    'R' => 'Residual'
  ];

  // 2. Procesamos y validamos en un solo ciclo
  foreach ($campos as $key => $label) {
    $inputStock = "renta_stock_{$key}";
    $inputNivel = "equipo_nivel_{$key}";

    // Aquí guardamos el VALOR en variables como $renta_stock_K, $equipo_nivel_K, etc.
    ${$inputStock} = (isset($_POST[$inputStock]) && $_POST[$inputStock] !== "") ? limpiarCadena($_POST[$inputStock]) : "0";
    ${$inputNivel} = (isset($_POST[$inputNivel]) && $_POST[$inputNivel] !== "") ? limpiarCadena($_POST[$inputNivel]) : "0";

    // En este array, la LLAVE es el NOMBRE de la variable que creamos arriba
    $validarEstos = [
      $inputStock => "STOCK color $label",
      $inputNivel => "nivel color $label"
    ];

    foreach ($validarEstos as $nombreVariable => $msg) {
      // IMPORTANTE: Usamos ${$nombreVariable} para acceder al CONTENIDO de la variable
      $valorAValidar = ${$nombreVariable};

      if (verificarDatos("^[0-9]+$", $valorAValidar)) {
        echo json_encode([
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          // Ahora sí, mostrará el valor real que falló para que puedas debuguear
          'Texto' => "(TT _ TT) El valor '$valorAValidar' en $msg no es un numero. (TT _ TT)",
          'Tipo' => 'error'
        ]);
        exit();
      }
    }
  }
  // ---------------------------------- ^^^ FIN ^^^ DATOS DE CONSUMIBLES ^^^ FIN ^^^ ---------------------------------- //

  // --------------------------------------------- Verificando Input File --------------------------------------------- //
  // ***************** Evidencia en JPG ***************** //
  if ($_FILES['reporte_archivo']['name'] == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No Ingresaste la evidencia. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($_FILES['reporte_archivo']['type'] != "application/pdf") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El formato de la evidencia debe ser PDF. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $temp_reporte_archivo = $_FILES['reporte_archivo']['tmp_name'];
  }
  // **************************************************** //
  // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //


  // Intentar guardar el archivo
  $saveDoc = saveReporte($temp_reporte_archivo, $reporte_archivo, $reporte_anio, $reporte_mes, $reporte_dia);

  if (!$saveDoc['status']) {
    echo json_encode([
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) ' . $saveDoc['result'] . '. (TT _ TT)',
      'Tipo' => 'error'
    ]);
    exit();
  }

  $reporte_archivo = $saveDoc['result'];

  // Preparamos el "Plan de Ejecución" en un array
  $sentenciasEjecutar = [
    "(o _ O) No se pudo actualizar los niveles de toner. (o _ O)" =>
    "UPDATE Equipos SET 
      equipo_nivel_K = '$equipo_nivel_K', equipo_nivel_M = '$equipo_nivel_M', 
      equipo_nivel_C = '$equipo_nivel_C', equipo_nivel_Y = '$equipo_nivel_Y', 
      equipo_nivel_R = '$equipo_nivel_R' 
    WHERE equipo_id = '$reporte_equipo_id'",
    "(o _ O) No se pudo actualizar el Stock de la Renta. (o _ O)" =>
    "UPDATE Rentas SET 
      renta_stock_K = '$renta_stock_K', renta_stock_M = '$renta_stock_M', 
      renta_stock_C = '$renta_stock_C', renta_stock_Y = '$renta_stock_Y', 
      renta_stock_R = '$renta_stock_R' 
    WHERE renta_id = '$reporte_renta_id'",
    "(o _ O) No se pudo completar el registro del reporte final. (o _ O)" =>
    "UPDATE Reportes SET
      reporte_estado = 1,
      reporte_fecha = '$reporte_fecha',
      reporte_fecha_inicio = '$reporte_fecha_inicio',
      reporte_fecha_fin = '$reporte_fecha_fin',
      reporte_wmakes = '$reporte_wmakes',
      reporte_renta_id = '$reporte_renta_id',
      reporte_equipo_id = '$reporte_equipo_id',
      reporte_archivo = '$reporte_archivo',
      reporte_reporte = '$reporte_reporte',
      reporte_resolucion = '$reporte_resolucion'
    WHERE reporte_id = '$reporte_id'"
  ];

  // Ejecutamos la transacción
  $resultado = transactionData($sentenciasEjecutar);

  // Manejamos la respuesta única
  if ($resultado['status']) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'ReportesR/Activos',
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error de Sistema',
      'Texto' => $resultado['result'], // Aquí saldrá el mensaje del Exception (la clave del array)
      'Tipo' => 'error'
    ];
  }

  echo json_encode($alerta);
  exit();
}
