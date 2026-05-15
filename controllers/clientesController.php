<?php
function clienteControlador()
{
  // 1. DETERMINAR ID Y MODO
  // Buscamos si viene de 'actualizarCliente' o 'agregarCliente'
  $id_raw = $_POST['actualizarCliente'] ?? $_POST['agregarCliente'] ?? null;
  $cliente_id = ($id_raw) ? decryption($id_raw) : 0;

  // Es UPDATE si el ID es válido y existe la llave de actualización
  $is_update = (isset($_POST['actualizarCliente']) && $cliente_id > 0);

  // 2. LIMPIEZA Y DATOS BÁSICOS (OBLIGATORIOS)
  $tipo = limpiarCadena($_POST['cliente_tipo'] ?? 'Fisica');
  $rfc  = limpiarCadena(str_replace("ñ", "Ñ", strtoupper($_POST['cliente_rfc'] ?? '')));
  $rs   = limpiarCadena(str_replace("ñ", "Ñ", strtoupper($_POST['cliente_rs'] ?? '')));
  $cp   = limpiarCadena($_POST['cliente_cp'] ?? '');

  // Validamos prioridad según tus formularios
  if ($rfc == "") responderError("El RFC es obligatorio para identificar al contribuyente.");
  if ($rs == "")  responderError("La Razón Social o Nombre es obligatorio.");
  if ($cp == "")  responderError("El Código Postal fiscal es obligatorio.");

  // Validacion de RFC duplicado
  $sqlDPXrfc = "SELECT cliente_id FROM Clientes WHERE cliente_rfc = '$rfc'";
  if ($is_update) {
    $sqlDPXrfc .= " AND cliente_id != '$cliente_id'";
  }
  if (consultaData($sqlDPXrfc)['numRows'] > 0) {
    responderError("El RFC '$rfc' ya se encuentra registrado con otro cliente. Por favor, verifícalo.");
  }

  // 3. CAMPOS CONDICIONALES SEGÚN TIPO
  // Inicializamos todo en vacío/null para evitar errores de variable no definida
  $curp = $nombre = $apellido1 = $apellido2 = $regCap = "";

  if ($tipo == "Fisica") {
    $curp      = limpiarCadena(strtoupper($_POST['cliente_curp'] ?? ''));
    $nombre    = limpiarCadena(strtoupper($_POST['cliente_nombre'] ?? ''));
    $apellido1 = limpiarCadena(strtoupper($_POST['cliente_apellido1'] ?? ''));
    $apellido2 = limpiarCadena(strtoupper($_POST['cliente_apellido2'] ?? ''));
  } else {
    // Para Morales
    $regCap = limpiarCadena(strtoupper($_POST['cliente_regCap'] ?? ''));
  }

  // 4. DATOS FISCALES Y CONTACTO (CON DESENCRIPTACIÓN)
  $regFis   = isset($_POST['cliente_regFis_id']) ? decryption($_POST['cliente_regFis_id']) : 0;
  $cfdi     = isset($_POST['cliente_cfdi_id'])   ? decryption($_POST['cliente_cfdi_id'])   : 0;
  $contacto = limpiarCadena(strtoupper($_POST['cliente_contacto'] ?? ''));
  $correo   = limpiarCadena($_POST['cliente_correo'] ?? '');
  $telefono = limpiarCadena($_POST['cliente_telefono'] ?? '');

  // 5. DOMICILIO (Mapeo masivo)
  $dirFields = [
    'cliente_tipoVialidad',
    'cliente_noVialidad',
    'cliente_nuExterior',
    'cliente_nuInterior',
    'cliente_noColonia',
    'cliente_noMunicipio',
    'cliente_calle1',
    'cliente_calle2',
    'cliente_nombreComercial'
  ];
  $dirV = [];
  foreach ($dirFields as $f) {
    $dirV[$f] = limpiarCadena(strtoupper($_POST[$f] ?? ''));
  }

  // Helper para SQL (Manejo de Nulll)
  $toSql = function ($v) {
    return ($v == "" || $v == "0") ? "NULL" : "'$v'";
  };

  // 6. CONSTRUCCIÓN DE SQL
  if ($is_update) {
    $sql = "UPDATE Clientes SET 
                cliente_tipo='$tipo', cliente_rs='$rs', cliente_rfc='$rfc', 
                cliente_curp=" . $toSql($curp) . ", cliente_nombre=" . $toSql($nombre) . ", 
                cliente_apellido1=" . $toSql($apellido1) . ", cliente_apellido2=" . $toSql($apellido2) . ",
                cliente_regCap=" . $toSql($regCap) . ", cliente_nombreComercial=" . $toSql($dirV['cliente_nombreComercial']) . ",
                cliente_cp='$cp', cliente_regFis_id=" . $toSql($regFis) . ", cliente_cfdi_id=" . $toSql($cfdi) . ",
                cliente_contacto='$contacto', cliente_correo='$correo', cliente_telefono='$telefono',
                cliente_tipoVialidad='{$dirV['cliente_tipoVialidad']}', cliente_noVialidad='{$dirV['cliente_noVialidad']}',
                cliente_nuExterior='{$dirV['cliente_nuExterior']}', cliente_nuInterior='{$dirV['cliente_nuInterior']}',
                cliente_noColonia='{$dirV['cliente_noColonia']}', cliente_noMunicipio='{$dirV['cliente_noMunicipio']}',
                cliente_calle1='{$dirV['cliente_calle1']}', cliente_calle2='{$dirV['cliente_calle2']}'
                WHERE cliente_id='$cliente_id'";
    $msg = "Datos del cliente actualizados correctamente.";
  } else {
    $sql = "INSERT INTO Clientes (
                    cliente_tipo, cliente_rs, cliente_rfc, cliente_curp, cliente_nombre, 
                    cliente_apellido1, cliente_apellido2, cliente_regCap, cliente_nombreComercial,
                    cliente_cp, cliente_regFis_id, cliente_cfdi_id, cliente_contacto, 
                    cliente_correo, cliente_telefono, cliente_tipoVialidad, cliente_noVialidad, 
                    cliente_nuExterior, cliente_nuInterior, cliente_noColonia, cliente_noMunicipio, 
                    cliente_calle1, cliente_calle2
                ) VALUES (
                    '$tipo', '$rs', '$rfc', " . $toSql($curp) . ", " . $toSql($nombre) . ", 
                    " . $toSql($apellido1) . ", " . $toSql($apellido2) . ", " . $toSql($regCap) . ", " . $toSql($dirV['cliente_nombreComercial']) . ",
                    '$cp', " . $toSql($regFis) . ", " . $toSql($cfdi) . ", '$contacto', 
                    '$correo', '$telefono', '{$dirV['cliente_tipoVialidad']}', '{$dirV['cliente_noVialidad']}', 
                    '{$dirV['cliente_nuExterior']}', '{$dirV['cliente_nuInterior']}', '{$dirV['cliente_noColonia']}', 
                    '{$dirV['cliente_noMunicipio']}', '{$dirV['cliente_calle1']}', '{$dirV['cliente_calle2']}'
                )";
    $msg = "Nuevo cliente registrado con éxito.";
  }

  // 7. EJECUCIÓN
  $res = transactionData([$msg => $sql]);

  if ($res['status']) {
    // 8. MANEJO DE ARCHIVO (OPCIONAL)
    // Obtenemos el ID real (si fue insert, lo pedimos a la conexión)
    $dbID = ($is_update) ? $cliente_id : connect()->insert_id;

    if (isset($_FILES['cliente_pdf']) && $_FILES['cliente_pdf']['error'] === UPLOAD_ERR_OK) {
      $dir = $_SERVER['DOCUMENT_ROOT'] . "/DocsCR/Constancias/";
      if (!file_exists($dir)) mkdir($dir, 0777, true);

      // Usamos el ID encriptado como nombre de archivo para coincidir con tu lógica
      $fileName = encryption($dbID) . ".pdf";
      move_uploaded_file($_FILES['cliente_pdf']['tmp_name'], $dir . $fileName);
    }

    echo json_encode([
      'Alerta' => 'recargar',
      'Titulo' => 'Operación Exitosa',
      'Texto' => $msg,
      'Tipo' => 'success'
    ]);
    exit();
  } else {
    responderError("Error en base de datos: " . $res['result']);
  }
}
