// Completando el script que estaba incompleto
//QUEDAMOS EN LA CARGA DE DATOS A LA VISTA.
import { checkAuthStatus } from "./authHelper.js";
let dataTemporal = {
  subtotal: null,
  totalItems: null,
  costoEnvio: null,
  aplicaDescuento: false,
  idDescuento: 0,
  porcentajeDescuento: 0,
};

function loadDataForUI() {
  fetch(
    "/../controllers/PedidosController.php?action=getDataForUI",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        showNotification(
          "Error al obtener los datos: " + data.message,
          "error"
        );
        return;
      }

      const { paymentMethods, deliveryMethods } = data.data;

      // Cargar métodos de pago
      renderDeliveryMethods(deliveryMethods);
      renderPaymentMethods(paymentMethods);

      // Configurar eventos después de renderizar
      setupDeliveryMethodEvents();
      setupPaymentMethodEvents();

      cargarDatosUser();
      setearDataTemporalEnvio(deliveryMethods);
      cargarDatosCarrito();
    })
    .catch((error) => {
      showNotification("Error al cargar los métodos de pago", "error");
    });
}

function cargarDatosCarrito() {
  fetch(
    "/../controllers/CarritoController.php?action=getCarrito",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        showNotification(
          "Error al obtener el carrito: " + data.message,
          "error"
        );
        return;
      }

      console.log("Carrito: ", data);

      const productos = data.data;
      renderProductosCarrito(productos);
      setearDataTemporalTotal(productos);
      renderTotales();
      updateTotal();
    });
}

function renderProductosCarrito(productos) {
  // Renderizar productos del carrito
  const productsContainer = document.getElementById("products-container");
  productsContainer.innerHTML = "";

  productos.items.forEach((item) => {
    const productHTML = `
            <div class="product-item">
                <img src="${item.imagen}" alt="${
      item.titulo
    }" class="product-image">
                <div class="product-details">
                    <div class="product-name">${item.titulo}</div>
                    <div class="product-price">${formatoCOP.format(
                      item.precio
                    )} <span class="product-quantity">x${
      item.cantidad
    }</span></div>
                </div>
                <div class="product-total">${formatoCOP.format(
                  item.total
                )}</div>
            </div>
        `;
    productsContainer.innerHTML += productHTML;
  });
}

function renderTotales() {
  // Actualizar contador de items
  const itemsCountElement = document.getElementById("items-count");

  if (itemsCountElement) {
    itemsCountElement.textContent =
      dataTemporal.totalItems !== null ? dataTemporal.totalItems : 0;
  }

  const subtotalElement = document.getElementById("subtotal");
  if (subtotalElement) {
    subtotalElement.textContent = formatoCOP.format(
      dataTemporal.subtotal !== null ? dataTemporal.subtotal : 0
    );
  }
}

function setearDataTemporalEnvio(deliveryMethods) {
  dataTemporal.costoEnvio = parseFloat(
    deliveryMethods[0].recargo !== null
      ? deliveryMethods[0].recargo
      : deliveryMethods[1].recargo !== null
      ? deliveryMethods[1].recargo
      : 0
  );
}

function setearDataTemporalDescuento(idDescuento, porcentajeDescuento) {
  if (idDescuento !== null) {
    let id = parseInt(idDescuento);

    if (!isNaN(id)) {
      dataTemporal.idDescuento = id;
      dataTemporal.aplicaDescuento = true;
      dataTemporal.porcentajeDescuento = parseFloat(porcentajeDescuento);
    } else {
      dataTemporal.aplicaDescuento = false;
      dataTemporal.idDescuento = null;
      dataTemporal.porcentajeDescuento = 0;
    }
  } else {
    dataTemporal.aplicaDescuento = false;
    dataTemporal.idDescuento = null;
    dataTemporal.porcentajeDescuento = 0;
  }
}

function setearDataTemporalTotal(productos) {
  dataTemporal.subtotal = parseFloat(productos.subtotal);
  dataTemporal.totalItems = parseInt(productos.total_items);
}

function cargarDatosUser() {
  fetch(
    "/../controllers/AuthController.php?action=getUserData",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        showNotification(
          "Error al obtener los datos del usuario: " + data.message,
          "error"
        );
        return;
      }

      setearDatosUser(data.data.user);
    })
    .catch((error) => {
      showNotification("Error al obtener los datos del usuario", "error");
    });
}

function setearDatosUser(userData) {
  // Set form input values
  document.getElementById("name").value = userData.name || "";
  document.getElementById("phone").value = userData.telefono || "";
  document.getElementById("cedula").value = userData.cedula || "";
  document.getElementById("address").value = userData.direccion || "";
  document.getElementById("city").value = "Cartago"; // Default
}

const formatoCOP = new Intl.NumberFormat("es-CO", {
  style: "currency",
  currency: "COP",
  minimumFractionDigits: 0,
});

function renderDeliveryMethods(deliveryMethods) {
  const deliveryMethodsContainer = document.getElementById(
    "delivery-methods-container"
  );

  deliveryMethods.forEach((method) => {
    const radioOption = document.createElement("div");
    radioOption.className = "radio-option";

    const radioId = `delivery-method-${method.id}`;
    const radioValue = parseInt(method.id);
    const tieneRecargo = method.recargo !== null;

    radioOption.innerHTML = `
        <input type="radio" id="${radioId}" name="delivery-method" value="${radioValue}">
        <label for="${radioId}">${method.metodo}</label>
        <span class="badge badge-${tieneRecargo ? "primary" : "secondary"}">${
      tieneRecargo ? formatoCOP.format(method.recargo) : "Gratis"
    }</span>
      `;

    deliveryMethodsContainer.appendChild(radioOption);
  });
}

function setupDeliveryMethodEvents() {
  // Seleccionar solo los radio-option de métodos de entrega
  const deliveryRadioOptions = document.querySelectorAll(
    "#delivery-methods-container .radio-option"
  );

  deliveryRadioOptions.forEach((option) => {
    const radio = option.querySelector('input[type="radio"]');

    option.addEventListener("click", function () {
      // Encontrar el grupo de radio buttons
      const groupName = radio.getAttribute("name");
      const groupOptions = document.querySelectorAll(
        `#delivery-methods-container .radio-option input[name="${groupName}"]`
      );

      // Desmarcar todos los del mismo grupo
      groupOptions.forEach((opt) => {
        opt.closest(".radio-option").classList.remove("active");
      });

      // Marcar el seleccionado
      radio.checked = true;
      
      option.classList.add("active");

      // Actualizar campos y total
      updateTotal();
    });
  });
}

function updateTotal() {
  const deliveryMethodElement = document.querySelector(
    'input[name="delivery-method"]:checked'
  );

  // Si no hay método seleccionado (no debería pasar), seleccionar el primero
  if (!deliveryMethodElement) {
    const firstMethod = document.querySelector('input[name="delivery-method"]');
    if (firstMethod) {
      firstMethod.checked = true;
      firstMethod.closest(".radio-option").classList.add("active");
      return updateTotal();
    }
    return;
  }

  const deliveryMethod = deliveryMethodElement.value;
  const deliveryCost =
    deliveryMethod === "1"
      ? dataTemporal.costoEnvio !== null
        ? dataTemporal.costoEnvio
        : 0
      : 0;

  document.getElementById("delivery-cost").textContent =
    formatoCOP.format(deliveryCost);

  // Mostrar u ocultar campos de dirección según método de entrega
  const deliveryFields = document.querySelectorAll(".delivery-field");
  if (deliveryMethod === "1") {
    deliveryFields.forEach((field) => {
      field.style.display = "block";
    });
    document.getElementById("address").setAttribute("required", "required");
    document.getElementById("city").setAttribute("required", "required");
  } else {
    deliveryFields.forEach((field) => {
      field.style.display = "none";
    });
    document.getElementById("address").removeAttribute("required");
    document.getElementById("city").removeAttribute("required");
  }

  const total =
    (dataTemporal.subtotal !== null ? dataTemporal.subtotal : 0) -
    (dataTemporal.aplicaDescuento
      ? (dataTemporal.subtotal * dataTemporal.porcentajeDescuento) / 100
      : 0) +
    deliveryCost;
  document.getElementById("total").textContent = formatoCOP.format(total);

  // Actualizar tiempo estimado
  const timeElement = document.querySelector(".time-value");
  if (deliveryMethod === "1") {
    timeElement.textContent = "30-45 minutos";
  } else {
    timeElement.textContent = "15-20 minutos";
  }
}

function renderPaymentMethods(paymentMethods) {
  const paymentMethodsContainer = document.getElementById(
    "payment-methods-container"
  );

  // Limpiar métodos existentes (excepto el header)
  const existingOptions = paymentMethodsContainer.querySelectorAll(
    ".radio-option, .payment-method-info"
  );
  existingOptions.forEach((option) => option.remove());

  paymentMethods.forEach((method) => {
    // Crear contenedor principal del método
    const methodContainer = document.createElement("div");
    methodContainer.className = "payment-method";

    // Crear radio button
    const radioOption = document.createElement("div");
    radioOption.className = "radio-option";
    const radioId = `payment-method-${method.id}`;

    radioOption.innerHTML = `
          <input type="radio" id="${radioId}" name="payment-method" value="${method.id}">
          <label for="${radioId}">${method.metodo}</label>
      `;

    methodContainer.appendChild(radioOption);

    // Crear detalles del método
    const methodDetails = document.createElement("div");
    methodDetails.className = "method-details";
    methodDetails.dataset.method = method.id;

    // Descripción corta
    const description = document.createElement("p");
    description.className = "method-description";
    description.textContent = method.descripcion.split("\n")[0]; // Tomamos solo la primera línea
    methodDetails.appendChild(description);

    // Información adicional
    const additionalInfo = document.createElement("div");
    additionalInfo.className = "method-additional";

    // Procesar indicaciones (convertir saltos de línea en párrafos)
    method.indicaciones.split("\n").forEach((paragraph) => {
      if (paragraph.trim()) {
        const p = document.createElement("p");
        p.className = "additional-note";
        p.innerHTML = paragraph.replace(/\n/g, "<br>");
        additionalInfo.appendChild(p);
      }
    });

    // Agregar cuentas bancarias si es método de transferencia
    if (method.id === "2" && method.accounts && method.accounts.length > 0) {
      const accountsContainer = document.createElement("div");
      accountsContainer.className = "bank-accounts-vertical";

      method.accounts.forEach((account) => {
        // Determinar el icono según la entidad
        let iconClass = "fas fa-university"; // Por defecto
        if (account.entidad.toLowerCase().includes("nequi")) {
          iconClass = "fas fa-mobile-alt";
        } else if (account.entidad.toLowerCase().includes("daviplata")) {
          iconClass = "fas fa-university";
        } else if (account.entidad.toLowerCase().includes("whatsapp")) {
          iconClass = "fab fa-whatsapp";
        }

        // Determinar si es WhatsApp para agregar link
        const isWhatsApp = account.entidad.toLowerCase().includes("whatsapp");

        const accountCard = document.createElement("div");
        accountCard.className = "account-card-vertical";

        accountCard.innerHTML = `
                  <div class="account-header">
                  ${
                    isWhatsApp
                      ? `<a href="https://wa.me/57${account.numero_cuenta}" target="_blank" class="account-icon-link">
                      <i class="${iconClass} account-icon"></i>
                    </a>`
                      : `<i class="${iconClass} account-icon"></i>`
                  }
                      <h4 class="account-title">${account.entidad}</h4>
                  </div>
                  <div class="account-details">
                      <div class="detail-row">
                          <span class="detail-label">Número:</span>
                          <span class="detail-value">${
                            account.numero_cuenta
                          }</span>
                      </div>
                      <div class="detail-row">
                          <span class="detail-label">Titular:</span>
                          <span class="detail-value">${account.titular}</span>
                      </div>
                  </div>
              `;

        accountsContainer.appendChild(accountCard);
      });

      additionalInfo.appendChild(accountsContainer);
    }

    methodDetails.appendChild(additionalInfo);
    methodContainer.appendChild(methodDetails);
    paymentMethodsContainer.appendChild(methodContainer);
  });

  // Configurar eventos para los métodos de pago
}
function setupPaymentMethodEvents() {
  const paymentRadios = document.querySelectorAll(
    '#payment-methods-container input[type="radio"]'
  );

  paymentRadios.forEach((radio) => {
    radio.addEventListener("change", function () {
      if (this.checked) {
        // Encontrar todos los elementos relacionados
        const paymentMethod = this.closest(".payment-method");
        const allMethodDetails = document.querySelectorAll(".method-details");

        // Resetear todos
        document.querySelectorAll(".radio-option").forEach((opt) => {
          opt.classList.remove("active");
        });
        allMethodDetails.forEach((detail) => {
          detail.style.maxHeight = "0";
          detail.style.opacity = "0";
          detail.style.transform = "translateY(-10px)";
        });

        // Activar el seleccionado
        this.closest(".radio-option").classList.add("active");
        const methodDetails = paymentMethod.querySelector(".method-details");

        // Animación suave
        setTimeout(() => {
          methodDetails.style.maxHeight = methodDetails.scrollHeight + "px";
          methodDetails.style.opacity = "1";
          methodDetails.style.transform = "translateY(0)";
        }, 10);
      }
    });
  });
}

async function validatePromoCode(code) {
  try {
    const response = await fetch(
      "/../controllers/PedidosController.php?action=validatePromoCode",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ code: code }),
      }
    );

    const data = await response.json();
    return data;
  } catch (error) {
    showNotification("Error al validar el código promocional", "error");
    return {
      success: false,
      message: "Error al validar el código promocional",
    };
  }
}

function inicializarPromoCode() {
  // Código promocional
  const promoCodeInput = document.querySelector(".promo-code input");
  const promoCodeButton = document.querySelector(".promo-code button");
  const discountElement = document.getElementById("discount");
  const savingsRow = document.querySelector(".savings-row");

  promoCodeButton.addEventListener("click", async function () {
    const code = promoCodeInput.value.trim().toUpperCase();

    if (!code) {
      showNotification("Por favor ingresa un código promocional", "error");
      return;
    }

    const result = await validatePromoCode(code);

    if (result.success) {
      // Aplicar descuento
      const discount =
        parseFloat(dataTemporal.subtotal) * (result.data.porcentaje / 100);
      discountElement.textContent = `-${formatoCOP.format(discount)}`;
      savingsRow.style.display = "flex";

      setearDataTemporalDescuento(result.data.id, result.data.porcentaje);

      updateTotal();

      // Feedback al usuario
      promoCodeButton.textContent = "Aplicado";
      promoCodeButton.disabled = true;
      promoCodeButton.style.backgroundColor = "var(--primary-color)";
      promoCodeInput.disabled = true;

      // Mostrar notificación
      showNotification(
        `¡Descuento del ${result.data.porcentaje}% aplicado!`,
        "success"
      );
    } else {
      // Código inválido
      showNotification(
        result.message || "Código promocional inválido",
        "error"
      );
      promoCodeInput.value = "";
    }
  });
}

function validateCheckoutForm(data) {
  let isValid = true;
  const resultData = {
    customer: {},
    deliveryMethod: null,
    paymentMethod: null,
    notes: "",
    infoDescuento: {
      aplica: dataTemporal.aplicaDescuento,
      id: dataTemporal.idDescuento,
    },
  };

  // 1. Validar método de entrega seleccionado
  const deliveryMethod = data.deliveryMethod;
  let deliveryMethodID = null;

  if (!deliveryMethod || !deliveryMethod.value) {
    isValid = false;
    const deliveryContainer = document.getElementById(
      "delivery-methods-container"
    );
    if (deliveryContainer) {
      deliveryContainer.classList.add("error-border");
      setTimeout(
        () => deliveryContainer.classList.remove("error-border"),
        3000
      );
    }
  } else {
    deliveryMethodID = parseInt(deliveryMethod.value);
    if (isNaN(deliveryMethodID)) {
      isValid = false;
      const deliveryContainer = document.getElementById(
        "delivery-methods-container"
      );
      if (deliveryContainer) {
        deliveryContainer.classList.add("error-border");
        setTimeout(
          () => deliveryContainer.classList.remove("error-border"),
          3000
        );
      }
    } else {
      resultData.deliveryMethod = deliveryMethodID;
    }
  }

  // 2. Validar información de contacto
  const fieldsToValidate = ["name", "phone", "cedula", "address", "city"];
  const customerData = data.customer || {};

  // Verificar que todos los elementos existan
  const missingFields = fieldsToValidate.filter(
    (field) => !customerData[field]
  );
  if (missingFields.length > 0) {
    isValid = false;
  }

  // Validar cada campo individualmente
  fieldsToValidate.forEach((field) => {
    const element = customerData[field];
    const errorElement = document.getElementById(`${field}-error`);

    if (!element) return; // Si el elemento no existe, saltar

    // Limpiar errores previos
    if (errorElement) {
      errorElement.textContent = "";
      errorElement.style.display = "none";
    }
    element.classList.remove("error");

    // Validaciones específicas para cada campo
    switch (field) {
      case "name":
        if (!element.value || element.value.trim().split(" ").length < 2) {
          isValid = false;
          if (errorElement) {
            errorElement.textContent = "Ingresa nombre y apellido";
            errorElement.style.display = "block";
          }
          element.classList.add("error");
        } else {
          resultData.customer[field] = element.value.trim();
        }
        break;

      case "phone":
        const cleanPhone = element.value.trim().replace(/[^0-9+\-\s()]/g, "");
        if (cleanPhone.length < 7 || cleanPhone.length > 15) {
          isValid = false;
          if (errorElement) {
            errorElement.textContent =
              "Ingresa un teléfono válido (entre 7 y 15 caracteres)";
            errorElement.style.display = "block";
          }
          element.classList.add("error");
        } else {
          resultData.customer[field] = cleanPhone;
        }
        break;

      case "cedula":
        if (
          !element.value.trim() ||
          element.value.trim().length < 6 ||
          isNaN(element.value)
        ) {
          isValid = false;
          if (errorElement) {
            errorElement.textContent =
              "Ingresa una cédula válida (mínimo 6 dígitos)";
            errorElement.style.display = "block";
          }
          element.classList.add("error");
        } else {
          resultData.customer[field] = element.value.trim();
        }
        break;

      case "address":
        // Solo validar si es envío a domicilio
        if (deliveryMethodID === 1) {
          const addressValue = element.value.trim();
          if (!addressValue) {
            isValid = false;
            if (errorElement) {
              errorElement.textContent = "La dirección es obligatoria";
              errorElement.style.display = "block";
            }
            element.classList.add("error");
          } else if (addressValue.length < 10) {
            isValid = false;
            if (errorElement) {
              errorElement.textContent =
                "La dirección debe ser más específica (mínimo 10 caracteres)";
              errorElement.style.display = "block";
            }
            element.classList.add("error");
          } else if (!/^[a-zA-Z0-9\s.,#-]+$/.test(addressValue)) {
            isValid = false;
            if (errorElement) {
              errorElement.textContent =
                "La dirección contiene caracteres no válidos";
              errorElement.style.display = "block";
            }
            element.classList.add("error");
          } else {
            resultData.customer[field] = addressValue;
          }
        } else {
          resultData.customer[field] = null;
        }
        break;

      case "city":
        // Solo validar si es envío a domicilio
        if (deliveryMethodID === 1) {
          const cityValue = element.value.trim();
          if (!cityValue) {
            isValid = false;
            if (errorElement) {
              errorElement.textContent = "La ciudad es obligatoria";
              errorElement.style.display = "block";
            }
            element.classList.add("error");
          } else if (cityValue.length < 3) {
            isValid = false;
            if (errorElement) {
              errorElement.textContent = "Ingresa un nombre de ciudad válido";
              errorElement.style.display = "block";
            }
            element.classList.add("error");
          } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(cityValue)) {
            isValid = false;
            if (errorElement) {
              errorElement.textContent =
                "El nombre de la ciudad solo debe contener letras";
              errorElement.style.display = "block";
            }
            element.classList.add("error");
          } else {
            resultData.customer[field] = cityValue;
          }
        } else {
          resultData.customer[field] = null;
        }
        break;
    }
  });

  // 3. Validar método de pago seleccionado
  const paymentMethod = data.paymentMethod;

  if (!paymentMethod || !paymentMethod.value) {
    isValid = false;
    const paymentContainer = document.getElementById("payment-methods-cont");
    if (paymentContainer) {
      paymentContainer.classList.add("error-border");
      setTimeout(() => paymentContainer.classList.remove("error-border"), 3000);
    }
  } else {
    const paymentMethodID = parseInt(paymentMethod.value);
    if (isNaN(paymentMethodID)) {
      isValid = false;
      const paymentContainer = document.getElementById("payment-methods-cont");
      if (paymentContainer) {
        paymentContainer.classList.add("error-border");
        setTimeout(
          () => paymentContainer.classList.remove("error-border"),
          3000
        );
      }
    } else {
      resultData.paymentMethod = paymentMethodID;
    }
  }

  // 4. Validar notas (opcional)
  if (data.notes && data.notes.value) {
    resultData.notes = data.notes.value.trim();
  }

  // 5. Validar información de descuento
  if (data.infoDescuento && data.infoDescuento.aplica) {
    if (
      !data.infoDescuento.id ||
      isNaN(data.infoDescuento.id) ||
      data.infoDescuento.id <= 0
    ) {
      isValid = false;
    }
  }

  // Mostrar mensaje general si hay errores
  if (!isValid) {
    const firstError = document.querySelector(".error");
    if (firstError) {
      firstError.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }

  return {
    isValid: isValid,
    data: isValid ? resultData : null, // Solo devolver datos si es válido
  };
}

async function sendDataPedido(orderData) {
  try {
    const response = await fetch(
      "/../controllers/PedidosController.php?action=createOrder",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(orderData),
      }
    );

    const data = await response.json();

    console.log(data);
    return data;
  } catch (error) {
    showNotification("Error al enviar el pedido", "error");
    return {
      success: false,
      message: "Error al enviar el pedido",
    };
  }
}

function initFormData() {
  // Validación de formulario

  const confirmOrderButton = document.getElementById("confirm-order");

  confirmOrderButton.addEventListener("click", async function (e) {
    e.preventDefault();
    confirmOrderButton.disabled = true;
    confirmOrderButton.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    const formData = {
      deliveryMethod: document.querySelector(
        'input[name="delivery-method"]:checked'
      ),
      paymentMethod: document.querySelector(
        'input[name="payment-method"]:checked'
      ),
      customer: {
        name: document.getElementById("name"),
        phone: document.getElementById("phone"),
        cedula: document.getElementById("cedula"),
        address: document.getElementById("address"),
        city: document.getElementById("city"),
      },
      notes: document.getElementById("notes"),
      infoDescuento: {
        aplica: dataTemporal.aplicaDescuento,
        id: dataTemporal.idDescuento,
      },
    };

    const result = validateCheckoutForm(formData);

    if (result.isValid) {
      const autenticado = await checkAuthStatus(2);

      if (!autenticado) {
        setTimeout(() => {
          confirmOrderButton.disabled = false;
          confirmOrderButton.innerHTML = "Confirmar pedido";
          showNotification("Error de autenticación", "error");
        }, 2000);
        return;
      }

      const resultInsertarPedido = await sendDataPedido(result.data);

      if (resultInsertarPedido.success) {
        confirmOrderButton.disabled = true;
        confirmOrderButton.innerHTML = "Pedido enviado";
        localStorage.removeItem('carritoProductos');
        showOrderConfirmation(resultInsertarPedido.data);
      } else {
        confirmOrderButton.disabled = false;
        confirmOrderButton.innerHTML = "Confirmar pedido";
        console.log(resultInsertarPedido.message);
        showNotification("Error al enviar el pedido" + resultInsertarPedido.message, "error");
      }
    } else {
      confirmOrderButton.disabled = false;
      confirmOrderButton.innerHTML = "Confirmar pedido";
      showNotification(
        "Por favor completa todos los campos requeridos correctamente",
        "error"
      );
    }
  });
}

// Función para mostrar confirmación de pedido
function showOrderConfirmation(orderData) {
  // Crear modal de confirmación
  const modal = document.createElement("div");
  modal.className = "order-confirmation-modal";
  modal.innerHTML = `
                          <div class="modal-content">
                              <div class="modal-header">
                                  <i class="fas fa-check-circle success-icon"></i>
                                  <h2>¡Pedido Confirmado!</h2>
                                  <p>Tu pedido ha sido recibido correctamente</p>
                              </div>
                              <div class="modal-body">
                                  <div class="order-number">
                                      <span>Número de pedido:</span>
                                      <strong>#${orderData.id}</strong>
                                  </div>
                                  <p class="confirmation-message">
                                      ¡Gracias por tu compra!. Hemos recibido tu pedido y estamos trabajando en él. Recibirás una confirmación por mensaje de texto al número proporcionado.
                                      ${
                                        orderData.paymentMethod === 2
                                          ? "No olvides enviar el comprobante de pago a nuestro WhatsApp para que podamos poner en marcha tu pedido"
                                          : ""
                                      }

                                  </p>
                                  <div class="delivery-details">
                                      <p>
                                          <i class="fas fa-${
                                            orderData.deliveryMethod === 1
                                              ? "truck"
                                              : "store"
                                          }"></i>
                                          <strong>${
                                            orderData.deliveryMethod === 1
                                              ? "Entrega a domicilio"
                                              : "Recoger en tienda"
                                          }</strong>
                                      </p>
                                      <p>
                                          <i class="fas fa-clock"></i>
                                          <span>Tiempo estimado: <strong>${
                                            orderData.deliveryMethod === 1
                                              ? "30-45 minutos"
                                              : "15-20 minutos"
                                          }</strong></span>
                                      </p>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                  <button class="button" id="close-confirmation">Volver a la tienda</button>
                              </div>
                          </div>
                      `;

  document.body.appendChild(modal);

  // Mostrar modal con animación
  setTimeout(() => {
    modal.classList.add("show");
  }, 10);

  // Configurar botón de cierre
  document
    .getElementById("close-confirmation")
    .addEventListener("click", function () {
      modal.classList.remove("show");
      setTimeout(() => {
        modal.remove();

        // Redireccionar a la página principal
        window.location.href = "home.php"; // Aquí iría la URL real de la tienda
      }, 300);
    });
}

document.addEventListener("DOMContentLoaded", function () {
  loadDataForUI();

  // Inicializar total
  updateTotal();

  inicializarPromoCode();
  initFormData();

  // Botón seguir comprando
  document
    .getElementById("continue-shopping")
    .addEventListener("click", function (e) {
      e.preventDefault();
      history.back();
    });

  // Añadir efectos a los campos del formulario
  const formInputs = document.querySelectorAll("input, textarea");
  formInputs.forEach((input) => {
    // Quitar mensaje de error al escribir
    input.addEventListener("input", function () {
      const errorElement = document.getElementById(`${input.id}-error`);
      if (errorElement) {
        errorElement.style.display = "none";
      }
      input.style.borderColor = "";
    });

    // Efecto de enfoque
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("focused");
    });

    input.addEventListener("blur", function () {
      this.parentElement.classList.remove("focused");
    });
  });
});
