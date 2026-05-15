<?php
$consulta = "SELECT * FROM Toners
            INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id
            WHERE toner_estado = 'Activo'
            ORDER BY toner_codigo ASC, toner_tipo ASC";
$query = consultaData($consulta);
?>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entrada"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
    </li>
    <li>
      <span class="btn btn-info" id="btn-Stock"><i class="fas fa-file-pdf fa-fw"></i> &nbsp; PDF</span>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salida"><i class="fas fa-sign-out-alt"></i> &nbsp; AGREGAR SALIDA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salidas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE SALIDAS</a>
    </li>
  </ul>
</div>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>NO. DE PARTE</th>
        <th>CODIGO</th>
        <th>COMPATIBILIDAD</th>
        <th>RENDIMIENTO</th>
        <th>TIPO</th>
        <th>STOCK</th>
        <th>PROVEEDOR</th>
        <th>ESTADO</th>
        <th>ACCIONES</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($query['numRows'] != 0) {
        foreach ($query['dataFetch'] as $row) {
          $tonerET = consultaData("SELECT SUM(tonerR_cant) AS tonerET FROM TonersRegistrosE WHERE tonerR_toner_id = " . $row['toner_id'])['dataFetch'][0]['tonerET'];
          $tonerST = consultaData("SELECT SUM(tonerRO_cantidad) AS tonerST FROM TonersRegistrosS WHERE tonerRO_toner_id = " . $row['toner_id'])['dataFetch'][0]['tonerST'];
          $tonersStock = $tonerET - $tonerST;

      ?>
          <tr>
            <td>
              <?= $row['toner_parte']; ?>
            </td>
            <td>
              <?= $row['toner_codigo']; ?>
            </td>
            <td>
              <?= $row['toner_comp']; ?>
            </td>
            <td>
              <center><?= $row['toner_rendi']; ?></center>
            </td>
            <?php if ($row['toner_tipo'] == 0) { ?>
              <td>Monocromatico</td>
            <?php } elseif ($row['toner_tipo'] == 1) { ?>
              <td>Negro</td>
            <?php } elseif ($row['toner_tipo'] == 2) { ?>
              <td>Magenta</td>
            <?php } elseif ($row['toner_tipo'] == 3) { ?>
              <td>Cyan</td>
            <?php } elseif ($row['toner_tipo'] == 4) { ?>
              <td>Amarillo</td>
            <?php } ?>
            <td>
              <?= $tonersStock; ?>
            </td>
            <td><?= $row['provT_nombre']; ?></td>
            <td><?= $row['toner_estado']; ?></td>
            <td>
              <button class="btn btn-warning btnEdit" value="<?= encryption($row['toner_id']); ?>"><i class="fas fa-pen"></i></button>
              <button class="btn btn-secondary btnDisable" value="<?= encryption($row['toner_id']); ?>"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>