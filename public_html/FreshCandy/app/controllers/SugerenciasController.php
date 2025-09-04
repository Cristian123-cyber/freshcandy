<?php


//El codigo de mano no sirve asi, se debe de seguir la estructura usada 
//en los demas controladores. (AuthController maneja todo lo relacionado con insercciones de usuarios y logins
// implementar el mismo esquema con las sugerencias)

//Controlador de sugerencias estructura basica:
header('Content-Type: application/json');

require_once '../models/MySQL.php';
require_once '../models/Sugerencia.php';

class SugerenciasController
{


    //Funcion para manejar las respuestas
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

    //Funcion para insertar una sugerencia
    public static function insertar()
    {
        try {
            // Obtener y validar datos JSON
            $data = json_decode(file_get_contents('php://input'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                self::handleResponse(false, 'JSON inválido', [], 400);
                return;
            }

            // Validar campos obligatorios
            $requiredFields = ['titulo', 'cuerpo', 'idTipoSugerencia'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    self::handleResponse(false, "El campo $field es requerido", [], 400);
                    return;
                }
            }

            // Sanitizar inputs
            $titulo = filter_var(trim($data['titulo']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cuerpo = filter_var(trim($data['cuerpo']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $idTipoSugerencia = filter_var(trim($data['idTipoSugerencia']), FILTER_VALIDATE_INT);

            if ($idTipoSugerencia === false || $idTipoSugerencia < 1 || $idTipoSugerencia > 4) {
                self::handleResponse(false, 'El tipo de sugerencia no es válido', [], 400);
            }
            // Validaciones específicas
            if (strlen($titulo) < 3 || strlen($titulo) > 100) {
                self::handleResponse(false, 'El título debe tener entre 3 y 100 caracteres', [], 400);
            }

            if (strlen($cuerpo) > 1000) {
                self::handleResponse(false, 'El cuerpo no puede exceder los 1000 caracteres', [], 400);
            }

            if (!$idTipoSugerencia) {
                self::handleResponse(false, 'IDs inválidos', [], 400);
            }

            // Conexión a la base de datos
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }

            // Obtener el id del cliente

            $idCliente = self::getIdCliente();

            if (!$idCliente) {
                self::handleResponse(false, 'No se puede registrar la sugerencia, usuario no autenticado', [], 401);
                return;
            }

            // Creamos una instancia del modelo de sugerencia
            $sugerenciaModel = new Sugerencia($mysql);

            // Insertar sugerencia
            if ($sugerenciaModel->insertarSugerencia($titulo, $cuerpo, $idTipoSugerencia, $idCliente)) {
                self::handleResponse(true, 'Sugerencia registrada con éxito', [
                    'sugerencia' => [
                        'titulo' => $titulo,
                        'tipo' => $idTipoSugerencia,
                        'idCliente' => $idCliente
                    ]
                ]);
            } else {
                self::handleResponse(false, 'Error al registrar la sugerencia', [], 500);
            }

            // Desconectamos de la base de datos
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        } catch (Exception $e) {
            error_log("Error en SugerenciasController::insertar: " . $e->getMessage());
            self::handleResponse(false, 'Error en el servidor: ' . $e->getMessage(), [], 500);
        }
    }

    private static function getIdCliente()
    {

        // Obtener el id del cliente

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user']) && $_SESSION['user']['logged_in'] && $_SESSION['user']['role'] === 2) {
            return intval($_SESSION['user']['id']);
        } else {
            return false;
        }
    }

    //Funcion para obtener los tipos de sugerencias
    public static function getTipos()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }

            $sugerenciaModel = new Sugerencia($mysql);
            $result = $sugerenciaModel->getTiposSugerencias();

            if ($result['success']) {
                self::handleResponse(true, 'Tipos de sugerencias obtenidos', $result['data']);
            } else {
                self::handleResponse(false, $result['message'], [], 500);
            }
        } catch (Exception $e) {
            error_log("Error en SugerenciasController::getTipos: " . $e->getMessage());
            self::handleResponse(false, 'Error al obtener tipos de sugerencias: ' . $e->getMessage(), [], 500);
        }
    }

    // Endpoint para listar todas las sugerencias con estado, tipo y usuario
    public static function listar()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }
            // Consulta con JOIN a estado, tipo y cliente
            $query = "SELECT s.id_sugerencia, s.titulo_sugerencia, s.sugerencia_info, s.fecha,
                             ts.nombre_tipo, es.nombre_estado, c.nombre_cliente
                      FROM sugerencias s
                      LEFT JOIN tipo_sugerencia ts ON s.Tipo_Sugerencia_id_tipo = ts.id_tipo
                      LEFT JOIN estado_sugerencias es ON s.Estado_Sugerencias_id_estado = es.id_estado
                      LEFT JOIN clientes c ON s.Clientes_id_cliente = c.id_cliente
                      ORDER BY s.fecha DESC";
            $result = $mysql->efectuarConsulta($query);
            $sugerencias = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $sugerencias[] = $row;
            }
            self::handleResponse(true, 'Sugerencias obtenidas', $sugerencias);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener sugerencias: ' . $e->getMessage(), [], 500);
        }
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'insertar':
            SugerenciasController::insertar();
            break;
        case 'getTipos':
            SugerenciasController::getTipos();
            break;
        default:
            SugerenciasController::handleResponse(false, 'Acción no válida', [], 400);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'listar':
            SugerenciasController::listar();
            break;
        default:
            SugerenciasController::handleResponse(false, 'Acción no válida', [], 400);
    }
} else {
    SugerenciasController::handleResponse(false, 'Método no permitido', [], 405);
}
