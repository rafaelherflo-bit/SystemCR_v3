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
  let formulario_actual = this; // Referencia al form que disparó el evento

  let encabezados = new Headers();
  let config = {
    method: method,
    headers: encabezados,
    mode: "cors",
    cache: "no-cache",
    body: data,
  };

  // Definimos títulos por defecto para evitar 'undefined'
  let titulos = {
    save: "Guardando",
    enable: "Habilitando",
    disable: "Deshabilitando",
    delete: "Borrando",
    update: "Actualizando",
    upload: "Subiendo",
    search: "Buscando",
    finish: "Finalizando",
    start: "Iniciando Registro",
  };

  let titulo_alerta = titulos[tipo] || "Atención";
  let texto_alerta = "¡Deseas realizar la operación?";

  if (tipo === "search") {
    texto_alerta =
      "Se eliminará el término de búsqueda y tendrás que escribir uno nuevo.";
  } else if (tipo === "loans") {
    texto_alerta = "¿Deseas remover los datos seleccionados?";
  }

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
        .then((respuesta) => {
          // Si el servidor responde 200, pero la respuesta está vacía, esto fallará
          return respuesta.text();
        })
        .then((texto) => {
          // Limpiamos espacios en blanco o errores invisibles al inicio del texto
          texto = texto.trim();

          try {
            const data = JSON.parse(texto);
            return alertas_ajax(data, formulario_actual);
          } catch (e) {
            Swal.fire({
              title: "Error de Respuesta",
              html: `
                                <div style="text-align:left; font-size:11px; background:#fff5f5; color:#c53030; padding:10px; border:1px solid #feb2b2; border-radius:5px; overflow-x:auto;">
                                    <strong>Respuesta no válida del servidor:</strong><br><br>
                                    ${texto || "El servidor respondió con una cadena vacía."}
                                </div>`,
              icon: "error",
            });
          }
        })
        .catch((error) => {
          Swal.fire({
            title: "Error de Red",
            text: "Ocurrió un problema al conectar con el servidor.",
            icon: "warning",
          });
        });
    }
  });
}

// Actualizamos alertas_ajax para recibir el formulario específico
function alertas_ajax(alerta, formulario = null) {
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
      if (result.isConfirmed && formulario) {
        formulario.reset();
      }
    });
  } else if (alerta.Alerta === "redireccionar") {
    Swal.fire({
      icon: alerta.Tipo ? alerta.Tipo : "success",
      title: alerta.Titulo ? alerta.Titulo : "¡Redireccionando!",
      text: alerta.Texto ? alerta.Texto : "Redirigiendo en 2 segundos...",
      timer: 2000, // 2000 milisegundos = 2 segundos
      timerProgressBar: true, // Muestra una barrita de progreso al pie
      showConfirmButton: false, // Oculta el botón de aceptar
      willClose: () => {
        // Aquí es donde ocurre la redirección al cerrarse
        window.location.href = alerta.url;
      },
    });
  }
}
