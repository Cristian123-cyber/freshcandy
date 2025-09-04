<?php
require_once '../../controllers/AuthMiddleware.php';
AuthMiddleware::protectUser();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - FreshCandy</title>
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/paletaColors.css">
    <link rel="stylesheet" href="../../assets/css/perfil.css">
    <link rel="stylesheet" href="../../assets/css/estilosHeader.css">
    <link rel="stylesheet" href="../../assets/css/estilosNotificacion.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap">
</head>

<body>
    <?php require_once '../../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="profile-hero">
        <div class="hero-overlay"></div>
        <div class="floating-bubbles">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
        </div>
        <div class="hero-content">
            <div class="hero-left">
                <div class="avatar-circle">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="hero-right">
                <div class="hero-main">
                    <h1 class="profile-username" id="nombre-display">Cargando...</h1>
                    <div class="hero-stats" id="hero-stats">
                        <!-- Las estadísticas se renderizarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-info-card">
                <div class="info-header">
                    <h3><i class="fas fa-info-circle"></i> Información</h3>
                </div>
                <div class="info-content">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-id-card"></i> Cédula</div>
                        <div class="info-value" id="cedula-display">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                        <div class="info-value" id="email-display">-</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-phone"></i> Teléfono</div>
                        <div class="info-value" id="telefono-display">-</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-main">
            <div class="profile-tabs">
                <button class="tab-button active" data-tab="info-tab"><i class="fas fa-user-edit"></i> Editar Perfil</button>
                <button class="tab-button" data-tab="password-tab"><i class="fas fa-lock"></i> Seguridad</button>
            </div>

            <div class="tab-content active" id="info-tab">
                <form id="personal-info-form" class="profile-edit-form">
                    <div class="form-section">
                        <h3><i class="fas fa-address-card"></i> Información de contacto</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="telefono"><i class="fas fa-mobile-alt"></i> Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" placeholder="Ej: 612345678">
                                <span class="form-error" id="telefono-error">Introduce un teléfono válido (9 dígitos)</span>
                            </div>
                            <div class="form-group">
                                <label for="direccion"><i class="fas fa-map-marked-alt"></i> Dirección</label>
                                <textarea id="direccion" name="direccion" placeholder="Tu dirección completa para envíos"></textarea>
                                <span class="form-error" id="direccion-error">Este campo es obligatorio</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-content" id="password-tab">
                <form id="password-form" class="profile-edit-form">
                    <div class="form-section">
                        <h3><i class="fas fa-key"></i> Cambiar contraseña</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="current-password"><i class="fas fa-lock"></i> Contraseña actual</label>
                                <div class="password-input-container">
                                    <input type="password" id="current-password" name="current-password" placeholder="Ingresa tu contraseña actual">
                                    <i class="fas fa-eye toggle-password" data-target="current-password"></i>
                                </div>
                                <span class="form-error" id="current-password-error">Introduce tu contraseña actual</span>
                            </div>
                            <div class="form-group">
                                <label for="new-password"><i class="fas fa-lock-open"></i> Nueva contraseña</label>
                                <div class="password-input-container">
                                    <input type="password" id="new-password" name="new-password" placeholder="Crea una contraseña segura">
                                    <i class="fas fa-eye toggle-password" data-target="new-password"></i>
                                </div>
                                <span class="form-error" id="new-password-error">Mínimo 8 caracteres con mayúsculas, números y símbolos</span>
                            </div>
                            <div class="form-group">
                                <label for="confirm-password"><i class="fas fa-check-circle"></i> Confirmar contraseña</label>
                                <div class="password-input-container">
                                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Repite tu nueva contraseña">
                                    <i class="fas fa-eye toggle-password" data-target="confirm-password"></i>
                                </div>
                                <span class="form-error" id="confirm-password-error">Las contraseñas no coinciden</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="profile-actions">
                <button type="button" class="button-secondary" id="logout-button">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
                <button type="button" class="button-primary" id="save-button">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <?php require_once '../../includes/carrito.php'; ?>

    <script src="../../assets/js/funciones.js"></script>
    <script src="../../assets/js/scriptBtnDesplegable.js"></script>
    <script type="module" src="../../assets/js/scriptCarrito.js"></script>
    <script type="module" src="../../assets/js/perfil.js"></script>
</body>

</html>