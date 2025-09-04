// sidebar.js - Módulo para la funcionalidad del sidebar

// Función autoejecutada para encapsular la funcionalidad
(function () {
  // Variables para los elementos del sidebar
  let menuToggle;
  let sidebar;
  let closeSidebarBtn;

  // Función para inicializar los elementos del sidebar
  function initSidebarElements() {
    menuToggle = document.getElementById("menu-toggle");
    sidebar = document.getElementById("sidebar");
    closeSidebarBtn = document.getElementById("close-sidebar");

    // Asegurarnos de que los elementos existen
    if (!sidebar) {
      console.warn("El elemento #sidebar no fue encontrado");
      return false;
    }

    return true;
  }

  // Función para alternar la visibilidad del sidebar
  function toggleSidebar(event) {
    if (event) {
      event.preventDefault();
    }
    sidebar.classList.toggle("active");
  }

  // Función para actualizar los badges de la barra lateral con datos reales
  function updateSidebarBadges() {
    // Pedidos
    fetch("/../../controllers/StatsController.php?action=getStatsForPedidos", { method: "POST" })
      .then((res) => res.json())
      .then((data) => {
        const pedidosPendientes = data.data && data.data.pedidos_pendientes ? data.data.pedidos_pendientes : 0;
        const badgePedidos = document.querySelector('#dashboard-link4 .notification-badge');
        if (badgePedidos) badgePedidos.textContent = pedidosPendientes;
      });
    // Sugerencias
    fetch("/../../controllers/StatsController.php?action=getStatsForSugerencias", { method: "POST" })
      .then((res) => res.json())
      .then((data) => {
        const sugerenciasPendientes = data.data && data.data.sugerencias_pendientes ? data.data.sugerencias_pendientes : 0;
        const badgeSugerencias = document.querySelector('#dashboard-link5 .notification-badge');
        if (badgeSugerencias) badgeSugerencias.textContent = sugerenciasPendientes;
      });
  }

  // Función para asignar event listeners
  function setupEventListeners() {
    if (menuToggle) {
      // Usamos una nueva función para asegurarnos que se pueda eliminar el listener si es necesario
      menuToggle.addEventListener("click", toggleSidebar);
    }

    if (closeSidebarBtn) {
      closeSidebarBtn.addEventListener("click", toggleSidebar);
    }
  }

  // Funcionalidad para cerrar el sidebar cuando se hace clic fuera
  function setupOutsideClickHandler() {
    document.addEventListener("click", function (event) {
      // Solo aplicamos esta lógica en viewport móvil
      if (window.innerWidth <= 992) {
        // Verificamos si el sidebar está activo
        if (!sidebar.classList.contains("active")) return;

        // Verificamos si el clic fue dentro del sidebar o en el botón del menú
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnMenuToggle =
          menuToggle && menuToggle.contains(event.target);

        if (!isClickInsideSidebar && !isClickOnMenuToggle) {
          sidebar.classList.remove("active");
        }
      }
    });
  }

  
  

 
  

  // Función de inicialización principal
  function init() {
    // Solo continuamos si los elementos se inicializaron correctamente
    if (!initSidebarElements()) return;

    setupEventListeners();
    setupOutsideClickHandler();
    updateSidebarBadges();

    
    
  }

  // Ejecutamos la inicialización cuando el DOM esté listo
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    // Si el DOM ya está cargado (posible si el script se carga al final)
    init();
  }

  // También inicializamos cuando la ventana se carga completamente
  // Esto ayuda con problemas en DevTools al cambiar a modo responsive
  window.addEventListener("load", init);
})();
