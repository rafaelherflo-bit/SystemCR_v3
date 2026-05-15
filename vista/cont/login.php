<div class="login-container">
    <div class="login-content">
        <p class="text-center">
            <i class="fas fa-user-circle fa-5x"></i>
        </p>
        <p class="text-center">
            Inicia sesión con tu cuenta
        </p>
        <form action="" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="UserName" class="bmd-label-floating"><i class="fas fa-user"></i> &nbsp; Usuario</label>
                <input type="text" class="form-control" id="UserName" name="usuario_log" pattern="^([a-z]{5})([0-9]{1})$" maxlength="8" required>
            </div>
            <div class="form-group">
                <label for="UserPassword" class="bmd-label-floating"><i class="fas fa-key"></i> &nbsp; Contraseña</label>
                <input type="password" class="form-control" id="UserPassword" name="clave_log" pattern="^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$" maxlength="16" required>
            </div>
            <button type="submit" class="btn-login text-center">Ingresar</button>
        </form>
    </div>
</div>
<?php
if (isset($_POST['usuario_log']) && isset($_POST['usuario_log'])) {
    echo loginSession();
}
?>