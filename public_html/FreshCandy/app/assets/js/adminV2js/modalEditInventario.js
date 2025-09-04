import { reloadIngredientsTable } from "./inventario.js";

// Script para manejar la funcionalidad de la modal de edición
document.addEventListener("DOMContentLoaded", function () {
  // Referencias a elementos DOM
  const editIngredientModal = document.getElementById("editIngredientModal");
  const closeBtn = document.getElementById("close-edit-ingredient-modal");
  const cancelBtn = document.getElementById("cancel-edit");
  const editForm = document.getElementById("edit-ingredient-form");
  const submitBtn = document.getElementById("submit-edit-ingredient");
  const unitSelect = document.getElementById("edit-ingredient-unit");
  const selectedUnitDisplay = document.getElementById("edit-selected-unit");
  const selectedUnitDisplayLow = document.getElementById(
    "edit-selected-unit-low"
  );

  // Función para abrir la modal con los datos del ingrediente
  window.openEditIngredientModal = function (ingredientData) {
    // Llenar el formulario con los datos del ingrediente
    document.getElementById("edit-ingredient-id").value =
      ingredientData.id_ingrediente;
    document.getElementById("edit-ingredient-name").value =
      ingredientData.nombre_ing || "";

    // Establecer la categoría usando el ID de la categoría
    const categorySelect = document.getElementById("edit-ingredient-category");
    if (categorySelect) {
      categorySelect.value =
        ingredientData.Categorias_Ingredientes_id_categoria || "";
    }

    document.getElementById("edit-ingredient-stock").value =
      ingredientData.stock_ing;

    // Establecer la unidad usando el ID de la unidad
    const unitSelect = document.getElementById("edit-ingredient-unit");
    if (unitSelect) {
      unitSelect.value = ingredientData.Unidades_id_unidad || "";
    }

    document.getElementById("edit-ingredient-critical-level").value =
      ingredientData.nivel_stock_critico;
    document.getElementById("edit-ingredient-low-level").value =
      ingredientData.nivel_stock_bajo;

    // Actualizar las unidades mostradas
    selectedUnitDisplay.textContent = ingredientData.unidad;
    selectedUnitDisplayLow.textContent = ingredientData.unidad;

    // Mostrar la modal
    editIngredientModal.classList.add("active");
    document.body.style.overflow = "hidden";
  };

  // Función para cerrar la modal
  function closeModal() {
    editIngredientModal.classList.remove("active");
    document.body.style.overflow = "";
    editForm.reset();
  }

  // Event listeners para cerrar la modal
  closeBtn.addEventListener("click", closeModal);
  cancelBtn.addEventListener("click", closeModal);

  // Cerrar modal al hacer clic fuera
  editIngredientModal.addEventListener("click", function (e) {
    if (e.target === editIngredientModal) {
      closeModal();
    }
  });

  // Actualizar unidad seleccionada cuando cambia
  unitSelect.addEventListener("change", function () {
    selectedUnitDisplay.textContent = getAbrevUnit(this.value);
    selectedUnitDisplayLow.textContent = getAbrevUnit(this.value);
  });

  // Función para determinar la clase del badge según el estado
  function getAbrevUnit(id_unidad) {
    switch (parseInt(id_unidad)) {
      case 1:
        return "kg";
      case 2:
        return "g";
      case 3:
        return "L";
      case 4:
        return "ml";
      case 5:
        return "u";
      default:
        return "N/A";
    }
  }

  // Función para validar los datos del formulario
  function validateIngredientData(formData) {
    const errors = [];

    if (!formData.name || formData.name.trim() === "") {
      errors.push("El nombre del ingrediente es requerido");
    }

    if (!formData.category || formData.category === "") {
      errors.push("La categoría es requerida");
    }

    if (
      !formData.stock ||
      isNaN(formData.stock) ||
      parseFloat(formData.stock) < 0
    ) {
      errors.push("El stock debe ser un número válido mayor o igual a 0");
    }

    if (!formData.unit || formData.unit === "") {
      errors.push("La unidad es requerida");
    }

    if (
      !formData.criticalLevel ||
      isNaN(formData.criticalLevel) ||
      parseFloat(formData.criticalLevel) < 0
    ) {
      errors.push(
        "El nivel crítico debe ser un número válido mayor o igual a 0"
      );
    }

    if (
      !formData.lowLevel ||
      isNaN(formData.lowLevel) ||
      parseFloat(formData.lowLevel) < 0
    ) {
      errors.push("El nivel bajo debe ser un número válido mayor o igual a 0");
    }

    if (parseFloat(formData.lowLevel) <= parseFloat(formData.criticalLevel)) {
      errors.push("El nivel bajo debe ser mayor que el nivel crítico");
    }

    return errors;
  }

  // Función para limpiar los campos del formulario
  function resetForm() {
    document.getElementById("edit-ingredient-id").value = "";
    document.getElementById("edit-ingredient-name").value = "";
    document.getElementById("edit-ingredient-category").value = "";
    document.getElementById("edit-ingredient-stock").value = "";
    document.getElementById("edit-ingredient-unit").value = "";
    document.getElementById("edit-ingredient-critical-level").value = "";
    document.getElementById("edit-ingredient-low-level").value = "";
    selectedUnitDisplay.textContent = "";
    selectedUnitDisplayLow.textContent = "";
  }

  // Manejo del envío del formulario
  submitBtn.addEventListener("click", function () {
    // Recolectar datos del formulario
    const formData = {
      id: document.getElementById("edit-ingredient-id").value,
      name: document.getElementById("edit-ingredient-name").value,
      category: document.getElementById("edit-ingredient-category").value,
      stock: document.getElementById("edit-ingredient-stock").value,
      unit: document.getElementById("edit-ingredient-unit").value,
      criticalLevel: document.getElementById("edit-ingredient-critical-level")
        .value,
      lowLevel: document.getElementById("edit-ingredient-low-level").value,
    };

    // Validar los datos
    const errors = validateIngredientData(formData);
    if (errors.length > 0) {
      // Mostrar errores al usuario
      alert(errors.join("\n"));
      return;
    }

    // Enviar datos al servidor
    fetch(
      "/../../controllers/IngredientsController.php?action=updateIngredient",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      }
    )
      .then((response) => response.json())
      .then((data) => {
        if (!data.success) {
          // Mostrar mensaje de error
          showNotification("Error al actualizar el ingrediente", "error");
          return;
        }

        showNotification("Ingrediente actualizado correctamente", "success");
        // Limpiar el formulario
        resetForm();
        // Cerrar la modal
        closeModal();
        // Recargar la tabla si existe la función
        reloadIngredientsTable();
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Error al actualizar el ingrediente", "error");
      });
  });
});
