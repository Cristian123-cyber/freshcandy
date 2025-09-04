import { reloadIngredientsTable } from "./inventario.js";

// Script para manejar la funcionalidad de la modal de reabastecimiento
document.addEventListener("DOMContentLoaded", function () {
  // Referencias a elementos DOM
  const restockModal = document.getElementById("restockIngredientModal");
  const closeBtn = document.getElementById("close-restock-modal");
  const cancelBtn = document.getElementById("cancel-restock");
  const restockForm = document.getElementById("restock-form");
  const submitBtn = document.getElementById("submit-restock");

  // Función para abrir la modal con los datos del ingrediente
  window.openRestockModal = function (ingredientId) {
    // Obtener los datos del ingrediente
    fetch(
      `/../../controllers/IngredientsController.php?action=getIngredientById&id=${ingredientId}`,
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
          console.log(data);
          const ingredient = data.data.ingredient;
          document.getElementById("restock-ingredient-id").value =
            ingredient.id_ingrediente;
          document.getElementById("restock-ingredient-name").textContent =
            ingredient.nombre_ing;
          document.getElementById("restock-current-stock").textContent = `Stock actual: ${ingredient.stock_ing} ${ingredient.unidad}`;
          document.getElementById("restock-unit").textContent =
            ingredient.unidad;
          restockModal.classList.add("active");
        } else {
          console.log(data);
          showNotification(
            "Error al cargar los datos del ingrediente",
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Error al cargar los datos del ingrediente", "error");
      });
  };

  // Función para cerrar la modal
  function closeModal() {
    restockModal.classList.remove("active");
    resetForm();
  }

  // Función para limpiar el formulario
  function resetForm() {
    restockForm.reset();
  }

  // Event listeners para cerrar la modal
  closeBtn.addEventListener("click", closeModal);
  cancelBtn.addEventListener("click", closeModal);
  window.addEventListener("click", (e) => {
    if (e.target === restockModal) {
      closeModal();
    }
  });

  // Validación del formulario
  function validateRestockData(data) {
    const errors = [];

    if (!data.ingredientId || !data.ingredientId.toString().trim()) {
      errors.push("ID del ingrediente no válido");
    }

    const quantity = parseFloat(data.quantity);
    if (isNaN(quantity) || quantity <= 0) {
      errors.push("La cantidad debe ser un número positivo");
    }

    return errors;
  }

  // Manejo del envío del formulario
  submitBtn.addEventListener("click", function (e) {
    e.preventDefault();

    const data = {
      ingredientId: document.getElementById("restock-ingredient-id").value,
      quantity: parseFloat(document.getElementById("restock-quantity").value),
    };

    const errors = validateRestockData(data);

    if (errors.length > 0) {
      showNotification(errors.join("\n"), "error");
      return;
    }

    fetch(
      "/../../controllers/IngredientsController.php?action=restockIngredient",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      }
    )
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showNotification(data.message, "success");
          closeModal();
          if (typeof reloadIngredientsTable === "function") {
            reloadIngredientsTable();
          }
        } else {
          showNotification(
            data.message || "Error al reabastecer el ingrediente",
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Error al procesar la solicitud", "error");
      });
  });

  // Validación adicional para la cantidad
  const quantityInput = document.getElementById("restock-quantity");
  quantityInput.addEventListener("input", function () {
    if (this.value < 0) {
      this.value = 0;
    }
  });
});
