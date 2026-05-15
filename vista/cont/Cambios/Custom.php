<?php
$SQL = "SELECT
        Cambios.cambio_id,
        Cambios.cambio_fecha,
        Cambios.cambio_folio,
        Cambios.cambio_motivo,
        Cambios.cambio_comm,
        Clientes.cliente_rs,
        Clientes.cliente_rfc,
        Contratos.contrato_folio,
        Rentas.renta_folio,
        Rentas.renta_depto,
        Zonas.zona_nombre,
        EquiposIng.equipo_serie AS equipoIng_serie,
        EquiposIng.equipo_codigo AS equipoIng_codigo,
        EquiposIng.equipo_modelo_id AS equipoIng_modelo_id,
        ModelosIng.modelo_modelo AS modeloIng_modelo,
        ModelosIng.modelo_linea AS modeloIng_linea,
        EquiposRet.equipo_serie AS equipoRet_serie,
        EquiposRet.equipo_codigo AS equipoRet_codigo,
        EquiposRet.equipo_modelo_id AS equipoRet_modelo_id,
        ModelosRet.modelo_modelo AS modeloRet_modelo,
        ModelosRet.modelo_linea AS modeloRet_linea
        FROM Cambios
        INNER JOIN Rentas ON Cambios.cambio_renta_id = Rentas.renta_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        INNER JOIN Equipos AS EquiposIng ON Cambios.cambio_equipoIng_id = EquiposIng.equipo_id
        INNER JOIN Modelos AS ModelosIng ON EquiposIng.equipo_modelo_id = ModelosIng.modelo_id
        INNER JOIN Equipos AS EquiposRet ON Cambios.cambio_equipoRet_id = EquiposRet.equipo_id
        INNER JOIN Modelos AS ModelosRet ON EquiposRet.equipo_modelo_id = ModelosRet.modelo_id
        WHERE MONTH(cambio_fecha) = $contenido[2]
        AND YEAR(cambio_fecha) = $contenido[1]";
$query = consultaData($SQL); ?>
<input type="hidden" id="custom_anio" value="<?= $contenido[1]; ?>">
<input type="hidden" id="custom_mes" value="<?= $contenido[2]; ?>">
<div class="container-fluid">
  <center>
    <h3><?= ucfirst(dateFormat($contenido[1] . "-" . $contenido[2] . "-" . date("d"), "mesanio")); ?></h3>
  </center>
</div>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Cambios/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <!-- <li>
      <a href="<?= SERVERURL; ?>Cambios/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; TODAS</a>
    </li> -->
    <li>
      <?php filtroCustom($pagina[0]); ?>
    </li>
  </ul>
</div>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>
          <center>FOLIO CAMBIO</center>
        </th>
        <th>
          <center>RENTA</center>
        </th>
        <th>
          <center>ZONA</center>
        </th>
        <th>
          <center>DIA DE LECTURA</center>
        </th>
        <th>
          <center>EQUIPO ACTUAL</center>
        </th>
        <th>
          <center>EQUIPO EN LECTURA</center>
        </th>
        <th>
          <center>CONTADORES</center>
        </th>
        <th>
          <center>ACCIONES</center>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php if ($query['numRows'] != 0) {
        foreach ($query['dataFetch'] as $row) {
          list($cambio_anio, $cambio_mes, $cambio_dia) = explode("-", $row['cambio_fecha']);
      ?>
          <tr>
            <td><?= $row['cambio_folio']; ?></td>
            <td><?= ucfirst(dateFormat($row['cambio_fecha'], "simple")); ?></td>
            <td><?= $row['cliente_rs'] . " (" . $row['cliente_rfc'] . ") <br> " . $row['contrato_folio'] . "-" . $row['renta_folio'] . " | " . $row['renta_depto'] . "<br>Zona: " . $row['zona_nombre']; ?></td>
            <td><?= $row['cambio_motivo']; ?></td>
            <td><?= $row['equipoIng_codigo'] . "<br>" . $row['modeloIng_linea'] . " " . $row['modeloIng_modelo'] . "<br>" . $row['equipoIng_serie']; ?></td>
            <td><?= $row['equipoRet_codigo'] . "<br>" . $row['modeloRet_linea'] . " " . $row['modeloRet_modelo'] . "<br>" . $row['equipoRet_serie']; ?></td>
            <td><?= $row['cambio_comm']; ?></td>
            <td>
              <?php
              // $pdfName = $cambio_dia . "-" . $cambio_mes . "-" . $cambio_anio . " - " . $row['cliente_rs'] . " (" . $row['contrato_folio'] . "-" . $row['renta_folio'] . " - " . $row['renta_depto'] . ") - " . $row['equipoRet_serie'] . " x " . $row['equipoIng_serie'] . " - Cambio de Equipo.pdf";
              // echo $pdfName;
              // if (file_exists(SERVERDIR . "DocsCR/CambiosDeEquipos/" . $cambio_anio . "/" . $cambio_mes . "/" . $pdfName)) {
              //   echo "Archivo existe escrito mal";
              // }
              ?>
              <!-- <button class="btn btn-secondary EquTaller" id="">Taller</button> -->
              <?php if (file_exists(SERVERDIR . "DocsCR/CambiosDeEquipos/" . $cambio_anio . "/" . $cambio_mes . "/" . $row['cambio_folio'] . ".pdf")) { ?>
                <button class="btn btn-info cambioAction" data-type="PDF" id="<?= encryption($row['cambio_id']); ?>">PDF</button>
              <?php } ?>
              <button class="btn btn-warning cambioAction" data-type="EDIT" id="<?= encryption($row['cambio_id']); ?>">EDITAR</button>
            </td>
          </tr>
      <?php }
      } ?>
    </tbody>
  </table>
</div>