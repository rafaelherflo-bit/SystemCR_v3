<?php

// ============================ Controladores de Lecturas ============================ //


/* --------------------- Funcion Guardar Archivos PDF --------------------- */
function saveLectura($temp_lectura_estado, $temp_lectura_formato, $nameDoc, $fecha_anio, $fecha_mes, $fecha_dia)
{

  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
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
  // +======+ Verificar Carpeta Mes +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
    $PEDir = $docDir . 'PE/';
    $formatoDir = $docDir . 'Formatos/';
  } else {
    $PEDir = $docDir . 'PE/';
    $formatoDir = $docDir . 'Formatos/';
  }
  // +======+ Verificar Carpeta de Formatos +======+ //
  if (!file_exists($formatoDir)) {
    mkdir($formatoDir, 0755, true);
  }
  // +======+ Verificar Carpeta de PE +======+ //
  if (!file_exists($PEDir)) {
    mkdir($PEDir, 0755, true);
  }

  $archivo = $fecha_dia . '-' . $fecha_mes . '-' . $fecha_anio . ' - ' . $nameDoc . ' - Toma de Lectura.jpg';

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  if (file_exists($formatoDir . $archivo)) {
    $result = [
      'status' => false,
      'result' => 'El formato de lectura, ya existe'
    ];
  }
  if (file_exists($PEDir . $archivo)) {
    $result = [
      'status' => false,
      'result' => 'La pagina de estado, ya existe'
    ];
  }
  if (move_uploaded_file($temp_lectura_formato, $formatoDir . $archivo)) {
    if (move_uploaded_file($temp_lectura_estado, $PEDir . $archivo)) {
      $result = [
        'status' => true,
        'result' => $archivo
      ];
    } else {
      $result = [
        'status' => false,
        'result' => 'No se pudo guardar la pagina de estado.'
      ];
    }
  } else {
    $result = [
      'status' => false,
      'result' => 'No se pudo guardar el formato de lectura.'
    ];
  }
  return $result;
} // Fin del la Funcion


function agregarLectura()
{
  // 1. Datos Básicos y Validación de Fecha
  $lectura_fecha = limpiarCadena($_POST['lectura_fecha']);
  $lectura_renta_id = limpiarCadena(decryption($_POST['lectura_renta_id']));
  $comments = limpiarCadena($_POST['comments']);

  if ($lectura_fecha == "") responderError("No se ingresó fecha.");
  if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $lectura_fecha)) responderError("Formato de fecha inválido.");

  list($lectura_anio, $lectura_mes, $lectura_dia) = explode("-", $lectura_fecha);

  // 2. Verificar Renta y Duplicados
  $SQL_renta = "SELECT R.*, M.modelo_tipo, C.cliente_rs, CO.contrato_folio 
                  FROM Rentas R
                  INNER JOIN Equipos E ON R.renta_equipo_id = E.equipo_id
                  INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
                  INNER JOIN Contratos CO ON R.renta_contrato_id = CO.contrato_id
                  INNER JOIN Clientes C ON CO.contrato_cliente_id = C.cliente_id
                  WHERE R.renta_id = '$lectura_renta_id' AND R.renta_estado = 'Activo'";

  $checkRenta = consultaData($SQL_renta);
  if ($checkRenta['numRows'] == 0) responderError("La renta no existe o no está activa.");

  $rentaData = $checkRenta['dataFetch'][0];
  $lectura_equipo_id = $rentaData['renta_equipo_id'];
  $esColor = ($rentaData['modelo_tipo'] == "Multicolor");

  // Verificar si ya existe lectura este mes
  $sql_duplicado = "SELECT lectura_id FROM Lecturas 
                      WHERE lectura_renta_id = '$lectura_renta_id' 
                      AND MONTH(lectura_fecha) = '$lectura_mes' 
                      AND YEAR(lectura_fecha) = '$lectura_anio'";
  if (consultaData($sql_duplicado)['numRows'] >= 1) {
    responderError("Ya existe una lectura registrada para esta renta en el mes seleccionado.");
  }

  // 3. Validar Contadores Dinámicamente
  $camposValidar = [
    'lectura_esc' => 'Escaneo',
    'lectura_bn'  => 'B&N'
  ];

  if ($esColor) {
    $camposValidar['lectura_col'] = 'Color';
  } else {
    $lectura_col = 0; // Valor por defecto si es B&N
  }

  foreach ($camposValidar as $campo => $nombre) {
    $valor = $_POST[$campo] ?? "";
    if ($valor === "") responderError("No se ingresó contador de $nombre.");
    if (verificarDatos("^[0-9]+$", $valor)) responderError("El contador de $nombre debe ser numérico.");
    ${$campo} = $valor;
  }

  // 4. Inserción del registro (lectura_pdf será NULL) y guardado de archivos con nombre {lectura_id}.jpg
  $conn = connect();
  $insert = "INSERT INTO Lecturas (lectura_renta_id, lectura_equipo_id, lectura_tipo, lectura_pdf, lectura_esc, lectura_bn, lectura_col, lectura_fecha) "
    . "VALUES ('{$lectura_renta_id}', '{$lectura_equipo_id}', 'Manual', NULL, '{$lectura_esc}', '{$lectura_bn}', '{$lectura_col}', '{$lectura_fecha}')";

  if ($conn->query($insert)) {
    $newLecturaId = $conn->insert_id;

    // Si se subieron archivos, los guardamos con el nombre {lectura_id}.jpg
    if (isset($_FILES['lectura_estado']) || isset($_FILES['lectura_formato'])) {
      $dirs = [
        'PE' => SERVERDIR . "DocsCR/Lecturas/$lectura_anio/$lectura_mes/PE/",
        'Formatos' => SERVERDIR . "DocsCR/Lecturas/$lectura_anio/$lectura_mes/Formatos/"
      ];

      foreach ($dirs as $d) {
        if (!file_exists($d)) mkdir($d, 0755, true);
      }

      $fileName = $newLecturaId . '.jpg';
      foreach (['lectura_estado' => 'PE', 'lectura_formato' => 'Formatos'] as $input => $sub) {
        if (isset($_FILES[$input]) && $_FILES[$input]['name'] != "") {
          if ($_FILES[$input]['type'] != "image/jpeg") {
            responderError("El archivo $input debe ser JPEG.");
          }
          move_uploaded_file($_FILES[$input]['tmp_name'], $dirs[$sub] . $fileName);
        }
      }
    }

    echo json_encode([
      'Alerta' => 'redireccionar',
      'Titulo' => 'Éxito',
      'Texto' => 'Lectura registrada.',
      'Tipo' => 'success',
      'url' => SERVERURL . 'Lecturas/Custom/' . date('Y') . "/" . ucfirst(dateFormat(date('Y-n-d'), 'mesL'))
    ]);
    exit();
  } else {
    responderError("Error técnico: No se pudo realizar el registro en la base de datos. " . $conn->error);
  }
}


function actualizarLectura()
{
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
  $lectura_id = limpiarCadena(decryption($_POST['actualizarLectura']));

  $lectura_fecha = limpiarCadena($_POST['lectura_fecha']);
  $lectura_renta_id = limpiarCadena(decryption($_POST['lectura_renta_id']));
  $comments = limpiarCadena($_POST['comments']);


  if ($lectura_id == "") {
    responderError("El campo del ID de lectura no puede estar vacio, recarga la pagina o verifica que el campo tenga el valor correcto. (TT _ TT).");
  } else if (verificarDatos("^[0-9]+$", $lectura_id)) {
    responderError("(TT _ TT) El formato de la lectura ingresada no tiene el formato correcto, recarga la pagina. (TT _ TT).");
  } else {
    $LecturaIdSQL = "SELECT * FROM Lecturas
                    WHERE lectura_id = '$lectura_id'";
    $LecturaIdQRY = consultaData($LecturaIdSQL);
    if ($LecturaIdQRY['numRows'] == 0) {
      responderError("(TT _ TT) No existe la lectura seleccionada. (TT _ TT).");
    }
  }


  if ($lectura_fecha == "") {
    responderError("(TT _ TT) No se ingreso fecha. (TT _ TT).");
  } else if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $lectura_fecha)) {
    responderError("La renta seleccionada No existe o no se encuentra activa. (TT _ TT).");
  } else {
    list($lectura_anio, $lectura_mes, $lectura_dia) = explode("-", $lectura_fecha);
  }


  if ($lectura_renta_id == "") {
    responderError("(TT _ TT) Debes seleccionar una Renta. (TT _ TT).");
  } else if (verificarDatos("^[0-9]+$", $lectura_renta_id)) {
    responderError("(TT _ TT) La Renta ingresada no tiene el formato solicitado. (TT _ TT).");
  } else {
    $rentaIdSQL = "SELECT * FROM Rentas
                  INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                  INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                  INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                  INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                  WHERE renta_id = '$lectura_renta_id' AND renta_estado = 'Activo'";
    $rentaIdQRY = consultaData($rentaIdSQL);
    if ($rentaIdQRY['numRows'] == 0) {
      responderError("La renta seleccionada No existe o no se encuentra activa. (TT _ TT).");
    } else {
      $rentaData = $rentaIdQRY['dataFetch'][0];
      $esColor = ($rentaData['modelo_tipo'] == "Multicolor");
    }
  }


  $camposValidar = [
    'lectura_esc' => 'Escaneo',
    'lectura_bn'  => 'B&N'
  ];

  // Si es color, lo agregamos al array de validación. 
  // Si no, inicializamos la variable en 0 por seguridad.
  if ($esColor) {
    $camposValidar['lectura_col'] = 'Color';
  } else {
    $lectura_col = 0;
  }

  foreach ($camposValidar as $campo => $nombre) {
    // Asignamos el valor del POST o vacío si no existe
    $valor = $_POST[$campo] ?? "";

    if ($valor === "") {
      responderError("No se ingresó contador de $nombre.");
    }

    if (verificarDatos("^[0-9]+$", $valor)) {
      responderError("El contador de $nombre no es un número, revisa el formato.");
    }

    // Creas dinámicamente las variables ($lectura_esc, $lectura_bn, etc.)
    ${$campo} = $valor;
  }

  // --------------------------------------------- Agregamos el Pendiente --------------------------------------------- //
  if ($comments != "") {
  }
  // --------------------------------- ^^^ FIN ^^^ Agregamos el pendiente ^^^ FIN ^^^ --------------------------------- //


  // --------------------------------------------- Verificando Input File --------------------------------------------- //
  // ************************************************ Evidencia en JPG ************************************************ //

  if (isset($_FILES['lectura_estado']) || isset($_FILES['lectura_formato'])) {
    list($anio, $mes, $dia) = explode("-", $lectura_fecha);

    $dirs = [
      'base' => SERVERDIR . "DocsCR/Lecturas/$anio/$mes/",
      'PE' => SERVERDIR . "DocsCR/Lecturas/$anio/$mes/PE/",
      'Formatos' => SERVERDIR . "DocsCR/Lecturas/$anio/$mes/Formatos/"
    ];

    foreach ($dirs as $d) {
      if (!file_exists($d)) mkdir($d, 0755, true);
    }

    // Guardamos con el nombre {lectura_id}.jpg
    $fileName = $lectura_id . '.jpg';
    foreach (['lectura_estado' => 'PE', 'lectura_formato' => 'Formatos'] as $input => $subCarpeta) {
      if (isset($_FILES[$input]) && $_FILES[$input]['name'] != "") {
        if ($_FILES[$input]['type'] != "image/jpeg") responderError("El archivo $input debe ser JPEG.");
        move_uploaded_file($_FILES[$input]['tmp_name'], $dirs[$subCarpeta] . $fileName);
      }
    }
  }
  // ****************************************************************************************************************** //
  // --------------------------------- ^^^ FIN ^^^ Verificando Input File ^^^ FIN ^^^ --------------------------------- //


  // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //

  $updateLectura = "UPDATE Lecturas SET 
    lectura_fecha = '$lectura_fecha',
    lectura_esc = '$lectura_esc',
    lectura_bn = '$lectura_bn',
    lectura_col = '$lectura_col'";
  // Dejamos lectura_pdf en NULL ya que no se usará
  $updateLectura .= ", lectura_pdf = NULL";
  $updateLectura .= " WHERE lectura_id = '$lectura_id'";

  if (sentenciaData($updateLectura)) {
    echo json_encode(['Alerta' => 'recargar', 'Titulo' => 'Éxito', 'Texto' => 'Lectura actualizada.', 'Tipo' => 'success']);
    exit();
  } else {
    responderError("Error al guardar en la base de datos.");
  }

  // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //
}
