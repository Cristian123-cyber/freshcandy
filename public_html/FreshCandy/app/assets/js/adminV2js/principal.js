// main-dashboard.js - Funcionalidades principales del dashboard

import openOrderModal from "./modalPedidos.js";
import { showConfirmation } from "./confirmDialog.js";


document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("dashboard-link1").classList.add("active");

  const recentOrdersTbody = document.getElementById("recent-orders-tbody");
  const lowStockTbody = document.getElementById("low-stock-tbody");

  const btnWeek = document.getElementById("week-btn");
  const btnMonth = document.getElementById("month-btn");
  const btnYear = document.getElementById("year-btn");

  // Variables para almacenar las instancias de los gráficos
  let currentSalesChart = null;
  let currentProductsChart = null;

  btnWeek.addEventListener("click", function () {
    createSalesChartWeek();
  });

  btnMonth.addEventListener("click", function () {
    createSalesChartMonth();
  });

  btnYear.addEventListener("click", function () {
    createSalesChartYear();
  });

  //delegacion de eventos para evitar el uso de event listeners en cada fila de la tabla

  recentOrdersTbody.addEventListener("click", function (e) {
    if (e.target.classList.contains("btn-view")) {
      const orderId = e.target.getAttribute("data-orderId");
      openOrderModal(orderId);
    }
  });

  lowStockTbody.addEventListener("click", function (e) {
    if (e.target.classList.contains("restock")) {
      const button = e.target;
      let ingredientId = button.getAttribute("data-id");

      ingredientId = parseInt(ingredientId);

      if (isNaN(ingredientId)) {
        showNotification("ID de ingrediente inválido", "error");
        return;
      }

      // Abrir la modal de reabastecimiento con los datos del ingrediente
      window.openRestockModalAdmin(ingredientId);
    }
  });

  // Sales Chart
  const createSalesChartWeek = async () => {
    const ctx = document.getElementById("salesChart");

    if (!ctx) return;
    ctx.innerHTML = "";

    document.getElementById("chart-period").textContent = "de la semana";

    // Destruir el gráfico de ventas anterior si existe
    if (currentSalesChart) {
      currentSalesChart.destroy();
    }

    try {
      const response = await fetch(
        "/../../controllers/PedidosController.php?action=getSalesByWeek",
        {
          method: "POST",
        }
      );
      const result = await response.json();
      console.log(result);

      if (!result.success) {
        console.error(
          "Error al obtener datos de ventas por semana:",
          result.message
        );
        return;
      }

      const salesData = {
        labels: result.data.labels,
        datasets: [
          {
            label: "Ventas ($)",
            data: result.data.data,
            backgroundColor: "rgba(55, 223, 225, 0.2)",
            borderColor: "#37dfe1",
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: "#ffffff",
            pointBorderColor: "#37dfe1",
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
        ],
      };

      currentSalesChart = new Chart(ctx, {
        type: "line",
        data: salesData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: "rgba(0, 0, 0, 0.05)",
              },
            },
            x: {
              grid: {
                display: false,
              },
            },
          },
        },
      });
    } catch (error) {
      console.error("Error al crear el gráfico de ventas por semana:", error);
    }
  };

  const createSalesChartMonth = async () => {
    const ctx = document.getElementById("salesChart");
    if (!ctx) return;

    document.getElementById("chart-period").textContent = "del mes";

    ctx.innerHTML = "";

    // Destruir el gráfico de ventas anterior si existe
    if (currentSalesChart) {
      currentSalesChart.destroy();
    }

    try {
      const response = await fetch(
        "/../../controllers/PedidosController.php?action=getSalesByMonth",
        {
          method: "POST",
        }
      );
      const result = await response.json();

      if (!result.success) {
        console.error(
          "Error al obtener datos de ventas por mes:",
          result.message
        );
        return;
      }

      const salesData = {
        labels: result.data.labels,
        datasets: [
          {
            label: "Pedidos ($)",
            data: result.data.data,
            backgroundColor: "rgba(55, 223, 225, 0.2)",
            borderColor: "#37dfe1",
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: "#ffffff",
            pointBorderColor: "#37dfe1",
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
        ],
      };

      currentSalesChart = new Chart(ctx, {
        type: "line",
        data: salesData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: "rgba(0, 0, 0, 0.05)",
              },
            },
            x: {
              grid: {
                display: false,
              },
            },
          },
        },
      });
    } catch (error) {
      console.error("Error al crear el gráfico de ventas por mes:", error);
    }
  };

  const createSalesChartYear = async () => {
    const ctx = document.getElementById("salesChart");

    if (!ctx) return;
    ctx.innerHTML = "";

    document.getElementById("chart-period").textContent = "del año";

    // Destruir el gráfico de ventas anterior si existe
    if (currentSalesChart) {
      currentSalesChart.destroy();
    }

    try {
      const response = await fetch(
        "/../../controllers/PedidosController.php?action=getSalesByYear",
        {
          method: "POST",
        }
      );
      const result = await response.json();

      if (!result.success) {
        console.error(
          "Error al obtener datos de ventas por año:",
          result.message
        );
        return;
      }

      const salesData = {
        labels: result.data.labels,
        datasets: [
          {
            label: "Pedidos ($)",
            data: result.data.data,
            backgroundColor: "rgba(55, 223, 225, 0.2)",
            borderColor: "#37dfe1",
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: "#ffffff",
            pointBorderColor: "#37dfe1",
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
        ],
      };

      currentSalesChart = new Chart(ctx, {
        type: "line",
        data: salesData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: "rgba(0, 0, 0, 0.05)",
              },
            },
            x: {
              grid: {
                display: false,
              },
            },
          },
        },
      });
    } catch (error) {
      console.error("Error al crear el gráfico de ventas por año:", error);
    }
  };

  // Popular Products Chart
  const createProductsChart = async () => {
    const ctx = document.getElementById("productsChart");

    if (!ctx) return;

    // Destruir el gráfico de productos anterior si existe
    if (currentProductsChart) {
      currentProductsChart.destroy();
    }

    try {
      const response = await fetch(
        "/../../controllers/PedidosController.php?action=getPopularProducts",
        {
          method: "POST",
        }
      );
      const result = await response.json();
      console.log(result);

      if (!result.success) {
        console.error(
          "Error al obtener datos de productos populares:",
          result.message
        );
        return;
      }

      const productsData = {
        labels: result.data.labels,
        datasets: [
          {
            data: result.data.data,
            backgroundColor: [
              "#37dfe1", // Primary
              "#ff5add", // Secondary
              "#c750e0", // Secondary2
              "#5b8def", // Chart-4
              "#ffbd59", // Chart-5
            ],
            borderColor: "#ffffff",
            borderWidth: 2,
          },
        ],
      };

      currentProductsChart = new Chart(ctx, {
        type: "doughnut",
        data: productsData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "bottom",
              labels: {
                boxWidth: 12,
                padding: 15,
              },
            },
          },
          cutout: "70%",
        },
      });
    } catch (error) {
      console.error("Error al crear el gráfico de productos populares:", error);
    }
  };

  // Period Button Toggle
  const setupChartPeriodButtons = () => {
    const periodButtons = document.querySelectorAll(".chart-period-btn");

    periodButtons.forEach((button) => {
      button.addEventListener("click", function () {
        // Remove active class from all buttons
        periodButtons.forEach((btn) => btn.classList.remove("active"));

        // Add active class to clicked button
        this.classList.add("active");

        // Here you would update the chart data based on the selected period
        // For now, we'll just simulate the change with an animation
        const chartCanvas = this.closest(".chart-card").querySelector("canvas");
        if (chartCanvas) {
          chartCanvas.style.opacity = "0.5";
          setTimeout(() => {
            chartCanvas.style.opacity = "1";
          }, 300);
        }
      });
    });
  };

  // Apply animations to cards on load
  const animateElements = () => {
    const statCards = document.querySelectorAll(".stat-card");
    const chartCards = document.querySelectorAll(".chart-card");
    const dataTables = document.querySelectorAll(".data-card");

    // Add animation classes with delay
    statCards.forEach((card, index) => {
      setTimeout(() => {
        card.classList.add("fade-in");
      }, 100 * index);
    });

    chartCards.forEach((card, index) => {
      setTimeout(() => {
        card.classList.add("slide-in");
      }, 300 + 100 * index);
    });

    dataTables.forEach((table, index) => {
      setTimeout(() => {
        table.classList.add("fade-in");
      }, 500 + 100 * index);
    });
  };

  // Función para formatear la fecha de forma inteligente
  function formatearFechaInteligente(fechaStr) {
    const fecha = new Date(fechaStr);
    const ahora = new Date();

    // Comparar si es hoy
    if (
      fecha.getDate() === ahora.getDate() &&
      fecha.getMonth() === ahora.getMonth() &&
      fecha.getFullYear() === ahora.getFullYear()
    ) {
      return `Hoy, ${fecha.toLocaleTimeString("es-ES", {
        hour: "2-digit",
        minute: "2-digit",
      })}`;
    }
    // Mismo año
    if (fecha.getFullYear() === ahora.getFullYear()) {
      return `${fecha.toLocaleDateString("es-ES", {
        day: "2-digit",
        month: "2-digit",
      })}, ${fecha.toLocaleTimeString("es-ES", {
        hour: "2-digit",
        minute: "2-digit",
      })}`;
    }
    // Otro año
    return `${fecha.toLocaleDateString("es-ES", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    })}, ${fecha.toLocaleTimeString("es-ES", { hour: "2-digit", minute: "2-digit" })}`;
  }
  function getClass(tipo) {
    if (tipo === 1) return 'product-idea';
    else if (tipo === 2) return 'service';
    else if (tipo === 3) return 'improvement';
    else if (tipo === 4) return 'complaint';
    else return 'otras';
  }
  // Función para cargar y renderizar sugerencias recientes
  async function cargarSugerenciasRecientes() {
    try {
      const response = await fetch(
        "/../../controllers/SugerenciasAjaxController.php?action=getRecentSuggestions"
      );
      const result = await response.json();

      if (!result.success) {
        console.error(
          "Error al obtener sugerencias recientes:",
          result.message
        );
        return;
      }

      const contenedor = document.querySelector(
        ".suggestions-section .data-body"
      );
      if (!contenedor) return;
      contenedor.innerHTML = ""; // Limpiar contenido previo

      if (!result.data || result.data.length === 0) {
        contenedor.innerHTML = `
          <div class="empty-state">
            <i class="fas fa-lightbulb"></i>
            <p>No hay sugerencias recientes</p>
          </div>
        `;
        return;
      }

      contenedor.innerHTML = result.data
        .map((sug) => {
          const fechaStr = formatearFechaInteligente(sug.fecha);
          const tipoClase = getClass(parseInt(sug.id_tipo));

          return `
          <div class="suggestion-card" data-id="${sug.id_sugerencia}">
            <div class="suggestion-header">
              <div class="suggestion-user">
                <img src="../../assets//images//user (1).png" alt="User Avatar">
                <div>
                  <h4>${sug.nombre_cliente || "Usuario"}</h4>
                  <span class="suggestion-date">${fechaStr}</span>
                </div>
              </div>
              <span class="suggestion-type ${tipoClase}">${
            sug.nombre_tipo || ""
          }</span>
            </div>
            <h5 class="suggestion-title">${sug.titulo_sugerencia}</h5>
            <p class="suggestion-text">${sug.sugerencia_info}</p>
            <div class="suggestion-actions">
              <button class="suggestion-btn mark-read" ${
                sug.nombre_estado === "Revisada" ? "disabled" : ""
              }>
                <i class="fas fa-check"></i> ${
                  sug.nombre_estado === "Revisada"
                    ? "Revisada"
                    : "Marcar como revisado"
                }
              </button>
              <button class="suggestion-btn delete" style="background:#e74c3c;color:#fff;" title="Eliminar sugerencia">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        `;
        })
        .join("");

      // Añadir event listeners para los botones
      setupSuggestionButtons();
    } catch (error) {
      console.error("Error al cargar sugerencias recientes:", error);
    }
  }

  // Función para configurar los botones de sugerencias
  function setupSuggestionButtons() {
    // Event listener para marcar como revisado
    document.querySelectorAll(".suggestion-btn.mark-read").forEach((btn) => {
      if (btn.disabled) return; // Skip if already reviewed
      btn.addEventListener("click", async function () {
        const card = this.closest(".suggestion-card");
        const id = card?.getAttribute("data-id");
        if (!id) return;

        try {
          const response = await fetch(
            "/../../controllers/SugerenciasAjaxController.php?action=marcarRevisada",
            {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: `id=${encodeURIComponent(id)}`,
            }
          );
          const result = await response.json();

          if (result.success) {
            this.innerHTML = '<i class="fas fa-check-double"></i> Revisada';
            this.disabled = true;
            card.style.opacity = "0.6";
          } else {
            console.error("Error al marcar como revisada:", result.message);
          }
        } catch (error) {
          console.error("Error al marcar como revisada:", error);
        }
      });
    });

    // Event listener para eliminar
    document.querySelectorAll(".suggestion-btn.delete").forEach((btn) => {
      btn.addEventListener("click", function () {
        const card = this.closest(".suggestion-card");
        const id = card?.getAttribute("data-id");
        if (!id) return;


        eliminarSugerencia(id);



        
      });
    });



  }

  // Función para eliminar sugerencia
  function eliminarSugerencia(id) {
  
  
    showConfirmation({
          title: `Eliminar Sugerencia"`,
          message: `¿Estás seguro de eliminar esta sugerencia permanentemente? Todos los datos asociados se perderán.`,
          type: "delete",
          confirmText: "Eliminar",
          callback: async function () {
            try {
              await deleteAction(id);
            } catch (error) {
              console.error("Error en la confirmación:", error);
              showError(`Error al eliminar la sugerencia: ${error.message}`);
            }
          },
        });
    
  }
  
  
  async function deleteAction(id){
  
    // Animación de la card antes de eliminar
        const card = document.querySelector(`.suggestion-card[data-id='${id}']`);
        if (card) {
          card.style.transition = 'all 0.5s cubic-bezier(.68,-0.55,.27,1.55)';
          card.style.transform = 'translateX(100%) scale(0.8)';
          card.style.opacity = '0';
        }
        setTimeout(() => {
          fetch('/../../controllers/SugerenciasAjaxController.php?action=marcarEliminada', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}`
          })
          .then(res => res.json())
          .then(json => {
            console.log(json);
            if (json.success) {
              showNotification("Sugerencia eliminada con exito !", "success");
              cargarSugerenciasRecientes();
            
              
            } else {
              showNotification("No se pudo eliminar la sugerencia !", "error");
  
              
            }
          })
          .catch(() => showNotification("Error de red al eliminar la sugerencia !", "error"));
        }, 400);
  
  }

  // Fetch and render recent orders
  const fetchAndRenderRecentOrders = async () => {
    try {
      const response = await fetch(
        "/../../controllers/PedidosController.php?action=getRecentOrders",
        {
          method: "POST",
        }
      );
      const result = await response.json();

      const tbody = document.getElementById("recent-orders-tbody");
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

  // Fetch and render low stock ingredients
  const fetchAndRenderLowStock = async () => {
    try {
      const response = await fetch(
        "/../../controllers/IngredientsController.php?action=getLowStockIngredients",
        {
          method: "POST",
        }
      );
      const result = await response.json();

      if (!result.success) {
        console.error(
          "Error al obtener ingredientes con stock bajo:",
          result.message
        );
        return;
      }

      const tbody = document.getElementById("low-stock-tbody");
      tbody.innerHTML = ""; // Clear existing content

      if (!result.data || result.data.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="5">
              <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <div class="empty-state-title">No hay ingredientes por reponer</div>
                <p>El inventario está en buen estado.</p>
              </div>
            </td>
          </tr>
        `;
        return;
      }

      result.data.forEach((ingredient) => {
        const row = document.createElement("tr");

        // Determine stock badge class
        let statusClass =
          ingredient.Estados_Stock_id_estado === 3
            ? "critical"
            : ingredient.Estados_Stock_id_estado === 4
            ? "out-of-stock"
            : "low";
        let statusText =
          ingredient.Estados_Stock_id_estado === 3
            ? "Crítico"
            : ingredient.Estados_Stock_id_estado === 4
            ? "Agotado"
            : "Bajo";

        row.innerHTML = `
          <td>${ingredient.nombre_ing}</td>
          <td>${ingredient.categoria}</td>
          <td>${ingredient.stock_ing} ${ingredient.unidad}</td>
          <td><span class="stock-badge ${statusClass}">${statusText}</span></td>
          <td>
            <button class="action-btn restock" data-id="${ingredient.id_ingrediente}">
              <i class="fas fa-plus-circle"></i> Reponer
            </button>
          </td>
        `;

        tbody.appendChild(row);
      });
    } catch (error) {
      console.error("Error al cargar ingredientes con stock bajo:", error);
    }
  };

  // Fetch and render dashboard statistics
  const fetchDashboardStats = async () => {
    try {
      const response = await fetch(
        "/../../controllers/StatsController.php?action=getStatsForDashboard",
        {
          method: "POST",
        }
      );
      const result = await response.json();

      if (!result.success) {
        console.error(
          "Error al obtener estadísticas del dashboard:",
          result.message
        );
        return;
      }

      // Actualizar ventas de hoy
      const ventasHoy = document.getElementById("ventas-hoy");
     
      if (ventasHoy) {
        ventasHoy.textContent = `${result.data.pedidos_hoy || 0}`;
      }

      // Actualizar pedidos pendientes
      const pedidosPendientes = document.getElementById("pedidos-pendientes");
      if (pedidosPendientes) {
        pedidosPendientes.textContent = result.data.pedidos_pendientes || 0;
      }

      // Actualizar stock bajo
      const stockBajo = document.getElementById("stock-bajo");
      if (stockBajo) {
        stockBajo.textContent = result.data.stock_bajo || 0;
      }

      // Actualizar nuevas sugerencias
      const nuevasSugerencias = document.getElementById("nuevas-sugerencias");
     
      if (nuevasSugerencias) {
        nuevasSugerencias.textContent = result.data.nuevas_sugerencias || 0;
      }
    } catch (error) {
      console.error("Error al cargar estadísticas del dashboard:", error);
    }
  };

  // Initialize all functions
  createSalesChartWeek();
  createProductsChart();
  setupChartPeriodButtons();
  animateElements();
  setupSuggestionButtons();
  fetchAndRenderRecentOrders();
  fetchAndRenderLowStock();
  fetchDashboardStats();
  cargarSugerenciasRecientes();
});
