<?php
//Middleware basico para proteger la página
//Si no es administrador, redirige a la página de login
require_once '../../controllers/AuthMiddleware.php';
AuthMiddleware::protectAdmin();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Candy | Panel de Administración</title>
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">

    <link rel="stylesheet" href="../../assets//css//adminV2css//variablesAdmin.css">

    <link rel="stylesheet" href="../../assets//css//adminV2css/estilosBase.css">

    <link rel="stylesheet" href="../../assets//css//adminV2css//sidebar.css">
    <link rel="stylesheet" href="../../assets//css/estilosNotificacion.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//infoModal.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//gestionProductos.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//confirmDialog.css">

    <link rel="stylesheet" href="../../assets//css//adminV2css/modalEdicion.css">

    <link rel="stylesheet" href="../../assets//css/adminV2css//estilosConfirmDialog.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//perfilAdmin.css">
</head>

<body>

    <?php require_once '../../includes/sidebarAdmin.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <div class="left-section">
                <button id="menu-toggle" class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Gestion Productos</h1>
            </div>
            <div class="right-section">

                <div class="header-actions">
                    <!-- <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-indicator"></span>
                    </button> -->
                    <div class="admin-dropdown">
                        <button class="admin-dropdown-btn">
                            <img src="../../assets//images//manager.png" alt="Admin">
                            <span>Admin</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="admin-dropdown-content">
                            <a id="admin-profile-btn"><i class="fas fa-user"></i> Perfil</a>

                            <a id="logout-btn-perfil"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="dashboard-welcome">
            <div class="welcome-message">
                <h2>¡Toda la informacion correspondiente a los helados!</h2>
                <p>Aquí tienes el resumen de actividad de Fresh Candy</p>
            </div>
            <div class="date-display">
                <i class="far fa-calendar-alt"></i>
                <span id="current-date">Cargando fecha...</span>
            </div>
        </div>

        <!-- Stats Cards -->
        <section class="stat-cards-sugerencias">
            <div class="stat-card sales">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3>Helado Más Vendido</h3>
                    <p class="stat-value" id="most-sold-product-name"></p>
                    <p class="stat-comparison positive" id="most-sold-product-sales"><i class="fas fa-arrow-up"></i> 120 ventas este mes</p>
                </div>
            </div>
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-ice-cream"></i>
                </div>
                <div class="stat-content">
                    <h3>Menor Demanda</h3>
                    <p class="stat-value" id="least-sold-product-name"></p>
                    <p class="stat-comparison negative" id="least-sold-product-sales"><i class="fas fa-arrow-down"></i> Solo 5 pedidos este mes</p>
                </div>
            </div>
            <div class="stat-card inventory">
                <div class="stat-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <div class="stat-content">
                    <h3>Total de Productos</h3>
                    <p class="stat-value" id="total-products-count">18</p>
                    <p class="stat-comparison" id="total-products-description">Disponibles en el menú</p>
                </div>
            </div>

        </section>

        <!-- Products Management Section -->
        <section class="products-section">
            <!-- Control Bar -->
            <div class="control-bar">
                <div class="filter-controls">
                    <div class="search-filter">
                        <input type="text" id="products-search" placeholder="Buscar productos...">
                        <i class="fas fa-search"></i>
                    </div>




                    <div class="filter-dropdown">
                        <div class="filter-dropdown2">


                            <button class="filter-btn" aria-label="Filtrar productos">
                                <i class="fa-solid fa-filter"></i>
                                <span class="filter-text">Filtrar</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                            <div class="filter-menu hidden" id="filterMenu">
                                <div class="filter-section">
                                    <h4>Ordenar por:</h4>
                                    <button class="filter-option" data-filter="price_asc">
                                        <i class="fa-solid fa-arrow-up-wide-short"></i> Precio (Menor a Mayor)
                                    </button>
                                    <button class="filter-option" data-filter="price_desc">
                                        <i class="fa-solid fa-arrow-down-wide-short"></i> Precio (Mayor a Menor)
                                    </button>

                                    <button class="filter-option" data-filter="date_desc">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Más recientes
                                    </button>
                                    <button class="filter-option" data-filter="date_asc">
                                        <i class="fa-solid fa-hourglass-end"></i> Más antiguos
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button class="clear-filters-btn" disabled>
                            <i class="fa-solid fa-broom"></i>
                            <span class="sr-only">Limpiar filtros</span>
                        </button>
                    </div>



                </div>
                <div class="action-controls">

                    <button class="btn-add-product" id="openAddModal">
                        <i class="fas fa-plus"></i>
                        <span>Agregar Producto</span>
                    </button>
                </div>


            </div>

            <!-- Grid View -->
            <div class="products-grid-view" id="gridView">
                <!-- Sample Product Cards -->






            </div>

            <!-- Table View (hidden by default) -->
            <!-- Table View (hidden by default) -->




        </section>

        <!-- Modals -->
        <!-- Add Product Modal -->
        <div class="modal-overlay" id="addProductModal">
            <div class="modal-container">
                <div class="modal-header">
                    <h2 class="modal-title">Agregar Nuevo Producto</h2>
                    <button class="modal-close" id="close-add-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="add-product-form" class="edit-form" enctype="multipart/form-data">
                        <!-- Campo Nombre -->
                        <div class="form-group">
                            <label for="new-product-name">Nombre del Producto</label>
                            <input type="text" id="new-product-name" name="nombre" class="form-input" required>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="form-group">
                            <label for="new-product-description">Descripción</label>
                            <textarea id="new-product-description" name="descripcion" class="form-textarea" rows="3"></textarea>
                        </div>

                        <!-- Campo Etiqueta -->
                        <div class="form-group">
                            <label for="new-product-tag">Etiqueta</label>
                            <div class="status-select-wrapper">
                                <select id="new-product-tag" name="etiqueta" class="order-status-select form-sel">
                                    <option value="">Selecciona una etiqueta</option>
                                </select>
                                <div class="status-select-arrow">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Precio -->
                        <div class="form-group">
                            <label for="new-product-price">Precio</label>
                            <div class="price-input-container">
                                <span class="currency-symbol">$</span>
                                <input type="number" id="new-product-price" name="precio" class="form-input price-input" step="0.01" min="0" required>
                            </div>
                        </div>

                        <!-- Campo Ingredientes (Checkboxes) -->
                        <div class="form-group">
                            <label class="ingredient-label">
                                Ingredientes
                                <button type="button" class="btn-add-ingredient" id="btn-add-ingredient" title="Seleccionar ingredientes">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </label>
                            <div class="ingredients-container" id="ingredients-container">
                                <!-- Aquí se mostrarán los ingredientes seleccionados con sus cantidades -->
                            </div>
                        </div>

                        <!-- Campo Imagen -->
                        <div class="form-group">
                            <label for="new-product-image">Imagen del Producto</label>
                            <div class="image-upload-container">
                                <div class="image-preview" id="new-image-preview">
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Vista Previa</p>
                                    </div>
                                </div>
                                <div class="image-upload-controls">
                                    <label for="new-product-image" class="upload-btn">
                                        <i class="fas fa-upload"></i> <span>Seleccionar Imagen</span>
                                    </label>
                                    <input type="file" id="new-product-image" name="imagen" accept="image/*" class="file-input">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="button-secondary" id="cancel-add">Cancelar</button>
                    <button type="button" class="button-primary" id="submit-add-product">Agregar Producto</button>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div class="modal-overlay" id="productModal">
            <div class="modal-container">
                <!-- Header de la modal -->
                <div class="modal-header">
                    <h2 class="modal-title">Editar Producto</h2>
                    <button class="modal-close" id="close-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Cuerpo de la modal -->
                <div class="modal-body">
                    <form id="edit-product-form" class="edit-form">
                        <!-- Campo Nombre -->
                        <div class="form-group">
                            <label for="product-name">Nombre del Producto</label>
                            <input type="text" id="product-name" name="nombre" class="form-input" required>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="form-group">
                            <label for="product-description">Descripción</label>
                            <textarea id="product-description" name="descripcion" class="form-textarea" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product-description">Etiqueta</label>

                            <div class="status-select-wrapper">
                                <select id="orderStatus" class="order-status-select form-sel" name="etiqueta">
                                    <option value="1">Sin etiqueta</option>
                                    <option value="2">Popular</option>
                                    <option value="3">Nuevo</option>
                                    <option value="4">Favorito</option>
                                </select>
                                <div class="status-select-arrow">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Precio -->
                        <div class="form-group">
                            <label for="product-price">Precio</label>
                            <div class="price-input-container">
                                <span class="currency-symbol">$</span>
                                <input type="number" id="product-price" name="precio" class="form-input price-input" step="0.01" min="0" required>
                            </div>
                        </div>

                        <!-- Campo Ingredientes (Checkboxes) -->
                        <div class="form-group">
                            <label class="ingredient-label">
                                Ingredientes
                                <button type="button" class="btn-add-ingredient" id="btn-add-ingredient-edit" title="Seleccionar ingredientes">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </label>
                            <div class="ingredients-container" id="ingredients-container">
                                <!-- Aquí se mostrarán los ingredientes seleccionados con sus cantidades -->
                            </div>
                        </div>


                        <!-- Campo Imagen con Vista Previa -->
                        <div class="form-group">
                            <label for="product-image">Imagen del Producto</label>
                            <div class="image-upload-container">
                                <div class="image-preview" id="image-preview">
                                    <img id="preview-img" src="#" alt="Vista previa">
                                    <div class="image-placeholder" id="image-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Vista Previa</p>
                                    </div>
                                </div>
                                <div class="image-upload-controls">
                                    <label for="product-image" class="upload-btn">
                                        <i class="fas fa-upload"></i> <span>Seleccionar Imagen</span>
                                    </label>
                                    <input type="file" id="product-image" name="product-image" accept="image/*" class="file-input">
                                    <button type="button" id="remove-image" class="remove-btn">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer de la modal con botones -->
                <div class="modal-footer">
                    <button type="button" class="button-secondary" id="cancel-edit">Cancelar</button>
                    <button type="button" class="button-primary" id="submit-product">Guardar Cambios</button>
                </div>
            </div>
        </div>

        <!-- Add Ingredient Modal -->
        <div class="ingredient-modal-overlay" id="ingredientModal">
            <div class="ingredient-modal-container">
                <div class="ingredient-modal-header">
                    <h3>Seleccionar Ingredientes</h3>
                    <button class="modal-close" id="close-ingredient-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="ingredient-modal-body">
                    <div class="ingredient-search">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="ingredient-search" placeholder="Buscar ingrediente...">
                    </div>
                    <div class="ingredients-checkbox-container">
                        <!-- Los checkboxes se cargarán dinámicamente con JS -->
                    </div>
                </div>
                <div class="ingredient-modal-footer">
                    <button type="button" class="button-secondary" id="cancel-ingredients">Cancelar</button>
                    <button type="button" class="button-primary" id="confirm-ingredients">Confirmar</button>
                </div>
            </div>
        </div>


        <!-- View Info Product Modal -->

        <div id="productIngredientsModal" class="pim-modal">
            <div class="pim-modal-content">
                <div class="pim-modal-header">
                    <h2 id="pim-product-title">Nombre del Producto</h2>
                    <button class="pim-close-modal">&times;</button>
                </div>
                <div class="pim-modal-body">
                    <div class="pim-ingredients-list">
                        <!-- Los ingredientes se añadirán dinámicamente aquí -->
                    </div>
                </div>
                <div class="pim-modal-footer">
                    <button class="pim-btn-close">Cerrar</button>
                </div>
            </div>
        </div>



        <!-- Confirmation Dialog -->

        <!-- Modal de Confirmación -->
        <div id="dangerActionModal" class="dam-modal">
            <div class="dam-modal-content">
                <div class="dam-modal-header">
                    <i id="dam-modal-icon" class="dam-icon"></i>
                    <h2 id="dam-modal-title">Confirmar acción</h2>
                    <button class="dam-close-modal">&times;</button>
                </div>
                <div class="dam-modal-body">
                    <p id="dam-modal-message">¿Estás seguro que deseas realizar esta acción?</p>
                </div>
                <div class="dam-modal-footer">
                    <button class="dam-btn dam-btn-cancel">Cancelar</button>
                    <button class="dam-btn dam-btn-confirm">Confirmar</button>
                </div>
            </div>
        </div>










        <footer class="dashboard-footer">
            <p>&copy; 2025 Fresh Candy. Todos los derechos reservados.</p>
        </footer>
    </main>

    <?php require_once '../../includes/perfilAdmin.php'; ?>

    <script src="../../assets/js/funciones.js"></script>
    <script type="module" src="../../assets//js//adminV2js//jsBase.js"></script>
    <script src="../../assets/js/adminV2js/sidebar.js"></script>
    <script src="../../assets//js//adminV2js/modalEdicion.js"></script>
    <script src="../../assets//js//adminV2js/infoModal.js"></script>
    <script type="module" src="../../assets//js//adminV2js//confirmDialog.js"></script>
    <script src="../../assets//js//adminV2js//filtros.js"></script>
    <script src="../../assets//js//adminV2js//perfilAdmin.js"></script>
    <script type="module" src="../../assets//js//adminV2js/gestionProductos.js"></script>

</body>

</html>