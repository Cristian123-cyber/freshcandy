// IIFE para encapsular el código y evitar variables globales
(function () {
  // Selectores DOM
  const modal = document.getElementById("productModal");
  const modalHeader = modal.querySelector(".modal-header");

  const closeModalBtn = document.getElementById("close-modal");
  const cancelBtn = document.getElementById("cancel-edit");
  const submitBtn = document.getElementById("submit-product");
  const imageInput = document.getElementById("product-image");
  const previewImg = document.getElementById("preview-img");
  const imagePlaceholder = document.getElementById("image-placeholder");
  const removeImageBtn = document.getElementById("remove-image");
  const ingredientsContainer = document.getElementById("ingredients-container");

  // Estado para manejar la imagen
  let currentImage = null;
  let currentProductId = null;

  // Función para abrir la modal con animación
  const openModal = () => {
    modal.classList.add("active");
    document.body.style.overflow = "hidden"; // Prevenir scroll en el cuerpo

    updateEmptyState();

    // Enfocarse en el primer campo después de la animación
    setTimeout(() => {
      document.getElementById("product-name").focus();
    }, 300);
  };

  const setearTipoModal = (tipo) => {
    const title = modalHeader.querySelector(".modal-title");

    switch (tipo) {
      case "crear":
        title.textContent = "Crear Producto";
        //accion 0 indicara que se CREE un nuevo producto
        submitBtn.setAttribute("data-accion", 0);
        submitBtn.textContent = "Crear";

        break;
      case "editar":
        title.textContent = "Editar Producto";
        //accion 1 indicara que se ACTUALIZE producto existente

        submitBtn.setAttribute("data-accion", 1);
        submitBtn.textContent = "Guardar cambios";

        break;

      default:
        title.textContent = "Tipo de modal desconocido(sin acciones)";
        //accion 1 indicara que se ACTUALIZE producto existente

        submitBtn.setAttribute("data-accion", "");
        submitBtn.textContent = "";

        break;
    }
  };

  // Función para cerrar la modal con animación
  const closeModal = () => {
    modal.classList.remove("active");
    document.body.style.overflow = ""; // Restaurar scroll

    // Opcional: Resetear el formulario al cerrar
    setTimeout(() => {
      document.getElementById("edit-product-form").reset();
      resetImagePreview();
      resetContainerIngredients();
      resetCheckboxesIngredientes();
      submitBtn.setAttribute("data-accion", "");
      document.getElementById("product-name").focus();
    }, 300);
  };

  // Función para resetear el formulario
  const resetForm = () => {
    document.getElementById("product-name").value = "";
    document.getElementById("product-description").value = "";
    document.getElementById("product-price").value = "";
    document.getElementById("orderStatus").value = "1";
    resetImagePreview();
    resetContainerIngredients();
    currentProductId = null;
  };

  // Función para cargar datos de un producto en la modal
  const loadProductData = async (productData) => {
    currentProductId = productData.id;

    // Completar los campos básicos
    document.getElementById("product-name").value = productData.nombre || "";
    document.getElementById("product-description").value =
      productData.descripcion || "";
    document.getElementById("product-price").value = productData.precio || "";
    document.getElementById("orderStatus").value = productData.etiqueta || "1";

    // Cargar imagen
    if (productData.image_url) {
      previewImg.src = productData.image_url;
      previewImg.classList.add("has-image");
      imagePlaceholder.style.display = "none";
      currentImage = productData.image_url;
    } else {
      resetImagePreview();
    }

    // Cargar ingredientes
    try {
      const response = await fetch(
        `../../controllers/obtenerProductos.php?action=obtenerIngredientesProducto&id=${productData.id}`
      );
      const result = await response.json();

      if (result.success && Array.isArray(result.data)) {
        resetContainerIngredients();
        result.data.forEach((ingredient) => {
          addIngrediente(
            ingredient.id_ingrediente,
            ingredient.nombre,
            ingredient.cantidad
          );
        });
        updateEmptyState();
      }
    } catch (error) {
      console.error("Error al cargar ingredientes:", error);
    }
  };

  // Función para resetear la vista previa de la imagen
  const resetImagePreview = () => {
    previewImg.src = "";
    previewImg.classList.remove("has-image");
    imagePlaceholder.style.display = "flex";
    imageInput.value = ""; // Limpiar el input de archivo

    currentImage = null;
  };

  const resetContainerIngredients = () => {
    ingredientsContainer.innerHTML = "";
  };

  // Función para manejar la vista previa de la imagen seleccionada
  const handleImagePreview = (file) => {
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        previewImg.src = e.target.result;
        previewImg.classList.add("has-image");
        imagePlaceholder.style.display = "none";
        currentImage = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  };

  // Función para recoger los datos del formulario
  const getFormData = () => {
    const formData = {
      name: document.getElementById("product-name").value,
      description: document.getElementById("product-description").value,
      price: document.getElementById("product-price").value,
      ingredients: [],
      image: currentImage,
    };

    // Recoger ingredientes seleccionados
    const checkboxes = document.querySelectorAll(
      'input[name="ingredients[]"]:checked'
    );
    checkboxes.forEach((checkbox) => {
      formData.ingredients.push(parseInt(checkbox.value));
    });

    return formData;
  };

  // Función para guardar los cambios
  const saveChanges = () => {
    const formData = getFormData();
    console.log("Datos a guardar:", formData);

    // Aquí el desarrollador implementará su propia lógica para guardar los datos
    // Ejemplo de llamada a una función que sería implementada:
    // saveProductToServer(formData)
    //   .then(response => {
    //     closeModal();
    //     showNotification('Producto actualizado correctamente');
    //   })
    //   .catch(error => {
    //     showNotification('Error al actualizar producto', 'error');
    //   });

    // Por ahora, solo cerramos la modal
    // closeModal(); // <-- Eliminar o comentar esta línea para evitar cierre automático

    
  };

  // Cargar dinámicamente los ingredientes (función de ejemplo)
  const loadIngredients = (ingredients) => {
    // Esta función sería implementada por el desarrollador para cargar los ingredientes
    // desde la base de datos
    // Por ahora, usamos los ejemplos ya definidos en el HTML
  };

  // Event Listeners

  closeModalBtn.addEventListener("click", closeModal);
  cancelBtn.addEventListener("click", closeModal);
  submitBtn.addEventListener("click", saveChanges);

  // Cerrar modal al hacer clic en el overlay (fuera de la modal)
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  // Manejar la vista previa de la imagen
  imageInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    handleImagePreview(file);
  });

  // Remover la imagen
  removeImageBtn.addEventListener("click", resetImagePreview);

  // Prevenir el envío del formulario por defecto
  document
    .getElementById("edit-product-form")
    .addEventListener("submit", (e) => {
      e.preventDefault();
    });

  // Exponer funciones que necesitarán ser accedidas desde fuera
  window.FreshCandyModalEdicion = {
    open: openModal,
    close: closeModal,
    loadProductData: loadProductData,
    loadIngredients: loadIngredients,
    setTypeModal: setearTipoModal,
    marcarIngredientes: marcarIngredientes,
    updateEmptyState: updateEmptyState,
  };

  

  //modal ingredientes codigo

  // Mostrar/ocultar modal de ingredientes

  const idBtnEdit = submitBtn.getAttribute("data-accion");
  const btnAddIngredient = document.getElementById(
    parseInt(idBtnEdit) === 0 ? "btn-add-ingredient" : "btn-add-ingredient-edit"
  );
  const ingredientModal = document.getElementById("ingredientModal");
  const closeIngredientModal = document.getElementById(
    "close-ingredient-modal"
  );
  const cancelIngredients = document.getElementById("cancel-ingredients");
  const confirmIngredients = document.getElementById("confirm-ingredients");
  const ingredientSearch = document.getElementById("ingredient-search");

  // Abrir modal de ingredientes
  btnAddIngredient.addEventListener("click", async () => {
    await loadIngredientesForModal();
    ingredientModal.classList.add("active");
  });

  // Cerrar modal de ingredientes
  function closeIngredientsModal() {
    ingredientModal.classList.remove("active");
  }

  closeIngredientModal.addEventListener("click", closeIngredientsModal);
  cancelIngredients.addEventListener("click", closeIngredientsModal);

  // Buscar ingredientes
  ingredientSearch.addEventListener("input", (e) => {
    const searchTerm = e.target.value.toLowerCase();
    const ingredientItems = document.querySelectorAll(".ingredient-item");

    ingredientItems.forEach((item) => {
      const ingredientName = item
        .querySelector("label")
        .textContent.toLowerCase();
      if (ingredientName.includes(searchTerm)) {
        item.style.display = "flex";
      } else {
        item.style.display = "none";
      }
    });
  });

  // Confirmar selección de ingredientes
  confirmIngredients.addEventListener("click", () => {
    const selectedIngredients = document.querySelectorAll(
      '.ingredient-item input[type="checkbox"]:checked'
    );

    // Limpiar contenedor de ingredientes seleccionados
    //ingredientsContainer.innerHTML = '';

    // Agregar ingredientes seleccionados al contenedor con campos de cantidad
    selectedIngredients.forEach(async (checkbox) => {
      const ingredientId = checkbox.value;

      try {
        // Fetch ingredient details from controller
        const response = await fetch(
          `../../controllers/IngredientsController.php?action=getIngredientById&id=${ingredientId}`,
          {
            method: "POST",
          }
        );
        const result = await response.json();

        console.log("Response from controller:", result);
        console.log("Success value:", result.success);
        console.log("Data:", result.data);
        console.log("Ingredient:", result.data?.ingredient);

        if (result.success && result.data.ingredient) {
          const ingredient = result.data.ingredient;
          console.log("Adding ingredient:", ingredient);

          // Verificar si el ingrediente ya está en el contenedor
          const existingIngredient = document.querySelector(
            `.selected-ingredient-item[data-id="${ingredientId}"]`
          );
          if (!existingIngredient) {
            addIngrediente(
              ingredientId,
              ingredient.nombre_ing,
              ingredient.unidad || "Kg",
              0
            );
            updateEmptyState();
          }
        } else {
          console.error(
            "Error al obtener detalles del ingrediente:",
            result.message
          );
        }
      } catch (error) {
        console.error("Error al obtener ingrediente:", error);
      }
    });

    closeIngredientsModal();
  });

  // Función para actualizar el mensaje de contenedor vacío
  function updateEmptyState() {
    if (
      ingredientsContainer.querySelectorAll(".selected-ingredient-item")
        .length === 0
    ) {
      ingredientsContainer.innerHTML = `
            <div class="empty-ingredients">
                <i class="fas fa-cookie-bite"></i>
                <p>No hay ingredientes seleccionados</p>
            </div>
        `;
    } else if (ingredientsContainer.querySelector(".empty-ingredients")) {
      ingredientsContainer.querySelector(".empty-ingredients").remove();
    }
  }

  function marcarIngredientes() {
    const ingredientesSeleccionados = document.querySelectorAll(
      ".selected-ingredient-item"
    );

    if (ingredientesSeleccionados.length !== 0) {
      ingredientesSeleccionados.forEach((ingrediente) => {
        const idIng = ingrediente.getAttribute("data-id");
        const checkBoxObjetivo = document.querySelector(`#ing-${idIng}`);

        if (checkBoxObjetivo) {
          checkBoxObjetivo.checked = true;
        }
      });
    }
  }

  function resetCheckboxesIngredientes() {
    const checkBoxActivos = document.querySelectorAll(
      'input[name="ingredients[]"]:checked'
    );
    if (checkBoxActivos.length !== 0) {
      checkBoxActivos.forEach((checkBox) => {
        checkBox.checked = false;
      });
    }
  }

  function addIngrediente(ingredientId, ingredientName, unidad, cantidad = 0) {
    // Crear elemento de ingrediente seleccionado
    const ingredientElement = document.createElement("div");
    ingredientElement.className = "selected-ingredient-item";
    ingredientElement.setAttribute("data-id", ingredientId);

    ingredientElement.innerHTML = `
            <div class="ingredient-name">
                <i class="fas fa-cookie"></i>
                <span>${ingredientName}</span>
            </div>
            <div class="ingredient-quantity">
                <input type="number" name="ingredient-quantity-${ingredientId}" class="quantity-input" min="0" step="0.1" value="${cantidad}">
                <span>${unidad || "Kg"}</span>
                <button type="button" class="remove-ingredient-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

    ingredientsContainer.appendChild(ingredientElement);

    // Evento para eliminar ingrediente
    const removeBtn = ingredientElement.querySelector(".remove-ingredient-btn");
    removeBtn.addEventListener("click", () => {
      ingredientElement.remove();
      // Desmarcar el checkbox correspondiente
      const checkbox = document.querySelector(`#ing-${ingredientId}`);
      if (checkbox) {
        checkbox.checked = false;
      }
      updateEmptyState();
    });
  }

  // Función para cargar ingredientes en el modal de selección
  async function loadIngredientesForModal() {
    const container = document.querySelector(
      "#ingredientModal .ingredients-checkbox-container"
    );
    if (!container) return;

    container.innerHTML = "<div>Cargando...</div>";
    try {
      const response = await fetch(
        "../../controllers/obtenerProductos.php?action=obtenerIngredientes",
        {
          method: "POST",
        }
      );
      const result = await response.json();

      if (result.success && Array.isArray(result.data)) {
        container.innerHTML = "";
        result.data.forEach((ing) => {
          container.innerHTML += `
            <div class="ingredient-item">
              <input type="checkbox" id="ing-${ing.id_ingrediente}" name="ingredients[]" value="${ing.id_ingrediente}">
              <label for="ing-${ing.id_ingrediente}">${ing.nombre_ing}</label>
            </div>
          `;
        });
      } else {
        container.innerHTML = "<div>No hay ingredientes disponibles.</div>";
      }
    } catch (e) {
      container.innerHTML =
        "<div>Error al cargar ingredientes: " + e.message + "</div>";
      console.error("Error loading ingredients:", e);
    }
  }
})();
