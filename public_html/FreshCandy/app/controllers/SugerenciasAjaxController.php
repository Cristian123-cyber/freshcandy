<?php
// Controlador AJAX dedicado para la vista de sugerencias (únicamente para admin)
// Devuelve sugerencias únicas, con filtros y paginación, y permite actualizar estado
// Autor: AI - FreshCandy

header('Content-Type: application/json');
require_once '../models/MySQL.php';
require_once '../models/Sugerencia.php';

class SugerenciasAjaxController
{
    // Maneja la respuesta estándar
    public static function handleResponse($success, $message, $data = [], $httpCode = 200)
    {
        http_response_code($httpCode);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    // Endpoint para listar sugerencias únicas (con filtros y paginación)
    public static function listarUnicas()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            // Parámetros de filtros y paginación
            $estado = $_GET['estado'] ?? '';
            $buscar = $_GET['buscar'] ?? '';
            $fecha = $_GET['fecha'] ?? '';
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $porPagina = isset($_GET['porPagina']) ? max(1, intval($_GET['porPagina'])) : 10;
            $offset = ($pagina - 1) * $porPagina;

            // Construir WHERE dinámico
            $where = '1=1';
            if ($estado && $estado !== 'todos') {
                $where .= " AND es.nombre_estado = '" . $mysql->escapar($estado) . "'";
            }
            if ($buscar) {
                $buscar = $mysql->escapar($buscar);
                $where .= " AND (s.titulo_sugerencia LIKE '%$buscar%' OR s.sugerencia_info LIKE '%$buscar%')";
            }
            if ($fecha && $fecha !== 'todas') {
                if ($fecha === 'hoy') {
                    $where .= " AND DATE(s.fecha) = CURDATE()";
                } else if ($fecha === 'semana') {
                    $where .= " AND YEARWEEK(s.fecha, 1) = YEARWEEK(CURDATE(), 1)";
                } else if ($fecha === 'mes') {
                    $where .= " AND YEAR(s.fecha) = YEAR(CURDATE()) AND MONTH(s.fecha) = MONTH(CURDATE())";
                }
            }

            // Consulta para contar total filtrado
            $queryTotal = "SELECT COUNT(DISTINCT s.id_sugerencia) as total
                FROM sugerencias s
                LEFT JOIN tipo_sugerencia ts ON s.Tipo_Sugerencia_id_tipo = ts.id_tipo
                LEFT JOIN estado_sugerencias es ON s.Estado_Sugerencias_id_estado = es.id_estado
                LEFT JOIN clientes c ON s.Clientes_id_cliente = c.id_cliente
                WHERE $where";
            $resultTotal = $mysql->efectuarConsulta($queryTotal);
            $total = 0;
            if ($row = mysqli_fetch_assoc($resultTotal)) {
                $total = intval($row['total']);
            }

            // Consulta principal con filtros y paginación
            $query = "SELECT s.id_sugerencia, s.titulo_sugerencia, s.sugerencia_info, s.fecha,
                             ts.nombre_tipo, ts.id_tipo AS id_tipo, es.nombre_estado, c.nombre_cliente
                      FROM sugerencias s
                      LEFT JOIN tipo_sugerencia ts ON s.Tipo_Sugerencia_id_tipo = ts.id_tipo
                      LEFT JOIN estado_sugerencias es ON s.Estado_Sugerencias_id_estado = es.id_estado
                      LEFT JOIN clientes c ON s.Clientes_id_cliente = c.id_cliente
                      WHERE $where
                      GROUP BY s.id_sugerencia
                      ORDER BY s.fecha DESC
                      LIMIT $offset, $porPagina";
            $result = $mysql->efectuarConsulta($query);
            $sugerencias = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $sugerencias[] = $row;
            }
            self::handleResponse(true, 'Sugerencias únicas obtenidas', [
                'sugerencias' => $sugerencias,
                'total' => $total
            ]);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener sugerencias: ' . $e->getMessage(), [], 500);
        }
    }

    // Endpoint para marcar sugerencia como revisada
    public static function marcarRevisada()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                self::handleResponse(false, 'ID inválido', [], 400);
                return;
            }
            // Estado 2 = Revisada
            $query = "UPDATE sugerencias SET Estado_Sugerencias_id_estado = 2 WHERE id_sugerencia = " . intval($id);
            $result = $mysql->efectuarConsulta($query);
            if ($result) {
                self::handleResponse(true, 'Sugerencia marcada como revisada');
            } else {
                self::handleResponse(false, 'No se pudo actualizar la sugerencia', [], 500);
            }
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al actualizar sugerencia: ' . $e->getMessage(), [], 500);
        }
    }

    // Endpoint para obtener los estados de sugerencias
    public static function getEstados()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            $sugerenciaModel = new Sugerencia($mysql);
            $result = $sugerenciaModel->getEstadosSugerencias();
            if ($result['success']) {
                self::handleResponse(true, 'Estados obtenidos', $result['data']);
            } else {
                self::handleResponse(false, $result['message'], [], 500);
            }
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener estados: ' . $e->getMessage(), [], 500);
        }
    }

    // Endpoint para marcar sugerencia como eliminada
    public static function marcarEliminada()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                self::handleResponse(false, 'ID inválido', [], 400);
                return;
            }
            // Estado 3 = Eliminada
            $query = "UPDATE sugerencias SET Estado_Sugerencias_id_estado = 3 WHERE id_sugerencia = " . intval($id);
            $result = $mysql->efectuarConsulta($query);
            if ($result) {
                self::handleResponse(true, 'Sugerencia marcada como eliminada');
            } else {
                self::handleResponse(false, 'No se pudo actualizar la sugerencia', [], 500);
            }
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al actualizar sugerencia: ' . $e->getMessage(), [], 500);
        }
    }

    // Endpoint para obtener sugerencias recientes (últimas 5)
    public static function getRecentSuggestions()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }

            // Consulta para obtener las últimas 5 sugerencias
            $query = "SELECT s.id_sugerencia, s.titulo_sugerencia, s.sugerencia_info, s.fecha,
                             ts.nombre_tipo, ts.id_tipo AS id_tipo, es.nombre_estado, c.nombre_cliente
                      FROM sugerencias s
                      LEFT JOIN tipo_sugerencia ts ON s.Tipo_Sugerencia_id_tipo = ts.id_tipo
                      LEFT JOIN estado_sugerencias es ON s.Estado_Sugerencias_id_estado = es.id_estado
                      LEFT JOIN clientes c ON s.Clientes_id_cliente = c.id_cliente
                      WHERE es.nombre_estado != 'Eliminada'
                      ORDER BY s.fecha DESC
                      LIMIT 5";

            $result = $mysql->efectuarConsulta($query);
            $sugerencias = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $sugerencias[] = $row;
            }
            self::handleResponse(true, 'Sugerencias recientes obtenidas', $sugerencias);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener sugerencias recientes: ' . $e->getMessage(), [], 500);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'listarUnicas':
            SugerenciasAjaxController::listarUnicas();
            break;
        case 'getEstados':
            SugerenciasAjaxController::getEstados();
            break;
        case 'getRecentSuggestions':
            SugerenciasAjaxController::getRecentSuggestions();
            break;
        default:
            SugerenciasAjaxController::handleResponse(false, 'Acción no válida', [], 400);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'marcarRevisada':
            SugerenciasAjaxController::marcarRevisada();
            break;
        case 'marcarEliminada':
            SugerenciasAjaxController::marcarEliminada();
            break;
        default:
            SugerenciasAjaxController::handleResponse(false, 'Acción no válida', [], 400);
    }
} else {
    SugerenciasAjaxController::handleResponse(false, 'Método no permitido', [], 405);
}
