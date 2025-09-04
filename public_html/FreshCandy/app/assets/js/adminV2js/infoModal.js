// Configuración de iconos por tipo de ingrediente

(function () {
  "use strict";

  // Configuración de iconos por tipo de ingrediente
  const INGREDIENT_ICONS = {
    default: "fa-utensils",
    flour: "fa-wheat-awn",
    sugar: "fa-candy-cane",
    milk: "fa-bottle-droplet",
    egg: "fa-egg",
    water: "fa-droplet",
    salt: "fa-shaker",
    spice: "fa-pepper-hot",
    vegetable: "fa-carrot",
    fruit: "fa-apple-whole",
    meat: "fa-drumstick-bite",
    fish: "fa-fish",
    cheese: "fa-cheese",
    oil: "fa-flask",
  };

  // Elementos del DOM
  let modal, modalTitle, ingredientsList, closeButtons;

  // Inicializar la modal
  function initIngredientsModal() {
    modal = document.getElementById("productIngredientsModal");
    modalTitle = document.getElementById("pim-product-title");
    ingredientsList = document.querySelector(".pim-ingredients-list");
    closeButtons = document.querySelectorAll(
      ".pim-close-modal, .pim-btn-close"
    );

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

  // Abrir la modal
  function openIngredientsModal(productId) {
    if (!modal || !modalTitle || !ingredientsList) {
      console.error(
        "Modal no inicializada. Llama a initIngredientsModal() primero."
      );
      return;
    }

    modalTitle.textContent = "Ingredientes";
    ingredientsList.innerHTML =
      '<div class="pim-loading">Cargando ingredientes...</div>';
    modal.style.display = "block";
    document.body.style.overflow = "hidden";

    loadIngredients(productId);
  }

  // Cerrar la modal
  function closeModal() {
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }

  // Cargar ingredientes (AJAX real)
  function loadIngredients(productId) {
    console.log(`Cargando ingredientes para el producto ID: ${productId}`);

    fetch(
      `../../controllers/obtenerProductos.php?action=obtenerIngredientesProducto&id=${productId}`
    )
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          displayIngredients(data.data);
        } else {
          console.error("Error al cargar ingredientes:", data.message);
          ingredientsList.innerHTML = `
              <div class="pim-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Error al cargar los ingredientes: ${data.message}
              </div>
            `;
        }
      })
      .catch((error) => {
        console.error("Error en la petición:", error);
        ingredientsList.innerHTML = `
            <div class="pim-error">
              <i class="fa-solid fa-triangle-exclamation"></i>
              Error al cargar los ingredientes
            </div>
          `;
      });
  }

  // Mostrar ingredientes en la modal
  function displayIngredients(ingredients) {
    ingredientsList.innerHTML = "";

    if (!ingredients || ingredients.length === 0) {
      ingredientsList.innerHTML = `
          <p class="pim-no-ingredients">
            <i class="fa-solid fa-circle-info"></i>
            Este producto no tiene ingredientes registrados.
          </p>
        `;
      return;
    }

    ingredients.forEach((ingredient) => {
      const iconType = ingredient.iconType || "default";
      const iconClass = INGREDIENT_ICONS[iconType] || INGREDIENT_ICONS.default;

      const ingredientElement = document.createElement("div");
      ingredientElement.className = "pim-ingredient-item";
      ingredientElement.innerHTML = `
          <div class="pim-ingredient-icon">
            <i class="fa-solid ${iconClass}"></i>
          </div>
          <div class="pim-ingredient-info">
            <div class="pim-ingredient-name">${ingredient.nombre}</div>
            <div class="pim-ingredient-quantity">${ingredient.cantidad} ${ingredient.unit}</div>
          </div>
        `;

      ingredientsList.appendChild(ingredientElement);
    });
  }

  // Función de ejemplo para simular datos - eliminar en producción
  function getMockIngredients() {
    return [
      {
        name: "Harina de trigo",
        quantity: 250,
        unit: "gramos",
        iconType: "flour",
      },
      {
        name: "Azúcar glass",
        quantity: 100,
        unit: "gramos",
        iconType: "sugar",
      },
      { name: "Huevos", quantity: 2, unit: "unidades", iconType: "egg" },
      { name: "Leche entera", quantity: 200, unit: "ml", iconType: "milk" },
      { name: "Sal fina", quantity: 5, unit: "gramos", iconType: "salt" },
      {
        name: "Mantequilla sin sal",
        quantity: 50,
        unit: "gramos",
        iconType: "default",
      },
    ];
  }

  // Inicialización cuando el DOM esté listo
  document.addEventListener("DOMContentLoaded", initIngredientsModal);

  // Exponer al ámbito global solo lo necesario
  window.ProductInfoModal = {
    open: openIngredientsModal,
    close: closeModal,
    init: initIngredientsModal,
  };
})();
