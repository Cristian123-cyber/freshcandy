<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya está logueado, enviar al home
if (!empty($_SESSION['user']) && $_SESSION['user']['logged_in']) {
    header('Location: home.php');
    exit;
}

// Capturamos y luego borramos el flash
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fresh Candy - Acceso</title>
    <link rel="stylesheet" href="../../assets/css//paletaColors.css" />
    <link rel="stylesheet" href="../../assets/css/estilosHeader.css" />
    <link rel="stylesheet" href="../../assets/css/estilosFooter.css" />
    <link rel="stylesheet" href="../../assets/css/estilosLogin2.css" />
    <link rel="stylesheet" href="../../assets/css//estilosHeroSecciones.css" />
    <link rel="stylesheet" href="../../assets/css//waves.css" />
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets//css//adminV2css//animaciones.css">
    <link rel="stylesheet" href="../../assets//css//estilosNotificacion.css">




</head>

<body>

    <?php require_once '../../includes/header.php'; ?>

    <div class="page-hero bg-bosque-encantado">
        <div class="hero-content">
            <h1 class="page-title">Acceso Fresh Candy</h1>
            <p class="page-subtitle">Únete a nuestra comunidad y obtén <span class="persuasive-highlight">15% de descuento en tu primera compra.</span></p>
        </div>
    </div>


    <div class="cont">
        <div class="background-especial"></div>
        <div class="auth-container">

            <div class="auth-box">
                <div class="auth-forms">
                    <div class="candy-floating candy-1"></div>
                    <div class="candy-floating candy-2"></div>
                    <div class="candy-floating candy-3"></div>

                    <div class="auth-tabs">
                        <div class="auth-tab active" id="login-tab">Iniciar Sesión</div>
                        <div class="auth-tab" id="register-tab">Registrarse</div>
                        <div class="tab-indicator login"></div>
                    </div>

                    <div class="auth-form-container">
                        <!-- Formulario de Login -->
                        <form class="auth-form active" id="login-form">
                            <h2 class="form-title">Bienvenido de nuevo</h2>

                            <div class="form-group">
                                <label for="login-email" class="form-label">Correo electrónico</label>
                                <input type="email" id="login-email" class="form-input" placeholder="tu@email.com" required>
                                <i class="fas fa-envelope input-icon"></i>
                                <div class="form-error">Por favor, ingresa un correo válido</div>
                            </div>

                            <div class="form-group">
                                <label for="login-password" class="form-label">Contraseña</label>
                                <input type="password" id="login-password" class="form-input" placeholder="••••••••" required>

                                <div class="form-error">La contraseña es requerida</div>
                            </div>

                            <!-- <div class="form-checkbox">
                                <input type="checkbox" id="remember-me">
                                <label for="remember-me">Recordarme</label>
                            </div> -->

                            <!-- <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a> -->

                            <button type="submit" id="login-btn" class="btn-auth">Iniciar Sesión</button>

                           <!--  <div class="social-login">
                                <div class="social-login-title"><span>O continúa con</span></div>
                                <div class="social-buttons">
                                    <button type="button" class="btn-social google"><i class="fab fa-google"></i></button>
                                    <button type="button" class="btn-social facebook"><i class="fab fa-facebook-f"></i></button>
                                    <button type="button" class="btn-social apple"><i class="fab fa-apple"></i></button>
                                </div>
                            </div> -->

                            <p class="terms-text">
                                Al iniciar sesión, aceptas nuestros <a href="#">Términos de servicio</a> y <a href="#">Política de privacidad</a>
                            </p>
                        </form>

                        <!-- Formulario de Registro -->
                        <form class="auth-form" id="register-form">
                            <h2 class="form-title">Crea tu cuenta</h2>

                            <div class="form-group">
                                <label for="register-name" class="form-label">Nombre completo</label>
                                <input type="text" id="register-name" class="form-input" placeholder="Nombre y apellido" required>
                                <i class="fas fa-user input-icon"></i>
                                <div class="form-error">Por favor, ingresa tu nombre</div>
                            </div>

                            <div class="form-group">
                                <label for="register-email" class="form-label">Correo electrónico</label>
                                <input type="email" id="register-email" class="form-input" placeholder="tu@email.com" required>
                                <i class="fas fa-envelope input-icon"></i>
                                <div class="form-error">Por favor, ingresa un correo válido</div>
                            </div>
                            <div class="form-group">
                                <label for="register-cedula" class="form-label">Cedula</label>
                                <input type="number" id="register-cedula" class="form-input" placeholder="Tu numero de indentificacion" required>
                                <i class="fas fa-envelope input-icon"></i>
                                <div class="form-error">Por favor, ingresa un numero valido</div>
                            </div>

                            <div class="form-group">
                                <label for="register-password" class="form-label">Contraseña</label>
                                <input type="password" id="register-password" class="form-input" placeholder="••••••••" required>

                                <div class="form-error">La contraseña debe tener al menos 8 caracteres</div>
                            </div>

                            <div class="form-group">
                                <label for="register-password-confirm" class="form-label">Confirmar contraseña</label>
                                <input type="password" id="register-password-confirm" class="form-input" placeholder="••••••••" required>

                                <div class="form-error">Las contraseñas no coinciden</div>
                            </div>

                            <div class="form-checkbox">
                                <input type="checkbox" id="accept-terms" required>
                                <label for="accept-terms">Acepto los <a href="#">Términos de servicio</a> y la <a href="#">Política de privacidad</a></label>
                            </div>

                            <button type="submit" id="register-btn" class="btn-auth">Crear cuenta</button>

                            <!-- <div class="social-login">
                                <div class="social-login-title"><span>O regístrate con</span></div>
                                <div class="social-buttons">
                                    <button type="button" class="btn-social google"><i class="fab fa-google"></i></button>
                                    <button type="button" class="btn-social facebook"><i class="fab fa-facebook-f"></i></button>
                                    <button type="button" class="btn-social apple"><i class="fab fa-apple"></i></button>
                                </div>
                            </div> -->
                        </form>
                    </div>
                </div>
                <div class="auth-image">
                    <div class="auth-image-content">
                        <h3>¡Dulce tentación en cada bocado!</h3>
                        <p>Descubre nuestra deliciosa variedad de helados artesanales y dulces exclusivos. Únete a la experiencia Fresh Candy.</p>
                    </div>
                </div>
            </div>



        </div>
        <div class="wave-transition-footer2">
            <svg viewBox="0 0 1440 320" preserveAspectRatio="none">

                <path fill="var(--primary-color)"
                    d="M0,128C300,240 1140,0 1440,160L1440,320L0,320Z">
                </path>
            </svg>

        </div>
    </div>



    <?php require_once '../../includes/footer.php'; ?>
    <?php require_once '../../includes/carrito.php'; ?>

    <script src="../../assets/js/funciones.js"></script>
    <script>
        //Mostrar notificacion de error por redireccion
        document.addEventListener('DOMContentLoaded', () => {

            window.FlashError = <?php echo json_encode($flashError); ?>;
            const msg = window.FlashError;
            if (msg !== null && msg !== undefined && msg !== '') {
                showNotification(msg, 'error');

                // Limpia la variable para que no se reutilice
                delete window.FlashError;
            }
        });
    </script>
    <script src="../../assets/js/scriptBtnDesplegable.js"></script>
    <script type="module" src="../../assets/js/scriptCarrito.js"></script>

    <script type="module" src="../../assets/js//authForm.js">

    </script>
</body>

</html>