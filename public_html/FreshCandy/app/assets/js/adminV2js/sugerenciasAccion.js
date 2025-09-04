function init() {
    console.log('Inicializando gestión de sugerencias...');
    document.getElementById("dashboard-link5").classList.add("active");
    
    loadSuggestionStats();
}





// Función para cargar las estadísticas de sugerencias
function loadSuggestionStats() {
    console.log('Cargando estadísticas de sugerencias...');
    fetch('/../../controllers/StatsController.php?action=getStatsForSugerencias', {
        method: 'POST'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        console.log('Estadísticas de sugerencias recibidas:', data);
        if (data.success && data.data) {
            const totalSugerenciasElement = document.querySelector('.stat-card.sales .stat-value');
            const sugerenciasPendientesElement = document.querySelector('.stat-card.orders .stat-value');
            const sugerenciasRevisadasElement = document.querySelector('.stat-card.revisadas .stat-value');

            console.log('Elementos encontrados (usando clases):', {
                total: totalSugerenciasElement,
                pendientes: sugerenciasPendientesElement,
                revisadas: sugerenciasRevisadasElement
            });

            if (totalSugerenciasElement) {
                totalSugerenciasElement.textContent = data.data.total_sugerencias || '0';
                console.log('Actualizado total sugerencias (sales):', data.data.total_sugerencias);
            } else {
                console.error('No se encontró el elemento para total sugerencias (.stat-card.sales)');
            }

            if (sugerenciasPendientesElement) {
                sugerenciasPendientesElement.textContent = data.data.sugerencias_pendientes || '0';
                console.log('Actualizado sugerencias pendientes (orders):', data.data.sugerencias_pendientes);
            } else {
                console.error('No se encontró el elemento para sugerencias pendientes (.stat-card.orders)');
            }

            if (sugerenciasRevisadasElement) {
                sugerenciasRevisadasElement.textContent = data.data.sugerencias_revisadas || '0';
                console.log('Actualizado sugerencias revisadas (revisadas):', data.data.sugerencias_revisadas);
            } else {
                console.error('No se encontró el elemento para sugerencias revisadas (.stat-card.revisadas)');
            }


        } else {
            console.error('Error o datos inválidos al cargar estadísticas de sugerencias:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la petición de estadísticas de sugerencias:', error);
    });
}

document.addEventListener("DOMContentLoaded", init);
