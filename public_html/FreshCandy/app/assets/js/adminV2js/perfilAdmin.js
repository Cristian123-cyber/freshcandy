/**
 * Admin Profile Component
 *
 * Componente lateral emergente (offcanvas) para gestionar el perfil de administrador.
 * Permite editar nombre de usuario, correo electrónico y contraseña.
 */

// Función para mostrar notificaciones
function showNotification(message, type) {
  // Crear elemento de notificación
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.innerHTML = `
      <div class="notification-content">
          <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
          <span>${message}</span>
      </div>
      `;

  // Añadir al DOM
  document.body.appendChild(notification);

  // Mostrar con animación
  setTimeout(() => {
      notification.classList.add('show');
  }, 10);

  // Ocultar después de 3 segundos
  setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => {
          notification.remove();
      }, 300);
  }, 3000);
}
document.addEventListener("DOMContentLoaded", () => {
  // Referencias a elementos del DOM
  const adminProfileBtn = document.getElementById("admin-profile-btn");
  const profilePanel = document.getElementById("admin-profile-panel");
  const closePanel = document.getElementById("close-panel");
  const cancelBtn = document.getElementById("cancel-btn");
  const profileOverlay = document.getElementById("profile-overlay");
  const profileForm = document.getElementById("profile-form");
  const securityForm = document.getElementById("security-form");
  const successAlert = document.getElementById("success-alert");
  const passwordInputs = document.querySelectorAll(".password-input");
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");
  const saveButton = document.querySelector("#save-btn");

  // Datos iniciales del perfil
  let initialProfileData = {
    username: "",
    email: "",
  };

  /**
   * Abre el panel lateral
   */
  const openPanel = async () => {
    profilePanel.classList.add("active");
    profileOverlay.classList.add("active");
    document.body.style.overflow = "hidden"; // Prevenir scroll en el body

    // Cargar datos del perfil
    await cargarDatosPerfil();

    // Resetear el formulario a los valores iniciales
    document.getElementById("username").value = initialProfileData.username;
    document.getElementById("email").value = initialProfileData.email;
    document.getElementById("current-password").value = "";
    document.getElementById("password").value = "";
    document.getElementById("confirm-password").value = "";

    // Limpiar mensajes de error
    clearErrors();
  };

  /**
   * Cierra el panel lateral
   */
  const closePanelF = () => {
    profilePanel.classList.remove("active");
    profileOverlay.classList.remove("active");
    document.body.style.overflow = ""; // Restaurar scroll en el body
  };

  /**
   * Muestra una alerta de éxito
   * @param {string} message - Mensaje a mostrar
   */
  const showSuccessAlert = (message) => {
    successAlert.querySelector("span").textContent = message;
    successAlert.classList.add("show");

    // Ocultar la alerta después de 3 segundos
    setTimeout(() => {
      successAlert.classList.remove("show");
    }, 3000);
  };

  /**
   * Muestra un mensaje de error en un campo específico
   * @param {string} fieldId - ID del campo
   * @param {string} message - Mensaje de error
   */
  const showError = (fieldId, message) => {
    const errorElement = document.getElementById(`${fieldId}-error`);
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.classList.add("active");
    }
  };

  /**
   * Limpia todos los mensajes de error
   */
  const clearErrors = () => {
    document.querySelectorAll(".form-error").forEach((error) => {
      error.classList.remove("active");
      error.textContent = "";
    });
  };

  /**
   * Valida el formulario de datos básicos
   * @returns {boolean} - Resultado de la validación
   */
  const validateBasicForm = () => {
    clearErrors();
    let isValid = true;
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();

    // Validar nombre de usuario
    if (!username) {
      showError("username", "El nombre de usuario es obligatorio");
      isValid = false;
    } else if (username.length < 3) {
      showError(
        "username",
        "El nombre de usuario debe tener al menos 3 caracteres"
      );
      isValid = false;
    }

    // Validar correo electrónico
    if (!email) {
      showError("email", "El correo electrónico es obligatorio");
      isValid = false;
    } else {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        showError("email", "Por favor, introduce un correo electrónico válido");
        isValid = false;
      }
    }

    return isValid;
  };

  /**
   * Valida el formulario de seguridad
   * @returns {boolean} - Resultado de la validación
   */
  const validateSecurityForm = () => {
    clearErrors();
    let isValid = true;
    const currentPassword = document.getElementById("current-password").value;
    const newPassword = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;

    // Validar contraseña actual
    if (!currentPassword) {
      showError("current-password", "La contraseña actual es obligatoria");
      isValid = false;
    }

    // Si se está cambiando la contraseña
    if (newPassword || confirmPassword) {
      if (!newPassword) {
        showError("password", "La nueva contraseña es obligatoria");
        isValid = false;
      } else if (newPassword.length < 8) {
        showError("password", "La contraseña debe tener al menos 8 caracteres");
        isValid = false;
      } else if (!/[A-Z]/.test(newPassword)) {
        showError(
          "password",
          "La contraseña debe contener al menos una mayúscula"
        );
        isValid = false;
      } else if (!/[a-z]/.test(newPassword)) {
        showError(
          "password",
          "La contraseña debe contener al menos una minúscula"
        );
        isValid = false;
      } else if (!/[0-9]/.test(newPassword)) {
        showError("password", "La contraseña debe contener al menos un número");
        isValid = false;
      } else if (!/[^A-Za-z0-9]/.test(newPassword)) {
        showError(
          "password",
          "La contraseña debe contener al menos un carácter especial"
        );
        isValid = false;
      }

      if (!confirmPassword) {
        showError("confirm-password", "Debes confirmar la nueva contraseña");
        isValid = false;
      } else if (newPassword !== confirmPassword) {
        showError("confirm-password", "Las contraseñas no coinciden");
        isValid = false;
      }
    }

    return isValid;
  };

  /**
   * Maneja el envío del formulario activo
   * @param {Event} e - Evento de formulario
   */
  const handleFormSubmit = async (e) => {
    e.preventDefault();
    const activeTab = document.querySelector(".tab-content.active");
    let isValid = false;
    let success = false;

    if (activeTab.id === "basic-tab") {
      isValid = validateBasicForm();
      if (isValid) {
        success = await enviarDatosBasicos();
      }
    } else if (activeTab.id === "security-tab") {
      isValid = validateSecurityForm();
      if (isValid) {
        success = await cambiarContraseña();
      }
    }

    if (success) {
      setTimeout(() => {
        closePanelF();
      }, 1000);
    }
  };

  /**
   * Envía los datos básicos al servidor
   * @returns {Promise<boolean>} - Resultado de la operación
   */
  const enviarDatosBasicos = async () => {
    try {
      const response = await fetch(
        "/../../controllers/AdminController.php?action=updateProfile",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            username: document.getElementById("username").value.trim(),
            email: document.getElementById("email").value.trim(),
          }),
        }
      );

      const data = await response.json();
      if (!data.success) throw new Error(data.message);

      showSuccessAlert("Datos básicos actualizados correctamente");
      return true;
    } catch (error) {
      showNotification(error.message || "Error al actualizar datos", "error");
      return false;
    }
  };

  /**
   * Cambia la contraseña del administrador
   * @returns {Promise<boolean>} - Resultado de la operación
   */
  const cambiarContraseña = async () => {
    try {
      const response = await fetch(
        "/../../controllers/AdminController.php?action=changePassword",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            current_password: document
              .getElementById("current-password")
              .value.trim(),
            new_password: document.getElementById("password").value.trim(),
            confirm_password: document
              .getElementById("confirm-password")
              .value.trim(),
          }),
        }
      );

      const data = await response.json();
      if (!data.success) throw new Error(data.message);

      showSuccessAlert("Contraseña actualizada correctamente");
      document.getElementById("security-form").reset();
      return true;
    } catch (error) {
      showNotification(error.message || "Error al cambiar contraseña", "error");
      return false;
    }
  };

  /**
   * Carga los datos del perfil del administrador
   */
  const cargarDatosPerfil = async () => {
    try {
      const response = await fetch(
        "/../../controllers/AuthController.php?action=getAdminData",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
        }
      );

      const data = await response.json();
      if (!data.success) throw new Error(data.message);

      initialProfileData = {
        username: data.data.admin.name,
        email: data.data.admin.email,
      };
    } catch (error) {
      console.error("Error al cargar perfil:", error);
      showNotification("Error al cargar datos del perfil", "error");
    }
  };

  /**
   * Configura la funcionalidad de mostrar/ocultar contraseña
   */
  const setupPasswordToggles = () => {
    passwordInputs.forEach((container) => {
      const input = container.querySelector("input");
      const toggleBtn = container.querySelector(".toggle-password");

      toggleBtn.addEventListener("click", () => {
        // Cambiar tipo de input entre 'password' y 'text'
        const type =
          input.getAttribute("type") === "password" ? "text" : "password";
        input.setAttribute("type", type);

        // Cambiar icono según estado
        const icon = toggleBtn.querySelector("i");
        if (type === "password") {
          icon.classList.remove("fa-eye-slash");
          icon.classList.add("fa-eye");
        } else {
          icon.classList.remove("fa-eye");
          icon.classList.add("fa-eye-slash");
        }
      });
    });
  };

  /**
   * Cambia entre las pestañas del panel
   * @param {string} tabId - ID de la pestaña a activar
   */
  const switchTab = (tabId) => {
    // Remover clase active de todos los botones y contenidos
    tabButtons.forEach((button) => button.classList.remove("active"));
    tabContents.forEach((content) => content.classList.remove("active"));

    // Activar la pestaña seleccionada
    const selectedButton = document.querySelector(`[data-tab="${tabId}"]`);
    const selectedContent = document.getElementById(tabId);

    if (selectedButton && selectedContent) {
      selectedButton.classList.add("active");
      selectedContent.classList.add("active");
    }

    // Limpiar errores al cambiar de pestaña
    clearErrors();
  };

  // Event Listeners
  adminProfileBtn.addEventListener("click", openPanel);
  closePanel.addEventListener("click", closePanelF);
  cancelBtn.addEventListener("click", closePanelF);
  profileOverlay.addEventListener("click", closePanelF);
  saveButton.addEventListener("click", handleFormSubmit);

  // Event listeners para las pestañas
  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      switchTab(button.getAttribute("data-tab"));
    });
  });

  // Configurar toggles de contraseña
  setupPasswordToggles();

  // Cerrar panel al presionar ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && profilePanel.classList.contains("active")) {
      closePanelF();
    }
  });

  // Evitar que se cierre el panel al hacer clic dentro de él
  profilePanel.addEventListener("click", (e) => {
    e.stopPropagation();
  });
});
