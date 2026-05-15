<?php

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
  $protocol = 'https://';
} else {
  $protocol = 'http://';
}
$host = $_SERVER["HTTP_HOST"] ?? 'localhost';
define('SERVERURL', $protocol . $host . '/');
define('SERVERDIR', $_SERVER["DOCUMENT_ROOT"] . '/');
define('SERVERNAME', 'SystemCR');
define('MONEDA', '$');
define('LOGOCR', SERVERDIR . 'vista/assets/img/logo.png');
define('COMPANYNAME', utf8_decode('CR - Imprime Tus Ideas'));
define('WEBSITE', utf8_decode('www.cr-imprimetusideas.com.mx'));
define('dataRFC1', utf8_decode('RENAN ARMANDO MAGAÑA DIAZ (MADR8504096K8)'));
define('dataRFC2', utf8_decode('CALLE ISLA MAGDALENA, MANZANA 536 LOTE 1,'));
define('dataRFC3', utf8_decode('EDIFICIO C DEPARTAMENTO 203, CANCÚN,'));
define('dataRFC4', utf8_decode('BENITO JUARÉZ, QUINTANA ROO, MÉXICO, CP 77517'));

date_default_timezone_get();
date_default_timezone_set("America/Cancun");
setlocale(LC_TIME, 'es_MX.UTF-8', 'esp');
// setlocale(LC_TIME, 'es_VE.UTF-8', 'esp');

define('CURRDATE', dateFormat(date('Y-m-d'), "full"));
define('CURRYEAR', dateFormat(date('Y-m-d'), "anio"));
define('CURRMONTHL', dateFormat(date('Y-m-d'), "mesL"));
define('CURRMONTHN', dateFormat(date('Y-m-d'), "mesN"));
define('CURRDAYL', dateFormat(date('Y-m-d'), "diaL"));
define('CURRDAYN', dateFormat(date('Y-m-d'), "diaN"));

// Detectamos el host de forma segura
$currentHost = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Configuramos las credenciales según el servidor
define("USER", "admin1");
define("DB", "SystemCR_v3");
define("HOST", "localhost");
define("PASS", "MySQL_admin1_");

const METHOD = 'AES-256-CBC';
const SECRET_KEY = '$SYSTEMA@2024';
const SECRET_IV = '010203';

// // Esto hará que cualquier error de PHP se convierta en una excepción
// set_error_handler(function ($errno, $errstr, $errfile, $errline) {
//   throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
// });

// Esto atrapará CUALQUIER error no manejado y lo devolverá como JSON
set_exception_handler(function ($e) {
  $alerta = [
    'Alerta' => 'simple',
    'Titulo' => 'Error de Ejecución',
    'Texto' => "Mensaje: " . $e->getMessage() . " en línea " . $e->getLine(),
    'Tipo' => 'error'
  ];
  echo json_encode($alerta);
  exit();
});

// Función auxiliar para respuestas rápidas
function responderError($msj)
{
  echo json_encode([
    'Alerta' => 'simple',
    'Titulo' => 'Error inesperado',
    'Texto' => $msj,
    'Tipo' => 'error'
  ]);
  exit();
};

/* --------------------- Funcion Obtener Vista --------------------- */
function getVista($vista)
{
  $respuesta = "404";
  if (isset($_GET["vista"])) {

    $listaBlanca = array(
      'Dash',
      'Almacen',
      'Clientes',
      'Contratos',
      'Cotizador',
      'Rentas',
      'Lecturas',
      'Lecturas2',
      'Cobranzas',
      'Equipos',
      'Toners',
      'Refacciones',
      'Proveedores',
      'Cambios',
      'ReportesR',
      'ReportesF',
      'Retiros',
      'Usuarios',
    );

    if (in_array($vista[0], $listaBlanca)) {
      if (is_file('./vista/cont/' . $vista[0] . '/configFile.php')) {
        $respuesta = './vista/cont/' . $vista[0] . '/configFile.php';
      } else {
        $respuesta = '404';
      }
    } else if ($vista[0] == 'Login') {
      $respuesta = 'Login';
    } else {
      $respuesta = '404';
    }
  } else {
    redirect(SERVERURL . "Dash/");
    exit();
  }
  return $respuesta;
} // Fin del la Funcion

function endForm($texto)
{
  if ($texto == "GUARDAR") {
    $color = "info";
    $icono = '<i class="far fa-save"></i>';
  } else if ($texto == "ACTUALIZAR") {
    $color = "warning";
    $icono = '<i class="fas fa-pen"></i>';
  } else if ($texto == "INICIAR") {
    $color = "primary";
    $icono = '<i class="fas fa-play"></i>';
  } else if ($texto == "AGREGAR") {
    $color = "light";
    $icono = '<i class="fas fa-plus-square"></i>';
  }

  echo '
        <input type="hidden" name="usuario_admin" id="usuario_admin" value="' . $_SESSION['usuario'] . '">
        <input type="hidden" name="clave_admin" id="clave_admin" value="' . $_SESSION['passclave'] . '">
        <p class="text-center" style="margin-top: 40px;">
            <button type="submit" class="btn btn-raised btn-' . $color . ' btn-sm">' . $icono . ' &nbsp; ' . $texto . '</button>
            &nbsp; &nbsp;
            <button id="resetBtn" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
        </p>
        ';
}

// ------------------------- Funcion para Añadir el stock a un Array de Producto --------------- //
function consultaAlmacenP($WHERE = "", $ORDER = "")
{
  $AlmP_SQL = "SELECT * FROM AlmacenP
              INNER JOIN AlmacenProvs ON AlmacenP.AlmP_prov_id = AlmacenProvs.AlmProv_id
              INNER JOIN unidadesList ON AlmacenP.AlmP_unidadM = unidadesList.unList_id";
  $espacio = " ";

  if ($WHERE != "") {
    $AlmP_SQL = $AlmP_SQL . $espacio . $WHERE . $espacio;
  }

  if ($ORDER != "") {
    $AlmP_SQL = $AlmP_SQL . $espacio . $ORDER;
  }

  $AlmP_QRY = consultaData($AlmP_SQL);
  $AlmP_array = $AlmP_QRY['dataFetch'];
  for ($i = 0; $i < $AlmP_QRY['numRows']; $i++) {
    // Variables
    $AlmP_stock = 0;
    $AlmP_id = $AlmP_array[$i]['AlmP_id'];

    // Sumando Entradas al STOCK
    $AlmP_stock_SQL = "SELECT * FROM AlmacenD INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id WHERE AlmDP_id = '$AlmP_id' AND AlmM_estado = 1";

    $AlmP_stock_IN = consultaData($AlmP_stock_SQL .  " AND AlmM_tipo = 0");
    foreach ($AlmP_stock_IN['dataFetch'] as $AlmP_stock_IN_row) {
      $AlmP_stock = $AlmP_stock + $AlmP_stock_IN_row['AlmD_cantidad'];
    }

    // Restando Salidas al STOCK
    $AlmP_stock_OUT = consultaData($AlmP_stock_SQL . " AND AlmM_tipo != 0");
    foreach ($AlmP_stock_OUT['dataFetch'] as $AlmP_stock_OUT_row) {
      $AlmP_stock = $AlmP_stock - $AlmP_stock_OUT_row['AlmD_cantidad'];
    }

    // Agregando stock al array del producto
    $AlmP_array[$i]['AlmP_stock'] = $AlmP_stock;
  }

  return ["numRows" => $AlmP_QRY['numRows'], "dataFetch" => $AlmP_array];
}

// ------------------------- Funcion para Añadir el stock a un Array de Producto --------------- //
function productosStock()
{
  $AlmP_SQL = "SELECT * FROM AlmacenP
                INNER JOIN AlmacenProvs ON AlmacenP.AlmP_prov_id = AlmacenProvs.AlmProv_id
                INNER JOIN unidadesList ON AlmacenP.AlmP_unidadM = unidadesList.unList_id
              WHERE AlmP_cat_id = 1 OR AlmP_cat_id = 2 OR AlmP_cat_id = 3
              ORDER BY AlmP_cat_id ASC";

  $AlmP_QRY = consultaData($AlmP_SQL);
  $AlmP_array = $AlmP_QRY['dataFetch'];
  for ($i = 0; $i < $AlmP_QRY['numRows']; $i++) {
    // Variables
    $AlmP_stock = 0;
    $AlmP_id = $AlmP_array[$i]['AlmP_id'];

    // Sumando Entradas al STOCK
    $AlmP_stock_SQL = "SELECT * FROM AlmacenD INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id WHERE AlmDP_id = '$AlmP_id' AND AlmM_estado = 1";

    $AlmP_stock_IN = consultaData($AlmP_stock_SQL .  " AND AlmM_tipo = 0");
    foreach ($AlmP_stock_IN['dataFetch'] as $AlmP_stock_IN_row) {
      $AlmP_stock = $AlmP_stock + $AlmP_stock_IN_row['AlmD_cantidad'];
    }

    // Restando Salidas al STOCK
    $AlmP_stock_OUT = consultaData($AlmP_stock_SQL . " AND AlmM_tipo != 0");
    foreach ($AlmP_stock_OUT['dataFetch'] as $AlmP_stock_OUT_row) {
      $AlmP_stock = $AlmP_stock - $AlmP_stock_OUT_row['AlmD_cantidad'];
    }

    // Agregando stock al array del producto
    $AlmP_array[$i]['AlmP_stock'] = $AlmP_stock;
  }

  return ["numRows" => $AlmP_QRY['numRows'], "dataFetch" => $AlmP_array];
}

// ------------------------- Funcion para Traer Codigos Postales del TXT ----------------------- //
function arrayCP()
{
  $CP_id = 0;

  // Crea un array vacío
  $CP = [];
  // $CP = array();

  $arc = fopen(SERVERDIR . 'vista/assets/cp.txt', "r");
  while (! feof($arc)) {
    $CP_id++;


    list($cp_id, $d_asenta, $d_tipo_asenta, $D_mnpio, $d_estado, $d_ciudad, $d_CP, $c_estado, $c_oficina, $c_CP, $c_tipo_asenta, $id_asenta_cpcons, $d_zona, $c_cve_ciudad) = explode("|", fgets($arc));

    // // Crea instancias de tus objetos
    // $newCP = new stdClass(); // Usamos stdClass para un objeto genérico
    // $newCP->CP_id = $CP_id;
    // $newCP->CP_codigo = $cp_id;
    // $newCP->CP_asentamiento = $d_asenta;
    // $newCP->CP_tipo_asentamiento = $d_tipo_asenta;
    // $newCP->CP_municipio = $D_mnpio;
    // $newCP->CP_estado = $d_estado;
    // $newCP->CP_ciudad = $d_ciudad;
    // $newCP->CP_zona = $c_cve_ciudad;

    $otroCP = [
      "CP_id" => $CP_id,
      "CP_codigo" => $cp_id,
      "CP_asentamiento" => $d_asenta,
      "CP_tipo_asentamiento" => $d_tipo_asenta,
      "CP_municipio" => $D_mnpio,
      "CP_estado" => $d_estado,
      "CP_ciudad" => $d_ciudad,
      "CP_zona" => $c_cve_ciudad,
    ];

    // array_push($CP, array(
    //   "CP_id" => $CP_id,
    //   "CP_codigo" => $cp_id,
    //   "CP_asentamiento" => $d_asenta,
    //   "CP_tipo_asentamiento" => $d_tipo_asenta,
    //   "CP_municipio" => $D_mnpio,
    //   "CP_estado" => $d_estado,
    //   "CP_ciudad" => $d_ciudad,
    //   "CP_zona" => $c_cve_ciudad
    // ));



    array_push($CP, $otroCP);
    // $CP[] = $newCP;
  }
  fclose($arc);

  return $CP;
}

function accesoVerificacion($usuario_admin, $clave_admin)
{
  if ($usuario_admin == "") {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) No se ingreso ningun usuario. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else if (verificarDatos("^([a-z]{5})([0-9]{1})$", $usuario_admin)) {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un Error inesperado',
      'Texto' => '(TT _ TT) El nombre de usuario no tiene el formato solicitado. (TT _ TT)',
      'Tipo' => 'error'
    ];
    echo json_encode($alerta);
    exit();
  } else {
    if ($clave_admin == "") {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un Error inesperado',
        'Texto' => '(TT _ TT) No se ingreso ninguna clave. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    } else {
      $check_user = consultaData("SELECT usuario_usuario,usuario_clave FROM Usuarios WHERE (usuario_usuario = '$usuario_admin' AND  usuario_clave = '$clave_admin') AND (usuario_privilegio = 1)");
    }

    if ($check_user['numRows'] <= 0) {
      $alerta = [
        'Alerta' => 'simple',
        'Titulo' => 'Ocurrio un error',
        'Texto' => '(TT _ TT) Se requiere de Usuario con permisos. (TT _ TT)',
        'Tipo' => 'error'
      ];
      echo json_encode($alerta);
      exit();
    }
  }
}

/* --------------------- Funcion Obtener Contenido de la Vista --------------------- */
function getContenido($url)
{
  $vista = explode("/", $url);
  $redirect = SERVERURL . "Dash/";
  $whitelist = array(
    'ID',
    'Agregar',
    'Lista',
    'Otros',
    'Custom',
    'Rentass',
    'Entrada',
    'Entradas',
    'Salida',
    'Salidas',
    'Pagos',
    'Cobros',
    'Detalles',
    'Editar',
    'Equipos',
    'Toners',
    'Refacciones',
  );
  $listaAños = array(
    '2023',
    '2024',
    '2025',
  );
  $listaMeses = array(
    'Enero',
    'Febrero',
    'Marzo',
    'Abril',
    'Mayo',
    'Junio',
    'Julio',
    'Agosto',
    'Septiembre',
    'Octubre',
    'Noviembre',
    'Diciembre',
  );

  if (in_array($vista[1], $whitelist)) {
    if ($vista[1] == "Custom") {
      if (in_array($vista[3], $listaMeses) || in_array($vista[2], $listaAños)) {
        $listaMeses = array(
          'Enero' => 1,
          'Febrero' => 2,
          'Marzo' => 3,
          'Abril' => 4,
          'Mayo' => 5,
          'Junio' => 6,
          'Julio' => 7,
          'Agosto' => 8,
          'Septiembre' => 9,
          'Octubre' => 10,
          'Noviembre' => 11,
          'Diciembre' => 12,
        );
        $return = array(
          $vista[1],
          $vista[2],
          $listaMeses[$vista[3]]
        );
      } else {
        $return = redirect($redirect);
      }
    } else if ($vista[1] == "Detalles" || $vista[1] == "Editar" || $vista[1] == "ID") {
      if ($vista[2] == "") {
        $return = redirect($redirect);
      } else {
        $return = array(
          $vista[1],
          $vista[2]
        );
      }
    } else {
      $return = $vista[1];
    }
  } else {
    $return = redirect($redirect);
  }

  return $return;
} // Fin del la Funcion

function filtroCustom($pageCustom)
{
  // Datos maestros
  $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
  $anios = range(2020, 2030);

  // Obtenemos mes y año actual una sola vez para evitar llamar a date() en cada vuelta del ciclo
  $mesActualId = (int)date("n") - 1; // n devuelve 1-12, lo pasamos a índice 0-11
  $anioActual = (int)date("Y");

  // Iniciamos el buffer de salida
?>
  <div class="btn-group" role="group" aria-label="Filtro personalizado">
    <select class="mi-select" id="mesCustom">
      <?php foreach ($meses as $index => $mes): ?>
        <option value="<?= $mes ?>" <?= ($index === $mesActualId) ? 'selected' : '' ?>>
          <?= $mes ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select class="mi-select" id="anioCustom">
      <?php foreach ($anios as $anio): ?>
        <option value="<?= $anio ?>" <?= ($anio === $anioActual) ? 'selected' : '' ?>>
          <?= $anio ?>
        </option>
      <?php endforeach; ?>
    </select>

    <a href="#" id="buscarCustom" data-page="<?= $pageCustom ?>">
      <i class="fas fa-filter fa-fw"></i> &nbsp; FILTRAR
    </a>
  </div>
  <?php
}

function filtroCheck($pageCustom)
{
  $customMonth = array(
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre"
  );
  $customYear = range(2020, 2030);
  $selects = '
            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                <select class="mi-select" id="mesCustom">';
  for ($i = 0; $i < count($customMonth); $i++) {
    $selects .= '<option value="' . $customMonth[$i] . '" ';
    if ($customMonth[$i] == ucfirst(dateFormat(date("Y") . "-" . date("n") . "-" . date("d"), "mesL"))) {
      $selects .= "selected";
    }
    $selects .= '>' . $customMonth[$i] . '</option>';
  }
  $selects .= '</select>
                <select class="mi-select" id="anioCustom">';
  for ($i = 0; $i < count($customYear); $i++) {
    $selects .= '<option value="' . $customYear[$i] . '" ';
    if ($customYear[$i] == date("Y")) {
      $selects .= "selected";
    }
    $selects .= '>' . $customYear[$i] . '</option>';
  }
  $selects .= '</select>
        <a href="#" id="buscarCheck" data-page="Rentas"><i class="fas fa-filter fa-fw"></i> &nbsp; FILTRAR</a>
        </div>';
  echo $selects;
}

function customDMY($Pagina)
{
  $customDay = range(1, 31);
  $customMonth = array(
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre"
  );
  $customYear = range(2020, 2030);

  $filtro = '<div class="row">
              <div class="col">
                <div class="col btn-group btn-group-sm" role="group" aria-label="Basic mixed styles example">
                  <select class="mi-select" id="slctCustomDay">';
  for ($i = 0; $i < count($customDay); $i++) {
    if ($customDay[$i] == date("d")) {
      $selectedD = "selected";
    } else {
      $selectedD = "";
    }
    $filtro .= '<option value="' . $customDay[$i] . '" ' . $selectedD . '>' . $customDay[$i] . '</option>';
  }
  $filtro .= '
                  </select>
                  <a href="#" id="btnCustomDay" data-page="' . $Pagina . '"><i class="fas fa-filter fa-fw"></i> &nbsp; DIA</a>
                </div>
              </div>
              <div class="col">
                <div class="col btn-group btn-group-sm" role="group" aria-label="Basic mixed styles example">
                  <select class="mi-select" id="slctCustomMonth">';
  for ($i = 0; $i < count($customMonth); $i++) {
    if ($customMonth[$i] == ucfirst(dateFormat(date("Y") . "-" . date("n") . "-" . date("d"), "mesL"))) {
      $selectedM = "selected";
    } else {
      $selectedM = "";
    }
    $filtro .= '<option value="' . $customMonth[$i] . '" ' . $selectedM . '>' . $customMonth[$i] . '</option>';
  }
  $filtro .= '
                  </select>
                  <a href="#" id="btnCustomMonth" data-page="' . $Pagina . '"><i class="fas fa-filter fa-fw"></i> &nbsp; MES</a>
                </div>
              </div>
              <div class="col">
                <div class="col btn-group btn-group-sm" role="group" aria-label="Basic mixed styles example">
                  <select class="mi-select" id="slctCustomYear">';
  for ($i = 0; $i < count($customYear); $i++) {
    if ($customYear[$i] == date("Y")) {
      $selectedY = "selected";
    } else {
      $selectedY = "";
    }
    $filtro .= '<option value="' . $customYear[$i] . '" ' . $selectedY . '>' . $customYear[$i] . '</option>';
  }
  $filtro .= '
                  </select>
                  <a href="#" id="btnCustomYear" data-page="' . $Pagina . '"><i class="fas fa-filter fa-fw"></i> &nbsp; AÑO</a>
                </div>
              </div>
            </div>';
  return $filtro;
}

// --------------------- Funcion para Limpiar los Strings --------------------- //
function limpiarCadena($cadena)
{
  $cadena = trim($cadena);
  $cadena = stripslashes($cadena);
  $cadena = str_ireplace('<script>', '', $cadena);
  $cadena = str_ireplace('</script>', '', $cadena);
  $cadena = str_ireplace('<script src', '', $cadena);
  $cadena = str_ireplace('<script type=', '', $cadena);
  $cadena = str_ireplace('SELECT * FROM', '', $cadena);
  $cadena = str_ireplace('DELETE FROM', '', $cadena);
  $cadena = str_ireplace('INSERT INTO', '', $cadena);
  $cadena = str_ireplace('DROP TABLE', '', $cadena);
  $cadena = str_ireplace('DROP DATABASE', '', $cadena);
  $cadena = str_ireplace('TRUNCATE TABLE', '', $cadena);
  $cadena = str_ireplace('SHOW TABLES', '', $cadena);
  $cadena = str_ireplace('SHOW DATABASES', '', $cadena);
  $cadena = str_ireplace('<?php', '', $cadena);
  $cadena = str_ireplace('?>', '', $cadena);
  $cadena = str_ireplace('--', '', $cadena);
  $cadena = str_ireplace('>', '', $cadena);
  $cadena = str_ireplace('<', '', $cadena);
  $cadena = str_ireplace('[', '', $cadena);
  $cadena = str_ireplace(']', '', $cadena);
  $cadena = str_ireplace('^', '', $cadena);
  $cadena = str_ireplace('==', '', $cadena);
  $cadena = str_ireplace(';', '', $cadena);
  $cadena = str_ireplace('::', '', $cadena);
  $cadena = str_ireplace('|', '', $cadena);
  $cadena = stripslashes($cadena);
  $cadena = trim($cadena);

  return $cadena;
} // Fin de la Funcion


// --------------------- Funcion para verificar datos --------------------- //
function verificarDatos($filtro, $cadena)
{
  if (preg_match("/^" . $filtro . "$/", $cadena)) {
    return false;
  } else {
    return true;
  }
} // Fin de la Funcion


// --------------------- Funcion para ejecutar consultas de Datos --------------------- //
function consultaData1($consulta)
{

  $query = connect()->query($consulta);

  if (!empty($query) && mysqli_num_rows($query) == 0) {
    $result = [
      "numRows" => 0,
      "dataFetch" => "Sin resultados"
    ];
  }

  if (!empty($query) && mysqli_num_rows($query) > 0) {
    $numRows = mysqli_num_rows($query);
    while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
      $dataFetch[] = $row;
    }
    $result = [
      "numRows" => $numRows,
      "dataFetch" => $dataFetch
    ];
  }

  return $result;
} // Fin de la Funcion


function getPKfromTable($table)
{
  return consultaData("SELECT COLUMN_NAME
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
  WHERE TABLE_NAME = '$table'
  AND CONSTRAINT_NAME = 'PRIMARY'
  AND TABLE_SCHEMA = 'SystemCR_v3'");
}


// --------------------- Funcion conectar a la DB --------------------- //
function connect()
{
  $conexion = new mysqli(HOST, USER, PASS, DB);
  return $conexion;
} // Fin del la Funcion


// --------------------- Funcion para ejecutar consultas de Datos 2.0 --------------------- //
function consultaData($consulta)
{
  $sentenciaData = sentenciaData($consulta);

  $numRows = mysqli_num_rows($sentenciaData);
  $dataFetch = [];

  if ($numRows > 0) {
    while ($row = $sentenciaData->fetch_array(MYSQLI_ASSOC)) {
      $dataFetch[] = $row;
    }
  }

  $result = [
    "numRows" => $numRows,
    "dataFetch" => $dataFetch
  ];


  return $result;
} // Fin de la Funcion


// --------------------- Funcion para ejecutar Sentencias de Datos --------------------- //
function sentenciaData($sentencia)
{
  $result = connect()->query($sentencia);
  return $result;
} // Fin de la Funcion

// --------------------- Funcion para ejecutar Sentencias de Datos --------------------- //
// $sentencias = [
//   '(o _ O) No se pudo actualizar los niveles de toner. (o _ O)' => "UPDATE Equipos SET 
//     equipo_level_K = '$equipo_level_K', equipo_level_M = '$equipo_level_M', 
//     equipo_level_C = '$equipo_level_C', equipo_level_Y = '$equipo_level_Y', 
//     equipo_level_R = '$equipo_level_R' 
//     WHERE equipo_id = '$reporte_equipo_id'",
//   '(o _ O) No se pudo actualizar el Stock de la Renta. (o _ O)' => "UPDATE Rentas SET 
//     renta_stock_K = '$renta_stock_K', renta_stock_M = '$renta_stock_M', 
//     renta_stock_C = '$renta_stock_C', renta_stock_Y = '$renta_stock_Y', 
//     renta_stock_R = '$renta_stock_R' 
//     WHERE renta_id = '$reporte_renta_id'"
// ];
function transactionData($sentencias)
{
  $con = connect(); // Obtenemos la conexión

  // 1. Iniciamos la transacción
  $con->begin_transaction();

  try {
    foreach ($sentencias as $mensajeError => $sql) {
      // Ejecutamos cada sentencia
      $ejecucion = $con->query($sql);

      // Si la consulta falla (devuelve false) lanzamos una excepción
      if (!$ejecucion) {
        throw new Exception($mensajeError);
      }
    }

    // 2. Si todo salió bien, confirmamos los cambios en la DB
    $con->commit();
    return [
      "status" => true,
      "result" => "Operación realizada con éxito"
    ];
  } catch (Exception $e) {
    // 3. Si algo falló, deshacemos TODO lo anterior
    $con->rollback();
    return [
      "status" => false,
      "result" => $e->getMessage() // Devuelve tu mensaje personalizado
    ];
  }
}


// --------------------- Funcion para ejecutar Sentencias de Datos --------------------- //
function insertID($sentencia)
{
  $conexion = new mysqli(HOST, USER, PASS, DB);
  $status = $conexion->query($sentencia);
  $ID = $conexion->insert_id;

  if ($status) {
    $result = [
      "status" => $status,
      "ID" => $ID
    ];
  } else {
    $result = [
      "status" => $status,
      "ID" => 0
    ];
  }

  return $result;
} // Fin de la Funcion


// --------------------- Funcion para ejecutar Sentencias de Datos --------------------- //
function lastInsertID()
{
  $result = connect()->insert_id;
  return $result;
} // Fin de la Funcion


function dateFormat($date, $type)
{
  $fecha = new DateTime($date);

  $strftime = strftime(
    '%A-%e-%B-%m-%Y',
    $fecha->getTimestamp()
  );

  list($diaL, $diaN, $mesL, $mesN, $anio) = explode("-", $strftime);

  if ($type == "full") {
    $date = $diaL . " " . $diaN . " de " . $mesL . " de " . $anio; // sábado 31 de marzo de 1979
  } else if ($type == "numeros") {
    $date = $diaN . "-" . $mesN . "-" . $anio; // 31-03-1979
  } else if ($type == "simple") {
    $date = $diaN . "/" . $mesL . "/" . $anio; // 31/marzo/1979
  } else if ($type == "diames") {
    $date = $diaN . "/" . $mesL; // 31/marzo
  } else if ($type == "diaNmesLcorto") {
    $date = $diaN . " " . substr($mesL, 0, 3); // 31 mar
  } else if ($type == "mesanio") {
    $date = $mesL . " " . $anio; // marzo 1979
  } else if ($type == "mesLanio") {
    $date = $mesL . " " . $anio; // marzo 1979
  } else if ($type == "mesNanio") {
    $date = $mesN . " " . $anio; // 03 1979
  } else if ($type == "diaNmesL") {
    $date = $diaN . "/" . $mesL; // 31/marzo
  } else if ($type == "diaNmesN") {
    $date = $diaN . "/" . $mesN; // 31/03
  } else if ($type == "mesLanio") {
    $date = $mesL . "/" . $anio; // marzo/1979
  } else if ($type == "mesNanio") {
    $date = $mesN . "/" . $anio; // 03/1979
  } else if ($type == "diaL") {
    $date = $diaL; // marzo/1979
  } else if ($type == "diaN") {
    $date = $diaN; // 03/1979
  } else if ($type == "mesL") {
    $date = $mesL; // marzo/1979
  } else if ($type == "mesN") {
    $date = $mesN; // 03/1979
  } else if ($type == "anio") {
    $date = $anio; // 03/1979
  }

  return $date;
} // Fin de la Funcion


function dateCompare($date1, $type, $date2)
{
  $date1 = explode("-", $date1);
  $date2 = explode("-", $date2);

  $date1 = str_pad($date1[0], 4, '0', STR_PAD_LEFT) . str_pad($date1[1], 2, '0', STR_PAD_LEFT) . str_pad($date1[2], 2, '0', STR_PAD_LEFT);
  $date2 = str_pad($date2[0], 4, '0', STR_PAD_LEFT) . str_pad($date2[1], 2, '0', STR_PAD_LEFT) . str_pad($date2[2], 2, '0', STR_PAD_LEFT);

  if ($type == "igual") {
    $result = ($date1 == $date2) ? TRUE : FALSE;
  } else if ($type == "mayor") {
    $result = ($date1 > $date2) ? TRUE : FALSE;
  } else if ($type == "igualOmayor") {
    $result = ($date1 >= $date2) ? TRUE : FALSE;
  } else if ($type == "menor") {
    $result = ($date1 < $date2) ? TRUE : FALSE;
  } else if ($type == "igualOmenor") {
    $result = ($date1 <= $date2) ? TRUE : FALSE;
  }
  return $result;
};


function dateTimeCompare($date1, $type, $date2)
{
  list($date1Date, $date1Time) = explode("T", $date1);
  $date1Date = explode("-", $date1Date);
  $date1Time = explode(":", $date1Time);
  $date1Date = str_pad($date1Date[0], 4, '0', STR_PAD_LEFT) . str_pad($date1Date[1], 2, '0', STR_PAD_LEFT) . str_pad($date1Date[2], 2, '0', STR_PAD_LEFT);
  $date1Time = str_pad($date1Time[0], 2, '0', STR_PAD_LEFT) . str_pad($date1Time[1], 2, '0', STR_PAD_LEFT);
  $dateTime1 = $date1Date . $date1Time;

  list($date2Date, $date2Time) = explode("T", $date2);
  $date2Date = explode("-", $date2Date);
  $date2Date = str_pad($date2Date[0], 4, '0', STR_PAD_LEFT) . str_pad($date2Date[1], 2, '0', STR_PAD_LEFT) . str_pad($date2Date[2], 2, '0', STR_PAD_LEFT);
  $date2Time = explode(":", $date2Time);
  $date2Time = str_pad($date2Time[0], 2, '0', STR_PAD_LEFT) . str_pad($date2Time[1], 2, '0', STR_PAD_LEFT);
  $dateTime2 = $date2Date . $date2Time;

  if ($type == "igual") {
    $result = ($dateTime1 == $dateTime2) ? TRUE : FALSE;
  } else if ($type == "mayor") {
    $result = ($dateTime1 > $dateTime2) ? TRUE : FALSE;
  } else if ($type == "igualOmayor") {
    $result = ($dateTime1 >= $dateTime2) ? TRUE : FALSE;
  } else if ($type == "menor") {
    $result = ($dateTime1 < $dateTime2) ? TRUE : FALSE;
  } else if ($type == "igualOmenor") {
    $result = ($dateTime1 <= $dateTime2) ? TRUE : FALSE;
  }
  return $result;
};


function diasDelMes($anio, $mes)
{
  return cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
}


// --------------------- Funcion para Cerrar Sesion ------------------------- //
function logOutSession()
{
  session_start();
  $usuario = decryption($_POST['usuario']);

  if ($usuario == $_SESSION['usuario']) {
    session_unset();
    session_destroy();
    $alerta = [
      "Alerta" => "redireccionar",
      "URL" => SERVERURL . "Login/"
    ];
  } else {
    $alerta = [
      'Alerta' => 'simple',
      'Titulo' => 'Ocurrio un error',
      'Texto' => 'No es posble cerrar la session.',
      'Tipo' => 'error'
    ];
  }
  echo json_encode($alerta);
} // Fin de la Funcion


// --------------------- Funcion para Iniciar Sesion -------------------------- //
function logInSession()
{
  $usuario = limpiarCadena($_POST['usuario_log']);
  $clave = limpiarCadena($_POST['clave_log']);

  // ------------------- Funcion para Comprobar Campos Vacios ------------------- //
  if ($usuario == "") {
    echo '
          <script>
              Swal.fire({
                  title: "Ocurrio un Error inesperado",
                  text: "No se ingreso ningun usuario.",
                  icon: "error",
                  confirmButtonText: "Aceptar"
              });
          </script>
          ';
    exit();
  }

  if ($clave == "") {
    echo '
          <script>
              Swal.fire({
                  title: "Ocurrio un Error inesperado",
                  text: "No se ingreso ninguna clave.",
                  icon: "error",
                  confirmButtonText: "Aceptar"
              });
          </script>
          ';
    exit();
  }

  // ------------------- Funcion para Comprobar Campos Vacios ------------------- //
  if (verificarDatos("^([a-z]{5})([0-9]{1})$", $usuario)) {
    echo '
          <script>
              Swal.fire({
                  title: "Ocurrio un Error inesperado",
                  text: "El nombre de usuario no tiene el formato solicitado.",
                  icon: "error",
                  confirmButtonText: "Aceptar"
              });
          </script>
          ';
    exit();
  } else if (verificarDatos("^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$", $clave)) {
    echo '
          <script>
              Swal.fire({
                  title: "Ocurrio un Error inesperado",
                  text: "La clave no tiene el formato solicitado.",
                  icon: "error",
                  confirmButtonText: "Aceptar"
              });
          </script>
          ';
    exit();
  } else {
    $clave = encryption($clave);
  }

  $check1SQL = "SELECT * FROM Usuarios WHERE usuario_usuario = '$usuario'";
  $check2SQL = $check1SQL . " AND usuario_clave = '$clave'";
  $check3SQL = $check2SQL . " AND usuario_estado = 'Activo'";

  if (consultaData($check1SQL)['numRows'] == 0) {
    echo '
          <script>
              Swal.fire({
                  title: "Ocurrio un Error inesperado",
                  text: "El Usuario No Existe.",
                  icon: "error",
                  confirmButtonText: "Aceptar"
              });
          </script>
          ';
    exit();
  } else if (consultaData($check2SQL)['numRows'] == 0) {
    echo '
          <script>
              Swal.fire({
                  title: "Ocurrio un Error inesperado",
                  text: "La clave No es la correcta.",
                  icon: "error",
                  confirmButtonText: "Aceptar"
              });
          </script>
          ';
    exit();
  } else if (consultaData($check3SQL)['numRows'] == 0) {
    echo '
          <script>
              Swal.fire({
                  title: "Ocurrio un Error inesperado",
                  text: "El usuario no esta activo.",
                  icon: "error",
                  confirmButtonText: "Aceptar"
              });
          </script>
          ';
    exit();
  } else {
    $userData = consultaData($check3SQL)['dataFetch'][0];
    $usuario_id = $userData['usuario_id'];
    $usuario_nombre = $userData['usuario_nombre'];
    $usuario_apellido = $userData['usuario_apellido'];
    $usuario_telefono = $userData['usuario_telefono'];
    $usuario_direccion = $userData['usuario_direccion'];
    $usuario_email = $userData['usuario_email'];
    $usuario_usuario = $userData['usuario_usuario'];
    $usuario_clave = $userData['usuario_clave'];
    $usuario_privilegio = $userData['usuario_privilegio'];
    $usuario_navbarStatus = $userData['usuario_navbarStatus'];
  }

  if (sentenciaData("INSERT INTO LogReg (logReg_usuario_id) VALUES ('$usuario_id')")) {

    session_start();

    $_SESSION['id'] = $usuario_id;
    $_SESSION['nombre'] = $usuario_nombre;
    $_SESSION['apellido'] = $usuario_apellido;
    $_SESSION['telefono'] = $usuario_telefono;
    $_SESSION['direccion'] = $usuario_direccion;
    $_SESSION['email'] = $usuario_email;
    $_SESSION['usuario'] = $usuario_usuario;
    $_SESSION['passclave'] = $usuario_clave;
    $_SESSION['privilegio'] = $usuario_privilegio;

    if ($usuario_navbarStatus == 1) {
      $_SESSION['navbarStatus'] = "show";
      $_SESSION['navbarBtn'] = "<i class='far fa-window-close'> &nbsp; OCULTAR</i>";
    } else {
      $_SESSION['navbarStatus'] = "";
      $_SESSION['navbarBtn'] = "<i class='far fa-check-square'> &nbsp; MANTENER</i></i>";
    }

    $_SESSION['mes'] = date("m");
    $_SESSION['anio'] = date("Y");

    if (headers_sent()) {
      return '
            <script>
                window.location.href="' . SERVERURL . 'Dash/";
            </script>
            ';
    } else {
      return header('Location: ' . SERVERURL . 'Dash/');
    }
  } else {
    echo '
          <script>
              Swal.fire({
                  title: "Ocurrio un Error inesperado",
                  text: "No se pudo realizar el registro de ingreso.",
                  icon: "error",
                  confirmButtonText: "Aceptar"
              });
          </script>
          ';
    exit();
  }
} // Fin de la Funcion


// --------------------- Funcion Forzar Cierre de Sesion -------------------- //
function forceOutSession()
{
  session_unset();
  session_destroy();
  if (headers_sent()) {
    return '
            <script>
                window.location.href="' . SERVERURL . 'Login/";
            </script>
            ';
  } else {
    return header("Location: " . SERVERURL . "Login/");
  }
} // Fin del la Funcion


// --------------------- Funcion para encriptar cadena --------------------- //
function encryption($string)
{
  $output = FALSE;
  $key = hash('sha256', SECRET_KEY);
  $iv = substr(hash('sha256', SECRET_IV), 0, 16);
  $output = openssl_encrypt($string, METHOD, $key, 0, $iv);
  $output = base64_encode($output);
  return $output;
} // Fin del la Funcion


// --------------------- Funcion para desencriptar cadena --------------------- //
function decryption($string)
{
  $key = hash('sha256', SECRET_KEY);
  $iv = substr(hash('sha256', SECRET_IV), 0, 16);
  $output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
  return $output;
} // Fin del la Funcion


/* --------------------- Funcion Obtener Contenido de la Vista --------------------- */
function showDoc($tipoDoc, $idReg)
{
  $idRegDecryp = decryption($idReg);


  if ($tipoDoc == "Lecturas") {
    $queryReg = "SELECT * FROM Lecturas WHERE lectura_id = '$idRegDecryp'";
  }

  $resultReg = consultaData($queryReg);
  $nameDoc = $resultReg['dataFetch'][0]['lectura_pdf'];

  $fechaDoc = explode(" - ", $nameDoc);
  list($diaDoc, $mesDoc, $anioDoc) = explode("-", $fechaDoc[0]);

  if ($tipoDoc == "Lecturas") {
    $dirDoc = SERVERDIR . "DocsCR/" . $tipoDoc . "/" . $anioDoc . "/" . $mesDoc . "/" . $nameDoc;
    $btnDoc = '<button value="' . $idReg . '" class="btn btn-success lectura_pdf">Abrir Lectura</button>';
  }

  if (file_exists($dirDoc)) {
    $result = $btnDoc;
  } else {
    $result = '<b>No hay Archivo en el Servidor</b>';
  }


  return $result;
} // Fin del la Funcion


/* --------------------- Funcion Guardar Archivos PDF --------------------- */
function saveDoc($tipoDoc, $tempDir, $nameDoc, $fecha_anio, $fecha_mes, $fecha_dia)
{
  $docDir = SERVERDIR . 'DocsCR/';
  // +======+ Carpeta Archivos +======+ //
  if (!file_exists($docDir)) {
    mkdir($docDir, 0755, true);
  }
  // +======+======+======+======+ Verificacion de Campos Obligatorios +======+======+======+======+======+ //
  if ($tipoDoc == "LecturaPDF") {
    $docDir .= 'Lecturas/PDF/';
    $tipo = 'Toma de Lectura.pdf';
  }
  if ($tipoDoc == "Lectura") {
    $docDir .= 'Lecturas/';
    $tipo = 'Toma de Lectura.jpg';
  }
  if ($tipoDoc == "retiroEqu") {
    $docDir .= 'RetirosDeEquipos/';
    $tipo = 'Retiro De Equipo.pdf';
  }
  if ($tipoDoc == "cambioEqu") {
    $docDir .= 'CambiosDeEquipos/';
    $tipo = 'Cambio De Equipo.pdf';
  }

  // +======+======+======+======+ Verificacion de Existencia de las carpetas +======+======+======+======+======+ //
  // +======+ Verificar Carpeta Archivos +======+ //
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
  }

  $archivo = $fecha_dia . '-' . $fecha_mes . '-' . $fecha_anio . ' - ' . $nameDoc . ' - ' . $tipo;

  // +======+======+======+======+ Movemos el Archivo a la nueva ubicacion +======+======+======+======+======+ //
  if (file_exists($docDir . $archivo)) {
    $result = [
      'status' => false,
      'result' => 'El archivo ya existe'
    ];
  } else if (move_uploaded_file($tempDir, $docDir . $archivo)) {
    $result = [
      'status' => true,
      'result' => $archivo
    ];
  } else {
    $result = [
      'status' => false,
      'result' => 'No se pudo guardar el archivo'
    ];
    if ($tipoDoc == "LecturaPDF") {
      $result = [
        'status' => false,
        'result' => 'No se pudo guardar el archivo PDF'
      ];
    }
  }

  return $result;
} // Fin del la Funcion


/* --------------------- Funcion Contenido No valido --------------------- */
function redirect($dir)
{
  if (headers_sent()) { ?>
    <script>
      window.location.href = "<?= $dir; ?>";
    </script>
<?php
  } else {
    $respuesta = header("Location: " . $dir);
  }
  return $respuesta;
} // Fin del la Funcion
