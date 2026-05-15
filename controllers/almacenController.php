<?php
/* --------------------- Funcion Guardar Archivos PDF --------------------- */
function guardarAlmMPDF($tmpDir, $folio)
{

  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
  $docDir = SERVERDIR . 'DocsCR/ALMACEN/';
  // +======+ Verificar Carpeta Almacen +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  } else {
    $docDir = $docDir . 'Evidencias/';
  }
  // +======+ Verificar Carpeta Evidencias +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  }

  $fileDir = $docDir . $folio . ".pdf";

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  $result = [
    'status' => true,
    'result' => "Exito."
  ];
  if (file_exists($fileDir)) {
    $result = [
      'status' => false,
      'result' => 'Ya existe un archivo con el mismo nombre (' . $folio . ').'
    ];
  } else if (!move_uploaded_file($tmpDir, $fileDir)) {
    $result = [
      'status' => false,
      'result' => 'No se pudo guardar la evidencia (' . $folio . ').'
    ];
  }
  return $result;
} // Fin del la Funcion


function cambiarFolioAlmMPDF($origen, $folio)
{
  $docDir = SERVERDIR . 'DocsCR/ALMACEN/';
  // +======+ Verificar Carpeta Almacen +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  } else {
    $docDir = $docDir . 'Evidencias/';
  }
  // +======+ Verificar Carpeta Evidencias +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  }

  $fileDir = $docDir . $folio . ".pdf";

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  $result = [
    'status' => true,
    'result' => "Exito."
  ];
  if (file_exists($fileDir)) {
    $result = [
      'status' => false,
      'result' => 'Ya existe un archivo con el mismo nombre (' . $folio . ').'
    ];
  } else if (!rename($origen, $fileDir)) {
    $result = [
      'status' => false,
      'result' => 'No se pudo renombrar la evidencia (' . $folio . ').'
    ];
  }
  return $result;
}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// ============================ Iniciar Registro de Movimiento V2.0 ============================ //
function iniciar_AlmM()
{
  // ------------------- Comprobacion de Formulario ------------------- //
  session_start();
  $AlmM_uS_id = limpiarCadena($_SESSION['id']);
  $AlmM_fecha = limpiarCadena($_POST['AlmM_fecha']);
  $AlmM_tipo = limpiarCadena($_POST['AlmM_tipo']);
  $AlmM_comentario = limpiarCadena($_POST['AlmM_comentario']);


  if ($AlmM_tipo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar el tipo de Movimiento',
      'Texto' => '(TT__TT) No se ingreso Categoria. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (!in_array($AlmM_tipo, range(0, 3))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar el tipo de Movimiento',
      'Texto' => '(TT__TT) La categoria no es reconocida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($AlmM_tipo == 0 || $AlmM_tipo == 1) {
    $AlmM_empleado = limpiarCadena(decryption($_POST['AlmM_empleado']));
    $AlmM_identificador = limpiarCadena(decryption($_POST['AlmM_empleado']));
    $AlmM_IVA = 0;
  } else {
    $AlmM_empleado = limpiarCadena(decryption($_POST['AlmM_empleado']));
    $AlmM_identificador = limpiarCadena(decryption($_POST['AlmM_identificador']));
    if ($AlmM_tipo == 3) {
      $AlmM_IVA = limpiarCadena($_POST['AlmM_IVA']);
      if ($AlmM_IVA == "") {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Error en Ingreso de Cantidad',
          'Texto' => '(TT__TT) No se ingreso cantidad de IVA. (TT__TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if (verificarDatos("[0-9]{0,100}", $AlmM_IVA)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Error en Ingreso de Cantidad',
          'Texto' => '(TT__TT) El IVA no puede ser una cantidad negativa o mayor a 100. (TT__TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    } else {
      $AlmM_IVA = 0;
    }
  }

  if ($AlmM_identificador == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en ingreso de identificador',
      'Texto' => '(TT _ TT) No se ingreso identificador. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $AlmM_identificador)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en ingreso de identificador',
      'Texto' => '(TT _ TT) El identificador no tiene el formato correcto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmM_empleado == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en ingreso de identificador',
      'Texto' => '(TT _ TT) No se ingreso Empleado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $AlmM_empleado)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en ingreso de identificador',
      'Texto' => '(TT _ TT) El ID de empleado no tiene el formato correcto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmM_tipo == 0) {
    $rentaQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $AlmM_identificador);
    if ($rentaQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el Empleado ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $folTipo = "RE";
    }
  } else if ($AlmM_tipo == 1) {
    $rentaQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $AlmM_identificador);
    if ($rentaQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el Empleado ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $folTipo = "RSI";
    }
  } else if ($AlmM_tipo == 2) {
    $rentaQRY = consultaData("SELECT * FROM Rentas WHERE renta_id = " . $AlmM_identificador);
    $usuarioQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $AlmM_empleado);
    if ($usuarioQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el Empleado ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($rentaQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe la renta ingresada. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $folTipo = "RSR";
    }
  } else if ($AlmM_tipo == 3) {
    $clienteQRY = consultaData("SELECT * FROM Clientes WHERE cliente_id = " . $AlmM_identificador);
    $usuarioQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $AlmM_empleado);
    if ($usuarioQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el Empleado ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($clienteQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el cliente ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $folTipo = "RSV";
    }
  }

  if ($AlmM_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la fecha',
      'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $AlmM_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la fecha',
      'Texto' => '(TT _ TT) La fecha no tiene el formato adecuado (ej. 2025-01-13). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (dateCompare($AlmM_fecha, "mayor", date("Y-n-d"))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la Fecha',
      'Texto' => '(TT _ TT) La fecha no puede ser mayor que la actual. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // ------------------------------------- Obtener el Folio ------------------------------------ //
  for ($AlmM_folio = $folTipo . "-000001";; $AlmM_folio++) {
    $check_folio = consultaData("SELECT AlmM_folio FROM AlmacenM WHERE AlmM_folio = '$AlmM_folio'");
    if ($check_folio['numRows'] == 0) {
      break;
    }
  }
  // ------------------------- ^^^ FIN ^^^ Obtener el Folio ^^^ FIN ^^^ ------------------------ //


  if ($AlmM_comentario == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun comentario (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($AlmM_comentario) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas de 10 caracteres en comentario. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // Terminan las comprobaciones para el toner Proveedor de Almacen //
  $insertID = insertID("INSERT INTO AlmacenM (AlmM_uS_id, AlmM_folio, AlmM_fecha, AlmM_tipo, AlmM_IVA, AlmM_identificador, AlmM_empleado, AlmM_comentario) VALUES ('$AlmM_uS_id', '$AlmM_folio', '$AlmM_fecha', '$AlmM_tipo', '$AlmM_IVA', '$AlmM_identificador', '$AlmM_empleado', '$AlmM_comentario')");
  if ($insertID['status']) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Movimientos/Detalles/' . encryption($insertID['ID'])
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo iniciar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}
// ============================= Editar Registro de Movimiento V2.0 ============================ //
function editar_AlmM()
{
  // ------------------- Comprobacion de Formulario ------------------- //
  session_start();
  $AlmM_uS_id = limpiarCadena($_SESSION['id']);
  $AlmM_id = limpiarCadena(decryption($_POST['editar_AlmM']));
  $AlmM_fecha = limpiarCadena($_POST['AlmM_fecha']);
  $AlmM_tipo = limpiarCadena($_POST['AlmM_tipo']);
  $AlmM_comentario = limpiarCadena($_POST['AlmM_comentario']);

  $QRY = consultaData("SELECT * FROM AlmacenM WHERE AlmM_id = $AlmM_id");
  if ($QRY['numRows'] == 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No existe el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $AlmMDATA = $QRY['dataFetch'][0];
    // $AlmM_folio = $AlmMDATA['AlmM_folio'];
  }

  if ($AlmM_fecha == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la Fecha',
      'Texto' => '(TT _ TT) No se ingreso fecha. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $AlmM_fecha)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la Fecha',
      'Texto' => '(TT _ TT) La fecha no tiene el formato adecuado (ej. 2025-01-13). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (dateCompare($AlmM_fecha, "mayor", date("Y-n-d"))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la Fecha',
      'Texto' => '(TT _ TT) La fecha no puede ser mayor que la actual. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmM_comentario == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun comentario (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($AlmM_comentario) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas de 10 caracteres en comentario. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmM_tipo == 0 || $AlmM_tipo == 1) {
    $AlmM_empleado = limpiarCadena(decryption($_POST['AlmM_empleado']));
    $AlmM_identificador = limpiarCadena(decryption($_POST['AlmM_empleado']));
    $AlmM_IVA = 0;
  } else {
    $AlmM_empleado = limpiarCadena(decryption($_POST['AlmM_empleado']));
    $AlmM_identificador = limpiarCadena(decryption($_POST['AlmM_identificador']));
    if ($AlmM_tipo == 3) {
      $AlmM_IVA = limpiarCadena($_POST['AlmM_IVA']);
      if ($AlmM_IVA == "") {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Error en Ingreso de Cantidad',
          'Texto' => '(TT__TT) No se ingreso cantidad de IVA. (TT__TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      } else if (verificarDatos("[0-9]{0,100}", $AlmM_IVA)) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Error en Ingreso de Cantidad',
          'Texto' => '(TT__TT) El IVA no puede ser una cantidad negativa o mayor a 100. (TT__TT)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    } else {
      $AlmM_IVA = 0;
    }
  }

  if ($AlmM_identificador == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en ingreso de identificador',
      'Texto' => '(TT _ TT) No se ingreso identificador. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $AlmM_identificador)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en ingreso de identificador',
      'Texto' => '(TT _ TT) El identificador no tiene el formato correcto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmM_empleado == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en ingreso de identificador',
      'Texto' => '(TT _ TT) No se ingreso Empleado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $AlmM_empleado)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en ingreso de identificador',
      'Texto' => '(TT _ TT) El ID de empleado no tiene el formato correcto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmM_tipo == 0) {
    $rentaQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $AlmM_identificador);
    if ($rentaQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el Empleado ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $folTipo = "RE";
    }
  } else if ($AlmM_tipo == 1) {
    $rentaQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $AlmM_identificador);
    if ($rentaQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el Empleado ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $folTipo = "RSI";
    }
  } else if ($AlmM_tipo == 2) {
    $rentaQRY = consultaData("SELECT * FROM Rentas WHERE renta_id = " . $AlmM_identificador);
    $usuarioQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $AlmM_empleado);
    if ($usuarioQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el Empleado ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($rentaQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe la renta ingresada. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $folTipo = "RSR";
    }
  } else if ($AlmM_tipo == 3) {
    $clienteQRY = consultaData("SELECT * FROM Clientes WHERE cliente_id = " . $AlmM_identificador);
    $usuarioQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $AlmM_empleado);
    if ($usuarioQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el Empleado ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($clienteQRY['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Error al consultar el identificador',
        'Texto' => '(TT__TT) No existe el cliente ingresado. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $folTipo = "RSV";
    }
  }



  $UPDATE = "UPDATE AlmacenM SET
              AlmM_uS_id = '$AlmM_uS_id',
              AlmM_fecha = '$AlmM_fecha',
              AlmM_comentario = '$AlmM_comentario',
              AlmM_tipo = '$AlmM_tipo',
              AlmM_IVA = '$AlmM_IVA',
              AlmM_identificador = '$AlmM_identificador',
              AlmM_empleado = '$AlmM_empleado'";


  if ($AlmM_tipo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Categoria. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (!in_array($AlmM_tipo, range(0, 3))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) La categoria no es reconocida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmM_tipo != $AlmMDATA['AlmM_tipo']) {
    // ------------------------------------- Obtener el Folio ------------------------------------ //
    for ($AlmM_folio = $folTipo . "-000001";; $AlmM_folio++) {
      $check_folio = consultaData("SELECT AlmM_folio FROM AlmacenM WHERE AlmM_folio = '$AlmM_folio'");
      if ($check_folio['numRows'] == 0) {
        break;
      }
    }
    // ------------------------- ^^^ FIN ^^^ Obtener el Folio ^^^ FIN ^^^ ------------------------ //


    $UPDATE = $UPDATE . ", AlmM_folio = '$AlmM_folio'";
  }

  $UPDATE = $UPDATE . " WHERE AlmM_id = '$AlmM_id'";


  // Terminan las comprobaciones para el toner Proveedor de Almacen //
  if (sentenciaData($UPDATE)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Movimientos/Detalles/' . encryption($AlmM_id)
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo Actualizar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}
// ============================ Activar Registro de Movimiento V2.0 ============================ //
function active_AlmM()
{
  // ------------------- Comprobacion de Formulario ------------------- //
  $AlmM_id = limpiarCadena(decryption($_POST['active_AlmM']));
  $AlmM_QRY = consultaData("SELECT * FROM AlmacenD WHERE AlmDM_id = '$AlmM_id'");
  if ($AlmM_QRY['numRows'] == 0) {
    responderError('(TT _ TT) El registro principal, no tiene registros de productos agregados. (TT _ TT)');
  } else {
    if ($AlmM_id == "") {
      responderError('(TT _ TT) No hay un valor en el identificador de registro principal. (TT _ TT)');
    } else if (verificarDatos("[0-9]{1,150}", $AlmM_id)) {
      responderError('(> _ <) El valor en el identificador de registro principal, NO cuenta con el Formato Solicitado. (> _ <)');
    } else {
      $checkID = consultaData("SELECT * FROM AlmacenM WHERE AlmM_id = '$AlmM_id'");
      if ($checkID['numRows'] == 0) {
        responderError('(o _ O) El registro principal no existe. (o _ O)');
      } else {
        $DATA = $checkID['dataFetch'][0];
        $AlmM_folio = $DATA['AlmM_folio'];
        $AlmM_tipo = $DATA['AlmM_tipo'];
        $AlmM_identificador = $DATA['AlmM_identificador'];
      }
    }


    if (!in_array($AlmM_tipo, range(0, 3))) {
      responderError('(TT _ TT) Tipo de Movimiento Desconocido. (TT _ TT)');
    }


    /*
      TIPO      VALOR
      0         Entrada
      1         Salida Interna
      2         Salida Renta
      3         Salida Venta
      */
    if ($AlmM_tipo != 0) {
      // Variable con funcion que realiza un array recopilando las entradas y salidas de los productos en Almacen basado en los movimientos (Funcion anidada en SERVER.php)
      $QRY_AlmP_total = consultaAlmacenP()['dataFetch'];

      $SQL_AlmP_AlmMid = "SELECT AlmDP_id, SUM(AlmD_cantidad) AS AlmP_stock, AlmP_codigo, AlmP_subcat_id FROM AlmacenD
                            INNER JOIN AlmacenP ON AlmacenD.AlmDP_id = AlmacenP.AlmP_id
                            INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id
                            WHERE AlmDM_id = '$AlmM_id'
                            AND AlmM_estado = 0
                            GROUP BY AlmDP_id
                            ORDER BY AlmDP_id ASC";
      $QRY_AlmDP_AlmMid = consultaData($SQL_AlmP_AlmMid);

      $entrega = ['K' => 0, 'M' => 0, 'C' => 0, 'Y' => 0];

      // Verificacion de Stock suficiente en almacen.
      foreach ($QRY_AlmDP_AlmMid['dataFetch'] as $AlmP_AlmMid) {
        $indice = array_search($AlmP_AlmMid['AlmDP_id'], array_column($QRY_AlmP_total, 'AlmP_id'));

        if ($indice !== false && $QRY_AlmP_total[$indice]['AlmP_stock'] < $AlmP_AlmMid['AlmP_stock']) {
          $menos = $QRY_AlmP_total[$indice]['AlmP_stock'] - $AlmP_AlmMid['AlmP_stock'];
          responderError('(TT _ TT) No es posible realizar la salida, el stock se reduce en ' . $menos . ', para el producto ' . $QRY_AlmP_total[$indice]['AlmP_codigo'] . ' (TT _ TT)');
        }

        if ($AlmM_tipo == 2) {
          $sub = $AlmP_AlmMid['AlmP_subcat_id'];
          if ($sub == 0 || $sub == 1) $entrega['K']++; // 0=BN, 1=Negro Color
          if ($sub == 2) $entrega['M']++;
          if ($sub == 3) $entrega['C']++;
          if ($sub == 4) $entrega['Y']++;
        }
      }

      if ($AlmM_tipo == 2) {
        $renta_id = $AlmM_identificador;
        $sqlRenta = "SELECT R.*, M.modelo_tipo FROM Rentas R 
                        INNER JOIN Equipos E ON R.renta_equipo_id = E.equipo_id 
                        INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id 
                      WHERE renta_id = $renta_id";
        $qryRenta = consultaData($sqlRenta);

        if ($qryRenta['numRows'] == 1) {
          $dataR = $qryRenta['dataFetch'][0];

          // 3. ACTUALIZACIÓN PROGRESIVA (SUMAR AL STOCK ACTUAL)
          // Si es monocromático, solo sumamos al K y dejamos los demás en 0 o NULL
          $newK = $dataR['renta_stock_K'] + $entrega['K'];

          if ($dataR['modelo_tipo'] == "Monocromatico") {
            $sqlUpdStock = "UPDATE Rentas SET renta_stock_K = $newK WHERE renta_id = $renta_id";
          } else {
            $newM = $dataR['renta_stock_M'] + $entrega['M'];
            $newC = $dataR['renta_stock_C'] + $entrega['C'];
            $newY = $dataR['renta_stock_Y'] + $entrega['Y'];
            $sqlUpdStock = "UPDATE Rentas SET 
                                  renta_stock_K = $newK, 
                                  renta_stock_M = $newM, 
                                  renta_stock_C = $newC, 
                                  renta_stock_Y = $newY 
                                  WHERE renta_id = $renta_id";
          }

          if (!sentenciaData($sqlUpdStock)) {
            responderError('(TT _ TT) Error al actualizar el stock en sitio del cliente. (TT _ TT)');
          }
        }
      }
    }


    // --------------------------------------------- Verificando Input File --------------------------------------------- //
    // ***************** Evidencia en PDF ***************** //
    if (isset($_POST['AlmM_file'])) {
      responderError('(TT _ TT) Formato Incorrecto para la Evidencia de Movimiento. (TT _ TT)');
    } else {
      if ($_FILES['AlmM_file']['name'] == "") {
        responderError('(TT _ TT) No Ingresaste Evidencia de Movimiento. (TT _ TT)');
      } else if ($_FILES['AlmM_file']['type'] != "application/pdf") {
        responderError('(TT _ TT) El Formato de la Evidencia debe ser en PDF. (TT _ TT)');
      } else {
        $temp_cobM_archivo = $_FILES['AlmM_file']['tmp_name'];
        $guardarAlmMPDF = guardarAlmMPDF($temp_cobM_archivo, $AlmM_folio);
        if (!$guardarAlmMPDF['status']) {
          responderError('(TT _ TT) ' . $guardarAlmMPDF['result'] . '. (TT _ TT)');
        }
      }
    }
    // **************************************************** //
    // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //

    $UPDATE = "UPDATE AlmacenM SET AlmM_estado = 1 WHERE AlmM_id = '$AlmM_id'";
    if (sentenciaData($UPDATE)) {
      $alerta = [
        'Alerta' => 'redireccionar',
        'url' => SERVERURL . 'Almacen/Movimientos/CustomMonth/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL"))
      ];
      // Lanzamos La respuesta Final //
      echo json_encode($alerta);
      exit();
    } else {
      responderError('(TT _ TT) No se pudo actualizar el estado del registro principal. (TT _ TT)');
    }
  }
}
// ============================ Agregar Registro de Movimiento V2.0 ============================ //
function agregar_AlmDM()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmDM_id = limpiarCadena(decryption($_POST['agregar_AlmDM']));
  $AlmDP_id = limpiarCadena(decryption($_POST['AlmDP_id']));
  $AlmD_cantidad = limpiarCadena($_POST['AlmD_cantidad']);
  $AlmD_comentario = limpiarCadena($_POST['AlmD_comentario']);

  // $QRY_prod = consultaData("SELECT * FROM AlmacenD WHERE AlmM_id = '$AlmDM_id' AND AlmP_id = '$AlmDP_id'");
  // if ($QRY_prod['numRows'] > 0) {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Error en Ingreso de Registro Principal',
  //     'Texto' => '(TT__TT) No existe el registro principal. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // }

  $QRY = consultaData("SELECT * FROM AlmacenM WHERE AlmM_id = $AlmDM_id");
  if ($QRY['numRows'] == 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en Ingreso de Registro Principal',
      'Texto' => '(TT__TT) No existe el registro principal. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $DATA = $QRY['dataFetch'][0];
    $AlmM_tipo = $DATA['AlmM_tipo'];
  }

  if ($AlmDP_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en Ingreso de Producto',
      'Texto' => '(TT _ TT) Es necesario ingresar Producto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]+", $AlmDP_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en Ingreso de Producto',
      'Texto' => '(> _ <) El formato del producto seleccionado no es el correcto. (> _ <)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmD_cantidad == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en Ingreso de Cantidad',
      'Texto' => '(TT__TT) No se ingreso Cantidad. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmD_cantidad <= 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en Ingreso de Cantidad',
      'Texto' => '(TT__TT) La cantidad ingresada no puede ser cero o negativa. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmM_tipo == 3) {
    $AlmD_precio = limpiarCadena($_POST['AlmD_precio']);
    if ($AlmD_precio == "") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if ($AlmD_precio < 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else if (verificarDatos("^\d+\.\d{2}$", $AlmD_precio)) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  } else {
    $AlmD_precio = 0;
  }


  if ($AlmD_comentario == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en Ingreso de Comentario',
      'Texto' => '(TT__TT) No se ingreso ningun comentario (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($AlmD_comentario) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error en Ingreso de Comentario',
      'Texto' => '(TT__TT) Debes agregar mas de 10 caracteres en comentario. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // Terminan las comprobaciones para el toner Proveedor de Almacen //
  $INSERT = "INSERT INTO AlmacenD (AlmDM_id, AlmDP_id, AlmD_cantidad, AlmD_precio, AlmD_comentario) VALUES ('$AlmDM_id', '$AlmDP_id', '$AlmD_cantidad', '$AlmD_precio', '$AlmD_comentario')";
  if (sentenciaData($INSERT)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Movimientos/Detalles/' . encryption($AlmDM_id)
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo Ingresar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// ============================== Entrada de Nuevo Proveedor V2.0 ============================== //
function nuevoRegistro_AlmProv()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmProv_nombre = limpiarCadena(strtoupper($_POST['AlmProv_nombre']));

  if ($AlmProv_nombre == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun codigo de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // Terminan las comprobaciones para el toner Proveedor de Almacen //

  if (sentenciaData("INSERT INTO AlmacenProvs (AlmProv_nombre) VALUES ('$AlmProv_nombre')")) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Proveedores/Lista'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo agregar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}
// ============================== Entrada de Nuevo Proveedor V2.0 ============================== //
function editarRegistro_AlmProv()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmProv_id = limpiarCadena(decryption($_POST['editarRegistro_AlmProv']));
  $AlmProv_estado = limpiarCadena($_POST['AlmProv_estado']);
  $AlmProv_nombre = limpiarCadena(strtoupper($_POST['AlmProv_nombre']));

  $QRY = consultaData("SELECT * FROM AlmacenProvs WHERE AlmProv_id = $AlmProv_id");

  if ($QRY['numRows'] != 1) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Categoria. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $AlmProvDATA = $QRY['dataFetch'][0];
  }


  if ($AlmProv_estado == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Estado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (!in_array($AlmProv_estado, range(0, 1))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se reconoce el estado seleccionado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmProv_nombre == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun codigo de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // Terminan las comprobaciones para el toner Proveedor de Almacen //
  $UPDATE = "UPDATE AlmacenProvs SET
              AlmProv_estado = '$AlmProv_estado',
              AlmProv_nombre = '$AlmProv_nombre'
              WHERE AlmProv_id = '$AlmProv_id'";
  if (sentenciaData($UPDATE)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Proveedores/Lista'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo Actualizar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// ================================== REGISTROS DE TONERS V2.0 ================================= //
function nuevoRegistro_AlmP_Toners()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $tonerCodigo1 = limpiarCadena($_POST['tonerCodigo1']);
  $tonerCodigo2 = limpiarCadena($_POST['tonerCodigo2']);
  $AlmP_subcat_id = limpiarCadena($_POST['colorToner']);
  $noParteToner = limpiarCadena($_POST['noParteToner']);
  $stockMin = limpiarCadena($_POST['stockMin']);
  $rendimientoToner = limpiarCadena($_POST['rendimientoToner']);
  $compatibilidadToner = limpiarCadena($_POST['compatibilidadToner']);
  $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  $AlmP_unidadM = limpiarCadena(decryption($_POST['AlmP_unidadM']));
  $AlmP_prov_id = limpiarCadena(decryption($_POST['AlmP_prov_id']));


  // -------------------------------------- Obtener el Folio ------------------------------------- //
  $codigoLibre = FALSE;
  while (!$codigoLibre) {
    $AlmP_codigo = random_int(10000, 99999);
    $check_codigo = consultaData("SELECT AlmP_codigo FROM AlmacenP WHERE AlmP_codigo = '$AlmP_codigo'");
    if ($check_codigo['numRows'] == 0) {
      $codigoLibre = TRUE;
    }
  }
  // -------------------------- ^^^ FIN ^^^ Obtener el Folio ^^^ FIN ^^^ ------------------------- //


  if ($tonerCodigo1 == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar Marca de toner',
      'Texto' => '(TT__TT) No se ingreso Marca de Toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($tonerCodigo1 != "TKR" && $tonerCodigo1 != "TK" && $tonerCodigo1 != "ESR"  && $tonerCodigo1 != "ES") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar Marca de toner',
      'Texto' => '(TT__TT) La marca de toner no es reconocida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($tonerCodigo2 == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun codigo de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($tonerCodigo2 < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo no puede ser un numero negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{3,5})$", $tonerCodigo2)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo deben ser solo numeros, de 4 a 5 digitos (ej. 1175) (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmP_subcat_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna opcion de color de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_subcat_id == 0) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2;
  } else if ($AlmP_subcat_id == 1) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "K";
  } else if ($AlmP_subcat_id == 2) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "M";
  } else if ($AlmP_subcat_id == 3) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "C";
  } else if ($AlmP_subcat_id == 4) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "Y";
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Color de toner seleccionado, no valido. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($noParteToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el numero de parte debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($noParteToner < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de numero de parte, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d+", $noParteToner)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de numero de parte, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($rendimientoToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el rendimiento debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($rendimientoToner < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del rendimiento, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d+", $rendimientoToner)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del rendimiento, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($stockMin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El campo de stock minimo no puede estar varcio. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($stockMin < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, no puede ser menos de 3. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+", $stockMin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($compatibilidadToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna compatibilidad (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($compatibilidadToner) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas descripcion de la compatibilidad. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($compatibilidadToner) > 60) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) La descripcion no debe exceder los 60 caracteres. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $AlmP_descripcion = $tonerCodigo . " | " . $noParteToner . " | " . $rendimientoToner . " | " . $compatibilidadToner;
  }


  if ($AlmP_prov_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun proveedor. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_prov_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El proveedor ingresado no tiene el formato solicitado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRY_checkCodigo = consultaData("SELECT * FROM AlmacenP WHERE AlmP_cat_id = '1'");
    foreach ($QRY_checkCodigo['dataFetch'] as $prodRow) {
      $AlmP_descr = explode(" | ", $prodRow['AlmP_descripcion']);
      if ($AlmP_descr[0] == $tonerCodigo && $AlmP_prov_id == $prodRow['AlmP_prov_id']) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ El codigo de toner (' . $tonerCodigo . ') que estas agregando ya se encuentra registrado con el mismo proveedor. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }

  if ($AlmP_precio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_precio < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmP_unidadM == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna unidad de medida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_unidadM)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de unidad de medida, no es el correcto. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRYuL = consultaData("SELECT * FROM unidadesList WHERE unList_id ='$AlmP_unidadM'");
    if ($QRYuL['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) La unidad de medida seleccionada, no es valida. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  // Terminan las comprobaciones para el registro del Producto Toner //
  $sqlInsert = "INSERT INTO AlmacenP
                  (AlmP_codigo, AlmP_stock_min, AlmP_descripcion, AlmP_precio, AlmP_unidadM, AlmP_cat_id, AlmP_subcat_id, AlmP_prov_id)
                VALUES
                  ('$AlmP_codigo', '$stockMin', '$AlmP_descripcion', '$AlmP_precio', '$AlmP_unidadM', '1', '$AlmP_subcat_id', '$AlmP_prov_id')";
  if (sentenciaData($sqlInsert)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Toners/Lista'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo agregar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}
function editarRegistro_AlmP_Toners()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmP_id = limpiarCadena(decryption($_POST['editarRegistro_AlmP_Toners']));
  $AlmP_estado = limpiarCadena($_POST['AlmP_estado']);
  $tonerCodigo1 = limpiarCadena($_POST['tonerCodigo1']);
  $tonerCodigo2 = limpiarCadena($_POST['tonerCodigo2']);
  $AlmP_subcat_id = limpiarCadena($_POST['colorToner']);
  $noParteToner = limpiarCadena($_POST['noParteToner']);
  $rendimientoToner = limpiarCadena($_POST['rendimientoToner']);
  $stockMin = limpiarCadena($_POST['stockMin']);
  $compatibilidadToner = limpiarCadena($_POST['compatibilidadToner']);
  $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  $AlmP_unidadM = limpiarCadena(decryption($_POST['AlmP_unidadM']));
  $AlmP_prov_id = limpiarCadena(decryption($_POST['AlmP_prov_id']));

  $QRY_ID = consultaData("SELECT * FROM AlmacenP WHERE AlmP_id = '$AlmP_id'");
  if ($QRY_ID['numRows'] == 1) {
    $DATA_AlmP = $QRY_ID['dataFetch'][0];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El registro no existe. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmP_estado == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Estado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (!in_array($AlmP_estado, range(0, 1))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se reconoce el estado seleccionado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($tonerCodigo1 == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Marca de Toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($tonerCodigo1 != "TKR" && $tonerCodigo1 != "TK" && $tonerCodigo1 != "ESR"  && $tonerCodigo1 != "ES") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) La marca de toner no es reconocida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($tonerCodigo2 == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun codigo de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($tonerCodigo2 < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo no puede ser un numero negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{3,5})$", $tonerCodigo2)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo deben ser solo numeros, de 4 a 5 digitos (ej. 1175) (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmP_subcat_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna opcion de color de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_subcat_id == 0) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2;
  } else if ($AlmP_subcat_id == 1) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "K";
  } else if ($AlmP_subcat_id == 2) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "M";
  } else if ($AlmP_subcat_id == 3) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "C";
  } else if ($AlmP_subcat_id == 4) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "Y";
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Color de toner seleccionado, no valido. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($noParteToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el numero de parte debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($noParteToner < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de numero de parte, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d+", $noParteToner)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de numero de parte, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($rendimientoToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el rendimiento debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($rendimientoToner < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del rendimiento, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d+", $rendimientoToner)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del rendimiento, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($stockMin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El campo de stock minimo no puede estar varcio. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($stockMin < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, no puede ser menos de 3. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+", $stockMin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($compatibilidadToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna compatibilidad (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($compatibilidadToner) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas descripcion de la compatibilidad. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $AlmP_descripcion = $tonerCodigo . " | " . $noParteToner . " | " . $rendimientoToner . " | " . $compatibilidadToner;
  }


  if ($AlmP_prov_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun proveedor. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_prov_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El proveedor ingresado no tiene el formato solicitado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRY_checkCodigo = consultaData("SELECT * FROM AlmacenP WHERE AlmP_cat_id = '1'");
    foreach ($QRY_checkCodigo['dataFetch'] as $prodRow) {
      $AlmP_descr = explode(" | ", $prodRow['AlmP_descripcion']);
      if ($AlmP_descr[0] == $tonerCodigo && $AlmP_prov_id == $prodRow['AlmP_prov_id'] && $prodRow['AlmP_id'] != $AlmP_id) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ El codigo de toner (' . $tonerCodigo . ') que estas agregando ya se encuentra registrado con el mismo proveedor. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }

  if ($AlmP_precio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_precio < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmP_unidadM == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna unidad de medida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_unidadM)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de unidad de medida, no es el correcto. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRYuL = consultaData("SELECT * FROM unidadesList WHERE unList_id ='$AlmP_unidadM'");
    if ($QRYuL['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) La unidad de medida seleccionada, no es valida. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  // Terminan las comprobaciones para la actualizacion del Producto Toner //
  $sqlUpdate = "UPDATE AlmacenP SET
                  AlmP_stock_min = '$stockMin',
                  AlmP_descripcion = '$AlmP_descripcion',
                  AlmP_precio = '$AlmP_precio',
                  AlmP_unidadM = '$AlmP_unidadM',
                  AlmP_subcat_id = '$AlmP_subcat_id',
                  AlmP_prov_id = '$AlmP_prov_id'
                WHERE AlmP_id = '$AlmP_id'";
  if (sentenciaData($sqlUpdate)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Toners/Lista',
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo actualizar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// ================================== REGISTROS DE CHIPS V2.0 ================================== //
function nuevoRegistro_AlmP_Chips()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $tonerCodigo1 = limpiarCadena($_POST['tonerCodigo1']);
  $tonerCodigo2 = limpiarCadena($_POST['tonerCodigo2']);
  $AlmP_subcat_id = limpiarCadena($_POST['colorToner']);
  $rendimientoToner = limpiarCadena($_POST['rendimientoToner']);
  $stockMin = limpiarCadena($_POST['stockMin']);
  $compatibilidadToner = limpiarCadena($_POST['compatibilidadToner']);
  $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  $AlmP_unidadM = limpiarCadena(decryption($_POST['AlmP_unidadM']));
  $AlmP_prov_id = limpiarCadena(decryption($_POST['AlmP_prov_id']));


  // ----------------------------------------------- Obtener el Folio de Cotizacion ----------------------------------------------- //
  $codigoLibre = FALSE;
  while (!$codigoLibre) {
    $AlmP_codigo = random_int(10000, 99999);
    $check_codigo = consultaData("SELECT AlmP_codigo FROM AlmacenP WHERE AlmP_codigo = '$AlmP_codigo'");
    if ($check_codigo['numRows'] == 0) {
      $codigoLibre = TRUE;
    }
  }
  // ----------------------------------- ^^^ FIN ^^^ Obtener el Folio de Cotizacion ^^^ FIN ^^^ ----------------------------------- //


  if ($tonerCodigo1 == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Marca de Toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($tonerCodigo1 != "TK" && $tonerCodigo1 != "ES") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) La marca de toner no es reconocida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($tonerCodigo2 == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun codigo de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($tonerCodigo2 < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo no puede ser un numero negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{3,5})$", $tonerCodigo2)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo deben ser solo numeros, de 4 a 5 digitos (ej. 1175) (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmP_subcat_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna opcion de color de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_subcat_id == 0) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2;
  } else if ($AlmP_subcat_id == 1) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "K";
  } else if ($AlmP_subcat_id == 2) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "M";
  } else if ($AlmP_subcat_id == 3) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "C";
  } else if ($AlmP_subcat_id == 4) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "Y";
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Color de toner seleccionado, no valido. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($rendimientoToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el rendimiento debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($rendimientoToner < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del rendimiento, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d+", $rendimientoToner)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del rendimiento, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($stockMin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El campo de stock minimo no puede estar varcio. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($stockMin < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, no puede ser menos de 3. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+", $stockMin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($compatibilidadToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna compatibilidad (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($compatibilidadToner) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas descripcion de la compatibilidad. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $AlmP_descripcion = $tonerCodigo . " | " . $rendimientoToner . " | " . $compatibilidadToner;
  }


  if ($AlmP_prov_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun proveedor. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_prov_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El proveedor ingresado no tiene el formato solicitado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRY_checkCodigo = consultaData("SELECT * FROM AlmacenP WHERE AlmP_cat_id = '2'");
    foreach ($QRY_checkCodigo['dataFetch'] as $prodRow) {
      $AlmP_descr = explode(" | ", $prodRow['AlmP_descripcion']);
      if ($AlmP_descr[0] == $tonerCodigo && $prodRow['AlmP_prov_id'] == $AlmP_prov_id) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ El codigo de toner (' . $tonerCodigo . ') que estas agregando ya se encuentra registrado con el mismo proveedor. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }

  if ($AlmP_precio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_precio < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmP_unidadM == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna unidad de medida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_unidadM)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de unidad de medida, no es el correcto. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRYuL = consultaData("SELECT * FROM unidadesList WHERE unList_id ='$AlmP_unidadM'");
    if ($QRYuL['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) La unidad de medida seleccionada, no es valida. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  // Terminan las comprobaciones para el registro del Producto Chip //
  $sqlInsert = "INSERT INTO AlmacenP
                  (AlmP_codigo, AlmP_stock_min, AlmP_codigo, AlmP_descripcion, AlmP_precio, AlmP_unidadM, AlmP_cat_id, AlmP_subcat_id, AlmP_prov_id)
                VALUES
                  ('$AlmP_codigo', '$stockMin', '$AlmP_descripcion', '$AlmP_precio', '$AlmP_unidadM', '2', '$AlmP_subcat_id', '$AlmP_prov_id')";
  if (sentenciaData($sqlInsert)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Chips/Lista'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo agregar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}

function editarRegistro_AlmP_Chips()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmP_id = limpiarCadena(decryption($_POST['editarRegistro_AlmP_Chips']));
  $AlmP_estado = limpiarCadena($_POST['AlmP_estado']);
  $tonerCodigo1 = limpiarCadena($_POST['tonerCodigo1']);
  $tonerCodigo2 = limpiarCadena($_POST['tonerCodigo2']);
  $AlmP_subcat_id = limpiarCadena($_POST['colorToner']);
  $rendimientoToner = limpiarCadena($_POST['rendimientoToner']);
  $stockMin = limpiarCadena($_POST['stockMin']);
  $compatibilidadToner = limpiarCadena($_POST['compatibilidadToner']);
  $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  $AlmP_unidadM = limpiarCadena(decryption($_POST['AlmP_unidadM']));
  $AlmP_prov_id = limpiarCadena(decryption($_POST['AlmP_prov_id']));

  $QRY_ID = consultaData("SELECT * FROM AlmacenP WHERE AlmP_id = '$AlmP_id'");
  if ($QRY_ID['numRows'] == 1) {
    $DATA_AlmP = $QRY_ID['dataFetch'][0];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El registro no existe. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmP_estado == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Estado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (!in_array($AlmP_estado, range(0, 1))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se reconoce el estado seleccionado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($tonerCodigo1 == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Marca de Toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($tonerCodigo1 != "TK" && $tonerCodigo1 != "ES") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) La marca de toner no es reconocida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($tonerCodigo2 == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun codigo de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($tonerCodigo2 < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo no puede ser un numero negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{3,5})$", $tonerCodigo2)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo deben ser solo numeros, de 4 a 5 digitos (ej. 1175) (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmP_subcat_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna opcion de color de toner. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_subcat_id == 0) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2;
  } else if ($AlmP_subcat_id == 1) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "K";
  } else if ($AlmP_subcat_id == 2) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "M";
  } else if ($AlmP_subcat_id == 3) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "C";
  } else if ($AlmP_subcat_id == 4) {
    $tonerCodigo = $tonerCodigo1 . "-" . $tonerCodigo2 . "Y";
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Color de toner seleccionado, no valido. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($rendimientoToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el rendimiento debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($rendimientoToner < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del rendimiento, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("\d+", $rendimientoToner)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del rendimiento, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($stockMin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El campo de stock minimo no puede estar varcio. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($stockMin < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, no puede ser menos de 3. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+", $stockMin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($compatibilidadToner == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna compatibilidad (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($compatibilidadToner) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas descripcion de la compatibilidad. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $AlmP_descripcion = $tonerCodigo . " | " . $rendimientoToner . " | " . $compatibilidadToner;
  }


  if ($AlmP_prov_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun proveedor. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_prov_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El proveedor ingresado no tiene el formato solicitado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRY_checkCodigo = consultaData("SELECT * FROM AlmacenP WHERE AlmP_cat_id = '2'");
    foreach ($QRY_checkCodigo['dataFetch'] as $prodRow) {
      $AlmP_descr = explode(" | ", $prodRow['AlmP_descripcion']);
      if ($AlmP_descr[0] == $tonerCodigo && $prodRow['AlmP_prov_id'] == $AlmP_prov_id && $prodRow['AlmP_id'] != $AlmP_id) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ El codigo de toner (' . $tonerCodigo . ') que estas agregando ya se encuentra registrado con el mismo proveedor. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }

  if ($AlmP_precio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_precio < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($AlmP_unidadM == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna unidad de medida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_unidadM)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de unidad de medida, no es el correcto. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRYuL = consultaData("SELECT * FROM unidadesList WHERE unList_id ='$AlmP_unidadM'");
    if ($QRYuL['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) La unidad de medida seleccionada, no es valida. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  // Terminan las comprobaciones para la actualizacion del Producto Chip //
  $sqlUpdate = "UPDATE AlmacenP SET
                  AlmP_descripcion = '$AlmP_descripcion',
                  AlmP_stock_min = '$stockMin',
                  AlmP_precio = '$AlmP_precio',
                  AlmP_unidadM = '$AlmP_unidadM',
                  AlmP_subcat_id = '$AlmP_subcat_id',
                  AlmP_prov_id = '$AlmP_prov_id'
                WHERE AlmP_id = '$AlmP_id'";
  if (sentenciaData($sqlUpdate)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Chips/Lista',
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo actualizar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// =============================== REGISTROS DE REFACCIONES V2.0 =============================== //
function nuevoRegistro_AlmP_Refacciones()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmP_subcat_id = limpiarCadena(decryption($_POST['AlmP_subcat_id']));
  $AlmP_prov_id = limpiarCadena(decryption($_POST['AlmP_prov_id']));
  $AlmP_unidadM = limpiarCadena(decryption($_POST['AlmP_unidadM']));
  $refaccionCodigo = limpiarCadena($_POST['refaccionCodigo']);
  $stockMin = limpiarCadena($_POST['stockMin']);
  $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  $refaccionDescripcion = limpiarCadena($_POST['refaccionDescripcion']);


  // ----------------------------------------------- Obtener el Folio de Cotizacion ----------------------------------------------- //
  $codigoLibre = FALSE;
  while (!$codigoLibre) {
    $AlmP_codigo = random_int(10000, 99999);
    $check_codigo = consultaData("SELECT AlmP_codigo FROM AlmacenP WHERE AlmP_codigo = '$AlmP_codigo'");
    if ($check_codigo['numRows'] == 0) {
      $codigoLibre = TRUE;
    }
  }
  // ----------------------------------- ^^^ FIN ^^^ Obtener el Folio de Cotizacion ^^^ FIN ^^^ ----------------------------------- //


  $catsRef = consultaData("SELECT * FROM CategoriasR WHERE catR_id = '$AlmP_subcat_id'");
  if ($AlmP_subcat_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso una categoria. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($catsRef['numRows'] != 1) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) La categoria de refaccion no es reconocida o no existe. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $catsRef = $catsRef['dataFetch'][0];
  }


  if ($refaccionCodigo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun codigo de refaccion. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($refaccionCodigo < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) La cantidad del codigo no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{3,5})$", $refaccionCodigo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo deben ser solo numeros, de 4 a 5 digitos (ej. 2100) (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $refaccionCodigo = $catsRef['catR_codigo'] . "-" . $refaccionCodigo;
  }

  if ($stockMin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El campo de stock minimo no puede estar varcio. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($stockMin < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, no puede ser menos de 3. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+", $stockMin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($refaccionDescripcion == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna compatibilidad (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($refaccionDescripcion) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas descripcion de la compatibilidad. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $AlmP_descripcion = $refaccionCodigo . " | " . $refaccionDescripcion;
  }


  if ($AlmP_prov_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun proveedor. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_prov_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El proveedor ingresado no tiene el formato solicitado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRY_checkCodigo = consultaData("SELECT * FROM AlmacenP WHERE AlmP_cat_id = '3'");
    foreach ($QRY_checkCodigo['dataFetch'] as $prodRow) {
      $AlmP_descr = explode(" | ", $prodRow['AlmP_descripcion']);
      if ($AlmP_descr[0] == $refaccionCodigo && $prodRow['AlmP_prov_id'] == $AlmP_prov_id) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ El codigo de Refaccion (' . $refaccionCodigo . ') que estas agregando ya se encuentra registrado con este proveedor. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }


  if ($AlmP_precio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_precio < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmP_unidadM == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna unidad de medida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_unidadM)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de unidad de medida, no es el correcto. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRYuL = consultaData("SELECT * FROM unidadesList WHERE unList_id ='$AlmP_unidadM'");
    if ($QRYuL['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) La unidad de medida seleccionada, no es valida. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  // Terminan las comprobaciones para la nueva Refaccion //
  $sqlInsert = "INSERT INTO AlmacenP
                  (AlmP_codigo, AlmP_stock_min, AlmP_descripcion, AlmP_precio, AlmP_unidadM, AlmP_cat_id, AlmP_subcat_id, AlmP_prov_id)
                VALUES
                  ('$AlmP_codigo', '$stockMin', '$AlmP_descripcion', '$AlmP_precio', '$AlmP_unidadM', '3', '$AlmP_subcat_id', '$AlmP_prov_id')";
  if (sentenciaData($sqlInsert)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Refacciones/Lista'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo agregar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}
function editarRegistro_AlmP_Refacciones()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmP_id = limpiarCadena(decryption($_POST['editarRegistro_AlmP_Refacciones']));
  $AlmP_estado = limpiarCadena($_POST['AlmP_estado']);
  $AlmP_subcat_id = limpiarCadena(decryption($_POST['AlmP_subcat_id']));
  $AlmP_unidadM = limpiarCadena(decryption($_POST['AlmP_unidadM']));
  $AlmP_prov_id = limpiarCadena(decryption($_POST['AlmP_prov_id']));
  $refaccionCodigo = limpiarCadena($_POST['refaccionCodigo']);
  $stockMin = limpiarCadena($_POST['stockMin']);
  $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  $refaccionDescripcion = limpiarCadena($_POST['refaccionDescripcion']);

  $QRY_ID = consultaData("SELECT * FROM AlmacenP WHERE AlmP_id = '$AlmP_id'");
  if ($QRY_ID['numRows'] == 1) {
    $DATA_AlmP = $QRY_ID['dataFetch'][0];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El registro no existe. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmP_estado == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Estado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (!in_array($AlmP_estado, range(0, 1))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se reconoce el estado seleccionado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  $catsRef = consultaData("SELECT * FROM CategoriasR WHERE catR_id = '$AlmP_subcat_id'");
  if ($AlmP_subcat_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso una categoria. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($catsRef['numRows'] != 1) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) La categoria de refaccion no es reconocida o no existe. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $catsRef = $catsRef['dataFetch'][0];
  }


  if ($refaccionCodigo == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun codigo de refaccion. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($refaccionCodigo < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo no puede ser un numero negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{3,5})$", $refaccionCodigo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El codigo deben ser solo numeros, de 4 a 5 digitos (ej. 2100) (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $refaccionCodigo = $catsRef['catR_codigo'] . "-" . $refaccionCodigo;
  }

  if ($stockMin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El campo de stock minimo no puede estar varcio. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($stockMin < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, no puede ser menos de 3. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+", $stockMin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del stock minimo, debe ser numerico y sin espacios. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($refaccionDescripcion == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna descripcion (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($refaccionDescripcion) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas descripcion. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $AlmP_descripcion = $refaccionCodigo . " | " . $refaccionDescripcion;
  }


  if ($AlmP_prov_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ningun proveedor. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_prov_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El proveedor ingresado no tiene el formato solicitado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRY_checkCodigo = consultaData("SELECT * FROM AlmacenP WHERE AlmP_cat_id = '3'");
    foreach ($QRY_checkCodigo['dataFetch'] as $prodRow) {
      $AlmP_descr = explode(" | ", $prodRow['AlmP_descripcion']);
      if ($AlmP_descr[0] == $refaccionCodigo && $prodRow['AlmP_prov_id'] == $AlmP_prov_id && $prodRow['AlmP_id'] != $AlmP_id) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '\(- _ -)/ El codigo de Refaccion (' . $refaccionCodigo . ') que estas agregando ya se encuentra registrado con este proveedor. \(- _ -)/',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }


  if ($AlmP_precio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_precio < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmP_unidadM == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna unidad de medida. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,15}", $AlmP_unidadM)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato de unidad de medida, no es el correcto. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $QRYuL = consultaData("SELECT * FROM unidadesList WHERE unList_id ='$AlmP_unidadM'");
    if ($QRYuL['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT__TT) La unidad de medida seleccionada, no es valida. (TT__TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }


  // Terminan las comprobaciones para el toner Nuevo //
  $UPDATE = "UPDATE AlmacenP SET
            AlmP_estado = '$AlmP_estado',
            AlmP_stock_min = '$stockMin',
            AlmP_descripcion = '$AlmP_descripcion',
            AlmP_precio = '$AlmP_precio',
            AlmP_unidadM = '$AlmP_unidadM',
            AlmP_subcat_id = '$AlmP_subcat_id',
            AlmP_prov_id = '$AlmP_prov_id'
            WHERE AlmP_id = '$AlmP_id'";
  if (sentenciaData($UPDATE)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Refacciones/Lista',
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo actualizar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// =============================== REGISTROS DE SERVICIOS V2.0 =============================== //
function nuevoRegistro_AlmP_Servicios()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  $AlmP_descripcion = limpiarCadena($_POST['AlmP_descripcion']);

  // ----------------------------------------------- Obtener el Folio de Cotizacion ----------------------------------------------- //
  $codigoLibre = FALSE;
  while (!$codigoLibre) {
    $AlmP_codigo = random_int(10000, 99999);
    $check_codigo = consultaData("SELECT AlmP_codigo FROM AlmacenP WHERE AlmP_codigo = '$AlmP_codigo'");
    if ($check_codigo['numRows'] == 0) {
      $codigoLibre = TRUE;
    }
  }
  // ----------------------------------- ^^^ FIN ^^^ Obtener el Folio de Cotizacion ^^^ FIN ^^^ ----------------------------------- //

  if ($AlmP_descripcion == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso ninguna compatibilidad (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (strlen($AlmP_descripcion) < 10) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Debes agregar mas descripcion de la compatibilidad. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmP_precio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_precio < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // Terminan las comprobaciones para la nueva Refaccion //

  if (sentenciaData("INSERT INTO AlmacenP (AlmP_codigo, AlmP_descripcion, AlmP_precio, AlmP_unidadM, AlmP_cat_id, AlmP_subcat_id, AlmP_prov_id) VALUES ('$AlmP_codigo', '$AlmP_descripcion', '$AlmP_precio', '2', '4', '1', '10')")) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Servicios/Lista'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo agregar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}
function editarRegistro_AlmP_Servicios()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  $AlmP_id = limpiarCadena(decryption($_POST['editarRegistro_AlmP_Servicios']));
  $AlmP_estado = limpiarCadena($_POST['AlmP_estado']);
  $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  $AlmP_descripcion = limpiarCadena($_POST['AlmP_descripcion']);

  $QRY_ID = consultaData("SELECT * FROM AlmacenP WHERE AlmP_id = '$AlmP_id'");
  if ($QRY_ID['numRows'] == 1) {
    $DATA_AlmP = $QRY_ID['dataFetch'][0];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El registro no existe. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmP_estado == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se ingreso Estado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (!in_array($AlmP_estado, range(0, 1))) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se reconoce el estado seleccionado. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($AlmP_precio == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($AlmP_precio < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // Terminan las comprobaciones para el toner Nuevo //
  $UPDATE = "UPDATE AlmacenP SET
            AlmP_estado = '$AlmP_estado',
            AlmP_descripcion = '$AlmP_descripcion',
            AlmP_precio = '$AlmP_precio'
            WHERE AlmP_id = '$AlmP_id'";
  if (sentenciaData($UPDATE)) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . 'Almacen/Servicios/Lista',
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT__TT) No se pudo actualizar el registro. (TT__TT)',
      'Tipo' => 'error'
    ];
  }

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// =============================== REGISTROS DE SERVICIOS V2.0 =============================== //
function nuevoRegistro_AlmP_Otros()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  // $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  // $AlmP_descripcion = limpiarCadena($_POST['AlmP_descripcion']);

  // // ----------------------------------------------- Obtener el Folio de Cotizacion ----------------------------------------------- //
  // $codigoLibre = FALSE;
  // while (!$codigoLibre) {
  //   $AlmP_codigo = random_int(10000, 99999);
  //   $check_codigo = consultaData("SELECT AlmP_codigo FROM AlmacenP WHERE AlmP_codigo = '$AlmP_codigo'");
  //   if ($check_codigo['numRows'] == 0) {
  //     $codigoLibre = TRUE;
  //   }
  // }
  // // ----------------------------------- ^^^ FIN ^^^ Obtener el Folio de Cotizacion ^^^ FIN ^^^ ----------------------------------- //

  // if ($AlmP_descripcion == "") {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) No se ingreso ninguna compatibilidad (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // } else if (strlen($AlmP_descripcion) < 10) {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) Debes agregar mas descripcion de la compatibilidad. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // }


  // if ($AlmP_precio == "") {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // } else if ($AlmP_precio < 0) {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // }


  // // Terminan las comprobaciones para la nueva Refaccion //

  // if (sentenciaData("INSERT INTO AlmacenP (AlmP_codigo, AlmP_descripcion, AlmP_precio, AlmP_unidadM, AlmP_cat_id, AlmP_subcat_id, AlmP_prov_id) VALUES ('$AlmP_codigo', '$AlmP_descripcion', '$AlmP_precio', '2', '4', '1', '10')")) {
  //   $alerta = [
  //     'Alerta' => 'redireccionar',
  //     'url' => SERVERURL . 'Almacen/Servicios/Lista'
  //   ];
  // } else {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) No se pudo agregar el registro. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  // }

  $alerta = [
    'Alerta' => 'simple',
    'Titulo' => 'Espera',
    'Texto' => '(TT__TT) Trabajando en esta seccion. (TT__TT)',
    'Tipo' => 'info'
  ];

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}
function editarRegistro_AlmP_Otros()
{
  // ------------------- Comprobacion de Formulario ------------------- //

  // $AlmP_id = limpiarCadena(decryption($_POST['editarRegistro_AlmP_Servicios']));
  // $AlmP_estado = limpiarCadena($_POST['AlmP_estado']);
  // $AlmP_precio = limpiarCadena($_POST['AlmP_precio']);
  // $AlmP_descripcion = limpiarCadena($_POST['AlmP_descripcion']);

  // $QRY_ID = consultaData("SELECT * FROM AlmacenP WHERE AlmP_id = '$AlmP_id'");
  // if ($QRY_ID['numRows'] == 1) {
  //   $DATA_AlmP = $QRY_ID['dataFetch'][0];
  // } else {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) El registro no existe. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // }


  // if ($AlmP_estado == "") {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) No se ingreso Estado. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // } else if (!in_array($AlmP_estado, range(0, 1))) {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) No se reconoce el estado seleccionado. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // }


  // if ($AlmP_precio == "") {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) Como minimo el precio debe estar en 0. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // } else if ($AlmP_precio < 0) {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) El formato del precio, no puede ser negativo. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // } else if (verificarDatos("^\d+\.\d{2}$", $AlmP_precio)) {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) El formato del precio, debe ser numerico y con dos decimales. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  //   echo json_encode($alerta);
  //   exit();
  // }


  // // Terminan las comprobaciones para el toner Nuevo //
  // $UPDATE = "UPDATE AlmacenP SET
  //           AlmP_estado = '$AlmP_estado',
  //           AlmP_descripcion = '$AlmP_descripcion',
  //           AlmP_precio = '$AlmP_precio'
  //           WHERE AlmP_id = '$AlmP_id'";
  // if (sentenciaData($UPDATE)) {
  //   $alerta = [
  //     'Alerta' => 'recargar',
  //     'Titulo' => 'Registro Completado',
  //     'Texto' => '\(- _ -)/ El servicio No. ' . $DATA_AlmP['AlmP_codigo'] . ', fue actualizado correctamente. \(- _ -)/',
  //     'Tipo' => 'success'
  //   ];
  // } else {
  //   $alerta = [
  //     'Alerta' => 'simple',
  //     'Titulo' => 'Ocurrio un Error inesperado',
  //     'Texto' => '(TT__TT) No se pudo actualizar el registro. (TT__TT)',
  //     'Tipo' => 'error'
  //   ];
  // }


  $alerta = [
    'Alerta' => 'simple',
    'Titulo' => 'Espera',
    'Texto' => '(TT__TT) Trabajando en esta seccion. (TT__TT)',
    'Tipo' => 'info'
  ];

  // Lanzamos La respuesta Final //
  echo json_encode($alerta);
  exit();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
