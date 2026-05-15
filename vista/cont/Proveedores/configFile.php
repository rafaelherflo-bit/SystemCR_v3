<?php
$contenido = getContenido($_GET['vista']);

if ($contenido == 'Toners') {
    require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else if ($contenido == 'Refacciones') {
    require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else if ($contenido == 'Equipos') {
    require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} ?>


<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <?= $link1; ?>
        </li>
        <li>
            <?= $link2; ?>
        </li>
        <li>
            <?= $link3; ?>
        </li>
    </ul>
</div>

<div class="container-fluid table-responsive">
    <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
        <thead class="table-dark">
            <tr>
                <th>NOMBRE</th>
                <th>ESTATUS</th>
                <th>ACCION</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (consultaData("SELECT * FROM $tabla")['numRows'] != 0) {
                foreach (consultaData("SELECT * FROM $tabla")['dataFetch'] as $row) {
            ?>
                    <tr>
                        <td><?= $row[$col . '_nombre']; ?></td>
                        <td><?= $row[$col . '_estado']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-edit-prov" id="<?= encryption($row[$col . '_id']); ?>" value="<?= $col; ?>">Editar</button>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>