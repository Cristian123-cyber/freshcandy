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
    <link rel="stylesheet" href="../../assets//css//adminV2css/sugerencias.css">
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//perfilAdmin.css">
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
         <section class="stat-cards-sugerencias">
            <div class="stat-card sales">
                <div class="stat-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-content">
                    <h3>Total de Sugerencias</h3>
                    <p class="stat-value">5</p>
                    
                </div>
            </div>
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <h3>Pendientes</h3>
                    <p class="stat-value">8</p>
                    
                </div>
            </div>
            <div class="stat-card revisadas">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-check"></i> 
                </div>
                <div class="stat-content">
                    <h3>Revisadas</h3>
                    <p class="stat-value">3</p>
                    
                </div>
            </div>
            
        </section>

        <section class="data-tables-section">
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
                                    <span>Filtrar por fecha</span>
                                </label>
                                <div class="select-wrapper">
                                    <select id="filtroFecha" class="filter-select">
                                        <option value="todas">Todas las fechas</option>
                                        <option value="hoy">Hoy</option>
                                        <option value="semana">Esta semana</option>
                                        <option value="mes">Este mes</option>
                                    </select>
                                    <div class="select-arrow">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-item">
                                <label for="filtroEstado" class="filter-label">
                                    <i class="fas fa-tasks"></i>
                                    <span>Estado</span>
                                </label>
                                <div class="select-wrapper">
                                    <select id="filtroEstado" class="filter-select">
                                        <option value="todos">Todos los estados</option>
                                        <!-- Opciones dinámicas se insertarán aquí por JS -->
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
        </section>

        <!-- Stats Cards -->
       

        

        

        <!-- Cards de sugerencias -->
        <div class="suggestions-list">
    <!-- Las sugerencias se cargarán aquí dinámicamente -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="../../assets//js//adminV2js//jsBase.js"></script>
    <script src="../../assets/js/adminV2js/sidebar.js"></script>
    <script src="../../assets//js//adminV2js//perfilAdmin.js"></script>
    <script type="module" src="../../assets/js/adminV2js/sugerenciasEnv.js"></script>
    <script src="../../assets/js/adminV2js/sugerenciasAccion.js"></script>

</body>

</html>