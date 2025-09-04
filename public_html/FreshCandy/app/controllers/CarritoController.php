<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/MySQL.php';
require_once __DIR__ . '/../config/config.php';

class CarritoController
{

    //Funcion para manejar las respuestas
    private static function handleResponse($success, $message, $data = [], $httpCode = 200)
    {
        http_response_code($httpCode);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    /**
     * Convert stored image paths to complete URLs for frontend display
     * Takes relative or absolute URLs from database and returns complete URLs
     * that work in both localhost and VPS environments
     * 
     * @param string $imagePath - path from database (relative or absolute)
     * @return string - complete URL for frontend use
     */
    private static function getCompleteImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }

        // If it's already a complete URL with same host, return as is
        if (preg_match('/^https?:\/\//', $imagePath)) {
            // Check if it's from the current host - if so, convert to ensure correct path
            $currentHost = $_SERVER['HTTP_HOST'];
            if (strpos($imagePath, $currentHost) !== false || strpos($imagePath, 'localhost') !== false) {
                // Extract filename and rebuild URL
                $filename = basename($imagePath);
                return getImageUrl($filename);
            }
            // External URL, return as is
            return $imagePath;
        }

        // Use the global function from config.php to get complete URL
        return getImageUrl($imagePath);
    }

    //Funcion para guardar el carrito
    public static function saveCarrito()
    {

        try {
            //code...

            // Obtener y decodificar los datos JSON
            $data = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                self::handleResponse(false, "JSON inválido", [], 400);
                return;
            }

            if (!self::validarCarrito($data)) {
                self::handleResponse(false, "Estructura de datos inválida", [], 400);
                return;
            }


            // Sanitizar y validar cada producto del carrito
            $productosSanitizados = [];

            foreach ($data['productos'] as $producto) {
                // Sanitizar cada campo
                $id = filter_var($producto['id'], FILTER_SANITIZE_NUMBER_INT);
                $titulo = filter_var(trim($producto['titulo']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $precio = filter_var($producto['precio'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $cantidad = filter_var($producto['cantidad'], FILTER_SANITIZE_NUMBER_INT);
                $imagen = filter_var(trim($producto['imagen']), FILTER_SANITIZE_URL);

                // Validar cada campo después de la sanitización
                if (!$id || $id <= 0) {
                    self::handleResponse(false, "Datos del producto inválidos", [], 400);
                    return;
                }

                if (empty($titulo) || strlen($titulo) > 255) {
                    self::handleResponse(false, "Datos del producto inválidos", [], 400);
                    return;
                }

                if (!$precio || $precio <= 0) {
                    self::handleResponse(false, "Datos del producto inválidos", [], 400);
                    return;
                }

                if (!$cantidad || $cantidad <= 0) {
                    self::handleResponse(false, "Datos del producto inválidos", [], 400);
                    return;
                }

                if (empty($imagen)) {
                    self::handleResponse(false, "Datos del producto inválidos", [], 400);
                    return;
                }

                // Agregar producto sanitizado al array
                $productosSanitizados[] = [
                    'id' => (int)$id,
                    'cantidad' => (int)$cantidad
                ];
            }

            self::limpiarCarrito();
            if (self::guardarCarrito($productosSanitizados)) {

                self::handleResponse(true, "Carrito guardado con éxito", [], 200);
            } else {
                self::handleResponse(false, "Error al guardar el carrito", [], 500);
            }
        } catch (\Throwable $th) {
            //throw $th;
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    private static function limpiarCarrito()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpiar solo la parte del carrito
        if (isset($_SESSION['carrito'])) {
            unset($_SESSION['carrito']);
        }
    }

    public static function getCarrito()
    {

        try {


            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : null;

            if (!$carrito || !is_array($carrito) || empty($carrito) || !isset($carrito['items']) || !is_array($carrito['items']) || empty($carrito['items'])) {
                self::handleResponse(false, "Carrito no encontrado", [], 404);
                return;
            }



            self::handleResponse(true, "Carrito encontrado", $carrito, 200);
        } catch (\Throwable $th) {
            //throw $th;
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    private static function guardarCarrito($productos)
    {
        // Iniciar sesión solo si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        $mysql = new MySQL();

        $mysql->conectar();

        if (!$mysql->getStatusConexion()) {
            return false;
        }
        $productoModel = new Producto($mysql);




        $data = [];

        $subtotal = 0;

        foreach ($productos as $producto) {

            $id = $producto['id'];

            $dataProducto = $productoModel->getProductoById($id);

            if (!$dataProducto || $dataProducto === null) {
                return false;
            }

            
            $precio = (float)$dataProducto['precio'];
            $cantidad = (int)$producto['cantidad']; // Cantidad debe ser entero
            $totalProducto = $precio * $cantidad; 

            $data[] = [
                'id' => $producto['id'],
                'titulo' => $dataProducto['nombre'],
                'precio' => $precio, 
                'imagen' => self::getCompleteImageUrl($dataProducto['image_url']),
                'cantidad' => $cantidad, 
                'total' => $totalProducto,
            ];
        
            $subtotal += $totalProducto;
        }

       


        // Encapsular el carrito en una estructura clara
        $_SESSION['carrito'] = [
            'items' => $data,
            'total_items' => array_sum(array_column($data, 'cantidad')), // total de unidades
            'subtotal' => $subtotal, // 
            'timestamp' => time(), // marca de cuándo se guardó
        ];


        // Desconectamos de la base de datos
        if (isset($mysql)) {
            $mysql->desconectar();
        }

        return true;
    }


    private static function validarCarrito($data)
    {


        // Validar que lleguen productos
        if (!isset($data['productos']) || !is_array($data['productos']) || empty($data['productos'])) {

            return false;
        }

        // Validar estructura mínima de cada producto
        foreach ($data['productos'] as $index => $producto) {
            if (
                !isset($producto['id']) ||
                !isset($producto['titulo']) ||
                !isset($producto['precio']) ||
                !isset($producto['cantidad']) ||
                !isset($producto['imagen'])
            ) {
                return false;
            }
        }


        return true;
    }
}

//Punto de entrada para el controlador de sugerencias
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? null;

    //sanitizamos la acción
    if (!$action) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'No se especificó la acción']);
        exit;
    }
    $action = filter_var(trim($action), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //Enrutamos la acción con el switch llamando a la funcion correspondiente
    switch ($action) {
        case 'saveCarrito':
            CarritoController::saveCarrito();
            break;
        case 'getCarrito':
            CarritoController::getCarrito();
            break;
        default:
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            exit;
    }
}
