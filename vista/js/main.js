// Validacion de Input con el pattern
document.addEventListener("DOMContentLoaded", function () {
  const inputs = document.querySelectorAll("input"); // Selecciona todos los inputs con la clase

  inputs.forEach((input) => {
    input.addEventListener("input", function () {
      validarInput(input);
    });
    input.addEventListener("blur", function () {
      // Valida también cuando se quita el foco del input
      validarInput(this);
    });
  });

  function validarInput(input) {
    const pattern = input.getAttribute("pattern");
    const value = input.value;

    if (pattern) {
      const regex = new RegExp(pattern);
      const esValido = regex.test(value);

      if (esValido) {
        input.style.borderColor = "green"; // Estilo para campo válido
      } else {
        input.style.borderColor = "red"; // Estilo para campo inválido
      }
    }
  }
});

// -------------------------------------------------------------------------------------
// Funcion para borrar registros mediante el ID
/* crear un boton con la siguiente sintaxis

            <span class="btn btn-raised btn-danger btn-sm btn-delRegWithID" data-table="equipos_ether" data-colname="equEther_equipo_id" data-value="<?= $pagina[2]; ?>">
              <i class="far fa-trash"></i> &nbsp; ELIMINAR CONFIGURACION
            </span>

*/
async function delRegWithID(table, colName, Value) {
  Swal.fire({
    title: "Estas seguro?",
    text: "No podras revertir el cambio!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, borrar!",
  }).then(async (result) => {
    if (result.isConfirmed) {
      var data = await QRYajax("delRegWithID", table, colName, Value);
      if (data.Status) {
        location.reload();
      } else {
        Swal.fire({
          title: "Ocurrio un error.",
          text: data.Data,
          icon: "error",
        });
      }
    }
  });
}
// Recoleccion de los borones con la clase designada y recorremos el array creado para poder realizar el evento click en cada uno.
if (document.querySelector(".btn-delRegWithID")) {
  document.querySelectorAll(".btn-delRegWithID").forEach((elem) => {
    elem.addEventListener("click", async function () {
      const Table = elem.dataset.table;
      const colName = elem.dataset.colname;
      const Value = elem.dataset.value;
      await delRegWithID(Table, colName, Value);
    });
  });
}
// -------------------------------------------------------------------------------------

$(document).ready(function () {
  select2();

  /*  Show/Hidden Submenus */
  $(".nav-btn-submenu").on("click", function (e) {
    e.preventDefault();
    var SubMenu = $(this).next("ul");
    var iconBtn = $(this).children(".fa-chevron-down");
    if (SubMenu.hasClass("show-nav-lateral-submenu")) {
      $(this).removeClass("active");
      iconBtn.removeClass("fa-rotate-180");
      SubMenu.removeClass("show-nav-lateral-submenu");
    } else {
      $(this).addClass("active");
      iconBtn.addClass("fa-rotate-180");
      SubMenu.addClass("show-nav-lateral-submenu");
    }
  });
});

$(function () {
  $('[data-toggle="popover"]').popover();
});

function select2() {
  $(".form-select").select2({
    theme: "bootstrap-5",
  });
}

if (document.getElementById("buscarCustom")) {
  document
    .getElementById("buscarCustom")
    .addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href =
        SERVERURL +
        this.dataset.page +
        "/Custom/" +
        document.getElementById("anioCustom").value +
        "/" +
        document.getElementById("mesCustom").value;
    });
}

if (document.getElementById("buscarCheck")) {
  document
    .getElementById("buscarCheck")
    .addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href =
        SERVERURL +
        this.dataset.page +
        "/Check/" +
        document.getElementById("anioCustom").value +
        "/" +
        document.getElementById("mesCustom").value;
    });
}

if (document.getElementById("btnCustomDay")) {
  document
    .getElementById("btnCustomDay")
    .addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href =
        SERVERURL +
        this.dataset.page +
        "/CustomDay/" +
        document.getElementById("slctCustomYear").value +
        "/" +
        document.getElementById("slctCustomMonth").value +
        "/" +
        document.getElementById("slctCustomDay").value;
    });
}

if (document.getElementById("btnCustomMonth")) {
  document
    .getElementById("btnCustomMonth")
    .addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href =
        SERVERURL +
        this.dataset.page +
        "/CustomMonth/" +
        document.getElementById("slctCustomYear").value +
        "/" +
        document.getElementById("slctCustomMonth").value;
    });
}

if (document.getElementById("btnCustomYear")) {
  document
    .getElementById("btnCustomYear")
    .addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href =
        SERVERURL +
        this.dataset.page +
        "/CustomYear/" +
        document.getElementById("slctCustomYear").value;
    });
}

function tablaDatos(pageLength, orderType, orderCol) {
  new DataTable(".dataTable", {
    paging: false,
    searching: true,
    info: false,
    responsive: true,
    autoWidth: false,
    order: [[orderCol, orderType]],
    language: {
      url: SERVERURL + "vista/js/dataTable_es-MX.json",
    },
  });
}

async function consultaFetch(tipo, valor) {
  var post = {
    method: "POST",
    body: JSON.stringify({
      tipo,
      valor,
    }),
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
  };
  const res = await fetch(SERVERURL + "ajax/consultasFetchAjax.php", post);
  const data = await res.json();
  return data;
}

async function QRYajax() {
  var post = {
    method: "POST",
    body: JSON.stringify({
      arguments,
    }),
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
  };
  const res = await fetch(SERVERURL + "ajax/fetchAjax.php", post);
  const data = await res.json();
  return data;
}

async function consultaFetch2(
  tipo,
  valor = 0,
  valor1 = 0,
  valor2 = 0,
  valor3 = 0,
) {
  var post = {
    method: "POST",
    body: JSON.stringify({
      tipo,
      valor,
      valor1,
      valor2,
      valor3,
    }),
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
  };
  const res = await fetch(SERVERURL + "ajax/consultasFetchAjax.php", post);
  const data = await res.json();
  return data;
}

async function sentenciaAjax(accion, id) {
  var post = {
    method: "POST",
    body: JSON.stringify({
      accion,
      id,
    }),
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
  };
  const res = await fetch(SERVERURL + "ajax/sentenciaAjax.php", post);
  const data = await res.json();
  return data;
}

if (document.getElementById("resetBtn")) {
  const resetBtn = document.getElementById("resetBtn");
  resetBtn.addEventListener("click", function (e) {
    e.preventDefault();
    Swal.fire({
      title: "Estas Seguro?",
      text: "Se reiniciara el formulario!!.",
      icon: "question",
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#3085d6",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      cancelButtonColor: "#d33",
    }).then((result) => {
      if (result.isConfirmed) {
        location.reload();
      }
    });
  });
}

if (document.querySelector(".formatBLK")) {
  document.querySelectorAll(".formatBLK").forEach((elem) => {
    elem.addEventListener("click", async function (e) {
      e.preventDefault();
      var url = SERVERURL + "vista/formats/";

      if (elem.id == "CamEqu") {
        var titleModal = "CAMBIO DE EQUIPO";
        url += "printCamEqu_B.php";
      } else if (elem.id == "RetEqu") {
        var titleModal = "RETIRO DE EQUIPO";
        url += "printRetEqu_B.php";
      } else if (elem.id == "EntEqu") {
        var titleModal = "ENTREGA DE EQUIPO";
        url += "printEntEqu_B.php";
      } else if (elem.id == "EntEquNew") {
        var titleModal = "ENTREGA DE NUEVA RENTA";
        url += "printEntEquNew_B.php";
      } else if (elem.id == "RepServ") {
        var titleModal = "REPORTE DE SERVICIO";
        url += "printRep_B.php";
      } else if (elem.id == "RepServF") {
        var titleModal = "REPORTE DE SERVICIO FORANEO";
        url += "printRepF_B.php";
      } else if (elem.id == "ContAct") {
        var titleModal = "CONTRATOS ACTIVOS";
        url += "printCont.php";
      } else if (elem.id == "LectB") {
        var titleModal = "TOMA DE LECTURA";
        url += "printLect_B.php";
      } else if (elem.id == "RentsStock") {
        var titleModal = "ABASTECIMIENTO DE RENTAS";
        url += "printRentsSto.php";
      }

      showModal(url, titleModal);

      function showModal(url, titleModal) {
        document.getElementById("modalTitleFull").innerText = titleModal;
        document.getElementById("modalBodyFull").innerHTML =
          `<embed src="` +
          url +
          `" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">`;
        $("#modalFull").modal("show");
      }
    });
  });
}

// Mapas para Leaflet
const mapTheme1 = "https://tile.openstreetmap.org/{z}/{x}/{y}.png";
const mapTheme2 =
  "https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.png";
const mapTheme3 =
  "https://{s}.tile.thunderforest.com/neighbourhood/{z}/{x}/{y}{r}.png?apikey={apikey}";
