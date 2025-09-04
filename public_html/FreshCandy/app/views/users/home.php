<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fresh Candy - Helados Artesanales</title>
    <link rel="stylesheet" href="../../assets/css//paletaColors.css">

    <link rel="stylesheet" href="../../assets/css/estilosHome2.css">
    <link rel="stylesheet" href="../../assets/css//estilosProds.css">

    <link rel="stylesheet" href="../../assets/css/estilosFooter.css">
    <link rel="stylesheet" href="../../assets/css//estilosNotificacion.css">
    <link rel="stylesheet" href="../../assets/css//waves.css">
    <link rel="stylesheet" href="../../assets/css/estilosHeader.css">


    <!-- Usa siempre una versión actual -->
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.7.2-web/css/all.min.css">





</head>

<body>
    <!-- La barra de navegación se mantiene exactamente igual -->

    <?php require_once '../../includes/header.php'; ?>


    <!-- Hero Section HTML Mejorado -->
    <section class="hero">
        <div class="decorative-circles">
            <div class="circle c1"></div>
            <div class="circle c2"></div>
            <div class="circle c3"></div>
            <div class="circle c4"></div>
            <div class="circle c5"></div>
        </div>

        <div class="shine-effect"></div>
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <img src="../../assets/images/logoFresh.png" alt="Fresh Candy Logo" class="hero-logo">
            <h1>Bienvenido al paraíso de los helados</h1>
            <p>Descubre sabores únicos, ingredientes frescos y la magia artesanal que solo Fresh Candy puede ofrecer.</p>


            <div class="hero-buttons">
                <a href="#seccionProductos" class="hero-btn primary-btn">Ver helados</a>
            </div>
            <div class="scroll-down">
                <a href="#seccionProductos" aria-label="Scroll to products">
                    <span class="scroll-icon"></span>
                </a>
            </div>
        </div>
    </section>



    <!-- seccion productos-->
    <section class="seccion-productos" id="seccionProductos">
        <!-- Fondo y elementos decorativos -->
        <div class="productos-background"></div>
        <div class="deco-circle deco-circle-1"></div>
        <div class="deco-circle deco-circle-2"></div>

        <!-- Encabezado mejorado -->
        <div class="encabezado-productos">
            <div class="titulo-wrapper">
                <h2>Delicias Heladas</h2>
                <div class="titulo-shadow">Delicias Heladas</div>
            </div>
            <div class="separador">
                <div class="separador-line"></div>
                <div class="separador-icon"><i class="fas fa-ice-cream"></i></div>
                <div class="separador-line"></div>
            </div>
            <p>
                Descubre nuestras creaciones heladas elaboradas con ingredientes frescos y naturales.
                Cada sabor cuenta una historia y cada bocado es una experiencia inolvidable.
            </p>

            <!-- Características destacadas -->
            <div class="destacados">
                <div class="destacado-item">
                    <i class="fas fa-leaf"></i>
                    <span>100% Natural</span>
                </div>
                <div class="destacado-item">
                    <i class="fas fa-medal"></i>
                    <span>Calidad Premium</span>
                </div>
                <div class="destacado-item">
                    <i class="fas fa-store"></i>
                    <span>Producción Artesanal</span>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="productos-container">
            <div class="productos" id="productosContainer">
                <!-- Aqui se renderizan los productos -->
            </div>
        </div>



        <!-- Carrusel de imágenes con mensajes -->
        <section class="carrusel-container carrusel-animado">
            <div class="carrusel-slider">
                <!-- Slide 1 -->
                <div class="carrusel-slide">
                    <div class="carrusel-img-wrapper">
                        <img src="../../assets/images//Costco-Food-Court-Ice-Cream-Sundae-Chocolate-and-Strwberry-1024x689.jpg" alt="Helados artesanales premium" />
                    </div>
                    <div class="carrusel-content">
                        <h2>Pasión por lo <span>Artesanal</span></h2>
                        <p>Desde el corazón de nuestra cocina, creamos helados que cuentan historias con cada cucharada.</p>
                        <a href="#seccionProductos" class="carrusel-btn">Descubre nuestros sabores</a>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carrusel-slide">
                    <div class="carrusel-img-wrapper">
                        <img src="../../assets/images/AA1EE0qc.jpg" alt="Elaboración artesanal" />
                    </div>
                    <div class="carrusel-content">
                        <h2>Frescura pensada <span>para Ti</span></h2>
                        <p>Cuidamos cada detalle para ofrecerte productos que no solo saben bien, sino que te hacen sentir bien.</p>
                        <a href="viewSugerencias.php" class="carrusel-btn">Envíanos tu opinión</a>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carrusel-slide">
                    <div class="carrusel-img-wrapper">
                        <img src="../../assets/images/AA1EE0qc.jpg" alt="Eventos especiales" />
                    </div>
                    <div class="carrusel-content">
                        <h2>Una Experiencia <span>Irresistible</span></h2>
                        <p>Sabores que despiertan emociones. Vive la magia de Fresh Candy en cada visita.</p>
                        <a href="#seccionProductos" class="carrusel-btn">Conócenos</a>
                    </div>
                </div>
            </div>

            <!-- Controles del carrusel -->
            <div class="carrusel-controls">
                <button class="carrusel-prev" aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>
                <div class="carrusel-indicators">
                    <span class="indicator active" data-slide="0"></span>
                    <span class="indicator" data-slide="1"></span>
                    <span class="indicator" data-slide="2"></span>
                </div>
                <button class="carrusel-next" aria-label="Siguiente"><i class="fas fa-chevron-right"></i></button>
            </div>
        </section>

        <div class="wave-transition">
            <svg viewBox="0 0 1440 320" preserveAspectRatio="none">

                <path fill="#ffffff"
                    d="M0,128C300,240 1140,0 1440,160L1440,320L0,320Z">
                </path>
            </svg>

        </div>



    </section>







    <!-- Características -->

    <section class="caracteristicas">

        <div class="caracteristica-wrapper">
            <div class="caracteristica bg1 animada">
                <div class="icon-container">
                    <i class="icon color1 fas fa-leaf"></i>
                </div>
                <h3>Ingredientes naturales</h3>
                <p>Seleccionamos los mejores ingredientes frescos y orgánicos para crear helados artesanales que deleitan todos tus sentidos.</p>
            </div>
        </div>


        <div class="caracteristica-wrapper">

            <div class="caracteristica bg2 animada">
                <div class="icon-container">
                    <i class="icon color2 fas fa-recycle"></i>
                </div>
                <h3>Compromiso ambiental</h3>
                <p>Empaques biodegradables y procesos sostenibles para cuidar el planeta mientras disfrutas tu helado favorito.</p>
            </div>
        </div>



        <div class="caracteristica-wrapper">

            <div class="caracteristica bg3 animada">
                <div class="icon-container">
                    <i class="icon color3 fa-solid fa-hand-holding-heart"></i>
                </div>
                <h3>Hecho con amor</h3>
                <p>Cada creación es elaborada artesanalmente con pasión y dedicación por nuestros maestros heladeros con décadas de experiencia.</p>
            </div>
        </div>
    </section>













    <div class="wave-transition-footer">
        <svg viewBox="0 0 1440 320" preserveAspectRatio="none">

            <path fill="var(--primary-color)"
                d="M0,128C300,240 1140,0 1440,160L1440,320L0,320Z">
            </path>
        </svg>

    </div>



    <?php require_once '../../includes/footer.php'; ?>


    <?php require_once '../../includes/carrito.php'; ?>








    <!-- El script de la barra de navegación se mantiene intacto -->
    <script src="../../assets/js/funciones.js"></script>
    <script src="../../assets/js//home.js"></script>
    <script type="module" src="../../assets/js//addCarrito.js"></script>
    <script type="module" src="../../assets/js/scriptCarrito.js"></script>
    <script src="../../assets/js//carrusel.js"></script>
    <script src="../../assets/js//carruselAnimado.js"></script>
    <script src="../../assets/js/scriptBtnDesplegable.js"></script>
</body>

</html>