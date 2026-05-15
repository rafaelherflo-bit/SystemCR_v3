<?php
$pagina = explode("/", $_GET['vista']);
$redirect = SERVERURL . "Dash/";
$whitelist = array(
  'Agregar',
  'Lista',
  'Vencidos',
  'Productos',
  'idE',
  'idD',
);


if (in_array($GLOBALS['pagina1'], $whitelist)) {

  if ($GLOBALS['pagina1'] == "idD" || $GLOBALS['pagina1'] == "idE") {
    if ($GLOBALS['pagina2'] == "") {
      $contenido = redirect($redirect);
    } else {
      $contenido = array(
        $GLOBALS['pagina1'],
        $GLOBALS['pagina2']
      );
    }
  } else if (!isset($GLOBALS['pagina2'])) {
    $contenido = $GLOBALS['pagina1'];
  } else {
    $contenido = redirect($redirect);
  }
} else {
  $contenido = redirect($redirect);
}

if ($contenido[0] == 'idD') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido[0] . ".php";
} else if ($contenido[0] == 'idE') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido[0] . ".php";
} else if ($contenido == 'Agregar') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido . ".php";
} else if ($contenido == 'Lista') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido . ".php";
} else if ($contenido == 'Vencidos') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido . ".php";
} else if ($contenido == 'Productos') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
