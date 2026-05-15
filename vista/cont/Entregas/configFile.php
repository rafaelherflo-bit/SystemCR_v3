<?php
$vista = getContenido($_GET['vista']);

if ($vista == 'Lista') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $GLOBALS['pagina1'] . ".php";
} else  if ($vista == 'Agregar') {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $GLOBALS['pagina1'] . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
