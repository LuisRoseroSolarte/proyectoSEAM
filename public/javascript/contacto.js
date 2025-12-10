document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("contactForm");
  const mensajeTextarea = document.getElementById("mensaje");
  const caracteresRestantes = document.getElementById("caracteresRestantes");
  const btnEnviar = document.getElementById("btnEnviar");
  const mensajeExito = document.getElementById("mensajeExito");
  const mensajeError = document.getElementById("mensajeError");

  // Contador de caracteres para el mensaje
  mensajeTextarea.addEventListener("input", function () {
    const restantes = 1000 - this.value.length;
    caracteresRestantes.textContent = restantes;

    if (restantes < 100) {
      caracteresRestantes.style.color = "#dc3545";
    } else {
      caracteresRestantes.style.color = "#6c757d";
    }
  });

  // Manejo del envío del formulario
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Ocultar mensajes previos
    mensajeExito.classList.add("d-none");
    mensajeError.classList.add("d-none");

    // Deshabilitar botón y mostrar carga
    btnEnviar.disabled = true;
    btnEnviar.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

    // Obtener datos del formulario
    const formData = new FormData(form);

    // Enviar datos por AJAX
    fetch("contacto.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Mostrar mensaje de éxito
          mensajeExito.classList.remove("d-none");
          mensajeExito.scrollIntoView({ behavior: "smooth", block: "center" });

          // Limpiar formulario
          form.reset();
          caracteresRestantes.textContent = "1000";
          caracteresRestantes.style.color = "#6c757d";
        } else {
          // Mostrar mensaje de error
          mensajeError.classList.remove("d-none");
          mensajeError.scrollIntoView({ behavior: "smooth", block: "center" });
          mensajeError.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error:</strong> ${data.message}`;
        }
      })
      .catch((error) => {
        // Error de conexión
        mensajeError.classList.remove("d-none");
        mensajeError.scrollIntoView({ behavior: "smooth", block: "center" });
        console.error("Error:", error);
      })
      .finally(() => {
        // Rehabilitar botón
        btnEnviar.disabled = false;
        btnEnviar.innerHTML =
          '<i class="bi bi-send-fill me-2"></i>Enviar Mensaje';
      });
  });
  // mensajes de formulario

  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("contactForm");
    const mensajeTextarea = document.getElementById("mensaje");
    const caracteresRestantes = document.getElementById("caracteresRestantes");
    const btnEnviar = document.getElementById("btnEnviar");
    const mensajeExito = document.getElementById("mensajeExito");
    const mensajeError = document.getElementById("mensajeError");

    // Contador de caracteres para el mensaje
    mensajeTextarea.addEventListener("input", function () {
      const restantes = 1000 - this.value.length;
      caracteresRestantes.textContent = restantes;

      if (restantes < 100) {
        caracteresRestantes.style.color = "#dc3545";
      } else {
        caracteresRestantes.style.color = "#6c757d";
      }
    });

    // Manejo del envío del formulario
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      console.log("Formulario enviado - iniciando proceso...");

      // Ocultar mensajes previos
      mensajeExito.classList.add("d-none");
      mensajeError.classList.add("d-none");

      // Deshabilitar botón y mostrar carga
      btnEnviar.disabled = true;
      btnEnviar.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

      // Obtener datos del formulario
      const formData = new FormData(form);

      console.log("Datos del formulario:", {
        nombre: formData.get("nombre"),
        email: formData.get("email"),
        asunto: formData.get("asunto"),
        mensaje: formData.get("mensaje"),
      });

      // Enviar datos por AJAX
      fetch("contacto.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          console.log("Respuesta recibida:", response);
          console.log("Status:", response.status);
          console.log("Status Text:", response.statusText);

          // Intentar leer como texto primero para ver qué devuelve
          return response.text();
        })
        .then((text) => {
          console.log("Respuesta como texto:", text);

          // Intentar parsear como JSON
          try {
            const data = JSON.parse(text);
            console.log("Datos parseados:", data);

            if (data.success) {
              // Mostrar mensaje de éxito
              mensajeExito.classList.remove("d-none");
              mensajeExito.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i><strong>¡Mensaje enviado!</strong> ${data.message}`;
              mensajeExito.scrollIntoView({
                behavior: "smooth",
                block: "center",
              });

              // Limpiar formulario
              form.reset();
              caracteresRestantes.textContent = "1000";
              caracteresRestantes.style.color = "#6c757d";

              // Limpiar clases de validación
              const inputs = form.querySelectorAll("input, select, textarea");
              inputs.forEach((input) => {
                input.classList.remove("is-valid", "is-invalid");
              });
            } else {
              // Mostrar mensaje de error
              mensajeError.classList.remove("d-none");
              mensajeError.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error:</strong> ${data.message}`;
              mensajeError.scrollIntoView({
                behavior: "smooth",
                block: "center",
              });
            }
          } catch (e) {
            console.error("Error al parsear JSON:", e);
            console.error("Texto recibido:", text);

            // Mostrar el error
            mensajeError.classList.remove("d-none");
            mensajeError.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error:</strong> La respuesta del servidor no es válida. Revisa la consola del navegador para más detalles.`;
            mensajeError.scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
          }
        })
        .catch((error) => {
          // Error de conexión
          console.error("Error de conexión:", error);
          mensajeError.classList.remove("d-none");
          mensajeError.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error de conexión:</strong> No se pudo conectar con el servidor. Verifica que el archivo procesar_contacto.php existe y está accesible.`;
          mensajeError.scrollIntoView({
            behavior: "smooth",
            block: "center",
          });
        })
        .finally(() => {
          // Rehabilitar botón
          btnEnviar.disabled = false;
          btnEnviar.innerHTML =
            '<i class="bi bi-send-fill me-2"></i>Enviar Mensaje';
          console.log("Proceso finalizado");
        });
    });

    // Validación en tiempo real
    const inputs = form.querySelectorAll("input, select, textarea");
    inputs.forEach((input) => {
      input.addEventListener("blur", function () {
        if (this.value.trim() === "" && this.hasAttribute("required")) {
          this.classList.add("is-invalid");
          this.classList.remove("is-valid");
        } else if (this.value.trim() !== "") {
          this.classList.remove("is-invalid");
          this.classList.add("is-valid");
        }
      });
    });
  });

  //mensajes de formulario

  // Validación en tiempo real
  const inputs = form.querySelectorAll("input, select, textarea");
  inputs.forEach((input) => {
    input.addEventListener("blur", function () {
      if (this.value.trim() === "" && this.hasAttribute("required")) {
        this.classList.add("is-invalid");
      } else {
        this.classList.remove("is-invalid");
        this.classList.add("is-valid");
      }
    });
  });
});
