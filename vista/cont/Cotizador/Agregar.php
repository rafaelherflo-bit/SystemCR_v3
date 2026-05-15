<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a class="active"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Cotizador/Lista"><i class="fas fa-list"></i> &nbsp; LISTA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Cotizador/Vencidos"><i class="fas fa-list"></i> &nbsp; VENCIDOS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Almacen/Toners/Lista"><i class="fas fa-boxes"></i> &nbsp; ALMACEN</a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="cotMnueva" autocomplete="off">
    <input type="hidden" name="nuevaCotizacion" value="1">
    <fieldset class="form-neon">
      <div class="container-fluid">
        <div class="row justify-content-md-center">
          <div class="col-md-auto">
            <div class="form-group">
              <label for="cotM_cliRS" class="bmd-label-floating">RAZON SOCIAL</label>
              <input class="form-control" type="text" id="cotM_cliRS" list="cotM_cliRS_list" name="cotM_cliRS" value="Publico en General">
              <datalist id="cotM_cliRS_list">
                <option value="Publico en General">Publico en General</option>
                <?php
                foreach (consultaData("SELECT * FROM Clientes WHERE cliente_id != 1 ORDER BY cliente_rs ASC")['dataFetch'] as $dato) { ?>
                  <option value="<?= $dato['cliente_rs']; ?>"><?= $dato['cliente_rs']; ?></option>
                <?php } ?>
              </datalist>
            </div>
          </div>
          <div class="col-md-auto">
            <div class="form-group">
              <label for="cotM_cliRFC" class="bmd-label-floating">RFC</label>
              <input class="form-control" type="text" id="cotM_cliRFC" list="cotM_cliRFC_list" name="cotM_cliRFC" value="XAXX010101000">
              <datalist id="cotM_cliRFC_list">
                <option value="XAXX010101000">XAXX010101000</option>
                <?php
                $sql = "SELECT * FROM Clientes WHERE cliente_id != 1 ORDER BY cliente_rs ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= $dato['cliente_rfc']; ?>"><?= $dato['cliente_rs']; ?></option>
                <?php } ?>
              </datalist>
            </div>
          </div>
          <div class="col-md-auto">
            <div class="form-group">
              <label for="cotM_IVA" id="cotM_IVA_label" class="bmd-label-floating">IVA AL 16%</label>
              <input class="form-control" id="cotM_IVA" name="cotM_IVA" type="range" value="16" min="0" max="100" pattern="^(/\d|[1-9]/\d|100)$" title="Ingrese un número entre 0 y 100">
            </div>
          </div>
        </div>
        <br>
        <div class="row justify-content-md-center">
          <div class="col-12">
            <div class="form-group">
              <center><label for="cotM_comm">COMENTARIOS</label></center>
              <textarea class="form-control" id="cotM_comm" name="cotM_comm" placeholder="Agrega un comentario de ser necesario" rows="5" style="width: 750vx;"></textarea>
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
  </form>
</div>