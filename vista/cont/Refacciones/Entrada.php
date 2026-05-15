<?php  ?>
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>Refacciones/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
        </li>
        <!-- <li>
            <a class="active"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
        </li> -->
        <li>
            <a href="<?php echo SERVERURL; ?>Refacciones/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; EXISTENCIAS</a>
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
    <h4><i class="fas fa-plus fa-fw"></i> &nbsp; Agregar Entrada</h4>
</center>

<div class="container-fluid">
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
        <fieldset>
            <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="refRE_fecha" class="bmd-label-floating">FECHA</label>
                            <input type="date" class="form-control" id="refRE_fecha" name="refRE_fecha" value="<?= date("Y-m-d"); ?>">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="ref_codigo_add" class="bmd-label-floating">CODIGO</label>
                            <input type="text" class="form-control" list="codigos" id="ref_codigo_add" name="ref_codigo_add" maxlength="50">
                            <datalist id="codigos">
                                <?php
                                $sql = "SELECT * FROM Refacciones
                                        INNER JOIN CategoriasR ON Refacciones.ref_catR_id = CategoriasR.catR_id
                                        WHERE ref_estado = 'Activo' ORDER BY ref_codigo ASC";
                                $query = consultaData($sql);
                                $dataTon = $query['dataFetch'];
                                foreach ($dataTon as $dato) { ?>
                                    <option value="<?php echo $dato['ref_codigo']; ?>"><?php echo $dato['catR_nombre']; ?></option>
                                <?php } ?>
                            </datalist>
                        </div>
                    </div>
                </div>
                <div id="formulario">
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="form-group">
                            <label for="refRE_cantidad" class="bmd-label-floating">CANTIDAD</label>
                            <input type="number" class="form-control" id="refRE_cantidad" name="refRE_cantidad" maxlength="50">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="form-group">
                            <label for="refRE_comm" class="bmd-label-floating">COMENTARIOS</label>
                            <input type="text" class="form-control" id="refRE_comm" name="refRE_comm" maxlength="50">
                        </div>
                    </div>
                </div>
        </fieldset>
        <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
    </form>
</div>