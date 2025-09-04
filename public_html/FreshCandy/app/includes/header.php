<?php 
    $isLoggedIn = !empty($_SESSION['user']) && $_SESSION['user']['logged_in'];
?>

<header>
    <nav class="navbar">
        <div class="nav-logo">
            <a href="#">
                <img src="../../assets/images/logoFresh.png" alt="Logo FreshCandy" />
            </a>
        </div>

        <div class="nav-links" id="nav-links">
            <a href="home.php">Home</a>
            <a href="viewSugerencias.php">Sugerencias</a>
            
            <?php if (!$isLoggedIn): ?>
                    <a href="authForm3.php">Login</a>
            <?php else: ?>
                <a href="perfil.php"><i class="fas fa-user-circle" style="font-size: 1.5rem;"></i> </a>
            <?php endif; ?>
        </div>

        <div class="nav-acciones">
            <div id="carrito-btn" class="carrito-icono">
                <i class="fas fa-shopping-cart"></i>
                <span class="contador-carrito"></span>
            </div>
            <div class="hamburguesa" id="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->

    <!-- Mobile Menu Panel -->

    <div class="mobile-menu" id="mobile-menu" style="display: none;">
        <div class="mobile-menu-header">
            <h3>Menú</h3>
            <button class="mobile-menu-close" id="menu-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-menu-content">
            <a href="home.php"><i class="fas fa-home"></i> Home</a>
            <a href="viewSugerencias.php"><i class="fas fa-comment-alt"></i> Sugerencias</a>
            

            <?php if (!$isLoggedIn): ?>
                <a href="authForm3.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php else: ?>
                <a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a>
            <?php endif; ?>
            
            
        </div>
    </div>
</header>