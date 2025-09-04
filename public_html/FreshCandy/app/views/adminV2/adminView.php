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
    <link rel="stylesheet" href="../../assets//css//adminV2css//principal.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//sidebar.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//perfilAdmin.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//modalPedido.css">
    <link rel="stylesheet" href="../../assets/css//estilosNotificacion.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css/confirmDialog.css">



    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">

</head>

<body>

    <?php require_once '../../includes/sidebarAdmin.php'; ?>



    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <div class="left-section">
                <button id="menu-toggle" class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Dashboard</h1>
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
                <h2>¡Bienvenido de nuevo, Admin!</h2>
                <p>Aquí tienes el resumen de actividad de Fresh Candy</p>
            </div>
            <div class="date-display">
                <i class="far fa-calendar-alt"></i>
                <span id="current-date">Cargando fecha...</span>
            </div>
        </div>

        <!-- Stats Cards -->
        <section class="stat-cards">
            <div class="stat-card sales">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3>Pedidos Hoy</h3>
                    <p class="stat-value" id="ventas-hoy"></p>
                   
                </div>
            </div>
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <h3>Pedidos Pendientes</h3>
                    <p class="stat-value" id="pedidos-pendientes">0</p>
                    
                </div>
            </div>
            <div class="stat-card inventory">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>Stock Bajo</h3>
                    <p class="stat-value" id="stock-bajo">0</p>
                    
                </div>
            </div>
            <div class="stat-card suggestions">
                <div class="stat-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="stat-content">
                    <h3>Nuevas Sugerencias</h3>
                    <p class="stat-value" id="nuevas-sugerencias">0</p>
                    
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="charts-container">
            <div class="chart-card sales-chart">
                <div class="chart-header back-card-color">
                    <h3><i class="fas fa-chart-line" style="margin-right: 5px;"></i> Pedidos <span id="chart-period"></span></h3>
                    <div class="chart-actions">
                        <button class="chart-period-btn active" id="week-btn">Semana</button>
                        <button class="chart-period-btn" id="month-btn">Mes</button>
                        <button class="chart-period-btn" id="year-btn">Año</button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="salesChart">

                    </canvas>
                </div>
            </div>

            <div class="chart-card popular-products">
                <div class="chart-header back-card-color">
                    <h3><i class="fas fa-star" style="margin-right: 5px;"></i> Productos Más Vendidos</h3>

                </div>
                <div class="chart-body">
                    <canvas id="productsChart"></canvas>
                </div>
            </div>
        </section>

        <!-- Recent Orders & Low Stock -->
        <section class="data-tables-section">
            <div class="data-card recent-orders">
                <div class="data-header back-card-color">
                    <h3> <i class="fas fa-clock" style="margin-right: 5px;"></i> Pedidos Recientes</h3>
                    <a href="gestionPedidos.php" class="view-all-btn">Ver todos <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="data-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="recent-orders-tbody">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="data-card low-stock">
                <div class="data-header back-card-color">
                    <h3><i class="fas fa-triangle-exclamation" style="margin-right: 5px;"></i> Inventario Bajo</h3>
                    <a href="inventario.php" class="view-all-btn">Ver todos <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="data-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="low-stock-tbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Recent Suggestions -->
        <section class="suggestions-section">
            <div class="data-card recent-suggestions">
                <div class="data-header back-card-color">
                    <h3>Sugerencias Recientes</h3>
                    <a href="sugerenciasEnv.php" class="view-all-btn">Ver todas <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="data-body">
                    <div class="suggestion-card">
                        <div class="suggestion-header">
                            <div class="suggestion-user">
                                <img src="../../assets//images//user (1).png" alt="User Avatar">
                                <div>
                                    <h4>Laura Martínez</h4>
                                    <span class="suggestion-date">Hoy, 10:45 AM</span>
                                </div>
                            </div>
                            <span class="suggestion-type product-idea">Idea de Producto</span>
                        </div>
                        <h5 class="suggestion-title">Helado de Pistacho con Chocolate</h5>
                        <p class="suggestion-text">Me encantaría que ofrecieran un helado de pistacho con trozos de chocolate. ¡Sería una combinación deliciosa!</p>
                        <div class="suggestion-actions">
                            <button class="suggestion-btn mark-read"><i class="fas fa-check"></i> Marcar como revisado</button>
                            <button class="suggestion-btn delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>

                    <div class="suggestion-card">
                        <div class="suggestion-header">
                            <div class="suggestion-user">
                                <img src="../../assets//images//user (1).png" alt="User Avatar">
                                <div>
                                    <h4>Roberto Sánchez</h4>
                                    <span class="suggestion-date">Ayer, 16:20 PM</span>
                                </div>
                            </div>
                            <span class="suggestion-type improvement">Mejora</span>
                        </div>
                        <h5 class="suggestion-title">Opción de Envases Reutilizables</h5>
                        <p class="suggestion-text">Sería genial si ofrecieran descuentos por llevar envases reutilizables. Ayudaría al medio ambiente y podría atraer a más clientes.</p>
                        <div class="suggestion-actions">
                            <button class="suggestion-btn mark-read"><i class="fas fa-check"></i> Marcar como revisado</button>
                            <button class="suggestion-btn delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="dashboard-footer">
            <p>&copy; 2025 Fresh Candy. Todos los derechos reservados.</p>
        </footer>
    </main>

    <?php require_once '../../includes/perfilAdmin.php'; ?>

    <!-- Modal Container for Orders -->

    <!-- Modal Container -->
    <div class="modal-container" id="orderModal">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2><i class="fas fa-clipboard-list"></i> Detalle del Pedido #12345</h2>
                <button class="modal-close" id="closeModalBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Order Basic Info -->
                <div class="order-info">
                    <div class="order-info-card total-card">
                        <div class="order-info-header">
                            <i class="fas fa-tag"></i> Total
                        </div>
                        <div class="total-info">
                            <div class="total-row">
                                <span class="total-label">Subtotal:</span>
                                <span class="total-value original">$69.30</span>
                            </div>
                            <div class="total-row discount">
                                <span class="total-label">Descuento:</span>
                                <span class="total-value">-$10.40</span>
                            </div>
                            <div class="total-row final">
                                <span class="total-label">Total Final:</span>
                                <span class="total-value">$58.90</span>
                            </div>
                        </div>
                    </div>


                    <div class="order-info-card">
                        <div class="order-info-header">
                            <i class="fas fa-calendar-alt"></i> Fecha
                        </div>
                        <div class="order-info-value">05/07/2025 14:30</div>
                    </div>



                    <div class="order-info-card">
                        <div class="order-info-header">
                            <i class="fas fa-ticket-alt"></i> Descuento Promocional
                        </div>
                        <div class="order-info-value">
                            <span class="discount-badge">
                                <i class="fas fa-percent"></i> Código: SUMMER2025 (-15%)
                            </span>
                        </div>
                    </div>

                    <div class="order-info-card">
                        <div class="order-info-header">
                            <i class="fas fa-truck"></i> Método de Envío
                        </div>
                        <div class="order-info-value">
                            <span class="shipping-badge delivery">
                                <i class="fas fa-truck"></i> Entrega a Domicilio
                            </span>
                        </div>
                    </div>

                    <div class="order-info-card">
                        <div class="order-info-header">
                            <i class="fas fa-credit-card"></i> Método de Pago
                        </div>
                        <div class="order-info-value">
                            <span class="payment-badge credit">
                                <i class="fas fa-credit-card"></i> Tarjeta de Crédito
                            </span>
                        </div>
                    </div>


                </div>

                <!-- Products -->
                <h3 class="section-title"><i class="fas fa-ice-cream"></i> Productos</h3>

                <div class="table-scroll-wrapper">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="product-id">1</td>
                                <td class="product-name">Helado de fresa</td>
                                <td>2</td>
                                <td>$12.50</td>
                                <td class="product-price">$25.00</td>
                            </tr>
                            <tr>
                                <td class="product-id">1</td>

                                <td class="product-name">Helado de chicle</td>
                                <td>1</td>
                                <td>$8.90</td>
                                <td class="product-price">$8.90</td>
                            </tr>

                        </tbody>
                    </table>


                </div>


                <!-- Customer Info -->
                <h3 class="section-title"><i class="fas fa-address-card"></i> Información de envio</h3>
                <div class="customer-info">
                    <div class="customer-info-item">
                        <span class="customer-info-label">Nombre Completo</span>
                        <span class="customer-info-value" id="customer-name">María González Rodríguez</span>
                    </div>
                    <div class="customer-info-item">
                        <span class="customer-info-label">Cédula</span>
                        <span class="customer-info-value" id="customer-cc">12345678-9</span>
                    </div>
                    <div class="customer-info-item">
                        <span class="customer-info-label">Teléfono</span>
                        <span class="customer-info-value" id="customer-tel">+1 (555) 123-4567</span>
                    </div>
                    <div class="customer-info-item">
                        <span class="customer-info-label">Dirección</span>
                        <span class="customer-info-value" id="customer-dir">Calle Dulce 123, Colonia Azúcar, Ciudad Golosina</span>
                    </div>
                </div>

                <!-- Notas Adicionales -->
                <h3 class="section-title"><i class="fas fa-sticky-note"></i> Notas Adicionales</h3>
                <div class="order-notes">
                    <div class="notes-content">
                        <p>Entregar en la puerta principal. El cliente solicita que el helado sea empacado con hielo seco para mantener la temperatura.</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <div class="status-update">
                    <div class="status-update-header">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Actualizar Estado del Pedido</span>
                    </div>
                    <div class="status-select-wrapper">
                        <select id="orderStatus" class="order-status-select">
                            <!-- Options will be populated dynamically from the database -->
                        </select>
                        <div class="status-select-arrow">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-outline" id="printOrderBtn">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <button class="btn btn-primary" id="saveOrderBtn">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
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

    <!-- Modal de Reabastecimiento -->
    <div class="modal-overlay" id="restockIngredientModal">
        <div class="modal-container-restock">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="../../assets//js//adminV2js/chart.min.js"></script>

    <script src="../../assets/js/funciones.js"></script>
    <script type="module" src="../../assets//js//adminV2js//jsBase.js"></script>

    <script src="../../assets/js/adminV2js/sidebar.js"></script>
    <script src="../../assets/js/adminV2js/perfilAdmin.js"></script>
    <script type="module" src="../../assets/js/adminV2js/modalPedidos.js"></script>
    <script type="module" src="../../assets/js/adminV2js//modalRestock copy.js"></script>
    <script type="module" src="../../assets/js/adminV2js/principal.js"></script>

</body>

</html>