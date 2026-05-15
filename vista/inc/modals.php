<!--- fullscrren --->
<div class="modal fade" id="modalFull" aria-hidden="true" aria-labelledby="modalTitleFull" tabindex="-1" data-bs-theme="dark">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalTitleFull"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBodyFull">
      </div>
    </div>
  </div>
</div>

<!--- Normal --->
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="modalWindowLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalTitleWindow"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBodyWindow">
      </div>
    </div>
  </div>
</div>

<!--- formulario --->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true" data-bs-theme="dark">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalTitleForm"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="modalForm" class="form-neon FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
        <div class="modal-body" id="modalBodyForm">
        </div>
        <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
      </form>
    </div>
  </div>
</div>

<form id="formRedirect" name="formRedirect" action="" method="POST">
</form>