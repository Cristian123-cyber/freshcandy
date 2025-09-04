// Importación del módulo
import { showConfirmation } from "./confirmDialog.js";



// Variables globales para búsqueda y filtros
let searchTimeout = null;
let currentFilters = {
  termino: "",
  categoria: "",
  precio_min: "",
  precio_max: "",
  ordenamiento: "",
};

// Función para mostrar confirmación (si es necesaria aquí, mantenerla)

// La función loadProducts parece redundante si searchProducts maneja la carga inicial y filtrada.
// Voy a eliminarla y depender de searchProducts para la carga inicial en DOMContentLoaded.
// REMOVED: function loadProducts() { ... }

// Función para mostrar productos (MANTENER esta función, ya que es llamada por searchProducts para renderizar)
function renderProducts(products) {
  console.log("Iniciando renderProducts...");
  console.log("Productos a renderizar:", products);
  const gridView = document.getElementById("gridView");

  // Limpiar vista actual
  gridView.innerHTML = "";

  if (!products || products.length === 0) {
    console.log("No hay productos o el array está vacío.");
    gridView.innerHTML =
      '<div class="no-products-message">No hay productos disponibles</div>';
  animateElements();

    return;
  }

  products.forEach((product) => {
    
    const card = createProductCard(product);
    if (card) {
      //console.log('Tarjeta creada, añadiendo al grid:', card);
      gridView.appendChild(card);
    } else {
      console.error(
        "createProductCard devolvió null o undefined para:",
        product
      );
    }
  });
  

  // Llamar a la función de animación después de que las tarjetas se han añadido al DOM
  animateElements();
}

// Función para crear tarjeta de producto (MANTENER esta función)
function createProductCard(product) {
  //console.log('Dentro de createProductCard para:', product);
  const card = document.createElement("div");
  card.className = "product-card";
  card.id = `product${product.id}`;

  // Formatear la fecha de creación
  let formattedDate = "Sin fecha";
  if (product.fecha_creacion) {
    try {
      // Intentar parsear la fecha YYYY-MM-DD y formatearla a DD/MM/YYYY
      const [year, month, day] = product.fecha_creacion.split("-");
      if (year && month && day) {
        formattedDate = `${day}/${month}/${year}`;
      } else {
        formattedDate = new Date(product.fecha_creacion).toLocaleDateString();
      }
    } catch (e) {
      console.error("Error formateando fecha:", product.fecha_creacion, e);
      formattedDate = "Fecha inválida";
    }
  } else if (product.date_added) {
    // Por si acaso, si viene con otro nombre
    try {
      formattedDate = new Date(product.date_added).toLocaleDateString();
    } catch (e) {
      console.error(
        "Error formateando fecha (date_added):",
        product.date_added,
        e
      );
      formattedDate = "Fecha inválida";
    }
  }

  // Determinar el nombre de la etiqueta
  const etiqueta = product.categoria_nombre || product.titulo_etiqueta || "";
  // Determinar clase de color para la etiqueta
  let etiquetaClass = "etiqueta";
  if (etiqueta) {
    const lower = etiqueta.toLowerCase();
    if (lower.includes("nuevo")) etiquetaClass += " nuevo";
    else if (lower.includes("popu")) etiquetaClass += " popular";
    else if (lower.includes("favorito")) etiquetaClass += " favorito";
    else etiquetaClass += " limitada";
  }

  const cardHTML = `
        <div class="product-image" style="position:relative;">
            <img src="${
              product.image_url || "../../assets/images/helado66.jpeg"
            }" alt="${product.nombre}">
            ${
              etiqueta
                ? `<span class="${etiquetaClass}">${etiqueta}</span>`
                : ""
            }
        </div>
        <div class="product-content">
            <h3>${product.nombre}</h3>
            <p class="product-description">${
              product.descripcion || "Sin descripción"
            }</p>
            <div class="product-details">
                <div class="price">$${product.precio}</div>
                <div class="date-added">
                    <i class="far fa-calendar-alt"></i>
                    <span>${formattedDate}</span>
                </div>
            </div>
           
            <div class="product-actions">
                <button class="btn-edit" data-id="${product.id}">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="btn-delete-c" data-id="${product.id}" data-nombre="${product.nombre}">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button class="btn-viewInfo" data-id="${product.id}">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    `;

  card.innerHTML = cardHTML;
  //console.log('HTML de la tarjeta generado:', cardHTML);
  return card;
}

// Función para mostrar errores en el grid (MANTENER esta función)
function showError(message) {
  const gridView = document.getElementById("gridView");
  if (gridView) {
    gridView.innerHTML = `<div class="no-products-message error-message">${message}</div>`;
  } else {
    console.error("No se encontró gridView para mostrar el error:", message);
  }
}

// Función para cargar las estadísticas de productos (MANTENER esta función)
function loadProductStats() {
  console.log("Cargando estadísticas de productos...");
  fetch("/../../controllers/StatsController.php?action=getStatsForProductos", {
    method: "POST", // StatsController usa POST para las acciones
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Estadísticas recibidas:", data);
      if (data.success && data.data) {
        // Actualizar los elementos HTML con los datos recibidos
        const mostSoldNameElement = document.getElementById(
          "most-sold-product-name"
        );
        const mostSoldSalesElement = document.getElementById(
          "most-sold-product-sales"
        );
        const leastSoldNameElement = document.getElementById(
          "least-sold-product-name"
        );
        const leastSoldSalesElement = document.getElementById(
          "least-sold-product-sales"
        );
        const totalCountElement = document.getElementById(
          "total-products-count"
        );
        // const totalDescriptionElement = document.getElementById('total-products-description'); // Mantendremos la descripción estática por ahora

        const stats = data.data;

        // Usar las nuevas claves para las ventas
        if (stats.helado_mas_vendido && mostSoldNameElement)
          mostSoldNameElement.textContent = stats.helado_mas_vendido;
        if (stats.ventas_mas_vendido !== undefined && mostSoldSalesElement)
          mostSoldSalesElement.innerHTML =
            '<i class="fas fa-arrow-up"></i> ' +
            stats.ventas_mas_vendido +
            " ventas totales";

        if (stats.helado_menos_vendido !== undefined && leastSoldNameElement)
          leastSoldNameElement.textContent = stats.helado_menos_vendido;
        if (stats.ventas_menos_vendido !== undefined && leastSoldSalesElement)
          leastSoldSalesElement.innerHTML =
            '<i class="fas fa-arrow-down"></i> Solo ' +
            stats.ventas_menos_vendido +
            " ventas totales";

        if (stats.total_productos !== undefined && totalCountElement)
          totalCountElement.textContent = stats.total_productos;
        // La descripción total_products-description la dejaremos estática ya que el backend no la devuelve con esa clave
      } else {
        console.error(
          "Error o datos inválidos al cargar estadísticas de productos:",
          data.message
        );
      }
    })
    .catch((error) => {
      console.error(
        "Error en la petición de estadísticas de productos:",
        error
      );
    });
}

// Función para manejar acciones de productos (MANTENER esta función)
function handleProductActions(e) {
  //Capturar click en el boton de view info
  if (e.target.classList.contains("btn-viewInfo")) {
    const boton = e.target;
    const idProducto = boton.getAttribute("data-id");
    console.log("Botón ver info clickeado para producto:", idProducto);
    // Si quieres cargar datos para ver, descomenta y adapta la lógica del botón editar

    ProductInfoModal.open(idProducto);
  }

  //Capturar click sobre el boton eliminar producto
  if (e.target.classList.contains("btn-delete-c")) {
    const boton = e.target;
    const idProducto = boton.getAttribute("data-id");
    const nombreProducto = boton.getAttribute("data-nombre");


    showConfirmation({
      title: `Eliminar "${nombreProducto}"`,
      message: `¿Estás seguro de eliminar "${nombreProducto}" permanentemente? Todos los datos asociados se perderán.`,
      type: "delete",
      confirmText: "Eliminar",
      callback: async function () {
        try {
          await deleteProduct(idProducto);
        } catch (error) {
          console.error("Error en la confirmación:", error);
          showError(`Error al eliminar el producto: ${error.message}`);
        }
      },
    });
  }

  //Capturar click en el boton de editar producto
  if (e.target.classList.contains("btn-edit")) {
    const boton = e.target;
    const productId = boton.getAttribute("data-id");

    // Fetch product data from server
    fetch(`/../../controllers/obtenerProductos.php?id=${productId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.data) {
          // Mapear los datos del backend al formato esperado por la modal
          const productDataForModal = {
            id: data.data.id,
            name: data.data.nombre, // Mapear nombre
            description: data.data.descripcion, // Mapear descripcion
            price: data.data.precio, // Mapear precio
            image: data.data.image_url, // Mapear image_url
            // Nota: La carga de ingredientes y etiqueta requiere lógica adicional
            // en modalEdicion.js o un formato de datos diferente del backend
          };

          // Asegurarse de que FreshCandyModalEdicion existe antes de usarlo
          if (window.FreshCandyModalEdicion) {
            FreshCandyModalEdicion.setTypeModal("editar");
            FreshCandyModalEdicion.loadProductData(productDataForModal);
            FreshCandyModalEdicion.open();
          } else {
            console.error("FreshCandyModalEdicion no está disponible.");
            // Opcional: Notificar al usuario que el modal no se pudo abrir
          }
        } else {
          console.error(
            "Error al cargar datos del producto para editar:",
            data.message
          );
          // Opcional: Mostrar un mensaje de error al usuario
        }
      })
      .catch((error) => {
        console.error(
          "Error en la petición de datos del producto para editar:",
          error
        );
        // Opcional: Mostrar un mensaje de error al usuario
      });
  }
}

// Función para eliminar producto
function deleteProduct(id) {
  console.log("Iniciando proceso de eliminación de producto con ID:", id);

  // Verificar que el ID sea válido
  if (!id || !Number.isInteger(Number(id))) {
    console.error("ID de producto no válido:", id);
    showError("ID de producto no válido");
    return;
  }

  // Mostrar loading
  const gridView = document.getElementById("gridView");
  if (gridView) {
    gridView.innerHTML = `
      <div class="no-products-message">
        <i class="fas fa-spinner fa-spin"></i>
        Eliminando producto...
      </div>`;
  }

  // Hacer la petición de eliminación
  fetch("/../../controllers/obtenerProductos.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      action: "eliminarProducto",
      id: id,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(
          `Error HTTP ${response.status}: ${response.statusText}`
        );
      }
      return response.json();
    })
    .then((result) => {
      console.log("Resultado del servidor:", result);
      if (result.success) {
        console.log("Producto eliminado exitosamente");
        // Mostrar mensaje de éxito
        if (gridView) {
          gridView.innerHTML = `
          <div class="no-products-message">
            <i class="fas fa-check-circle"></i>
            Producto eliminado exitosamente
          </div>`;
          // Refrescar la lista de productos después de un breve delay
          setTimeout(() => {
            searchProducts();
          }, 1500);
          searchProducts();
        }
      }
    });
}

// Funciones para búsqueda y filtros

// Inicializar el buscador
function initializeSearch() {
  const searchInput = document.getElementById("products-search");
  if (searchInput) {
    searchInput.addEventListener("input", function (e) {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        currentFilters.termino = e.target.value;
        searchProducts();
      }, 300);
    });
    console.log("Listener de búsqueda inicializado."); // Debug
  } else {
    console.error("No se encontró el input de búsqueda con id products-search");
  }
}

// Cargar categorías en el select
async function loadCategories() {
  try {
    console.log("Cargando categorías..."); // Debug
    const response = await fetch(
      "../../controllers/obtenerProductos.php?action=obtenerCategorias",
      {
        method: "POST",
      }
    );
    const result = await response.json();
    console.log("Respuesta de categorías:", result); // Debug

    if (result.success) {
      const categoriaSelect = document.getElementById("filter-categoria");
      if (categoriaSelect) {
        categoriaSelect.innerHTML =
          '<option value="">Todas las categorías</option>';
        result.data.forEach((categoria) => {
          categoriaSelect.innerHTML += `<option value="${categoria.id}">${categoria.nombre}</option>`;
        });
        console.log("Categorías cargadas en el select."); // Debug
      }
    } else {
      console.error("Error al cargar categorías:", result.message); // Debug
    }
  } catch (error) {
    console.error("Error en la petición de categorías:", error); // Debug
  }
}

// Configurar listeners para los filtros
function setupFilterListeners() {
  // Filtro de categoría
  const categoriaSelect = document.getElementById("filter-categoria");
  if (categoriaSelect) {
    categoriaSelect.addEventListener("change", function () {
      currentFilters.categoria = this.value;
      console.log("Filtro categoría cambiado:", this.value); // Debug
      searchProducts();
    });
  }

  // Filtros de precio
  const precioMin = document.getElementById("filter-precio-min");
  const precioMax = document.getElementById("filter-precio-max");
  if (precioMin) {
    precioMin.addEventListener("change", function () {
      currentFilters.precio_min = this.value;
      console.log("Filtro precio mínimo cambiado:", this.value); // Debug
      searchProducts();
    });
  }
  if (precioMax) {
    precioMax.addEventListener("change", function () {
      currentFilters.precio_max = this.value;
      console.log("Filtro precio máximo cambiado:", this.value); // Debug
      searchProducts();
    });
  }

  // Manejar el menú de filtros
  const filterBtn = document.querySelector(".filter-btn");
  const filterMenu = document.getElementById("filterMenu");
  const clearFiltersBtn = document.querySelector(".clear-filters-btn");
  const filterOptions = document.querySelectorAll(".filter-option");

  if (filterBtn && filterMenu) {
    filterBtn.addEventListener("click", () => {
      filterMenu.classList.toggle("hidden");
    });

    // Cerrar el menú al hacer clic fuera
    document.addEventListener("click", (e) => {
      if (!filterBtn.contains(e.target) && !filterMenu.contains(e.target)) {
        filterMenu.classList.add("hidden");
      }
    });

    // Manejar opciones de ordenamiento
    filterOptions.forEach((option) => {
      option.addEventListener("click", () => {
        const filterValue = option.getAttribute("data-filter");
        currentFilters.ordenamiento = filterValue;
        console.log("Ordenamiento cambiado:", filterValue);
        searchProducts();
        filterMenu.classList.add("hidden");
      });
    });

    // Limpiar filtros
    if (clearFiltersBtn) {
      clearFiltersBtn.addEventListener("click", () => {
        currentFilters = {
          termino: "",
          categoria: "",
          precio_min: "",
          precio_max: "",
          ordenamiento: "",
        };
        // Limpiar inputs
        const searchInput = document.getElementById("products-search");
        if (searchInput) searchInput.value = "";
        if (categoriaSelect) categoriaSelect.value = "";
        if (precioMin) precioMin.value = "";
        if (precioMax) precioMax.value = "";
        // Actualizar UI
        clearFiltersBtn.disabled = true;
        searchProducts();
      });
    }
  }

  console.log("Listeners de filtros configurados."); // Debug
}

// Realizar la búsqueda
async function searchProducts() {
  try {
   
    // Asegurarse de enviar solo los filtros que tienen valor (no vacíos)
    const activeFilters = {};
    for (const key in currentFilters) {
      if (currentFilters[key] !== "") {
        activeFilters[key] = currentFilters[key];
      }
    }
    console.log("Enviando filtros activos:", activeFilters); // Debug

    const response = await fetch(
      "../../controllers/obtenerProductos.php?action=buscarProductos",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams(activeFilters),
      }
    );

    const result = await response.json();
    console.log("Respuesta del servidor para búsqueda:", result); // Debug

    if (result.success) {
      renderProducts(result.data);
      // Habilitar/deshabilitar botón de limpiar filtros
      const clearFiltersBtn = document.querySelector(".clear-filters-btn");
      if (clearFiltersBtn) {
        clearFiltersBtn.disabled = Object.keys(activeFilters).length === 0;
      }
    } else {
      showError("Error al buscar productos: " + result.message);
    }
  } catch (error) {
    console.error("Error en la búsqueda:", error); // Debug
    showError("Error al realizar la búsqueda");
  }
}

// Cargar etiquetas dinámicamente en el modal de agregar producto
async function loadEtiquetasForModal() {
  const select = document.getElementById("new-product-tag");
  if (!select) return;
  select.innerHTML = '<option value="">Selecciona una etiqueta</option>';
  try {
    const response = await fetch(
      "../../controllers/obtenerProductos.php?action=obtenerCategorias",
      { method: "POST" }
    );
    const result = await response.json();
    if (result.success && Array.isArray(result.data)) {
      result.data.forEach((etiqueta) => {
        select.innerHTML += `<option value="${etiqueta.id}">${etiqueta.nombre}</option>`;
      });
    } else {
      select.innerHTML += '<option value="">No hay etiquetas</option>';
    }
  } catch (e) {
    select.innerHTML += '<option value="">Error al cargar etiquetas</option>';
  }
}

// Función para cargar los ingredientes desde la base de datos usando el controlador correcto y los nombres correctos
async function loadIngredients() {
  try {
    const response = await fetch(
      "../../controllers/IngredientsController.php?action=getAllIngredients",
      {
        method: "POST",
      }
    );
    const data = await response.json();
    if (data.success && data.data && data.data.ingredients) {
      return data.data.ingredients;
    } else {
      throw new Error(data.message || "Error al cargar los ingredientes");
    }
  } catch (error) {
    console.error("Error:", error);
    throw error;
  }
}

// Función para cargar las unidades desde la base de datos
async function loadUnits() {
  try {
    const response = await fetch(
      "../../controllers/IngredientsController.php?action=getUnits",
      {
        method: "POST",
      }
    );
    const data = await response.json();
    if (data.success) {
      return data.data.units;
    } else {
      throw new Error(data.message || "Error al cargar las unidades");
    }
  } catch (error) {
    console.error("Error:", error);
    throw error;
  }
}

// Función para mostrar el modal de ingredientes
async function showIngredientModal() {
  const modal = document.getElementById("ingredientModal");
  const checkboxContainer = modal.querySelector(
    ".ingredients-checkbox-container"
  );
  const selectedIds = Array.from(
    document.querySelectorAll(".selected-ingredient-item")
  ).map((div) => div.dataset.id);
  try {
    const ingredients = await loadIngredients();
    // Limpiar el contenedor
    checkboxContainer.innerHTML = "";
    // Crear los checkboxes para cada ingrediente
    ingredients.forEach((ingredient) => {
      const div = document.createElement("div");
      div.className = "ingredient-item";
      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.id = `ingredient-${ingredient.id_ingrediente}`;
      checkbox.value = ingredient.id_ingrediente;
      checkbox.dataset.name = ingredient.nombre_ing;
      checkbox.dataset.unit = ingredient.unidad || "unidad";
      if (selectedIds.includes(String(ingredient.id_ingrediente))) {
        checkbox.checked = true;
      }
      const label = document.createElement("label");
      label.htmlFor = `ingredient-${ingredient.id_ingrediente}`;
      label.textContent = ingredient.nombre_ing;
      div.appendChild(checkbox);
      div.appendChild(label);
      checkboxContainer.appendChild(div);
    });
    // Mostrar el modal
    modal.classList.add("active");
  } catch (error) {
    console.error("Error al cargar ingredientes:", error);
    alert("Error al cargar los ingredientes. Por favor, intente nuevamente.");
  }
}

// Función para manejar la selección de ingredientes
function handleIngredientSelection() {
  const modal = document.getElementById("ingredientModal");
  const confirmBtn = modal.querySelector("#confirm-ingredients");
  const ingredientsContainer = document.getElementById("ingredients-container");
  confirmBtn.addEventListener("click", () => {
    const selectedIngredients = Array.from(
      modal.querySelectorAll('input[type="checkbox"]:checked')
    );
    // Mantener los ya seleccionados
    const currentIds = Array.from(
      ingredientsContainer.querySelectorAll(".selected-ingredient-item")
    ).map((div) => div.dataset.id);
    // Agregar solo los nuevos seleccionados
    selectedIngredients.forEach((ingredient) => {
      if (!currentIds.includes(ingredient.value)) {
        const div = document.createElement("div");
        div.className = "selected-ingredient-item";
        div.dataset.id = ingredient.value;
        const nameSpan = document.createElement("span");
        nameSpan.className = "ingredient-name";
        nameSpan.textContent = ingredient.dataset.name;
        const quantityDiv = document.createElement("div");
        quantityDiv.className = "ingredient-quantity";
        const quantityInput = document.createElement("input");
        quantityInput.type = "number";
        quantityInput.className = "quantity-input";
        quantityInput.min = "0";
        quantityInput.step = "0.1";
        quantityInput.required = true;
        quantityInput.placeholder = "Cantidad";
        quantityDiv.appendChild(quantityInput);
        const unitSpan = document.createElement("span");
        unitSpan.className = "ingredient-unit";
        unitSpan.textContent = ingredient.dataset.unit;
        quantityDiv.appendChild(unitSpan);
        const removeBtn = document.createElement("button");
        removeBtn.className = "remove-ingredient";
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.addEventListener("click", () => {
          div.remove();
          // Desmarcar el checkbox correspondiente
          const checkbox = document.getElementById(
            `ingredient-${ingredient.value}`
          );
          if (checkbox) checkbox.checked = false;
        });
        div.appendChild(nameSpan);
        div.appendChild(quantityDiv);
        div.appendChild(removeBtn);
        ingredientsContainer.appendChild(div);
      }
    });
    // Eliminar los que se desmarcaron
    Array.from(
      ingredientsContainer.querySelectorAll(".selected-ingredient-item")
    ).forEach((div) => {
      if (!selectedIngredients.find((ing) => ing.value === div.dataset.id)) {
        div.remove();
      }
    });
    // Cerrar el modal
    modal.classList.remove("active");
  });
}

// Función para manejar el modal de agregar producto
function handleAddProductModal() {
  const modal = document.getElementById("addProductModal");
  const openBtn = document.getElementById("openAddModal");
  const closeBtn = document.getElementById("close-add-modal");
  const cancelBtn = document.getElementById("cancel-add");
  const submitBtn = document.getElementById("submit-add-product");
  const addIngredientBtn = document.getElementById("btn-add-ingredient");
  const form = document.getElementById("add-product-form");
  const ingredientsContainer = document.getElementById("ingredients-container");

  // Abrir modal
  openBtn.addEventListener("click", () => {
    modal.classList.add("active");
    // Limpiar ingredientes seleccionados y checkboxes
    ingredientsContainer.innerHTML = "";
    // Cargar las etiquetas al abrir el modal
    loadEtiquetasForModal();
  });

  // Cerrar modal
  const closeModal = () => {
    modal.classList.remove("active");
    form.reset();
    document.getElementById("ingredients-container").innerHTML = "";
    document.getElementById("new-image-preview").innerHTML = `
            <div class="image-placeholder">
                <i class="fas fa-image"></i>
                <p>Vista Previa</p>
            </div>
        `;
  };

  closeBtn.addEventListener("click", closeModal);
  cancelBtn.addEventListener("click", closeModal);

  // Manejar la selección de ingredientes
  addIngredientBtn.addEventListener("click", showIngredientModal);

  // Manejar la vista previa de la imagen
  const imageInput = document.getElementById("new-product-image");
  const imagePreview = document.getElementById("new-image-preview");

  imageInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    imagePreview.innerHTML = "";
    if (file) {
      const reader = new FileReader();
      reader.onload = (ev) => {
        imagePreview.innerHTML = `<img src="${ev.target.result}" alt="Vista previa" class="image-preview-img" style="display:block;margin:auto;">`;
      };
      reader.readAsDataURL(file);
    } else {
      imagePreview.innerHTML = `<div class=\"image-placeholder\"><i class=\"fas fa-image\"></i><p>Vista Previa</p></div>`;
    }
  });

  // Manejar el envío del formulario
  submitBtn.addEventListener("click", async () => {
    // Validación personalizada de todos los campos obligatorios
    const nombreInput = document.getElementById("new-product-name");
    const descripcionInput = document.getElementById("new-product-description");
    const etiquetaSelect = document.getElementById("new-product-tag");
    const precioInput = document.getElementById("new-product-price");
    const ingredientesSeleccionados = Array.from(
      document.querySelectorAll(".selected-ingredient-item")
    );
    const imageInput = document.getElementById("new-product-image");

    if (!nombreInput.value.trim()) {
      showNotification("Por favor, ingresa el nombre del producto.", "error");
      nombreInput.focus();
      return;
    }
    if (!descripcionInput.value.trim()) {
      showNotification(
        "Por favor, ingresa la descripción del producto.",
        "error"
      );
      descripcionInput.focus();
      return;
    }
    if (!etiquetaSelect.value) {
      showNotification(
        "Por favor, selecciona una etiqueta para el producto.",
        "error"
      );
      etiquetaSelect.focus();
      return;
    }
    if (
      !precioInput.value ||
      isNaN(precioInput.value) ||
      Number(precioInput.value) <= 0
    ) {
      showNotification(
        "Por favor, ingresa un precio válido para el producto.",
        "error"
      );
      precioInput.focus();
      return;
    }
    if (ingredientesSeleccionados.length === 0) {
      showNotification(
        "Por favor, selecciona al menos un ingrediente.",
        "error"
      );
      addIngredientBtn.focus();
      return;
    }
    // Validar cantidad de cada ingrediente
    for (const item of ingredientesSeleccionados) {
      const cantidadInput = item.querySelector(".quantity-input");
      let valor = cantidadInput.value.replace(",", ".");
      if (!valor || isNaN(valor) || Number(valor) <= 0) {
        showNotification(
          "Por favor, ingresa una cantidad válida para cada ingrediente.",
          "error"
        );
        cantidadInput.focus();
        return;
      }
    }
    // Si quieres que la imagen sea obligatoria, descomenta esto:
     if (!imageInput.value) {
         showNotification('Por favor, selecciona una imagen para el producto.', 'error');
        imageInput.focus();
        return;
     }

    const formData = new FormData(form);
    // Agregar los ingredientes seleccionados
    const selectedIngredients = ingredientesSeleccionados.map((item) => ({
      id: item.dataset.id,
      cantidad: item.querySelector(".quantity-input").value,
    }));
    formData.append("ingredientes", JSON.stringify(selectedIngredients));
    try {

      console.log(formData);
      const response = await fetch(
        "../../controllers/obtenerProductos.php?action=agregarProducto",
        {
          method: "POST",
          body: formData,
        }
      );
      const text = await response.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error("Respuesta no es JSON válido:", text);
        showNotification(
          "Error inesperado al agregar el producto. Respuesta del servidor: " +
            text,
          "error"
        );
        return;
      }
      if (data.success) {
        showNotification("Producto agregado exitosamente", "success");
        closeModal();
        searchProducts();
      } else {
        showNotification(
          "Error al agregar el producto: " +
            (data.message || "Error desconocido."),
          "error"
        );
      }
    } catch (error) {
      console.error("Error:", error);
      showNotification(
        "Error al agregar el producto. Por favor, intente nuevamente.",
        "error"
      );
    }
  });
}

// --- Lógica para editar producto ---
function handleEditProductModal() {
  const editModal = document.getElementById("productModal");
  const closeEditModalBtn = document.getElementById("close-modal");
  const cancelEditBtn = document.getElementById("cancel-edit");
  const submitEditBtn = document.getElementById("submit-product");
  const editProductForm = document.getElementById("edit-product-form");
  const imageInput = document.getElementById("product-image");
  const imagePreview = document.getElementById("image-preview");
  const previewImg = document.getElementById("preview-img");
  const imagePlaceholder = document.getElementById("image-placeholder");
  const removeImageBtn = document.getElementById("remove-image");
  const etiquetaSelect = document.getElementById("orderStatus");
  const ingredientsContainer = editModal.querySelector(
    "#ingredients-container"
  );
  const addIngredientBtn = document.getElementById("btn-add-ingredient-edit");

  let currentProductId = null;
  let currentImageUrl = null;

  // Cargar ingredientes asociados al producto
  async function loadProductIngredients(productId) {
    try {
      const response = await fetch(
        `../../controllers/obtenerProductos.php?action=obtenerIngredientesProducto&id=${productId}`
      );
      const data = await response.json();
      if (data.success && Array.isArray(data.data)) {
        // Renderizar ingredientes seleccionados
        ingredientsContainer.innerHTML = "";
        data.data.forEach((ingredient) => {
          const div = document.createElement("div");
          div.className = "selected-ingredient-item";
          div.dataset.id = ingredient.id_ingrediente;

          const nameSpan = document.createElement("span");
          nameSpan.className = "ingredient-name";
          nameSpan.textContent = ingredient.nombre;

          const quantityDiv = document.createElement("div");
          quantityDiv.className = "ingredient-quantity";

          const quantityInput = document.createElement("input");
          quantityInput.type = "number";
          quantityInput.className = "quantity-input";
          quantityInput.min = "0";
          quantityInput.step = "0.1";
          quantityInput.required = true;
          quantityInput.placeholder = "Cantidad";
          quantityInput.value = ingredient.cantidad;

          const unitSpan = document.createElement("span");
          unitSpan.className = "ingredient-unit";
          unitSpan.textContent = ingredient.unit || "unidad";

          quantityDiv.appendChild(quantityInput);
          quantityDiv.appendChild(unitSpan);

          const removeBtn = document.createElement("button");
          removeBtn.className = "remove-ingredient";
          removeBtn.innerHTML = '<i class="fas fa-times"></i>';
          removeBtn.addEventListener("click", () => {
            div.remove();
          });

          div.appendChild(nameSpan);
          div.appendChild(quantityDiv);
          div.appendChild(removeBtn);
          ingredientsContainer.appendChild(div);
        });
      }
    } catch (error) {
      console.error("Error al cargar ingredientes del producto:", error);
    }
  }

  // Cargar etiquetas dinámicamente en el modal de editar producto
  async function loadEtiquetasForEditModal() {
    const select = document.getElementById("orderStatus");
    if (!select) return;
    select.innerHTML = "";
    try {
      const response = await fetch(
        "../../controllers/obtenerProductos.php?action=obtenerCategorias",
        { method: "POST" }
      );
      const result = await response.json();
      if (result.success && Array.isArray(result.data)) {
        result.data.forEach((etiqueta) => {
          select.innerHTML += `<option value="${etiqueta.id}">${etiqueta.nombre}</option>`;
        });
      } else {
        select.innerHTML += '<option value="">No hay etiquetas</option>';
      }
    } catch (e) {
      select.innerHTML += '<option value="">Error al cargar etiquetas</option>';
    }
  }

  // Abrir modal y cargar datos
  document.addEventListener("click", async function (e) {
    if (e.target.classList.contains("btn-edit")) {
      const productId = e.target.getAttribute("data-id");
      currentProductId = productId;
      // Cargar etiquetas antes de cargar datos del producto
      await loadEtiquetasForEditModal();
      // Cargar datos del producto
      const response = await fetch(
        `../../controllers/obtenerProductos.php?id=${productId}`
      );
      const data = await response.json();
      if (data.success && data.data) {
        const prod = data.data;
        document.getElementById("product-name").value = prod.nombre || "";
        document.getElementById("product-description").value =
          prod.descripcion || "";
        document.getElementById("product-price").value = prod.precio || "";
        // Etiqueta: buscar opción que coincida
        if (etiquetaSelect) {
          for (let i = 0; i < etiquetaSelect.options.length; i++) {
            if (
              etiquetaSelect.options[i].textContent.trim().toLowerCase() ===
              (prod.titulo_etiqueta || prod.categoria_nombre || "")
                .trim()
                .toLowerCase()
            ) {
              etiquetaSelect.selectedIndex = i;
              break;
            }
          }
        }
        // Imagen
        currentImageUrl = prod.image_url || "";
        if (currentImageUrl) {
          previewImg.src = currentImageUrl;
          previewImg.style.display = "block";
          imagePlaceholder.style.display = "none";
          // Si la imagen falla al cargar, mostrar el placeholder
          previewImg.onerror = function () {
            previewImg.style.display = "none";
            imagePlaceholder.style.display = "block";
          };
        } else {
          previewImg.style.display = "none";
          imagePlaceholder.style.display = "block";
        }
        // Cargar ingredientes asociados
        await loadProductIngredients(productId);
        editModal.classList.add("active");
      }
    }
  });

  // Agregar ingredientes (abre el modal de selección y añade los nuevos)
  addIngredientBtn.addEventListener("click", async () => {
    // Cargar todos los ingredientes disponibles
    const modal = document.getElementById("ingredientModal");
    const checkboxContainer = modal.querySelector(
      ".ingredients-checkbox-container"
    );
    try {
      const ingredients = await loadIngredients();
      // Obtener los IDs de los ingredientes ya seleccionados en el producto
      const selectedIds = Array.from(
        ingredientsContainer.querySelectorAll(".selected-ingredient-item")
      ).map((div) => div.dataset.id);
      checkboxContainer.innerHTML = "";
      ingredients.forEach((ingredient) => {
        const div = document.createElement("div");
        div.className = "ingredient-item";
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.id = `ingredient-${ingredient.id_ingrediente}`;
        checkbox.value = ingredient.id_ingrediente;
        checkbox.dataset.name = ingredient.nombre_ing;
        checkbox.dataset.unit = ingredient.unidad || "unidad";
        // MARCAR SI YA ESTÁ SELECCIONADO
        if (selectedIds.includes(String(ingredient.id_ingrediente))) {
          checkbox.checked = true;
        }
        const label = document.createElement("label");
        label.htmlFor = `ingredient-${ingredient.id_ingrediente}`;
        label.textContent = ingredient.nombre_ing;
        div.appendChild(checkbox);
        div.appendChild(label);
        checkboxContainer.appendChild(div);
      });
      modal.classList.add("active");
      // Confirmar selección
      const confirmBtn = modal.querySelector("#confirm-ingredients");
      confirmBtn.onclick = () => {
        // Añadir los nuevos seleccionados que no estén ya, y mantener cantidades previas
        const selected = Array.from(
          checkboxContainer.querySelectorAll('input[type="checkbox"]:checked')
        );
        selected.forEach((ingredient) => {
          let existingDiv = ingredientsContainer.querySelector(
            `[data-id="${ingredient.value}"]`
          );
          if (!existingDiv) {
            // Nuevo ingrediente, crear con cantidad vacía
            const div = document.createElement("div");
            div.className = "selected-ingredient-item";
            div.dataset.id = ingredient.value;
            const nameSpan = document.createElement("span");
            nameSpan.className = "ingredient-name";
            nameSpan.textContent = ingredient.dataset.name;
            const quantityDiv = document.createElement("div");
            quantityDiv.className = "ingredient-quantity";
            const quantityInput = document.createElement("input");
            quantityInput.type = "number";
            quantityInput.className = "quantity-input";
            quantityInput.min = "0";
            quantityInput.step = "0.1";
            quantityInput.required = true;
            quantityInput.placeholder = "Cantidad";
            const unitSpan = document.createElement("span");
            unitSpan.className = "ingredient-unit";
            unitSpan.textContent = ingredient.dataset.unit;
            quantityDiv.appendChild(quantityInput);
            quantityDiv.appendChild(unitSpan);
            const removeBtn = document.createElement("button");
            removeBtn.className = "remove-ingredient";
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.addEventListener("click", () => {
              div.remove();
            });
            div.appendChild(nameSpan);
            div.appendChild(quantityDiv);
            div.appendChild(removeBtn);
            ingredientsContainer.appendChild(div);
          } else {
            // Ya existe, mantener la cantidad previa
            // No hacer nada, solo asegurarse que no se elimina
          }
        });
        // Eliminar los que se desmarcaron
        Array.from(
          ingredientsContainer.querySelectorAll(".selected-ingredient-item")
        ).forEach((div) => {
          if (!selected.find((ing) => ing.value === div.dataset.id)) {
            div.remove();
          }
        });
        modal.classList.remove("active");
      };
    } catch (error) {
      alert("Error al cargar ingredientes.");
    }
  });

  // Cerrar modal
  const closeModal = () => {
    editModal.classList.remove("active");
    document.body.style.overflow = ""; // Restaurar scroll

    setTimeout(() => {
      document.getElementById("product-name").focus();
      editProductForm.reset();
      previewImg.src = "#";
      previewImg.style.display = "none";
      imagePlaceholder.style.display = "block";
      currentProductId = null;
      currentImageUrl = null;
      ingredientsContainer.innerHTML = "";
    }, 300);
  };

  closeEditModalBtn.addEventListener("click", closeModal);
  cancelEditBtn.addEventListener("click", closeModal);

  // Vista previa de imagen
  imageInput.addEventListener("change", function (e) {
    const file = e.target.files[0];
    previewImg.style.display = "none";
    imagePlaceholder.style.display = "block";
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.style.display = "block";
        imagePlaceholder.style.display = "none";
      };
      reader.readAsDataURL(file);
    }
  });
  // Eliminar imagen (solo visual)
  removeImageBtn.addEventListener("click", function () {
    showNotification(
      "La imagen es obligatoria para el producto. No puedes eliminarla.",
      "error"
    );
    // No borres la imagen del preview
  });

  // Guardar cambios
  submitEditBtn.addEventListener("click", async function () {
    if (!currentProductId) return;
    // Validaciones de campos obligatorios
    const nombreInput = document.getElementById("product-name");
    const descripcionInput = document.getElementById("product-description");
    const precioInput = document.getElementById("product-price");
    const etiquetaSelect = document.getElementById("orderStatus");
    const ingredientesSeleccionados = Array.from(
      ingredientsContainer.querySelectorAll(".selected-ingredient-item")
    );
    const imageInput = document.getElementById("product-image");
    const previewImg = document.getElementById("preview-img");

    if (!nombreInput.value.trim()) {
      showNotification("Por favor, ingresa el nombre del producto.", "error");
      nombreInput.focus();
      return;
    }
    if (!descripcionInput.value.trim()) {
      showNotification(
        "Por favor, ingresa la descripción del producto.",
        "error"
      );
      descripcionInput.focus();
      return;
    }
    if (
      !precioInput.value ||
      isNaN(precioInput.value) ||
      Number(precioInput.value) <= 0
    ) {
      showNotification("Por favor, ingresa un precio válido.", "error");
      precioInput.focus();
      return;
    }
    if (!etiquetaSelect.value) {
      showNotification("Por favor, selecciona una etiqueta.", "error");
      etiquetaSelect.focus();
      return;
    }
    if (ingredientesSeleccionados.length === 0) {
      showNotification(
        "Por favor, selecciona al menos un ingrediente.",
        "error"
      );
      return;
    }
    // Validar imagen (debe haber imagen previa o nueva visible)
    const src = previewImg.src;
    const isPreviewVisible =
      previewImg.style.display !== "none" &&
      !!src &&
      src !== "#" &&
      src !== "about:blank" &&
      !src.endsWith("/app/assets/images/#");
    if (!isPreviewVisible) {
      showNotification(
        "Por favor, selecciona una imagen para el producto.",
        "error"
      );
      imageInput.focus();
      return;
    }
    // Si todas las validaciones pasan, ahora sí continuar y cerrar modal después de éxito
    const formData = new FormData();
    formData.append("id", currentProductId);
    formData.append("nombre", document.getElementById("product-name").value);
    formData.append(
      "descripcion",
      document.getElementById("product-description").value
    );
    formData.append("precio", document.getElementById("product-price").value);
    // Etiqueta: valor del select
    if (etiquetaSelect) {
      formData.append("etiqueta", etiquetaSelect.value);
    }
    // Imagen: solo si se seleccionó una nueva
    if (imageInput.files && imageInput.files[0]) {
      formData.append("imagen", imageInput.files[0]);
    }
    // Ingredientes seleccionados
    const selectedIngredients = Array.from(
      ingredientsContainer.querySelectorAll(".selected-ingredient-item")
    ).map((item) => ({
      id: item.dataset.id,
      cantidad: item.querySelector(".quantity-input").value,
    }));
    formData.append("ingredientes", JSON.stringify(selectedIngredients));
    try {
      const response = await fetch(
        "../../controllers/obtenerProductos.php?action=editarProducto",
        {
          method: "POST",
          body: formData,
        }
      );
      const text = await response.text();
      let result;
      try {
        result = JSON.parse(text);
      } catch (jsonError) {
        alert(
          "Error inesperado al actualizar el producto. Respuesta del servidor: " +
            text
        );
        return;
      }
      if (result.success) {
        closeModal();
        searchProducts();
        showNotification("Producto actualizado exitosamente", "success");
      } else {
        showNotification(
          "Error al actualizar el producto: " + result.message,
          "error"
        );
      }
    } catch (error) {
      showNotification(
        "Error al actualizar el producto (catch): " + error,
        "error"
      );
    }
  });
}

// Inicialización cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM cargado en gestionProductos.js");
  loadProductStats(); // Cargar estadísticas

  // Inicializar y cargar productos con filtros
  initializeSearch();
  loadCategories();
  setupFilterListeners();
  searchProducts(); // Carga inicial de productos
  handleAddProductModal(); // Inicializar el modal de agregar producto
  handleEditProductModal();
  handleIngredientSelection();

  document.getElementById("dashboard-link2").classList.add("active");

  // Configurar eventos de acción
  const gridView = document.getElementById("gridView");
  if (gridView) {
    gridView.addEventListener("click", handleProductActions);
  } else {
    console.error(
      "Elemento #gridView no encontrado en DOMContentLoaded (gestionProductos.js)."
    );
  }
});

// Apply animations to cards on load (MANTENER esta función)
// Esta función ahora se llama desde renderProducts después de añadir los elementos
const animateElements = () => {
  console.log("Ejecutando animateElements...");
  const statCards = document.querySelectorAll(".stat-card");
  const barraAcciones = document.querySelector(".control-bar");
  // Seleccionar las tarjetas de producto recién añadidas
  const cardsProductos = document.querySelectorAll(
    ".products-grid-view .product-card"
  );

  // Add animation classes with delay (para stat cards y barra de acciones si aplica)
  statCards.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add("fade-in");
    }, 100 * index);
  });

  // La barra de acciones parece tener animación en el CSS, podrías activarla aquí si es necesario
  if (barraAcciones) {
    barraAcciones.classList.add("slide-in");
  }

  // Animar las tarjetas de productos recién añadidas (descomentar y ajustar retraso si deseas animación escalonada)
  cardsProductos.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add("slide-in"); // O fade-in, según tu CSS de animación
    }, 50 * index);
  });
};
