<!-- Sidebar Navigation -->
<aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="../../assets//images//logoFresh.png" alt="Fresh Candy Logo" class="sidebar-logo">
            </div>
            <button id="close-sidebar" class="close-sidebar-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="admin-profile">
            <div class="admin-avatar">
                <img src="../../assets//images//manager.png" alt="Admin Avatar">
            </div>
            <div class="admin-info">
                <h4>Admin</h4>
                <p>Administrador</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item">
                    <a href="adminView.php" class="nav-link" id="dashboard-link1">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestionProductos.php" class="nav-link" id="dashboard-link2">
                        <i class="fas fa-ice-cream"></i>
                        <span>Productos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="inventario.php" class="nav-link" id="dashboard-link3">
                        <i class="fas fa-boxes"></i>
                        <span>Inventario</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestionPedidos.php" class="nav-link" id="dashboard-link4">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Pedidos</span>
                        <div class="notification-badge"></div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="sugerenciasEnv.php" class="nav-link" id="dashboard-link5">
                        <i class="fas fa-lightbulb"></i>
                        <span>Sugerencias</span>
                        <div class="notification-badge"></div>
                    </a>
                </li>
            
                
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a id="logout-btn-admin" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>