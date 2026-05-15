<?php ?>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entrada"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; EXISTENCIAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salidas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE SALIDAS</a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <div class="row" id="divRowOutTalm">
      <div class="col">
        <div class="card" style="width: 18rem;">
          <div class="card-header">
            <div class="row">
              <div class="col-md-10">
                <legend><i class="fas fa-spray-can"></i> &nbsp; Salida de Toners</legend>
              </div>
              <div class="col-md-2" id="fin-outTalm" style="display: none;">
                <span id="btnSuccess-outTalm" class="btn btn-success">Confirmar</span>
                <span id="btnCancel-outTalm" class="btn btn-warning">Cancelar</span>
              </div>
            </div>
          </div>
          <ul class="list-group list-group-flush">
            <input class="form-control" type="date" id="fecha-outTalm" value="<?= date("Y-m-d"); ?>">
            <div class="form-group">
              <select class="form-select" id="toner_id-outTalm" data-placeholder="CODIGO">
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
                  if ($tonersStock > 0) {
                    echo '<option value="' . encryption($dato['toner_id']) . '">' . $dato['toner_parte'] . ' | ' . $dato['toner_codigo'] . ' | ' . $toner_tipo . ' | STOCK: ' . $tonersStock . ' | ' . $dato['toner_comp'] . '</option>';
                  }
                } ?>
              </select>
            </div>
            <div class="form-group">
              <div class="custom-control custom-radio">
                <input type="radio" id="tipo-outTalm1" name="tipo-outTalm" value="Venta" class="custom-control-input tonerRO_CB">
                <label class="custom-control-label" for="tipo-outTalm1">Venta</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="tipo-outTalm2" name="tipo-outTalm" value="Renta" class="custom-control-input tonerRO_CB" checked>
                <label class="custom-control-label" for="tipo-outTalm2">Renta</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="tipo-outTalm3" name="tipo-outTalm" value="Interno" class="custom-control-input tonerRO_CB">
                <label class="custom-control-label" for="tipo-outTalm3">Interno</label>
              </div>
            </div>
            <input class="form-control" type="number" id="cant-outTalm" placeholder="CANTIDAD">
            <div class="row" id="divContTipo">
              <div class="col-12 col-md-12">
                <div class="form-group">
                  <input type="hidden" id="tipo-outTalm" value="Renta">
                  <select class="form-select" id="identificador-outTalm" data-placeholder="Selecciona una Renta">
                    <option></option>
                    <?php
                    $sql = 'SELECT * FROM Rentas
                              INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                              INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                              WHERE renta_estado = "Activo"';
                    $query = consultaData($sql);
                    $dataTon = $query['dataFetch'];
                    foreach ($dataTon as $dato) { ?>
                      <option value="<?php echo encryption($dato['renta_id']); ?>"><?php echo "(" . $dato['contrato_folio'] . "-" . $dato['renta_folio'] . ") - " . $dato['cliente_rs'] . " | " . $dato['renta_depto']; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-md-12">
                <div class="form-group">
                  <select class="form-select" id="emp-outTalm" data-placeholder="Selecciona un Empleado">
                    <option></option>
                    <option value="Candy">Candy</option>
                    <option value="Renan">Renan</option>
                    <option value="Rafa">Rafa</option>
                    <option value="Darwin">Darwin</option>
                  </select>
                </div>
              </div>
            </div>
            <!-- <div class="col-12 col-md-12">
              <div class="form-group">
                <input type="file" class="form-control" id="RST_file" accept="application/pdf">
              </div>
            </div> -->
            <textarea class="form-control" type="text" id="comm-outTalm" placeholder="COMENTARIO" rows="5"></textarea>
            <button class="btn btn-warning" id="btn-outTalm">Añadir Salida</button>
          </ul>
        </div>
      </div>
    </div>
  </fieldset>
</div>