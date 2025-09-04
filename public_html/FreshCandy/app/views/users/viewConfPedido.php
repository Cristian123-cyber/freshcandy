<?php
//Middleware basico para proteger la página
//Si no es usuario, lo redirigimos a la página de login
require_once '../../controllers/AuthMiddleware.php';
AuthMiddleware::protectUser();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido - Heladería</title>

    <link rel="stylesheet" href="../../assets//css//paletaColors.css">
    <link rel="stylesheet" href="../../assets//css//estilosViewConfPedido.css">
    <link rel="stylesheet" href="../../assets//css//estilosNotificacion.css">
    <link rel="stylesheet" href="../../assets//css//estilosHeroSecciones.css">
    <link rel="stylesheet" href="../../assets//css//estilosFooter.css">
    <link rel="stylesheet" href="../../assets//css//waves.css">
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">

</head>

<body>
    <header class="checkout-header">
        <div class="logo-container">
            <img src="../../assets//images//logoFresh.png" alt="Logo FreshCandy" class="logo">

        </div>

    </header>
    <!-- Hero Header simple y reutilizable -->
    <div class="page-hero bg-bosque-encantado">
        <div class="hero-content">
            <h1 class="page-title">Finalizar Compra</h1>
            <p class="page-subtitle">Estás a solo unos pasos de disfrutar tus productos. <span class="persuasive-highlight">¡Entrega en tiempo récord!</span></p>
        </div>
    </div>

    <div class="cont">
        <div class="background-especial"></div>
        <div class="container">


            <!-- HTML del indicador de pasos -->
            <div class="steps-container">
                <div class="step-item completed">
                    <div class="step-icon-container">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="step-label">Carrito</div>
                </div>
                <div class="step-item active">
                    <div class="step-icon-container">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="step-label">Información</div>
                </div>
                <div class="step-item">
                    <div class="step-icon-container">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="step-label">Confirmación</div>
                </div>
            </div>

            <div class="checkout-container">
                <div class="checkout-left">
                    <div class="card" id="delivery-methods-container">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <h2 class="card-title">Método de entrega</h2>
                                <p class="card-subtitle">Selecciona cómo quieres recibir tu pedido</p>
                            </div>
                        </div>

                        <!-- Aqui se renderizan los metodos de entrega -->


                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h2 class="card-title">Información de contacto</h2>
                                <p class="card-subtitle">Datos para contactarte y entregar tu pedido</p>
                            </div>
                        </div>

                        <form id="customer-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Nombre completo</label>
                                    <input type="text" id="name" placeholder="Tu nombre completo" required>
                                    <div class="error-message" id="name-error">Este campo es obligatorio</div>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Teléfono</label>
                                    <input type="tel" id="phone" placeholder="Tu número de teléfono" required>
                                    <div class="error-message" id="phone-error">Ingresa un teléfono válido</div>
                                </div>
                                <div class="form-group">
                                    <label for="cedula">Cedula</label>
                                    <input type="number" id="cedula" placeholder="Tu numero de identificacion" required>
                                    <div class="error-message" id="cedula-error">Ingresa un numero de identificacion válido</div>
                                </div>
                            </div>

                            <div class="form-group delivery-field">
                                <label for="address">Dirección de entrega</label>
                                <input type="text" id="address" placeholder="Dirección completa para la entrega" required>
                                <div class="error-message" id="address-error">Este campo es obligatorio</div>
                            </div>

                            <div class="form-row delivery-field">
                                <div class="form-group">
                                    <label for="city">Ciudad</label>
                                    <input type="text" id="city" placeholder="Tu ciudad" required>
                                    <div class="error-message" id="city-error">Este campo es obligatorio</div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="notes">Notas adicionales (opcional)</label>
                                <textarea id="notes" rows="3" placeholder="Instrucciones especiales, preferencias, etc."></textarea>
                            </div>
                        </form>
                    </div>

                    <div class="card" id="payment-methods-cont">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div>
                                <h2 class="card-title">Método de pago</h2>
                                <p class="card-subtitle">Selecciona cómo quieres pagar tu pedido</p>
                            </div>
                        </div>

                        <div class="payment-methods-wrapper" id="payment-methods-container">
                            <!-- Aqui se renderizan los metodos de pago -->

                           
                        </div>






                    </div>
                </div>

                <div class="order-summary card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Resumen del pedido</h2>
                            <p class="card-subtitle"><span id="items-count">6</span> productos en tu pedido</p>
                        </div>
                    </div>

                    <div class="products-scroll" id="products-container">
                        <!-- Los productos se cargarán dinámicamente desde JS -->
                    </div>

                    <div class="divider"></div>

                    <div class="promo-code">
                        <input type="text" placeholder="Código promocional">
                        <button type="button">Aplicar</button>
                    </div>

                    <div class="divider"></div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-row delivery-cost">
                        <span>Costo de envío</span>
                        <span id="delivery-cost">$2.00</span>
                    </div>

                    <div class="summary-row savings-row" style="display: none;">
                        <span>Descuento</span>
                        <span class="savings" id="discount">-$0.00</span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span id="total">$0.00</span>
                    </div>

                    <div class="time-estimation">
                        <i class="far fa-clock"></i> Tiempo estimado de entrega: <span class="time-value">30-45 minutos</span>
                    </div>

                    <button id="confirm-order" class="button">
                        <i class="fas fa-check-circle"></i> Confirmar pedido
                    </button>
                    <button id="continue-shopping" class="button secondary">
                        <i class="fas fa-store"></i> Seguir comprando
                    </button>
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




    <script src="../..//assets//js//funciones.js"></script>

    <script type="module" src="../../assets/js/confirmPedido.js"></script>
</body>

</html>