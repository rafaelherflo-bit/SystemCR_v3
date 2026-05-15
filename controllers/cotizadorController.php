<?php

// ============================ Controladores de Cotizador ============================ //

function nuevaCotizacion()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

  $cotM_estatus = limpiarCadena($_POST['nuevaCotizacion']);
  $cotM_cliRS = limpiarCadena($_POST['cotM_cliRS']);
  $cotM_cliRFC = limpiarCadena($_POST['cotM_cliRFC']);
  $cotM_IVA = limpiarCadena($_POST['cotM_IVA']);
  $cotM_comm = limpiarCadena($_POST['cotM_comm']);

  if ($cotM_estatus != "1") {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Error Critico',
      'Texto' => '(TT _ TT) Recargando la pagina, si el error persiste consulta al administrador. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  if ($cotM_cliRS == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesaio agregar una Razon Social. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($cotM_cliRFC == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesaio agregar un RFC. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($cotM_IVA == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El IVA es necesario. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^(\d|[1-9]\d|100)$", $cotM_IVA)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El formato del IVA es incorrecto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // ----------------------------------------------- Obtener el Folio de Cotizacion ----------------------------------------------- //
  for ($cotM_folio = "COT-40001";; $cotM_folio++) {
    $check_folio = consultaData("SELECT cotM_folio FROM cotizadorM WHERE cotM_folio = '$cotM_folio'");
    if ($check_folio['numRows'] == 0) {
      break;
    }
  }
  // ----------------------------------- ^^^ FIN ^^^ Obtener el Folio de Cotizacion ^^^ FIN ^^^ ----------------------------------- //


  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
  $insertID = insertID("INSERT INTO cotizadorM (cotM_estatus, cotM_folio, cotM_IVA, cotM_cliRS, cotM_cliRFC, cotM_comm) VALUES ('$cotM_estatus', '$cotM_folio', '$cotM_IVA', '$cotM_cliRS', '$cotM_cliRFC', '$cotM_comm')");
  if ($insertID['status']) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . "Cotizador/idD/" . encryption($insertID['ID'])
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

  echo json_encode($alerta);
  exit();
}

function editarCotizacion()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

  $cotM_id = limpiarCadena(decryption($_POST['editarCotizacion']));
  $cotM_cliRS = limpiarCadena($_POST['cotM_cliRS']);
  $cotM_cliRFC = limpiarCadena($_POST['cotM_cliRFC']);
  $cotM_IVA = limpiarCadena($_POST['cotM_IVA']);
  $cotM_comm = limpiarCadena($_POST['cotM_comm']);

  if ($cotM_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'ERROR',
      'Texto' => '(TT _ TT) Recarga la pagina. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,150}", $cotM_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'ERROR',
      'Texto' => '(> _ <) Formato incorrecto. (> _ <)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $checkID = consultaData("SELECT * FROM cotizadorM WHERE cotM_id = '$cotM_id'");
    if ($checkID['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(o _ O) La cotizacion NO existe en la base de datos. (o _ O)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $Data = $checkID['dataFetch'][0];
      if ($Data['cotM_estatus'] != 1) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(¬ _ ¬) La cotizacion ya esta vencida. (¬ _ ¬)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }


  if ($cotM_cliRS == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesaio agregar una Razon Social. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($cotM_cliRFC == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) Es necesaio agregar un RFC. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($cotM_IVA == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El IVA es necesario. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^(\d|[1-9]\d|100)$", $cotM_IVA)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El formato del IVA es incorrecto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
  if (sentenciaData("UPDATE cotizadorM SET cotM_IVA = '$cotM_IVA', cotM_cliRS = '$cotM_cliRS', cotM_cliRFC = '$cotM_cliRFC', cotM_comm = '$cotM_comm' WHERE cotM_id = '$cotM_id'")) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . "Cotizador/idD/" . encryption($cotM_id)
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT_TT) No se pudo actualizar el registro. (TT_TT)',
      'Tipo' => 'error'
    ];
  }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

  echo json_encode($alerta);
  exit();
}

function agregarProdCotD()
{
  $cotD_cotM_id = limpiarCadena(decryption($_POST['agregarProdCotD']));
  $cotD_prod_id = limpiarCadena(decryption($_POST['cotD_prod_id']));
  $cotD_cantidad = limpiarCadena($_POST['cotD_cantidad']);
  $cotD_monto = limpiarCadena($_POST['cotD_monto']);
  $cotD_descuento = limpiarCadena($_POST['cotD_descuento']);

  if ($cotD_cotM_id == "") {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Error Critico',
      'Texto' => '(TT _ TT) Recargando la pagina, el ID de registro cotM NO ingresado en el formulario. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,150}", $cotD_cotM_id)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Error Critico',
      'Texto' => '(> _ <) El registro CotM NO cuenta con el Formato Solicitado. (> _ <)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    $checkID = consultaData("SELECT * FROM cotizadorM WHERE cotM_id = '$cotD_cotM_id'");
    if ($checkID['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'recargar',
        'Titulo' => 'Error Critico',
        'Texto' => '(o _ O) Recargando la pagina, el registro cotM seleccionado NO existe en la base de datos. (o _ O)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }

  if ($cotD_prod_id == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(> _ <) Debes ingresar un producto. (> _ <)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("[0-9]{1,150}", $cotD_prod_id)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(> _ <) El POST de ID de producto NO cuenta con el Formato Solicitado. (> _ <)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    // Verificacion de existencia de producto en base de datos //
    $checkID = consultaData("SELECT * FROM AlmacenP WHERE AlmP_id = '$cotD_prod_id'");
    if ($checkID['numRows'] == 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(> _ <) El producto seleccionado no existe en la base de datos. (> _ <)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      // Comprobacion de duplicidad de producto en la misma cotizacion //
      $checkDuplexProduc = consultaData("SELECT * FROM cotizadorD WHERE cotD_cotM_id = '$cotD_cotM_id' AND cotD_prod_id = '$cotD_prod_id'");
      if ($checkDuplexProduc['numRows'] >= 1) {
        $alerta = [
          'Alerta' => 'simple',
          'Titulo' => 'Ocurrio un Error inesperado',
          'Texto' => '(> _ <) No puedes agregar dos veces el mismo producto. (> _ <)',
          'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
      }
    }
  }

  if ($cotD_cantidad == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la cantidad',
      'Texto' => '(TT _ TT) Debes agregar una cantidad. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[0-9]+$", $cotD_cantidad)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la cantidad',
      'Texto' => '(TT _ TT) El formato debe ser Entero en cantidad (int). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($cotD_cantidad < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar la cantidad',
      'Texto' => '(TT _ TT) El formato no puede ser negativo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($cotD_descuento == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar el descuento',
      'Texto' => '(TT _ TT) Debes agregar una cantidad de descuento o en su defecto 0. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $cotD_descuento)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar el descuento',
      'Texto' => '(TT _ TT) El formato no es el correcto (0.00). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($cotD_descuento < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar el descuento',
      'Texto' => '(TT _ TT) El formato no puede ser negativo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($cotD_monto == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar el importe',
      'Texto' => '(TT _ TT) Debes agregar un monto. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^\d+\.\d{2}$", $cotD_monto)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar el importe',
      'Texto' => '(TT _ TT) El formato no es el correcto (0.00). (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if ($cotD_monto < 0) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Error al ingresar el importe',
      'Texto' => '(TT _ TT) El formato no puede ser negativo. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }


  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
  if (sentenciaData("INSERT INTO cotizadorD (cotD_cotM_id, cotD_prod_id, cotD_cantidad, cotD_monto, cotD_descuento) VALUES ('$cotD_cotM_id', '$cotD_prod_id', '$cotD_cantidad', '$cotD_monto', '$cotD_descuento')")) {
    $alerta = [
      'Alerta' => 'redireccionar',
      'url' => SERVERURL . "Cotizador/idD/" . $_POST['agregarProdCotD']
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
  echo json_encode($alerta);
  exit();
}


// function nuevoProducto()
// {
//   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

//   $prod_estado = limpiarCadena($_POST['nuevoProducto']);
//   $prod_desc = limpiarCadena($_POST['prod_desc']);
//   $prod_precio = limpiarCadena($_POST['prod_precio']);
//   $prod_unList_id = limpiarCadena(decryption($_POST['prod_unList_id']));

//   if ($prod_estado != "1") {
//     $alerta = [
//       'Alerta' => 'recargar',
//       'Titulo' => 'Error Critico',
//       'Texto' => '(TT _ TT) Recargando la pagina, si el error persiste consulta al administrador. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($prod_desc == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Tienes que agregar una descripcion de producto. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (mb_strlen($prod_desc, 'UTF-8') <= 9) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Debes ser mas especifico, la descripcion debe contener almenos 10 caracteres. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $prod_desc = strtoupper($prod_desc);
//   }

//   if ($prod_precio == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Error al ingresar el precio',
//       'Texto' => '(TT _ TT) Debes agregar un precio. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^\d+\.\d{2}$", $prod_precio)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Error al ingresar el precio',
//       'Texto' => '(TT _ TT) El formato no es el correcto (0.00). (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if ($prod_precio < 0) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Error al ingresar el precio',
//       'Texto' => '(TT _ TT) El formato no puede ser negativo. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($prod_unList_id == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Debes agregar un precio. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^[0-9]+$", $prod_unList_id)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El formato debe ser Entero (int). (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if ($prod_unList_id != "1" && $prod_unList_id != "2" && $prod_unList_id != "3" && $prod_unList_id != "4" && $prod_unList_id != "5") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No es una unidad valida. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   // ----------------------------------------------- Obtener el Folio de Cotizacion ----------------------------------------------- //
//   $codigoLibre = FALSE;
//   while (!$codigoLibre) {
//     $prod_codigo = random_int(10000, 99999);
//     $check_folio = consultaData("SELECT prod_codigo FROM Productos WHERE prod_codigo = '$prod_codigo'");
//     if ($check_folio['numRows'] == 0) {
//       $codigoLibre = TRUE;
//     }
//   }
//   // ----------------------------------- ^^^ FIN ^^^ Obtener el Folio de Cotizacion ^^^ FIN ^^^ ----------------------------------- //


//   // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
//   if (sentenciaData("INSERT INTO Productos (prod_estado, prod_codigo, prod_desc, prod_precio, prod_unList_id) VALUES ('$prod_estado', '$prod_codigo', '$prod_desc', '$prod_precio', '$prod_unList_id')")) {
//     $alerta = [
//       'Alerta' => 'recargar',
//       'Titulo' => 'Registro Completado',
//       'Texto' => '\(- _ -)/ Registro agregado correctamente. \(- _ -)/',
//       'Tipo' => 'success'
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

// function actualizarProducto()
// {
//   // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

//   $prod_id = limpiarCadena(decryption($_POST['actualizarProducto']));
//   $prod_desc = limpiarCadena($_POST['prod_desc']);
//   $prod_estado = limpiarCadena($_POST['prod_estado']);
//   $prod_precio = limpiarCadena($_POST['prod_precio']);
//   $prod_unList_id = limpiarCadena(decryption($_POST['prod_unList_id']));


//   if ($prod_id == "") {
//     $alerta = [
//       'Alerta' => 'recargar',
//       'Titulo' => 'Error Critico',
//       'Texto' => '(TT _ TT) Recargando la pagina, el ID de registro no ingresado en el formulario. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("[0-9]{1,150}", $prod_id)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(> _ <) El Cliente NO cuenta con el Formato Solicitado. (> _ <)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $checkID = consultaData("SELECT * FROM Productos WHERE prod_id = '$prod_id'");
//     if ($checkID['numRows'] == 0) {
//       $alerta = [
//         'Alerta' => 'recargar',
//         'Titulo' => 'Error Critico',
//         'Texto' => '(o _ O) Recargando la pagina, el Producto seleccionado NO existe en la base de datos. (o _ O)',
//         'Tipo' => 'error'
//       ];
//       echo json_encode($alerta);
//       exit();
//     }
//   }

//   if ($prod_estado != "0" && $prod_estado != "1") {
//     $alerta = [
//       'Alerta' => 'recargar',
//       'Titulo' => 'Error Critico',
//       'Texto' => '(TT _ TT) Recargando la pagina, el estatus no es el correcto. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($prod_desc == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Tienes que agregar una descripcion de producto. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (mb_strlen($prod_desc, 'UTF-8') <= 9) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Debes ser mas especifico, la descripcion debe contener almenos 10 caracteres. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else {
//     $prod_desc = strtoupper($prod_desc);
//   }

//   if ($prod_precio == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Debes agregar un precio. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^\d+\.\d{2}$", $prod_precio)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Error al ingresar el precio',
//       'Texto' => '(TT _ TT) El formato no es el correcto (0.00). (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if ($prod_precio < 0) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Error al ingresar el precio',
//       'Texto' => '(TT _ TT) El formato no puede ser negativo. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }

//   if ($prod_unList_id == "") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) Debes agregar un precio. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if (verificarDatos("^[0-9]+$", $prod_unList_id)) {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) El formato debe ser Entero (int). (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   } else if ($prod_unList_id != "1" && $prod_unList_id != "2" && $prod_unList_id != "3" && $prod_unList_id != "4" && $prod_unList_id != "5") {
//     $alerta = [
//       'Alerta' => 'simple',
//       'Titulo' => 'Ocurrio un Error inesperado',
//       'Texto' => '(TT _ TT) No es una unidad valida. (TT _ TT)',
//       'Tipo' => 'error'
//     ];
//     echo json_encode($alerta);
//     exit();
//   }


//   // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
//   if (sentenciaData("UPDATE Productos SET prod_estado = '$prod_estado', prod_desc = '$prod_desc', prod_unList_id = '$prod_unList_id', prod_precio = '$prod_precio' WHERE prod_id = '$prod_id'")) {
//     $alerta = [
//       'Alerta' => 'recargar',
//       'Titulo' => 'Registro Completado',
//       'Texto' => '\(- _ -)/ Registro actualizado correctamente. \(- _ -)/',
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
