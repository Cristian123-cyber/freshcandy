<?php
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../models/MySQL.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class ReporteController
{
    private $mysql;
    private $reporteModel;

    public function __construct()
    {
        $this->mysql = new MySQL();
        $this->reporteModel = new Reporte($this->mysql);
    }

    /**
     * Maneja la respuesta en formato JSON
     */
    private function handleResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Reporte de inventario de ingredientes
     * Recibe: categoriaId (opcional), estadoStock (opcional)
     */
    public function inventarioIngredientes()
    {
        $categoriaId = isset($_GET['categoriaId']) ? intval($_GET['categoriaId']) : null;
        $estadoStock = isset($_GET['estadoStock']) ? intval($_GET['estadoStock']) : null;
        $data = $this->reporteModel->getInventarioIngredientes($categoriaId, $estadoStock);
        $this->handleResponse($data);
    }

    /**
     * Reporte de pedidos por fecha
     * Recibe: fechaInicio, fechaFin, estado (opcional)
     */
    public function pedidosPorFecha()
    {
        $fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : null;
        $fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : null;
        $estado = isset($_GET['estado']) ? intval($_GET['estado']) : null;
        if (!$fechaInicio || !$fechaFin) {
            $this->handleResponse(['error' => 'Fechas requeridas']);
        }
        $data = $this->reporteModel->getPedidosPorFecha($fechaInicio, $fechaFin, $estado);
        $this->handleResponse($data);
    }

    /**
     * Reporte de pedidos por producto
     * Recibe: fechaInicio, fechaFin, categoriaId (opcional)
     */
    public function pedidosPorProducto()
    {
        $fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : null;
        $fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : null;
        $categoriaId = isset($_GET['categoriaId']) ? intval($_GET['categoriaId']) : null;
        if (!$fechaInicio || !$fechaFin) {
            $this->handleResponse(['error' => 'Fechas requeridas']);
        }
        $data = $this->reporteModel->getPedidosPorProducto($fechaInicio, $fechaFin, $categoriaId);
        $this->handleResponse($data);
    }

    /**
     * Devuelve las categorías de ingredientes en formato JSON para el frontend
     */
    public function categoriasIngredientes()
    {
        $result = $this->mysql->efectuarConsulta("SELECT id_categoria, titulo_categoria FROM categorias_ingredientes");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Enrutamiento simple (puedes adaptarlo a tu sistema de rutas)
$controller = new ReporteController();
$action = isset($_GET['action']) ? $_GET['action'] : null;

switch ($action) {
    case 'inventarioIngredientes':
        $controller->inventarioIngredientes();
        break;
    case 'pedidosPorFecha':
        $controller->pedidosPorFecha();
        break;
    case 'pedidosPorProducto':
        $controller->pedidosPorProducto();
        break;
    case 'categoriasIngredientes':
        $controller->categoriasIngredientes();
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Acción no válida']);
        exit;
} 