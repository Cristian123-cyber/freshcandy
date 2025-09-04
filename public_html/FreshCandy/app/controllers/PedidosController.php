<?php
header('Content-Type: application/json');
require_once '../models/MySQL.php';
require_once '../models/Pedidos.php';

class PedidosController
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

    public static function getDataForUI()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
            }

            $pedidos = new Pedidos($mysql);
            $paymentMethods = $pedidos->getPaymentMethods();

            if (!$paymentMethods || $paymentMethods === false) {
                self::handleResponse(false, 'Error al obtener los métodos de pago mi papa', [], 500);
            }

            $deliveryMethods = $pedidos->getDeliveryMethods();

            if (!$deliveryMethods || $deliveryMethods === false) {
                self::handleResponse(false, 'Error al obtener los métodos de entrega', [], 500);
            }

            if (empty($paymentMethods) && empty($deliveryMethods)) {
                self::handleResponse(false, 'No se encontraron métodos de pago o entrega', [], 404);
            }

            $data = [
                'paymentMethods' => $paymentMethods,
                'deliveryMethods' => $deliveryMethods
            ];

            //Desconectamos de la base de datos
            if (isset($mysql)) {
                $mysql->desconectar();
            }

            self::handleResponse(true, "Métodos de pago y entrega obtenidos correctamente", $data, 200);
        } catch (\Throwable $th) {

            if (isset($mysql)) {
                $mysql->desconectar();
            }
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    public static function validatePromoCode()
    {
        try {
            // Get and decode JSON data
            $data = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                self::handleResponse(false, "JSON inválido", [], 400);
                return;
            }

            if (!isset($data['code']) || empty(trim($data['code']))) {
                self::handleResponse(false, "El código promocional es requerido", [], 400);
                return;
            }


            $code = $data['code'];

            $code = filter_var(trim($code), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (strlen($code) > 50) {
                self::handleResponse(false, "El código promocional excede la longitud máxima permitida", [], 400);
            }

            // Validar que el código solo contenga caracteres alfanuméricos y guiones
            if (!preg_match('/^[a-zA-Z0-9-]+$/', $code)) {
                self::handleResponse(false, "El código promocional contiene caracteres no permitidos", [], 400);
            }



            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $result = $pedidos->validatePromoCode($code);

            if ($result === false) {
                self::handleResponse(false, "Código promocional inválido o expirado", [], 400);
            }

            // Disconnect from database
            if (isset($mysql)) {
                $mysql->desconectar();
            }

            self::handleResponse(true, "Código promocional válido", $result);
        } catch (\Throwable $th) {
            if (isset($mysql)) {
                $mysql->desconectar();
            }
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    private static function validateOrderData($data)
    {
        //Validamos que los campos requeridos estén presentes
        $requiredFields = ['deliveryMethod', 'paymentMethod', 'customer', 'notes', 'infoDescuento'];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data)) {
                return false;
            }
        }

        //Validamos que los campos requeridos en el cliente estén presentes
        $requiredFieldsCustomer = ['name', 'phone', 'cedula'];
        foreach ($requiredFieldsCustomer as $field) {
            if (!array_key_exists($field, $data['customer'])) {
                return false;
            }
        }

        // Validamos que address y city existan en customer (pueden ser null)
        if (!array_key_exists('address', $data['customer']) || !array_key_exists('city', $data['customer'])) {
            return false;
        }

        //Validamos que los campos requeridos en la información del descuento estén presentes
        $requiredFieldsInfoDescuento = ['aplica'];
        foreach ($requiredFieldsInfoDescuento as $field) {
            if (!array_key_exists($field, $data['infoDescuento'])) {
                return false;
            }
        }

        // Validamos que id exista en infoDescuento (puede ser null)
        if (!array_key_exists('id', $data['infoDescuento'])) {
            return false;
        }

        return true;
    }

    private static function sanitizeOrderData($data)
    {
        // Sanitizamos el método de entrega
        $deliveryMethod = filter_var($data['deliveryMethod'], FILTER_SANITIZE_NUMBER_INT);

        //Validamos que el método de entrega sea un número
        if (!is_numeric($deliveryMethod)) {
            return false;
        }

        // Convertimos a entero y validamos el rango
        $deliveryMethod = intval($deliveryMethod);
        if ($deliveryMethod < 1 || $deliveryMethod > 2) {
            return false;
        }

        // Guardamos el valor sanitizado de nuevo en el array
        $data['deliveryMethod'] = $deliveryMethod;

        // Sanitizamos y validamos el método de pago
        $paymentMethod = filter_var($data['paymentMethod'], FILTER_SANITIZE_NUMBER_INT);

        // Validamos que el método de pago sea un número
        if (!is_numeric($paymentMethod)) {
            return false;
        }

        // Convertimos a entero y validamos el rango
        $paymentMethod = intval($paymentMethod);
        if ($paymentMethod < 1 || $paymentMethod > 2) {
            return false;
        }

        // Guardamos el valor sanitizado de nuevo en el array
        $data['paymentMethod'] = $paymentMethod;

        // Sanitizamos las notas y eliminamos cualquier HTML y limitamos la longitud
        $notes = filter_var($data['notes'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (strlen($notes) > 500) {
            return false;
        }
        $data['notes'] = $notes;

        // Sanitizamos y validamos el nombre del cliente
        $name = filter_var(trim($data['customer']['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (strlen($name) < 2 || strlen($name) > 100) {
            return false;
        }
        $data['customer']['name'] = $name;

        // Sanitizamos y validamos el teléfono del cliente
        $phone = filter_var(trim($data['customer']['phone']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // Remove any non-numeric characters except +, -, spaces, and parentheses
        $phone = preg_replace('/[^0-9+\-\s()]/', '', $phone);
        if (!preg_match('/^[0-9+\-\s()]{7,15}$/', $phone)) {
            return false;
        }
        $data['customer']['phone'] = $phone;

        // Sanitizamos y validamos la cédula del cliente
        $cedula = filter_var(trim($data['customer']['cedula']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // Remove any non-numeric characters
        $cedula = preg_replace('/[^0-9]/', '', $cedula);
        if (!preg_match('/^[0-9]{8,12}$/', $cedula)) {
            return false;
        }
        $data['customer']['cedula'] = $cedula;

        // Validamos dirección y ciudad solo si el método de entrega es 1 (envío a domicilio)
        if ($deliveryMethod === 1) {
            // Sanitizamos y validamos la dirección del cliente
            $address = filter_var(trim($data['customer']['address']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (strlen($address) < 5 || strlen($address) > 300) {
                return false;
            }
            $data['customer']['address'] = $address;

            // Sanitizamos y validamos la ciudad del cliente
            $city = filter_var(trim($data['customer']['city']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (strlen($city) < 2 || strlen($city) > 100) {
                return false;
            }
            $data['customer']['city'] = $city;
        } else {
            // Si el método de entrega es 2 (recogida en tienda), establecemos dirección y ciudad como null
            $data['customer']['address'] = null;
            $data['customer']['city'] = null;
        }

        // Sanitizamos y validamos la información del descuento
        $discountId = filter_var($data['infoDescuento']['id'], FILTER_SANITIZE_NUMBER_INT);

        // Validamos que el id del descuento sea un número
        if (!is_numeric($discountId)) {
            return false;
        }

        // Convertimos a entero y validamos el rango
        $discountId = intval($discountId);
        if ($discountId < 0) {
            return false;
        }

        // Guardamos el valor sanitizado de nuevo en el array
        $data['infoDescuento']['id'] = $discountId;

        // Si el descuento aplica, validamos los campos adicionales del descuento
        // Sanitizamos y validamos el campo aplica
        $aplica = (bool)$data['infoDescuento']['aplica'];
        if (!$aplica) {
            $data['infoDescuento']['id'] = 0;
        }



        $data['infoDescuento']['aplica'] = $aplica;

        return $data;
    }



    public static function createOrder()
    {

        try {
            // Get and decode JSON data
            $data = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                self::handleResponse(false, "JSON inválido", [], 400);
                return;
            }



            $isValid = self::validateOrderData($data);



            if (!$isValid) {
                self::handleResponse(false, "Datos inválidos 1", [], 400);
                return;
            }


            $sanitizedData = self::sanitizeOrderData($data);


            if (!$sanitizedData) {
                self::handleResponse(false, "Datos inválidos 2", [], 400);
            }


            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
            }

            $pedidos = new Pedidos($mysql);
            $result = $pedidos->createOrder($sanitizedData);


            if (!$result['success']) {
                self::handleResponse(false, $result['message'], [], 500);
            }

            $pedido = $pedidos->getOrderById($result["pedidoId"]);

            if (!$pedido || $pedido === null) {
                self::handleResponse(false, "Error al obtener el pedido", [], 500);
            }

            $idPedido = $pedido["id"];







            self::handleResponse(true, "Pedido creado con éxito", $pedido, 200);
        } catch (\Throwable $th) {
            self::handleResponse(false, "Error interno del servidor mi hermano", [], 500);
        }
    }

    public static function getPedidos()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                self::handleResponse(false, "JSON inválido", [], 400);
                return;
            }

            $fecha = isset($data['fecha']) ? filter_var(trim($data['fecha']), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
            $estado = isset($data['estado']) && $data['estado'] !== null && $data['estado'] !== '' ? filter_var($data['estado'], FILTER_SANITIZE_NUMBER_INT) : null;
            $tituloOcontenido = isset($data['tituloOcontenido']) ? filter_var(trim($data['tituloOcontenido']), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

            if ($fecha && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                self::handleResponse(false, "Formato de fecha inválido. Use YYYY-MM-DD", [], 400);
                return;
            }

            if ($estado !== null && $estado !== '' && (!is_numeric($estado) || $estado < 1)) {
                self::handleResponse(false, "Estado inválido", [], 400);
                return;
            }

            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $result = $pedidos->getPedidos($fecha, $estado, $tituloOcontenido);

            file_put_contents('debug.log', print_r($result, true), FILE_APPEND);




            if ($result === false) {
                $debugInfo = [
                    'sql' => $pedidos->lastQuery ?? '',
                    'params' => $pedidos->lastParams ?? [],
                    'tipos' => $pedidos->lastTipos ?? ''
                ];
                file_put_contents('debug.log', print_r($debugInfo, true), FILE_APPEND);
                self::handleResponse(false, "Error al obtener los pedidos.", [], 500);
                return;
            }

            if (isset($mysql)) {
                $mysql->desconectar();
            }

            // Si no hay resultados, retornamos un array vacío con éxito
            self::handleResponse(true, empty($result) ? "No se encontraron pedidos" : "Pedidos obtenidos correctamente", $result ?? []);
        } catch (\Throwable $th) {
            if (isset($mysql)) {
                $mysql->desconectar();
            }
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }
    private function validateStockAvailability($orderId)
    {
        try {
            $insufficientIngredients = [];
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            // Obtener los productos del pedido
            $sql = "SELECT Productos_id_producto, cantidad 
            FROM pedidos_has_productos 
            WHERE Pedidos_id_pedido = ?";

            $orderProducts = $mysql->prepararConsultaSelect($sql, "i", [$orderId]);

            if (!$orderProducts) {
                return ['success' => false, 'message' => 'No se pudieron obtener los productos del pedido'];
            }

            // Para cada producto en el pedido
            foreach ($orderProducts as $product) {
                $productId = $product['Productos_id_producto'];
                $productQuantity = $product['cantidad'];

                // Obtener ingredientes necesarios para este producto
                $sql = "SELECT 
                    phi.Ingredientes_id_ingrediente, 
                    phi.cantidad as cantidad_necesaria,
                    i.stock_ing as stock_actual,
                    i.nombre_ingrediente
                FROM productos_has_ingredientes phi
                INNER JOIN ingredientes i ON phi.Ingredientes_id_ingrediente = i.id_ingrediente
                WHERE phi.Productos_id_producto = ?";

                $productIngredients = $mysql->prepararConsultaSelect($sql, "i", [$productId]);

                if (!$productIngredients) {
                    continue;
                }

                // Validar cada ingrediente
                foreach ($productIngredients as $ingredient) {
                    $totalNeeded = $ingredient['cantidad_necesaria'] * $productQuantity;
                    $stockActual = $ingredient['stock_actual'];

                    if ($stockActual < $totalNeeded) {
                        $insufficientIngredients[] = [
                            'id' => $ingredient['Ingredientes_id_ingrediente'],
                            'nombre' => $ingredient['nombre_ingrediente'],
                            'stock_actual' => $stockActual,
                            'cantidad_necesaria' => $totalNeeded,
                            'faltante' => $totalNeeded - $stockActual
                        ];
                    }
                }
            }

            if (!empty($insufficientIngredients)) {
                $ingredientNames = array_column($insufficientIngredients, 'nombre');
                $message = "Stock insuficiente para los ingredientes: " . implode(', ', $ingredientNames);

                return [
                    'success' => false,
                    'message' => $message,
                    'insufficient_ingredients' => $insufficientIngredients
                ];
            }

            return ['success' => true, 'message' => 'Stock suficiente'];
        } catch (Exception $e) {
            error_log("Error validating stock availability: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error validando disponibilidad de stock'];
        }
    }


    public static function updateOrderStatus()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                self::handleResponse(false, "JSON inválido", [], 400);
                return;
            }

            $id = isset($data['orderId']) ? intval($data['orderId']) : null;
            $nuevoEstado = isset($data['statusId']) ? intval($data['statusId']) : null;

            if (!$id || !$nuevoEstado) {
                self::handleResponse(false, "ID de pedido o estado inválido", [], 400);
                return;
            }

            if ($nuevoEstado < 1 || $nuevoEstado > 5) {
                self::handleResponse(false, "Estado inválido", [], 400);
                return;
            }

            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);

            // Obtener el estado actual del pedido
            $resultEstadoPedido = $pedidos->getOrderStatusById($id);

            if (!$resultEstadoPedido || $resultEstadoPedido === null) {
                $mysql->desconectar();
                self::handleResponse(false, "ERROR AL OBTENER ESTADO DE PEDIDO", [], 404);
                return;
            }

            $estadoActual = intval($resultEstadoPedido['estado']);

            // Verificar si realmente hay un cambio de estado
            if ($estadoActual === $nuevoEstado) {
                $mysql->desconectar();
                self::handleResponse(true, "El pedido ya tiene ese estado", []);
                return;
            }

            
            $result = $pedidos->updateOrderStatus($id, $nuevoEstado);

            if (!$result) {
                $mysql->desconectar();
                self::handleResponse(false, "No se pudo actualizar el estado", [], 500);
                return;
            }

            // 2. Actualizar el stock de ingredientes (MANTENER CONEXIÓN ACTIVA)
            $resultUpdateStock = $pedidos->updateIngredientStock($id, $nuevoEstado, $estadoActual);

            // Ahora sí desconectar después de todas las operaciones
            $mysql->desconectar();

            if ($resultUpdateStock === false) {
                self::handleResponse(false, "Error al actualizar el stock de ingredientes", [], 500);
                return;
            }

            self::handleResponse(true, "Estado actualizado correctamente", [
                'orderId' => $id,
                'oldStatus' => $estadoActual,
                'newStatus' => $nuevoEstado
            ]);
        } catch (Exception $e) {
            error_log("Error en updateOrderStatus: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
            self::handleResponse(false, "Error interno: " . $e->getMessage(), [], 500);
        }
    }


    public static function getOrderStates()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $states = $pedidos->getOrderStates();

            $mysql->desconectar();

            if ($states === false) {
                self::handleResponse(false, "Error al obtener los estados de pedido.", [], 500);
                return;
            }
            if (!is_array($states)) {
                error_log("Modelo getOrderStates retornó un tipo inesperado: " . gettype($states));
                self::handleResponse(false, "Error interno al procesar los estados.", [], 500);
                return;
            }

            self::handleResponse(true, "Estados de pedido obtenidos correctamente", $states);
        } catch (Exception $e) {
            error_log("Excepción en getOrderStates: " . $e->getMessage() . " en " . $e->getFile() . " linea " . $e->getLine());
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    public static function getSalesByWeek()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $data = $pedidos->getSalesByWeek();

            $mysql->desconectar();

            if ($data === false) {
                self::handleResponse(false, "Error al obtener las ventas por semana", [], 500);
                return;
            }

            self::handleResponse(true, "Ventas por semana obtenidas correctamente", $data);
        } catch (Exception $e) {
            error_log("Error en getSalesByWeek: " . $e->getMessage());
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    public static function getSalesByMonth()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $data = $pedidos->getSalesByMonth();

            $mysql->desconectar();

            if ($data === false) {
                self::handleResponse(false, "Error al obtener las ventas por mes", [], 500);
                return;
            }

            self::handleResponse(true, "Ventas por mes obtenidas correctamente", $data);
        } catch (Exception $e) {
            error_log("Error en getSalesByMonth: " . $e->getMessage());
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    public static function getSalesByYear()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $data = $pedidos->getSalesByYear();

            $mysql->desconectar();

            if ($data === false) {
                self::handleResponse(false, "Error al obtener las ventas por año", [], 500);
                return;
            }

            self::handleResponse(true, "Ventas por año obtenidas correctamente", $data);
        } catch (Exception $e) {
            error_log("Error en getSalesByYear: " . $e->getMessage());
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    public static function getPopularProducts()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $data = $pedidos->getPopularProducts();

            $mysql->desconectar();

            if ($data === false) {
                self::handleResponse(false, "Error al obtener los productos populares", [], 500);
                return;
            }

            self::handleResponse(true, "Productos populares obtenidos correctamente", $data);
        } catch (Exception $e) {
            error_log("Error en getPopularProducts: " . $e->getMessage());
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    public static function getOrderDetails()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                self::handleResponse(false, "JSON inválido", [], 400);
                return;
            }

            $orderId = isset($data['orderId']) ? intval($data['orderId']) : null;
            if (!$orderId) {
                self::handleResponse(false, "ID de pedido inválido", [], 400);
                return;
            }

            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $orderDetails = $pedidos->getOrderDetails($orderId);

            $mysql->desconectar();

            if ($orderDetails === false) {
                self::handleResponse(false, "Error al obtener los detalles del pedido aca pa", [], 500);
                return;
            }

            self::handleResponse(true, "Detalles del pedido obtenidos correctamente", $orderDetails);
        } catch (Exception $e) {
            error_log("Error en getOrderDetails: " . $e->getMessage());
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }

    public static function getRecentOrders()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $pedidos = new Pedidos($mysql);
            $result = $pedidos->getRecentOrders();

            $mysql->desconectar();

            if ($result === false) {
                self::handleResponse(false, "Error al obtener los pedidos recientes", [], 500);
                return;
            }

            self::handleResponse(true, "Pedidos recientes obtenidos correctamente", $result);
        } catch (Exception $e) {
            error_log("Error en getRecentOrders: " . $e->getMessage());
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }
}

// Punto de entrada para las peticiones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? null;

    if (!$action) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'No se especificó la acción']);
        exit;
    }

    $action = filter_var(trim($action), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Aquí irá tu switch para manejar las diferentes acciones
    switch ($action) {
        case 'getDataForUI':
            PedidosController::getDataForUI();
            break;
        case 'validatePromoCode':
            PedidosController::validatePromoCode();
            break;
        case 'createOrder':
            PedidosController::createOrder();
            break;
        case 'getPedidos':
            PedidosController::getPedidos();
            break;
        case 'updateOrderStatus':
            PedidosController::updateOrderStatus();
            break;
        case 'getOrderStates':
            PedidosController::getOrderStates();
            break;
        case 'getSalesByWeek':
            PedidosController::getSalesByWeek();
            break;
        case 'getSalesByMonth':
            PedidosController::getSalesByMonth();
            break;
        case 'getSalesByYear':
            PedidosController::getSalesByYear();
            break;
        case 'getPopularProducts':
            PedidosController::getPopularProducts();
            break;
        case 'getOrderDetails':
            PedidosController::getOrderDetails();
            break;
        case 'getRecentOrders':
            PedidosController::getRecentOrders();
            break;
        default:
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['success' => false, 'message' => 'Endpoint no encontrado']);
            exit;
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}
