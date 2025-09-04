<?php
//Middleware basico para proteger la página
//Si no es usuario, lo redirigimos a la página de login
require_once '../../controllers/AuthMiddleware.php';
AuthMiddleware::protectUser();


if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fresh Candy - Sugerencias</title>
  <link rel="stylesheet" href="../../assets/css//paletaColors.css" />
  <link rel="stylesheet" href="../../assets/css//estilosSugerencias.css" />
  <link rel="stylesheet" href="../../assets/css/estilosFooter.css">
  <link rel="stylesheet" href="../../assets/css/estilosNotificacion.css">
  <link rel="stylesheet" href="../../assets/css//estilosHeroSecciones.css">
  <link rel="stylesheet" href="../../assets/css/estilosHeader.css" />
  <link rel="stylesheet" href="../../assets/css//waves.css">
  <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">


</head>

<body>
  <?php require_once '../../includes/header.php'; ?>

  <!-- Hero Header simple y reutilizable -->
  <div class="page-hero bg-bosque-encantado">
    <div class="hero-content">
      <h1 class="page-title">Envianos tu opinión</h1>
      <p class="page-subtitle">Para Fresh Candy tu opinión es importante <span class="persuasive-highlight">Envía la tuya para mejorar nuestra calidad</span></p>
    </div>
  </div>



  <div class="cont">

    <div class="background-especial"></div>


    <section class="contenedor-principal">
      <!-- Background especial implementado aquí -->

      <!-- Burbujas decorativas -->
      <div class="burbuja"></div>
      <div class="burbuja"></div>

      <!-- Formulario de sugerencias -->
      <div class="contenedor-sugerencias">
        <div class="form-header">
          <h2>¡Tu opinión nos importa!</h2>
          <p class="form-description">
            ¿Tienes ideas para mejorar nuestros helados o servicio? Compártelas aquí.
            <br>Tus comentarios son confidenciales y solo serán vistos por el administrador.
          </p>
        </div>

        <form id="formulario-sugerencias" action="../../controllers/SugerenciasController.php" method="post">
          <div class="form-grid">
            <div class="grupo-formulario">
              <label for="titulo">
                <i class="fas fa-heading"></i>
                Título de tu sugerencia
              </label>
              <input
                type="text"
                id="titulo"
                name="titulo"
                placeholder="Ej. Nueva idea de helado"
                required />
              <span class="form-error" id="titulo-error">Este campo es obligatorio</span>
            </div>
          </div>

          <div class="grupo-formulario">
            <label>
              <i class="fas fa-tags"></i>
              Tipo de sugerencia
            </label>
            <div class="radio-group">
              <!-- Los tipos de sugerencia se cargarán dinámicamente desde la base de datos -->
            </div>
            <span class="form-error" id="tipo-sugerencia-error">Debes seleccionar un tipo de sugerencia</span>
          </div>

          <div class="grupo-formulario">
            <label for="mensaje">
              <i class="fas fa-comment-alt"></i>
              Tu sugerencia
            </label>
            <textarea
              id="mensaje"
              name="mensaje"
              placeholder="Escribe aquí tu sugerencia..."
              required></textarea>
            <span class="form-error" id="mensaje-error">Este campo es obligatorio</span>
          </div>

          <div class="form-footer">
            <button type="submit" class="boton" id="btn-enviar-sugerencia">
              <i class="fas fa-paper-plane"></i>
              Enviar sugerencia
            </button>
          </div>
        </form>
      </div>
    </section>
    <div class="wave-transition-footer2">
      <svg viewBox="0 0 1440 320" preserveAspectRatio="none">

        <path fill="var(--primary-color)"
          d="M0,128C300,240 1140,0 1440,160L1440,320L0,320Z">
        </path>
      </svg>

    </div>



  </div>


  <!-- Panel lateral del carrito -->
  <?php require_once '../../includes/footer.php'; ?>
  <?php require_once '../../includes/carrito.php'; ?>

  <script src="../../assets/js/funciones.js"></script>
  <script src="../../assets/js/scriptBtnDesplegable.js"></script>
  <script type="module" src="../../assets/js/scriptCarrito.js"></script>
  <script type="module" src="../../assets/js/scriptSugerencias.js"></script>
</body>

</html>