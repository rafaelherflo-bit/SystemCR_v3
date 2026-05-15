<?php
$DT_pageLength = 10;
$DT_orderType = "asc";
$DT_orderCol = 0;

$consulta = "SELECT * FROM Refacciones
                INNER JOIN CategoriasR ON Refacciones.ref_catR_id = CategoriasR.catR_id
                INNER JOIN ProveedoresR ON Refacciones.ref_provR_id = ProveedoresR.provR_id";
$query = consultaData($consulta); ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?php echo SERVERURL; ?>Refacciones/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
    </li>
    <li>
      <a href="<?php echo SERVERURL; ?>Refacciones/Entrada"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
    </li>
    <li>
      <button class="btn btn-secondary" id="btn-Cats"><i class="fas fa-suitcase"></i> &nbsp; CATEGORIAS</button>
    </li>
    <li>
      <a href="<?php echo SERVERURL; ?>Refacciones/Salida"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR SALIDA</a>
    </li>
    <li>
      <a href="<?php echo SERVERURL; ?>Refacciones/Salidas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE SALIDAS</a>
    </li>
  </ul>
</div>
<center>
  <h4><i class="fas fa-suitcase"></i> &nbsp; Lista de Refacciones <span class="btn btn-info" id="btn-Stock"><i class="fas fa-file-pdf fa-fw"></i></span></h4>
</center>
<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>
          <center>CATEGORIA</center>
        </th>
        <th>
          <center>CODIGO</center>
        </th>
        <th>
          <center>EXISTENCIAS</center>
        </th>
        <th>
          <center>COMPATIBILIDAD</center>
        </th>
        <th>
          <center>PROVEEDOR</center>
        </th>
        <th>
          <center>ESTADO</center>
        </th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($query['numRows'] != 0) {
        foreach ($query['dataFetch'] as $row) {
          $refE = consultaData("SELECT SUM(refRE_cant) AS refE FROM RefaccionesRegistrosE WHERE refRE_ref_id = " . $row['ref_id'])['dataFetch'][0]['refE'];
          $refS = consultaData("SELECT SUM(refRS_cant) AS refS FROM RefaccionesRegistrosS WHERE refRS_ref_id = " . $row['ref_id'])['dataFetch'][0]['refS'];
          $refStock = $refE - $refS;
      ?>
          <tr>
            <td><?= $row['catR_nombre']; ?></td>
            <td><?= $row['ref_codigo']; ?></td>
            <td>
              <center><?= $refStock; ?></center>
            </td>
            <td><?= $row['ref_comp']; ?></td>
            <td><?= $row['provR_nombre']; ?></td>
            <td><?= $row['ref_estado']; ?></td>
            <td>
              <button class="btn btn-warning btnEdit" value="<?= encryption($row['ref_id']); ?>"><i class="fas fa-pen"></i></button>
              <button class="btn btn-info btnPhoto" value="<?= encryption($row['ref_id']); ?>"><i class="fas fa-image"></i></button>
            </td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>
<div class="modal fade" id="modalCatsR" tabindex="-1" aria-labelledby="modalCatsRLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">CATEGORIAS DE REFACCIONES</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table-sm table-hover table table-secondary table-striped-columns">
          <thead class="table-dark">
            <tr>
              <th>CODIGO</th>
              <th>DESCRIPCION</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach (consultaData("SELECT * FROM CategoriasR")['dataFetch'] as $row) {
            ?>
              <tr>
                <td><?= $row['catR_nombre']; ?></td>
                <td><?= $row['catR_codigo']; ?></td>
                <td><button class="btn btn-warning btn-editCatR" id="<?= encryption($row['catR_id']); ?>"><i class="fas fa-pen"></i></button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-addCatR">Agregar Categoria</button>
      </div>
    </div>
  </div>
</div>