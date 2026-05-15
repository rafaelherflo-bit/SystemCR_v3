<?php
$query = consultaData("SELECT * FROM Usuarios WHERE usuario_id != 1");
?>
<div class="container-fluid table-responsive">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?php echo SERVERURL; ?>Usuarios/agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR USUARIO</a>
    </li>
    <li>
      <a class="active"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE USUARIOS</a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>USUARIO</th>
        <th>NOMBRE COMPLETO</th>
        <th>CORREO</th>
        <th>TELEFONO</th>
        <th>DIRECCION</th>
        <th>ESTADO</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($query['numRows'] != 0) {
        foreach ($query['dataFetch'] as $row) {
      ?>
          <tr class="filaTBody" id="<?= encryption($row['usuario_id']); ?>">
            <td><?= $row['usuario_usuario']; ?></td>
            <td><?= $row['usuario_nombre'] . " " . $row['usuario_apellido']; ?></td>
            <td><?= $row['usuario_email']; ?></td>
            <td><?= $row['usuario_telefono']; ?></td>
            <td><?= $row['usuario_direccion']; ?></td>
            <td><?= $row['usuario_estado']; ?></td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>

</div>
<div class="modal fade" id="modalFormUpd">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
        <input type="hidden" id="usuario_id_Upd" name="usuario_id_Upd" required>
        <div class="col-12 col-md-6">
          <div class="form-group">
            <label style="color: black;" for="usuario_usuario_Upd">USUARIO</label>
            <input type="text" class="form-control" id="usuario_usuario_Upd" name="usuario_usuario_Upd" pattern="^([a-z]{5})([0-9]{1})$" maxlength="8" required>
          </div>
          <div class="form-group">
            <label style="color: black;" for="usuario_nombre_Upd">NOMBRE(s)</label>
            <input type="text" class="form-control" id="usuario_nombre_Upd" name="usuario_nombre_Upd" maxlength="150" required>
          </div>
          <div class="form-group">
            <label style="color: black;" for="usuario_apellido_Upd">APELLIDO(s)</label>
            <input type="text" class="form-control" id="usuario_apellido_Upd" name="usuario_apellido_Upd" maxlength="150" required>
          </div>
          <div class="form-group">
            <label style="color: black;" for="usuario_email_Upd">EMAIL</label>
            <input type="text" class="form-control" id="usuario_email_Upd" name="usuario_email_Upd" maxlength="100" required>
          </div>
          <div class="form-group">
            <label style="color: black;" for="usuario_telefono_Upd">TELEFONO</label>
            <input type="text" class="form-control" id="usuario_telefono_Upd" name="usuario_telefono_Upd" required>
          </div>
          <div class="form-group">
            <label style="color: black;" for="usuario_direccion_Upd">DIRECCION</label>
            <textarea class="form-control" id="usuario_direccion_Upd" name="usuario_direccion_Upd" maxlength="255" required></textarea>
          </div>
        </div>
        <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
      </form>
    </div>
  </div>
</div>