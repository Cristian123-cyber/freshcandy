import { reloadIngredientsTable, getCategories, getUnits } from "./inventario.js";

// Script para manejar la funcionalidad de la modal
document.addEventListener("DOMContentLoaded", function () {
  // Referencias a elementos DOM
  const addIngredientModal = document.getElementById("addIngredientModal");
  const closeBtn = document.getElementById("close-ingredient-modal");
  const cancelBtn = document.getElementById("cancel-add");
  const ingredientForm = document.getElementById("add-ingredient-form");
  const submitBtn = document.getElementById("submit-ingredient");
  const unitSelect = document.getElementById("ingredient-unit");
  const selectedUnitDisplay = document.getElementById("selected-unit");
  const selectedUnitDisplayLow = document.getElementById("selected-unit-low");

  // Función para abrir la modal
  window.openAddIngredientModal = async function () {
    // Cargar categorías

    const categories = await getCategories();
    const units = await getUnits();

    if (!categories || !units) {
      showNotification("Error al cargar las categorías o unidades", "error");
      return;
    }

    const categorySelect = document.getElementById("ingredient-category");
    categorySelect.innerHTML =
      '<option value="">Seleccione una categoría</option>';
    categories.forEach((category) => {
      categorySelect.innerHTML += `<option value="${category.id_categoria}">${category.titulo_categoria}</option>`;
    });

    const unitSelect = document.getElementById("ingredient-unit");
    unitSelect.innerHTML =
      '<option value="">Seleccione una unidad</option>';
    units.forEach((unit) => {
      unitSelect.innerHTML += `<option value="${unit.id_unidad}">${unit.abrev_unidad}</option>`;
    });

    // Mostrar la modal
    addIngredientModal.classList.add("active");
    document.body.style.overflow = "hidden";

  // Cerrar la función openAddIngredientModal
  };  

  // Función para cerrar la modal
  function closeModal() {
    addIngredientModal.classList.remove("active");
    document.body.style.overflow = "";
    resetForm();
  }

  // Función para limpiar los campos del formulario
  function resetForm() {
    document.getElementById("ingredient-name").value = "";
    document.getElementById("ingredient-category").value = "";
    document.getElementById("ingredient-stock").value = "";
    document.getElementById("ingredient-unit").value = "";
    document.getElementById("ingredient-critical-level").value = "";
    document.getElementById("ingredient-low-level").value = "";
    selectedUnitDisplay.textContent = "";
    selectedUnitDisplayLow.textContent = "";
  }

  // Event listeners para cerrar la modal
  closeBtn.addEventListener("click", closeModal);
  cancelBtn.addEventListener("click", closeModal);

  // Cerrar modal al hacer clic fuera
  addIngredientModal.addEventListener("click", function (e) {
    if (e.target === addIngredientModal) {
      closeModal();
    }
  });

  // Actualizar unidad seleccionada cuando cambia
  unitSelect.addEventListener("change", function () {
    selectedUnitDisplay.textContent = getAbrevUnit(this.value);
    selectedUnitDisplayLow.textContent = getAbrevUnit(this.value);
  });

  // Función para obtener la abreviatura de la unidad
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
        return "";
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

  // Manejo del envío del formulario
  submitBtn.addEventListener("click", function () {
    // Recolectar datos del formulario
    const formData = {
      name: document.getElementById("ingredient-name").value,
      category: document.getElementById("ingredient-category").value,
      stock: document.getElementById("ingredient-stock").value,
      unit: document.getElementById("ingredient-unit").value,
      criticalLevel: document.getElementById("ingredient-critical-level").value,
      lowLevel: document.getElementById("ingredient-low-level").value,
    };

    // Validar los datos
    const errors = validateIngredientData(formData);
    if (errors.length > 0) {
      // Mostrar errores al usuario
      showNotification(errors.join("\n"), "error");
      return;
    }

    // Enviar datos al servidor
    fetch(
      "/../../controllers/IngredientsController.php?action=addIngredient",
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
          showNotification(
            data.message || "Error al agregar el ingrediente",
            "error"
          );
          return;
        }

        showNotification("Ingrediente agregado correctamente", "success");
        // Limpiar el formulario y cerrar la modal
        closeModal();
        // Recargar la tabla
        reloadIngredientsTable();
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Error al agregar el ingrediente", "error");
      });
  });
});
