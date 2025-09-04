/**
 * Fresh Candy Admin - Clientes
 * Script para funcionalidades de la vista de clientes
 */

document.addEventListener("DOMContentLoaded", init);

/**
 * Inicializa todas las funcionalidades al cargar la página
 */
function init() {
  document.getElementById("dashboard-link6").classList.add("active");

  // Inicializar botones de acción
  initActionButtons();

  // Inicializar paginación
  initPagination();

  // Animación de entrada para los elementos
  animateContent();

  animateElements();

  initOrderHistoryModal();
}

/**
 * Inicializa la funcionalidad de búsqueda de clientes
 */

/**
 * Simula una búsqueda filtrando las filas visibles (solo para demo)
 * En implementación real, se haría una petición AJAX al servidor
 * @param {string} term - Término de búsqueda
 */

/**
 * Simula ordenamiento de tabla para demo
 * En implementación real, se haría una petición AJAX al servidor
 * @param {string} criteria - Criterio de ordenamiento
 */
function simulateSort(criteria) {
  const tableBody = document.querySelector(".data-table tbody");
  const rows = Array.from(tableBody.querySelectorAll("tr"));

  // Determine sort column index based on criteria
  let columnIndex;
  switch (criteria) {
    case "orders":
      columnIndex = 5; // Total Pedidos
      break;
    case "spent":
      columnIndex = 6; // Total Gastado
      break;
    case "date":
      columnIndex = 7; // Último Pedido
      break;
    default:
      return; // Si es default, no hacemos nada
  }

  // Ordenar filas
  rows.sort((a, b) => {
    let aValue = a.children[columnIndex].textContent;
    let bValue = b.children[columnIndex].textContent;

    // Manejar formato de dinero ($X,XXX)
    if (criteria === "spent") {
      aValue = parseFloat(aValue.replace("$", "").replace(",", ""));
      bValue = parseFloat(bValue.replace("$", "").replace(",", ""));
    }
    // Manejar formato de fecha (XX/XX/XXXX)
    else if (criteria === "date") {
      const aParts = aValue.split("/");
      const bParts = bValue.split("/");
      aValue = new Date(aParts[2], aParts[1] - 1, aParts[0]);
      bValue = new Date(bParts[2], bParts[1] - 1, bParts[0]);
    }
    // Para números simples
    else {
      aValue = parseInt(aValue);
      bValue = parseInt(bValue);
    }

    return bValue - aValue; // Orden descendente
  });

  // Re-agregar filas ordenadas
  rows.forEach((row) => tableBody.appendChild(row));

  // Mostrar efecto de actualización
  tableBody.classList.add("animate-fadeIn");
  setTimeout(() => tableBody.classList.remove("animate-fadeIn"), 500);
}

/**
 * Inicializa los botones de acción en la tabla
 */
function initActionButtons() {
  // Botones para ver historial
  const historyButtons = document.querySelectorAll(".view-history");

  historyButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const clientId = this.getAttribute("data-id");
      openOrderHistory(clientId);
    });
  });

 

  // Cerrar modal al hacer clic fuera
  
}

// Elementos del DOM
let modal, modalTitle, closeButtons;

// Inicializar la modal
function initOrderHistoryModal() {
  modal = document.getElementById("orderHistoryModal");
  modalTitle = document.getElementById("pim-product-title");

  closeButtons = document.querySelectorAll(".pim-close-modal, .pim-btn-close");

  setupEventListeners();
}

// Configurar event listeners
function setupEventListeners() {
  // Cerrar modal al hacer clic en los botones de cerrar
  closeButtons.forEach((button) => {
    button.addEventListener("click", closeModal);
  });

  // Cerrar modal al hacer clic fuera del contenido
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  // Cerrar con tecla ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.style.display === "block") {
      closeModal();
    }
  });
}



// Cerrar la modal
function closeModal() {
  modal.style.display = "none";
  document.body.style.overflow = "auto";
}

/**
 * Abre el modal de historial de pedidos para un cliente específico
 * @param {string} clientId - ID del cliente
 */
function openOrderHistory(clientId) {
  console.log("Obteniendo historial para cliente ID:", clientId);

  // En una implementación real, aquí haríamos una petición AJAX para obtener
  // el historial de pedidos del cliente con el ID especificado

  // Por ahora, simulamos con datos fijos y buscamos en la tabla el cliente
  const clientRow = document
    .querySelector(`.view-history[data-id="${clientId}"]`)
    .closest("tr");

  if (clientRow) {
    const clientName = clientRow.children[1].textContent;
    const clientEmail = clientRow.children[2].textContent;
    const clientPhone = clientRow.children[3].textContent;
    const totalOrders = clientRow.children[4].textContent;
    const totalSpent = clientRow.children[5].textContent;

    // Calcular promedio por pedido
    const avgOrder = calculateAverage(
      parseFloat(totalSpent.replace("$", "").replace(",", "")),
      parseInt(totalOrders)
    );

    // Actualizar datos en el modal
    document.getElementById("clientName").textContent = clientName;
    document.getElementById("clientName2").textContent = clientName;
    document.getElementById("clientEmail").textContent = clientEmail;
    document.getElementById("clientPhone").textContent = clientPhone;
    document.getElementById("clientTotalOrders").textContent = totalOrders;
    document.getElementById("clientTotalSpent").textContent = totalSpent;
    document.getElementById("clientAvgOrder").textContent = "$" + avgOrder;

    // En una implementación real, actualizaríamos la tabla de pedidos
    // con los datos obtenidos de la petición AJAX

    modal.style.display = "block";
    document.body.style.overflow = "hidden";
  }
}

/**
 * Cierra el modal de historial de pedidos
 */


/**
 * Calcula el promedio de gasto por pedido
 * @param {number} total - Total gastado
 * @param {number} orders - Número de pedidos
 * @returns {string} - Promedio formateado
 */
function calculateAverage(total, orders) {
  if (orders === 0) return "0";
  const avg = total / orders;
  return avg.toFixed(0);
}

/**
 * Inicializa la funcionalidad de paginación
 */
function initPagination() {
  const paginationButtons = document.querySelectorAll(".pagination-btn");
  console.log("Botones de paginación:", paginationButtons);

  paginationButtons.forEach((button) => {
    if (!button.disabled && !button.classList.contains("active")) {
      button.addEventListener("click", function () {
        // En implementación real, haríamos petición AJAX para traer la página seleccionada
        console.log("Cambiando a página:", this.textContent);

        // Simular cambio de página activa
        document
          .querySelector(".pagination-btn.active")
          .classList.remove("active");
        this.classList.add("active");

        // Aquí se cargarían los nuevos datos con AJAX
      });
    }
  });
}

/**
 * Actualiza el contador de resultados de paginación
 * Solo para demo, en implementación real vendría del servidor
 */
function updatePaginationInfo() {
  const visibleRows = document.querySelectorAll(
    '.data-table tbody tr:not([style*="display: none"])'
  ).length;
  const paginationInfo = document.querySelector(".pagination-info");

  if (paginationInfo) {
    paginationInfo.textContent = `Mostrando 1-${visibleRows} de ${visibleRows} clientes`;
  }
}

/**
 * Inicializa la funcionalidad de exportación
 */

/**
 * Aplica animaciones de entrada a los elementos de la página
 */
function animateContent() {
  // Animar tabla principal
  const dataCard = document.querySelector(".data-card");
  if (dataCard) {
    setTimeout(() => {
      dataCard.classList.add("slide-in");
    }, 300);
  }
}

/**
 * Carga datos de clientes desde el servidor
 * Función que se implementaría con AJAX
 */
function loadClientData() {
  // Mostrar estado de carga
  const tableBody = document.querySelector(".data-table tbody");

  if (tableBody) {
    tableBody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                    </div>
                </td>
            </tr>
        `;

    // Aquí iría la petición AJAX para cargar los datos
    // fetch('/api/clients')
    //     .then(response => response.json())
    //     .then(data => renderClientData(data))
    //     .catch(error => showError(error));

    // Para la demo, simplemente devolvemos el control después de un tiempo
    setTimeout(() => {
      // La tabla ya tiene datos de ejemplo en el HTML
      console.log("Datos cargados correctamente");
    }, 1000);
  }
}

/**
 * Maneja errores en las peticiones AJAX
 * @param {Error} error - Error ocurrido
 */
function showError(error) {
  console.error("Error al cargar datos:", error);

  const tableBody = document.querySelector(".data-table tbody");
  if (tableBody) {
    tableBody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <h4>Error al cargar datos</h4>
                        <p>No se pudieron cargar los datos de clientes. Por favor, intente de nuevo más tarde.</p>
                    </div>
                </td>
            </tr>
        `;
  }
}

const animateElements = () => {
  const statCards = document.querySelectorAll(".stat-card");


  // Animate filters container
  const filtersContainer = document.querySelector(".filters-wrapper");
  if (filtersContainer) {
    setTimeout(() => {
      filtersContainer.classList.add("slide-in");
    }, 300);
  }

  // Add animation classes with delay
  statCards.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add("fade-in");
    }, 100 * index);
  });

 

  /* dataTables.forEach((table, index) => {
        setTimeout(() => {
            table.classList.add('fade-in');
        }, 500 + (100 * index));
    });
     */
};
