<?php
//Middleware basico para proteger la página
//Si no es administrador, redirige a la página de login
require_once '../../controllers/AuthMiddleware.php';
AuthMiddleware::protectAdmin();

// require_once __DIR__ . '/../assets/plugins/fpdf/fpdf.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Candy | Reportes</title>

    <!-- CSS Básicos -->
    <link rel="stylesheet" href="../../assets/css/adminV2css/variablesAdmin.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/estilosBase.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/animaciones.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css//reportesView.css">
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/adminV2css/perfilAdmin.css">
</head>



<body>

<h1>DISABLED</h1>
    
<!--
    <?php require_once '../../includes/sidebarAdmin.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <div class="left-section">
                <button id="menu-toggle" class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Reportes</h1>
            </div>
            <div class="right-section">
                <div class="header-actions">
                   
                    <div class="admin-dropdown">
                        <button class="admin-dropdown-btn">
                            <img src="../../assets/images/manager.png" alt="Admin">
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
                <h2>¡Bienvenido a los reportes!</h2>
                <p>Consulta y analiza los datos clave de Fresh Candy.</p>
            </div>
            <div class="date-display">
                <i class="far fa-calendar-alt"></i>
                <span id="current-date">Cargando fecha...</span>
            </div>
        </div>

       
        <section class="reportes-section">
            <div class="reportes-container">
                
                <div class="report-card" id="report-inventario">
                    <div class="report-card-header">
                        <div class="report-card-title">
                            <i class="fas fa-box-open"></i>
                            <h3>Reporte de inventario de ingredientes</h3>
                        </div>
                        <span class="report-card-badge">Información actualizada</span>
                    </div>
                    <div class="report-card-body">
                        <p class="report-description">Genera un reporte detallado del inventario actual de todos los ingredientes disponibles en el sistema.</p>

                        <div class="report-filters">
                            <div class="filter-group">
                                <label for="categoria-inventario">Categoría de ingredientes:</label>
                                <select id="categoria-inventario" class="filter-select">
                                    <option value="todos">Todos los ingredientes</option>
                                  
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="stock-status">Estado de stock:</label>
                                <select id="stock-status" class="filter-select">
                                    <option value="todos">Todos los estados</option>
                                    <option value="ok">Stock óptimo</option>
                                    <option value="low">Stock bajo</option>
                                    <option value="critical">Stock crítico</option>
                                </select>
                            </div>
                        </div>

                        <div class="report-preview-container">
                            <div class="table-scroll-wrapper">
                                <table class="report-table" id="inventario-table">
                                    <thead>
                                        <tr>
                                            <th>Ingrediente</th>
                                            <th>Categoría</th>
                                            <th>Stock actual</th>
                                            <th>Unidad</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                
                                    </tbody>
                                </table>

                            </div>

                        </div>

                        <div class="report-actions">
                            <button class="btn-generate-pdf" data-report="inventario">
                                <i class="fas fa-file-pdf"></i>
                                Generar PDF
                            </button>
                        </div>
                    </div>
                </div>

               
                <div class="report-card" id="report-pedidos-fechas">
                    <div class="report-card-header">
                        <div class="report-card-title">
                            <i class="fas fa-calendar-alt"></i>
                            <h3>Reporte de pedidos por fechas</h3>
                        </div>
                        <span class="report-card-badge">Análisis temporal</span>
                    </div>
                    <div class="report-card-body">
                        <p class="report-description">Genera un reporte detallado de todos los pedidos realizados en un rango de fechas específico.</p>

                        <div class="report-filters">
                            <div class="filter-group">
                                <label for="fecha-inicio">Fecha inicio:</label>
                                <input type="date" id="fecha-inicio" class="filter-date">
                            </div>
                            <div class="filter-group">
                                <label for="fecha-fin">Fecha fin:</label>
                                <input type="date" id="fecha-fin" class="filter-date">
                            </div>
                            <div class="filter-group">
                                <label for="estado-pedido">Estado:</label>
                                <select id="estado-pedido" class="filter-select">
                                    <option value="todos">Todos los estados</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="processing">En proceso</option>
                                    <option value="shipped">Enviado</option>
                                    <option value="delivered">Entregado</option>
                                    <option value="canceled">Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <div class="report-preview-container">
                            <div class="table-scroll-wrapper">
                                <table class="report-table" id="pedidos-fechas-table">
                                    <thead>
                                        <tr>
                                            <th>ID Pedido</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     
                                    </tbody>
                                </table>


                            </div>


                        </div>

                        <div class="report-actions">
                            <button class="btn-generate-pdf" data-report="pedidos-fechas">
                                <i class="fas fa-file-pdf"></i>
                                Generar PDF
                            </button>
                        </div>
                    </div>
                </div>

               
                <div class="report-card" id="report-pedidos-producto">
                    <div class="report-card-header">
                        <div class="report-card-title">
                            <i class="fas fa-ice-cream"></i>
                            <h3>Reporte de pedidos por producto</h3>
                        </div>
                        <span class="report-card-badge">Análisis de productos</span>
                    </div>
                    <div class="report-card-body">
                        <p class="report-description">Genera un reporte detallado de las ventas por producto en un período de tiempo específico.</p>

                        <div class="report-filters">
                            <div class="filter-group">
                                <label for="producto-fecha-inicio">Fecha inicio:</label>
                                <input type="date" id="producto-fecha-inicio" class="filter-date">
                            </div>
                            <div class="filter-group">
                                <label for="producto-fecha-fin">Fecha fin:</label>
                                <input type="date" id="producto-fecha-fin" class="filter-date">
                            </div>
                            <div class="filter-group">
                                <label for="categoria-producto">Categoría:</label>
                                <select id="categoria-producto" class="filter-select">
                                    <option value="todos">Todos los productos</option>
                                    <option value="helados">Helados</option>
                                    <option value="paletas">Paletas</option>
                                    <option value="postres">Postres helados</option>
                                    <option value="bebidas">Bebidas frías</option>
                                </select>
                            </div>
                        </div>

                        <div class="report-preview-container">

                            <div class="table-scroll-wrapper">
                                <table class="report-table" id="pedidos-producto-table">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th>Unidades vendidas</th>
                                            <th>Total ventas</th>
                                            <th>% del total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     
                                    </tbody>
                                </table>


                            </div>


                        </div>

                        <div class="report-actions">
                            <button class="btn-generate-pdf" data-report="pedidos-producto">
                                <i class="fas fa-file-pdf"></i>
                                Generar PDF
                            </button>
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


    <script type="module" src="../../assets//js//adminV2js//jsBase.js"></script>
    <script src="../../assets/js/adminV2js/sidebar.js"></script>
    
    <script src="../../assets//js//adminV2js//chart.min.js"></script>
    <script src="../../assets/js/adminV2js//perfilAdmin.js"></script>
    <script src="../../assets/js/adminV2js//reportesView.js"></script>
    <script src="../../assets/js/adminV2js//logicaReporte.js"></script>

    <script>
    document.querySelector('#dashboard-link7').classList.add('active');

    </script>

-->

    
    
</body>



</html>