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

    <link rel="stylesheet" href="../../assets//css//adminV2css//variablesAdmin.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css/estilosBase.css">

    <link rel="stylesheet" href="../../assets//css//adminV2css//sidebar.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//animaciones.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css/confirmDialog.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//perfilAdmin.css">
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//inventario.css">
    <link rel="stylesheet" href="../../assets/css//estilosNotificacion.css">


</head>

<body>

    <?php require_once '../../includes/sidebarAdmin.php'; ?>


    <main class="main-content">

        <header class="page-header">
            <div class="left-section">
                <button id="menu-toggle" class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Inventario</h1>
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
                <h2>¡Hora de revisar el inventario!</h2>
                <p>Mantén el control de tus ingredientes y asegura el stock de Fresh Candy.</p>
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
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>Ingredientes en Stock Bajo</h3>
                    <p class="stat-value" id="stock-bajo-count">4</p>

                </div>
            </div>
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-content">
                    <h3>Sin Existencias</h3>
                    <p class="stat-value" id="sin-existencias-count">2</p>

                </div>
            </div>
            <div class="stat-card inventory">
                <div class="stat-icon">
                    <i class="fas fa-thermometer-empty"></i>
                </div>
                <div class="stat-content">
                    <h3>Ingredientes Críticos</h3>
                    <p class="stat-value" id="ingredientes-criticos-count">1</p>

                </div>
            </div>

        </section>



        <!-- Inventory Section Here - Diseña la seccion de inventario para mi sistema -->

        <!-- Inventory Section -->
        <section class="data-tables-section">


            <!-- Control Bar -->
            <div class="filters-wrapper">
                <div class="filters-container">
                    <div class="filters-header">
                        <h3><i class="fas fa-filter iF"></i> Filtros de Inventario</h3>
                        <button class="btn-add-ingredient" id="openAddModal">
                            <i class="fas fa-plus"></i>
                            <span>Agregar Ingrediente</span>
                        </button>
                    </div>
                    <div class="filters-content">
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="products-search" class="filter-label">
                                    <i class="fas fa-search"></i>
                                    <span>Buscar</span>
                                </label>
                                <div class="search-wrapper">
                                    <input type="text" id="products-search" class="filter-input" placeholder="Buscar ingredientes...">
                                    <div class="search-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-item">
                                <label for="category-filter" class="filter-label">
                                    <i class="fas fa-tags"></i>
                                    <span>Categoría</span>
                                </label>
                                <div class="select-wrapper">
                                    <select id="category-filter" class="filter-select">
                                        <option value="">Todas las categorías</option>
                                        <option value="1">Bases</option>
                                        <option value="2">Saborizantes</option>
                                        <option value="3">Toppings</option>
                                        <option value="4">Decoración</option>
                                    </select>
                                    <div class="select-arrow">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-item">
                                <label for="status-filter" class="filter-label">
                                    <i class="fas fa-flag"></i>
                                    <span>Estado</span>
                                </label>
                                <div class="select-wrapper">
                                    <select id="status-filter" class="filter-select">
                                        <option value="">Todos los estados</option>
                                        <option value="normal">Normal</option>
                                        <option value="low">Stock bajo</option>
                                        <option value="expiring">Por vencer</option>
                                    </select>
                                    <div class="select-arrow">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="data-card">
                <div class="data-header back-card-color">
                    <h3><i class="fas fa-ice-cream" style="margin-right: 5px;"></i>Inventario De Ingredientes</h3>

                </div>
                <div class="data-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ingrediente</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tableIngredientesBody">
                            

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination Footer -->
            <div class="table-footer">
                <div class="pagination-info">
                    Mostrando <span id="pagination-from">1</span> a <span id="pagination-to">10</span> de <span id="pagination-total">0</span> registros
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="pagination-first" title="Primera página" disabled>
                        <i class="fas fa-angle-double-left"></i>
                    </button>
                    <button class="pagination-btn" id="pagination-prev" title="Página anterior" disabled>
                        <i class="fas fa-angle-left"></i>
                    </button>
                    <div class="pagination-pages">
                        <span id="pagination-current-page"> 1 </span> de <span id="pagination-total-pages"> 1</span>
                    </div>
                    <button class="pagination-btn" id="pagination-next" title="Página siguiente">
                        <i class="fas fa-angle-right"></i>
                    </button>
                    <button class="pagination-btn" id="pagination-last" title="Última página">
                        <i class="fas fa-angle-double-right"></i>
                    </button>
                </div>
                <div class="pagination-size">
                    <label for="items-per-page">Mostrar</label>
                    <select id="items-per-page" class="select-pagination">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>por página</span>
                </div>
            </div>
        </section>












        <footer class="dashboard-footer">
            <p>&copy; 2025 Fresh Candy. Todos los derechos reservados.</p>
        </footer>

    </main>

    <?php require_once '../../includes/perfilAdmin.php'; ?>

    <!-- Modal de Agregar Ingrediente -->
    <!-- Modal para Agregar Ingredientes -->
    <div class="modal-overlay" id="addIngredientModal">
        <div class="modal-container">
            <!-- Header de la modal -->
            <div class="modal-header">
                <h2 class="modal-title">Agregar Ingrediente</h2>
                <button class="modal-close" id="close-ingredient-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Cuerpo de la modal -->
            <div class="modal-body">
                <form id="add-ingredient-form" class="edit-form">
                    <!-- Campo Nombre -->
                    <div class="form-group">
                        <label for="ingredient-name">Nombre del Ingrediente</label>
                        <input type="text" id="ingredient-name" name="ingredient-name" class="form-input" required>
                    </div>

                    <!-- Campo Categoría -->
                    <div class="form-group">
                        <label for="ingredient-category">Categoría</label>
                        <select id="ingredient-category" name="ingredient-category" class="form-input" required>
                            
                        </select>
                    </div>

                    <!-- Campo Stock Inicial -->
                    <div class="form-group">
                        <label for="ingredient-stock">Stock Inicial</label>
                        <div class="stock-input-container">
                            <input type="number" id="ingredient-stock" name="ingredient-stock" class="form-input" step="0.1" min="0" required>
                            <select id="ingredient-unit" name="ingredient-unit" class="form-input unit-select">
                                
                            </select>
                        </div>
                    </div>

                    <!-- Campo Nivel Crítico -->
                    <div class="form-group">
                        <label for="ingredient-critical-level">Nivel Crítico</label>
                        <div class="stock-input-container">
                            <input type="number" id="ingredient-critical-level" name="ingredient-critical-level" class="form-input" step="0.1" min="0" required>
                            <span class="unit-display" id="selected-unit">Kg</span>
                        </div>
                        <small class="form-helper">Cantidad mínima antes de mostrar alerta de stock crítico</small>
                    </div>

                    <!-- Campo Nivel Bajo -->
                    <div class="form-group">
                        <label for="ingredient-low-level">Nivel Bajo</label>
                        <div class="stock-input-container">
                            <input type="number" id="ingredient-low-level" name="ingredient-low-level" class="form-input" step="0.1" min="0" required>
                            <span class="unit-display" id="selected-unit-low">Kg</span>
                        </div>
                        <small class="form-helper">Cantidad mínima antes de mostrar alerta de stock bajo</small>
                    </div>
                </form>
            </div>

            <!-- Footer de la modal con botones -->
            <div class="modal-footer">
                <button type="button" class="button-secondary" id="cancel-add">Cancelar</button>
                <button type="button" class="button-primary" id="submit-ingredient">
                    <i class="fas fa-save"></i> Guardar Ingrediente
                </button>
            </div>
        </div>
    </div>

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

    <!-- Modal de Editar Ingrediente -->
    <div class="modal-overlay" id="editIngredientModal">
        <div class="modal-container">
            <!-- Header de la modal -->
            <div class="modal-header">
                <h2 class="modal-title">Editar Ingrediente</h2>
                <button class="modal-close" id="close-edit-ingredient-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Cuerpo de la modal -->
            <div class="modal-body">
                <form id="edit-ingredient-form" class="edit-form">
                    <!-- Campo ID (oculto) -->
                    <input type="hidden" id="edit-ingredient-id" name="edit-ingredient-id">

                    <!-- Campo Nombre -->
                    <div class="form-group">
                        <label for="edit-ingredient-name">Nombre del Ingrediente</label>
                        <input type="text" id="edit-ingredient-name" name="edit-ingredient-name" class="form-input" required>
                    </div>

                    <!-- Campo Categoría -->
                    <div class="form-group">
                        <label for="edit-ingredient-category">Categoría</label>
                        <select id="edit-ingredient-category" name="edit-ingredient-category" class="form-input" required>
                            
                        </select>
                    </div>

                    <!-- Campo Stock Actual -->
                    <div class="form-group">
                        <label for="edit-ingredient-stock">Stock Actual</label>
                        <div class="stock-input-container">
                            <input type="number" id="edit-ingredient-stock" name="edit-ingredient-stock" class="form-input" step="0.1" min="0" required>
                            <select id="edit-ingredient-unit" name="edit-ingredient-unit" class="form-input unit-select">
                                <option value="Kg">Kg</option>
                                <option value="L">L</option>
                                <option value="g">g</option>
                                <option value="ml">ml</option>
                                <option value="unidades">unidades</option>
                            </select>
                        </div>
                    </div>


                    <!-- Campo Nivel Crítico -->
                    <div class="form-group">
                        <label for="edit-ingredient-critical-level">Nivel Crítico</label>
                        <div class="stock-input-container">
                            <input type="number" id="edit-ingredient-critical-level" name="edit-ingredient-critical-level" class="form-input" step="0.1" min="0" required>
                            <span class="unit-display" id="edit-selected-unit">Kg</span>
                        </div>
                        <small class="form-helper">Cantidad mínima antes de mostrar alerta de stock crítico</small>
                    </div>

                    <!-- Campo Nivel Bajo -->
                    <div class="form-group">
                        <label for="edit-ingredient-low-level">Nivel Bajo</label>
                        <div class="stock-input-container">
                            <input type="number" id="edit-ingredient-low-level" name="edit-ingredient-low-level" class="form-input" step="0.1" min="0" required>
                            <span class="unit-display" id="edit-selected-unit-low">Kg</span>
                        </div>
                        <small class="form-helper">Cantidad mínima antes de mostrar alerta de stock bajo</small>
                    </div>
                </form>
            </div>

            <!-- Footer de la modal con botones -->
            <div class="modal-footer">
                <button type="button" class="button-secondary" id="cancel-edit">Cancelar</button>
                <button type="button" class="button-primary" id="submit-edit-ingredient">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Reabastecimiento -->
    <div class="modal-overlay" id="restockIngredientModal">
        <div class="modal-container">
            <!-- Header de la modal -->
            <div class="modal-header">
                <h2 class="modal-title">Reabastecer Stock</h2>
                <button class="modal-close" id="close-restock-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Cuerpo de la modal -->
            <div class="modal-body">
                <form id="restock-form" class="restock-form">
                    <!-- Campo ID (oculto) -->
                    <input type="hidden" id="restock-ingredient-id" name="restock-ingredient-id">

                    <!-- Información del Ingrediente -->
                    <div class="form-group">
                        <label>Ingrediente</label>
                        <div class="ingredient-info">
                            <span id="restock-ingredient-name">Nombre del ingrediente</span>
                            <span class="current-stock" id="restock-current-stock">Stock actual: 0 Kg</span>
                        </div>
                    </div>

                    <!-- Campo Cantidad a Agregar -->
                    <div class="form-group">
                        <label for="restock-quantity">Cantidad a Agregar</label>
                        <div class="stock-input-container">
                            <input type="number" id="restock-quantity" name="restock-quantity" class="form-input" step="0.1" min="0" required>
                            <span class="unit-display" id="restock-unit">Kg</span>
                        </div>
                    </div>

                    <!-- Campo Notas -->
                    <div class="form-group">
                        <label for="restock-notes">Notas (opcional)</label>
                        <textarea id="restock-notes" name="restock-notes" class="form-input" rows="3" placeholder="Agregar notas sobre el reabastecimiento..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Footer de la modal con botones -->
            <div class="modal-footer">
                <button type="button" class="button-secondary" id="cancel-restock">Cancelar</button>
                <button type="button" class="button-primary" id="submit-restock">
                    <i class="fas fa-box-open"></i> Confirmar Reabastecimiento
                </button>
            </div>
        </div>
    </div>
    <script src="../../assets/js/funciones.js"></script>
    <script type="module" src="../../assets//js//adminV2js//jsBase.js"></script>
    <script type="module" src="../../assets/js/adminV2js/inventario.js"></script>
    <script type="module" src="../../assets//js//adminV2js//modalnventarioIngrediente.js"></script>
    <script type="module" src="../../assets//js//adminV2js//modalEditInventario.js"></script>
    <script type="module" src="../../assets//js//adminV2js//modalRestock.js"></script>
    <script src="../../assets//js//adminV2js//paginationInventario.js"></script>
    <script src="../../assets/js/adminV2js/sidebar.js"></script>
    <script src="../../assets//js//adminV2js//perfilAdmin.js"></script>

</body>

</html>