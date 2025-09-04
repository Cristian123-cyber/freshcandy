/**
 * FreshCandy - Mi Perfil (Rediseñado)
 * Funcionalidad completa para perfil de usuario
 */

import { logout, checkAuthStatus } from "./authHelper.js";

// Función para animar campos inválidos
function animateInvalidInput(inputElement) {
  inputElement.style.borderColor = "var(--primary-color)";
  inputElement.style.animation = "shake 0.3s";

  setTimeout(() => {
    inputElement.style.animation = "";
  }, 300);
}

// Función para obtener estadísticas del usuario
async function obtenerEstadisticasUsuario() {
  try {
    const response = await fetch(
      "/../controllers/StatsController.php?action=getEstadisticasCliente",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      }
    );

    // Primero obtenemos el texto de la respuesta para debug
    const responseText = await response.text();

    // Intentamos parsear el texto como JSON
    let data;
    try {
      data = JSON.parse(responseText);
    } catch (parseError) {
      console.error("Respuesta del servidor:", responseText);
      throw new Error("El servidor devolvió una respuesta inválida");
    }

    if (!data.success) {
      throw new Error(data.message || "Error en estadísticas");
    }

    return data.data;
  } catch (error) {
    console.error("Error al obtener estadísticas:", error);
    showNotification("Error al cargar estadísticas: " + error.message, "error");
    return null;
  }
}

// Función para renderizar estadísticas
function renderizarEstadisticas(estadisticas) {
  const statsContainer = document.getElementById("hero-stats");
  if (!statsContainer) return;

  // Limpiar el contenedor
  statsContainer.innerHTML = "";

  // Crear elementos de estadísticas
  const pedidosStat = document.createElement("div");
  pedidosStat.className = "hero-stat-item";
  pedidosStat.innerHTML = `
        <div class="hero-stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="hero-stat-content">
            <div class="hero-stat-value">${estadisticas.total_pedidos || "No hay pedidos registrados"}</div>
            <div class="hero-stat-label">Pedidos realizados</div>
        </div>
    `;

  const favoritoStat = document.createElement("div");
  favoritoStat.className = "hero-stat-item";
  favoritoStat.innerHTML = `
        <div class="hero-stat-icon">
            <i class="fas fa-heart"></i>
        </div>
        <div class="hero-stat-content">
            <div class="hero-stat-value">${estadisticas.producto_favorito || "No hay pedidos registrados"}</div>
            <div class="hero-stat-label">Sabor Favorito</div>
        </div>
    `;

  // Añadir elementos al contenedor
  statsContainer.appendChild(pedidosStat);
  statsContainer.appendChild(favoritoStat);
}

// Función para validar formulario de datos personales
function validarFormularioDatos() {
  let isValid = true;
  const telefono = document.getElementById("telefono");
  const direccion = document.getElementById("direccion");

  // Validar teléfono
  if (telefono.value.trim() === "") {
    document.getElementById("telefono-error").classList.add("active");
    animateInvalidInput(telefono);
    isValid = false;
  } else if (!/^\d{7,15}$/.test(telefono.value.trim())) {
    document.getElementById("telefono-error").textContent =
      "Debe tener 9 dígitos";
    document.getElementById("telefono-error").classList.add("active");
    animateInvalidInput(telefono);
    isValid = false;
  }

  // Validar dirección
  if (direccion.value.trim() === "") {
    document.getElementById("direccion-error").classList.add("active");
    animateInvalidInput(direccion);
    isValid = false;
  }

  return isValid;
}

// Función para validar formulario de contraseña
function validarFormularioPassword() {
  let isValid = true;
  const currentPass = document.getElementById("current-password");
  const newPass = document.getElementById("new-password");
  const confirmPass = document.getElementById("confirm-password");

  // Validar contraseña actual
  if (currentPass.value.trim() === "") {
    document.getElementById("current-password-error").classList.add("active");
    animateInvalidInput(currentPass);
    isValid = false;
  }

  // Validar nueva contraseña
  if (newPass.value.trim() === "") {
    document.getElementById("new-password-error").textContent =
      "Campo obligatorio";
    document.getElementById("new-password-error").classList.add("active");
    animateInvalidInput(newPass);
    isValid = false;
  } else if (newPass.value.length < 8) {
    document.getElementById("new-password-error").textContent =
      "Mínimo 8 caracteres";
    document.getElementById("new-password-error").classList.add("active");
    animateInvalidInput(newPass);
    isValid = false;
  } else if (!/[A-Z]/.test(newPass.value)) {
    document.getElementById("new-password-error").textContent = "1 mayúscula";
    document.getElementById("new-password-error").classList.add("active");
    animateInvalidInput(newPass);
    isValid = false;
  } else if (!/[a-z]/.test(newPass.value)) {
    document.getElementById("new-password-error").textContent = "1 minúscula";
    document.getElementById("new-password-error").classList.add("active");
    animateInvalidInput(newPass);
    isValid = false;
  } else if (!/[0-9]/.test(newPass.value)) {
    document.getElementById("new-password-error").textContent = "1 número";
    document.getElementById("new-password-error").classList.add("active");
    animateInvalidInput(newPass);
    isValid = false;
  } else if (!/[^A-Za-z0-9]/.test(newPass.value)) {
    document.getElementById("new-password-error").textContent =
      "1 carácter especial";
    document.getElementById("new-password-error").classList.add("active");
    animateInvalidInput(newPass);
    isValid = false;
  }

  // Validar confirmación
  if (confirmPass.value !== newPass.value) {
    document.getElementById("confirm-password-error").classList.add("active");
    animateInvalidInput(confirmPass);
    isValid = false;
  }

  return isValid;
}

function limpiarErroresInput(input) {
  const errorElement = document.getElementById(`${input.id}-error`);
  if (errorElement) {
    errorElement.classList.remove("active");
    errorElement.textContent = "";
  }
  input.classList.remove("invalid");
}

// Agregar event listeners para limpiar errores al escribir
document
  .getElementById("current-password")
  .addEventListener("input", function () {
    limpiarErroresInput(this);
  });

document.getElementById("new-password").addEventListener("input", function () {
  limpiarErroresInput(this);
});

document
  .getElementById("confirm-password")
  .addEventListener("input", function () {
    limpiarErroresInput(this);
  });

document.getElementById("telefono").addEventListener("input", function () {
  limpiarErroresInput(this);
});

document.getElementById("direccion").addEventListener("input", function () {
  limpiarErroresInput(this);
});

// Función para enviar datos personales
async function enviarDatosPersonales() {
  try {
    const response = await fetch(
      "/../controllers/UserController.php?action=updateProfile",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          phone: document.getElementById("telefono").value.trim(),
          address: document.getElementById("direccion").value.trim(),
        }),
      }
    );

    const data = await response.json();
    if (!data.success) throw new Error(data.message);

    showNotification("Datos actualizados correctamente", "success");
    console.log(data);
    return true;
  } catch (error) {
    showNotification(error.message || "Error al actualizar datos", "error");
    return false;
  }
}

// Función para cambiar contraseña
async function cambiarContraseña() {
  try {
    const response = await fetch(
      "/../controllers/UserController.php?action=changePassword",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          current_password: document
            .getElementById("current-password")
            .value.trim(),
          new_password: document.getElementById("new-password").value.trim(),
          confirm_password: document
            .getElementById("confirm-password")
            .value.trim(),
        }),
      }
    );

    const data = await response.json();
    if (!data.success) throw new Error(data.message);

    showNotification("Contraseña cambiada correctamente", "success");
    console.log(data);
    document.getElementById("password-form").reset();
    return true;
  } catch (error) {
    showNotification(error.message || "Error al cambiar contraseña", "error");
    return false;
  }
}

// Función para manejar el envío del formulario activo
async function manejarEnvioFormulario() {
  const activeTab = document.querySelector(".tab-content.active");
  if (!activeTab) return false;

  let isValid = false;
  let success = false;

  const autenticado = await checkAuthStatus(2);

  if (!autenticado) {
    showNotification("Error de autenticación", "error");
    return false;
  }

  // Validar según el formulario activo
  if (activeTab.id === "info-tab") {
    isValid = validarFormularioDatos();
    if (isValid) success = await enviarDatosPersonales();
  } else if (activeTab.id === "password-tab") {
    isValid = validarFormularioPassword();
    if (isValid) success = await cambiarContraseña();
  }

  return success;
}

// Función para mostrar/ocultar contraseñas
function togglePasswordVisibility(inputElement, iconElement) {
  if (inputElement.type === "password") {
    inputElement.type = "text";
    iconElement.className = "fas fa-eye-slash toggle-password";
  } else {
    inputElement.type = "password";
    iconElement.className = "fas fa-eye toggle-password";
  }
}

// Función para cargar datos del perfil
async function cargarDatosPerfil() {
  try {
    const response = await fetch(
      "/../controllers/AuthController.php?action=getUserData",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
      }
    );

    const data = await response.json();
    if (!data.success) throw new Error(data.message);

    setearInputs(data.data.user);
    await cargarEstadisticas();
  } catch (error) {
    console.error("Error al cargar perfil:", error);
    showNotification("Error al cargar datos del perfil", "error");
  }
}

// Función para cargar estadísticas
async function cargarEstadisticas() {
  const statsData = await obtenerEstadisticasUsuario();
  renderizarEstadisticas(statsData);
}

// Función para setear datos en los inputs
function setearInputs(userData) {
  document.getElementById("nombre-display").textContent =
    userData.name || "No disponible";
  document.getElementById("cedula-display").textContent =
    userData.cedula || "No disponible";
  document.getElementById("email-display").textContent =
    userData.email || "No disponible";
  document.getElementById("telefono-display").textContent =
    userData.telefono || "No disponible";
  document.getElementById("telefono").value = userData.telefono || "";
  document.getElementById("direccion").value = userData.direccion || "";
}

// Función para cerrar sesión
function cerrarSesion(e) {
  e.target.disabled = true;
  e.target.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Cerrando sesión...';

  setTimeout(() => {
    e.target.disabled = false;
    e.target.innerHTML = '<i class="fas fa-sign-out-alt"></i> Cerrar sesión';
    logout();
  }, 1500);
}

// Función para limpiar inputs de la pestaña actual
function limpiarInputsPestañaActual() {
  const activeTab = document.querySelector(".tab-content.active");
  if (!activeTab) return;

  // Solo limpiar si estamos en la pestaña de contraseña
  if (activeTab.id === "password-tab") {
    const inputs = activeTab.querySelectorAll("input");
    inputs.forEach((input) => {
      input.value = "";
      limpiarErroresInput(input);
    });
  }
}

// Función para limpiar inputs de una pestaña específica
function limpiarInputsPestaña(tabId) {
  const tab = document.getElementById(tabId);
  if (!tab) return;

  // Solo limpiar si es la pestaña de contraseña
  if (tabId === "password-tab") {
    const inputs = tab.querySelectorAll("input");
    inputs.forEach((input) => {
      input.value = "";
      limpiarErroresInput(input);
    });
  }
}

// Inicialización
document.addEventListener("DOMContentLoaded", () => {
  // Elementos del DOM
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");
  const saveButton = document.getElementById("save-button");
  const logoutButton = document.getElementById("logout-button");
  const togglePasswordIcons = document.querySelectorAll(".toggle-password");
  const formInputs = document.querySelectorAll("input, textarea");

  // Cargar datos iniciales
  cargarDatosPerfil();

  // Manejador del botón guardar
  if (saveButton) {
    saveButton.addEventListener("click", async (e) => {
      e.preventDefault();
      saveButton.disabled = true;
      saveButton.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Guardando...';

      await manejarEnvioFormulario();

      saveButton.disabled = false;
      saveButton.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
    });
  }

  // Manejador de pestañas
  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const tabId = button.getAttribute("data-tab");

      tabButtons.forEach((btn) => btn.classList.remove("active"));
      tabContents.forEach((content) => content.classList.remove("active"));

      // Limpiar inputs de la pestaña actual
      limpiarInputsPestañaActual();

      // Limpiar inputs de la pestaña destino
      limpiarInputsPestaña(tabId);

      button.classList.add("active");
      document.getElementById(tabId).classList.add("active");
    });
  });

  // Manejador de mostrar/ocultar contraseña
  togglePasswordIcons.forEach((icon) => {
    icon.addEventListener("click", () => {
      const targetId = icon.getAttribute("data-target");
      const inputElement = document.getElementById(targetId);
      togglePasswordVisibility(inputElement, icon);
    });
  });

  // Manejador de logout
  if (logoutButton) {
    logoutButton.addEventListener("click", cerrarSesion);
  }
});
