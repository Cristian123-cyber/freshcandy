import { showConfirmation as mostrarDangerModal } from "./confirmDialog.js";

// Inicializar el módulo de inventario cuando el DOM esté cargado
document.addEventListener("DOMContentLoaded", initInventory);

async function initInventory() {
  animateElements();
  document.getElementById("dashboard-link3").classList.add("active");

  const inventoryTable = document.querySelector("#tableIngredientesBody");

  inventoryTable.addEventListener("click", handleTableClick);

  const addIngredientBtn = document.getElementById("openAddModal");

  addIngredientBtn.addEventListener("click", function () {
    window.openAddIngredientModal();
  });

  // Inicializar los filtros
  await initializeFilters();

  // Cargar los ingredientes iniciales
  const ingredientes = await getAllIngredients();
  if (ingredientes === false) {
    showNotification("Error al obtener los ingredientes", "error");
  } else {
    renderIngredientsTable(ingredientes);
  }

  // Configurar los event listeners para los filtros
  setupFilterListeners();

  console.log("Inventario.js cargado y DOM listo.");
  loadInventoryStats();
}

async function initializeFilters() {
  try {
    // Obtener categorías
    const categories = await getCategories();
    if (categories) {
      renderCategoriesFilter(categories);
    }

    // Obtener estados de stock
    const states = await getStockStates();
    if (states) {
      renderStatesFilter(states);
    }
  } catch (error) {
    console.error("Error al inicializar los filtros:", error);
    showNotification("Error al cargar los filtros", "error");
  }
}

async function getStockStates() {
  try {
    const response = await fetch(
      "../../controllers/IngredientsController.php?action=getStockStates",
      {
        method: "POST",
      }
    );

    const data = await response.json();
    if (!data.success) {
      showNotification(data.message || "Error al obtener los estados", "error");
      return false;
    }

    return data.data.states;
  } catch (error) {
    console.error("Error al obtener los estados:", error);
    showNotification("Error al obtener los estados", "error");
    return false;
  }
}

function renderCategoriesFilter(categories) {
  const categorySelect = document.getElementById("category-filter");
  if (!categorySelect) return;

  // Limpiar el select
  categorySelect.innerHTML = '<option value="">Todas las categorías</option>';

  // Agregar las categorías
  categories.forEach((category) => {
    const option = document.createElement("option");
    option.value = category.id_categoria;
    option.textContent = category.titulo_categoria;
    categorySelect.appendChild(option);
  });
}

function renderStatesFilter(states) {
  const stateSelect = document.getElementById("status-filter");
  if (!stateSelect) return;

  // Limpiar el select
  stateSelect.innerHTML = '<option value="">Todos los estados</option>';

  // Agregar los estados
  states.forEach((state) => {
    const option = document.createElement("option");
    option.value = state.id_estado;
    option.textContent = state.titulo_estado;
    stateSelect.appendChild(option);
  });
}

function setupFilterListeners() {
  const searchInput = document.getElementById("products-search");
  const categoryFilter = document.getElementById("category-filter");
  const statusFilter = document.getElementById("status-filter");

  // Función para aplicar los filtros
  const applyFilters = async () => {
    const searchTerm = searchInput.value.trim();
    const categoryId = categoryFilter.value;
    const stateId = statusFilter.value;

    try {
      const url = new URL(
        "/controllers/IngredientsController.php",
        window.location.origin
      );
      url.searchParams.append("action", "searchIngredients");
      url.searchParams.append("search", searchTerm);
      if (categoryId) url.searchParams.append("category", categoryId);
      if (stateId) url.searchParams.append("state", stateId);

    

      const response = await fetch(url.toString(), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
      });

      const data = await response.json();
      

      if (data.success) {
        renderIngredientsTable(data.data.ingredients);
      } else {
        showNotification(
          data.message || "Error al aplicar los filtros",
          "error"
        );
      }
    } catch (error) {
      console.error("Error al aplicar los filtros:", error);
      showNotification("Error al aplicar los filtros", "error");
    }
  };

  // Event listeners para los filtros
  searchInput.addEventListener("input", debounce(applyFilters, 300));
  categoryFilter.addEventListener("change", applyFilters);
  statusFilter.addEventListener("change", applyFilters);
}

// Función debounce para evitar demasiadas llamadas al servidor
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

export async function reloadIngredientsTable() {
  const ingredientes = await getAllIngredients();
  if (ingredientes === false) {
    showNotification("Error al obtener los ingredientes", "error");
  } else {
    renderIngredientsTable(ingredientes);
  }
}
function handleTableClick(event) {
  if (event.target.classList.contains("btn-edit")) {
    handleEditClick(event);
  }
  if (event.target.classList.contains("btn-restock")) {
    handleRestockClick(event);
  }
  if (event.target.classList.contains("btn-delete")) {
    const boton = event.target;
    let idIngredient = boton.getAttribute("data-id");
    idIngredient = parseInt(idIngredient);

    if (isNaN(idIngredient) || idIngredient === null) {
      showNotification("ID de ingrediente inválido", "error");
      return;
    }

    const nameIngredient = boton.getAttribute("data-name");

    mostrarDangerModal({
      title: `Eliminar "${nameIngredient}"`,
      message: `¿Estás seguro de eliminar "${nameIngredient}" permanentemente? Todos los datos asociados se perderán.`,
      type: "delete",
      confirmText: "Eliminar",
      callback: function () {
        fetch(
          `../../controllers/IngredientsController.php?action=deleteIngredient&id=${idIngredient}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
          }
        )
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              showNotification(data.message, "success");
              reloadIngredientsTable();
            } else {
              console.log(data);
              showNotification(
                data.message || "Error al eliminar el ingrediente",
                "error"
              );
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            showNotification("Error al procesar la solicitud", "error");
          });
      },
    });
  }
}

// Apply animations to cards on load
const animateElements = () => {
  const statCards = document.querySelectorAll(".stat-card");
  const dataTables = document.querySelectorAll(".data-card");

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

  dataTables.forEach((table, index) => {
    setTimeout(() => {
      table.classList.add("fade-in");
    }, 500 + 100 * index);
  });
};

function animateTableRows() {
  const rows = document.querySelectorAll(".inventory-table tbody tr");
  rows.forEach((row, index) => {
    row.style.opacity = "0";
    row.style.transform = "translateY(10px)";
    row.style.animation = `fadeIn 0.3s ease forwards ${index * 0.1}s`;
  });
}

// Función para obtener las categorías de ingredientes
export async function getCategories() {
  try {
    const response = await fetch(
      "../../controllers/IngredientsController.php?action=getCategories",
      {
        method: "POST",
      }
    );

    const data = await response.json();
    console.log("Categorías:", data);

    if (!data.success) {
      showNotification(
        data.message || "Error al obtener las categorías",
        "error"
      );
      return false;
    }

    return data.data.categories;
  } catch (error) {
    console.error("Error al obtener las categorías:", error);
    showNotification("Error al obtener las categorías", "error");
    return false;
  }
}

// Función para renderizar las categorías en el select
function renderCategories(categories) {
  const categorySelect = document.getElementById("edit-ingredient-category");
  if (!categorySelect) return;

  // Limpiar el select
  categorySelect.innerHTML = '<option value="">Seleccionar categoría</option>';

  // Agregar las categorías
  categories.forEach((category) => {
    const option = document.createElement("option");
    option.value = category.id_categoria;
    option.textContent = category.titulo_categoria;
    categorySelect.appendChild(option);
  });
}

// Función para obtener las unidades de medida
export async function getUnits() {
  try {
    const response = await fetch(
      "../../controllers/IngredientsController.php?action=getUnits",
      {
        method: "POST",
      }
    );

    const data = await response.json();
    console.log("Unidades:", data);

    if (!data.success) {
      showNotification(
        data.message || "Error al obtener las unidades",
        "error"
      );
      return false;
    }

    return data.data.units;
  } catch (error) {
    console.error("Error al obtener las unidades:", error);
    showNotification("Error al obtener las unidades", "error");
    return false;
  }
}

// Función para renderizar las unidades en el select
function renderUnits(units) {
  const unitSelect = document.getElementById("edit-ingredient-unit");
  if (!unitSelect) return;

  // Limpiar el select
  unitSelect.innerHTML = '<option value="">Seleccionar unidad</option>';

  // Agregar las unidades
  units.forEach((unit) => {
    const option = document.createElement("option");
    option.value = unit.id_unidad;
    option.textContent = `${unit.abrev_unidad}`;
    unitSelect.appendChild(option);
  });
}

// Función para manejar el clic en el botón de editar
async function handleEditClick(event) {
  const button = event.target;
  let ingredientId = button.dataset.id;

  console.log(ingredientId);

  try {
    // Obtener las categorías y unidades primero
    const [categories, units] = await Promise.all([
      getCategories(),
      getUnits(),
    ]);

    if (categories === false || units === false) {
      return;
    }

    // Renderizar categorías y unidades
    renderCategories(categories);
    renderUnits(units);

    // Obtener los datos del ingrediente
    const response = await fetch(
      `../../controllers/IngredientsController.php?action=getIngredientById&id=${ingredientId}`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      }
    );

    const data = await response.json();
    console.log(data);

    if (!data.success) {
      showNotification(
        data.message || "Error al obtener los datos del ingrediente",
        "error"
      );
      return;
    }

    // Abrir la modal de edición con los datos del ingrediente
    window.openEditIngredientModal(data.data.ingredient);
  } catch (error) {
    console.error("Error al obtener los datos del ingrediente:", error);
    showNotification("Error al obtener los datos del ingrediente", "error");
  }
}

// Función para manejar el clic en el botón de reabastecimiento
function handleRestockClick(event) {
  const button = event.target;
  let ingredientId = button.dataset.id;

  ingredientId = parseInt(ingredientId);

  if (isNaN(ingredientId)) {
    showNotification("ID de ingrediente inválido", "error");
    return;
  }

  // Abrir la modal de reabastecimiento con los datos del ingrediente
  window.openRestockModal(ingredientId);
}

// Función para determinar la clase del badge según el estado
function getStockBadgeClass(estado) {
  switch (estado.toLowerCase()) {
    case "optimo":
      return "ok";
    case "bajo":
      return "low";
    case "crítico":
      return "critical";
    case "agotado":
      return "out-of-stock";
    default:
      return "ok";
  }
}

// Función para renderizar la tabla de ingredientes
function renderIngredientsTable(ingredientes) {
  const tableBody = document.getElementById("tableIngredientesBody");
  if (!tableBody) return;

  // Limpiar la tabla
  tableBody.innerHTML = "";
  console.log(ingredientes);

  // Renderizar cada ingrediente
  ingredientes.forEach((ingrediente) => {
    const row = document.createElement("tr");
    row.innerHTML = `
            <td>#${ingrediente.id_ingrediente}</td>
            <td>${ingrediente.nombre_ing}</td>
            <td>${ingrediente.categoria}</td>
            <td>${ingrediente.stock_ing} ${ingrediente.unidad}</td>
            <td><span class="stock-badge ${getStockBadgeClass(
              ingrediente.estado
            )}">${ingrediente.estado}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon btn-edit" data-id="${
                      ingrediente.id_ingrediente
                    }" title="Editar ingrediente">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon btn-restock" data-id="${
                      ingrediente.id_ingrediente
                    }" title="Reabastecer stock">
                        <i class="fas fa-box-open"></i>
                    </button>
                    <button class="btn-icon btn-delete" data-id="${
                      ingrediente.id_ingrediente
                    }" data-name="${
      ingrediente.nombre_ing
    }" title="Eliminar ingrediente">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
    tableBody.appendChild(row);
  });

  // Animar las filas después de renderizar
  animateTableRows();
}

async function getAllIngredients() {
  try {
    const response = await fetch(
      "../../controllers/IngredientsController.php?action=getAllIngredients",
      {
        method: "POST",
      }
    );

    const data = await response.json();
    console.log(data);

    if (data.success && data.data) {
      return data.data.ingredients;
    } else {
      console.error("Error al obtener ingredientes:", data.message);
      return false;
    }
  } catch (error) {
    console.error("Error en la petición de ingredientes:", error);
    return false;
  }
}

// Función para cargar las estadísticas de inventario
function loadInventoryStats() {
  console.log("Cargando estadísticas de inventario...");
  fetch("/../../controllers/StatsController.php?action=getStatsForInventario", {
    method: "POST",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la respuesta del servidor");
      }
      return response.json();
    })
    .then((data) => {
      console.log("Estadísticas de inventario recibidas:", data);
      if (data.success && data.data) {
        // Actualizar los elementos HTML con los datos recibidos
        const stockBajoElement = document.getElementById("stock-bajo-count");
        const sinExistenciasElement = document.getElementById(
          "sin-existencias-count"
        );
        const criticosElement = document.getElementById(
          "ingredientes-criticos-count"
        );

        if (stockBajoElement) {
          stockBajoElement.textContent = data.data.productos_stock_bajo || "0";
        }
        if (sinExistenciasElement) {
          sinExistenciasElement.textContent =
            data.data.productos_sin_existencias || "0";
        }
        if (criticosElement) {
          criticosElement.textContent = data.data.productos_criticos || "0";
        }

        // Animar las tarjetas después de actualizar los datos
        animateElements();
      } else {
        console.error(
          "Error o datos inválidos al cargar estadísticas de inventario:",
          data.message
        );
      }
    })
    .catch((error) => {
      console.error(
        "Error en la petición de estadísticas de inventario:",
        error
      );
    });
}
