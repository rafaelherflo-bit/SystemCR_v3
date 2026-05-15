<?php ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; EXISTENCIAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salida"><i class="fas fa-sign-out-alt"></i> &nbsp; AGREGAR SALIDA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salidas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE SALIDAS</a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <fieldset class="form-neon container-fluid" id="AddExsT">
    <div class="row" id="divRowAddExsT">
      <div class="col">
        <div class="card" style="width: 18rem;">
          <div class="card-header">
            <div class="row">
              <div class="col-md-10">
                <legend><i class="fas fa-spray-can"></i> &nbsp; Entrada de Toners</legend>
              </div>
              <div class="col-md-2" id="fin-AddExsT" style="display: none;">
                <span id="btnSuccess-AddExsT" class="btn btn-success">Confirmar</span>
                <span id="btnCancel-AddExsT" class="btn btn-warning">Cancelar</span>
              </div>
            </div>
          </div>
          <ul class="list-group list-group-flush">
            <input class="form-control" type="date" id="fecha-AddExsT" value="<?= date("Y-m-d"); ?>">
            <select class="form-select" id="codigo-AddExsT" data-placeholder="CODIGO">
              <option></option>
              <?php
              $sql = 'SELECT * FROM Toners
                      INNER JOIN ProveedoresT ON ProveedoresT.provT_id = Toners.toner_provT_id
                      WHERE toner_estado = "Activo"
                      ORDER BY toner_codigo ASC';
              $query = consultaData($sql);
              $dataTon = $query['dataFetch'];
              foreach ($dataTon as $dato) {
                $tonerET = consultaData("SELECT SUM(tonerR_cant) AS tonerET FROM TonersRegistrosE WHERE tonerR_toner_id = " . $dato['toner_id'])['dataFetch'][0]['tonerET'];
                $tonerST = consultaData("SELECT SUM(tonerRO_cantidad) AS tonerST FROM TonersRegistrosS WHERE tonerRO_toner_id = " . $dato['toner_id'])['dataFetch'][0]['tonerST'];
                $tonersStock = $tonerET - $tonerST;
                if ($dato['toner_tipo'] == 0) {
                  $toner_tipo = "MONO";
                } elseif ($dato['toner_tipo'] == 1) {
                  $toner_tipo = "NEGRO";
                } elseif ($dato['toner_tipo'] == 2) {
                  $toner_tipo = "MAGENTA";
                } elseif ($dato['toner_tipo'] == 3) {
                  $toner_tipo = "CYAN";
                } elseif ($dato['toner_tipo'] == 4) {
                  $toner_tipo = "AMARILLO";
                }
              ?>
                <option value="<?= encryption($dato['toner_id']); ?>"><?= $dato['toner_parte'] . " | " . $dato['toner_codigo'] . " | " . $toner_tipo . " | STOCK: " . $tonersStock . " | " . $dato['toner_comp']; ?></option>
              <?php } ?>
            </select>
            <input class="form-control" type="number" id="cant-AddExsT" placeholder="CANTIDAD">
            <input class="form-control" type="text" id="comm-AddExsT" placeholder="COMENTARIO">
            <button class="btn btn-success" id="btn-AddExsT">Añadir Entrada</button>
          </ul>
        </div>
      </div>
    </div>
  </fieldset>

  <fieldset class="form-neon container-fluid" id="AddNewT">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
      <input type="hidden" name="toner_nuevo">
      <legend><i class="fas fa-spray-can"></i> &nbsp; Agregar Toner Nuevo</legend>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="tonerR_fecha" class="bmd-label-floating">FECHA</label>
              <input type="date" class="form-control" name="tonerR_fecha" id="tonerR_fecha" value="<?= date("Y-m-d"); ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="toner_codigo_add" class="bmd-label-floating">CODIGO</label>
              <input type="number" class="form-control" list="codigos" id="toner_codigo_add" name="toner_codigo_add" maxlength="50" pattern="^([0-9]{3,5})$">
              <datalist id="codigos">
                <?php
                $sql = 'SELECT * FROM Toners
                        INNER JOIN ProveedoresT ON ProveedoresT.provT_id = Toners.toner_provT_id
                        WHERE toner_estado = "Activo" ORDER BY toner_codigo ASC';
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= explode("-", $dato['toner_codigo'])[1]; ?>"><?= $dato['toner_codigo'] . " | " . $dato['toner_comp'] . " | " . $dato['provT_nombre']; ?></option>
                <?php } ?>
              </datalist>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="toner_parte" class="bmd-label-floating">NO. DE PARTE</label>
              <input type="text" class="form-control" id="toner_parte" name="toner_parte" maxlength="50" value="0">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="toner_stock" class="bmd-label-floating">RENDIMENTO</label>
              <input type="number" class="form-control" id="toner_stock" name="toner_rendi" value="0">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="toner_stock" class="bmd-label-floating">CANTIDAD</label>
              <input type="number" class="form-control" id="toner_stock" name="toner_stock" pattern="^[0-9]+$">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md">
          <div class="form-group">
            <select class="form-select" id="toner_tipo" name="toner_tipo" data-placeholder="Selecciona el color">
              <option></option>
              <option value="0">Monocromatico</option>
              <option value="1">Color Negro</option>
              <option value="2">Color Magenta</option>
              <option value="3">Color Cyan</option>
              <option value="4">Color Amarillo</option>
            </select>
          </div>
        </div>
        <div class="col-12 col-md">
          <div class="form-group">
            <select class="form-select" id="toner_marca" name="toner_marca" data-placeholder="Selecciona una marca de toner">
              <option></option>
              <option value="CH">Chip</option>
              <option value="TK">Toner Kyocera</option>
              <option value="ES">Toner OKI</option>
            </select>
          </div>
        </div>
        <div class="col-12 col-md">
          <div class="form-group">
            <select class="form-select" id="toner_provT_id" name="toner_provT_id" data-placeholder="Selecciona un proveedor">
              <option></option>
              <?php
              $sql = 'SELECT * FROM ProveedoresT ORDER BY provT_nombre ASC';
              $query = consultaData($sql);
              $dataProvT = $query['dataFetch'];
              foreach ($dataProvT as $dato) { ?>
                <option value="<?php echo $dato['provT_id']; ?>"><?php echo $dato['provT_nombre']; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-12">
          <div class="form-group">
            <label for="toner_comp" class="bmd-label-floating">COMPATIBILIDAD</label>
            <input type="text" class="form-control" id="toner_comp" name="toner_comp">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-12">
          <div class="form-group">
            <label for="tonerR_comm" class="bmd-label-floating">COMENTARIOS</label>
            <input type="text" class="form-control" id="tonerR_comm" name="tonerR_comm">
          </div>
        </div>
      </div>
      <div>
        <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
      </div>
    </form>
  </fieldset>
</div>