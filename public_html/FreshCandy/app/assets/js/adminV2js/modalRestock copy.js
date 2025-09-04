// Función para mostrar notificaciones
function showNotification(message, type) {
  // Crear elemento de notificación
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.innerHTML = `
      <div class="notification-content">
          <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
          <span>${message}</span>
      </div>
      `;

  // Añadir al DOM
  document.body.appendChild(notification);

  // Mostrar con animación
  setTimeout(() => {
      notification.classList.add('show');
  }, 10);

  // Ocultar después de 3 segundos
  setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => {
          notification.remove();
      }, 300);
  }, 3000);
}

// Script para manejar la funcionalidad de la modal de reabastecimiento
document.addEventListener("DOMContentLoaded", function () {
  // Referencias a elementos DOM
  const restockModal = document.getElementById("restockIngredientModal");
  const closeBtn = document.getElementById("close-restock-modal");
  const cancelBtn = document.getElementById("cancel-restock");
  const restockForm = document.getElementById("restock-form");
  const submitBtn = document.getElementById("submit-restock");

  // Función para abrir la modal con los datos del ingrediente
  window.openRestockModalAdmin = function (ingredientId) {
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
          fetchAndRenderLowStock();
          
          closeModal();
         
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

      /// Determine stock badge class
      let statusClass =
      ingredient.Estados_Stock_id_estado === 3 ? "critical" :
      ingredient.Estados_Stock_id_estado === 4 ? "out-of-stock" : "low";
    let statusText =
      ingredient.Estados_Stock_id_estado === 3 ? "Crítico" :
      ingredient.Estados_Stock_id_estado === 4 ? "Agotado" : "Bajo";

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
