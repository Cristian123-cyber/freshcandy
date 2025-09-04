// Modal Functions
import { loadOrders } from "./gestionPedidos.js";
const orderModal = document.getElementById("orderModal");
const closeModalBtn = document.getElementById("closeModalBtn");
const printOrderBtn = document.getElementById("printOrderBtn");
const saveOrderBtn = document.getElementById("saveOrderBtn");
const orderStatusSelect = document.getElementById("orderStatus");

// Function to open the modal with order data
function openOrderModal(orderId) {
  // Fetch order details
  fetch("/../../controllers/PedidosController.php?action=getOrderDetails", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ orderId: orderId }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log("Detalles del pedido OBTENIDOS RETURN:", data.data);
        
        renderOrderDetails(data.data);
        // Show the modal with animation
        orderModal.classList.add("active");
        // Prevent body scrolling when modal is open
        document.body.style.overflow = "hidden";
      } else {
        alert("Error al cargar los detalles del pedido: " + data.message);
      }

      console.log(data);
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al cargar los detalles del pedido");
    });
}
const formatoCOP = new Intl.NumberFormat("es-CO", {
  style: "currency",
  currency: "COP",
  minimumFractionDigits: 0,
});
// Function to render order details in the modal
function renderOrderDetails(order) {
  // Update modal title with order ID
  document.querySelector(
    ".modal-header h2"
  ).textContent = `Detalle del Pedido #${order.id_pedido}`;

  // Update order status select
  if (orderStatusSelect) {
    // Fetch order states
    fetch("/../../controllers/PedidosController.php?action=getOrderStates", {
      method: "POST",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success && Array.isArray(data.data)) {
          // Clear existing options
          orderStatusSelect.innerHTML = "";

          // Add new options
          data.data.forEach((state) => {
            const option = document.createElement("option");
            option.value = state.id_estado;
            option.textContent = state.titulo_estado;
            if (state.id_estado === order.Estados_pedido_id_estado) {
              option.selected = true;
            }
            orderStatusSelect.appendChild(option);
          });
        } else {
          console.error("Error al cargar estados:", data.message);
        }
      })
      .catch((error) => {
        console.error("Error en fetch al cargar estados:", error);
      });
  }

  // Update totals
  const subtotal = order.subtotal;
  const discount = order.porcentaje_descuento
    ? (subtotal * order.porcentaje_descuento) / 100
    : 0;
  const total = parseFloat(order.monto_total);

  document.querySelector(".total-value.original").textContent =
    formatoCOP.format(subtotal);
  document.querySelector(
    ".total-row.discount .total-value"
  ).textContent = `-${formatoCOP.format(discount)}`;
  document.querySelector(".total-row.final .total-value").textContent =
    formatoCOP.format(total);

  // Update date
  const orderDate = new Date(order.fecha);
  document.querySelector(".order-info-value").textContent =
    orderDate.toLocaleString();

  // Update discount info
  const discountElement = document.querySelector(".discount-badge");
  if (order.codigo_promocional) {
    discountElement.innerHTML = `<i class="fas fa-percent"></i> Código: ${order.codigo_promocional} (-${order.porcentaje_descuento}%)`;
  } else {
    discountElement.innerHTML = `<i class="fas fa-percent"></i> Sin descuento`;
  }

  // Update shipping method
  const shippingElement = document.querySelector(".shipping-badge");
  shippingElement.className = `shipping-badge ${
    order.metodo_envio === "Entrega a domicilio" ? "delivery" : "pickup"
  }`;
  shippingElement.innerHTML = `<i class="fas fa-truck"></i> ${order.metodo_envio}`;

  // Update payment method
  const paymentElement = document.querySelector(".payment-badge");
  paymentElement.className = "payment-badge credit";
  paymentElement.innerHTML = `<i class="fas fa-credit-card"></i> ${order.metodo_pago}`;

  // Update products table
  const tbody = document.querySelector(".products-table tbody");
  tbody.innerHTML = "";
  order.productos.forEach((product) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td class="product-id">${product.Productos_id_producto}</td>
      <td class="product-name">${product.nombre_producto}</td>
      <td>${product.cantidad}</td>
      <td>${formatoCOP.format(parseFloat(product.precio_prod))}</td>
      <td class="product-price">${formatoCOP.format(
        parseFloat(product.precio_prod) * product.cantidad
      )}</td>
    `;
    tbody.appendChild(row);
  });

  // Update customer info
  document.querySelector("#customer-name").textContent =
    order.nombre_destinatario;
  document.querySelector("#customer-cc").textContent = order.cedula;
  document.querySelector("#customer-tel").textContent =
    order.telefono_destinatario;
  document.querySelector("#customer-dir").textContent =
    order.direccion_envio || "No especificada";

  // Update notes
  document.querySelector(".notes-content p").textContent =
    order.notas_adicionales || "Sin notas adicionales";

  saveOrderBtn.setAttribute("data-orderId", order.id_pedido);
}

// Function to close the modal
function closeOrderModal() {
  // Hide the modal with animation
  orderModal.classList.remove("active");

  // Restore body scrolling
  document.body.style.overflow = "auto";
}

// Function to save order changes
function saveOrderChanges(e) {
  const newStatus = orderStatusSelect.value;
  const orderId = e.target.getAttribute("data-orderId");
  console.log("EJECUTANDO SAVE ORDER CHANGES");
  

  

  fetch("/../../controllers/PedidosController.php?action=updateOrderStatus", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      orderId: orderId,
      statusId: newStatus,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        
        showNotification("Estado actualizado correctamente", "success");
        loadOrders();
        fetchAndRenderRecentOrders();

      } else {
        showNotification(
          "No se pudo actualizar el estado: " + data.message,
          "error"
        );
      }
    })
    .catch((error) => {
      showNotification("Error al actualizar el estado: " + error, "error");
    });
  // Close the modal after saving
  closeOrderModal();
}

// Fetch and render recent orders
const fetchAndRenderRecentOrders = async () => {
  try {
    const tbody = document.getElementById("recent-orders-tbody");
    if (!tbody) {
      return;
    }
    const response = await fetch(
      "/../../controllers/PedidosController.php?action=getRecentOrders",
      {
        method: "POST",
      }
    );
    const result = await response.json();
    
    tbody.innerHTML = ""; // Clear existing content
    
    if (!result.success) {
      console.error("Error al obtener pedidos recientes:", result.message);
      tbody.innerHTML = `
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <i class="fas fa-clipboard-list"></i>
              <div class="empty-state-title">No hay pedidos recientes</div>
              <p>No se han encontrado pedidos en el sistema.</p>
            </div>
          </td>
        </tr>
      `;
      return;
    }


    if (!result.data || result.data.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <i class="fas fa-clipboard-list"></i>
              <div class="empty-state-title">No hay pedidos recientes</div>
              <p>No se han encontrado pedidos en el sistema.</p>
            </div>
          </td>
        </tr>
      `;
      return;
    }

    result.data.forEach((order) => {
      const row = document.createElement("tr");

      // Format date
      const orderDate = new Date(order.fecha);
      const formattedDate = orderDate.toLocaleDateString("es-ES", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });

      // Format total with 2 decimal places
      const formattedTotal = parseFloat(order.monto_total).toFixed(2);
     

      // Determine status badge class
      let statusClass = "";
      switch (order.Estados_pedido_id_estado) {
        case 1:
          statusClass = "pending";
          break;
        case 2:
          statusClass = "processing";
          break;
        case 3:
          statusClass = "shipped";
          break;
        case 4:
          statusClass = "delivered";
          break;
        case 5:
          statusClass = "canceled";
          break;
        default:
          statusClass = "pending";
      }

      row.innerHTML = `
        <td>#${order.id_pedido}</td>
        <td>${order.nombre_cliente || order.nombre_destinatario}</td>
        <td>${formattedDate}</td>
        <td>$${formattedTotal}</td>
        <td><span class="status-badge ${statusClass}">${
        order.titulo_estado
      }</span></td>
        <td>
          <button class="action-btn btn-view" data-orderId="${
            order.id_pedido
          }">
            <i class="fas fa-eye"></i>
          </button>
        </td>
      `;

      tbody.appendChild(row);
    });
  } catch (error) {
    console.error("Error al cargar pedidos recientes:", error);
  }
};

// Event Listeners
closeModalBtn.addEventListener("click", closeOrderModal);
// Por esto:
saveOrderBtn.removeEventListener("click", saveOrderChanges); // Primero remueve por si acaso
saveOrderBtn.addEventListener("click", saveOrderChanges);

// Close modal if clicking outside the content
orderModal.addEventListener("click", function (event) {
  if (event.target === orderModal) {
    closeOrderModal();
  }
});

// For demonstration purposes, let's open the modal automatically
// In a real implementation, this would be triggered by clicking on a button in your table
// openOrderModal(12345);

export default openOrderModal;
