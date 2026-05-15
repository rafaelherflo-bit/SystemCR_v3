<?php  ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a class="active" class=""><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CLIENTE</a>
    </li>
    <li>
      <a href="<?php echo SERVERURL; ?>Clientes/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE CLIENTES</a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
    <fieldset>
      <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="cliente_rs" class="bmd-label-floating">RAZON SOCIAL</label>
              <input type="text" class="form-control" list="clientes_rs" id="cliente_rs" name="cliente_rs_add" maxlength="50" pattern="^[A-Za-z0-9\s.,&-]+(?:\s+[A-Za-z0-9\s.,&-]+)*$">
              <datalist id="clientes_rs">
                <?php
                $sql = "SELECT * FROM Clientes WHERE cliente_id != 1 ORDER BY cliente_rs ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?php echo $dato['cliente_rs']; ?>"><?php echo $dato['cliente_rs']; ?></option>
                <?php } ?>
              </datalist>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="cliente_rfc" class="bmd-label-floating">RFC</label>
              <input type="text" class="form-control" list="clientes_rfc" id="cliente_rfc" name="cliente_rfc_add" maxlength="50" pattern="[a-zA-Z0-9]{10,15}">
              <datalist id="clientes_rfc">
                <?php
                $sql = "SELECT * FROM Clientes WHERE cliente_id != 1 ORDER BY cliente_rfc ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?php echo $dato['cliente_rfc']; ?>"><?php echo $dato['cliente_rfc']; ?></option>
                <?php } ?>
              </datalist>
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
  </form>
</div>