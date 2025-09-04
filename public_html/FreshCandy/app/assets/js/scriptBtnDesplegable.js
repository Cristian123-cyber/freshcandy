document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.getElementById("menu-toggle");
  const mobileMenu = document.getElementById("mobile-menu");

  const menuClose = document.getElementById("menu-close");
  const menuLinks = document.querySelectorAll(".mobile-menu-content a");
  const html = document.documentElement;
  const body = document.body;

  // Asegurar que el menú está oculto al cargar la página
  if (mobileMenu) {
    mobileMenu.classList.remove("active");
    mobileMenu.style.display = "none";
  }

  // Función para abrir el menú
  function openMenu() {
    // Primero hacemos el menú visible pero manteniendo su posición fuera de la vista
    mobileMenu.style.display = "flex";

    // Forzamos un repintado
    void mobileMenu.offsetWidth;

    // Ahora activamos la transición
    setTimeout(() => {
      mobileMenu.classList.add("active");

      body.style.overflow = "hidden";
      menuToggle.classList.add("active");

      // Añadir animación a los enlaces
      menuLinks.forEach((link, index) => {
        link.style.transitionDelay = `${0.1 + index * 0.05}s`;
      });
    }, 10);

    // Evitar scroll en el body para pantallas táctiles
    html.style.touchAction = "none";
  }

  // Función para cerrar el menú
  function closeMenu() {
    mobileMenu.classList.remove("active");

    body.style.overflow = "";
    menuToggle.classList.remove("active");

    // Resetear las animaciones de los enlaces
    menuLinks.forEach((link) => {
      link.style.transitionDelay = "0s";
    });

    // Restaurar scroll en el body para pantallas táctiles
    html.style.touchAction = "";

    // Esperar a que termine la animación para ocultar completamente
    setTimeout(() => {
      mobileMenu.style.display = "none";
    }, 300);
  }

  // Evento para el botón hamburguesa
  if (menuToggle) {
    menuToggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      if (mobileMenu.classList.contains("active")) {
        closeMenu();
      } else {
        openMenu();
      }
    });
  }

  // Evento para el botón de cierre
  if (menuClose) {
    menuClose.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      closeMenu();
    });
  }

  // Eventos para los enlaces del menú
  menuLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      const href = this.getAttribute("href");
      closeMenu();
      setTimeout(() => {
        window.location.href = href;
      }, 300);
    });
  });

  // Cerrar menú con la tecla Escape
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && mobileMenu.classList.contains("active")) {
      closeMenu();
    }
  });

  // Prevenir que los clicks dentro del menú cierren el overlay
  if (mobileMenu) {
    mobileMenu.addEventListener("click", function (e) {
      e.stopPropagation();
    });
  }

  // Manejar el cambio de orientación o redimensionamiento
  window.addEventListener("resize", function () {
    // Si el menú está abierto y cambiamos a escritorio, cerrarlo
    if (mobileMenu.classList.contains("active") && window.innerWidth > 768) {
      closeMenu();
    }
  });
});
