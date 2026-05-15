<?php ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Lecturas/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <?php filtroCustom("Lecturas"); // Función externa para mostrar el filtro de fecha/periodo 
      ?>
    </li>
  </ul>
</div>
<input type="hidden" id="custom_anio" value="<?= $_POST['custom_anio']; ?>">
<input type="hidden" id="custom_mes" value="<?= $_POST['custom_mes']; ?>">

<div class="container-fluid">
  <center>
    <h3><?= ucfirst(dateFormat($_POST['custom_anio'] . "-" . $_POST['custom_mes'] . "-" . date("d"), "mesanio")); ?></h3>
  </center>
</div>

<div class="container-fluid"></div>
<div id="map-template">
</div>