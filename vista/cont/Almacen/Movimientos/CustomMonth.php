<?php
$anio = $pagina[3];
$mes = $listaMeses[$pagina[4]];
$dia = diasDelMes($anio, $mes);
$currFecha = $anio . "-" . $mes . "-" . $dia;

$QRY = consultaData("SELECT * FROM AlmacenM WHERE MONTH(AlmM_fecha) = $mes AND YEAR(AlmM_fecha) = $anio");
?>

<input type="hidden" id="AlmMovDia" value="<?= $dia; ?>">
<input type="hidden" id="AlmMovMes" value="<?= $mes; ?>">
<input type="hidden" id="AlmMovAnio" value="<?= $anio; ?>">
<input type="hidden" id="AlmMovPeriodo" value="<?= ucfirst(dateFormat($currFecha, "mesanio")); ?>">

<div class="container-fluid">
  <center>
    <h3><i class="fas fa-clipboard-list fa-fw"></i>&nbsp;<?= strtoupper("FECHA: " . dateFormat($currFecha, "mesanio")); ?></h3>
  </center>
</div>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <?= customDMY($pagina[0] . "/" . $pagina[1]); ?>
    </li>
  </ul>
</div>

<?php
require_once SERVERDIR . "vista/cont/Almacen/Movimientos/tabla.php";
?>