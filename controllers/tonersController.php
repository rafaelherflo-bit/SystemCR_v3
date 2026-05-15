<?php

// ============================ Entrada de Nuevo toner ============================ //
function entradaNewToner()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $toner_codigo = limpiarCadena($_POST['toner_codigo_add']);
  $tonerR_fecha = limpiarCadena($_POST['tonerR_fecha']);
  $toner_stock = limpiarCadena($_POST['toner_stock']);
  $toner_parte = limpiarCadena($_POST['toner_parte']);
  $toner_rendi = limpiarCadena($_POST['toner_rendi']);
  $tonerR_comm = limpiarCadena($_POST['tonerR_comm']);
  $toner_tipo = limpiarCadena($_POST['toner_tipo']);
  $toner_marca = limpiarCadena($_POST['toner_marca']);
  $provT_id = limpiarCadena($_POST['toner_provT_id']);
  $toner_comp = limpiarCadena($_POST['toner_comp']);


  if ($tonerR_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso fecha. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $tonerR_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ La fecha no tiene el formato adecuado (ej. 2025-01-13). \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_parte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ningun numero de parte. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_rendi == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ninguna cantidad de rendimiento. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_codigo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ningun codigo de toner. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{3,5})$", $toner_codigo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El codigo deben ser solo numeros, de 4 a 5 digitos (ej. 1175) y si es a color agrega una letra (ej. 5521M). \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($tonerR_comm == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingresaron comentario, debes agregar folios de entrega o algun otro comentario sobre el ingreso. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_stock == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ninguna Cantidad para agregar al stock. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($toner_stock <= 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ La cantidad no puede ser menor a 1. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $toner_stock)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Formato de cantidad incorrecto. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_tipo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ningun tipo de toner. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($toner_tipo != 0 && $toner_tipo != 1 && $toner_tipo != 2 && $toner_tipo != 3 && $toner_tipo != 4) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ningun tipo de toner valido. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-4]{1}", $toner_tipo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El tipo de toner ingresado no tiene el formato solicitado. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    if ($toner_tipo == 0) {
      $toner_codigo = strtoupper($toner_marca . "-" . $toner_codigo . "-" . $provT_id);
    } else if ($toner_tipo == 1) {
      $toner_codigo = strtoupper($toner_marca . "-" . $toner_codigo . "K-" . $provT_id);
    } else if ($toner_tipo == 2) {
      $toner_codigo = strtoupper($toner_marca . "-" . $toner_codigo . "M-" . $provT_id);
    } else if ($toner_tipo == 3) {
      $toner_codigo = strtoupper($toner_marca . "-" . $toner_codigo . "C-" . $provT_id);
    } else if ($toner_tipo == 4) {
      $toner_codigo = strtoupper($toner_marca . "-" . $toner_codigo . "Y-" . $provT_id);
    }
  }

  if ($toner_marca == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ningun tipo de toner. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($toner_marca != "TK" && $toner_marca != "ES" && $toner_marca != "CH") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ningun tipo de toner valido. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[A-Z]{2}", $toner_marca)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El tipo de toner ingresado no tiene el formato solicitado. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($provT_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso ningun proveedor de toner. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $provT_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El proveedor ingresado no tiene el formato solicitado. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_comp == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes ingresar una descripcion de compatibilidad. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[a-zA-Z0-9 !-\/]{10,250}", $toner_comp)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ La descripcion de compatibilidad no tiene el formato correcto. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // Terminan las comprobaciones para el toner Nuevo //


  if (consultaData("SELECT * FROM Toners WHERE toner_codigo = '$toner_codigo'")['numRows'] >= 1) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El codigo de toner (' . $toner_codigo . ') que estas agregando ya se encuentra registrado o verifica si esta Inactivo. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (sentenciaData("INSERT INTO Toners (toner_codigo, toner_parte, toner_rendi, toner_tipo, toner_provT_id, toner_comp) VALUES ('$toner_codigo', '$toner_parte', '$toner_rendi', '$toner_tipo', '$provT_id', '$toner_comp')")) {
    $toner_id = consultaData("SELECT * FROM Toners WHERE toner_codigo = '$toner_codigo'")['dataFetch'][0]['toner_id'];
    if (sentenciaData("INSERT INTO TonersRegistrosE (tonerR_fecha, tonerR_toner_id, tonerR_cant, tonerR_comm) VALUES ('$tonerR_fecha', '$toner_id', '$toner_stock', '$tonerR_comm')")) {
      $alerta = [
        'Alerta' => 'recargar',
        'Titulo' => 'Registro Completado',
        'Texto' => '\(- _ -)/ Nuevo registro agregado, (' . $toner_codigo . ') correctamente. \(- _ -)/',
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
      'Texto' => '\(- _ -)/ No se pudo agregar el nuevo toner. \(- _ -)/',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


// ============================ Actualizar Datos del Toner ============================ //
function actualizarToner()
{
  // ------------------- Comprobacion de Formulario ------------------- //
  $toner_id = limpiarCadena(decryption($_POST['toner_id_edit']));
  $toner_comp = limpiarCadena($_POST['toner_comp_edit']);
  $toner_parte = limpiarCadena($_POST['toner_parte_edit']);
  $toner_rendi = limpiarCadena($_POST['toner_rendi_edit']);
  $provT_id = limpiarCadena($_POST['provT_id']);
  $toner_estado = limpiarCadena($_POST['toner_estado_edit']);


  if ($toner_comp == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes ingresar la compatibilidad. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_parte == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes ingresar el numero de parte. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_rendi == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes ingresar la cantidad de rendimiento. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^[0-9]+$", $provT_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Formato invalido - ID de Toner. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (consultaData("SELECT * FROM Toners WHERE toner_id = '$toner_id'")['numRows'] <= 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El toner a editar no existe, recarga la pagina. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($toner_estado != 'Activo' && $toner_estado != 'Inactivo') {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Estatus Incorrecto. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }



  // Termina el registro del nuevo Toner //
  if (sentenciaData("UPDATE Toners SET toner_rendi = '$toner_rendi', toner_parte = '$toner_parte', toner_estado = '$toner_estado', toner_comp = '$toner_comp', toner_provT_id = '$provT_id' WHERE toner_id = '$toner_id'")) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Se actualizo el registro correctamente. \(- _ -)/',
      'Tipo' => 'success'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se pudo actualizar el registro. \(- _ -)/',
      'Tipo' => 'error'
    ];
  }
  // Termina el registro del nuevo Toner //
  echo json_encode($alerta);
  exit();
}


// ============================ Agregar registro de salida de Toner ============================ //
function salidaToner()
{
  // ------------------- Comprobacion de Formulario ------------------- //
  $tonerRO_fecha = limpiarCadena($_POST['tonerRO_fecha']);
  $tonerRO_toner_id = limpiarCadena(decryption($_POST['tonerRO_toner_id']));
  $tonerRO_cantidad = limpiarCadena($_POST['tonerRO_cantidad']);
  $tonerRO_comm = limpiarCadena($_POST['tonerRO_comm']);
  $tonerRO_tipo = limpiarCadena($_POST['tonerRO_tipo']);
  $tonerRO_empleado = limpiarCadena($_POST['tonerRO_empleado']);
  $tonerRO_identificador = limpiarCadena(decryption($_POST['tonerRO_identificador']));
  // $RST_file = $_FILES['RST_file'];

  if ($tonerRO_fecha == "") {
    $data = [
      'Status' => FALSE,
      'Result' => '\(- _ -)/ Ingresa la Fecha de salida. \(- _ -)/',
    ];
    echo json_encode($data);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $tonerRO_fecha)) {
    $data = [
      'Status' => FALSE,
      'Result' => '\(- _ -)/ La Fecha de salida no tiene el formato solicitado. \(- _ -)/',
    ];
    echo json_encode($data);
    exit();
  } else {
    list($anioFecha, $mesFecha, $diaFecha) = explode("-", $tonerRO_fecha);
  }


  if (verificarDatos("^[0-9]+$", $tonerRO_cantidad)) {
    $data = [
      'Status' => FALSE,
      'Result' => '\(- _ -)/ La cantidad ingresada a salir no tiene el formato correcto. \(- _ -)/',
    ];
    echo json_encode($data);
    exit();
  } else if ($tonerRO_cantidad == 0) {
    $data = [
      'Status' => FALSE,
      'Result' => '\(- _ -)/ Debes ingresar una cantidad a retirar. \(- _ -)/',
    ];
    echo json_encode($data);
    exit();
  }


  if ($tonerRO_toner_id == "") {
    $data = [
      'Status' => FALSE,
      'Result' => '\(- _ -)/ Debes ingresar un Toner de Salida. \(- _ -)/',
    ];
    echo json_encode($data);
    exit();
  } else if (verificarDatos("^[0-9]+$", $tonerRO_toner_id)) {
    $data = [
      'Status' => FALSE,
      'Result' => '\(- _ -)/ El Toner de salida no tiene el formato solicitado. \(- _ -)/',
    ];
    echo json_encode($data);
    exit();
  } else {
    $tonerData = consultaData("SELECT * FROM Toners WHERE toner_id = '$tonerRO_toner_id' AND toner_estado = 'Activo'");
    if ($tonerData['numRows'] <= 0) {
      $data = [
        'Status' => FALSE,
        'Result' => '\(- _ -)/ El Toner seleccionado no existe, o esta inactivo. \(- _ -)/',
      ];
      echo json_encode($data);
      exit();
    } else {
      $tonerData = $tonerData['dataFetch'][0];
      $tonerET = consultaData("SELECT SUM(tonerR_cant) AS tonerET FROM TonersRegistrosE WHERE tonerR_toner_id = " . $tonerData['toner_id'])['dataFetch'][0]['tonerET'];
      $tonerST = consultaData("SELECT SUM(tonerRO_cantidad) AS tonerST FROM TonersRegistrosS WHERE tonerRO_toner_id = " . $tonerData['toner_id'])['dataFetch'][0]['tonerST'];
      $tonersStock = $tonerET - $tonerST;
      if ($tonersStock <= 0) {
        $data = [
          'Status' => FALSE,
          'Result' => '\(- _ -)/ El Toner seleccionado no marca existencias en el sistema, verificalo en almacen. \(- _ -)/',
        ];
        echo json_encode($data);
        exit();
      } else if ($tonersStock < $tonerRO_cantidad) {
        $data = [
          'Status' => FALSE,
          'Result' => '\(- _ -)/ La cantidad a solicitada supera las existencias en el sistema, verificalo en almacen. \(- _ -)/',
        ];
        echo json_encode($data);
        exit();
      }
    }
  }



  if ($tonerRO_tipo != "Venta" && $tonerRO_tipo != "Renta" && $tonerRO_tipo != "Interno") {
    $data = [
      'Status' => FALSE,
      'Result' => '\(- _ -)/ Tipo de Salida Desconocida. \(- _ -)/',
    ];
    echo json_encode($data);
    exit();
  } else {
    if ($tonerRO_tipo == 'Venta' || $tonerRO_tipo == 'Renta') {
      if ($tonerRO_tipo == 'Venta') {
        if ($tonerRO_identificador == "") {
          $data = [
            'Status' => FALSE,
            'Result' => '\(- _ -)/ Selecciona un Cliente. \(- _ -)/',
          ];
          echo json_encode($data);
          exit();
        } else {
          $clienteData = consultaData("SELECT * FROM Clientes WHERE cliente_id = '$tonerRO_identificador'");
          if ($clienteData['numRows'] <= 0) {
            $data = [
              'Status' => FALSE,
              'Result' => '\(- _ -)/ El Cliente seleccionado no existe. \(- _ -)/',
            ];
            echo json_encode($data);
            exit();
          } else {
            // ----------------------------------------------- Obtener Folio de Salida ----------------------------------------------- //
            $tonerRO_folio = 'CR';
            $tonerRO_folio = getFolioTonerRO($tonerRO_tipo, $tonerRO_folio);
            // ----------------------------------- ^^^ FIN ^^^ Obtener el codigo de equipo ^^^ FIN ^^^ ----------------------------------- //
          }
        }
      } else if ($tonerRO_tipo == 'Renta') {
        if ($tonerRO_identificador == "") {
          $data = [
            'Status' => FALSE,
            'Result' => '\(- _ -)/ Selecciona una Renta. \(- _ -)/',
          ];
          echo json_encode($data);
          exit();
        } else {
          $rentaData = consultaData("SELECT * FROM Rentas WHERE renta_id = '$tonerRO_identificador'");
          if ($rentaData['numRows'] <= 0) {
            $data = [
              'Status' => FALSE,
              'Result' => '\(- _ -)/ La Renta seleccionada no existe. \(- _ -)/',
            ];
            echo json_encode($data);
            exit();
          } else {
            // ----------------------------------------------- Obtener Folio de Salida ----------------------------------------------- //
            $tonerRO_folio = 'FR';
            $tonerRO_folio = getFolioTonerRO($tonerRO_tipo, $tonerRO_folio);
            // ----------------------------------- ^^^ FIN ^^^ Obtener el codigo de equipo ^^^ FIN ^^^ ----------------------------------- //
          }
        }
      }
    } else if ($tonerRO_tipo == 'Interno') {
      $tonerRO_identificador = 0;
      // ----------------------------------------------- Obtener Folio de Salida ----------------------------------------------- //
      $tonerRO_folio = 'IN';
      $tonerRO_folio = getFolioTonerRO($tonerRO_tipo, $tonerRO_folio);
      // ----------------------------------- ^^^ FIN ^^^ Obtener el codigo de equipo ^^^ FIN ^^^ ----------------------------------- //
    }

    // if (empty($RST_file['tmp_name'])) {
    //     $data = [
    //         'Status' => FALSE,
    //         'Result' => '\(- _ -)/ No se seleccionó ningún archivo. \(- _ -)/',
    //     ];
    //     echo json_encode($data);
    //     exit();
    // } else {
    //     if (!move_uploaded_file($RST_file['tmp_name'], SERVERDIR . 'DocsCR/MovimientosAlmacen/' . $anioFecha . "/" . $mesFecha . "/" . $tonerRO_folio . ".pdf")) {
    //         $data = [
    //             'Status' => FALSE,
    //             'Result' => '\(- _ -)/ No se pudo subir el archivo. \(- _ -)/',
    //         ];
    //         echo json_encode($data);
    //         exit();
    //     }
    // }

    $tonerRO_empleado = limpiarCadena($_POST['tonerRO_empleado']);
    if ($tonerRO_empleado == "") {
      $data = [
        'Status' => FALSE,
        'Result' => '\(- _ -)/ Selecciona un Empleado. \(- _ -)/',
      ];
      echo json_encode($data);
      exit();
    } else if ($tonerRO_empleado != "Candy" && $tonerRO_empleado != "Renan" && $tonerRO_empleado != "Rafa") {
      $data = [
        'Status' => FALSE,
        'Result' => '\(- _ -)/ Selecciona un Empleado Valido. \(- _ -)/'
      ];
      echo json_encode($data);
      exit();
    }
  }


  if ($tonerRO_comm == "") {
    $data = [
      'Status' => FALSE,
      'Result' => '\(- _ -)/ Debes ingresar un comentario de salida. \(- _ -)/'
    ];
    echo json_encode($data);
    exit();
  }

  $sqlRegSalTon = "INSERT INTO TonersRegistrosS (tonerRO_fecha, tonerRO_folio, tonerRO_toner_id, tonerRO_cantidad, tonerRO_comm, tonerRO_tipo, tonerRO_empleado, tonerRO_identificador) VALUES ('$tonerRO_fecha', '$tonerRO_folio', '$tonerRO_toner_id', '$tonerRO_cantidad', '$tonerRO_comm', '$tonerRO_tipo', '$tonerRO_empleado', '$tonerRO_identificador')";
  $Status = sentenciaData($sqlRegSalTon);
  if ($Status) {
    $Result = "Completado.";
  } else {
    $Result = "Error al Insertar registro de Salida.";
  }

  $data = [
    'Status' => $Status,
    'Result' => $Result
  ];
  echo json_encode($data);
}

// ============================ Actualizar registro de salida de Toner ============================ //
function actualizarRST()
{
  // ------------------- Comprobacion de Formulario ------------------- //v
  $tonerRO_id = limpiarCadena(decryption($_POST['actualizarRST']));
  $tonerRO_fecha = limpiarCadena($_POST['tonerRO_fecha_edit']);
  $tonerRO_toner_id = limpiarCadena(decryption($_POST['tonerRO_toner_id_edit']));
  $tonerRO_cantidad = limpiarCadena($_POST['tonerRO_cantidad']);
  $tonerRO_comm = limpiarCadena($_POST['tonerRO_comm']);
  $tonerRO_tipo = limpiarCadena($_POST['tonerRO_tipo']);
  $tonerRO_empleado = limpiarCadena($_POST['tonerRO_empleado']);
  $tonerRO_identificador = limpiarCadena(decryption($_POST['tonerRO_identificador']));

  if ($tonerRO_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes ingresar un Toner de Salida. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $tonerRO_id)) {

    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El Toner de salida no tiene el formato solicitado. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $dataRST = consultaData("SELECT * FROM TonersRegistrosS WHERE tonerRO_id  = '$tonerRO_id'");
    if ($dataRST['numRows'] <= 0) {

      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '\(- _ -)/ No existe el registro de salida de Toner a editar. \(- _ -)/',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $dataRST = $dataRST['dataFetch'][0];
      $tonerRO_folio = $dataRST['tonerRO_folio'];
      $tonerRO_tipo_RST = $dataRST['tonerRO_tipo'];


      if (verificarDatos("^[0-9]+$", $tonerRO_cantidad)) {

        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ La cantidad ingresada a salir no tiene el formato correcto. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if ($tonerRO_cantidad == 0) {

        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ Debes ingresar una cantidad a retirar. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }

      if ($tonerRO_toner_id == "") {

        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ Debes ingresar un Toner de Salida. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if (verificarDatos("^[0-9]+$", $tonerRO_toner_id)) {

        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ El Toner de salida no tiene el formato solicitado. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else {
        $tonerData = consultaData("SELECT * FROM Toners WHERE toner_id = '$tonerRO_toner_id' AND toner_estado = 'Activo'");
        if ($tonerData['numRows'] <= 0) {

          $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ El Toner seleccionado no existe, o esta inactivo. \(- _ -)/',
            'Tipo' => 'error'
          ];
          echo json_encode($alerta);
          exit();
        } else {
          $tonerData = $tonerData['dataFetch'][0];
          $tonerET = consultaData("SELECT SUM(tonerR_cant) AS tonerET FROM TonersRegistrosE WHERE tonerR_toner_id = " . $tonerData['toner_id'])['dataFetch'][0]['tonerET'];
          $tonerST = consultaData("SELECT SUM(tonerRO_cantidad) AS tonerST FROM TonersRegistrosS WHERE tonerRO_toner_id = " . $tonerData['toner_id'])['dataFetch'][0]['tonerST'];
          $tonersStock = $tonerET - $tonerST;
          if ($dataRST['tonerRO_toner_id'] == $tonerRO_toner_id) {
            $tonersStock = $tonersStock + $dataRST['tonerRO_cantidad'];
          }
          if ($tonersStock <= 0) {

            $alerta = [
              'Alerta' => 'simple',
              'Titulo' => 'Ocurrio un Error inesperado',
              'Texto' => '\(- _ -)/ El Toner seleccionado no marca existencias en el sistema, verificalo en almacen. \(- _ -)/',
              'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
          } else if ($tonersStock < $tonerRO_cantidad) {

            $alerta = [
              'Alerta' => 'simple',
              'Titulo' => 'Ocurrio un Error inesperado',
              'Texto' => '\(- _ -)/ La cantidad a solicitada supera las existencias en el sistema, verificalo en almacen. \(- _ -)/',
              'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
          }
        }
      }
    }
  }


  if ($tonerRO_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Ingresa la Fecha de salida. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $tonerRO_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ La Fecha de salida no tiene el formato solicitado. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    list($anioFecha, $mesFecha, $diaFecha) = explode("-", $tonerRO_fecha);
  }


  if ($tonerRO_tipo != "Venta" && $tonerRO_tipo != "Renta" && $tonerRO_tipo != "Interno") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Tipo de Salida Desconocida. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    if ($tonerRO_tipo == 'Venta' || $tonerRO_tipo == 'Renta') {
      if ($tonerRO_tipo == 'Venta') {
        if ($tonerRO_identificador == "") {
          $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ Selecciona un Cliente. \(- _ -)/',
            'Tipo' => 'error'
          ];
          echo json_encode($alerta);
          exit();
        } else {
          $clienteData = consultaData("SELECT * FROM Clientes WHERE cliente_id = '$tonerRO_identificador'");
          if ($clienteData['numRows'] <= 0) {
            $alerta = [
              'Alerta' => 'simple',
              'Titulo' => 'Ocurrio un Error inesperado',
              'Texto' => '\(- _ -)/ El Cliente seleccionado no existe. \(- _ -)/',
              'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
          }
        }
        if ($tonerRO_tipo != $tonerRO_tipo_RST) {
          // ----------------------------------------------- Si cambia el tipo de Salida ----------------------------------------------- //
          $TipoReg = ($tonerRO_tipo_RST == "Interno") ? "INTERNOS" : (($tonerRO_tipo_RST == "Venta") ? "VENTAS" : (($tonerRO_tipo_RST == "Renta") ? "RENTAS" : ""));
          if (file_exists(SERVERDIR . "DocsCR/ALMACEN/" . $TipoReg . "/" . $anioFecha . "/" . $mesFecha . "/" . $tonerRO_folio . ".pdf")) {
            $alerta = [
              'Alerta' => 'simple',
              'Titulo' => 'Ocurrio un Error inesperado',
              'Texto' => '\(- _ -)/ Primero debes eliminar la evidencia guardada. \(- _ -)/',
              'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
          }
          $tonerRO_folio = 'CR';
          $tonerRO_folio = getFolioTonerRO($tonerRO_tipo, $tonerRO_folio);
          // ----------------------------------- ^^^ FIN ^^^ Si cambia el tipo de salida ^^^ FIN ^^^ ----------------------------------- //
        }
      } else if ($tonerRO_tipo == 'Renta') {
        if ($tonerRO_identificador == "") {
          $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ Selecciona una Renta. \(- _ -)/',
            'Tipo' => 'error'
          ];
          echo json_encode($alerta);
          exit();
        } else {
          $rentaData = consultaData("SELECT * FROM Rentas WHERE renta_id = '$tonerRO_identificador'");
          if ($rentaData['numRows'] <= 0) {
            $alerta = [
              'Alerta' => 'simple',
              'Titulo' => 'Ocurrio un Error inesperado',
              'Texto' => '\(- _ -)/ La Renta seleccionada no existe. \(- _ -)/',
              'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
          }
        }
        if ($tonerRO_tipo != $tonerRO_tipo_RST) {
          // ----------------------------------------------- Si cambia el tipo de Salida ----------------------------------------------- //
          $TipoReg = ($tonerRO_tipo_RST == "Interno") ? "INTERNOS" : (($tonerRO_tipo_RST == "Venta") ? "VENTAS" : (($tonerRO_tipo_RST == "Renta") ? "RENTAS" : ""));
          if (file_exists(SERVERDIR . "DocsCR/ALMACEN/" . $TipoReg . "/" . $anioFecha . "/" . $mesFecha . "/" . $tonerRO_folio . ".pdf")) {
            $alerta = [
              'Alerta' => 'simple',
              'Titulo' => 'Ocurrio un Error inesperado',
              'Texto' => '\(- _ -)/ Primero debes eliminar la evidencia guardada. \(- _ -)/',
              'Tipo' => 'error'
            ];
            echo json_encode($alerta);
            exit();
          }
          $tonerRO_folio = 'FR';
          $tonerRO_folio = getFolioTonerRO($tonerRO_tipo, $tonerRO_folio);
          // ----------------------------------- ^^^ FIN ^^^ Si cambia el tipo de salida ^^^ FIN ^^^ ----------------------------------- //
        }
      }
    } else if ($tonerRO_tipo == 'Interno') {
      $tonerRO_identificador = 0;
      if ($tonerRO_tipo != $tonerRO_tipo_RST) {
        // ----------------------------------------------- Si cambia el tipo de Salida ----------------------------------------------- //
        $TipoReg = ($tonerRO_tipo_RST == "Interno") ? "INTERNOS" : (($tonerRO_tipo_RST == "Venta") ? "VENTAS" : (($tonerRO_tipo_RST == "Renta") ? "RENTAS" : ""));
        if (file_exists(SERVERDIR . "DocsCR/ALMACEN/" . $TipoReg . "/" . $anioFecha . "/" . $mesFecha . "/" . $tonerRO_folio . ".pdf")) {
          $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '\(- _ -)/ Primero debes eliminar la evidencia guardada. \(- _ -)/',
            'Tipo' => 'error'
          ];
          echo json_encode($alerta);
          exit();
        }
        $tonerRO_folio = 'IN';
        $tonerRO_folio = getFolioTonerRO($tonerRO_tipo, $tonerRO_folio);
        // ----------------------------------- ^^^ FIN ^^^ Si cambia el tipo de salida ^^^ FIN ^^^ ----------------------------------- //
      }
    }

    if (isset($_FILES['evidencia_PDF'])) {
      if ($_FILES['evidencia_PDF']['name'] == "") {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) No Ingresaste Evidencia. (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if ($_FILES['evidencia_PDF']['type'] != "application/pdf") {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) El formato de la evidencia de ser en PDF. (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if (saveEvidencia($_FILES['evidencia_PDF']['tmp_name'], $tonerRO_folio, $anioFecha, $mesFecha, $tonerRO_tipo)['status'] == FALSE) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ No se pudo subir el archivo. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }

    $tonerRO_empleado = limpiarCadena($_POST['tonerRO_empleado']);
    if ($tonerRO_empleado == "") {

      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '\(- _ -)/ Selecciona un Empleado. \(- _ -)/',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($tonerRO_empleado != "Candy" && $tonerRO_empleado != "Renan" && $tonerRO_empleado != "Rafa") {

      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '\(- _ -)/ Selecciona un Empleado Valido. \(- _ -)/',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  if ($tonerRO_comm == "") {

    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes ingresar un comentario de salida. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  $update = "UPDATE TonersRegistrosS SET
                tonerRO_toner_id = '$tonerRO_toner_id',
                tonerRO_cantidad = '$tonerRO_cantidad',
                tonerRO_comm = '$tonerRO_comm',
                tonerRO_tipo = '$tonerRO_tipo',
                tonerRO_folio = '$tonerRO_folio',
                tonerRO_empleado = '$tonerRO_empleado',
                tonerRO_identificador = '$tonerRO_identificador'
                WHERE tonerRO_id = '$tonerRO_id'";

  if (sentenciaData($update)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Actualizacion Completada. \(- _ -)/',
      'Tipo' => 'success'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se pudo actualizar el registro. \(- _ -)/',
      'Tipo' => 'error'
    ];
  }


  echo json_encode($alerta);
}

// Funcion para obtener los Folio de Salida de almacen.
function getFolioTonerRO($tonerRO_tipo, $tonerRO_folio)
{
  $query_DB = consultaData("SELECT * FROM TonersRegistrosS WHERE tonerRO_tipo = '$tonerRO_tipo'");
  if ($query_DB['numRows'] == 0) {
    $tonerRO_folio = $tonerRO_folio . '-000001';
  } else {
    $last_data_DB = array_pop($query_DB['dataFetch']);
    $last_data_DB['tonerRO_folio']++;
    $tonerRO_folio = $last_data_DB['tonerRO_folio'];
  }

  return $tonerRO_folio;
}


/* --------------------- Funcion Guardar Archivos PDF --------------------- */
function saveEvidencia($tmpDir, $folio, $anio, $mes, $tipo)
{
  $TipoReg = ($tipo == "Interno") ? "INTERNOS" : (($tipo == "Venta") ? "VENTAS" : (($tipo == "Renta") ? "RENTAS" : ""));

  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
  // +======+ Verificar Carpeta DocsCR +======+ //
  $docDir = SERVERDIR . 'DocsCR/';
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $docDir .=  'ALMACEN/';
  } else {
    $docDir .=  'ALMACEN/';
  }
  // +======+ Verificar Carpeta ALMACEN +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $docDir .=  $TipoReg . '/';
  } else {
    $docDir .=  $TipoReg . '/';
  }
  // +======+ Verificar Carpeta Tipo +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $docDir .=  $anio . '/';
  } else {
    $docDir .=  $anio . '/';
  }
  // +======+ Verificar Carpeta Anio +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $docDir .= $mes . '/';
  } else {
    $docDir .= $mes . '/';
  }
  // +======+ Verificar Carpeta Mes +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  }

  $archivo = $folio . '.pdf';

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  if (file_exists($docDir . $archivo)) {
    $result = [
      'status' => FALSE,
      'result' => 'La evidencia, ya existe'
    ];
  } else if (move_uploaded_file($tmpDir, $docDir . $archivo)) {
    $result = [
      'status' => TRUE,
      'result' => 'Evidencia Guardada.'
    ];
  } else {
    $result = [
      'status' => FALSE,
      'result' => 'No se pudo guardar la evidencia.'
    ];
  }

  return $result;
} // Fin del la Funcion
