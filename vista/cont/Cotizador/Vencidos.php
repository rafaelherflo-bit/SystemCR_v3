<!--  Botones de navegacion. -->
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Cotizador/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Cotizador/Lista"><i class="fas fa-list"></i> &nbsp; LISTA</a>
    </li>
    <li>
      <a class="active"><i class="fas fa-list"></i> &nbsp; VENCIDOS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Almacen/Toners/Lista"><i class="fas fa-boxes"></i> &nbsp; ALMACEN</a>
    </li>
  </ul>
</div>

<!-- Tabla. -->
<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>
          <center>FOLIO</center>
        </th>
        <th>
          <center>FECHA DE EMISION</center>
        </th>
        <th>
          <center>CLIENTE</center>
        </th>
        <th>
          <center>MONTO</center>
        </th>
        <th>
          <center>ARTICULOS</center>
        </th>
        <th>
          <center>CADUCIDAD</center>
        </th>
        <th>
          <center>ACCIONES</center>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php
      $QRYcotM = consultaData("SELECT * FROM cotizadorM WHERE cotM_estatus = 0");
      if ($QRYcotM['numRows'] >= 1) {
        foreach ($QRYcotM['dataFetch'] as $rowM) {
          $monto = 0;
          $articulos = 0;

          $fecha_registro = new DateTime($rowM['cotM_fecha']);

          // Obtener la fecha actual
          $fecha_actual = new DateTime();

          // Calcular la fecha límite (fecha de registro + 30 días)
          $fecha_limite = (clone $fecha_registro)->modify("+29 days");

          // Comparar la fecha actual con la fecha límite
          if ($fecha_actual <= $fecha_limite) {
            $caducidad = "Vigente hasta el " . $fecha_limite->format('Y-m-d');
          } else {
            $caducidad = "El registro caduco el " . $fecha_limite->format('Y-m-d');
            if ($rowM['cotM_estatus'] == 1) {
              sentenciaData("UPDATE cotizadorM SET cotM_estatus = 0 WHERE cotM_id = " . $rowM['cotM_id']);
            }
          }


          $QRYcotD = consultaData("SELECT * FROM cotizadorD WHERE cotD_cotM_id = " . $rowM['cotM_id']);
          if ($QRYcotD['numRows'] >= 1) {
            foreach ($QRYcotD['dataFetch'] as $rowD) {
              $articulos = $articulos + $rowD['cotD_cantidad'];
              $monto = $monto + $rowD['cotD_monto'];
            }

            // Verificar si el registro esta sin productos,  de ser asi se elimina y se recarga la pagina.
          } else {
            sentenciaData("DELETE FROM cotizadorM WHERE cotM_id = " . $rowM['cotM_id']);
            echo '
            <script>
              window.location.reload();
            </script>
            ';
          }


      ?>
          <tr>
            <td><?= $rowM['cotM_folio']; ?></td>
            <td><?= dateFormat($rowM['cotM_fecha'], "numeros"); ?></td>
            <td><?= "(" . $rowM['cotM_cliRFC'] . ") | " . $rowM['cotM_cliRS']; ?></td>
            <td><?= $monto; ?></td>
            <td><?= $articulos; ?></td>
            <td><?= $caducidad; ?></td>
            <td>
              <span class="btn btn-info btnDetalles" data-id="<?= encryption($rowM['cotM_id']); ?>"><i class="fas fa-info"></i></span>
            </td>
          </tr>
      <?php }
      } ?>
    </tbody>
  </table>
</div>