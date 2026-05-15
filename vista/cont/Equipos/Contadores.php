<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h6 class="fw-bold mb-3 text-uppercase small">Filtrar Historial de Reportes</h6>
    <div class="row g-3">
      <input type="hidden" id="equipo_id" value="<?= $pagina[2] ?>">
      <div class="col-md-3">
        <label class="small fw-bold text-muted">FECHA FIN</label>
        <input type="date" id="fecha_fin" class="form-control">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="button" id="btnFiltrar" class="btn btn-primary w-100">
          <i class="fas fa-search me-1"></i> Consultar
        </button>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
  <table class="dataTable table table-sm table-hover table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>Fecha Recibo</th>
        <th class="text-center">Escaneo Total</th>
        <th class="text-center">B&N Total</th>
        <th class="text-center">Color Total</th>
      </tr>
    </thead>
    <tbody id="bodyContenido"></tbody>
  </table>
</div>

<script>
  document.getElementById("btnFiltrar").addEventListener("click", async function() {
    const equipo = document.getElementById("equipo_id").value;
    const fin = document.getElementById("fecha_fin").value;
    const bodyContenido = document.getElementById("bodyContenido");

    if (!equipo || !fin) {
      alert("Por favor completa todos los campos.");
      return;
    }

    // Usamos FormData para enviar los datos como $_POST
    var result = await QRYajax("Equipos", "consultar_contador", equipo, fin);
    console.log(result)

    if (result.Status) {
      var lastLect = result.Data.lastLect;
      var lastReg = result.Data.lastReg;
      var Calculo = result.Data.Calculo;
      let TRs = `
                <tr>
                    <td>${lastLect.fecha}</td>
                    <td class="text-center fw-bold text-success">${lastLect.esc}</td>
                    <td class="text-center fw-bold text-dark">${lastLect.bn}</td>
                    <td class="text-center fw-bold text-danger">${lastLect.col}</td>
                </tr>
                <tr>
                    <td>${lastReg.fecha}</td>
                    <td class="text-center fw-bold text-success">${lastReg.esc}</td>
                    <td class="text-center fw-bold text-dark">${lastReg.bn}</td>
                    <td class="text-center fw-bold text-danger">${lastReg.col}</td>
                </tr>
                <tr>
                    <td>TOTAL:</td>
                    <td class="text-center fw-bold text-success">${Calculo.diffEsc}</td>
                    <td class="text-center fw-bold text-dark">${Calculo.diffBN}</td>
                    <td class="text-center fw-bold text-danger">${Calculo.diffCol}</td>
                </tr>`;
      bodyContenido.innerHTML = TRs;
    } else {
      console.log(result.Data);
    }
  });
</script>