import { redirectTo } from "./authHelper.js";

function init() {
  const loginTab = document.getElementById("login-tab");
  const registerTab = document.getElementById("register-tab");
  const loginForm = document.getElementById("login-form");
  const registerForm = document.getElementById("register-form");
  const tabIndicator = document.querySelector(".tab-indicator");

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

  // Cambiar entre formularios con animación y limpieza
loginTab.addEventListener("click", function () {
  if (!loginTab.classList.contains("active")) {
    loginTab.classList.add("active");
    registerTab.classList.remove("active");
    tabIndicator.className = "tab-indicator login";

    registerForm.style.transform = "translateX(50px)";
    registerForm.style.opacity = "0";

    setTimeout(() => {
      loginForm.classList.add("active");
      registerForm.classList.remove("active");
      
      // Limpiar el formulario de registro
      clearForm(registerForm);


      setTimeout(() => {
        loginForm.style.transform = "translateX(0)";
        loginForm.style.opacity = "1";
      }, 50);
    }, 300);
  }
});

  registerTab.addEventListener("click", function () {
    if (!registerTab.classList.contains("active")) {
      registerTab.classList.add("active");
      loginTab.classList.remove("active");
      tabIndicator.className = "tab-indicator register";
  
      loginForm.style.transform = "translateX(-50px)";
      loginForm.style.opacity = "0";
  
      setTimeout(() => {
        registerForm.classList.add("active");
        loginForm.classList.remove("active");
        
        // Limpiar el formulario de login
        clearForm(loginForm);
  
        setTimeout(() => {
          registerForm.style.transform = "translateX(0)";
          registerForm.style.opacity = "1";
        }, 50);
      }, 300);
    }
  });
// Función para limpiar formulario
function clearForm(form) {

  clearErrors();
  // Limpiar todos los inputs del formulario
  const inputs = form.querySelectorAll('input');
  inputs.forEach(input => {
    if (input.type === 'checkbox') {
      input.checked = false;
    } else {
      input.value = '';
    }
  });
  
  
}
  // Efectos decorativos de los "candies" flotantes
  const candies = document.querySelectorAll(".candy-floating");

  candies.forEach((candy) => {
    setInterval(() => {
      const xPos = Math.random() * 10 - 5;
      const yPos = Math.random() * 10 - 5;
      const rotation = Math.random() * 20 - 10;

      candy.style.transform = `translate(${xPos}px, ${yPos}px) rotate(${rotation}deg)`;
    }, 3000);
  });

  registerForm.addEventListener("submit", handleSubmitRegister);
  loginForm.addEventListener("submit", handleSubmitLogin);

  animateElements();
}

function validateData(data) {
  const errors = [];

  // Validar campos
  if (!data.name) {
    errors.push({ field: "register-name", message: "El nombre es requerido" });
  } else if (data.name.length < 3) {
    errors.push({ field: "register-name", message: "Mínimo 3 caracteres" });
  } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(data.name)) {
    errors.push({ field: "register-name", message: "Solo letras y espacios" });
  }

  if (!data.email) {
    errors.push({ field: "register-email", message: "El email es requerido" });
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
    errors.push({ field: "register-email", message: "Email no válido" });
  }

  if (!data.cedula) {
    errors.push({
      field: "register-cedula",
      message: "La cédula es requerida",
    });
  }

  if (!data.password) {
    errors.push({
      field: "register-password",
      message: "La contraseña es requerida",
    });
  } else {
    if (data.password.length < 8)
      errors.push({
        field: "register-password",
        message: "Mínimo 8 caracteres",
      });
    if (!/[A-Z]/.test(data.password))
      errors.push({ field: "register-password", message: "1 mayúscula" });
    if (!/[0-9]/.test(data.password))
      errors.push({ field: "register-password", message: "1 número" });
    if (!/[^A-Za-z0-9]/.test(data.password))
      errors.push({
        field: "register-password",
        message: "1 carácter especial",
      });
    if (!/[a-z]/.test(data.password))
      errors.push({ field: "register-password", message: "1 minúscula" });
  }

  if (data.password !== data.password_confirmation) {
    errors.push({
      field: "register-password-confirm",
      message: "Las contraseñas no coinciden",
    });
  }

  return errors.length === 0 ? true : errors;
}

function validateDataLogin(data) {
  const errors = [];

  // Validar campos
  if (!data.email) {
    errors.push({ field: "login-email", message: "El email es requerido" });
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
    errors.push({ field: "login-email", message: "Email no válido" });
  }

  if (!data.password) {
    errors.push({
      field: "login-password",
      message: "La contraseña es requerida",
    });
  } else {
    if (data.password.length < 8)
      errors.push({ field: "login-password", message: "Mínimo 8 caracteres" });
    if (!/[A-Z]/.test(data.password))
      errors.push({ field: "login-password", message: "1 mayúscula" });
    if (!/[0-9]/.test(data.password))
      errors.push({ field: "login-password", message: "1 número" });
    if (!/[^A-Za-z0-9]/.test(data.password))
      errors.push({ field: "login-password", message: "1 carácter especial" });
    if (!/[a-z]/.test(data.password))
      errors.push({ field: "login-password", message: "1 minúscula" });
  }

  return errors.length === 0 ? true : errors;
}

function clearErrors() {
  // Limpiar clases de error
  document.querySelectorAll(".form-input").forEach((input) => {
    input.classList.remove("error");
  });

  // Ocultar mensajes de error
  document.querySelectorAll(".form-error").forEach((error) => {
    error.classList.remove("visible");
  });
}

function showErrors(errors) {
  errors.forEach((error) => {
    const fieldId = error.field;
    const message = error.message;

    // Campos normales
    const input = document.getElementById(fieldId);
    if (input) {
      input.classList.add("error");

      // Mostramos el mensaje de error existente
      const errorElement = input
        .closest(".form-group")
        .querySelector(".form-error");
      if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add("visible");
      }
    }
  });
}

function handleSubmitRegister(event) {
  event.preventDefault();

  // Obtener valores
  const data = {
    name: document.getElementById("register-name").value.trim(),
    email: document.getElementById("register-email").value.trim(),
    cedula: document.getElementById("register-cedula").value.trim(),
    password: document.getElementById("register-password").value.trim(),
    password_confirmation: document
      .getElementById("register-password-confirm")
      .value.trim(),
  };

  // Validar datos
  const NoErrors = validateData(data);

  // Limpiar errores previos
  clearErrors();

  // Manejar resultados
  if (NoErrors === true) {
    // Enviar formulario si no hay errores
    console.log("Enviando datos:", data);

    enviarDatosRegistro(data);
  } else {
    // Mostrar errores específicos
    showErrors(NoErrors);
  }
}

function handleSubmitLogin(event) {
  event.preventDefault();

  // Obtener valores
  const data = {
    email: document.getElementById("login-email").value.trim(),
    password: document.getElementById("login-password").value.trim(),
  };

  // Validar datos
  const NoErrors = validateDataLogin(data);

  // Limpiar errores previos
  clearErrors();

  // Manejar resultados
  if (NoErrors === true) {
    // Enviar formulario si no hay errores
    console.log("Enviando datos:", data);

    enviarDatosLogin(data);
  } else {
    // Mostrar errores específicos
    showErrors(NoErrors);
  }
}

function enviarDatosLogin(data) {
  // Simulación de guardado
  const loginButton = document.getElementById("login-btn");
  loginButton.disabled = true;
  loginButton.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';

  fetch("/../../controllers/AuthController.php?action=login", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => {
      return response.json();
    })
    .then((data) => {
      loginButton.innerHTML = "Iniciar sesión";
      if (!data.success) {
        console.error("Error en el login:", data.message);
        showNotification(data.message, "error");

        setTimeout(() => {
          loginButton.disabled = false;
        }, 3000);
        return;
      }
      console.log("login exitoso:", data);
      // Aquí puedes redirigir al usuario o mostrar un mensaje de éxito
      redirectTo(data.data.redirect);
    })
    .catch((error) => {
      console.error("Error al logear:", error);
      // Aquí puedes mostrar un mensaje de error al usuario
      loginButton.disabled = false;
      loginButton.innerHTML =
        '<i class="fas fa-sign-in-alt"></i> Iniciar sesión';
    });
}
function enviarDatosRegistro(data) {
  const registerButton = document.getElementById("register-btn");
  registerButton.disabled = true;
  registerButton.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Creando cuenta...';

  fetch("/../../controllers/AuthController.php?action=register", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => {
      return response.json();
    })
    .then((data) => {
      
      registerButton.innerHTML = "Crear cuenta";
      if (!data.success) {
        console.error("Error en el registro:", data.message);
        console.log(data)
        showNotification(data.message, "error");

        setTimeout(() => {
          registerButton.disabled = false;
        }, 3000);
        

        return;
      }
      console.log("Registro exitoso:", data.data.redirect);
      showNotification("Registro exitoso, bienvenido a FreshCandy", "success");
      // Aquí puedes redirigir al usuario o mostrar un mensaje de éxito

      setTimeout(() => {
        redirectTo(data.data.redirect);
      }, 3000);
    })
    .catch((error) => {
      console.error("Error al registrar:", error);
      // Aquí puedes mostrar un mensaje de error al usuario
      registerButton.disabled = false;
      registerButton.innerHTML = "Crear cuenta";
    });
}

const animateElements = () => {
  const formCard = document.querySelector(".auth-container");

  if (formCard) {
    setTimeout(() => {
      formCard.classList.add("slide-in");
    }, 200);
  }
};

document.addEventListener("DOMContentLoaded", init);
