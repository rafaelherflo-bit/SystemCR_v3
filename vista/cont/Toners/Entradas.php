<?php
$DT_orderType = "desc";
$DT_orderCol = 0;
$consulta = "SELECT * FROM TonersRegistrosE
            INNER JOIN Toners ON TonersRegistrosE.tonerR_toner_id = Toners.toner_id
            INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id";
$query = consultaData($consulta); ?>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entrada"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; EXISTENCIAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salidas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE SALIDAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salida"><i class="fas fa-sign-out-alt"></i> &nbsp; AGREGAR SALIDA</a>
    </li>
  </ul>
</div>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>FECHA REGISTRO</th>
        <th>NO. DE PARTE</th>
        <th>CODIGO</th>
        <th>COMPATIBILIDAD</th>
        <th>RENDIMIENTO</th>
        <th>CANTIDAD</th>
        <th>PROVEEDOR</th>
        <th>COMENTARIO</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($query['numRows'] != 0) {
        foreach ($query['dataFetch'] as $row) {
      ?>
          <tr>
            <td>
              <?= $row['tonerR_fecha']; ?>
            </td>
            <td>
              <?= $row['toner_parte']; ?>
            </td>
            <td>
              <?= $row['toner_codigo']; ?>
            </td>
            <td>
              <?= $row['provT_nombre']; ?>
            </td>
            <td>
              <?= $row['toner_rendi']; ?>
            </td>
            <td>
              <?= $row['tonerR_cant']; ?>
            </td>
            <td>
              <?= $row['toner_comp']; ?>
            </td>
            <td>
              <?= $row['tonerR_comm']; ?>
            </td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>