<?php
$anio = $contenido[1];
$sqlRepMY = "SELECT * FROM Reportes
INNER JOIN Rentas ON Reportes.reporte_renta_id = Rentas.renta_id
INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
WHERE reporte_estado = 1
AND YEAR(reporte_fecha) = $anio";
$queryReportes = consultaData($sqlRepMY); ?>

<input type="hidden" id="custom_anio" value="<?= $anio; ?>">
<input type="hidden" id="periodoCustom" value="<?= ucfirst(dateFormat($anio . "-" . date("n") . "-" . date("d"), "anio")); ?>">

<div class="container-fluid">
  <center>
    <h3><?= strtoupper(dateFormat($anio . "-" . date("n") . "-" . date("d"), "anio")); ?></h3>
  </center>
</div>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL . $pagina[0]; ?>/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <a href="<?= SERVERURL . $pagina[0]; ?>/Iniciar"><i class="fas fa-play fa-fw"></i> &nbsp; INICIAR</a>
    </li>
    <li>
      <a href="<?= SERVERURL . $pagina[0]; ?>/Activos"><i class="fas fa-hourglass fa-fw"></i> &nbsp; <b><?= consultaData("SELECT * FROM Reportes WHERE reporte_estado = 0")['numRows']; ?></b> &nbsp; ACTIVOS</a>
    </li>
    <li>
      <?= customDMY($pagina[0]); ?>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <center>
    <div class="row">
      <?php
      $queryZonas = consultaData("SELECT * FROM Zonas");
      foreach ($queryZonas['dataFetch'] as $zona) {
        $sqlRentCount = "SELECT * FROM Reportes
                            INNER JOIN Rentas ON Reportes.reporte_renta_id = Rentas.renta_id
                          WHERE YEAR(reporte_fecha) = $anio
                          AND renta_zona_id = '" . $zona['zona_id'] . "'";
        $sqlRentCount = consultaData($sqlRentCount)['numRows'];
        echo "<div class='border shadow-sm form-neon mb-3 col mx-1 zona_id' data-tipo='3' data-anio='" . $anio . "' id='" . $zona['zona_id'] . "'>";
        echo ($sqlRentCount == 0) ? "No hay registros en " . $zona['zona_nombre'] : $sqlRentCount . " en " . $zona['zona_nombre'];
        echo "</div>";
      }
      ?>
    </div>
  </center>
</div>

<?php
require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/CusDMYTabla.php";
?>