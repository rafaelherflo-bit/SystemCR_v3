<?php
$SQL = "SELECT * FROM Lecturas
INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
WHERE lectura_id = " . decryption($GLOBALS['pagina2']);

$QRY = consultaData($SQL);
if ($QRY['numRows'] == 0) {
  redirect($GLOBALS['redirect']);
  exit();
} else {
  $Data = $QRY['dataFetch'][0];
}

// Preparar rutas de archivos una sola vez
$fechaPath = explode("-", $Data['lectura_fecha']);
$basePath = "DocsCR/Lecturas/{$fechaPath[0]}/{$fechaPath[1]}/";
$pathFormato = SERVERDIR . $basePath . "Formatos/" . $Data['lectura_pdf'];
$pathPE = SERVERDIR . $basePath . "PE/" . $Data['lectura_pdf'];
$urlFormato = SERVERURL . $basePath . "Formatos/" . $Data['lectura_pdf'];
$urlPE = SERVERURL . $basePath . "PE/" . $Data['lectura_pdf'];
?>

<div class="container-fluid py-3">
  <div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
      <li>
        <a href="<?= SERVERURL; ?>Lecturas/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
      </li>
      <li>
        <?php filtroCustom("Lecturas"); ?>
      </li>
    </ul>
  </div>

  <div class="container-fluid mb-2">
    <h4 class="fw-bold mb-0 text-primary text-uppercase"><i class="fas fa-edit me-2"></i> Editor de Lectura</h4>
    <span class="text-muted fw-bold"><?= $Data['cliente_rs']; ?> | <span class="text-success"><?= $Data['renta_depto']; ?></span></span>
  </div>


  <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
    <input type="hidden" name="actualizarLectura" value="<?= $GLOBALS['pagina2']; ?>">
    <input type="hidden" name="lectura_renta_id" value="<?= encryption($Data['lectura_renta_id']); ?>">

    <input type="hidden" name="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
    <input type="hidden" name="clave_admin" value="<?= $_SESSION['passclave']; ?>">

    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-0 pt-3">
            <h6 class="fw-bold text-uppercase text-muted small"><i class="fas fa-calculator me-2"></i> Contadores Actuales</h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="small fw-bold">Fecha de Lectura</label>
                <input type="date" class="form-control" name="lectura_fecha" value="<?= $Data['lectura_fecha']; ?>">
              </div>
              <div class="col-md-6">
                <label class="small fw-bold">Escaneo</label>
                <input type="number" class="form-control" name="lectura_esc" value="<?= $Data['lectura_esc']; ?>">
              </div>
              <div class="col-md-6">
                <label class="small fw-bold">B&N</label>
                <input type="number" class="form-control fw-bold" name="lectura_bn" value="<?= $Data['lectura_bn']; ?>">
              </div>
              <?php if ($Data['modelo_tipo'] != "Monocromatico"): ?>
                <div class="col-md-6">
                  <label class="small fw-bold text-danger">Color</label>
                  <input type="number" class="form-control fw-bold text-danger" name="lectura_col" value="<?= $Data['lectura_col']; ?>">
                </div>
              <?php endif; ?>
              <div class="col-12">
                <label class="small fw-bold">Comentarios / Observaciones</label>
                <textarea class="form-control" name="comments" rows="3" maxlength="250" placeholder="Escribe aquí..."></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-0 pt-3">
            <h6 class="fw-bold text-uppercase text-muted small"><i class="fas fa-paperclip me-2"></i> Evidencia Digital</h6>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-md-6 border-end">
                <p class="small fw-bold text-uppercase">Formato de Lectura</p>
                <?php if ($Data['lectura_pdf'] != "" && file_exists($pathFormato)): ?>
                  <img src="<?= $urlFormato; ?>" class="img-fluid rounded shadow-sm mb-2" style="max-height: 250px;">
                  <button type="button" class="btn btn-sm btn-outline-danger d-block mx-auto" id="delLectFL" data-id="<?= $GLOBALS['pagina2']; ?>" data-archivo="<?= $Data['lectura_pdf']; ?>" data-fecha="<?= $Data['lectura_fecha']; ?>">
                    <i class="fas fa-trash"></i> Eliminar
                  </button>
                <?php else: ?>
                  <div class="p-3 border rounded bg-light">
                    <input type="checkbox" class="btn-check" id="lectura_formato">
                    <label class="btn btn-outline-primary btn-sm" for="lectura_formato">Subir Formato</label>
                    <div id="div_lectura_formato" class="mt-2"></div>
                  </div>
                <?php endif; ?>
              </div>

              <div class="col-md-6">
                <p class="small fw-bold text-uppercase">Página de Estado</p>
                <?php if ($Data['lectura_pdf'] != "" && file_exists($pathPE)): ?>
                  <img src="<?= $urlPE; ?>" class="img-fluid rounded shadow-sm mb-2" style="max-height: 250px;">
                  <button type="button" class="btn btn-sm btn-outline-danger d-block mx-auto" id="delLectPE" data-id="<?= $GLOBALS['pagina2']; ?>" data-archivo="<?= $Data['lectura_pdf']; ?>" data-fecha="<?= $Data['lectura_fecha']; ?>">
                    <i class="fas fa-trash"></i> Eliminar
                  </button>
                <?php else: ?>
                  <div class="p-3 border rounded bg-light">
                    <input type="checkbox" class="btn-check" id="lectura_estado">
                    <label class="btn btn-outline-primary btn-sm" for="lectura_estado">Subir Estado</label>
                    <div id="div_lectura_estado" class="mt-2"></div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm p-4 text-center">
          <button type="submit" class="btn btn-primary btn-lg fw-bold w-100 mb-3 shadow">
            <i class="fas fa-save me-2"></i> GUARDAR CAMBIOS
          </button>
          <button type="reset" id="resetBtn" class="btn btn-light btn-sm text-muted">
            <i class="fas fa-paint-roller me-1"></i> Limpiar Formulario
          </button>
        </div>
      </div>
    </div>
  </form>
</div>