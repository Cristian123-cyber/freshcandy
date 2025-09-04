// Configuración de paginación
let paginationConfig = {
    currentPage: 1,
    itemsPerPage: 5,
    totalItems: 0,
    totalPages: 1
};

// Arreglo global para todos los ingredientes
let allIngredients = [];

// Elementos DOM para la paginación
const paginationFrom = document.getElementById('pagination-from');
const paginationTo = document.getElementById('pagination-to');
const paginationTotal = document.getElementById('pagination-total');
const paginationCurrentPage = document.getElementById('pagination-current-page');
const paginationTotalPages = document.getElementById('pagination-total-pages');
const paginationFirst = document.getElementById('pagination-first');
const paginationPrev = document.getElementById('pagination-prev');
const paginationNext = document.getElementById('pagination-next');
const paginationLast = document.getElementById('pagination-last');
const itemsPerPageSelect = document.getElementById('items-per-page');

// Inicializar la paginación
// Al cargar el DOM, se agregan los listeners a los botones y se carga la primera página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar eventos de los botones de paginación
    paginationFirst.addEventListener('click', () => goToPage(1));
    paginationPrev.addEventListener('click', () => goToPage(paginationConfig.currentPage - 1));
    paginationNext.addEventListener('click', () => goToPage(paginationConfig.currentPage + 1));
    paginationLast.addEventListener('click', () => goToPage(paginationConfig.totalPages));
    
    // Cambiar cantidad de items por página
    itemsPerPageSelect.addEventListener('change', function() {
        paginationConfig.itemsPerPage = parseInt(this.value);
        paginationConfig.currentPage = 1; // Reset a primera página
        renderPaginatedTable();
    });
    
    // Sincronizar el valor del select con el valor por defecto del JS
    itemsPerPageSelect.value = paginationConfig.itemsPerPage;
    
    // Cargar todos los ingredientes una sola vez
    fetchAllIngredients();
});

// Cargar todos los ingredientes del backend una sola vez
function fetchAllIngredients() {
    fetch('/../../controllers/IngredientsController.php?action=getAllIngredients', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        allIngredients = data.data.ingredients || [];
        renderPaginatedTable();
    })
    .catch(error => {
        console.error('Error al cargar datos del inventario:', error);
        showErrorMessage('Error al cargar los datos. Por favor, inténtalo de nuevo.');
    });
}

// Renderizar la tabla paginada usando el arreglo global
function renderPaginatedTable() {
    const totalItems = allIngredients.length;
    paginationConfig.totalItems = totalItems;
    paginationConfig.totalPages = Math.max(1, Math.ceil(totalItems / paginationConfig.itemsPerPage));
    
    // Ajustar página actual si se sale de rango
    if (paginationConfig.currentPage > paginationConfig.totalPages) {
        paginationConfig.currentPage = paginationConfig.totalPages;
    }
    if (paginationConfig.currentPage < 1) {
        paginationConfig.currentPage = 1;
    }

    const from = (paginationConfig.currentPage - 1) * paginationConfig.itemsPerPage;
    const to = Math.min(from + paginationConfig.itemsPerPage, totalItems);
    const pageItems = allIngredients.slice(from, to);

    updateInventoryTable(pageItems);
    updatePaginationInfo();
}

// Actualizar la tabla con los datos recibidos (solo los de la página actual)
function updateInventoryTable(items) {
    const tableBody = document.querySelector('.data-table tbody');
    
    // Limpiar tabla existente
    tableBody.innerHTML = '';
    
    // Si no hay datos, mostrar mensaje
    if (items.length === 0) {
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = `
            <td colspan="6" class="empty-table-message">
                <i class="fas fa-info-circle"></i> 
                No se encontraron productos en el inventario
            </td>
        `;
        tableBody.appendChild(emptyRow);
        return;
    }
    
    // Agregar filas con los datos recibidos
    items.forEach(item => {
        const row = document.createElement('tr');
        
        // Determinar el estado del stock y su clase CSS
        let stockBadgeClass = 'normal';
        if (item.stock_status === 'Crítico') {
            stockBadgeClass = 'critical';
        } else if (item.stock_status === 'Bajo') {
            stockBadgeClass = 'low';
        }
        
        row.innerHTML = `
            <td>${item.id_ingrediente || item.id}</td>
            <td>${item.nombre_ing || item.nombre}</td>
            <td>${item.categoria}</td>
            <td>${item.stock_ing || item.stock} ${item.unidad}</td>
            <td><span class="stock-badge ${stockBadgeClass}">${item.estado || item.stock_status}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon btn-edit" title="Editar ingrediente" data-id="${item.id_ingrediente || item.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon btn-restock" title="Reabastecer stock" data-id="${item.id_ingrediente || item.id}">
                        <i class="fas fa-box-open"></i>
                    </button>
                    <button class="btn-icon btn-delete" title="Eliminar ingrediente" data-id="${item.id_ingrediente || item.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Actualizar la información de paginación en la UI
function updatePaginationInfo() {
    const from = (paginationConfig.currentPage - 1) * paginationConfig.itemsPerPage + 1;
    const to = Math.min(from + paginationConfig.itemsPerPage - 1, paginationConfig.totalItems);
    
    paginationFrom.textContent = paginationConfig.totalItems === 0 ? 0 : from;
    paginationTo.textContent = to;
    paginationTotal.textContent = paginationConfig.totalItems;
    paginationCurrentPage.textContent = paginationConfig.currentPage;
    paginationTotalPages.textContent = paginationConfig.totalPages;
    
    // Habilitar/deshabilitar botones de navegación
    paginationFirst.disabled = paginationConfig.currentPage <= 1;
    paginationPrev.disabled = paginationConfig.currentPage <= 1;
    paginationNext.disabled = paginationConfig.currentPage >= paginationConfig.totalPages;
    paginationLast.disabled = paginationConfig.currentPage >= paginationConfig.totalPages;
}

// Navegar a una página específica
function goToPage(page) {
    if (page < 1 || page > paginationConfig.totalPages || page === paginationConfig.currentPage) {
        return;
    }
    paginationConfig.currentPage = page;
    renderPaginatedTable();
}

// Mostrar mensaje de error
function showErrorMessage(message) {
    console.error(message);
    const notification = document.createElement('div');
    notification.className = 'error-notification';
    notification.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

