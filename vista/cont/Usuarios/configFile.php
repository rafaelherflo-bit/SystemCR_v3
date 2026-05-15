<?php
if ($_SESSION['id'] != 1) {
  redirect(SERVERURL . 'Dash/');
}

$contenido = getContenido($_GET['vista']);

if ($contenido == 'Lista') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else  if ($contenido == 'Agregar') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else  if ($contenido == 'Actualizar') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
