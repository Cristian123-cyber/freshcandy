// logicaReporte.js
// Lógica AJAX para los reportes de la vista de reportes (reportesView.php)
// Este archivo se encarga de consumir el backend y llenar las tablas según los filtros seleccionados

// Esperar a que el DOM esté listo
window.addEventListener('DOMContentLoaded', () => {
    cargarCategoriasIngredientes(); // Llenar el select de categorías dinámicamente
    initReportesListeners();
    cargarInventario();
    cargarPedidosPorFecha();
    cargarPedidosPorProducto();
});

/**
 * Carga las categorías de ingredientes desde el backend y llena el select
 */
function cargarCategoriasIngredientes() {
    fetch('/../../controllers/ReporteController.php?action=categoriasIngredientes')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('categoria-inventario');
            // Limpiar opciones actuales y agregar la opción "Todos"
            select.innerHTML = '<option value="todos">Todos los ingredientes</option>';
            // Agregar cada categoría como opción
            data.forEach(cat => {
                select.innerHTML += `<option value="${cat.id_categoria}">${cat.titulo_categoria}</option>`;
            });
        });
}

function initReportesListeners() {
    // Inventario de ingredientes
    document.getElementById('categoria-inventario').addEventListener('change', cargarInventario);
    document.getElementById('stock-status').addEventListener('change', cargarInventario);
    // Pedidos por fecha
    document.getElementById('fecha-inicio').addEventListener('change', cargarPedidosPorFecha);
    document.getElementById('fecha-fin').addEventListener('change', cargarPedidosPorFecha);
    document.getElementById('estado-pedido').addEventListener('change', cargarPedidosPorFecha);
    // Pedidos por producto
    document.getElementById('producto-fecha-inicio').addEventListener('change', cargarPedidosPorProducto);
    document.getElementById('producto-fecha-fin').addEventListener('change', cargarPedidosPorProducto);
    document.getElementById('categoria-producto').addEventListener('change', cargarPedidosPorProducto);
}

// ================== REPORTE INVENTARIO ==================
function cargarInventario() {
    const categoria = document.getElementById('categoria-inventario').value;
    const estado = document.getElementById('stock-status').value;
    // El valor ahora es el ID real de la categoría
    const categoriaId = mapCategoriaIngrediente(categoria);
    const estadoStock = mapEstadoStock(estado);
    fetch(`/../../controllers/ReporteController.php?action=inventarioIngredientes&categoriaId=${categoriaId}&estadoStock=${estadoStock}`)
        .then(res => res.json())
        .then(data => llenarTablaInventario(data))
        .catch(() => llenarTablaInventario([]));
}

function llenarTablaInventario(data) {
    const tbody = document.querySelector('#inventario-table tbody');
    tbody.innerHTML = '';
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5">No hay datos para mostrar.</td></tr>';
        return;
    }
    data.forEach(row => {
        const estado = formatearEstadoStock(row['titulo_estado']);
        tbody.innerHTML += `
            <tr>
                <td>${row['nombre_ing']}</td>
                <td>${row['titulo_categoria']}</td>
                <td>${row['stock_ing']}</td>
                <td>${row['nombre_unidad']}</td>
                <td>${estado}</td>
            </tr>
        `;
    });
}

// ================== REPORTE PEDIDOS POR FECHA ==================
function cargarPedidosPorFecha() {
    const fechaInicio = document.getElementById('fecha-inicio').value;
    const fechaFin = document.getElementById('fecha-fin').value;
    const estado = document.getElementById('estado-pedido').value;
    const estadoId = mapEstadoPedido(estado);
    if (!fechaInicio || !fechaFin) return;
    fetch(`/../../controllers/ReporteController.php?action=pedidosPorFecha&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&estado=${estadoId}`)
        .then(res => res.json())
        .then(data => llenarTablaPedidosPorFecha(data))
        .catch(() => llenarTablaPedidosPorFecha([]));
}

function llenarTablaPedidosPorFecha(data) {
    const tbody = document.querySelector('#pedidos-fechas-table tbody');
    tbody.innerHTML = '';
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5">No hay datos para mostrar.</td></tr>';
        return;
    }
    data.forEach(row => {
        tbody.innerHTML += `
            <tr>
                <td>#FC-${row['id_pedido']}</td>
                <td>${formatearFecha(row['fecha'])}</td>
                <td>${row['nombre_cliente']}</td>
                <td>$${parseFloat(row['monto_total']).toFixed(2)}</td>
                <td>${formatearEstadoPedido(row['titulo_estado'])}</td>
            </tr>
        `;
    });
}

// ================== REPORTE PEDIDOS POR PRODUCTO ==================
function cargarPedidosPorProducto() {
    const fechaInicio = document.getElementById('producto-fecha-inicio').value;
    const fechaFin = document.getElementById('producto-fecha-fin').value;
    const categoria = document.getElementById('categoria-producto').value;
    const categoriaId = mapCategoriaProducto(categoria);
    if (!fechaInicio || !fechaFin) return;
    fetch(`/../../controllers/ReporteController.php?action=pedidosPorProducto&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&categoriaId=${categoriaId}`)
        .then(res => res.json())
        .then(data => llenarTablaPedidosPorProducto(data))
        .catch(() => llenarTablaPedidosPorProducto([]));
}

function llenarTablaPedidosPorProducto(data) {
    const tbody = document.querySelector('#pedidos-producto-table tbody');
    tbody.innerHTML = '';
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5">No hay datos para mostrar.</td></tr>';
        return;
    }
    data.forEach(row => {
        // Puede venir como nombre_categoria o titulo_etiqueta
        const categoria = row['nombre_categoria'] || row['titulo_etiqueta'] || '';
        tbody.innerHTML += `
            <tr>
                <td>${row['nombre_producto']}</td>
                <td>${categoria}</td>
                <td>${row['unidades_vendidas']}</td>
                <td>$${parseFloat(row['total_ventas']).toFixed(2)}</td>
                <td>${row['porcentaje']}%</td>
            </tr>
        `;
    });
}

// ================== MAPEO Y FORMATEO ==================
// Ahora el valor del select es el ID real, así que solo devolvemos ese valor
function mapCategoriaIngrediente(valor) {
    return valor === 'todos' ? '' : valor;
}
// Mapea los valores del select de estado de stock a los IDs de la base de datos
function mapEstadoStock(valor) {
    switch (valor) {
        case 'ok': return 1;
        case 'low': return 2;
        case 'critical': return 3;
        default: return '';
    }
}
// Mapea los valores del select de estado de pedido a los IDs de la base de datos
function mapEstadoPedido(valor) {
    switch (valor) {
        case 'pending': return 1;
        case 'processing': return 2;
        case 'shipped': return 3;
        case 'delivered': return 4;
        case 'canceled': return 5;
        default: return '';
    }
}
// Mapea los valores del select de categoría de productos a los IDs de la base de datos
function mapCategoriaProducto(valor) {
    switch (valor) {
        case 'helados': return 1;
        case 'paletas': return 2;
        case 'postres': return 3;
        case 'bebidas': return 4;
        default: return '';
    }
}
// Formatea el estado de stock para mostrarlo con color
function formatearEstadoStock(estado) {
    switch (estado ? estado.toLowerCase() : '') {
        case 'óptimo':
        case 'optimo':
            return '<span class="estado-optimo">Óptimo</span>';
        case 'bajo':
            return '<span class="estado-bajo">Bajo</span>';
        case 'crítico':
        case 'critico':
            return '<span class="estado-critico">Crítico</span>';
        default:
            return estado || '';
    }
}
// Formatea el estado del pedido para mostrarlo con color
function formatearEstadoPedido(estado) {
    switch (estado ? estado.toLowerCase() : '') {
        case 'pendiente':
            return '<span class="estado-pendiente">Pendiente</span>';
        case 'en proceso':
            return '<span class="estado-proceso">En proceso</span>';
        case 'enviado':
            return '<span class="estado-enviado">Enviado</span>';
        case 'entregado':
            return '<span class="estado-entregado">Entregado</span>';
        case 'cancelado':
            return '<span class="estado-cancelado">Cancelado</span>';
        default:
            return estado || '';
    }
}
// Formatea la fecha a DD/MM/YYYY
function formatearFecha(fecha) {
    if (!fecha) return '';
    const d = new Date(fecha);
    return d.toLocaleDateString('es-ES');
}

// Evento para generar el PDF del inventario de ingredientes
const btnPDFInventario = document.querySelector('.btn-generate-pdf[data-report="inventario"]');
if (btnPDFInventario) {

    btnPDFInventario.addEventListener('click', function() {
        showLoading(btnPDFInventario);
        const categoria = document.getElementById('categoria-inventario').value;
        const estado = document.getElementById('stock-status').value;
        const categoriaId = mapCategoriaIngrediente(categoria);
        const estadoStock = mapEstadoStock(estado);
        const url = `../../controllers/reporteInventarioPDF.php?categoriaId=${categoriaId}&estadoStock=${estadoStock}`;

        setTimeout(() => {
            hideLoading(btnPDFInventario);
            window.open(url, '_blank');
        }, 1000);
    });
}
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
function hideLoading(button) {
    const spinner = button.querySelector('.loading-spinner');
    if (spinner) {
        spinner.style.display = 'none';
        button.disabled = false;
    }
}
// Evento para generar el PDF de pedidos por fecha
const btnPDFPedidosFecha = document.querySelector('.btn-generate-pdf[data-report="pedidos-fechas"]');
if (btnPDFPedidosFecha) {
    btnPDFPedidosFecha.addEventListener('click', function() {
        showLoading(btnPDFPedidosFecha);
        const fechaInicio = document.getElementById('fecha-inicio').value;
        const fechaFin = document.getElementById('fecha-fin').value;
        const estado = document.getElementById('estado-pedido').value;
        const estadoId = mapEstadoPedido(estado);
        const url = `../../controllers/reportePedidosFechaPDF.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&estado=${estadoId}`;
        setTimeout(() => {
            hideLoading(btnPDFPedidosFecha);
            window.open(url, '_blank');
        }, 1000);
    });
}

// Evento para generar el PDF de pedidos por producto
const btnPDFPedidosProducto = document.querySelector('.btn-generate-pdf[data-report="pedidos-producto"]');
if (btnPDFPedidosProducto) {
    btnPDFPedidosProducto.addEventListener('click', function() {
        showLoading(btnPDFPedidosProducto);
        const fechaInicio = document.getElementById('producto-fecha-inicio').value;
        const fechaFin = document.getElementById('producto-fecha-fin').value;
        const categoria = document.getElementById('categoria-producto').value;
        const categoriaId = mapCategoriaProducto(categoria);
        const url = `../../controllers/reportePedidosProductoPDF.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&categoriaId=${categoriaId}`;
        setTimeout(() => {
            hideLoading(btnPDFPedidosProducto);
            window.open(url, '_blank');
        }, 1000);
    });
} 