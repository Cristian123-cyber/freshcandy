<?php

header('Content-Type: application/json');
require_once '../models/MySQL.php';
require_once '../models/Stats.php';

class StatsController
{
    //Funcion para manejar las respuestas
    public static function handleResponse($success, $message, $data = [], $httpCode = 200) //en el parametro $data se pasan los DATOS OBTENIDOS
                                                                                           //YO DESPUES USO EL CONTROLADOR Y LOS RENDERIZO
                                                                                           //USTED SOLO CREE EL CONTROLADOR Y EL MODELO         
    {
        http_response_code($httpCode);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    // Obtener estadísticas para el dashboard principal
    public static function getStatsForPrincipal()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }

            $stats = new Stats($mysql);
            $data = [];
            $data = array_merge(
                $stats->getPedidosHoy(),
                $stats->getPedidosPendientes(),
                $stats->getStockBajo(),
                $stats->getNuevasSugerencias(),
                $stats->getPedidosAyer()
            );
            self::handleResponse(true, 'Estadísticas obtenidas con éxito', $data);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener las estadísticas dashboard ' . $e->getMessage(), [], 500);
        }
    }

    // Obtener estadísticas de clientes
    public static function getStatsForClientes()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            $stats = new Stats($mysql);
            $data = array_merge(
                $stats->getTotalClientes(),
                $stats->getClienteMasPedidos(),
                $stats->getClienteMayorGasto()
            );
            self::handleResponse(true, 'Estadísticas de clientes obtenidas con éxito', $data);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener las estadísticas de clientes', [], 500);
        }
    }

    // Obtener estadísticas de pedidos
    public static function getStatsForPedidos()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            $stats = new Stats($mysql);
            $data = array_merge(
                $stats->getPedidosHoy(),
                $stats->getPedidosPendientes(),
                $stats->getPedidosCompletados(),
                $stats->getPedidosCancelados()
            );
            self::handleResponse(true, 'Estadísticas de pedidos obtenidas con éxito', $data);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener las estadísticas de pedidos', [], 500);
        }
    }

    // Obtener estadísticas de productos
    public static function getStatsForProductos()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            $stats = new Stats($mysql);
            $data = array_merge(
                $stats->getHeladoMasVendido(),
                $stats->getHeladoMenosVendido(),
                $stats->getTotalProductos()
            );
            self::handleResponse(true, 'Estadísticas de productos obtenidas con éxito', $data);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener las estadísticas de productos', [], 500);
        }
    }

    // Obtener estadísticas de inventario
    public static function getStatsForInventario()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            $stats = new Stats($mysql);
            $data = array_merge(
                $stats->getIngredientesStockBajo(),
                $stats->getProductosSinExistencias(),
                $stats->getProductosCriticos()
            );
            self::handleResponse(true, 'Estadísticas de inventario obtenidas con éxito', $data);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener las estadísticas de inventario', [], 500);
        }
    }

    // Obtener estadísticas de sugerencias
    public static function getStatsForSugerencias()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            $stats = new Stats($mysql);
            $data = array_merge(
                $stats->getTotalSugerencias(),
                $stats->getSugerenciasPendientes(),
                $stats->getSugerenciasRevisadas(),
                $stats->getSugerenciasNuevasHoy(),
                $stats->getSugerenciasNuevasAyer()
            );
            self::handleResponse(true, 'Estadísticas de sugerencias obtenidas con éxito', $data);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener las estadísticas de sugerencias', [], 500);
        }
    }

    // Obtener estadísticas personalizadas del cliente logueado
    public static function getEstadisticasCliente()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            $stats = new Stats($mysql);
            $data = $stats->getEstadisticasCliente();
            self::handleResponse(true, 'Estadísticas del cliente obtenidas con éxito', $data);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener las estadísticas del cliente', [], 500);
        }
    }
}
// Verificamos si la petición es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? null;

    //sanitizamos la acción
    if (!$action) {
        StatsController::handleResponse(false, 'No se especificó la acción', [], 400);
        exit;
    }
    $action = filter_var(trim($action), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //Enrutamos la accion con el switch llamando a la funcion correspondiente
    switch ($action) {
        case 'getStatsForDashboard':
            StatsController::getStatsForPrincipal();
            break;
        case 'getStatsForClientes':
            StatsController::getStatsForClientes();
            break;
        case 'getStatsForPedidos':
            StatsController::getStatsForPedidos();
            break;
        case 'getStatsForProductos':
            StatsController::getStatsForProductos();
            break;
        case 'getStatsForInventario':
            StatsController::getStatsForInventario();
            break;
        case 'getStatsForSugerencias':
            StatsController::getStatsForSugerencias();
            break;
        case 'getEstadisticasCliente':
            StatsController::getEstadisticasCliente();
            break;
        default:
            StatsController::handleResponse(false, 'Endpoint no encontrado', [], 404);
            exit;
    }
} else {
    StatsController::handleResponse(false, 'Método no permitido', [], 405);
    exit;
}

