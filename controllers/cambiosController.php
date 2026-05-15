<?php

// ============================ Controladores de Cambios ============================ //
/* --------------------- Funcion Guardar Archivos PDF --------------------- */
function saveCambioPDF($temp_archivo, $folio, $fecha_anio, $fecha_mes)
{

  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
  $docDir = SERVERDIR . 'DocsCR/CambiosDeEquipos/';
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

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  if (file_exists($docDir . $folio . ".pdf")) {
    $result = [
      'status' => false,
      'result' => 'La evidencia, ya existe'
    ];
  } else if (move_uploaded_file($temp_archivo, $docDir . $folio . ".pdf")) {
    $result = [
      'status' => true
    ];
  } else {
    $result = [
      'status' => false,
      'result' => 'No se pudo guardar la evidencia.'
    ];
  }

  return $result;
} // Fin del la Funcion

function cambioAdd()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|INICIO|~~~ Comprobacion de Formulario ~~~|INICIO|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //


  $cambio_fecha = limpiarCadena($_POST['cambio_fecha']);
  if ($cambio_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar la Fecha de Retiro. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $cambio_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La fecha NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    list($cambio_anio, $cambio_mes, $cambio_dia) = explode("-", $cambio_fecha);
  }

  $cambio_motivo = limpiarCadena($_POST['cambio_motivo']);
  if ($cambio_motivo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario seleccionar tipo de retiro. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($cambio_motivo != "Por Reparacion" && $cambio_motivo != "Fallos Constantes" && $cambio_motivo != "Peticion del Cliente" && $cambio_motivo != "Decicion Interna") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se reconoce el motivo del cambio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  $cambio_renta_id = limpiarCadena(decryption($_POST['cambio_renta_id']));
  if ($cambio_renta_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario seleccionar una Renta. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_renta_id)) {
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
                    WHERE renta_id = '$cambio_renta_id'";
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
      $cambio_equipoRet_id = $rentaData['renta_equipo_id'];
      $equipoRet_serie = $rentaData['equipo_serie'];
      $contrato_folio = $rentaData['contrato_folio'];
      $renta_folio = $rentaData['renta_folio'];
      $cliente_rs = $rentaData['cliente_rs'];
      $cliente_rfc = $rentaData['cliente_rfc'];
      $renta_depto = $rentaData['renta_depto'];
    }
  }

  $cambio_equipoIng_id = limpiarCadena(decryption($_POST['cambio_equipoIng_id']));
  if ($cambio_equipoIng_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario ingresar un Equipo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_equipoIng_id)) {
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
        WHERE equipo_id = '$cambio_equipoIng_id'";
    $checkEquID = consultaData($equipoSQL);
    if ($checkEquID['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El Equipo seleccionado no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $equipoSQL .= "AND equipo_estado = 'Espera'";
      $checkEquID = consultaData($equipoSQL);
      if ($checkEquID['numRows'] == 0) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(TT _ TT) El Equipo seleccionado no esta disponible. (TT _ TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else {
        $equipoData = $checkEquID['dataFetch'][0];
        $equipoIng_serie = $equipoData['equipo_serie'];
      }
    }
  }

  $equipo_estado = limpiarCadena($_POST['equipo_estado']);
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


  // ---- INICIO ---- Verificacion de Contadores del equipo Ingresado ---- INICIO ---- //
  $cambio_Ing_esc = limpiarCadena($_POST['cambio_Ing_esc']);
  if ($cambio_Ing_esc == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de Escaneo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ing_esc)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Escaneo). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $cambio_Ing_bn = limpiarCadena($_POST['cambio_Ing_bn']);
  if ($cambio_Ing_bn == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de B&N. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ing_bn)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de B&N). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $cambio_Ing_col = limpiarCadena($_POST['cambio_Ing_col']);
  if ($cambio_Ing_col == "") {
    $cambio_Ing_col = 0;
  } else if (verificarDatos("^[0-9]+$", $cambio_Ing_col)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Color). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // ----- FIN ----- Verificacion de Contadores del equipo Ingresado ----- FIN ----- //


  // ---- INICIO ---- Verificacion de Contadores del equipo Retirado ---- INICIO ---- //
  $cambio_Ret_esc = limpiarCadena($_POST['cambio_Ret_esc']);
  if ($cambio_Ret_esc == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de Escaneo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ret_esc)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Escaneo). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $cambio_Ret_bn = limpiarCadena($_POST['cambio_Ret_bn']);
  if ($cambio_Ret_bn == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de B&N. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ret_bn)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de B&N). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  $cambio_Ret_col = limpiarCadena($_POST['cambio_Ret_col']);
  if ($cambio_Ret_col == "") {
    $cambio_Ret_col = 0;
  } else if (verificarDatos("^[0-9]+$", $cambio_Ret_col)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Color). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // ----- FIN ----- Verificacion de Contadores del equipo Retirado ----- FIN ----- //

  $cambio_comm = limpiarCadena($_POST['cambio_comm']);
  if ($cambio_comm == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Espera',
      'Texto' => '(TT _ TT) El comentario de cambio es obligatorio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $cambio_comm = $_POST['cambio_comm'];
  }



  for ($cambio_folio = "CE000001";; $cambio_folio++) {
    $check_folio = consultaData("SELECT cambio_folio FROM Cambios WHERE cambio_folio = '$cambio_folio'");
    if ($check_folio['numRows'] == 0) {
      break;
    }
  }

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|FIN|~~ Comprobacion de Formulario ~~~|FIN|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //


  // ------ INICIO ------ Guardado del Archivo ------ INICIO ------ //
  // ***************** Evidencia en PDF ***************** //
  if ($_FILES['cambio_file']['name'] == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No ingresaste evidencia. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($_FILES['cambio_file']['type'] != "application/pdf") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El formato del archivo debe ser PDF. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $tempDir = $_FILES['cambio_file']['tmp_name'];
    $saveDoc = saveCambioPDF($tempDir, $cambio_folio, $cambio_anio, $cambio_mes, $cambio_dia);
    if ($saveDoc['status'] == false) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) ' . $saveDoc['result'] . '. (TT _ TT)',
        'Tipo' => 'error'
      ];
    }
  }
  // **************************************************** //
  // ------- FIN ------- Guardado del Archivo ------- FIN ------- //

  $insertCam = "INSERT INTO Cambios (cambio_fecha, cambio_folio, cambio_renta_id, cambio_motivo, cambio_equipoRet_id, cambio_Ret_esc, cambio_Ret_bn, cambio_Ret_col, cambio_equipoIng_id, cambio_Ing_esc, cambio_Ing_bn, cambio_Ing_col, cambio_comm) VALUES ('$cambio_fecha', '$cambio_folio', '$cambio_renta_id', '$cambio_motivo', '$cambio_equipoRet_id', '$cambio_Ret_esc', '$cambio_Ret_bn', '$cambio_Ret_col', '$cambio_equipoIng_id', '$cambio_Ing_esc', '$cambio_Ing_bn', '$cambio_Ing_col', '$cambio_comm')";
  if (sentenciaData($insertCam)) {
    $updateEquIng = "UPDATE Equipos SET equipo_estado = 'Rentado' WHERE equipo_id = '$cambio_equipoIng_id'";
    $updateEquRet = "UPDATE Equipos SET equipo_estado = '$equipo_estado' WHERE equipo_id = '$cambio_equipoRet_id'";
    if (sentenciaData($updateEquIng) && sentenciaData($updateEquRet)) {
      $updateRenta = "UPDATE Rentas SET renta_equipo_id = '$cambio_equipoIng_id' WHERE renta_id = '$cambio_renta_id'";
      if (sentenciaData($updateRenta)) {
        $alerta = [
          'Alerta' => 'recargar',
          'Titulo' => 'Registro Completado',
          'Texto' => '\(- _ -)/ El cambio de  ' . $cliente_rs . ' - ' . $renta_depto . ' (' . $contrato_folio . '-' . $renta_folio . '), se realizo correctamente. \(- _ -)/',
          'Tipo' => 'success'
        ];
      } else {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(o _ O) No se pudo actualizar el nuevo equipo asignado a la renta. (o _ O)',
          'Tipo' => 'error'
        ];
      }
    } else {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(o _ O) No se pudo actualizar el estado de los equipos. (o _ O)',
        'Tipo' => 'error'
      ];
    }
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(o _ O) No se pudo realizar el registro de retiro. (o _ O)',
      'Tipo' => 'error'
    ];
  }

  // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

  echo json_encode($alerta);
  exit();
}

function cambioEdit()
{
  $cambio_id = limpiarCadena(decryption($_POST['cambioEdit']));
  $cambio_fecha = limpiarCadena($_POST['cambio_fecha']);
  $cambio_Ing_esc = limpiarCadena($_POST['cambio_Ing_esc']);
  $cambio_Ing_bn = limpiarCadena($_POST['cambio_Ing_bn']);
  $cambio_Ing_col = limpiarCadena($_POST['cambio_Ing_col']);
  $cambio_Ret_esc = limpiarCadena($_POST['cambio_Ret_esc']);
  $cambio_Ret_bn = limpiarCadena($_POST['cambio_Ret_bn']);
  $cambio_Ret_col = limpiarCadena($_POST['cambio_Ret_col']);
  $cambio_comm = limpiarCadena($_POST['cambio_comm']);


  if ($cambio_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Cambio no puede estar vacio, recarga la pagina. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El ID de Cambio no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $SQL = "SELECT * FROM Cambios
        WHERE cambio_id = '$cambio_id'";
    $checkRepCambio = consultaData($SQL);
    if ($checkRepCambio['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El cambio de equipo no existe. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $cambData = $checkRepCambio['dataFetch'][0];
      $cambio_folio = $cambData['cambio_folio'];
    }
  }

  if ($cambio_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar la Fecha de Retiro. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $cambio_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) La fecha NO cuenta con el Formato Solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    list($cambio_anio, $cambio_mes, $cambio_dia) = explode("-", $cambio_fecha);
  }

  // ----------------------- CONTADORES
  // ----------------- EQUIPO INGRESADO
  if ($cambio_Ing_esc == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de Escaneo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ing_esc)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Escaneo). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($cambio_Ing_bn == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de BN. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ing_bn)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de BN). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($cambio_Ing_col == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de Color. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ing_col)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Color). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // --------- FIN --- EQUIPO INGRESADO

  // ------------------ EQUIPO RETIRADO
  if ($cambio_Ret_esc == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de Escaneo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ret_esc)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Escaneo). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($cambio_Ret_bn == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de BN. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ret_bn)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de BN). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($cambio_Ret_col == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesario agregar contador de Color. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cambio_Ret_col)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Formato ingresado Incorrecto (Contador de Color). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  // ---------- FIN --- EQUIPO RETIRADO
  // --------- FIN --------- CONTADORES

  $cambio_comm = limpiarCadena($_POST['cambio_comm']);
  if ($cambio_comm == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Espera',
      'Texto' => '(TT _ TT) El comentario de cambio es obligatorio. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (isset($_FILES['cambio_file'])) {
    if ($_FILES['cambio_file']['name'] == "") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) No Ingresaste la evidencia. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($_FILES['cambio_file']['type'] != "application/pdf") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) El formato de la evidencia debe ser PDF. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $temp_file = $_FILES['cambio_file']['tmp_name'];
      $saveDoc = saveCambioPDF($temp_file, $cambio_folio, $cambio_anio, $cambio_mes);
      if ($saveDoc['status'] == false) {
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
  }

  $updateSQL = "UPDATE Cambios SET
                cambio_fecha = '$cambio_fecha',
                cambio_Ing_esc = '$cambio_Ing_esc',
                cambio_Ing_bn = '$cambio_Ing_bn',
                cambio_Ing_col = '$cambio_Ing_col',
                cambio_Ret_esc = '$cambio_Ret_esc',
                cambio_Ret_bn = '$cambio_Ret_bn',
                cambio_Ret_col = '$cambio_Ret_col',
                cambio_comm = '$cambio_comm'
                WHERE cambio_id = '$cambio_id'";

  if (sentenciaData($updateSQL)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Se actualizaron los datos correctamente. \(- _ -)/',
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
  echo json_encode($alerta);
  exit();
}
