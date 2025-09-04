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
    <title>Fresh Candy Admin - Clientes Registrados</title>

    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/estilosBase.css">

    <link rel="stylesheet" href="../../assets/css/adminV2css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/clientes.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/variablesAdmin.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/infoModal.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/animaciones.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/perfilAdmin.css">



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
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-indicator"></span>
                    </button>
                    <div class="admin-dropdown">
                        <button class="admin-dropdown-btn">
                            <img src="../../assets//images//manager.png" alt="Admin">
                            <span>Admin</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="admin-dropdown-content">
                            <a id="admin-profile-btn"><i class="fas fa-user"></i> Perfil</a>

                            <a href="#"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
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
        <section class="stat-cards-sugerencias">
            <div class="stat-card sales">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Clientes</h3>
                    <p class="stat-value">1,892</p>
                    <p class="stat-comparison positive"><i class="fas fa-arrow-up"></i> 8% vs. mes anterior</p>
                </div>
            </div>
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3>Cliente con Más Pedidos</h3>
                    <p class="stat-value">Ana García</p>
                    <p class="stat-comparison">Total: 42 pedidos</p>
                </div>
            </div>
            <div class="stat-card inventory">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <h3>Mayor Gasto</h3>
                    <p class="stat-value">Carlos Ruiz</p>
                    <p class="stat-comparison">Total: $15,240</p>
                </div>
            </div>

        </section>




        <!-- Estadísticas de Clientes -->
        <!-- <div class="stat-cards">
            <div class="stat-card total-clients">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Clientes</h3>
                    <p class="stat-value">1,892</p>
                    <p class="stat-comparison positive"><i class="fas fa-arrow-up"></i> 8% vs. mes anterior</p>
                </div>
            </div>
            
            <div class="stat-card top-ordered">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3>Cliente con Más Pedidos</h3>
                    <p class="stat-value">Ana García</p>
                    <p class="stat-comparison">Total: 42 pedidos</p>
                </div>
            </div>
            
            <div class="stat-card top-spent">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <h3>Mayor Gasto</h3>
                    <p class="stat-value">Carlos Ruiz</p>
                    <p class="stat-comparison">Total: $15,240</p>
                </div>
            </div>
            
            <div class="stat-card newest-client">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-content">
                    <h3>Cliente Más Reciente</h3>
                    <p class="stat-value">Laura Sánchez</p>
                    <p class="stat-comparison">Registro: 10/05/2025</p>
                </div>
            </div>
        </div> -->

        <!-- Tabla de Clientes -->
        <section class="data-tables-section">


            <!-- Control Bar -->
           

            <!-- Filtros -->
            <div class="filters-wrapper">
                <div class="filters-container">
                    <div class="filters-header back-card-color">
                        <h3><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
                        <button id="aplicarFiltros" class="btn btn-primary">
                            <i class="fas fa-check"></i> Aplicar Filtros
                        </button>
                    </div>
                    <div class="filters-content">
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="filtroFecha" class="filter-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Ordenar clientes por:</span>
                                </label>
                                <div class="select-wrapper">
                                    <select id="filtroFecha" class="filter-select">
                                    <option value="defecto">Por defecto</option>
                                    <option value="fecha_reciente">Fecha de registro (más reciente)</option>
                                    <option value="fecha_antigua">Fecha de registro (más antigua)</option>
                                    <option value="pedidos_mayor">Total de pedidos (mayor a menor)</option>
                                    <option value="pedidos_menor">Total de pedidos (menor a mayor)</option>
                                    <option value="gasto_mayor">Total gastado (mayor a menor)</option>
                                    <option value="gasto_menor">Total gastado (menor a mayor)</option>
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
                    <h3><i class="fas fa-users" style="margin-right: 5px;"></i> Clientes Registrados</h3>

                </div>
                <div class="data-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Contacto</th>

                                <th>Total Pedidos</th>
                                <th>Total Gastado</th>
                                <th>Último Pedido</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tableIngredientesBody">
                            <tr>
                                <td>1001</td>
                                <td>Ana García</td>
                                <td>ana.garcia@email.com</td>
                                <td>555-123-4567</td>

                                <td>42</td>
                                <td>$12,560</td>
                                <td>08/05/2025</td>
                                <td>
                                    <button class="action-btn view-history" data-id="1001" title="Ver historial de pedidos"><i class="fas fa-history"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>1002</td>
                                <td>Carlos Ruiz</td>
                                <td>carlos.r@email.com</td>
                                <td>555-987-6543</td>

                                <td>36</td>
                                <td>$15,240</td>
                                <td>09/05/2025</td>
                                <td>
                                    <button class="action-btn view-history" data-id="1002" title="Ver historial de pedidos"><i class="fas fa-history"></i></button>
                                </td>
                            </tr>

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


        <!-- Modal de Historial de Pedidos -->

        <!--  <td>
            <button class="action-btn view-history" data-id="1005"><i class="fas fa-history"></i></button>
        </td> -->

        <footer class="dashboard-footer">
            <p>&copy; 2025 Fresh Candy. Todos los derechos reservados.</p>
        </footer>

    </main>

    <?php require_once '../../includes/perfilAdmin.php'; ?>

    <div id="orderHistoryModal" class="pim-modal">
        <div class="pim-modal-content">
            <div class="pim-modal-header">
                <h2>Historial de Pedidos - <span id="clientName">Cliente</span></h2>
                <button class="pim-close-modal">&times;</button>
            </div>
            <div class="pim-modal-body">
                <!-- Customer Info -->
                <h3 class="section-title"><i class="fas fa-address-card"></i> Información del Cliente</h3>
                <div class="customer-info">
                    <div class="customer-info-item">
                        <span class="customer-info-label">Nombre Completo</span>
                        <span class="customer-info-value" id="clientName2">María González Rodríguez</span>
                    </div>
                    <div class="customer-info-item">
                        <span class="customer-info-label">Cédula</span>
                        <span class="customer-info-value">12345678-9</span>
                    </div>
                    <div class="customer-info-item">
                        <span class="customer-info-label">Teléfono</span>
                        <span class="customer-info-value" id="clientPhone">+1 (555) 123-4567</span>
                    </div>
                    <div class="customer-info-item">
                        <span class="customer-info-label">Email</span>
                        <span class="customer-info-value" id="clientEmail">Calle Dulce 123, Colonia Azúcar, Ciudad Golosina</span>
                    </div>
                </div>

                <div class="client-summary">

                    <div class="client-stats">
                        <div class="client-stat">
                            <h4>Total Pedidos</h4>
                            <p id="clientTotalOrders">42</p>
                        </div>
                        <div class="client-stat">
                            <h4>Total Gastado</h4>
                            <p id="clientTotalSpent">$12,560</p>
                        </div>
                        <div class="client-stat">
                            <h4>Promedio/Pedido</h4>
                            <p id="clientAvgOrder">$299</p>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <h3 class="section-title"><i class="fas fa-receipt"></i> Pedidos</h3>

                <div class="table-scroll-wrapper">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="orderHistoryTableBody">
                            <!-- Ejemplo de filas -->
                            <tr>
                                <td>ORD-4587</td>
                                <td>08/05/2025</td>
                                <td>$450</td>
                                <td><span class="status-badge delivered">Entregado</span></td>
                            </tr>
                            <tr>
                                <td>ORD-4532</td>
                                <td>02/05/2025</td>
                                <td>$380</td>
                                <td><span class="status-badge delivered">Entregado</span></td>
                            </tr>
                            <tr>
                                <td>ORD-4498</td>
                                <td>25/04/2025</td>
                                <td>$520</td>
                                <td><span class="status-badge delivered">Entregado</span></td>
                            </tr>
                            <tr>
                                <td>ORD-4498</td>
                                <td>25/04/2025</td>
                                <td>$520</td>
                                <td><span class="status-badge delivered">Entregado</span></td>
                            </tr>
                            <tr>
                                <td>ORD-4498</td>
                                <td>25/04/2025</td>
                                <td>$520</td>
                                <td><span class="status-badge delivered">Entregado</span></td>
                            </tr>
                            <tr>
                                <td>ORD-4498</td>
                                <td>25/04/2025</td>
                                <td>$520</td>
                                <td><span class="status-badge delivered">Entregado</span></td>
                            </tr>
                        </tbody>


                    </table>


                </div>




            </div>
            <div class="pim-modal-footer">
                <button class="pim-btn-close">Cerrar</button>
            </div>
        </div>
    </div>




    <script type="module" src="../../assets//js//adminV2js//jsBase.js"></script>
    <script src="../../assets/js/adminV2js/sidebar.js"></script>
    <script src="../../assets//js//adminV2js//clientes.js"></script>
    <script src="../../assets//js//adminV2js//perfilAdmin.js"></script>


</body>

</html>