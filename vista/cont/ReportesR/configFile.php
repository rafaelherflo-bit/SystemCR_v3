<?php
$redirect = SERVERURL . "Dash/";
$whitelist = array(
  'idRC',
  'idRAedit',
  'idRCedit',
  'Agregar',
  'Iniciar',
  'Activos',
  'CustomDay',
  'CustomMonth',
  'CustomYear',
);
$listaDias = range(1, 31);
$listaAños = range(2020, 2030);
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


if (in_array($GLOBALS['pagina1'], $whitelist)) {

  if ($GLOBALS['pagina1'] == "CustomDay") {
    if ($GLOBALS['pagina2'] != "" && in_array($GLOBALS['pagina2'], $listaAños) && $GLOBALS['pagina3'] != ""  && in_array($GLOBALS['pagina3'], $listaMeses) && $GLOBALS['pagina4'] != ""  && in_array($GLOBALS['pagina4'], $listaDias)) {
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
      $contenido = array(
        $GLOBALS['pagina1'],
        $GLOBALS['pagina2'],
        $listaMeses[$GLOBALS['pagina3']],
        $GLOBALS['pagina4']
      );
    } else {
      redirect($redirect);
    }
  } else if ($GLOBALS['pagina1'] == "CustomMonth") {
    if ($GLOBALS['pagina2'] != "" && in_array($GLOBALS['pagina2'], $listaAños) && $GLOBALS['pagina3'] != ""  && in_array($GLOBALS['pagina3'], $listaMeses)) {
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
      $contenido = array(
        $GLOBALS['pagina1'],
        $GLOBALS['pagina2'],
        $listaMeses[$GLOBALS['pagina3']]
      );
    } else {
      redirect($redirect);
    }
  } else if ($GLOBALS['pagina1'] == "CustomYear") {
    if ($GLOBALS['pagina2'] != "" && !isset($GLOBALS['pagina3']) && in_array($GLOBALS['pagina2'], $listaAños)) {
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
      $contenido = array(
        $GLOBALS['pagina1'],
        $GLOBALS['pagina2']
      );
    } else {
      redirect($redirect);
    }
  } else if (($GLOBALS['pagina1'] == "Agregar" || $GLOBALS['pagina1'] == "Iniciar" || $GLOBALS['pagina1'] == "Activos") && !isset($GLOBALS['pagina2'])) {
    $contenido = $GLOBALS['pagina1'];
  } else if (($GLOBALS['pagina1'] == "idRCedit" && isset($GLOBALS['pagina2']) && $GLOBALS['pagina2'] != "") || ($GLOBALS['pagina1'] == "idRAedit" && isset($GLOBALS['pagina2']) && $GLOBALS['pagina2'] != "") || ($GLOBALS['pagina1'] == "idRC" && isset($GLOBALS['pagina2']) && $GLOBALS['pagina2'] != "")) {
    $contenido = array(
      $GLOBALS['pagina1'],
      $GLOBALS['pagina2']
    );
  } else {
    redirect($redirect);
  }
} else {
  redirect($redirect);
}

if ($contenido[0] == 'CustomDay') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido[0] . ".php";
} else if ($contenido[0] == 'CustomMonth') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido[0] . ".php";
} else if ($contenido[0] == 'CustomYear') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido[0] . ".php";
} else if ($contenido[0] == 'idRCedit') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido[0] . ".php";
} else if ($contenido[0] == 'idRAedit') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido[0] . ".php";
} else if ($contenido[0] == 'idRC') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido[0] . ".php";
} else if ($contenido == 'Agregar') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido . ".php";
} else if ($contenido == 'Iniciar') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido . ".php";
} else if ($contenido == 'Activos') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $contenido . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
