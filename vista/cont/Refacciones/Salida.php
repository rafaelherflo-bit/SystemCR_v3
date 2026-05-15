<?php  ?>
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?= SERVERURL; ?>Refacciones/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
        </li>
        <li>
            <a href="<?= SERVERURL; ?>Refacciones/Entrada"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
        </li>
        <li>
            <a href="<?= SERVERURL; ?>Refacciones/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; EXISTENCIAS</a>
        </li>
        <!-- <li>
            <a class="active"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR SALIDA</a>
        </li> -->
        <li>
            <a href="<?= SERVERURL; ?>Refacciones/Salidas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE SALIDAS</a>
        </li>
    </ul>
</div>

<center>
    <h4><i class="fas fa-plus fa-fw"></i> &nbsp; Agregar Salida</h4>
</center>

<div class="container-fluid">
    <form class="form-neon FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
        <fieldset>
            <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <select class="form-select" id="ref_id_out" name="ref_id_out" data-placeholder="Selecciona una Refaccion">
                                <option></option>
                                <?php
                                $sql = "SELECT * FROM Refacciones
                                    INNER JOIN CategoriasR ON Refacciones.ref_catR_id = CategoriasR.catR_id
                                    INNER JOIN ProveedoresR ON Refacciones.ref_provR_id = ProveedoresR.provR_id
                                    WHERE ref_estado = 'Activo'
                                    ORDER BY ref_codigo ASC";
                                $query = consultaData($sql);
                                $dataTon = $query['dataFetch'];
                                foreach ($dataTon as $dato) {
                                    $refE = consultaData("SELECT SUM(refRE_cant) AS refE FROM RefaccionesRegistrosE WHERE refRE_ref_id = " . $dato['ref_id'])['dataFetch'][0]['refE'];
                                    $refS = consultaData("SELECT SUM(refRS_cant) AS refS FROM RefaccionesRegistrosS WHERE refRS_ref_id = " . $dato['ref_id'])['dataFetch'][0]['refS'];
                                    $refStock = $refE - $refS;
                                    if ($refStock > 0) {
                                ?>
                                        <option value="<?= $dato['ref_id']; ?>"><?= $dato['catR_nombre'] . " | " . $dato['ref_codigo'] . " | " . $dato['provR_nombre'] . " | Stock: " . $refStock; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <input type="date" class="form-control" name="refRS_fecha" value="<?= date("Y-m-d"); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="refRS_cant" class="bmd-label-floating">CANTIDAD</label>
                            <input type="number" class="form-control" id="refRS_cant" name="refRS_cant" maxlength="50">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <select class="form-select" id="refRS_empleado" name="refRS_empleado" data-placeholder="Selecciona un Empleado">
                                <option></option>
                                <option value="Candy">Candy</option>
                                <option value="Renan">Renan</option>
                                <option value="Rafa">Rafa</option>
                                <option value="Darwin">Darwin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="form-group">
                            <label for="refRS_comm" class="bmd-label-floating">COMENTARIOS</label>
                            <textarea class="form-control" id="refRS_comm" name="refRS_comm" maxlength="50"></textarea>
                        </div>
                    </div>
                </div>
        </fieldset>
        <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
    </form>
</div>