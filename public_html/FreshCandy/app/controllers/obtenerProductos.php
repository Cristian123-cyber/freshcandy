<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once '../models/MySQL.php';
require_once '../models/Producto.php';
require_once '../config/config.php';


class ProductoController
{
    // Obtener todos los productos
    // retorna respuestas en formato JSON
    // Maneja errores de conexión y resultados vacíos
    // Asegura la conexión y cierre de la conexión

    

    public static function getAllProducts()
    {
        if (ob_get_length()) {
            ob_clean();
        }
        flush();
        $mysql = new MySQL();
        $mysql->conectar();
        if (!$mysql->getStatusConexion()) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al conectar con la base de datos'
            ]);
            exit;
        }
        $productoModel = new Producto($mysql);
        $productos = $productoModel->getAllProductos();
        if ($productos === false) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al consultar la base de datos'
            ]);
            exit;
        }
        foreach ($productos as &$producto) {
            if (isset($producto['image_url']) && $producto['image_url'] !== null) {
                $producto['image_url'] = self::getCompleteImageUrl($producto['image_url']);
            }
        }
        // fin corrección URL

        if (empty($productos)) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'No se encontraron productos'
            ]);
            exit;
        }
        echo json_encode([
            'success' => true,
            'data' => $productos,
            'count' => count($productos)
        ]);
        if (isset($mysql)) {
            $mysql->desconectar();
        }
        exit;
    }

    // Obtener un producto por ID
    // retorna respuestas en formato JSON
    // Maneja errores de conexión y resultados vacíos
    // Asegura la conexión y cierre de la conexión
    public static function getProductById($id)
    {
        if (ob_get_length()) {
            ob_clean();
        }
        flush();
        $mysql = new MySQL();
        $mysql->conectar();

        if (!$mysql->getStatusConexion()) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al conectar con la base de datos'
            ]);
            exit;
        }

        $productoModel = new Producto($mysql);
        $producto = $productoModel->getProductoById($id);

        if ($producto === false || $producto === null) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]);
            exit;
        }

        // Convert stored path to complete URL for frontend
        if (isset($producto['image_url']) && $producto['image_url'] !== null) {
            $producto['image_url'] = self::getCompleteImageUrl($producto['image_url']);
        }

        echo json_encode([
            'success' => true,
            'data' => $producto
        ]);

        if (isset($mysql)) {
            $mysql->desconectar();
        }
        exit;
    }

    public static function handleResponse($success, $message, $data = [], $httpCode = 200)
    {
        if (ob_get_length()) {
            ob_clean();
        }
        flush();
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

    public static function buscarProductos()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error de conexión a la base de datos', [], 500);
                return;
            }

            $producto = new Producto($mysql);

            // Obtener parámetros de búsqueda y filtros
            $termino = $_POST['termino'] ?? '';
            $filtros = [
                'categoria' => $_POST['categoria'] ?? '',
                'precio_min' => $_POST['precio_min'] ?? '',
                'precio_max' => $_POST['precio_max'] ?? '',
                'ordenamiento' => $_POST['ordenamiento'] ?? ''
            ];

            // Realizar la búsqueda con filtros
            $resultados = $producto->buscarProductos($termino, $filtros);
            
            // Convert image URLs to complete URLs for frontend
            foreach ($resultados as &$resultado) {
                if (isset($resultado['image_url']) && $resultado['image_url'] !== null) {
                    $resultado['image_url'] = self::getCompleteImageUrl($resultado['image_url']);
                }
            }

            self::handleResponse(true, 'Búsqueda realizada con éxito', $resultados);
        } catch (Exception $e) {
            error_log("Error en buscarProductos (controlador): " . $e->getMessage());
            self::handleResponse(false, 'Error interno al buscar productos.', [], 500);
        }
    }

    public static function obtenerCategorias()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error de conexión a la base de datos', [], 500);
                return;
            }

            $producto = new Producto($mysql);
            $categorias = $producto->obtenerCategorias();

            self::handleResponse(true, 'Categorías obtenidas con éxito', $categorias);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al obtener categorías: ' . $e->getMessage(), [], 500);
        }
    }

    public static function obtenerIngredientes()
    {
        $mysql = new MySQL();
        $mysql->conectar();
        $producto = new Producto($mysql);
        $ingredientes = $producto->obtenerIngredientes();
        self::handleResponse(true, 'Ingredientes obtenidos', $ingredientes);
    }

    public static function obtenerIngredientesProducto()
    {
        try {
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                self::handleResponse(false, 'ID de producto inválido', [], 400);
                return;
            }

            $id = (int)$_GET['id'];
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error de conexión a la base de datos', [], 500);
                return;
            }

            $producto = new Producto($mysql);
            $ingredientes = $producto->obtenerIngredientesProducto($id);

            self::handleResponse(true, 'Ingredientes obtenidos exitosamente', $ingredientes);
        } catch (Exception $e) {
            error_log("Error al obtener ingredientes del producto: " . $e->getMessage());
            self::handleResponse(false, 'Error al obtener ingredientes', [], 500);
        }
    }

    public static function agregarProducto()
    {
        try {
           
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error de conexión a la base de datos', [], 500);
                return;
            }

            // Validar datos requeridos
            if (empty($_POST['nombre']) || empty($_POST['precio']) || empty($_FILES['imagen'])) {
                self::handleResponse(false, 'Todos los campos son requeridos', [], 400);
                return;
            }

            $directorioBase = dirname(__DIR__, 1);
            $directorioImagenes = $directorioBase . '/assets/images/';

        

            $directorio = realpath($directorioImagenes);

            if ($directorio === false) {
                self::handleResponse(false, 'Error interno al determinar la ruta de imagen', [], 500);
                return;
            }


            $nombreArchivo = time() . '_' . basename($_FILES['imagen']['name']);
            $rutaCompleta = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo;


            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
                self::handleResponse(false, 'Error al subir la imagen', [], 500);
                return;
            }

            $producto = new Producto($mysql);
            // Store relative path in database
            $rutaWeb = 'assets/images/' . $nombreArchivo;
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => $_POST['precio'],
                'imagen' => $rutaWeb, // Store relative path
                'etiqueta' => $_POST['etiqueta'] ?? 1
            ];

            $resultado = $producto->crearProducto($datos);

            if ($resultado) {
                // Guardar ingredientes si vienen en POST
                if (!empty($_POST['ingredientes'])) {
                    $ingredientes = json_decode($_POST['ingredientes'], true);
                    if (is_array($ingredientes)) {
                        $producto->agregarIngredientesProducto($resultado, $ingredientes);
                    }
                }
                // Return complete URL for frontend
                $responseData = [
                    'id' => $resultado,
                    'image_url' => getImageUrl($rutaWeb) // Complete URL for response
                ];
                self::handleResponse(true, 'Producto agregado exitosamente', $responseData);
            } else {
                unlink($rutaCompleta);
                self::handleResponse(false, 'Error al agregar el producto', [], 500);
            }
        } catch (Exception $e) {
            self::handleResponse(false, 'Error: ' . $e->getMessage(), [], 500);
        }
    }

    public static function editarProducto()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                if (ob_get_length()) {
                    ob_clean();
                }
                flush();
                self::handleResponse(false, 'Error de conexión a la base de datos', [], 500);
                return;
            }
            $id = $_POST['id'] ?? null;
            if (!$id || !is_numeric($id)) {
                if (ob_get_length()) {
                    ob_clean();
                }
                flush();
                self::handleResponse(false, 'ID de producto inválido', [], 400);
                return;
            }
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => $_POST['precio'] ?? '',
                'etiqueta' => $_POST['etiqueta'] ?? ''
            ];
            // Procesar imagen si se envía
            if (isset($_FILES['imagen']) && $_FILES['imagen']['tmp_name']) {

                $directorioBase = dirname(__DIR__, 1);
                $directorioImagenes = $directorioBase . '/assets/images/';


                $directorio = realpath($directorioImagenes);

                // Manejar error si realpath falla
                if ($directorio === false) {
                    error_log('Error interno al determinar la ruta de imagen (editar)');
                    self::handleResponse(false, 'Error interno al determinar la ruta de imagen', [], 500);
                    return;
                }

                $nombreArchivo = time() . '_' . basename($_FILES['imagen']['name']);
                $rutaCompleta = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo;

                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
                    if (ob_get_length()) {
                        ob_clean();
                    }
                    flush();
                    self::handleResponse(false, 'Error al subir la imagen', [], 500);
                    return;
                }
                

                // Store relative path in database
                $datos['imagen'] = 'assets/images/' . $nombreArchivo;
            }
            $producto = new Producto($mysql);
            $resultado = $producto->updateProducto($id, $datos);
            if ($resultado) {
                // Actualizar ingredientes si vienen en POST
                if (!empty($_POST['ingredientes'])) {
                    $ingredientes = json_decode($_POST['ingredientes'], true);
                    if (is_array($ingredientes)) {
                        // Eliminar todos los ingredientes actuales del producto
                        $producto->eliminarIngredientesProducto($id);
                        // Insertar los nuevos ingredientes
                        $producto->agregarIngredientesProducto($id, $ingredientes);
                    }
                }
                if (ob_get_length()) {
                    ob_clean();
                }
                flush();
                self::handleResponse(true, 'Producto actualizado exitosamente');
            } else {
                if (ob_get_length()) {
                    ob_clean();
                }
                flush();
                self::handleResponse(false, 'Error al actualizar el producto', [], 500);
            }
        } catch (Exception $e) {
            if (ob_get_length()) {
                ob_clean();
            }
            flush();
            self::handleResponse(false, 'Error: ' . $e->getMessage(), [], 500);
        }
    }

    public static function eliminarProducto()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error de conexión a la base de datos', [], 500);
                return;
            }
            $id = $_POST['id'] ?? null;
            if (!$id || !is_numeric($id)) {
                self::handleResponse(false, 'ID de producto inválido', [], 400);
                return;
            }
            $producto = new Producto($mysql);
            $resultado = $producto->deleteProducto($id);
            if ($resultado === false) {
                self::handleResponse(false, 'Error al eliminar el producto', [], 500);
                return;
            }

            

            self::handleResponse(true, 'Producto eliminado exitosamente');
        } catch (Exception $e) {
            self::handleResponse(false, 'Error: ' . $e->getMessage(), [], 500);
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'eliminarProducto':
            ProductoController::eliminarProducto();
            exit;
            break;
            // Otros casos...
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'obtenerIngredientesProducto':
                ProductoController::obtenerIngredientesProducto();
                break;
                // ... existing cases ...
        }
    } else if (isset($_GET['id'])) {
        $id = filter_var(trim($_GET["id"]), FILTER_SANITIZE_NUMBER_INT);
        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de producto no válido'
            ]);
            exit;
        }
        ProductoController::getProductById($id);
    } else {
        ProductoController::getAllProducts();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'buscarProductos':
            ProductoController::buscarProductos();
            break;
        case 'obtenerCategorias':
            ProductoController::obtenerCategorias();
            break;
        case 'obtenerIngredientes':
            ProductoController::obtenerIngredientes();
            break;
        case 'agregarProducto':
            ProductoController::agregarProducto();
            break;
        case 'editarProducto':
            ProductoController::editarProducto();
            break;
        case 'eliminarProducto':
            ProductoController::eliminarProducto();
            break;
        default:
            ProductoController::handleResponse(false, 'Acción no válida', [], 400);
            break;
    }
}
