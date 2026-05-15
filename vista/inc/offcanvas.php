<header class="navbar bd-navbar navbar-dark bg-dark fixed-top sticky-top">
  <div class="container-fluid">

    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <a class="btn btn-light" href="<?= SERVERURL; ?>Dash/"><i class="fab fa-dashcube fa-fw"></i></a>

    <div class="dropdown">
      <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-window-restore"></i>
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="<?= SERVERURL . 'Cotizador/Lista'/*"#"*/; ?>"><i class="fas fa-cash-register"></i> &nbsp; COTIZADOR</a></li>
        <!-- <li><a class="dropdown-item" href="<?= SERVERURL . 'Cobranzas/Morosos'; ?>"><i class="fas fa-cash-register"></i> &nbsp; COBRANZAS</a></li> -->
        <li><a class="dropdown-item" href="<?= SERVERURL . 'Almacen/Movimientos/CustomMonth/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")); ?>"><i class="fas fa-dolly"></i> &nbsp; ALMACEN</a></li>
        <li>
          <a class="dropdown-item" href="<?= SERVERURL . 'Lecturas/Custom/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")); ?>"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LECTURAS</a>
        </li>
      </ul>
    </div>
    <div class="dropdown">
      <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-layer-group"></i>
      </button>
      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item" href="<?= SERVERURL; ?>Clientes/Lista"><i class="fas fa-fingerprint fa-fw"></i> &nbsp; Clientes &nbsp; <?= consultaData("SELECT * FROM Clientes")['numRows']; ?></a>
        </li>
        <li>
          <a class="dropdown-item" href="<?= SERVERURL; ?>Contratos/Lista"><i class="fas fa-file-contract fa-fw"></i> &nbsp; Contratos &nbsp; <?= consultaData("SELECT * FROM Contratos WHERE contrato_estado = 'Activo'")['numRows']; ?></a>
        </li>
        <li>
          <a class="dropdown-item" href="<?= SERVERURL . 'Rentas/Lista'; ?>"><i class="fas fa-concierge-bell fa-fw"></i> &nbsp; Rentas &nbsp; <?= consultaData("SELECT * FROM Rentas WHERE renta_estado = 'Activo'")['numRows']; ?></a>
        </li>
      </ul>
    </div>

    <a href="#" class="btn btn-danger btn-exit-system">
      <i class="fas fa-power-off"></i>
    </a>

    <div class="offcanvas offcanvas-start text-bg-dark <?= $_SESSION['navbarStatus']; ?>" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">

      <!-- <a href="#" class="btn btn-dark closeToggler"><?= $_SESSION['navbarBtn']; ?></a> -->

      <div class="offcanvas-header">
        <figure class="full-box nav-lateral-avatar">
          <i class="far fa-times-circle show-nav-lateral"></i>
          <?php
          if (file_exists(SERVERDIR . "vista/assets/avatar/" . $_SESSION['usuario'] . ".png")) {
            $avatar = SERVERURL . "vista/assets/avatar/" . $_SESSION['usuario'] . ".png";
          } else {
            $avatar = SERVERURL . "vista/assets/avatar/Avatar.png";
          }
          ?>
          <img src="<?= $avatar; ?>" class="img-fluid" alt="Avatar">
          <figcaption class="roboto-medium text-center">
            <?= $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?>
            <br>
            <small class="roboto-condensed-light">
              <?= $_SESSION['email']; ?>
            </small>
          </figcaption>
        </figure>
      </div>

      <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
          <!-- <li>
                    <a href="<?= SERVERURL; ?>Inicio/"><i class="fab fa-dashcube fa-fw"></i> &nbsp; Inicio</a>
                </li> -->
          <!-- <li class="nav-item">
                        <a class="nav-link" href="<?= SERVERURL; ?>Dash/"><i class="fab fa-dashcube fa-fw"></i> &nbsp; DASHBOARD</a>
                    </li> -->

          <!-- <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL . 'Cobranzas/Morosos'; ?>"><i class="fas fa-cash-register"></i> &nbsp; Cobranzas</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL . 'Cotizador/Lista'/*"#"*/; ?>"><i class="fas fa-cash-register"></i> &nbsp; Cotizador</a>
          </li> -->

          <!-- <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL . 'Lecturas/CustomMonth/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")); ?>"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lecturas</a>
          </li> -->

          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL . 'ReportesR/CustomMonth/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")); ?>"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Reportes - CR</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL . 'ReportesF/Custom/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")); ?>"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Reportes - Foraneos</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="<?= SERVERURL; ?>Equipos/Lista"><i class="fas fa-print fa-fw"></i> &nbsp; Equipos</a>
          </li>

          <?php
          // if ($_SESSION['id'] == 1 || $_SESSION['id'] == 2) {
          ?>
          <!-- <li>
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-warehouse"></i> &nbsp; Almacen</i>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li>
                <a class="dropdown-item" href="<?= SERVERURL . 'Almacen/Movimientos/CustomMonth/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")); ?>"><i class="fas fa-dolly"></i> &nbsp; Movimientos</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= SERVERURL; ?>Almacen/Toners/Lista"><i class="fas fa-spray-can"></i> &nbsp; Toners</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= SERVERURL; ?>Almacen/Refacciones/Lista"><i class="fas fa-toolbox"></i> &nbsp; Refacciones</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= SERVERURL; ?>Almacen/Chips/Lista"><i class="fas fa-microchip"></i> &nbsp; Chips</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= SERVERURL; ?>Almacen/Servicios/Lista"><i class="fas fa-concierge-bell"></i> &nbsp; Servicios</a>
              </li>
            </ul>
          </li> -->
          <?php
          // }
          ?>

          <!-- <li>
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-warehouse"></i> &nbsp; Almacen</i>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li>
                <a class="dropdown-item" href="<?= SERVERURL; ?>Toners/Lista"><i class="fas fa-spray-can"></i> &nbsp; Toners</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= SERVERURL; ?>Refacciones/Lista"><i class="fas fa-toolbox"></i> &nbsp; Refacciones</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= SERVERURL; ?>Proveedores/Toners"><i class="fas fa-parachute-box"></i> &nbsp; Proveedores</a>
              </li>
            </ul>
          </li> -->

          <li>
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-shuttle-van"></i> &nbsp; Operaciones</i>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li>
                <a class="dropdown-item" href="<?= SERVERURL . 'Cambios/Custom/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")); ?>"><i class="fas fa-exchange-alt"></i> &nbsp; Cambios de Equipo</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= SERVERURL; ?>Retiros/Lista"><i class="fas fa-cart-arrow-down"></i> &nbsp; Retiros de Equipo</a>
              </li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-align-justify"></i> &nbsp; Formatos</i>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li>
                <a class="dropdown-item formatBLK" id="CamEqu"><i class="fas fa-exchange-alt"></i> &nbsp; Cambios de Equipo</a>
              </li>
              <li>
                <a class="dropdown-item formatBLK" id="RetEqu"><i class="fas fa-cart-arrow-down"></i> &nbsp; Retiro de Equipo</a>
              </li>
              <li>
                <a class="dropdown-item formatBLK" id="EntEqu"><i class="fas fa-cart-plus"></i> &nbsp; Entrega de Equipo</a>
              </li>
              <li>
                <a class="dropdown-item formatBLK" id="EntEquNew"><i class="fas fa-cart-plus"></i> &nbsp; Entrega Nueva Renta</a>
              </li>
              <li>
                <a class="dropdown-item formatBLK" id="LectB"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Toma de Lectura</a>
              </li>
              <li>
                <a class="dropdown-item formatBLK" id="RepServ"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Reporte de Servicio</a>
              </li>
              <li>
                <a class="dropdown-item formatBLK" id="RepServF"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Reporte de Servicio (F)</a>
              </li>
              <li>
                <a class="dropdown-item formatBLK" id="ContAct"><i class="fas fa-thumbs-up"></i> &nbsp; Contratos Activos</a>
              </li>
              <li>
                <a class="dropdown-item formatBLK" id="RentsStock"><i class="fas fa-plus fa-fw"></i> &nbsp; Abastecimiento de Rentas</a>
              </li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-align-justify"></i> &nbsp; Programas</i>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li>
                <a class="dropdown-item" href="<?= "http://" . $_SERVER["HTTP_HOST"]; ?>:8080/AnyDesk.exe" target="_blank"><i class="fas fa-exchange-alt"></i> &nbsp; AnyDesk</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= "http://" . $_SERVER["HTTP_HOST"]; ?>:8080/KxUDP.exe" target="_blank"><i class="fas fa-exchange-alt"></i> &nbsp; KX Driver</a>
              </li>
              <li>
                <a class="dropdown-item" href="<?= "http://" . $_SERVER["HTTP_HOST"]; ?>:8080/office.rar" target="_blank"><i class="fas fa-exchange-alt"></i> &nbsp; Office 2021 LTSC</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</header>