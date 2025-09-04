// Variables globales para la paginación y filtros
import { showConfirmation } from "./confirmDialog.js";


let sugerenciasData = [];
let sugerenciasFiltradas = [];
let paginaActual = 1;
let itemsPorPagina = 10;
let totalRegistros = 0;
let filtroEstado = 'todos';
let filtroFecha = 'todas';
let filtroBuscar = '';

function init2() {
  document.getElementById("dashboard-link5").classList.add("active");
  animateElements();
  initializeFilters();
  initializePagination();
  cargarSugerencias();
}

function initializeFilters() {
  const filtersContainer = document.querySelector(".filters-container");
  if (filtersContainer) {
    filtersContainer.style.display = "block";
    filtersContainer.style.visibility = "visible";
    filtersContainer.style.opacity = "1";
  }
  document.getElementById('aplicarFiltros')?.addEventListener('click', () => {
    paginaActual = 1;
    aplicarFiltros();
  });
  document.getElementById('buscar')?.addEventListener('input', () => {
    paginaActual = 1;
    aplicarFiltros();
  });
  document.getElementById('filtroFecha')?.addEventListener('change', () => {
    paginaActual = 1;
    aplicarFiltros();
  });
  document.getElementById('filtroEstado')?.addEventListener('change', () => {
    paginaActual = 1;
    aplicarFiltros();
  });
}

function initializePagination() {
  document.getElementById('pagination-first')?.addEventListener('click', () => irAPagina(1));
  document.getElementById('pagination-prev')?.addEventListener('click', () => irAPagina(paginaActual - 1));
  document.getElementById('pagination-next')?.addEventListener('click', () => irAPagina(paginaActual + 1));
  document.getElementById('pagination-last')?.addEventListener('click', () => {
    const totalPaginas = Math.ceil(totalRegistros / itemsPorPagina);
    irAPagina(totalPaginas);
  });
  document.getElementById('items-per-page')?.addEventListener('change', function() {
    itemsPorPagina = parseInt(this.value);
    paginaActual = 1;
    cargarSugerencias();
  });
}

document.addEventListener("DOMContentLoaded", function () {
  cargarEstadosSugerencias();
  init2();
});

function cargarEstadosSugerencias() {
  fetch('/../../controllers/SugerenciasAjaxController.php?action=getEstados')
    .then(res => res.json())
    .then(json => {
      if (json.success && json.data && Array.isArray(json.data.estados)) {
        const select = document.getElementById('filtroEstado');
        if (!select) return;
        // Elimina todas las opciones excepto la primera ("Todos los estados")
        select.innerHTML = '<option value="todos">Todos los estados</option>';
        json.data.estados.forEach(estado => {
          const option = document.createElement('option');
          option.value = estado.nombre_estado;
          option.textContent = estado.nombre_estado;
          select.appendChild(option);
        });
      }
    });
}

function aplicarFiltros() {
  filtroEstado = document.getElementById('filtroEstado')?.value || 'todos';
  filtroFecha = document.getElementById('filtroFecha')?.value || 'todas';
  filtroBuscar = document.getElementById('buscar')?.value || '';
  cargarSugerencias();
}

function irAPagina(numeroPagina) {
  paginaActual = numeroPagina;
  cargarSugerencias();
}

function cargarSugerencias() {
  // Construir la URL con filtros y paginación
  const params = new URLSearchParams({
    estado: filtroEstado,
    fecha: filtroFecha,
    buscar: filtroBuscar,
    pagina: paginaActual,
    porPagina: itemsPorPagina
  });
  fetch(`/../../controllers/SugerenciasAjaxController.php?action=listarUnicas&${params.toString()}`)
    .then(res => {
      if (!res.ok) throw new Error('Error de red o servidor: ' + res.status);
      return res.json();
    })
    .then(json => {
      if (json.success && json.data && Array.isArray(json.data.sugerencias)) {
        sugerenciasData = json.data.sugerencias;
        totalRegistros = json.data.total || 0;
        renderizarSugerencias(sugerenciasData);
        actualizarPaginacion();
      } else {
        mostrarError('No se pudieron obtener las sugerencias: ' + (json.message || 'Error desconocido'));
      }
    })
    .catch(err => {
      mostrarError('Error al cargar sugerencias: ' + err.message);
    });
}

// Función para formatear la fecha de forma inteligente
function formatearFechaInteligente(fechaStr) {
  const fecha = new Date(fechaStr);
  const ahora = new Date();

  // Comparar si es hoy
  if (
    fecha.getDate() === ahora.getDate() &&
    fecha.getMonth() === ahora.getMonth() &&
    fecha.getFullYear() === ahora.getFullYear()
  ) {
    return `Hoy, ${fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}`;
  }
  // Mismo año
  if (fecha.getFullYear() === ahora.getFullYear()) {
    return `${fecha.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' })}, ${fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}`;
  }
  // Otro año
  return `${fecha.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })}, ${fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}`;
}

function getClass(tipo) {
  if (tipo === 1) return 'product-idea';
  else if (tipo === 2) return 'service';
  else if (tipo === 3) return 'improvement';
  else if (tipo === 4) return 'complaint';
  else return 'otras';
}

function renderizarSugerencias(sugerencias) {
  const contenedor = document.querySelector('.suggestions-list');
  if (!contenedor) return;
  contenedor.innerHTML = '';

  if (!Array.isArray(sugerencias) || !sugerencias.length) {
    contenedor.innerHTML = '<p style="text-align:center;">No hay sugerencias para mostrar.</p>';
    return;
  }

  sugerencias.forEach(sug => {
    const fechaStr = formatearFechaInteligente(sug.fecha);
    let tipoClase = 'otras';
    if (sug.nombre_tipo && sug.nombre_tipo.toLowerCase().includes('producto')) tipoClase = 'product-idea';
    else if (sug.nombre_tipo && sug.nombre_tipo.toLowerCase().includes('mejora')) tipoClase = 'mejora';
    else if (sug.nombre_tipo && sug.nombre_tipo.toLowerCase().includes('experiencia')) tipoClase = 'experiencia';
    contenedor.innerHTML += `
      <div class="suggestion-card" data-id="${sug.id_sugerencia}">
        <div class="suggestion-header">
          <div class="suggestion-user">
            <img src="../../assets//images//user (1).png" alt="User Avatar">
            <div>
              <h4>${sug.nombre_cliente || 'Usuario'}</h4>
              <span class="suggestion-date">${fechaStr}</span>
            </div>
          </div>
          <span class="suggestion-type ${getClass(parseInt(sug.id_tipo))}">${sug.nombre_tipo || ''}</span>
        </div>
        <h5 class="suggestion-title">${sug.titulo_sugerencia}</h5>
        <p class="suggestion-text">${sug.sugerencia_info}</p>
        <div class="suggestion-actions">
          <button class="suggestion-btn mark-read"><i class="fas fa-check"></i> Marcar como revisado</button>
          <button class="suggestion-btn delete" style="background:#e74c3c;color:#fff;" title="Eliminar sugerencia"><i class="fas fa-trash"></i></button>
        </div>
      </div>
    `;
  });
  animarSugerencias();
  if (typeof setupSuggestionButtons === 'function') {
    setupSuggestionButtons();
  }
}

function actualizarPaginacion() {
  const total = totalRegistros;
  const totalPaginas = Math.ceil(total / itemsPorPagina) || 1;
  const inicio = total > 0 ? (paginaActual - 1) * itemsPorPagina + 1 : 0;
  const fin = Math.min(paginaActual * itemsPorPagina, total);
  document.getElementById('pagination-from').textContent = inicio;
  document.getElementById('pagination-to').textContent = fin;
  document.getElementById('pagination-total').textContent = total;
  document.getElementById('pagination-current-page').textContent = paginaActual;
  document.getElementById('pagination-total-pages').textContent = totalPaginas;
  const firstBtn = document.getElementById('pagination-first');
  const prevBtn = document.getElementById('pagination-prev');
  const nextBtn = document.getElementById('pagination-next');
  const lastBtn = document.getElementById('pagination-last');
  if (firstBtn) firstBtn.disabled = paginaActual === 1;
  if (prevBtn) prevBtn.disabled = paginaActual === 1;
  if (nextBtn) nextBtn.disabled = paginaActual === totalPaginas;
  if (lastBtn) lastBtn.disabled = paginaActual === totalPaginas;
}

// Marcar como revisado (AJAX)
document.addEventListener('click', function(e) {
  if (e.target.closest('.suggestion-btn.mark-read')) {
    const card = e.target.closest('.suggestion-card');
    const id = card?.getAttribute('data-id');
    if (id) {
      fetch('/../../controllers/SugerenciasAjaxController.php?action=marcarRevisada', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(id)}`
      })
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          cargarSugerencias();
        } else {
          alert('No se pudo marcar como revisada: ' + (json.message || 'Error desconocido'));
        }
      })
      .catch(() => alert('Error de red al marcar como revisada.'));
    }
  }
});

// Event listener global para el botón eliminar (siempre funciona aunque las cards sean dinámicas)
document.addEventListener('click', function(e) {
  if (e.target.closest('.suggestion-btn.delete')) {
    const card = e.target.closest('.suggestion-card');
    const id = card?.getAttribute('data-id');
    if (id) {
      eliminarSugerencia(id);
    }
  }
});

// Apply animations to cards on load
const animateElements = () => {
  const statCards = document.querySelectorAll(".stat-card");
  statCards.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add("fade-in");
    }, 100 * index);
  });

  const filtersContainer = document.querySelector(".filters-wrapper");
  if (filtersContainer) {
    setTimeout(() => {
      filtersContainer.classList.add("slide-in");
    }, 300);
  }
};

function mostrarLoading() {
  const contenedor = obtenerContenedorSugerencias();
  if (contenedor) {
    contenedor.innerHTML = `
      <div style="text-align: center; padding: 2em; color: #666;">
        <i class="fas fa-spinner fa-spin" style="font-size: 2em; margin-bottom: 1em;"></i>
        <p>Cargando sugerencias...</p>
      </div>
    `;
  }
}

function mostrarError(mensaje) {
  const contenedor = obtenerContenedorSugerencias();
  if (contenedor) {
    contenedor.innerHTML = `
      <div class="error-message" style="color: #c00; text-align: center; margin: 2em 0;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 0.5em;"></i>
        ${mensaje}
      </div>
    `;
  }
}

function obtenerContenedorSugerencias() {
  let contenedor = document.querySelector('.suggestions-list');
  
  if (!contenedor) {
    const filtersSection = document.querySelector('.data-tables-section');
    if (filtersSection) {
      contenedor = document.createElement('div');
      contenedor.className = 'suggestions-list';
      filtersSection.insertAdjacentElement('afterend', contenedor);
      
      // Ocultar cards estáticas
      const staticCards = document.querySelectorAll('.suggestion-card');
      staticCards.forEach(card => {
        card.style.display = 'none';
      });
    }
  }
  
  return contenedor;
}

function animarSugerencias() {
  const cards = document.querySelectorAll('.suggestion-card');
  cards.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add('slide-in');
      card.style.opacity = '1';
      card.style.transform = 'none';
    }, 100 * index);
  });
}

// Actualizar estadísticas en las cards superiores
function actualizarEstadisticas() {
  const total = sugerenciasData.length;
  const pendientes = sugerenciasData.filter(s => !s.estado || s.estado === 'pendiente').length;
  const revisadas = sugerenciasData.filter(s => s.estado === 'revisado').length;

  // Actualizar cards de estadísticas
  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach(card => {
    const title = card.querySelector('h3')?.textContent;
    const valueElement = card.querySelector('.stat-value');
    
    if (title && valueElement) {
      if (title.includes('Total')) valueElement.textContent = total;
      else if (title.includes('Pendientes')) valueElement.textContent = pendientes;
      else if (title.includes('Revisadas')) valueElement.textContent = revisadas;
    }
  });
}

// Event listeners para acciones de las sugerencias
function añadirEventListeners() {
  document.querySelectorAll('.suggestion-btn.mark-read').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      marcarComoRevisado(id);
    });
  });

  document.querySelectorAll('.suggestion-btn.delete').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      eliminarSugerencia(id);
    });
  });
}

// Función para marcar como revisado
function marcarComoRevisado(id) {
  if (confirm('¿Estás seguro de que quieres marcar esta sugerencia como revisada?')) {
    // Aquí harías la petición al backend
    console.log('Marcando como revisado:', id);
    // Ejemplo de petición:
    // fetch(`../../controllers/SugerenciasController.php?action=marcar_revisado&id=${id}`)
    //   .then(() => cargarSugerencias()); // Recargar datos
  }
}

// Función para eliminar sugerencia
function eliminarSugerencia(id) {


  showConfirmation({
        title: `Eliminar Sugerencia"`,
        message: `¿Estás seguro de eliminar esta sugerencia permanentemente? Todos los datos asociados se perderán.`,
        type: "delete",
        confirmText: "Eliminar",
        callback: async function () {
          try {
            await deleteAction(id);
          } catch (error) {
            console.error("Error en la confirmación:", error);
            showError(`Error al eliminar la sugerencia: ${error.message}`);
          }
        },
      });
  
}


async function deleteAction(id){

  // Animación de la card antes de eliminar
      const card = document.querySelector(`.suggestion-card[data-id='${id}']`);
      if (card) {
        card.style.transition = 'all 0.5s cubic-bezier(.68,-0.55,.27,1.55)';
        card.style.transform = 'translateX(100%) scale(0.8)';
        card.style.opacity = '0';
      }
      setTimeout(() => {
        fetch('/../../controllers/SugerenciasAjaxController.php?action=marcarEliminada', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `id=${encodeURIComponent(id)}`
        })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            showNotification("Sugerencia eliminada con exito !", "success");
          
            cargarSugerencias();
          } else {
            showNotification("No se pudo eliminar la sugerencia !", "error");

            
          }
        })
        .catch(() => showNotification("Error de red al eliminar la sugerencia !", "error"));
      }, 400);

}