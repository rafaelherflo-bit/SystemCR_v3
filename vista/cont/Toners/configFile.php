<?php

$redirect = SERVERURL . "Dash/";

$whitelist = array(
  'Entrada',
  'Entradas',
  'Salida',
  'Salidas',
  'Lista',
  'RST',
  'RET',
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


if (in_array($pagina[1], $whitelist)) {

  if ($pagina[1] == "CustomDay") {
    if ($vista[2] != "" && in_array($pagina[2], $listaAños) && $pagina[3] != ""  && in_array($pagina[3], $listaMeses) && $pagina[4] != ""  && in_array($pagina[4], $listaDias)) {
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
        $pagina[1],
        $pagina[2],
        $listaMeses[$pagina[3]],
        $pagina[4]
      );
    } else {
      redirect($redirect);
    }
  } else if ($pagina[1] == "CustomMonth") {
    if ($pagina[2] != "" && in_array($pagina[2], $listaAños) && $pagina[3] != ""  && in_array($pagina[3], $listaMeses)) {
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
        $pagina[1],
        $pagina[2],
        $listaMeses[$pagina[3]]
      );
    } else {
      redirect($redirect);
    }
  } else if ($pagina[1] == "CustomYear") {
    if ($pagina[2] != "" && !isset($pagina[3]) && in_array($pagina[2], $listaAños)) {
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
        $pagina[1],
        $pagina[2]
      );
    } else {
      redirect($redirect);
    }
  } else if ((($pagina[1] == "RST" || $pagina[1] == "RET") && isset($pagina[2]) && $pagina[2] != "")) {
    $contenido = array(
      $pagina[1],
      $pagina[2]
    );
  } else if (($pagina[1] == "Entrada" || $pagina[1] == "Entradas" || $pagina[1] == "Salida" || $pagina[1] == "Salidas" || $pagina[1] == "Lista") && !isset($pagina[2])) {
    $contenido = $pagina[1];
  } else {
    redirect($redirect);
  }
} else {
  redirect($redirect);
}
if ($contenido == 'Lista') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else if ($contenido == 'Entrada') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else if ($contenido == 'Salida') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else if ($contenido == 'Entradas') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else if ($contenido == 'Salidas') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else if ($contenido[0] == 'RST') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido[0] . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
