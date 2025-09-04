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
    <link rel="stylesheet" href="../../assets//css//adminV2css//pedidos.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//modalPedido.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//perfilAdmin.css">
    <link rel="stylesheet" href="../../assets/css//estilosNotificacion.css">



    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">

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
                <h2>¡Hora de revisar los pedidos!</h2>
                <p>Controla el estado de cada orden y mantén felices a los clientes de Fresh Candy.</p>
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
                    <h3>Nuevos Pedidos Hoy</h3>
                    <p class="stat-value">0</p>

                </div>
            </div>
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <h3>Pedidos Pendientes</h3>
                    <p class="stat-value">0 </p>

                </div>
            </div>
            <div class="stat-card inventory">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Pedidos Completados</h3>
                    <p class="stat-value">0</p>


                </div>
            </div>
            <div class="stat-card suggestions">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Pedidos Cancelados</h3>
                    <p class="stat-value">0</p>

                </div>
            </div>
        </section>

        <section class="data-tables-section">
            <!-- Control Bar -->

            <div class="filters-wrapper">
                <div class="filters-container">
                    <div class="filters-header back-card-color">
                        <h3><i class="fas fa-filter iF"></i> Filtros de Búsqueda</h3>
                        <button id="aplicarFiltros" class="btn btn-primary">
                            <i class="fas fa-check"></i> Aplicar Filtros
                        </button>
                    </div>
                    <div class="filters-content">
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="filtroFecha" class="filter-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Filtrar por fecha</span>
                                </label>
                                <div class="select-wrapper">

                                    <input type="date" class="filter-select-control" id="date-filter">

                                </div>
                            </div>

                            <div class="filter-item">
                                <label for="filtroEstado" class="filter-label">
                                    <i class="fas fa-tasks"></i>
                                    <span>Estado</span>
                                </label>
                                <div class="select-wrapper">
                                    <select id="filtroEstado" class="filter-select">

                                    </select>
                                    <div class="select-arrow">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-item">
                                <label for="buscar" class="filter-label">
                                    <i class="fas fa-search"></i>
                                    <span>Buscar</span>
                                </label>
                                <div class="search-wrapper">
                                    <input type="text" id="buscar" class="filter-input" placeholder="Buscar por título o contenido...">
                                    <div class="search-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="data-card">
                <div class="data-header back-card-color">
                    <h3><i class="fas fa-box" style="margin-right: 5px;"></i> Pedidos</h3>

                </div>
                <div class="data-body">

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Método de Envío</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="orderTableBody">

                        </tbody>
                    </table>
                </div>
            </div>
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

    <!-- Modal Container -->
    <div class="modal-container" id="orderModal">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2><i class="fas fa-clipboard-list"></i></h2>
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
                                <span class="total-value original"></span>
                            </div>
                            <div class="total-row discount">
                                <span class="total-label">Descuento:</span>
                                <span class="total-value"></span>
                            </div>
                            <div class="total-row final">
                                <span class="total-label">Total Final</span>
                                <span class="total-value"></span>
                            </div>
                        </div>
                    </div>


                    <div class="order-info-card">
                        <div class="order-info-header">
                            <i class="fas fa-calendar-alt"></i> Fecha
                        </div>
                        <div class="order-info-value"></div>
                    </div>



                    <div class="order-info-card">
                        <div class="order-info-header">
                            <i class="fas fa-ticket-alt"></i> Descuento Promocional
                        </div>
                        <div class="order-info-value">
                            <span class="discount-badge">

                            </span>
                        </div>
                    </div>

                    <div class="order-info-card">
                        <div class="order-info-header">
                            <i class="fas fa-truck"></i> Método de Envío
                        </div>
                        <div class="order-info-value">
                            <span class="shipping-badge delivery">

                            </span>
                        </div>
                    </div>

                    <div class="order-info-card">
                        <div class="order-info-header">
                            <i class="fas fa-credit-card"></i> Método de Pago
                        </div>
                        <div class="order-info-value">
                            <span class="payment-badge credit">

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

                    <button class="btn btn-primary" id="saveOrderBtn" data-orderId="">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script type="module" src="../../assets//js//adminV2js//jsBase.js"></script>
    <script src="../../assets/js/adminV2js/sidebar.js"></script>
    <script type="module" src="../../assets//js//adminV2js//gestionPedidos.js"></script>
    <script type="module" src="../../assets//js//adminV2js//modalPedidos.js"></script>
    <script src="../../assets//js//adminV2js//perfilAdmin.js"></script>

   

    <script>
        document.getElementById("dashboard-link4").classList.add("active");
    </script>
</body>

</html>