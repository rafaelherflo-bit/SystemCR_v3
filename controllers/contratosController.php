<?php

// ============================ Controladores de Contratos ============================ //
function contratoFileUpload()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $contrato_id = limpiarCadena(decryption($_POST['contrato_id_upload']));

  if ($contrato_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario ingresar un ID de Contrato. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $contrato_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Contrato NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $checkContrato = consultaData("SELECT * FROM Contratos WHERE contrato_id = '$contrato_id'");
    if ($checkContrato['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El Contrato seleccionado no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $contrato_folio = $checkContrato['dataFetch'][0]['contrato_folio'];
    }
  }

  // ************************************************ Evidencia en PDF ************************************************ //
  if (!isset($_FILES['contrato_file_upload'])) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Debes ingresar un archivo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // ****************************************************************************************************************** //


  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
  $docDir = SERVERDIR . 'DocsCR/Contratos/';
  // +======+ Verificar Carpeta Raiz +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  }
  $archivo = $contrato_folio . '.pdf';
  if (file_exists($docDir . $archivo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El archivo ya existe. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  if (move_uploaded_file($_FILES['contrato_file_upload']['tmp_name'], $docDir . $archivo)) {
    if (sentenciaData("UPDATE Contratos SET contrato_fecha_firma = NOW(), contrato_firma_estatus = 1 WHERE contrato_id = $contrato_id")) {
      $alerta = [
        'Alerta' => 'recargar',
        'Titulo' => 'Archivo Guardado',
        'Texto' => '\(- _ -)/ Se guardo el archivo correctamente. \(- _ -)/',
        'Tipo' => 'success'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) Se guardo el archivo, pero no se pudo actualizar el estatus del contrato. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se pudo guardar el archivo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
}


function contratoAdd()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $fecha_inicio = limpiarCadena($_POST['fecha_inicio']);
  $cliente_id = limpiarCadena(decryption($_POST['cliente_id']));
  $contacto = limpiarCadena($_POST['contacto']);
  $telefono = limpiarCadena($_POST['telefono']);

  if ($fecha_inicio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $fecha_inicio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La fecha no tiene el formato adecuado (ej. 2025-01-13). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($cliente_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario ingresar un Cliente. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cliente_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El Cliente NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $checkCliID = consultaData("SELECT * FROM Clientes WHERE cliente_id = '$cliente_id'");
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
      $cliente_rs = $checkCliID['dataFetch'][0]['cliente_rs'];
      $cliente_rfc = $checkCliID['dataFetch'][0]['cliente_rfc'];
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

  // --------------------------------------------- Conseguimos un Folio de Contrato disponible --------------------------------- //

  for ($contrato_folio = "CR001";; $contrato_folio++) {
    $check_folio = consultaData("SELECT contrato_folio FROM Contratos WHERE contrato_folio = '$contrato_folio'");
    if ($check_folio['numRows'] == 0) {
      break;
    }
  }
  // --------------------------------------------------------------------------------------------------------------------------- //



  // --------------------------------------------- AGREGAMOS EL CONTRATO A LOS REGISTROS --------------------------------------------- //
  $insertContratoSQL = "INSERT INTO Contratos (contrato_estado, contrato_finicio, contrato_cliente_id, contrato_folio, contrato_contacto, contrato_telefono) VALUES ('Activo', '$fecha_inicio', '$cliente_id', '$contrato_folio', '$contacto', '$telefono')";
  $insertContrato = insertID($insertContratoSQL);
  if ($insertContrato['status']) {
    $contrato_id = $insertContrato['ID'];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) No se pudo realizar el registro del Contrato. (o _ O)',
      'Tipo' => 'error'
    ];
  }
  // --------------------------------- ^^^ FIN ^^^ AGREGAMOS EL CONTRATO A LOS REGISTROS ^^^ FIN ^^^ --------------------------------- //




  // ================================================= Si el insert del contrato sale correcto obtenemos el ID generado para su uso y continuamos con el registro de la nueva Renta ================================================= //
  $renta_tipo = limpiarCadena($_POST['renta_tipo']);
  $zona_id = limpiarCadena(decryption($_POST['zona_id']));
  $renta_depto = limpiarCadena($_POST['renta_depto']);
  $equipo_id = limpiarCadena(decryption($_POST['equipo_id']));
  $renta_coor = limpiarCadena($_POST['renta_coor']);
  $renta_stock = limpiarCadena($_POST['renta_stock']);

  $renta_costo = limpiarCadena($_POST['renta_costo']);
  $renta_inc_esc = limpiarCadena($_POST['renta_inc_esc']);
  $renta_exc_esc = limpiarCadena($_POST['renta_exc_esc']);
  $renta_inc_bn = limpiarCadena($_POST['renta_inc_bn']);
  $renta_exc_bn = limpiarCadena($_POST['renta_exc_bn']);
  $renta_inc_col = limpiarCadena($_POST['renta_inc_col']);
  $renta_exc_col = limpiarCadena($_POST['renta_exc_col']);

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
  $insertNewRenta = "INSERT INTO Rentas (renta_finicio, renta_contrato_id, renta_zona_id, renta_coor, renta_depto, renta_equipo_id, renta_contacto, renta_telefono, renta_tipo, renta_costo, renta_inc_esc, renta_inc_bn, renta_inc_col, renta_exc_esc, renta_exc_bn, renta_exc_col, renta_stock_K, renta_stock_M, renta_stock_Y, renta_stock_C, renta_stock_R, renta_folio) VALUES ('$fecha_inicio', '$contrato_id', '$zona_id', '$renta_coor', '$renta_depto', '$equipo_id', '$contacto', '$telefono', '$renta_tipo', '$renta_costo', '$renta_inc_esc', '$renta_inc_bn', '$renta_inc_col', '$renta_exc_esc', '$renta_exc_bn', '$renta_exc_col', '$renta_stock_K', '$renta_stock_M', '$renta_stock_Y', '$renta_stock_C', '$renta_stock_R', '$renta_folio')";
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
  // $insertNewUbi = "INSERT INTO EquiposUbi (equUbi_equipo_id, equUbi_fecha_inicio, equUbi_ubi, equUbi_ubicacion) VALUES ('" . $equipo_id . "', '" . $fecha_inicio . "', 1, '" . $renta_id . "')";
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


  list($fecha_anio, $fecha_mes, $fecha_dia) = explode("-", $fecha_inicio);


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
  $docDir = SERVERDIR . 'DocsCR/Lecturas/';
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

  $lectura_pdf = $fecha_dia . '-' . $fecha_mes . '-' . $fecha_anio . ' - ' . $lectura_pdf . ' - Toma de Lectura.jpg';

  if (!isset($_FILES['lectura_estado']) && !isset($_FILES['lectura_formato'])) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Espera',
      'Texto' => '(o _ o) Debes agregar una evidencia. (o _ o)',
      'Tipo' => 'info'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (isset($_FILES['lectura_estado'])) {
    if ($_FILES['lectura_estado']['name'] == "") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) No Ingresaste Pagina de Estado. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($_FILES['lectura_estado']['type'] != "image/jpeg") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El formato de la pagina de estado debe ser en JPEG. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $temp_lectura_estado = $_FILES['lectura_estado']['tmp_name'];
      // +======+ Verificar Carpeta Mes +======+ //
      if (!file_exists($docDir)) {
        mkdir($docDir, 0755, true);
        $PEDir = $docDir . 'PE/';
      } else {
        $PEDir = $docDir . 'PE/';
      }
      // +======+ Verificar Carpeta de PE +======+ //
      if (!file_exists($PEDir)) {
        mkdir($PEDir, 0755, true);
      }
      // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
      if (file_exists($PEDir . $lectura_pdf)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) La pagina de estado, ya existe. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
      if (!move_uploaded_file($temp_lectura_estado, $PEDir . $lectura_pdf)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) No se pudo guardar la pagina de estado. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }

  if (isset($_FILES['lectura_formato'])) {
    if ($_FILES['lectura_formato']['name'] == "") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) No Ingresaste Formato de Lectura. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($_FILES['lectura_formato']['type'] != "image/jpeg") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El formato del formato de lectura debe ser en JPEG. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $temp_lectura_formato = $_FILES['lectura_formato']['tmp_name'];
      // +======+ Verificar Carpeta Mes +======+ //
      if (!file_exists($docDir)) {
        mkdir($docDir, 0755, true);
        $formatoDir = $docDir . 'Formatos/';
      } else {
        $formatoDir = $docDir . 'Formatos/';
      }
      // +======+ Verificar Carpeta de Formatos +======+ //
      if (!file_exists($formatoDir)) {
        mkdir($formatoDir, 0755, true);
      }
      // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
      if (file_exists($formatoDir . $lectura_pdf)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) El formato de lectura, ya existe. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
      if (!move_uploaded_file($temp_lectura_formato, $formatoDir . $lectura_pdf)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) No se pudo guardar el formato de lectura. (o _ O)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
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
  $insert = "INSERT INTO Lecturas (lectura_renta_id, lectura_equipo_id, lectura_pdf, lectura_esc, lectura_bn, lectura_col, lectura_fecha) VALUES ('$renta_id', '$equipo_id', '$lectura_pdf', '$lectura_esc', '$lectura_bn', '$lectura_col', '$fecha_inicio')";
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
