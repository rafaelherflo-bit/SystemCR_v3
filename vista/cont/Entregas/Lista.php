<?php
$query = consultaData("SELECT * FROM Clientes WHERE cliente_id != 1"); ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?php echo SERVERURL; ?>Clientes/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CLIENTE</a>
    </li>
    <li>
      <a class="active"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE CLIENTES</a>
    </li>
  </ul>
</div>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>RAZON SOCIAL</th>
        <th>RFC</th>
        <th>ESTADO</th>
        <th>ACCIONES</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($query['numRows'] != 0) {
        foreach ($query['dataFetch'] as $row) {
      ?>
          <tr>
            <td><?= $row['cliente_rs']; ?></td>
            <td><?= $row['cliente_rfc']; ?></td>
            <td><?= "<b>" . $row['cliente_estado'] . "</b>"; ?></td>
            <td>
              <button class="btn btn-warning btnEdit" value="<?= encryption($row['cliente_id']); ?>">EDITAR</button>
            </td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>
<div class="modal fade" id="modalForm">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitleForm"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
        <div class="modal-body" id="modalBodyForm">
        </div>
        <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
      </form>
    </div>
  </div>
</div>