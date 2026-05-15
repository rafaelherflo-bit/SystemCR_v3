<?php

// ============================ Controladores de Rentas ============================ //
function rentaAdd()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|INICIO|~~~ Comprobacion de Formulario ~~~|INICIO|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

  $renta_finicio = limpiarCadena($_POST['renta_finicio']);
  $contrato_id = limpiarCadena(decryption($_POST['renta_contrato_id']));
  $contacto = limpiarCadena($_POST['renta_contacto']);
  $telefono = limpiarCadena($_POST['renta_telefono']);

  $renta_tipo = limpiarCadena($_POST['renta_tipo']);
  $zona_id = limpiarCadena(decryption($_POST['zona_id']));
  $renta_depto = limpiarCadena($_POST['renta_depto']);
  $equipo_id = limpiarCadena(decryption($_POST['equipo_id']));
  $renta_coor = limpiarCadena($_POST['renta_coor']);
  $renta_stock = limpiarCadena($_POST['renta_stock']);

  $renta_costo = limpiarCadena($_POST['renta_costo']);

  $renta_inc_esc = limpiarCadena($_POST['renta_inc_esc']);
  $renta_inc_bn = limpiarCadena($_POST['renta_inc_bn']);
  $renta_inc_col = (isset($_POST['renta_inc_col']) ? limpiarCadena($_POST['renta_inc_col']) : 0);

  $renta_exc_esc = limpiarCadena($_POST['renta_exc_esc']);
  $renta_exc_bn = limpiarCadena($_POST['renta_exc_bn']);
  $renta_exc_col = (isset($_POST['renta_exc_col']) ? limpiarCadena($_POST['renta_exc_col']) : 0);

  if ($renta_finicio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $renta_finicio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La fecha no tiene el formato adecuado (ej. 2025-01-13). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($contrato_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario ingresar un Contrato. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $contrato_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contrato NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $contratoSQL = "SELECT * FROM Contratos
                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                    WHERE contrato_id = '$contrato_id'";
    $checkContratoID = consultaData($contratoSQL);
    if ($checkContratoID['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El contrato seleccionado no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $contrato_folio = $checkContratoID['dataFetch'][0]['contrato_folio'];
      $cliente_rfc = $checkContratoID['dataFetch'][0]['cliente_rfc'];
      $cliente_rs = $checkContratoID['dataFetch'][0]['cliente_rs'];
    }
  }

  if ($contacto == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El nombre de contacto es necesario. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[a-zA-Z ?.?]{5,50}", $contacto)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El nombre de contacto no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("[0-9]{0,15}", $telefono)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) el numero de telefono no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($renta_tipo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario seleccionar el tipo de renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($renta_tipo != "fija" && $renta_tipo != "temporal") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se reconoce el tipo de renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($zona_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario ingresar una Zona. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $zona_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La Zona NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $checkZonID = consultaData("SELECT * FROM Zonas WHERE zona_id = '$zona_id'");
    if ($checkZonID['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) La Zona seleccionado no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $zona_codigo = $checkZonID['dataFetch'][0]['zona_codigo'];
    }
  }

  // -------------- Conseguimos un Folio de Renta disponible -------------- //

  $foliosSQL = "SELECT * FROM Rentas
                WHERE renta_contrato_id = '$contrato_id'";
  $check_folio = consultaData($foliosSQL);
  if ($check_folio['numRows'] == 0) {
    $totalRentas = 1;
  } else {
    $totalRentas = $check_folio['numRows'] + 1;
  }
  $renta_folio = $zona_codigo . "-" . $totalRentas;
  // --------------------------------------------------------------------------------------------------------------------------- //

  if ($renta_depto == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar un departamento. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[a-zA-Z ?.?]{4,50}", $renta_depto)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El departamento ingresado NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($renta_coor == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar las coordenadas. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($equipo_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario ingresar un Equipo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $equipo_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Equipo NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $equipoSQL = "SELECT * FROM Equipos
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE equipo_id = '$equipo_id'";
    $checkEquID = consultaData($equipoSQL);
    $equipoRentaSQL = "SELECT * FROM Rentas
        WHERE renta_equipo_id = '$equipo_id'";
    $checkRentaEquID = consultaData($equipoRentaSQL);
    if ($checkEquID['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El Equipo seleccionado no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($checkRentaEquID['numRows'] >= 1) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El Equipo seleccionado ya ah sido asignado a una renta. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $modelo_tipo = $checkEquID['dataFetch'][0]['modelo_tipo'];
      $equipo_serie = $checkEquID['dataFetch'][0]['equipo_serie'];
    }
  }



  if ($renta_stock == "true") {
    $renta_stock_K = limpiarCadena($_POST['renta_stock_K']);
    $renta_stock_M = limpiarCadena($_POST['renta_stock_M']);
    $renta_stock_Y = limpiarCadena($_POST['renta_stock_Y']);
    $renta_stock_C = limpiarCadena($_POST['renta_stock_C']);
    $renta_stock_R = limpiarCadena($_POST['renta_stock_R']);

    if ($renta_stock_K == "" && $renta_stock_M == "" && $renta_stock_Y == "" && $renta_stock_C == "" && $renta_stock_R == "") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(o _ O) En realidad dejaste algun stock??. (o _ O)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($renta_stock_K == 0 && $renta_stock_M == 0 && $renta_stock_Y == 0 && $renta_stock_C == 0 && $renta_stock_R == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(o _ O) En realidad dejaste algun stock??. (o _ O)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }

    if ($renta_stock_K == "") {
      $renta_stock_R = 0;
    } else if (verificarDatos("[0-9]{1}", $renta_stock_K)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) En stock K NO ingresaste el Formato Solicitado. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($renta_stock_K > 2) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(o _ O) Estas dejando demasiado stock de color K. (o _ O)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
    if ($renta_stock_R == "") {
      $renta_stock_R = 0;
    } else if (verificarDatos("[0-9]{1}", $renta_stock_R)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) En stock Residual NO ingresaste el Formato Solicitado. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($renta_stock_R > 2) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(o _ O) Estas dejando demasiado stock Residual. (o _ O)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
    if ($modelo_tipo == "Multicolor") {
      if ($renta_stock_M == "") {
        $renta_stock_M = 0;
      } else if (verificarDatos("[0-9]{1}", $renta_stock_M)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) En stock M NO ingresaste el Formato Solicitado. (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if ($renta_stock_M > 2) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) Estas dejando demasiado stock de color M. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }

      if ($renta_stock_Y == "") {
        $renta_stock_Y = 0;
      } else if (verificarDatos("[0-9]{1}", $renta_stock_Y)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) En stock Y NO ingresaste el Formato Solicitado. (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if ($renta_stock_Y > 2) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) Estas dejando demasiado stock de color Y. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }

      if ($renta_stock_C == "") {
        $renta_stock_C = 0;
      } else if (verificarDatos("[0-9]{1}", $renta_stock_C)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) En stock C NO ingresaste el Formato Solicitado. (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if ($renta_stock_C > 2) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) Estas dejando demasiado stock de color C. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    } else if ($modelo_tipo == "Monocromatico") {
      if ($renta_stock_M >= 1) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) El equipo es Monocromatico, no puedes dejar toner de color M. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if ($renta_stock_C >= 1) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) El equipo es Monocromatico, no puedes dejar toner de color C. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if ($renta_stock_Y >= 1) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) El equipo es Monocromatico, no puedes dejar toner de color Y. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else {
        $renta_stock_M = 0;
        $renta_stock_C = 0;
        $renta_stock_Y = 0;
      }
    }
  } else {
    $renta_stock_K = 0;
    $renta_stock_M = 0;
    $renta_stock_Y = 0;
    $renta_stock_C = 0;
    $renta_stock_R = 0;
  }


  if ($renta_costo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar el costo, de no tenerlo en el momento dejarlo en "0". (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $renta_costo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Costo mensual de la renta). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($renta_inc_esc != "") {
    if (verificarDatos("^[0-9]+$", $renta_inc_esc)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Cantidad Incluida a ESC). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if ($renta_exc_esc != "") {
    if (verificarDatos("^([0-9]+\.?[0-9]{0,3})$", $renta_exc_esc)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Costo de excedente a ESC). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  if ($renta_inc_bn != "") {
    if (verificarDatos("^[0-9]+$", $renta_inc_bn)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Cantidad Incluida a B&N). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if ($renta_exc_bn != "") {
    if (verificarDatos("^([0-9]+\.?[0-9]{0,3})$", $renta_exc_bn)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Costo de excedente a B&N). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  if ($renta_inc_col != "") {
    if (verificarDatos("^[0-9]+$", $renta_inc_col)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Cantidad Incluida a B&N). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if ($modelo_tipo == "Multicolor") {
    if ($renta_inc_col != "") {
      if (verificarDatos("^[0-9]+$", $renta_inc_col)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Cantidad Incluida a Color). (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
    if ($renta_exc_col != "") {
      if (verificarDatos("^([0-9]+\.?[0-9]{0,3})$", $renta_exc_col)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Costo de excedente a Color). (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }


  // --------------------------------------------- AGREGAMOS LA RENTA A LOS REGISTROS --------------------------------------------- //
  $insertNewRenta = "INSERT INTO Rentas (renta_finicio, renta_contrato_id, renta_zona_id, renta_coor, renta_depto, renta_equipo_id, renta_contacto, renta_telefono, renta_tipo, renta_costo, renta_inc_esc, renta_inc_bn, renta_inc_col, renta_exc_esc, renta_exc_bn, renta_exc_col, renta_stock_K, renta_stock_M, renta_stock_Y, renta_stock_C, renta_stock_R, renta_folio) VALUES ('$renta_finicio', '$contrato_id', '$zona_id', '$renta_coor', '$renta_depto', '$equipo_id', '$contacto', '$telefono', '$renta_tipo', '$renta_costo', '$renta_inc_esc', '$renta_inc_bn', '$renta_inc_col', '$renta_exc_esc', '$renta_exc_bn', '$renta_exc_col', '$renta_stock_K', '$renta_stock_M', '$renta_stock_Y', '$renta_stock_C', '$renta_stock_R', '$renta_folio')";
  $insertIDrenta = insertID($insertNewRenta);
  if ($insertIDrenta['status']) {
    $renta_id = $insertIDrenta['ID'];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) No se pudo realizar el registro de renta. (o _ O)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // --------------------------------- ^^^ FIN ^^^ AGREGAMOS LA RENTA A LOS REGISTROS ^^^ FIN ^^^ --------------------------------- //



  // --------------------------------------------- AGREGAMOS A LOS REGISTROS LA NUEVA UBICACION DEL EQUIPO --------------------------------------------- //
  // $insertNewUbi = "INSERT INTO EquiposUbi (equUbi_equipo_id, equUbi_fecha_inicio, equUbi_ubi, equUbi_ubicacion) VALUES ('" . $equipo_id . "', '" . $renta_finicio . "', 1, '" . $renta_id . "')";
  // if (sentenciaData($insertNewUbi) == FALSE) {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(o _ O) No se pudo realizar el registro de la nueva ubicacion del equipo, pero se agrego la renta correctamente. (o _ O)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // }
  // --------------------------------- ^^^ FIN ^^^ AGREGAMOS A LOS REGISTROS LA NUEVA UBICACION DEL EQUIPO ^^^ FIN ^^^ --------------------------------- //



  $equipo_nivel_K = limpiarCadena($_POST['equipo_nivel_K']);
  $equipo_nivel_M = limpiarCadena($_POST['equipo_nivel_M']);
  $equipo_nivel_C = limpiarCadena($_POST['equipo_nivel_C']);
  $equipo_nivel_Y = limpiarCadena($_POST['equipo_nivel_Y']);
  $equipo_nivel_R = limpiarCadena($_POST['equipo_nivel_R']);

  if (verificarDatos("^[0-9]+$", $equipo_nivel_K)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel colo Negro, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if (verificarDatos("^[0-9]+$", $equipo_nivel_M)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel colo Magenta, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if (verificarDatos("^[0-9]+$", $equipo_nivel_C)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel colo Cyan, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if (verificarDatos("^[0-9]+$", $equipo_nivel_Y)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado en nivel colo Amarillo, no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
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


  // --------------------------------------------- ACTUALIZAMOS EL ESTATUS DEL EQUIPO --------------------------------------------- //
  $updateEquipo = "UPDATE Equipos SET
            equipo_estado = 'Rentado',
            equipo_nivel_K = '$equipo_nivel_K',
            equipo_nivel_M = '$equipo_nivel_M',
            equipo_nivel_C = '$equipo_nivel_C',
            equipo_nivel_Y = '$equipo_nivel_Y',
            equipo_nivel_R = '$equipo_nivel_R'
            WHERE equipo_id = '$equipo_id'";
  if (sentenciaData($updateEquipo) == FALSE) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) No se pudo actualizar el estado del Equipo asignado, pero se agrego la renta correctamente. (o _ O)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // --------------------------------- ^^^ FIN ^^^ ACTUALIZAMOS EL ESTATUS DEL EQUIPO ^^^ FIN ^^^ --------------------------------- //






  // | -°_°- | -°_°- | -°_°- | -°_°- | -°_°- | -°_°- |  PASAMOS A LA VALIDACION DE DATOS PARA EL REGISTRO DE LA LECTURA | -°_°- | -°_°- | -°_°- | -°_°- | -°_°- | -°_°- |  //


  list($fecha_anio, $fecha_mes, $fecha_dia) = explode("-", $renta_finicio);


  $lectura_pdf = $cliente_rs . " (" . $contrato_folio . "-" . $renta_folio . " - " . $renta_depto . " - " . $equipo_serie . ")";

  $checkRentaLectSQL = "SELECT * FROM Lecturas
                              WHERE lectura_renta_id = '$renta_id'
                              AND MONTH(lectura_fecha) = '$fecha_mes'
                              AND YEAR(lectura_fecha) = '$fecha_anio'";
  $checkRentaLectQRY = consultaData($checkRentaLectSQL);
  if ($checkRentaLectQRY['numRows'] >= 1) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La lectura del mes para ' . $lectura_pdf . ', ya esta ingresada. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }



  // --------------------------------------------- Verificando Input File --------------------------------------------- //
  // ************************************************ Evidencia en JPG ************************************************ //
  // --- 1. Definición de Rutas Base ---
  $basePath = SERVERDIR . "/DocsCR/Lecturas/{$fecha_anio}/{$fecha_mes}/";
  $PEDir = $basePath . 'PE/';
  $formatoDir = $basePath . 'Formatos/';

  // --- 2. Creación de directorios en una sola pasada ---
  // Creamos la ruta más profunda con 'true' para que cree todas las anteriores automáticamente
  if (!is_dir($PEDir)) {
    if (!mkdir($PEDir, 0770, true)) {
      echo json_encode(['Alerta' => 'simple', 'Titulo' => 'Error', 'Texto' => 'No se pudo crear carpeta PE', 'Tipo' => 'error']);
      exit();
    }
  }
  if (!is_dir($formatoDir)) {
    if (!mkdir($formatoDir, 0770, true)) {
      echo json_encode(['Alerta' => 'simple', 'Titulo' => 'Error', 'Texto' => 'No se pudo crear carpeta Formatos', 'Tipo' => 'error']);
      exit();
    }
  }

  // --- 3. Procesamiento de Archivos ---
  $lectura_filename = $fecha_dia . '-' . $fecha_mes . '-' . $fecha_anio . ' - ' . $lectura_pdf . ' - Toma de Lectura.jpg';

  // Validación rápida de existencia de archivos
  if (!isset($_FILES['lectura_estado']) && !isset($_FILES['lectura_formato'])) {
    echo json_encode(['Alerta' => 'simple', 'Titulo' => 'Espera', 'Texto' => 'Debes agregar una evidencia.', 'Tipo' => 'info']);
    exit();
  }

  // Función auxiliar interna para evitar repetir código de subida
  $subirArchivo = function ($fileKey, $destinoDir, $nombreArchivo) {
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['name'] != "") {
      if ($_FILES[$fileKey]['type'] != "image/jpeg") {
        return "El formato de {$_FILES[$fileKey]['name']} debe ser JPEG.";
      }
      if (file_exists($destinoDir . $nombreArchivo)) {
        return "El archivo ya existe en el servidor.";
      }
      if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $destinoDir . $nombreArchivo)) {
        return "No se pudo guardar el archivo en el servidor.";
      }
    }
    return true;
  };

  // Ejecutar subidas
  $resEstado = $subirArchivo('lectura_estado', $PEDir, $lectura_filename);
  if ($resEstado !== true) {
    echo json_encode(['Alerta' => 'simple', 'Titulo' => 'Error', 'Texto' => $resEstado, 'Tipo' => 'error']);
    exit();
  }

  $resFormato = $subirArchivo('lectura_formato', $formatoDir, $lectura_filename);
  if ($resFormato !== true) {
    echo json_encode(['Alerta' => 'simple', 'Titulo' => 'Error', 'Texto' => $resFormato, 'Tipo' => 'error']);
    exit();
  }
  // ****************************************************************************************************************** //
  // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //



  // --------------------------------------------- Validacion de Contadores de Lecturas --------------------------------------------- //
  $lectura_esc = limpiarCadena($_POST['lectura_esc']);
  $lectura_bn = limpiarCadena($_POST['lectura_bn']);
  $lectura_col = limpiarCadena($_POST['lectura_col']);

  if ($lectura_esc == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso contador de Escaneo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $lectura_esc)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($lectura_bn == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso contador de B&N. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $lectura_bn)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contador ingresado no es un numero, revisa el formato ingresado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($lectura_col != "") {
    if (verificarDatos("^[0-9]+$", $lectura_col)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El contador ingresado no es un numero, revisa el formato ingresado. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  } else {
    $lectura_col = 0;
  }
  // --------------------------------- ^^^ FIN ^^^ Validacion de Contadores de Lecturas ^^^ FIN ^^^ --------------------------------- //

  // --------------------------------------------- AGREGAMOS REGISTRO DE LECTURA --------------------------------------------- //
  $insert = "INSERT INTO Lecturas (lectura_renta_id, lectura_equipo_id, lectura_pdf, lectura_esc, lectura_bn, lectura_col, lectura_fecha) VALUES ('$renta_id', '$equipo_id', '$lectura_pdf', '$lectura_esc', '$lectura_bn', '$lectura_col', '$renta_finicio')";
  if (sentenciaData($insert)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Se agrego el registro correctamente. \(- _ -)/',
      'Tipo' => 'success'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) No se pudo realizar el registro. (o _ O)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // --------------------------------- ^^^ FIN ^^^ AGREGAMOS REGISTRO DE LECTURA ^^^ FIN ^^^ --------------------------------- //
}

function rentaEdit()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|INICIO|~~~ Comprobacion de Formulario ~~~|INICIO|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $renta_id = decryption(limpiarCadena($_POST['renta_id_edit']));

  $NColores = ['K' => 0, 'M' => 0, 'C' => 0, 'Y' => 0, 'R' => 0];
  $valores_stock = [];

  foreach ($NColores as $Color => $Nivel) {
    // Guardamos el resultado en un array con la llave correspondiente
    $valores_stock[$Color] = (isset($_POST['renta_stock_' . $Color]) ? limpiarCadena($_POST['renta_stock_' . $Color]) : 0);
  }

  $sqlRenta = "SELECT * FROM Rentas
    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
    INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
    INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id    
    WHERE renta_id = '$renta_id'";
  $queryRenta = consultaData($sqlRenta);
  if ($renta_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) Se necesita seleccionar una renta valida. (o _ O)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($queryRenta['numRows'] == 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) La Renta solicitada no existe. (o _ O)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $dataRenta = $queryRenta['dataFetch'][0];

    $modelo_tipo = $dataRenta['modelo_tipo'];
    $contrato_folio = $dataRenta['contrato_folio'];
    $renta_folio = $dataRenta['renta_folio'];
  }

  $renta_depto = limpiarCadena($_POST['renta_depto']);
  if ($renta_depto == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar un departamento. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[a-zA-Z ?.?]{4,50}", $renta_depto)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El departamento ingresado NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $renta_coor = limpiarCadena($_POST['renta_coor']);
  if ($renta_coor == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar las coordenadas. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $renta_costo = limpiarCadena($_POST['renta_costo']);
  if ($renta_costo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar el costo, de no tenerlo en el momento dejarlo en "0". (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $renta_costo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Costo mensual de la renta). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $renta_inc_esc = limpiarCadena($_POST['renta_inc_esc']);
  if ($renta_inc_esc != "") {
    if (verificarDatos("^[0-9]+$", $renta_inc_esc)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Cantidad Incluida a ESC). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }
  $renta_exc_esc = limpiarCadena($_POST['renta_exc_esc']);
  if ($renta_exc_esc != "") {
    if (verificarDatos("^([0-9]+\.?[0-9]{0,3})$", $renta_exc_esc)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Costo de excedente a ESC). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  $renta_inc_bn = limpiarCadena($_POST['renta_inc_bn']);
  if ($renta_inc_bn != "") {
    if (verificarDatos("^[0-9]+$", $renta_inc_bn)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Cantidad Incluida a B&N). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }
  $renta_exc_bn = limpiarCadena($_POST['renta_exc_bn']);
  if ($renta_exc_bn != "") {
    if (verificarDatos("^([0-9]+\.?[0-9]{0,3})$", $renta_exc_bn)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Costo de excedente a B&N). (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if (isset($_POST['renta_inc_col']) && isset($_POST['renta_exc_col'])) {
    $renta_inc_col = limpiarCadena($_POST['renta_inc_col']);
    if ($renta_inc_col != "") {
      if (verificarDatos("^[0-9]+$", $renta_inc_col)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Cantidad Incluida a COLOR). (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
    $renta_exc_col = limpiarCadena($_POST['renta_exc_col']);
    if ($renta_exc_col != "") {
      if (verificarDatos("^([0-9]+\.?[0-9]{0,3})$", $renta_exc_col)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Costo de excedente a COLOR). (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  } else {
    $renta_inc_col = 0;
    $renta_exc_col = 0;
  }


  $renta_contacto = limpiarCadena($_POST['renta_contacto']);
  if ($renta_contacto == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar un contacto en la renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[a-zA-Z ?.?]{5,50}", $renta_contacto)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El contacto de renta NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $renta_telefono = limpiarCadena($_POST['renta_telefono']);
  if (verificarDatos("[0-9]{0,15}", $renta_telefono)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El telefono NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // ------------------------------------------------------------------------- //


  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|FIN|~~ Comprobacion de Formulario ~~~|FIN|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
  $update = "UPDATE Rentas SET
    renta_depto = '$renta_depto',
    renta_coor = '$renta_coor',
    renta_contacto = '$renta_contacto',
    renta_telefono = '$renta_telefono',
    renta_costo = '$renta_costo',
    renta_inc_esc = '$renta_inc_esc',
    renta_inc_bn = '$renta_inc_bn',
    renta_inc_col = '$renta_inc_col',
    renta_exc_esc = '$renta_exc_esc',
    renta_exc_bn = '$renta_exc_bn',
    renta_exc_col = '$renta_exc_col',
    renta_stock_K = '{$valores_stock['K']}',
    renta_stock_M = '{$valores_stock['M']}',
    renta_stock_C = '{$valores_stock['C']}',
    renta_stock_Y = '{$valores_stock['Y']}',
    renta_stock_R = '{$valores_stock['R']}'
    WHERE renta_id = '$renta_id'";
  if (sentenciaData($update)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ La Renta No. ' . $contrato_folio . '-' . $renta_folio . ' se actualizo correctamente. \(- _ -)/',
      'Tipo' => 'success'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) No se pudo actualizar el registro de renta. (o _ O)',
      'Tipo' => 'error'
    ];
  }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

  echo json_encode($alerta);
  exit();
}
