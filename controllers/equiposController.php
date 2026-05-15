<?php

// ------ Agregar Nuevo Equipo
function updateModelo()
{
  // 1. Recepción y Limpieza básica
  $modelo_id = (int)limpiarCadena(decryption($_POST['update_modelo_id']));
  $modelo_tipo = limpiarCadena($_POST['modelo_tipo']);
  $modelo_linea = limpiarCadena($_POST['modelo_linea']);
  $modelo_modelo = limpiarCadena(strtoupper($_POST['modelo_modelo']));
  $modelo_toner = limpiarCadena(strtoupper($_POST['modelo_toner']));

  // Función auxiliar para validar números y manejar el paso de NULL a la base de datos
  $formatearValor = function ($valor, $nombre) {
    $v = limpiarCadena($valor);
    if ($v === "") return "NULL";
    if (!is_numeric($v)) responderError("El campo $nombre debe ser un valor numérico.");
    return "'$v'";
  };

  $modelo_DK = $formatearValor($_POST['modelo_DK'], "DK");
  $modelo_DV = $formatearValor($_POST['modelo_DV'], "DV");
  $modelo_FK = $formatearValor($_POST['modelo_FK'], "FK");
  $modelo_DP = $formatearValor($_POST['modelo_DP'], "DP");
  $modelo_TR = $formatearValor($_POST['modelo_TR'], "TR");
  $modelo_DR = $formatearValor($_POST['modelo_DR'], "DR");

  $modelo_resi = (int)limpiarCadena($_POST['modelo_resi']);
  $modelo_wifi = (int)limpiarCadena($_POST['modelo_wifi']);

  // 2. Validaciones de Existencia
  $checkModelo = consultaData("SELECT modelo_id FROM Modelos WHERE modelo_id = '$modelo_id'");
  if ($checkModelo['numRows'] == 0) {
    responderError("\(- _ -)/ No existe el modelo a editar. \(- _ -)/");
  }

  // 3. Validaciones de Formato y Requeridos
  if ($modelo_modelo == "" || $modelo_toner == "") {
    responderError("\(- _ -)/ El Modelo y el Tóner son campos obligatorios. \(- _ -)/");
  }

  $tipos_validos = ["Monocromatico", "Multicolor"];
  if (!in_array($modelo_tipo, $tipos_validos)) {
    responderError("\(- _ -)/ Tipo de modelo inválido. \(- _ -)/");
  }

  $lineas_validas = ["ECOSYS", "TASKalfa"];
  if (!in_array($modelo_linea, $lineas_validas)) {
    responderError("\(- _ -)/ Línea de modelo inválida. \(- _ -)/");
  }

  // 4. Lógica de modelo_lin solicitada
  $modelo_lin = ($modelo_linea == "ECOSYS") ? "ECO" : "TAS";

  

  // 5. Inserción Transaccional
  $sentencias = [
    "\(- _ -)/ Error al actualizar el modelo en la base de datos. \(- _ -)/" =>
    "UPDATE Modelos SET 
        modelo_tipo = '$modelo_tipo',
        modelo_linea = '$modelo_linea',
        modelo_lin = '$modelo_lin',
        modelo_modelo = '$modelo_modelo',
        modelo_toner = '$modelo_toner',
        modelo_DK = $modelo_DK,
        modelo_DV = $modelo_DV,
        modelo_TR = $modelo_TR,
        modelo_FK = $modelo_FK,
        modelo_DP = $modelo_DP,
        modelo_DR = $modelo_DR,
        modelo_resi = '$modelo_resi',
        modelo_wifi = '$modelo_wifi'
      WHERE modelo_id = '$modelo_id'"
  ];

  $res = transactionData($sentencias);

  if ($res['status']) {
    echo json_encode([
      'Alerta' => 'redireccionar',
      'Titulo' => '¡Operación exitosa!',
      'Texto' => 'Modelo actualizado correctamente: ' . $modelo_linea . ' ' . $modelo_modelo,
      'Tipo' => 'success',
      'url' => '/Equipos/Modelos'
    ]);
  } else {
    responderError($res['result']);
  }
  exit();
}

// ------ Agregar Nuevo Equipo
function agregarEquipo()
{
  // 1. Recepción y limpieza
  $equipo_serie = limpiarCadena(str_replace(" ", "", strtoupper($_POST['equipo_serie'])));
  $equipo_fingreso = limpiarCadena($_POST['equipo_fingreso']);
  $equipo_estado = limpiarCadena($_POST['equipo_estado']);
  $equipo_modelo_id = limpiarCadena(decryption($_POST['equipo_modelo_id']));
  $equipo_provE_id = limpiarCadena(decryption($_POST['equipo_provE_id']));

  // 2. Validaciones básicas de formato
  if (verificarDatos("[A-Z0-9]{9,15}", $equipo_serie)) {
    responderError("\(- _ -)/ El No. de Serie debe tener entre 9 y 15 caracteres alfanuméricos. \(- _ -)/");
  }

  $checkSerie = consultaData("SELECT equipo_id FROM Equipos WHERE equipo_serie = '$equipo_serie'");
  if ($checkSerie['numRows'] > 0) {
    responderError("\(- _ -)/ Este No. de Serie ya está registrado. \(- _ -)/");
  }

  if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $equipo_fingreso)) {
    responderError("\(- _ -)/ Formato de fecha inválido. \(- _ -)/");
  }

  $estados_validos = ["Espera", "Reparacion", "Inhabilitado"];
  if (!in_array($equipo_estado, $estados_validos)) {
    responderError("\(- _ -)/ Estado de equipo incorrecto para registro nuevo. \(- _ -)/");
  }

  // 3. Procesar Suministros (Niveles y Chips)
  $chip_k = $chip_m = $chip_c = $chip_y = 0;
  $equipo_nivel_K = $equipo_nivel_M = $equipo_nivel_C = $equipo_nivel_Y = $equipo_nivel_R = 0;
  $suministros = ['K' => 'Negro', 'M' => 'Magenta', 'C' => 'Cyan', 'Y' => 'Amarillo'];

  foreach ($suministros as $key => $label) {
    $nombreNivel = "equipo_nivel_{$key}";
    $nombreChip  = "chip_" . strtolower($key);

    $valorNivel = (isset($_POST[$nombreNivel]) && $_POST[$nombreNivel] !== "") ? limpiarCadena($_POST[$nombreNivel]) : "0";
    $valorChip = (isset($_POST[$nombreChip])) ? (int)$_POST[$nombreChip] : 0;

    if (verificarDatos("^[0-9]+$", $valorNivel)) responderError("Nivel $label inválido.");

    ${$nombreNivel} = $valorNivel;
    ${$nombreChip}  = $valorChip;
  }

  $equipo_nivel_R = (isset($_POST['equipo_nivel_R']) && $_POST['equipo_nivel_R'] !== "") ? limpiarCadena($_POST['equipo_nivel_R']) : "0";

  // 4. Generación Automática del Código de Equipo
  $query_modelo = consultaData("SELECT modelo_lin FROM Modelos WHERE modelo_id = '$equipo_modelo_id'");
  if ($query_modelo['numRows'] == 0) responderError("Modelo no encontrado.");

  $modelo_lin = $query_modelo['dataFetch'][0]['modelo_lin'];
  $equipo_codigo = $modelo_lin . '-001';

  // Lógica incremental para el código
  while (true) {
    $checkCod = consultaData("SELECT equipo_id FROM Equipos WHERE equipo_codigo = '$equipo_codigo'");
    if ($checkCod['numRows'] == 0) break;
    $equipo_codigo++; // PHP incrementa strings alfanuméricos automáticamente (A-001 -> A-002)
  }

  // 5. Inserción Transaccional
  $sentencias = [
    "\(- _ -)/ Error al registrar el equipo en la base de datos. \(- _ -)/" =>
    "INSERT INTO Equipos (
            equipo_modelo_id, equipo_provE_id, equipo_estado, equipo_codigo, 
            equipo_serie, equipo_fingreso, chip_k, chip_m, chip_c, chip_y,
            equipo_nivel_K, equipo_nivel_M, equipo_nivel_C, equipo_nivel_Y, equipo_nivel_R
        ) VALUES (
            '$equipo_modelo_id', '$equipo_provE_id', '$equipo_estado', '$equipo_codigo', 
            '$equipo_serie', '$equipo_fingreso', '$chip_k', '$chip_m', '$chip_c', '$chip_y',
            '$equipo_nivel_K', '$equipo_nivel_M', '$equipo_nivel_C', '$equipo_nivel_Y', '$equipo_nivel_R'
        )"
  ];

  $res = transactionData($sentencias);

  if ($res['status']) {
    echo json_encode([
      'Alerta' => 'recargar',
      'Titulo' => '¡Éxito!',
      'Texto' => 'Equipo registrado con código: ' . $equipo_codigo,
      'Tipo' => 'success'
    ]);
  } else {
    responderError($res['result']);
  }
  exit();
}

// ------ Actualizar Equipo
function actualizarEquipo()
{
  session_start();

  // 1. Recepción y Limpieza básica
  $equipo_id = limpiarCadena(decryption($_POST['actualizarEquipo']));
  $equipo_serie = limpiarCadena(str_replace(" ", "", strtoupper($_POST['equipo_serie'])));
  $equipo_fingreso = limpiarCadena($_POST['equipo_fingreso']);
  $equipo_modelo_id = limpiarCadena(decryption($_POST['equipo_modelo_id']));
  $equipo_provE_id = limpiarCadena(decryption($_POST['equipo_provE_id']));

  // 2. Validaciones de Existencia y Negocio
  $equQRY = consultaData("SELECT equipo_id FROM Equipos WHERE equipo_id = '$equipo_id'");
  if ($equQRY['numRows'] == 0) {
    responderError("\(- _ -)/ No existe el equipo a editar. \(- _ -)/");
  }

  $equSerieQRY = consultaData("SELECT equipo_id FROM Equipos WHERE equipo_serie = '$equipo_serie' AND equipo_id != '$equipo_id'");
  if ($equSerieQRY['numRows'] > 0) {
    responderError("\(- _ -)/ El No. de Serie ya está registrado en otro equipo. \(- _ -)/");
  }

  // 3. Lógica de Estado (Rentas)
  $equRentQRY = consultaData("SELECT renta_finicio FROM Rentas WHERE renta_equipo_id = '$equipo_id' AND renta_estado = 'Activo'");
  if ($equRentQRY['numRows'] >= 2) {
    responderError("(TT_TT) Error Grave: Equipo en más de una renta activa. (TT_TT)");
  }

  if ($equRentQRY['numRows'] == 1) {
    $equipo_estado = "Rentado";
  } else {
    $equipo_estado = limpiarCadena($_POST['equipo_estado']);
    $estados_permitidos = ["Espera", "Reparacion", "Inhabilitado", "Vendido"];

    if ($equipo_estado == "" || !in_array($equipo_estado, $estados_permitidos)) {
      responderError("\(- _ -)/ Estado de equipo inválido o no seleccionado. \(- _ -)/");
    }
  }

  // 4. Validaciones de Formato (Regex)
  if (verificarDatos("[A-Z0-9]{9,15}", $equipo_serie)) {
    responderError("\(- _ -)/ El No. de Serie debe tener entre 9 y 15 caracteres (mayúsculas/números). \(- _ -)/");
  }

  if (verificarDatos("^([0-9]{4})-([0-9]{2})-([0-9]{2})$", $equipo_fingreso)) {
    responderError("\(- _ -)/ Formato de fecha incorrecto (AAAA-MM-DD). \(- _ -)/");
  }

  if (verificarDatos("^[0-9]+$", $equipo_modelo_id) || verificarDatos("^[0-9]+$", $equipo_provE_id)) {
    responderError("\(- _ -)/ Error en los identificadores de Modelo o Proveedor. \(- _ -)/");
  }

  // 5. Procesamiento de Suministros (Niveles y Chips)
  // Inicializamos todas para evitar el error de "Undefined Variable"
  $chip_k = $chip_m = $chip_c = $chip_y = 0;
  $equipo_nivel_K = $equipo_nivel_M = $equipo_nivel_C = $equipo_nivel_Y = $equipo_nivel_R = 0;

  $suministros = ['K' => 'Negro', 'M' => 'Magenta', 'C' => 'Cyan', 'Y' => 'Amarillo'];

  foreach ($suministros as $key => $label) {
    $nombreNivel = "equipo_nivel_{$key}";
    $nombreChip  = "chip_" . strtolower($key);

    // Procesar Nivel
    $valorNivel = (isset($_POST[$nombreNivel]) && $_POST[$nombreNivel] !== "") ? limpiarCadena($_POST[$nombreNivel]) : "0";
    if (verificarDatos("^[0-9]+$", $valorNivel)) responderError("Nivel $label inválido.");

    // Procesar Chip (Forzamos entero 0 o 1)
    $valorChip = (isset($_POST[$nombreChip])) ? (int)$_POST[$nombreChip] : 0;

    // Asignación dinámica
    ${$nombreNivel} = $valorNivel;
    ${$nombreChip}  = $valorChip;
  }

  // Residual (Caso aparte)
  $equipo_nivel_R = (isset($_POST['equipo_nivel_R']) && $_POST['equipo_nivel_R'] !== "") ? limpiarCadena($_POST['equipo_nivel_R']) : "0";
  if (verificarDatos("^[0-9]+$", $equipo_nivel_R)) responderError("Nivel Residual inválido.");


  // 6. Ejecución de Transacción
  $sentencias = [
    "\(- _ -)/ Error al actualizar los datos del equipo. \(- _ -)/" =>
    "UPDATE Equipos SET
      equipo_modelo_id = '$equipo_modelo_id',
      equipo_provE_id = '$equipo_provE_id',
      equipo_estado = '$equipo_estado',
      equipo_serie = '$equipo_serie',
      equipo_fingreso = '$equipo_fingreso',
      chip_k = '$chip_k',
      chip_m = '$chip_m',
      chip_c = '$chip_c',
      chip_y = '$chip_y',
      equipo_nivel_K = '$equipo_nivel_K',
      equipo_nivel_M = '$equipo_nivel_M',
      equipo_nivel_C = '$equipo_nivel_C',
      equipo_nivel_Y = '$equipo_nivel_Y',
      equipo_nivel_R = '$equipo_nivel_R'
    WHERE equipo_id = '$equipo_id'"
  ];

  $ejecucion = transactionData($sentencias);

  if ($ejecucion['status']) {
    echo json_encode([
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Actualizado',
      'Texto' => '\(- _ -)/ El equipo ha sido actualizado con éxito. \(- _ -)/',
      'Tipo' => 'success'
    ]);
  } else {
    responderError($ejecucion['result']);
  }
  exit();
}

// -------------------- Seccion que para agrear Configuraciones de equipo
// ------ Configuracion de WIFI
function configuracionWIFI()
{
  $equWifi_equipo_id = decryption($_POST['configuracionWIFI']);
  $equWifi_SSID = $_POST['equWifi_SSID'];
  $equWifi_WPA = $_POST['equWifi_WPA'];
  $equWifi_IP = $_POST['equWifi_IP'];
  $equWifi_MASK = $_POST['equWifi_MASK'];
  $equWifi_PE = $_POST['equWifi_PE'];
  $existe = (consultaData("SELECT * FROM equipos_wifi WHERE equWifi_equipo_id = '$equWifi_equipo_id'")['numRows'] == 1) ? TRUE : FALSE;

  if ($equWifi_SSID == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso Nombre de Red SSID. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($equWifi_WPA == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso Clave de Red. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($equWifi_IP == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso IP asignada. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{1,3}\.){3}[0-9]{1,3}$", $equWifi_IP)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser como el siguiente: 192.168.1.100. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($equWifi_MASK == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso Mascara de Red. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{1,3}\.){3}[0-9]{1,3}$", $equWifi_MASK)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser como el siguiente: 255.255.255.0. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($equWifi_PE == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso Puerta de Enlace. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{1,3}\.){3}[0-9]{1,3}$", $equWifi_PE)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser como el siguiente: 192.168.1.1. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($existe) {
    $SQL = "UPDATE equipos_wifi SET
            equWifi_SSID = '$equWifi_SSID',
            equWifi_WPA = '$equWifi_WPA',
            equWifi_IP = '$equWifi_IP',
            equWifi_MASK = '$equWifi_MASK',
            equWifi_PE = '$equWifi_PE'
            WHERE equWifi_equipo_id = '$equWifi_equipo_id'";
    $Texto = 'actualizo';
  } else {
    $SQL = "INSERT INTO equipos_wifi (equWifi_equipo_id, equWifi_SSID, equWifi_WPA, equWifi_IP, equWifi_MASK, equWifi_PE) VALUES ('$equWifi_equipo_id', '$equWifi_SSID', '$equWifi_WPA', '$equWifi_IP', '$equWifi_MASK', '$equWifi_PE')";
    $Texto = 'agrego';
  }
  if (sentenciaData($SQL)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Se ' . $Texto . ' la configuracion correctamente. \(- _ -)/',
      'Tipo' => 'success'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se pudo realizar la configuracion. \(- _ -)/',
      'Tipo' => 'error'
    ];
  }
  echo json_encode($alerta);
  exit();
}

// ------ Configuracion de Ethernet
function configuracionEthernet()
{
  $equEther_equipo_id = decryption($_POST['configuracionEthernet']);
  $equEther_IP = $_POST['equEther_IP'];
  $equEther_MASK = $_POST['equEther_MASK'];
  $equEther_PE = $_POST['equEther_PE'];
  $existe = (consultaData("SELECT * FROM equipos_ether WHERE equEther_equipo_id = '$equEther_equipo_id'")['numRows'] == 1) ? TRUE : FALSE;

  if ($equEther_IP == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso IP asignada. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{1,3}\.){3}[0-9]{1,3}$", $equEther_IP)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser como el siguiente: 192.168.1.100. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($equEther_MASK == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso Mascara de Red. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{1,3}\.){3}[0-9]{1,3}$", $equEther_MASK)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser como el siguiente: 255.255.255.0. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($equEther_PE == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se ingreso Puerta de Enlace. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([0-9]{1,3}\.){3}[0-9]{1,3}$", $equEther_PE)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser como el siguiente: 192.168.1.1. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if ($existe) {
    $SQL = "UPDATE equipos_ether SET
            equEther_IP = '$equEther_IP',
            equEther_MASK = '$equEther_MASK',
            equEther_PE = '$equEther_PE'
            WHERE equEther_equipo_id = '$equEther_equipo_id'";
    $Texto = 'actualizo';
  } else {
    $SQL = "INSERT INTO `equipos_ether` (`equEther_equipo_id`, `equEther_IP`, `equEther_MASK`, `equEther_PE`) VALUES ('$equEther_equipo_id', '$equEther_IP', '$equEther_MASK', '$equEther_PE')";
    $Texto = 'agrego';
  }
  if (sentenciaData($SQL)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Se ' . $Texto . ' la configuracion correctamente. \(- _ -)/',
      'Tipo' => 'success'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se pudo realizar la configuracion. \(- _ -)/',
      'Tipo' => 'error'
    ];
  }
  echo json_encode($alerta);
  exit();
}

// ------ Configuracion de Ethernet
function equipoContactoAdd()
{
  $equCon_equipo_id = decryption($_POST['equipoContactoAdd']);
  $equCon_nombre = $_POST['equCon_nombre'];
  $equCon_host = $_POST['equCon_host'];
  $equCon_ruta = $_POST['equCon_ruta'];
  $equCon_usuario = $_POST['equCon_usuario'];
  $equCon_clave = $_POST['equCon_clave'];
  $equCon_correo = $_POST['equCon_correo'];

  if (strlen($equCon_nombre) < 5) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes agregar un Nombre de Contacto mayor a 5 caracteres. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[a-zA-Z0-9]+( [a-zA-Z0-9]+)*$", $equCon_nombre)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser texto obligatorio, sin espacios en los extremos y solo espacios simples entre palabras. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})?$", $equCon_correo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formatode la clave debe ser sin espacios puede incluir (@ % $ # * () - _ !). \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^(\\\\[a-zA-Z0-9.-]+|(\d{1,3}\.){3}\d{1,3}|[a-zA-Z0-9-]+)?$", $equCon_host)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser como el siguiente: \\url.remotehost.com, 192.168.1.100, DESKTOP-SD451D, PCany \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if (verificarDatos("^[a-zA-Z0-9]*$", $equCon_ruta)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato de la ruta solo pueden ser numeros y letras mayusculas o minusculas sin espacios. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if (!$equCon_host == "" && $equCon_ruta == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Si agregaste un host debes agregar tambien la ruta \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($equCon_correo == "" && $equCon_host == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes agregar un correo o una ruta \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^[a-zA-Z0-9]*$", $equCon_usuario)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato del usuario solo pueden ser numeros y letras mayusculas o minusculas sin espacios. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^[a-zA-Z0-9@%$#*()\-_!]*$", $equCon_clave)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formatode la clave debe ser sin espacios puede incluir (@ % $ # * () - _ !). \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($equCon_host != "" && $equCon_usuario == "" && $equCon_clave == "") {
    $equCon_usuario = "user";
    $equCon_clave = "user";
  }

  $INSERT = "INSERT INTO equipos_contactos (equCon_equipo_id, equCon_nombre, equCon_host, equCon_ruta, equCon_usuario, equCon_clave, equCon_correo) VALUES ('$equCon_equipo_id', '$equCon_nombre', '$equCon_host', '$equCon_ruta', '$equCon_usuario', '$equCon_clave', '$equCon_correo')";

  if (sentenciaData($INSERT)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Registro agregado correctamente. \(- _ -)/',
      'Tipo' => 'success'
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ No se pudo realizar el registro. \(- _ -)/',
      'Tipo' => 'error'
    ];
  }
  echo json_encode($alerta);
  exit();
}

// ------ Configuracion de Ethernet
function equipoContactoEdit()
{
  $equCon_id = decryption($_POST['equipoContactoEdit']);
  $equCon_nombre = $_POST['equCon_nombre'];
  $equCon_host = $_POST['equCon_host'];
  $equCon_ruta = $_POST['equCon_ruta'];
  $equCon_usuario = $_POST['equCon_usuario'];
  $equCon_clave = $_POST['equCon_clave'];
  $equCon_correo = $_POST['equCon_correo'];

  if (strlen($equCon_nombre) < 5) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes agregar un Nombre de Contacto mayor a 5 caracteres. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^[a-zA-Z0-9]+( [a-zA-Z0-9]+)*$", $equCon_nombre)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser texto obligatorio, sin espacios en los extremos y solo espacios simples entre palabras. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})?$", $equCon_correo)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formatode la clave debe ser sin espacios puede incluir (@ % $ # * () - _ !). \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^(\\\\[a-zA-Z0-9.-]+|(\d{1,3}\.){3}\d{1,3}|[a-zA-Z0-9-]+)?$", $equCon_host)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato debe ser como el siguiente: \\url.remotehost.com, 192.168.1.100, DESKTOP-SD451D, PCany \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if (verificarDatos("^[a-zA-Z0-9]*$", $equCon_ruta)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato de la ruta solo pueden ser numeros y letras mayusculas o minusculas sin espacios. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }
  if (!$equCon_host == "" && $equCon_ruta == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Si agregaste un host debes agregar tambien la ruta \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($equCon_correo == "" && $equCon_host == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ Debes agregar un correo o una ruta \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^[a-zA-Z0-9]*$", $equCon_usuario)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formato del usuario solo pueden ser numeros y letras mayusculas o minusculas sin espacios. \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if (verificarDatos("^[a-zA-Z0-9@%$#*()\-_!]*$", $equCon_clave)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '\(- _ -)/ El formatode la clave debe ser sin espacios puede incluir (@ % $ # * () - _ !). \(- _ -)/',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  }

  if ($equCon_host != "" && $equCon_usuario == "" && $equCon_clave == "") {
    $equCon_usuario = "user";
    $equCon_clave = "user";
  }

  $UPDATE = "UPDATE equipos_contactos SET
              equCon_nombre = '$equCon_nombre',
              equCon_host = '$equCon_host',
              equCon_ruta = '$equCon_ruta',
              equCon_usuario = '$equCon_usuario',
              equCon_clave = '$equCon_clave',
              equCon_correo = '$equCon_correo'
              WHERE equCon_id = $equCon_id";

  if (sentenciaData($UPDATE)) {
    $alerta = [
      'Alerta' => 'recargar',
      'Titulo' => 'Registro Completado',
      'Texto' => '\(- _ -)/ Registro actualizado correctamente. \(- _ -)/',
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
  exit();
}
