<?php
?>

<div class="container-fluid">
    <center>
        <h3><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</h3>
    </center>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?= SERVERURL . $pagina[0]; ?>/Activos"><i class="fas fa-hourglass fa-fw"></i> &nbsp; <b><?= consultaData("SELECT * FROM Reportes WHERE reporte_estado = 0")['numRows']; ?></b> &nbsp; ACTIVOS</a>
        </li>
        <li>
            <a href="<?= SERVERURL . $pagina[0]; ?>/Iniciar"><i class="fas fa-play fa-fw"></i> &nbsp; INICIAR</a>
        </li>
        <li>
            <?= customDMY($pagina[0]); ?>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
        <input type="hidden" name="reporte_estado" value="1">
        <fieldset class="form-neon">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-2">
                        <div class="form-group">
                            <label for="reporte_fecha" class="bmd-label-floating">FECHA DE REPORTE</label>
                            <input type="datetime-local" class="form-control" name="reporte_fecha" id="reporte_fecha" value="<?= date("Y-m-d\TH:i"); ?>">
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="form-group">
                            <label for="reporte_fecha_inicio" class="bmd-label-floating">INICIO</label>
                            <input type="datetime-local" class="form-control" name="reporte_fecha_inicio" id="reporte_fecha_inicio" value="<?= date("Y-m-d\TH:i"); ?>">
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="form-group">
                            <label for="reporte_fecha_fin" class="bmd-label-floating">FINAL</label>
                            <input type="datetime-local" class="form-control" name="reporte_fecha_fin" id="reporte_fecha_fin" value="<?= date("Y-m-d\TH:i"); ?>">
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label for="reporte_renta_id" class="bmd-label-floating">RENTA</label>
                            <select name="reporte_renta_id" id="reporte_renta_id" class="form-select">
                                <option></option>
                                <?php
                                $sql = "SELECT * FROM Rentas
                                    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                                    ORDER BY contrato_folio ASC";
                                $query = consultaData($sql);
                                $dataTon = $query['dataFetch'];
                                foreach ($dataTon as $dato) { ?>
                                    <option value="<?= encryption($dato['renta_id']); ?>"><?= $dato['contrato_folio'] . "-" . $dato['renta_folio'] . " | " . $dato['cliente_rs'] . "  ( " . $dato['cliente_rfc'] . " ) | " . $dato['renta_depto']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="form-group">
                            <label for="reporte_wmakes" class="bmd-label-floating">REPORTO</label>
                            <input type="text" class="form-control" name="reporte_wmakes" id="reporte_wmakes" placeholder="Persona quien realizo el reporte">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label for="reporte_reporte" class="bmd-label-floating">REPORTE</label>
                            <textarea class="form-control" id="reporte_reporte" name="reporte_reporte"></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label for="reporte_resolucion" class="bmd-label-floating">RESOLUCION</label>
                            <textarea class="form-control" id="reporte_resolucion" name="reporte_resolucion"></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label for="comments" class="bmd-label-floating">COMENTARIOS</label>
                            <textarea class="form-control" id="comments" name="comments" maxlength="250"></textarea>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <legend id="renta_equipo">&nbsp; CONSUMIBLES</legend>
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label for="renta_stock_K" class="bmd-label-floating">STOCK NEGRO</label>
                            <input type="number" class="form-control" id="renta_stock_K" name="renta_stock_K" maxlength="50">
                        </div>
                    </div>
                    <div class="col" id="col_renta_stock_M">
                        <div class="form-group">
                            <label for="renta_stock_M" class="bmd-label-floating">STOCK MAGENTA</label>
                            <input type="number" class="form-control" id="renta_stock_M" name="renta_stock_M" maxlength="50">
                        </div>
                    </div>
                    <div class="col-12 col-md" id="col_renta_stock_C">
                        <div class="form-group">
                            <label for="renta_stock_C" class="bmd-label-floating">STOCK CYAN</label>
                            <input type="number" class="form-control" id="renta_stock_C" name="renta_stock_C" maxlength="50">
                        </div>
                    </div>
                    <div class="col-12 col-md" id="col_renta_stock_Y">
                        <div class="form-group">
                            <label for="renta_stock_Y" class="bmd-label-floating">STOCK AMARILLO</label>
                            <input type="number" class="form-control" id="renta_stock_Y" name="renta_stock_Y" maxlength="50">
                        </div>
                    </div>
                    <div class="col-12 col-md" id="col_renta_stock_R">
                        <div class="form-group">
                            <label for="renta_stock_R" class="bmd-label-floating">STOCK RESIDUAL</label>
                            <input type="number" class="form-control" id="renta_stock_R" name="renta_stock_R" maxlength="50">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label for="equipo_nivel_K" class="bmd-label-floating">NIVEL NEGRO</label>
                            <input type="number" class="form-control" id="equipo_nivel_K" name="equipo_nivel_K" maxlength="50">
                        </div>
                    </div>
                    <div class="col-12 col-md" id="col_equipo_nivel_M">
                        <div class="form-group">
                            <label for="equipo_nivel_M" class="bmd-label-floating">NIVEL MAGENTA</label>
                            <input type="number" class="form-control" id="equipo_nivel_M" name="equipo_nivel_M" maxlength="50">
                        </div>
                    </div>
                    <div class="col-12 col-md" id="col_equipo_nivel_C">
                        <div class="form-group">
                            <label for="equipo_nivel_C" class="bmd-label-floating">NIVEL CYAN</label>
                            <input type="number" class="form-control" id="equipo_nivel_C" name="equipo_nivel_C" maxlength="50">
                        </div>
                    </div>
                    <div class="col-12 col-md" id="col_equipo_nivel_Y">
                        <div class="form-group">
                            <label for="equipo_nivel_Y" class="bmd-label-floating">NIVEL AMARILLO</label>
                            <input type="number" class="form-control" id="equipo_nivel_Y" name="equipo_nivel_Y" maxlength="50">
                        </div>
                    </div>
                    <div class="col-12 col-md" id="col_equipo_nivel_R">
                        <div class="form-group">
                            <label for="equipo_nivel_R" class="bmd-label-floating">NIVEL RESIDUAL</label>
                            <input type="number" class="form-control" id="equipo_nivel_R" name="equipo_nivel_R" maxlength="50">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label class="bmd-label-floating">EVIDENCIA</label>
                            <input type="file" class="form-control" name="reporte_archivo" accept="application/pdf">
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
    </form>
</div>