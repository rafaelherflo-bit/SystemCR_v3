<?php
$redirect = SERVERURL . "Almacen/Proveedores/Lista";
$whiteList = array(
  'Movimientos',
  'Toners',
  'Chips',
  'Refacciones',
  'Servicios',
  'Otros',
  'Equipos',
  'Proveedores',
);
$listaDias = range(1, 31);
$listaAños = range(2020, 2030);
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

//  PAGINA[0]  /   PAGINA[1]   /  PAGINA[2]  /  PAGINA[3]  /  PAGINA[4]  /  PAGINA[5]
//  Almacen    /  Movimientos  /  CustomDay  /    2025     /   Octubre   /     29

$showContent = FALSE;
if (in_array($GLOBALS['pagina1'], $whiteList)) {
  if ($GLOBALS['pagina2'] == "CustomDay" || $GLOBALS['pagina2'] == "CustomMonth" || $GLOBALS['pagina2'] == "CustomYear") {
    if ($GLOBALS['pagina2'] == "CustomDay" && (((isset($GLOBALS['pagina3']) && $GLOBALS['pagina3'] != "") && in_array($GLOBALS['pagina3'], $listaAños)) && ((isset($GLOBALS['pagina4']) && $GLOBALS['pagina4'] != "")  && array_key_exists($GLOBALS['pagina4'], $listaMeses)) && ((isset($GLOBALS['pagina5']) && $GLOBALS['pagina5'] != "")  && in_array($GLOBALS['pagina5'], $listaDias)) && !isset($GLOBALS['pagina6']))) {
      $showContent = TRUE;
    } else if ($GLOBALS['pagina2'] == "CustomMonth" && (((isset($GLOBALS['pagina3']) && $GLOBALS['pagina3'] != "") && in_array($GLOBALS['pagina3'], $listaAños)) && ((isset($GLOBALS['pagina4']) && $GLOBALS['pagina4'] != "")  && array_key_exists($GLOBALS['pagina4'], $listaMeses)) && !isset($GLOBALS['pagina5']))) {
      $showContent = TRUE;
    } else if ($GLOBALS['pagina2'] == "CustomYear" && (((isset($GLOBALS['pagina3']) && $GLOBALS['pagina3'] != "") && in_array($GLOBALS['pagina3'], $listaAños)) && !isset($GLOBALS['pagina4']))) {
      $showContent = TRUE;
    }
  } else if (($GLOBALS['pagina2'] == "Lista" || $GLOBALS['pagina2'] == "Agregar") && !isset($GLOBALS['pagina3'])) {
    $showContent = TRUE;
  } else if (($GLOBALS['pagina2'] == "Editar" || $GLOBALS['pagina2'] == "Detalles") && isset($GLOBALS['pagina3']) && !isset($GLOBALS['pagina4'])) {
    $showContent = TRUE;
  }
}

if ($showContent && file_exists(SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $GLOBALS['pagina1'] . "/" . $GLOBALS['pagina2'] . ".php")) {
  echo '<input id="SysVista" type="hidden" data-vista="' . $GLOBALS['pagina0'] . '" data-cont="' . $GLOBALS['pagina1'] . '" data-subcont="' . $GLOBALS['pagina2'] . '">';
  $LINKS = [
    ["MOVIMIENTOS", SERVERURL . 'Almacen/Movimientos/CustomMonth/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")), "fas fa-dolly"],
    ["TONERS", SERVERURL . "Almacen/Toners/Lista", "fas fa-spray-can"],
    ["REFACCIONES", SERVERURL . "Almacen/Refacciones/Lista", "fas fa-toolbox"],
    ["CHIPS", SERVERURL . "Almacen/Chips/Lista", "fas fa-microchip"],
    ["SERVICIOS", SERVERURL . "Almacen/Servicios/Lista", "fas fa-concierge-bell"],
    ["OTROS", SERVERURL . "Almacen/Otros/Lista", "fas fa-satellite"],
    ["EQUIPOS", SERVERURL . "Almacen/Equipos/Lista", "fas fa-print"],
    ["PROVEEDORES", SERVERURL . "Almacen/Proveedores/Lista", "fas fa-parachute-box"]
  ];
  $subContNav = '
<div class="container-fluid d-flex justify-content-center my-4">
    <ul class="list-unstyled d-flex flex-wrap justify-content-center align-items-center gap-3" style="margin-bottom: 0;">';

  // Botón "AGREGAR REGISTRO"
  $paginasLista = ["Lista", "CustomDay", "CustomMonth", "CustomYear"];
  if (in_array($GLOBALS['pagina2'], $paginasLista)) {
    if ($GLOBALS['pagina1'] != "Equipos") {
      $subContNav .= '<li class="nav-item">
                            <a class="btn btn-secondary shadow-sm" href="' . SERVERURL . 'Almacen/' . $GLOBALS['pagina1'] . '/Agregar">
                                <i class="fas fa-plus fa-fw"></i> &nbsp;AGREGAR REGISTRO
                            </a>
                        </li>';
    }
  }

  // Generación de Links Dinámicos
  for ($i = 0; $i < count($LINKS); $i++) {
    $linkNombre = $LINKS[$i][0];
    $linkUrl = $LINKS[$i][1];
    $linkIcono = $LINKS[$i][2];

    // Condición de visibilidad
    // if ($linkNombre != strtoupper($GLOBALS['pagina1']) || in_array($GLOBALS['pagina2'], ["Agregar", "Editar", "Detalles"])) {

    $subContNav .= '<li class="nav-item">';

    if (in_array($linkNombre, ["TONERS", "REFACCIONES", "CHIPS"])) {
      // Grupo de botones con PDF unido
      $subContNav .= '
                <div class="btn-group shadow-sm" role="group">
                    <a class="btn btn-secondary" href="' . $linkUrl . '">
                        <i class="' . $linkIcono . '"></i> &nbsp;' . $linkNombre . '
                    </a>
                    <button type="button" class="btn btn-danger btn-PDF" data-PDF="' . $linkNombre . '">
                        <i class="fas fa-file-pdf fa-fw"></i>
                    </button>
                </div>';
    } else {
      // Botón simple estilizado
      $subContNav .= '
                <a class="btn btn-secondary shadow-sm" href="' . $linkUrl . '">
                    <i class="' . $linkIcono . '"></i> &nbsp;' . $linkNombre . '
                </a>';
    }

    $subContNav .= '</li>';
    // }
  }

  $subContNav .= '</ul>
              </div>';
  echo $subContNav;

  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $GLOBALS['pagina1'] . "/" . $GLOBALS['pagina2'] . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
