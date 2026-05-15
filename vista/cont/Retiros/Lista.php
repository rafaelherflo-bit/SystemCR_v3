<?php
$SQLretiros = "SELECT * FROM Retiros
              INNER JOIN Rentas ON Retiros.retiro_renta_id = Rentas.renta_id
              INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
              INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
              INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
              INNER JOIN Equipos ON Retiros.retiro_equipo_id = Equipos.equipo_id
              INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id";
$query = consultaData($SQLretiros); ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?php echo SERVERURL; ?>Retiros/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CANCELACION</a>
    </li>
    <li>
      <a class="active"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE CANCELACIONES</a>
    </li>
    <li>
      <?php filtroCustom("Retiros"); ?>
    </li>
  </ul>
</div>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>FECHA</th>
        <th>SERVICIO</th>
        <th>MOTIVO</th>
        <th>COMENTARIOS</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($query['numRows'] != 0) {
        foreach ($query['dataFetch'] as $row) { ?>
          <tr>
            <td><?= ucfirst(dateFormat($row['retiro_fecha'], "completa")); ?></td>
            <td><?= $row['cliente_rs'] . " (" . $row['cliente_rfc'] . ") | " . $row['contrato_folio'] . "-" . $row['renta_folio'] . "<br>Zona: " . $row['zona_nombre']; ?></td>
            <td><?= $row['retiro_motivo']; ?></td>
            <td><?= $row['retiro_comm']; ?></td>
          </tr>
      <?php }
      } ?>
    </tbody>
  </table>
</div>