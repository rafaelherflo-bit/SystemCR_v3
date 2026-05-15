<?php

// ============================ CONTROLADOR DE REPORTES FORANEOS ============================ //


/* --------------------- Funcion Guardar Reporte Foraneo PDF --------------------- */
function saveReporteF($temp_reporte_archivo, $nameDoc, $fecha_anio, $fecha_mes)
{

  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
  $docDir = SERVERDIR . 'DocsCR/ReportesF/';
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

  $archivo = $nameDoc . '.pdf';

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


function agregarReporteForaneo()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $reporteF_estado = limpiarCadena($_POST['reporteF_estado']);
  $reporteF_cliente_id = limpiarCadena(decryption($_POST['reporteF_cliente_id']));
  $reporteF_equ_modelo_id = limpiarCadena(decryption($_POST['reporteF_equ_modelo_id']));
  $reporteF_equ_serie = limpiarCadena($_POST['reporteF_equ_serie']);
  $reporteF_equ_estado = limpiarCadena($_POST['reporteF_equ_estado']);
  $reporteF_fecha = limpiarCadena($_POST['reporteF_fecha']);
  $reporteF_fecha_inicio = limpiarCadena($_POST['reporteF_fecha_inicio']);
  $reporteF_fecha_fin = limpiarCadena($_POST['reporteF_fecha_fin']);
  $reporteF_wmakes = ($_POST['reporteF_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporteF_wmakes']);
  $reporteF_reporte = limpiarCadena($_POST['reporteF_reporte']);
  $reporteF_resolucion = limpiarCadena($_POST['reporteF_resolucion']);

  if ($reporteF_estado != 1) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Estado del reporte incorrecto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha de Reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    list($reporteF_anio, $reporteF_mes, $reporteF_dia) = explode("-", explode("T", $reporteF_fecha)[0]);
  }

  if ($reporteF_fecha_inicio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha de Inicio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (dateTimeCompare($reporteF_fecha_inicio, "menor", $reporteF_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Fecha de Inicio no puede ser menor que a Fecha de Reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_fecha_fin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha de finalizacion. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (dateTimeCompare($reporteF_fecha_fin, "igualOmenor", $reporteF_fecha_inicio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Fecha Final no puede ser menor o igual que a Fecha Inicial. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_cliente_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar un Cliente. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_cliente_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El cliente ingresado no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $clienteIdSQL = "SELECT * FROM Clientes WHERE cliente_id = '$reporteF_cliente_id'";
    $clienteQRY = consultaData($clienteIdSQL);
    if ($clienteQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El cliente seleccionado No existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $clienteData = $clienteQRY['dataFetch'][0];
    }
  }

  if ($reporteF_equ_serie == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El numero de serie del equipo no puede estar vacio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($reporteF_equ_serie) < 8 || strlen($reporteF_equ_serie) > 15) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La longitud del numero de serie del equipo no es el correcto debe ser mayor a 8 y menor a 15. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_equ_modelo_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar un Modelo de Equipo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_equ_modelo_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El modelo de equipo ingresado no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $modeloIdSQL = "SELECT * FROM Modelos WHERE modelo_id = '$reporteF_equ_modelo_id'";
    $modeloQRY = consultaData($modeloIdSQL);
    if ($modeloQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El modelo de equipo seleccionado No existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $ModeloData = $clienteQRY['dataFetch'][0];
    }
  }

  if ($reporteF_equ_estado != 1 && $reporteF_equ_estado != 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Estado del Equipo Incorrecto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // --------------------------------------------- Conseguimos un Folio de Contrato disponible --------------------------------- //
  for ($reporteF_folio = "RF0000000";; $reporteF_folio++) {
    $check_folio = consultaData("SELECT reporteF_folio FROM ReportesF WHERE reporteF_folio = '$reporteF_folio'");
    if ($check_folio['numRows'] == 0) {
      break;
    }
  }
  // --------------------------------------------------------------------------------------------------------------------------- //


  if ($reporteF_reporte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_resolucion == "") {
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
  // if ($comments != "") {
  // }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el pendiente ^^^ FIN ^^^ --------------------------------- //

  // Datos de Contadores --------------------------------------------------
  $reporteF_esc_ini = limpiarCadena($_POST['reporteF_esc_ini']);
  $reporteF_esc_fin = limpiarCadena($_POST['reporteF_esc_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_esc_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de Escaneo Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_esc_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de Escaneo Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_bn_ini = limpiarCadena($_POST['reporteF_bn_ini']);
  $reporteF_bn_fin = limpiarCadena($_POST['reporteF_bn_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_bn_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de B&N Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_bn_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de B&N Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_col_ini = limpiarCadena($_POST['reporteF_col_ini']);
  $reporteF_col_fin = limpiarCadena($_POST['reporteF_col_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_col_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de Color Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_col_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de Color Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // ----------------------------------------------------------------------

  // Datos de Consumibles --------------------------------------------------
  $reporteF_nivelK_ini = limpiarCadena($_POST['reporteF_nivelK_ini']);
  $reporteF_nivelK_fin = limpiarCadena($_POST['reporteF_nivelK_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelK_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Negro Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelK_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Negro Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_nivelM_ini = limpiarCadena($_POST['reporteF_nivelM_ini']);
  $reporteF_nivelM_fin = limpiarCadena($_POST['reporteF_nivelM_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelM_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Magenta Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelM_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Magenta Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_nivelC_ini = limpiarCadena($_POST['reporteF_nivelC_ini']);
  $reporteF_nivelC_fin = limpiarCadena($_POST['reporteF_nivelC_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelC_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Cyan Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelC_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Cyan Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_nivelY_ini = limpiarCadena($_POST['reporteF_nivelY_ini']);
  $reporteF_nivelY_fin = limpiarCadena($_POST['reporteF_nivelY_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelY_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Amarillo Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelY_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Amarillo Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_nivelR_ini = limpiarCadena($_POST['reporteF_nivelR_ini']);
  $reporteF_nivelR_fin = limpiarCadena($_POST['reporteF_nivelR_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelR_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel del Contenedor Residual Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelR_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel del Contenedor Residual Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // ----------------------------------------------------------------------


  // --------------------------------------------- Verificando Input File --------------------------------------------- //
  // ***************** Evidencia en JPG ***************** //
  if ($_FILES['reporteF_archivo']['name'] == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No Ingresaste la evidencia. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($_FILES['reporteF_archivo']['type'] != "application/pdf") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El formato de la evidencia debe ser PDF. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $temp_reporteF_archivo = $_FILES['reporteF_archivo']['tmp_name'];
  }
  // **************************************************** //
  // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //


  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //

  $saveDoc = saveReporteF($temp_reporteF_archivo, $reporteF_folio, $reporteF_anio, $reporteF_mes);
  if ($saveDoc['status']) {
    $insertReporteF = "INSERT INTO ReportesF (reporteF_fecha, reporteF_folio, reporteF_fecha_fin, reporteF_fecha_inicio, reporteF_estado, reporteF_wmakes, reporteF_cliente_id, reporteF_equ_serie, reporteF_equ_estado, reporteF_equ_modelo_id, reporteF_esc_ini, reporteF_bn_ini, reporteF_col_ini, reporteF_esc_fin, reporteF_bn_fin, reporteF_col_fin, reporteF_nivelK_ini, reporteF_nivelM_ini, reporteF_nivelC_ini, reporteF_nivelY_ini, reporteF_nivelR_ini, reporteF_nivelK_fin, reporteF_nivelM_fin, reporteF_nivelC_fin, reporteF_nivelY_fin, reporteF_nivelR_fin, reporteF_reporte, reporteF_resolucion) VALUES ('$reporteF_fecha', '$reporteF_folio', '$reporteF_fecha_fin', '$reporteF_fecha_inicio', '$reporteF_estado', '$reporteF_wmakes', '$reporteF_cliente_id', '$reporteF_equ_serie', '$reporteF_equ_estado', '$reporteF_equ_modelo_id', '$reporteF_esc_ini', '$reporteF_bn_ini', '$reporteF_col_ini', '$reporteF_esc_fin', '$reporteF_bn_fin', '$reporteF_col_fin', '$reporteF_nivelK_ini', '$reporteF_nivelM_ini', '$reporteF_nivelC_ini', '$reporteF_nivelY_ini', '$reporteF_nivelR_ini', '$reporteF_nivelK_fin', '$reporteF_nivelM_fin', '$reporteF_nivelC_fin', '$reporteF_nivelY_fin', '$reporteF_nivelR_fin', '$reporteF_reporte', '$reporteF_resolucion')";
    if (sentenciaData($insertReporteF)) {
      $alerta = [
        'Alerta' => 'redireccionar',
        'url' => SERVERURL . 'ReportesF/CustomMonth/' . date("Y") . '/' . ucfirst(dateFormat(date("Y") . "-" . date("n") . "-" . date("d"), "mesL"))
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
      'Texto' => '(TT _ TT) ' . $saveDoc['result'] . '. (TT _ TT)',
      'Tipo' => 'error'
    ];
  }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


function actualizarReporteForaneo()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $reporteF_id = limpiarCadena(decryption($_POST['reporteF_completo_update']));
  $reporteF_estado = limpiarCadena($_POST['reporteF_estado']);
  $reporteF_cliente_id = limpiarCadena(decryption($_POST['reporteF_cliente_id']));
  $reporteF_equ_modelo_id = limpiarCadena(decryption($_POST['reporteF_equ_modelo_id']));
  $reporteF_equ_serie = limpiarCadena($_POST['reporteF_equ_serie']);
  $reporteF_equ_estado = limpiarCadena($_POST['reporteF_equ_estado']);
  $reporteF_fecha = limpiarCadena($_POST['reporteF_fecha']);
  $reporteF_fecha_inicio = limpiarCadena($_POST['reporteF_fecha_inicio']);
  $reporteF_fecha_fin = limpiarCadena($_POST['reporteF_fecha_fin']);
  $reporteF_wmakes = ($_POST['reporteF_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporteF_wmakes']);
  $reporteF_reporte = limpiarCadena($_POST['reporteF_reporte']);
  $reporteF_resolucion = limpiarCadena($_POST['reporteF_resolucion']);

  if ($reporteF_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar un Cliente. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El cliente ingresado no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $reporteFIdSQL = "SELECT * FROM ReportesF
    INNER JOIN Clientes ON ReportesF.reporteF_cliente_id = Clientes.cliente_id
    INNER JOIN Modelos ON ReportesF.reporteF_equ_modelo_id = Modelos.modelo_id
    WHERE reporteF_id = '$reporteF_id'";
    $reporteFQRY = consultaData($reporteFIdSQL);
    if ($reporteFQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El Reporte Foraneo seleccionado No existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $reporteFData = $reporteFQRY['dataFetch'][0];
    }
  }

  if ($reporteF_estado != 1) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Estado del reporte incorrecto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha de Reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    list($reporteF_anio, $reporteF_mes, $reporteF_dia) = explode("-", explode("T", $reporteF_fecha)[0]);
  }

  if ($reporteF_fecha_inicio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha de Inicio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (dateTimeCompare($reporteF_fecha_inicio, "menor", $reporteF_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Fecha de Inicio no puede ser menor que a Fecha de Reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_fecha_fin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso Fecha de finalizacion. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (dateTimeCompare($reporteF_fecha_fin, "igualOmenor", $reporteF_fecha_inicio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Fecha Final no puede ser menor o igual que a Fecha Inicial. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_cliente_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar un Cliente. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_cliente_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El cliente ingresado no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $clienteIdSQL = "SELECT * FROM Clientes WHERE cliente_id = '$reporteF_cliente_id'";
    $clienteQRY = consultaData($clienteIdSQL);
    if ($clienteQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El cliente seleccionado No existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $clienteData = $clienteQRY['dataFetch'][0];
    }
  }

  if ($reporteF_equ_serie == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El numero de serie del equipo no puede estar vacio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($reporteF_equ_serie) < 8 || strlen($reporteF_equ_serie) > 15) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La longitud del numero de serie del equipo no es el correcto debe ser mayor a 8 y menor a 15. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_equ_modelo_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes seleccionar un Modelo de Equipo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_equ_modelo_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El modelo de equipo ingresado no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $modeloIdSQL = "SELECT * FROM Modelos WHERE modelo_id = '$reporteF_equ_modelo_id'";
    $modeloQRY = consultaData($modeloIdSQL);
    if ($modeloQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El modelo de equipo seleccionado No existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $ModeloData = $clienteQRY['dataFetch'][0];
    }
  }

  if ($reporteF_equ_estado != 1 && $reporteF_equ_estado != 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Estado del Equipo Incorrecto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($reporteF_reporte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($reporteF_resolucion == "") {
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
  // if ($comments != "") {
  // }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el pendiente ^^^ FIN ^^^ --------------------------------- //

  // Datos de Contadores --------------------------------------------------
  $reporteF_esc_ini = limpiarCadena($_POST['reporteF_esc_ini']);
  $reporteF_esc_fin = limpiarCadena($_POST['reporteF_esc_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_esc_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de Escaneo Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_esc_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de Escaneo Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_bn_ini = limpiarCadena($_POST['reporteF_bn_ini']);
  $reporteF_bn_fin = limpiarCadena($_POST['reporteF_bn_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_bn_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de B&N Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_bn_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de B&N Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_col_ini = limpiarCadena($_POST['reporteF_col_ini']);
  $reporteF_col_fin = limpiarCadena($_POST['reporteF_col_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_col_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de Color Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_col_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Contador de Color Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // ----------------------------------------------------------------------

  // Datos de Consumibles --------------------------------------------------
  $reporteF_nivelK_ini = limpiarCadena($_POST['reporteF_nivelK_ini']);
  $reporteF_nivelK_fin = limpiarCadena($_POST['reporteF_nivelK_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelK_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Negro Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelK_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Negro Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_nivelM_ini = limpiarCadena($_POST['reporteF_nivelM_ini']);
  $reporteF_nivelM_fin = limpiarCadena($_POST['reporteF_nivelM_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelM_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Magenta Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelM_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Magenta Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_nivelC_ini = limpiarCadena($_POST['reporteF_nivelC_ini']);
  $reporteF_nivelC_fin = limpiarCadena($_POST['reporteF_nivelC_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelC_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Cyan Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelC_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Cyan Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_nivelY_ini = limpiarCadena($_POST['reporteF_nivelY_ini']);
  $reporteF_nivelY_fin = limpiarCadena($_POST['reporteF_nivelY_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelY_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Amarillo Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelY_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel de Color Amarillo Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $reporteF_nivelR_ini = limpiarCadena($_POST['reporteF_nivelR_ini']);
  $reporteF_nivelR_fin = limpiarCadena($_POST['reporteF_nivelR_fin']);
  if (verificarDatos("^[0-9]+$", $reporteF_nivelR_ini)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel del Contenedor Residual Inicial, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $reporteF_nivelR_fin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Nivel del Contenedor Residual Final, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // ----------------------------------------------------------------------


  // --------------------------------------------- Verificando Input File --------------------------------------------- //
  // ***************** Evidencia en PDF ***************** //
  if (isset($_FILES['reporteF_archivo'])) {
    if ($_FILES['reporteF_archivo']['name'] == "") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) No Ingresaste la evidencia. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($_FILES['reporteF_archivo']['type'] != "application/pdf") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El formato de la evidencia debe ser PDF. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $temp_reporteF_archivo = $_FILES['reporteF_archivo']['tmp_name'];
    }

    $saveDoc = saveReporteF($temp_reporteF_archivo, $reporteFData['reporteF_folio'], $reporteF_anio, $reporteF_mes);
    if ($saveDoc['status'] == FALSE) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) ' . $saveDoc['result'] . '. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }
  // **************************************************** //
  // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //


  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
  $updateReporteFSQL = "UPDATE ReportesF SET
                        reporteF_fecha = '$reporteF_fecha',
                        reporteF_fecha_fin = '$reporteF_fecha_fin',
                        reporteF_fecha_inicio = '$reporteF_fecha_inicio',
                        reporteF_estado = '$reporteF_estado',
                        reporteF_wmakes = '$reporteF_wmakes',
                        reporteF_cliente_id = '$reporteF_cliente_id',
                        reporteF_equ_serie = '$reporteF_equ_serie',
                        reporteF_equ_estado = '$reporteF_equ_estado',
                        reporteF_equ_modelo_id = '$reporteF_equ_modelo_id',
                        reporteF_esc_ini = '$reporteF_esc_ini',
                        reporteF_bn_ini = '$reporteF_bn_ini',
                        reporteF_col_ini = '$reporteF_col_ini',
                        reporteF_esc_fin = '$reporteF_esc_fin',
                        reporteF_bn_fin = '$reporteF_bn_fin',
                        reporteF_col_fin = '$reporteF_col_fin',
                        reporteF_nivelK_ini = '$reporteF_nivelK_ini',
                        reporteF_nivelM_ini = '$reporteF_nivelM_ini',
                        reporteF_nivelC_ini = '$reporteF_nivelC_ini',
                        reporteF_nivelY_ini = '$reporteF_nivelY_ini',
                        reporteF_nivelR_ini = '$reporteF_nivelR_ini',
                        reporteF_nivelK_fin = '$reporteF_nivelK_fin',
                        reporteF_nivelM_fin = '$reporteF_nivelM_fin',
                        reporteF_nivelC_fin = '$reporteF_nivelC_fin',
                        reporteF_nivelY_fin = '$reporteF_nivelY_fin',
                        reporteF_nivelR_fin = '$reporteF_nivelR_fin',
                        reporteF_reporte = '$reporteF_reporte',
                        reporteF_resolucion = '$reporteF_resolucion'
                        WHERE reporteF_id = '$reporteF_id'";
  if (sentenciaData($updateReporteFSQL)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Se actualizo el Reporte Foraneo correctamente. \(- _ -)/',
      'Tipo' => 'success'
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


// function iniciarReporte()
// {
//   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
//   $reporte_estado = limpiarCadena($_POST['reporte_estado']);
//   $reporte_fecha = limpiarCadena($_POST['reporte_fecha']);
//   $reporte_renta_id = limpiarCadena(decryption($_POST['reporte_renta_id']));
//   $reporte_wmakes = ($_POST['reporte_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporte_wmakes']);
//   $reporte_reporte = limpiarCadena($_POST['reporte_reporte']);

//   if ($reporte_estado != 0) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Estado del reporte incorrecto. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($reporte_fecha == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso Fecha Inicial. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($reporte_renta_id == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Debes seleccionar una Renta. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^[0-9]+$", $reporte_renta_id)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $SQL_renta_id_DB = "SELECT * FROM Rentas
//                             INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
//                             INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
//                             INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
//                             INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
//                             WHERE renta_id = '$reporte_renta_id' AND renta_estado = 'Activo'";
//     $check_renta_id_db = consultaData($SQL_renta_id_DB);
//     if ($check_renta_id_db['numRows'] == 0) {
//       $alerta = [
//         'Alerta' => 'simple',
//         'Titulo' => 'Ocurrio un Error inesperado',
//         'Texto' => '(TT _ TT) La renta seleccionada No existe o no se encuentra activa. (TT _ TT)',
//         'Tipo' => 'error'
//       ];
//       echo json_encode($alerta);
//       exit();
//     } else {
//       $rentaData = $check_renta_id_db['dataFetch'][0];
//       $reporte_equipo_id = $rentaData['renta_equipo_id'];
//     }
//   }

//   if ($reporte_reporte == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }


//   // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
//   $Insert = "INSERT INTO Reportes (reporte_fecha, reporte_renta_id, reporte_wmakes, reporte_equipo_id, reporte_reporte, reporte_estado) VALUES ('$reporte_fecha', '$reporte_renta_id', '$reporte_wmakes', '$reporte_equipo_id', '$reporte_reporte', 0)";
//   if (sentenciaData($Insert)) {
//     $alerta = [
//       'Alerta' => 'redireccionar',
//       'url' => SERVERURL . 'Reportes/Activos'
//     ];
//   } else {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(o _ O) No se pudo realizar el registro. (o _ O)',
//       'Tipo' => 'error'
//     ];
//   }
//   // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

//   // Lanzamos La respuesta Final //
//   echo json_encode($alerta);
//   exit();
// }


// function ActualizarIniciarReporte()
// {
//   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
//   $reporte_id = limpiarCadena(decryption($_POST['reporte_activo_update']));
//   $reporte_fecha = limpiarCadena($_POST['reporte_fecha']);
//   $reporte_renta_id = limpiarCadena(decryption($_POST['reporte_renta_id']));
//   $reporte_wmakes = ($_POST['reporte_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporte_wmakes']);
//   $reporte_reporte = limpiarCadena($_POST['reporte_reporte']);


//   if ($reporte_id == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El ID de Reporte no puede estar vacio, recarga la pagina. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^[0-9]+$", $reporte_id)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El ID de Reporte no tiene el formato solicitado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $SQL = "SELECT * FROM Reportes WHERE reporte_id = '$reporte_id'";
//     $checkRepActExist = consultaData($SQL);
//     if ($checkRepActExist['numRows'] == 0) {
//       $alerta = [
//         'Alerta' => 'simple',
//         'Titulo' => 'Ocurrio un Error inesperado',
//         'Texto' => '(TT _ TT) El reporte no existe. (TT _ TT)',
//         'Tipo' => 'error'
//       ];
//       echo json_encode($alerta);
//       exit();
//     }

//     $checkRepActEstado = consultaData($SQL .= " AND reporte_estado = '0'");
//     if ($checkRepActEstado['numRows'] == 0) {
//       $alerta = [
//         'Alerta' => 'simple',
//         'Titulo' => 'Ocurrio un Error inesperado',
//         'Texto' => '(TT _ TT) El reporte existe pero el Estatus no corresponde con el tipo de actualizacion. (TT _ TT)',
//         'Tipo' => 'error'
//       ];
//       echo json_encode($alerta);
//       exit();
//     }
//   }

//   if ($reporte_fecha == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso Fecha Inicial. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($reporte_renta_id == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Debes seleccionar una Renta. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^[0-9]+$", $reporte_renta_id)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $rentaSQL = "SELECT * FROM Rentas
//                     INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
//                     INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
//                     INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
//                     INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
//                     WHERE renta_id = '$reporte_renta_id' AND renta_estado = 'Activo'";
//     $rentaQRY = consultaData($rentaSQL);
//     if ($rentaQRY['numRows'] == 0) {
//       $alerta = [
//         'Alerta' => 'simple',
//         'Titulo' => 'Ocurrio un Error inesperado',
//         'Texto' => '(TT _ TT) La renta seleccionada No existe o no se encuentra activa. (TT _ TT)',
//         'Tipo' => 'error'
//       ];
//       echo json_encode($alerta);
//       exit();
//     } else {
//       $reporte_equipo_id = $rentaQRY['dataFetch'][0]['renta_equipo_id'];
//     }
//   }

//   if ($reporte_reporte == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }


//   // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
//   $update = "UPDATE Reportes SET
//                 reporte_fecha = '$reporte_fecha',
//                 reporte_renta_id = '$reporte_renta_id',
//                 reporte_wmakes = '$reporte_wmakes',
//                 reporte_equipo_id = '$reporte_equipo_id',
//                 reporte_reporte = '$reporte_reporte'
//                 WHERE reporte_id = '$reporte_id'";
//   if (sentenciaData($update)) {
//     $alerta = [
//       'Alerta' => 'recargar',
//       'Titulo' => 'Registro Completado',
//       'Texto' => '\(- _ -)/ Se actualizo el reporte inicial correctamente. \(- _ -)/',
//       'Tipo' => 'success'
//     ];
//   } else {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(o _ O) No se pudo actualizar el registro. (o _ O)',
//       'Tipo' => 'error'
//     ];
//   }
//   // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

//   // Lanzamos La respuesta Final //
//   echo json_encode($alerta);
//   exit();
// }


// function completarInicioReporte()
// {
//   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
//   $reporte_id = limpiarCadena(decryption($_POST['reporte_activo_completar']));
//   $reporte_fecha = limpiarCadena($_POST['reporte_fecha']);
//   $reporte_fecha_fin = limpiarCadena($_POST['reporte_fecha_fin']);
//   $reporte_fecha_inicio = limpiarCadena($_POST['reporte_fecha_inicio']);
//   $reporte_wmakes = ($_POST['reporte_wmakes'] == "") ? 0 : limpiarCadena($_POST['reporte_wmakes']);
//   $reporte_renta_id = limpiarCadena(decryption($_POST['reporte_renta_id']));
//   $reporte_reporte = limpiarCadena($_POST['reporte_reporte']);
//   $reporte_resolucion = limpiarCadena($_POST['reporte_resolucion']);
//   $comments = limpiarCadena($_POST['comments']);


//   if ($reporte_id == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El ID de Reporte no puede estar vacio, recarga la pagina. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^[0-9]+$", $reporte_id)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El ID de Reporte no tiene el formato solicitado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $SQL = "SELECT * FROM Reportes WHERE reporte_id = '$reporte_id'";
//     $checkRepActExist = consultaData($SQL);
//     if ($checkRepActExist['numRows'] == 0) {
//       $alerta = [
//         'Alerta' => 'simple',
//         'Titulo' => 'Ocurrio un Error inesperado',
//         'Texto' => '(TT _ TT) El reporte no existe. (TT _ TT)',
//         'Tipo' => 'error'
//       ];
//       echo json_encode($alerta);
//       exit();
//     }

//     $checkRepActEstado = consultaData($SQL .= " AND reporte_estado = '0'");
//     if ($checkRepActEstado['numRows'] == 0) {
//       $alerta = [
//         'Alerta' => 'simple',
//         'Titulo' => 'Ocurrio un Error inesperado',
//         'Texto' => '(TT _ TT) El reporte existe pero ya se encuentra completado. (TT _ TT)',
//         'Tipo' => 'error'
//       ];
//       echo json_encode($alerta);
//       exit();
//     }
//   }

//   if ($reporte_fecha == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso Fecha Inicial. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     list($reporte_anio, $reporte_mes, $reporte_dia) = explode("-", explode("T", $reporte_fecha)[0]);
//   }

//   if ($reporte_fecha_fin == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso Fecha Final. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($reporte_fecha_inicio == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso Fecha Final. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($reporte_renta_id == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Debes seleccionar una Renta. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^[0-9]+$", $reporte_renta_id)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $rentaSQL = "SELECT * FROM Rentas
//                     INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
//                     INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
//                     INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
//                     INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
//                     WHERE renta_id = '$reporte_renta_id' AND renta_estado = 'Activo'";
//     $checkRenta = consultaData($rentaSQL);
//     if ($checkRenta['numRows'] == 0) {
//       $alerta = [
//         'Alerta' => 'simple',
//         'Titulo' => 'Ocurrio un Error inesperado',
//         'Texto' => '(TT _ TT) La renta seleccionada No existe o no se encuentra activa. (TT _ TT)',
//         'Tipo' => 'error'
//       ];
//       echo json_encode($alerta);
//       exit();
//     } else {
//       $rentaData = $checkRenta['dataFetch'][0];
//       $reporte_equipo_id = $rentaData['renta_equipo_id'];

//       $reporte_archivo = $rentaData['cliente_rs'] . " (" . $rentaData['contrato_folio'] . "-" . $rentaData['renta_folio'] . " - " . $rentaData['renta_depto'] . " - " . $rentaData['equipo_serie'] . ")";
//     }
//   }

//   if ($reporte_reporte == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso el reporte. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($reporte_resolucion == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No se ingreso la resolucion. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   // --------------------------------------------- Agregamos el Pendiente --------------------------------------------- //
//   if ($comments != "") {
//   }
//   // --------------------------------- ^^^ FIN ^^^ Agregamos el pendiente ^^^ FIN ^^^ --------------------------------- //


//   // ---------------------------------------------- DATOS DE CONSUMIBLES ---------------------------------------------- //
//   $renta_stock_K = limpiarCadena($_POST['renta_stock_K']);
//   if (verificarDatos("^[0-9]+$", $renta_stock_K)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en STOCK color Negro, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   $renta_stock_M = limpiarCadena($_POST['renta_stock_M']);
//   if (verificarDatos("^[0-9]+$", $renta_stock_M)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en STOCK color Magenta, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   $renta_stock_C = limpiarCadena($_POST['renta_stock_C']);
//   if (verificarDatos("^[0-9]+$", $renta_stock_C)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en STOCK color Cyan, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   $renta_stock_Y = limpiarCadena($_POST['renta_stock_Y']);
//   if (verificarDatos("^[0-9]+$", $renta_stock_Y)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en STOCK color Amarillo, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   $renta_stock_R = limpiarCadena($_POST['renta_stock_R']);
//   if (verificarDatos("^[0-9]+$", $renta_stock_R)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en STOCK Residual, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   $equipo_nivel_K = limpiarCadena($_POST['equipo_nivel_K']);
//   if (verificarDatos("^[0-9]+$", $equipo_nivel_K)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en nivel color Negro, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   $equipo_nivel_M = limpiarCadena($_POST['equipo_nivel_M']);
//   if (verificarDatos("^[0-9]+$", $equipo_nivel_M)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en nivel color Magenta, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   $equipo_nivel_C = limpiarCadena($_POST['equipo_nivel_C']);
//   if (verificarDatos("^[0-9]+$", $equipo_nivel_C)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en nivel color Cyan, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   $equipo_nivel_Y = limpiarCadena($_POST['equipo_nivel_Y']);
//   if (verificarDatos("^[0-9]+$", $equipo_nivel_Y)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en nivel color Amarillo, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   $equipo_nivel_R = limpiarCadena($_POST['equipo_nivel_R']);
//   if (verificarDatos("^[0-9]+$", $equipo_nivel_R)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El contador ingresado en nivel Residual, no es un numero, revisa el formato ingresado. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }
//   // ---------------------------------- ^^^ FIN ^^^ DATOS DE CONSUMIBLES ^^^ FIN ^^^ ---------------------------------- //

//   // --------------------------------------------- Verificando Input File --------------------------------------------- //
//   // ***************** Evidencia en JPG ***************** //
//   if ($_FILES['reporte_archivo']['name'] == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No Ingresaste la evidencia. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if ($_FILES['reporte_archivo']['type'] != "application/pdf") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El formato de la evidencia debe ser PDF. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $temp_reporte_archivo = $_FILES['reporte_archivo']['tmp_name'];
//   }
//   // **************************************************** //
//   // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //


//   // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //

//   $saveDoc = saveReporte($temp_reporte_archivo, $reporte_archivo, $reporte_anio, $reporte_mes, $reporte_dia);
//   if ($saveDoc['status']) {
//     $reporte_archivo = $saveDoc['result'];
//     $updateNiveles = "UPDATE Equipos SET equipo_nivel_K = '$equipo_nivel_K', equipo_nivel_M = '$equipo_nivel_M', equipo_nivel_C = '$equipo_nivel_C', equipo_nivel_Y = '$equipo_nivel_Y', equipo_nivel_R = '$equipo_nivel_R' WHERE equipo_id = '$reporte_equipo_id'";
//     if (sentenciaData($updateNiveles)) {
//       $updateStock = "UPDATE Rentas SET renta_stock_K = '$renta_stock_K', renta_stock_M = '$renta_stock_M', renta_stock_C = '$renta_stock_C', renta_stock_Y = '$renta_stock_Y', renta_stock_R = '$renta_stock_R' WHERE renta_id = '$reporte_renta_id'";
//       if (sentenciaData($updateStock)) {
//         $Update = "UPDATE Reportes SET
//                   reporte_estado = 1,
//                   reporte_fecha = '$reporte_fecha',
//                   reporte_fecha_inicio = '$reporte_fecha_inicio',
//                   reporte_fecha_fin = '$reporte_fecha_fin',
//                   reporte_wmakes = '$reporte_wmakes',
//                   reporte_renta_id = '$reporte_renta_id',
//                   reporte_equipo_id = '$reporte_equipo_id',
//                   reporte_archivo = '$reporte_archivo',
//                   reporte_reporte = '$reporte_reporte',
//                   reporte_resolucion = '$reporte_resolucion'
//                   WHERE reporte_id = '$reporte_id'";
//         if (sentenciaData($Update)) {
//           $alerta = [
//             'Alerta' => 'redireccionar',
//             'url' => SERVERURL . 'Reportes/Activos',
//           ];
//         } else {
//           $alerta = [
//             'Alerta' => 'simple',
//             'Titulo' => 'Ocurrio un Error inesperado',
//             'Texto' => '(o _ O) No se pudo realizar la operacion. (o _ O)',
//             'Tipo' => 'error'
//           ];
//         }
//       } else {
//         $alerta = [
//           'Alerta' => 'simple',
//           'Titulo' => 'Ocurrio un Error inesperado',
//           'Texto' => '(o _ O) No se pudo actualizar El Stock de la Renta. (o _ O)',
//           'Tipo' => 'error'
//         ];
//       }
//     } else {
//       $alerta = [
//         'Alerta' => 'simple',
//         'Titulo' => 'Ocurrio un Error inesperado',
//         'Texto' => '(o _ O) No se pudo actualizar los niveles de toner en el equipo. (o _ O)',
//         'Tipo' => 'error'
//       ];
//     }
//   } else {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) ' . $saveDoc['result'] . '. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//   }
//   // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

//   // Lanzamos La respuesta Final //
//   echo json_encode($alerta);
//   exit();
// }
