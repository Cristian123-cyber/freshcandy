import { checkAuthStatus } from "./authHelper.js";

async function init() {
  cargarTiposSugerencias();
  
  const formulario = document.getElementById("formulario-sugerencias");
  const radioButtons = document.querySelectorAll(
    'input[name="tipo-sugerencia"]'
  );
  
  // Efectos de campos de formulario
  const inputs = document.querySelectorAll(".form-input");
  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("focused");
    });
    
    input.addEventListener("blur", function () {
      this.parentElement.classList.remove("focused");
    });
  });
  
  // Efecto visual para los radio buttons
  radioButtons.forEach((radio) => {
    radio.addEventListener("change", function () {
      // Remover la clase activa de todos los labels
      document.querySelectorAll(".radio-option label").forEach((label) => {
        label.classList.remove("active");
      });

      // Agregar la clase activa al label seleccionado
      if (this.checked) {
        this.nextElementSibling.classList.add("active");
        // Ocultar mensaje de error al seleccionar un radio button
        const errorElement = document.getElementById("tipo-sugerencia-error");
        if (errorElement) {
          errorElement.classList.remove("active");
          // Remover la clase de error del grupo de radio buttons
          document.querySelector(".radio-group").classList.remove("error");
        }
      }
    });
  });

  // Validación en tiempo real para los campos de texto
  const textInputs = document.querySelectorAll("input[type='text'], textarea");
  textInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const errorElement = document.getElementById(`${this.id}-error`);
      if (errorElement && errorElement.classList.contains("active")) {
        errorElement.classList.remove("active");
        this.classList.remove("error");
      }
    });
  });

  // Cargar tipos de sugerencias al iniciar

  // Manejar el envío del formulario
  formulario.addEventListener("submit", handleSubmitSugerencia);
}

function validateDataSugerencia(data) {
  const errors = [];

  // Validar título
  if (!data.titulo) {
    errors.push({ field: "titulo-error", message: "El título es requerido" });
  } else if (data.titulo.length < 3) {
    errors.push({
      field: "titulo-error",
      message: "El título debe tener al menos 3 caracteres",
    });
  } else if (data.titulo.length > 100) {
    errors.push({
      field: "titulo-error",
      message: "El título no debe exceder los 100 caracteres",
    });
  }

  // Validar tipo de sugerencia
  const radioButtons = document.querySelectorAll(
    'input[name="tipo-sugerencia"]'
  );
  const radioSelected = Array.from(radioButtons).some((radio) => radio.checked);

  if (!radioSelected) {
    errors.push({
      field: "tipo-sugerencia-error",
      message: "Debe seleccionar un tipo de sugerencia",
    });
  } else {
    const selectedValue = document.querySelector(
      'input[name="tipo-sugerencia"]:checked'
    ).value;
    if (
      !selectedValue ||
      isNaN(selectedValue) ||
      parseInt(selectedValue) <= 0
    ) {
      errors.push({
        field: "tipo-sugerencia-error",
        message: "El tipo de sugerencia seleccionado no es válido",
      });
    }
  }

  // Validar mensaje
  if (!data.mensaje) {
    errors.push({ field: "mensaje-error", message: "El mensaje es requerido" });
  } else {
    const mensajeLength = data.mensaje.trim().length;
    if (mensajeLength < 10) {
      errors.push({
        field: "mensaje-error",
        message: "El mensaje debe tener al menos 10 caracteres",
      });
    } else if (mensajeLength > 1000) {
      errors.push({
        field: "mensaje-error",
        message: "El mensaje no debe exceder los 1000 caracteres",
      });
    }
  }

  return errors.length === 0 ? true : errors;
}

function showErrors(errors) {
  // Limpiar errores previos
  clearErrors();

  errors.forEach((error) => {
    const errorElement = document.getElementById(error.field);
    if (errorElement) {
      errorElement.textContent = error.message;
      errorElement.classList.add("active");

      // Agregar clase de error al input correspondiente
      const inputId = error.field.replace("-error", "");
      const input = document.getElementById(inputId);

      if (input) {
        input.classList.add("error");
      } else if (error.field === "tipo-sugerencia-error") {
        // Si es error de radio buttons, agregar la clase al grupo
        const radioGroup = document.querySelector(".radio-group");
        if (radioGroup) {
          radioGroup.classList.add("error");
          // Asegurarse de que el mensaje de error sea visible
          errorElement.style.display = "block";
        }
      }
    }
  });
}

function clearErrors() {
  // Limpiar clases de error de todos los inputs y textareas
  document.querySelectorAll("input, textarea").forEach((input) => {
    input.classList.remove("error");
  });

  // Limpiar clase de error del grupo de radio buttons
  const radioGroup = document.querySelector(".radio-group");
  if (radioGroup) {
    radioGroup.classList.remove("error");
  }

  // Ocultar todos los mensajes de error
  document.querySelectorAll(".form-error").forEach((error) => {
    error.classList.remove("active");
    error.textContent = "";
  });
}

function handleSubmitSugerencia(event) {
  event.preventDefault();

  // Obtener valores
  const data = {
    titulo: document.getElementById("titulo").value.trim(),
    tipo: document.querySelector('input[name="tipo-sugerencia"]:checked')
      ?.value,
    mensaje: document.getElementById("mensaje").value.trim(),
  };

  // Validar datos
  const validationResult = validateDataSugerencia(data);

  // Manejar resultados
  if (validationResult === true) {
    // Enviar formulario si no hay errores
    enviarSugerencia(data);
  } else {
    // Mostrar errores específicos
    showErrors(validationResult);
  }
}

async function enviarSugerencia(data) {
  //Animar el boton de enviar
  const btnEnviar = document.getElementById("btn-enviar-sugerencia");
  btnEnviar.disabled = true;
  btnEnviar.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Enviando sugerencia...';

  const autenticado = await checkAuthStatus(2);

  if (!autenticado) {
    console.error("Error al autenticar");
    setTimeout(() => {
      btnEnviar.disabled = false;
      btnEnviar.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar sugerencia';
      showNotification("Error de autenticación", "error");
    }, 500);
    return;
  }

  const dataToSend = {
    titulo: data.titulo,
    cuerpo: data.mensaje,
    idTipoSugerencia: data.tipo,
  };

  fetch(
    "/../../controllers/SugerenciasController.php?action=insertar",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(dataToSend),
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        console.error("Error al enviar la sugerencia:", data.message);
        setTimeout(() => {
          btnEnviar.disabled = false;
          btnEnviar.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar sugerencia';
          showNotification("Error al enviar la sugerencia", "error");
        }, 500);
        return;
      }

      setTimeout(() => {
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar sugerencia';
        showNotification("Sugerencia enviada con éxito", "success");
      }, 500);

      //Limpiar formulario
      document.getElementById("formulario-sugerencias").reset();
    })
    .catch((error) => {
      console.error("Error al enviar la sugerencia:", error);
      setTimeout(() => {
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar sugerencia';
        showNotification("Error al enviar la sugerencia", "error");
      }, 500);
    });
}

function cargarTiposSugerencias() {
  fetch("/../../controllers/SugerenciasController.php?action=getTipos", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const radioGroup = document.querySelector(".radio-group");
        radioGroup.innerHTML = ""; // Clear existing options

        data.data.tiposSugerencias.forEach((tipo) => {
          const iconClass = getIconForTipo(tipo.nombre_tipo);
          const div = document.createElement("div");
          div.className = "radio-option";
          div.innerHTML = `
            <input type="radio" id="tipo-${tipo.id_tipo}" name="tipo-sugerencia" value="${tipo.id_tipo}">
            <label for="tipo-${tipo.id_tipo}">
              <i class="fas ${iconClass}"></i>
              <span>${tipo.nombre_tipo}</span>
            </label>
          `;
          radioGroup.appendChild(div);
        });
      } else {
        console.error("Error al cargar tipos de sugerencias:", data.message);
        showNotification("Error al cargar tipos de sugerencias:", "error");
      }
    })
    .catch((error) => {
      console.error("Error al cargar tipos de sugerencias:", error);
      showNotification("Error al cargar tipos de sugerencias:", "error");
    });
}

function getIconForTipo(nombreTipo) {
  const tipo = nombreTipo.toLowerCase();
  if (tipo.includes("producto")) return "fa-ice-cream";
  if (tipo.includes("servicio")) return "fa-concierge-bell";
  if (tipo.includes("experiencia")) return "fa-star";
  return "fa-lightbulb";
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", init);
