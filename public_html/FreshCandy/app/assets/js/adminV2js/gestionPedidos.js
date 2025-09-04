import openOrderModal from "./modalPedidos.js";

function init() {
  animateElements();
  loadOrderStates();
  loadOrders();
  loadOrderStats();

  document
    .getElementById("aplicarFiltros")
    ?.addEventListener("click", loadOrders);
  document.getElementById("buscar")?.addEventListener("input", loadOrders);
  document
    .getElementById("filtroEstado")
    ?.addEventListener("change", loadOrders);
  document
    .getElementById("date-filter")
    ?.addEventListener("change", loadOrders);

  const tbody = document.querySelector("#orderTableBody");
  tbody?.addEventListener("click", handleOrderClick);
}

function loadOrderStates() {
  fetch("/../../controllers/PedidosController.php?action=getOrderStates", {
    method: "POST",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success && Array.isArray(data.data)) {
        populateStateFilter(data.data);
      } else {
        console.error("Error al cargar estados:", data.message);
      }
    })
    .catch((error) => {
      console.error("Error en fetch al cargar estados:", error);
    });
}

function populateStateFilter(states) {
  const selectElement = document.getElementById("filtroEstado");
  if (!selectElement) {
    return;
  }

  selectElement.innerHTML = '<option value="todos">Todos los estados</option>';

  states.forEach((state) => {
    const option = document.createElement("option");
    option.value = state.id_estado;
    option.textContent = state.titulo_estado;
    selectElement.appendChild(option);
  });
}

// --- PAGINACIÓN FRONTEND PARA PEDIDOS ---
let allOrders = [];
let pedidosPaginationConfig = {
  currentPage: 1,
  itemsPerPage: 5,
  totalItems: 0,
  totalPages: 1
};

// Elementos DOM para la paginación de pedidos
const pedidosPaginationFrom = document.getElementById('pagination-from');
const pedidosPaginationTo = document.getElementById('pagination-to');
const pedidosPaginationTotal = document.getElementById('pagination-total');
const pedidosPaginationCurrentPage = document.getElementById('pagination-current-page');
const pedidosPaginationTotalPages = document.getElementById('pagination-total-pages');
const pedidosPaginationFirst = document.getElementById('pagination-first');
const pedidosPaginationPrev = document.getElementById('pagination-prev');
const pedidosPaginationNext = document.getElementById('pagination-next');
const pedidosPaginationLast = document.getElementById('pagination-last');
const pedidosItemsPerPageSelect = document.getElementById('items-per-page');

// Inicializar paginación de pedidos al cargar el DOM
// (Solo si existen los elementos de paginación en la vista de pedidos)
document.addEventListener('DOMContentLoaded', function() {
  if (pedidosPaginationFirst && pedidosPaginationPrev && pedidosPaginationNext && pedidosPaginationLast && pedidosItemsPerPageSelect) {
    pedidosPaginationFirst.addEventListener('click', () => goToPedidosPage(1));
    pedidosPaginationPrev.addEventListener('click', () => goToPedidosPage(pedidosPaginationConfig.currentPage - 1));
    pedidosPaginationNext.addEventListener('click', () => goToPedidosPage(pedidosPaginationConfig.currentPage + 1));
    pedidosPaginationLast.addEventListener('click', () => goToPedidosPage(pedidosPaginationConfig.totalPages));
    pedidosItemsPerPageSelect.addEventListener('change', function() {
      pedidosPaginationConfig.itemsPerPage = parseInt(this.value);
      pedidosPaginationConfig.currentPage = 1;
      renderPaginatedPedidosTable();
    });
  }
});

// Sobrescribir loadOrders para guardar todos los pedidos en el arreglo global y paginar en frontend
export function loadOrders() {
  const fecha = document.getElementById("date-filter")?.value;
  let estado = document.getElementById("filtroEstado")?.value;
  const busqueda = document.getElementById("buscar")?.value;

  // Si el valor es 'todos', lo convertimos a null
  if (estado === "todos") {
    estado = null;
  }

  fetch("/../../controllers/PedidosController.php?action=getPedidos", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      fecha: fecha || null,
      estado: estado,
      tituloOcontenido: busqueda || null,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        allOrders = data.data || [];
        renderPaginatedPedidosTable();
      } else {
        allOrders = [];
        renderPaginatedPedidosTable();
        console.error("Error en la respuesta:", data.message);
      }
    })
    .catch((error) => {
      allOrders = [];
      renderPaginatedPedidosTable();
      console.error("Error al cargar los pedidos:", error);
    });
}

function renderPaginatedPedidosTable() {
  const totalItems = allOrders.length;
  pedidosPaginationConfig.totalItems = totalItems;
  pedidosPaginationConfig.totalPages = Math.max(1, Math.ceil(totalItems / pedidosPaginationConfig.itemsPerPage));

  // Ajustar página actual si se sale de rango
  if (pedidosPaginationConfig.currentPage > pedidosPaginationConfig.totalPages) {
    pedidosPaginationConfig.currentPage = pedidosPaginationConfig.totalPages;
  }
  if (pedidosPaginationConfig.currentPage < 1) {
    pedidosPaginationConfig.currentPage = 1;
  }

  const from = (pedidosPaginationConfig.currentPage - 1) * pedidosPaginationConfig.itemsPerPage;
  const to = Math.min(from + pedidosPaginationConfig.itemsPerPage, totalItems);
  const pageItems = allOrders.slice(from, to);

  updateOrdersTable(pageItems);
  updatePedidosPaginationInfo();
}

function updatePedidosPaginationInfo() {
  if (!pedidosPaginationFrom) return;
  const from = (pedidosPaginationConfig.currentPage - 1) * pedidosPaginationConfig.itemsPerPage + 1;
  const to = Math.min(from + pedidosPaginationConfig.itemsPerPage - 1, pedidosPaginationConfig.totalItems);

  pedidosPaginationFrom.textContent = pedidosPaginationConfig.totalItems === 0 ? 0 : from;
  pedidosPaginationTo.textContent = to;
  pedidosPaginationTotal.textContent = pedidosPaginationConfig.totalItems;
  pedidosPaginationCurrentPage.textContent = pedidosPaginationConfig.currentPage;
  pedidosPaginationTotalPages.textContent = pedidosPaginationConfig.totalPages;

  pedidosPaginationFirst.disabled = pedidosPaginationConfig.currentPage <= 1;
  pedidosPaginationPrev.disabled = pedidosPaginationConfig.currentPage <= 1;
  pedidosPaginationNext.disabled = pedidosPaginationConfig.currentPage >= pedidosPaginationConfig.totalPages;
  pedidosPaginationLast.disabled = pedidosPaginationConfig.currentPage >= pedidosPaginationConfig.totalPages;
}

function goToPedidosPage(page) {
  if (page < 1 || page > pedidosPaginationConfig.totalPages || page === pedidosPaginationConfig.currentPage) {
    return;
  }
  pedidosPaginationConfig.currentPage = page;
  renderPaginatedPedidosTable();
}

function updateOrdersTable(orders) {
  const tbody = document.querySelector("#orderTableBody");
  if (!tbody) {
    return;
  }

  tbody.innerHTML = "";

  if (!Array.isArray(orders) || orders.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td colspan="7" class="no-data-message">
        No hay pedidos disponibles
      </td>
    `;
    tbody.appendChild(row);
    return;
  }

  orders.forEach((order) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>#${order.id_pedido}</td>
      <td>${order.nombre_cliente}</td>
      <td>${formatDate(order.fecha)}</td>
      <td>$${order.monto_total}</td>
      <td>
        <span class="shipping-badge ${getShippingClass(order.metodo_envio)}">
          ${order.metodo_envio}
        </span>
      </td>
      <td>
        <span class="status-badge ${getStatusClass(
          order.Estados_pedido_id_estado
        )}">
          ${order.titulo_estado}
        </span>
      </td>
      <td class="action-buttons">
        <button class="action-btn btn-view" data-orderId="${order.id_pedido}">
          <i class="fas fa-eye"></i>
        </button>
        <button class="action-btn btn-check" data-orderId="${order.id_pedido}">
          <i class="fas fa-check-circle"></i>
        </button>
        <button class="action-btn btn-cancelOrder" data-orderId="${
          order.id_pedido
        }">
          <i class="fas fa-times"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function getStatusClass(status) {
  const statusMap = {
    1: "pending",
    2: "processing",
    3: "shipped",
    4: "delivered",
    5: "canceled",
  };
  return statusMap[status] || "pending";
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("es-ES", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

function handleOrderClick(event) {
  if (event.target.classList.contains("btn-view")) {
    const orderId = event.target.getAttribute("data-orderid");
    openOrderModal(orderId);
  }

  if (event.target.classList.contains("btn-check")) {
    const orderId = event.target.getAttribute("data-orderId");
    const status = event.target.closest("tr").querySelector(".status-badge");
    // Estado 3 = Enviado
    fetch("/../../controllers/PedidosController.php?action=updateOrderStatus", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        orderId: orderId,
        statusId: 3,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          status.className = "status-badge shipped";
          status.textContent = "Enviado";
        } else {
          alert("No se pudo actualizar el estado: " + data.message);
        }
      })
      .catch((error) => {
        alert("Error al actualizar el estado: " + error);
      });
  }

  if (event.target.classList.contains("btn-cancelOrder")) {
    const orderId = event.target.getAttribute("data-orderId");
    const status = event.target.closest("tr").querySelector(".status-badge");
    // Estado 5 = Cancelado
    fetch("/../../controllers/PedidosController.php?action=updateOrderStatus", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        orderId: orderId,
        statusId: 5,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          status.className = "status-badge canceled";
          status.textContent = "Cancelado";
        } else {
          alert("No se pudo actualizar el estado: " + data.message);
        }
      })
      .catch((error) => {
        alert("Error al actualizar el estado: " + error);
      });
  }
}

function animateElements() {
  const statCards = document.querySelectorAll(".stat-card");
  statCards.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add("fade-in");
    }, 100 * index);
  });

  const filtersContainer = document.querySelector(".filters-wrapper");
  if (filtersContainer) {
    setTimeout(() => {
      filtersContainer.classList.add("slide-in");
    }, 300);
  }

  const dataCards = document.querySelectorAll(".data-card");
  dataCards.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add("fade-in");
    }, 300 + index * 100);
  });
}

function getShippingClass(methodName) {
  const shippingMap = {
    "Entrega a Domicilio": "delivery",
    "Recogida en tienda": "pickup",
  };
  return shippingMap[methodName] || "default-shipping";
}

function loadOrderStats() {
  fetch("/../../controllers/StatsController.php?action=getStatsForPedidos", {
    method: "POST",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la respuesta del servidor");
      }
      return response.json();
    })
    .then((data) => {
      if (data.success && data.data) {
        const pedidosHoy = data.data.pedidos_hoy || 0;
        const pedidosPendientes = data.data.pedidos_pendientes || 0;
        const pedidosCompletados = data.data.pedidos_completados || 0;
        const pedidosCancelados = data.data.pedidos_cancelados || 0;
        const pedidosHoyElement = document.querySelector(
          ".stat-card.sales .stat-value"
        );

        if (pedidosHoyElement) {
          pedidosHoyElement.textContent = pedidosHoy;
        }

        const pendientesElement = document.querySelector(
          ".stat-card.orders .stat-value"
        );

        if (pendientesElement) {
          pendientesElement.textContent = pedidosPendientes;
        }

        const completadosElement = document.querySelector(
          ".stat-card.inventory .stat-value"
        );

        if (completadosElement) {
          completadosElement.textContent = pedidosCompletados;
        }

        const canceladosElement = document.querySelector(
          ".stat-card.suggestions .stat-value"
        );

        if (canceladosElement) {
          canceladosElement.textContent = pedidosCancelados;
        }

        // Badge de pedidos en sidebar
        const badgePedidos = document.querySelector(
          "#dashboard-link4 .notification-badge"
        );
        if (badgePedidos) badgePedidos.textContent = pedidosPendientes;

        // Sugerencias
        fetch(
          "/../../controllers/StatsController.php?action=getStatsForSugerencias",
          { method: "POST" }
        )
          .then((res) => res.json())
          .then((sugData) => {
            const sugerenciasPendientes =
              sugData.data && sugData.data.sugerencias_pendientes
                ? sugData.data.sugerencias_pendientes
                : 0;
            // Badge de sugerencias en sidebar
            const badgeSugerencias = document.querySelector(
              "#dashboard-link5 .notification-badge"
            );
            if (badgeSugerencias)
              badgeSugerencias.textContent = sugerenciasPendientes;
          });
        animateElements();
      } else {
        console.error(
          "Error o datos inválidos al cargar estadísticas de pedidos:",
          data.message
        );
      }
    })
    .catch((error) => {
      console.error("Error en la petición de estadísticas de pedidos:", error);
    });
}

document.addEventListener("DOMContentLoaded", init);
