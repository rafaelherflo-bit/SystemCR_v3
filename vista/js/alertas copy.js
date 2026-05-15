const formularios_ajax = document.querySelectorAll(".FormularioAjax");
formularios_ajax.forEach((formularios) => {
  formularios.addEventListener("submit", enviar_formulario_ajax);
});

function enviar_formulario_ajax(e) {
  e.preventDefault();

  let data = new FormData(this);
  let method = this.getAttribute("method");
  let action = this.getAttribute("action");
  let tipo = this.getAttribute("data-form");

  let encabezados = new Headers();

  let config = {
    method: method,
    headers: encabezados,
    mode: "cors",
    cache: "no-cache",
    body: data,
  };

  let titulo_alerta;
  var texto_alerta = "Deseas Continuar??";

  if (tipo === "save") {
    titulo_alerta = "Guardando";
  } else if (tipo === "enable") {
    titulo_alerta = "Hanilitando";
  } else if (tipo === "disable") {
    titulo_alerta = "Deshabilitando";
  } else if (tipo === "delete") {
    titulo_alerta = "Borrando";
  } else if (tipo === "update") {
    titulo_alerta = "Actualizando";
  } else if (tipo === "search") {
    titulo_alerta = "Buscando";
    texto_alerta =
      "Se elminara el termino de busqueda y tendras que escribir uno nuevo.";
  } else if (tipo === "finish") {
    titulo_alerta = "Finalizando";
  } else if (tipo === "loans") {
    titulo_alerta = "Removiendo";
    texto_alerta = "Deseas remover los datos seleccionados?";
  } else if (tipo === "startCob") {
    titulo_alerta = "Iniciando";
    texto_alerta = "Deseas Iniciar Nueva Cobranza?";
  } else if (tipo === "active_AlmM") {
    titulo_alerta = "Iniciando";
    texto_alerta = "Deseas Activar el movimiento?";
  } else if (tipo === "start") {
    titulo_alerta = "Iniciando Registro";
    texto_alerta = "Deseas Continuar?";
  } else if (tipo === "editCob") {
    titulo_alerta = "Editando";
    texto_alerta = "Deseas Editar esta Cobranza?";
  } else if (tipo === "cobCadd") {
    titulo_alerta = "Agregando";
    texto_alerta = "Agregar Cobro?";
  } else if (tipo === "cobPadd") {
    titulo_alerta = "Agregando";
    texto_alerta = "Agregar Pago?";
  } else if (tipo === "AlmDMadd") {
    titulo_alerta = "Agregando Producto";
    texto_alerta = "Deseas Continuar?";
  } else if (tipo === "startRep") {
    titulo_alerta = "Iniciando";
  } else if (tipo === "cotMnueva") {
    titulo_alerta = "Iniciando";
    texto_alerta = "Deseas Iniciar Nueva Cotizacion?";
  } else {
    texto_alerta = "Quieres realizar esta operacion?";
    texto_alerta = "";
  }

  // BACKUP DE RESPUESTAS SWEETALERT
  //   Swal.fire({
  //     title: titulo_alerta,
  //     text: texto_alerta,
  //     icon: "question",
  //     confirmButtonText: "Aceptar",
  //     confirmButtonColor: "#3085d6",
  //     showCancelButton: true,
  //     cancelButtonText: "Cancelar",
  //     cancelButtonColor: "#d33",
  //   }).then((result) => {
  //     if (result.isConfirmed) {
  //       fetch(action, config)
  //         .then((respuesta) => respuesta.json())
  //         .then((respuesta) => {
  //           return alertas_ajax(respuesta);
  //         });
  //     }
  //   });
  // }

  Swal.fire({
    title: titulo_alerta,
    text: texto_alerta,
    icon: "question",
    confirmButtonText: "Aceptar",
    confirmButtonColor: "#3085d6",
    showCancelButton: true,
    cancelButtonText: "Cancelar",
    cancelButtonColor: "#d33",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(action, config)
    .then(respuesta => {
        // Si el servidor responde con error 500 o 404, lanzamos error
        if (!respuesta.ok) throw new Error("Error en la respuesta del servidor");
        return respuesta.text(); // <--- IMPORTANTE: Leemos como texto primero
    })
    .then(texto => {
        try {
            // Intentamos convertir el texto a objeto JSON
            const data = JSON.parse(texto);
            return alertas_ajax(data);
        } catch (e) {
            // Si falla el parseo, el servidor mandó un error técnico (HTML)
            // Mostramos el error real en el SweetAlert
            Swal.fire({
                title: "Error Técnico detectado",
                html: `
                    <div style="text-align:left; font-size:11px; background:#fff5f5; color:#c53030; padding:10px; border:1px solid #feb2b2; border-radius:5px; overflow-x:auto;">
                        <strong>El servidor no respondió con JSON:</strong><br><br>
                        ${texto}
                    </div>`,
                icon: "error",
                confirmButtonText: "Cerrar"
            });
            console.error("Error de formato:", texto);
        }
    })
    .catch(error => {
        Swal.fire({
            title: "Error de Red",
            text: "No se pudo conectar con el controlador. Verifica tu conexión.",
            icon: "warning"
        });
    });
    }
  });
}

function alertas_ajax(alerta) {
  if (alerta.Alerta === "simple") {
    Swal.fire({
      title: alerta.Titulo,
      text: alerta.Texto,
      icon: alerta.Tipo,
      confirmButtonText: "Aceptar",
    });
  } else if (alerta.Alerta === "recargar") {
    Swal.fire({
      title: alerta.Titulo,
      text: alerta.Texto,
      icon: alerta.Tipo,
      confirmButtonText: "OK !!",
      confirmButtonColor: "#3085d6",
    }).then((result) => {
      if (result.isConfirmed) {
        location.reload();
      }
    });
  } else if (alerta.Alerta === "limpiar") {
    Swal.fire({
      title: alerta.Titulo,
      text: alerta.Texto,
      icon: alerta.Tipo,
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#3085d6",
      showCancelButton: true,
      cancelButtonText: "Cancelar",
      cancelButtonColor: "#d33",
    }).then((result) => {
      if (result.isConfirmed) {
        document.querySelector(".FormularioAjax").reset();
      }
    });
  } else if (alerta.Alerta === "redireccionar") {
    window.location.href = alerta.url;
  }
}
