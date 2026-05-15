<?php

// ============================ Controladores de Lecturas ============================ //


/* --------------------- Funcion Guardar Archivos PDF --------------------- */
function saveFactura($fechaFact, $archivoTmp, $dataCliente, $noFact)
{
  list($fechaFact_anio, $fechaFact_mes, $fechaFact_dia) = explode("-", $fechaFact);
  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
  $docDir = SERVERDIR . 'DocsCR/Facturas/';
  // +======+ Verificar Carpeta Raiz +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $docDir .=  $fechaFact_anio . '/';
  } else {
    $docDir .=  $fechaFact_anio . '/';
  }
  // +======+ Verificar Carpeta Anio +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $docDir .= $fechaFact_mes . '/';
  } else {
    $docDir .= $fechaFact_mes . '/';
  }
  // +======+ Verificar Carpeta de Formatos +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  }

  $pCh_archivo = $fechaFact_dia . '-' . $fechaFact_mes . '-' . $fechaFact_anio . ' - ' . $dataCliente . ' - Factura No.' . $noFact . '.pdf';

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  if (file_exists($docDir . $pCh_archivo)) {
    $result = [
      'status' => false,
      'result' => 'El archivo, ya existe'
    ];
  } else if (move_uploaded_file($archivoTmp, $docDir . $pCh_archivo)) {
    $result = [
      'status' => true,
      'result' => $pCh_archivo
    ];
  } else {
    $result = [
      'status' => false,
      'result' => 'No se pudo guardar el archivo.'
    ];
  }
  return $result;
} // Fin del la Funcion


function agregarFactura()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $pCh_renta_id = limpiarCadena(decryption($_POST['pCh_renta_id']));
  if ($pCh_renta_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Sin renta reconocida. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]+", $pCh_renta_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Formato de renta incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $rentaSQL = "SELECT * FROM Rentas
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                WHERE renta_id = $pCh_renta_id";
    $rentaQuery = consultaData($rentaSQL);
    if ($rentaQuery['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT_TT) No existe la renta solicitada. (TT_TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $rentaData = $rentaQuery['dataFetch'][0];
      $dataCliente = $rentaData['cliente_rs'] . " (" . $rentaData['cliente_rfc'] . ") - " . $rentaData['contrato_folio'] . "-" . $rentaData['renta_folio'];
    }
  }

  $pCh_fechaFact = limpiarCadena($_POST['pCh_fechaFact']);
  if ($pCh_fechaFact == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Debes ingresar una fecha de factura. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d{4}-\d{2}-\d{2}", $pCh_fechaFact)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Formato de fecha de factura incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCh_noFact = limpiarCadena($_POST['pCh_noFact']);
  $pCh_noFact = strtoupper($_POST['pCh_noFact']);
  if ($pCh_noFact == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ningun numero de factura. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[A-Z0-9]+", $pCh_noFact)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en el nombre de factura es la incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCh_subTFact = limpiarCadena($_POST['pCh_subTFact']);
  if ($pCh_subTFact == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ninguna cantidad en Factura. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^-?\d+(\.\d+)?$", $pCh_subTFact)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en costo de factura es la incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($pCh_subTFact <= 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en costo de factura debe ser mayor a 0. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCh_ivaFact = limpiarCadena($_POST['pCh_ivaFact']);
  if ($pCh_ivaFact == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ninguna cantidad de IVA. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^(\d|[1-9]\d|100)$", $pCh_ivaFact)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en el IVA es incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (isset($_POST['pCh_archivo'])) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'ERROR',
      'Texto' => '(TT _ TT) Input de PDF incorrecto, recarga la pagina. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($_FILES['pCh_archivo']["error"] != 0) {
    if ($_FILES['pCh_archivo']["type"] != "application/pdf") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'ERROR',
        'Texto' => '(TT _ TT) El archivo debe ser un PDF. (TT _ TT)',
        'Tipo' => 'error'
      ];
    } else {
      $phpFileUploadErrors = array(
        1 => 'El archivo cargado excede la directiva upload_max_filesize en php.ini',
        2 => 'El archivo cargado excede la directiva MAX_FILE_SIZE especificada en el formulario HTML',
        3 => 'El archivo cargado solo se cargó parcialmente',
        4 => 'No se cargó ningún archivo',
        6 => 'Falta una carpeta temporal',
        7 => 'No se pudo escribir el archivo en el disco',
        8 => 'Una extensión de PHP detuvo la carga del archivo',
      );
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'ERROR',
        'Texto' => '(TT _ TT) Error al subir el archivo: ' . (in_array($_FILES['pCh_archivo']["error"], $phpFileUploadErrors)) ? $phpFileUploadErrors[$_FILES['pCh_archivo']["error"]] : "Error desconocido." . '. (TT _ TT)',
        'Tipo' => 'error'
      ];
    }
    echo json_encode($alerta);
    exit();
  } else {
    $pCh_archivo_tmp = $_FILES['pCh_archivo']["tmp_name"];
  }

  $saveFactura = saveFactura($pCh_fechaFact, $pCh_archivo_tmp, $dataCliente, $pCh_noFact);
  if ($saveFactura['status']) {
    $pCh_archivo = $saveFactura['result'];
    if (sentenciaData("INSERT INTO payCheck (pCh_renta_id, pCh_fechaFact, pCh_noFact, pCh_subTFact, pCh_ivaFact, pCh_archivo) VALUES ('$pCh_renta_id', '$pCh_fechaFact', '$pCh_noFact', '$pCh_subTFact', '$pCh_ivaFact', '$pCh_archivo')")) {
      $alerta = [
        'Alerta' => 'recargar',
        'Titulo' => 'EXITO',
        'Texto' => '\(U _U) REGISTRO COMPLETADO. \(U _U)',
        'Tipo' => 'success'
      ];
    } else {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT_TT) No se pudo realizar el registro. (TT_TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) ' . $saveFactura['result'] . '. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}

function editarFactura()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $pCh_id = limpiarCadena(decryption($_POST['pCh_modo']));
  if ($pCh_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No hay registro asociado | payCheck. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]+", $pCh_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Formato incorrecto | payCheck_id. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $pChSQL = "SELECT * FROM payCheck
                INNER JOIN Rentas ON payCheck.pCh_renta_id = Rentas.renta_id
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                WHERE pCh_id = $pCh_id";
    $pChQuery = consultaData($pChSQL);
    if ($pChQuery['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT_TT) No existe el registro de factura solicitado. (TT_TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $rentaData = $pChQuery['dataFetch'][0];
      $dataCliente = $rentaData['cliente_rs'] . " (" . $rentaData['cliente_rfc'] . ") - " . $rentaData['contrato_folio'] . "-" . $rentaData['renta_folio'];
    }
  }

  $pCh_fechaFact = limpiarCadena($_POST['pCh_fechaFact']);
  if ($pCh_fechaFact == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Debes ingresar una fecha de factura. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d{4}-\d{2}-\d{2}", $pCh_fechaFact)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Formato de fecha de factura incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCh_noFact = limpiarCadena($_POST['pCh_noFact']);
  $pCh_noFact = strtoupper($_POST['pCh_noFact']);
  if ($pCh_noFact == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ningun numero de factura. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[A-Z0-9]+", $pCh_noFact)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en el nombre de factura es la incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCh_ivaFact = limpiarCadena($_POST['pCh_ivaFact']);
  if ($pCh_ivaFact == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ninguna cantidad de IVA. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^(\d|[1-9]\d|100)$", $pCh_ivaFact)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en el IVA es incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCh_subTFact = limpiarCadena($_POST['pCh_subTFact']);
  if ($pCh_subTFact == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ninguna cantidad en Factura. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^-?\d+(\.\d+)?$", $pCh_subTFact)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en costo de factura es la incorrecta. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($pCh_subTFact <= 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en costo de factura debe ser mayor a 0. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCHSQL = "SELECT * FROM payCheck_pagos WHERE pCH_pCh_id = $pCh_id";
  $pCHQuery = consultaData($pCHSQL);
  if ($pCHQuery['numRows'] > 0) {
    $pCHData = $pCHQuery['dataFetch'];
    $tPagos = 0;
    for ($i = 0; $i < $pCHQuery['numRows']; $i++) {
      $tPagos = $pCHData[$i]['pCH_cantPago'] + $tPagos;
    }
    // IVAs = IVAs / 100;
    // IVAs = IVAs * parseFloat(document.getElementById('pCh_subTFact').value);
    // IVAs = IVAs + parseFloat(document.getElementById('pCh_subTFact').value);
    $IVAs = $pCh_ivaFact / 100;
    $IVAs = $IVAs * $pCh_subTFact;
    $FactMasIVA = $IVAs + $pCh_subTFact;

    if ($tPagos > $FactMasIVA) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT_TT) El TOTAL de pagos realizados SUPERA el total con IVA del monto INGREADO en la factura. (TT_TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  $sqlUpdate = "UPDATE payCheck SET
                pCh_fechaFact = '$pCh_fechaFact',
                pCh_noFact = '$pCh_noFact',
                pCh_subTFact = '$pCh_subTFact',";



  if (isset($_FILES['pCh_archivo'])) {
    if ($_FILES['pCh_archivo']["error"] != 0) {
      if ($_FILES['pCh_archivo']["type"] != "application/pdf") {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'ERROR',
          'Texto' => '(TT _ TT) El archivo debe ser un PDF. (TT _ TT)',
          'Tipo' => 'error'
        ];
      } else {
        $phpFileUploadErrors = array(
          1 => 'El archivo cargado excede la directiva upload_max_filesize en php.ini',
          2 => 'El archivo cargado excede la directiva MAX_FILE_SIZE especificada en el formulario HTML',
          3 => 'El archivo cargado solo se cargó parcialmente',
          4 => 'No se cargó ningún archivo',
          6 => 'Falta una carpeta temporal',
          7 => 'No se pudo escribir el archivo en el disco',
          8 => 'Una extensión de PHP detuvo la carga del archivo',
        );
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'ERROR',
          'Texto' => '(TT _ TT) Error al subir el archivo: ' . (in_array($_FILES['pCh_archivo']["error"], $phpFileUploadErrors)) ? $phpFileUploadErrors[$_FILES['pCh_archivo']["error"]] : "Error desconocido." . '. (TT _ TT)',
          'Tipo' => 'error'
        ];
      }
      echo json_encode($alerta);
      exit();
    } else {
      $pCh_archivo_tmp = $_FILES['pCh_archivo']["tmp_name"];
      $saveFactura = saveFactura($pCh_fechaFact, $pCh_archivo_tmp, $dataCliente, $pCh_noFact);
      if ($saveFactura['status']) {
        $pCh_archivo = $saveFactura['result'];
        $sqlUpdate .= "pCh_archivo = '$pCh_archivo',";
      }
    }
  }



  $sqlUpdate .= "
                pCh_ivaFact = '$pCh_ivaFact'
                WHERE pCh_id = '$pCh_id'";
  if (sentenciaData($sqlUpdate)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'EXITO',
      'Texto' => '\(U _U) REGISTRO ACTUALIZADO. \(U _U)',
      'Tipo' => 'success'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se pudo realizar la actualizacion. (TT_TT)',
      'Tipo' => 'error'
    ];
  }
  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}

function agregarPagoFactura()
{
  $pCh_id = limpiarCadena($_POST['pCh_id']);
  if ($pCh_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Sin registro de factura. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]+", $pCh_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) Formato de registro de factura incorrecto. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pChQuery = consultaData("SELECT * FROM payCheck WHERE pCh_id = $pCh_id");
  if ($pChQuery['numRows'] == 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No existe el registro de factura solicitado. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCH_fechaPago = limpiarCadena($_POST['pCH_fechaPago']);
  if ($pCH_fechaPago == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ninguna fecha del Pago. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d{4}-\d{2}-\d{2}", $pCH_fechaPago)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) El formato de fecha no es el correcto. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCH_cantPago = limpiarCadena($_POST['pCH_cantPago']);
  if ($pCH_cantPago == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ningun monto de Pago. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^-?\d+(\.\d+)?$", $pCH_cantPago)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) El formato del monto no es el correcto. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($pCH_cantPago <= 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) La cantidad ingresada en el pago debe ser mayor a 0. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCH_comm = limpiarCadena($_POST['pCH_comm']);
  if ($pCH_comm == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se ingreso ningun comentario. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $pCh_subTFact = $pChQuery['dataFetch'][0]['pCh_subTFact'];
  $pCh_ivaFact = $pChQuery['dataFetch'][0]['pCh_ivaFact'];
  $IVAs = $pCh_ivaFact / 100;
  $IVAs = $pCh_subTFact * $IVAs;
  $pCh_TFactIVA = $pCh_subTFact + $IVAs;
  $pCH_TotalPagos = $pCH_cantPago;

  $pCHQuery = consultaData("SELECT pCH_cantPago FROM payCheck_pagos WHERE pCH_pCh_id = $pCh_id");
  if ($pCHQuery['numRows'] >= 1) {
    $pCH_TotalPagos = 0;
    for ($i = 0; $i < $pCHQuery['numRows']; $i++) {
      $pCH_TotalPagos = $pCHQuery['dataFetch'][$i]['pCH_cantPago'] + $pCH_TotalPagos;
    }
    $pCH_TotalPagos = $pCH_TotalPagos + $pCH_cantPago;
  }

  if ($pCH_TotalPagos > $pCh_TFactIVA) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) El monto de pagos supera el monto de la factura con IVA. (TT_TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($pCH_TotalPagos == $pCh_TFactIVA) {
    if (sentenciaData("UPDATE payCheck SET pCh_status = 2 WHERE pCH_id = '$pCh_id'") == FALSE) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT_TT) No se pudo actualizar el estatus de la factura pagada. (TT_TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if (sentenciaData("INSERT INTO payCheck_pagos (pCH_pCh_id, pCH_fechaPago, pCH_cantPago, pCH_comm) VALUES ('$pCh_id', '$pCH_fechaPago', '$pCH_cantPago', '$pCH_comm')")) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'EXITO',
      'Texto' => '/(U_ u)/ Pago agregado correctamente. /(U_ u)/',
      'Tipo' => 'success'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se pudo realizar el pago. (TT_TT)',
      'Tipo' => 'error'
    ];
  }


  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}
