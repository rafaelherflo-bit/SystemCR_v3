<fieldset>
    <!-- <p class="text-center">Para poder guardar los cambios, debes ser usuario autorizado.</p> -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <!-- <label for="usuario_admin" class="bmd-label-floating">Nombre de usuario</label> -->
                    <input type="hidden" class="form-control" name="usuario_admin" id="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <!-- <label for="clave_admin" class="bmd-label-floating">Contraseña</label> -->
                    <input type="hidden" class="form-control" name="clave_admin" id="clave_admin" value="<?= $_SESSION['passclave']; ?>">
                </div>
            </div>
        </div>
    </div>
</fieldset>
<p class="text-center" style="margin-top: 40px;">
    <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
    &nbsp; &nbsp;
    <button id="resetBtn" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
</p>