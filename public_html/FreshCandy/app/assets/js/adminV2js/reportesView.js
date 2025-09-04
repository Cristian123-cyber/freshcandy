

/**
 * reportes.js - Funcionalidades JavaScript para la sección de reportes
 * Fresh Candy - Panel de Administración
 * 
 * Este archivo maneja la interacción del usuario con los formularios de reportes,
 * validaciones, previsualizaciones y eventos de los botones de generación de PDFs.
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    initDatePickers();
    initCharts();
    attachEventListeners();
    setDefaultDates();
});

/**
 * Inicializa los selectores de fecha con valores predeterminados
 */
function initDatePickers() {
    // Obtener todos los inputs de tipo fecha
    const dateInputs = document.querySelectorAll('.filter-date');
    
    // Establecer el atributo max a la fecha actual para evitar fechas futuras
    const today = new Date().toISOString().split('T')[0];
    dateInputs.forEach(input => {
        input.setAttribute('max', today);
    });
}

/**
 * Establece fechas predeterminadas (mes actual)
 */
function setDefaultDates() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    const firstDayFormatted = firstDay.toISOString().split('T')[0];
    const todayFormatted = today.toISOString().split('T')[0];
    
    // Establecer fechas para el reporte de pedidos por fechas
    document.getElementById('fecha-inicio').value = firstDayFormatted;
    document.getElementById('fecha-fin').value = todayFormatted;
    
    // Establecer fechas para el reporte de pedidos por producto
    document.getElementById('producto-fecha-inicio').value = firstDayFormatted;
    document.getElementById('producto-fecha-fin').value = todayFormatted;
}

/**
 * Inicializa los gráficos de ejemplo para los reportes
 */
function initCharts() {
    // Verificar si existe el elemento canvas para el gráfico
    const chartCanvas = document.getElementById('productos-chart');
    if (!chartCanvas) return;
    
    // Si se desea implementar gráficos reales, aquí se puede usar una librería como Chart.js
    // Por ahora solo mostramos un mensaje de placeholder
    const ctx = chartCanvas.getContext('2d');
    ctx.font = '14px Arial';
    ctx.fillStyle = '#999';
    ctx.textAlign = 'center';
    ctx.fillText('Vista previa del gráfico - Implementar con Chart.js', chartCanvas.width / 2, chartCanvas.height / 2);
}

/**
 * Adjunta los event listeners a los elementos interactivos
 */
function attachEventListeners() {
    
    
    // Event listeners para cambios en los filtros para actualizar la vista previa
    const filterInputs = document.querySelectorAll('.filter-select, .filter-date');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            const reportCard = findClosestParent(this, '.report-card');
            if (reportCard) {
                updatePreview(reportCard.id);
            }
        });
    });
}

/**
 * Maneja el evento de clic en el botón de generación de PDF
 * @param {Event} event - El evento de clic
 */
function handlePdfGeneration(event) {
    const button = event.currentTarget;
    const reportType = button.getAttribute('data-report');
    
    // Mostrar un estado de carga
    showLoading(button);
    
    // Validar los filtros antes de generar el PDF
    if (validateFilters(reportType)) {
        // Simular una petición asíncrona
        setTimeout(() => {
            // Aquí solo simulamos la generación del PDF
            // En una implementación real, se enviaría una solicitud al backend
            showFeedback(reportType, 'success', 'El reporte se ha generado correctamente.');
            hideLoading(button);
        }, 1500);
    } else {
        hideLoading(button);
    }
}

/**
 * Valida los filtros del formulario según el tipo de reporte
 * @param {string} reportType - El tipo de reporte a validar
 * @returns {boolean} - Indica si los filtros son válidos
 */
function validateFilters(reportType) {
    let isValid = true;
    let errorMessage = '';
    
    switch (reportType) {
        case 'inventario':
            // No se requiere validación especial para el inventario
            break;
            
        case 'pedidos-fechas':
            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;
            
            if (!fechaInicio || !fechaFin) {
                isValid = false;
                errorMessage = 'Por favor selecciona un rango de fechas válido.';
            } else if (new Date(fechaInicio) > new Date(fechaFin)) {
                isValid = false;
                errorMessage = 'La fecha de inicio no puede ser posterior a la fecha fin.';
            }
            break;
            
        case 'pedidos-producto':
            const productoFechaInicio = document.getElementById('producto-fecha-inicio').value;
            const productoFechaFin = document.getElementById('producto-fecha-fin').value;
            
            if (!productoFechaInicio || !productoFechaFin) {
                isValid = false;
                errorMessage = 'Por favor selecciona un rango de fechas válido.';
            } else if (new Date(productoFechaInicio) > new Date(productoFechaFin)) {
                isValid = false;
                errorMessage = 'La fecha de inicio no puede ser posterior a la fecha fin.';
            }
            break;
    }
    
    if (!isValid) {
        showFeedback(reportType, 'error', errorMessage);
    }
    
    return isValid;
}

/**
 * Actualiza la vista previa según los filtros seleccionados
 * @param {string} reportId - El ID del reporte a actualizar
 */
function updatePreview(reportId) {
    // En una implementación real, aquí se haría una solicitud AJAX
    // para obtener los datos filtrados desde el servidor
    
    // Por ahora, solo simulamos un cambio en la vista previa
    const reportCard = document.getElementById(reportId);
    if (!reportCard) return;
    
    // Simular estado de carga
    const previewContainer = reportCard.querySelector('.report-preview-container');
    if (previewContainer) {
        previewContainer.style.opacity = '0.5';
        
        // Simular tiempo de carga
        setTimeout(() => {
            previewContainer.style.opacity = '1';
            
            // En una implementación real, aquí se actualizarían los datos
            // Por ahora no hacemos cambios en la vista previa
        }, 800);
    }
}

/**
 * Muestra un mensaje de retroalimentación (éxito o error)
 * @param {string} reportType - El tipo de reporte
 * @param {string} type - El tipo de mensaje ('success' o 'error')
 * @param {string} message - El mensaje a mostrar
 */
function showFeedback(reportType, type, message) {
    const reportCard = document.getElementById(`report-${reportType}`);
    if (!reportCard) return;
    
    // Buscar si ya existe un elemento de feedback o crearlo
    let feedbackEl = reportCard.querySelector('.report-feedback');
    if (!feedbackEl) {
        feedbackEl = document.createElement('div');
        feedbackEl.className = `report-feedback ${type}`;
        reportCard.querySelector('.report-card-body').appendChild(feedbackEl);
    } else {
        feedbackEl.className = `report-feedback ${type}`;
    }
    
    // Actualizar el mensaje y mostrar
    feedbackEl.textContent = message;
    feedbackEl.style.display = 'block';
    
    // Ocultar después de 5 segundos
    setTimeout(() => {
        feedbackEl.style.display = 'none';
    }, 5000);
}

/**
 * Muestra un indicador de carga en un botón
 * @param {HTMLElement} button - El botón donde mostrar la carga
 */
function showLoading(button) {
    // Crear un spinner si no existe
    let spinner = button.querySelector('.loading-spinner');
    if (!spinner) {
        spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        button.prepend(spinner);
    }
    
    // Mostrar el spinner
    spinner.style.display = 'inline-block';
    button.disabled = true;
}

/**
 * Oculta el indicador de carga de un botón
 * @param {HTMLElement} button - El botón donde ocultar la carga
 */
function hideLoading(button) {
    const spinner = button.querySelector('.loading-spinner');
    if (spinner) {
        spinner.style.display = 'none';
        button.disabled = false;
    }
}

/**
 * Busca el elemento padre más cercano que coincida con el selector
 * @param {HTMLElement} element - El elemento desde donde buscar
 * @param {string} selector - El selector CSS para encontrar el padre
 * @returns {HTMLElement|null} - El elemento padre encontrado o null
 */
function findClosestParent(element, selector) {
    while (element && element !== document) {
        if (element.matches(selector)) return element;
        element = element.parentElement;
    }
    return null;
}

/**
 * Formatea una fecha para mostrarla en el formato local
 * @param {string} dateString - La fecha en formato YYYY-MM-DD
 * @returns {string} - La fecha formateada
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/**
 * Genera un identificador único
 * @returns {string} - Un identificador único
 */
function generateUniqueId() {
    return 'id_' + Math.random().toString(36).substr(2, 9);
}

/**
 * Prepara los datos para enviar al servidor para generar el PDF
 * @param {string} reportType - El tipo de reporte
 * @returns {Object} - Los datos para enviar al servidor
 */
function prepareReportData(reportType) {
    const data = {
        reportType: reportType,
        filters: {}
    };
    
    switch (reportType) {
        case 'inventario':
            data.filters.categoria = document.getElementById('categoria-inventario').value;
            data.filters.stockStatus = document.getElementById('stock-status').value;
            break;
            
        case 'pedidos-fechas':
            data.filters.fechaInicio = document.getElementById('fecha-inicio').value;
            data.filters.fechaFin = document.getElementById('fecha-fin').value;
            data.filters.estado = document.getElementById('estado-pedido').value;
            break;
            
        case 'pedidos-producto':
            data.filters.fechaInicio = document.getElementById('producto-fecha-inicio').value;
            data.filters.fechaFin = document.getElementById('producto-fecha-fin').value;
            data.filters.categoria = document.getElementById('categoria-producto').value;
            break;
    }
    
    return data;
}

/**
 * Función que se conectaría con el backend para generar el PDF
 * En una implementación real, esta función enviaría los datos al servidor
 * @param {Object} data - Los datos para generar el PDF
 * @returns {Promise} - Una promesa que se resuelve cuando se genera el PDF
 */
function requestPdfGeneration(data) {
    // Esta es una simulación. En la implementación real, aquí habría una
    // petición AJAX al servidor que generaría el PDF.
    
    return new Promise((resolve, reject) => {
        // Simular una respuesta exitosa después de 1.5 segundos
        setTimeout(() => {
            if (Math.random() > 0.1) { // 90% de éxito
                resolve({
                    success: true,
                    message: 'PDF generado correctamente',
                    pdfUrl: '#' // En la implementación real, aquí vendría la URL del PDF
                });
            } else {
                reject({
                    success: false,
                    message: 'Error al generar el PDF. Inténtalo de nuevo.'
                });
            }
        }, 1500);
    });
}