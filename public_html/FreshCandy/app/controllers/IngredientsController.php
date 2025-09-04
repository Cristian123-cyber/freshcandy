<?php

header('Content-Type: application/json');
require_once '../models/MySQL.php';
require_once '../models/Ingredients.php';

class IngredientsController
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

    public static function getAllIngredients()
    {
        try {
            // Conexión a la base de datos
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }

            // Creamos una instancia del modelo de ingredientes
            $ingredientsModel = new Ingredients($mysql);

            // Obtenemos todos los ingredientes
            $ingredients = $ingredientsModel->getAllIngredients();

            if ($ingredients === false) {
                self::handleResponse(false, 'Error al obtener los ingredientes', [], 500);
                return;
            }

            self::handleResponse(true, 'Ingredientes obtenidos con éxito', [
                'ingredients' => $ingredients
            ]);
        } catch (Exception $e) {
            error_log("Error en getAllIngredients: " . $e->getMessage());
            self::handleResponse(false, 'Error interno del servidor', [], 500);
        } finally {
            // Desconectamos de la base de datos
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    public static function getIngredientById($id)
    {
        try {
            $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {
                self::handleResponse(false, "ID de ingrediente inválido", [], 400);
                return;
            }

            $mysql = new MySQL();
            $ingredients = new Ingredients($mysql);
            $result = $ingredients->getIngredientById($id);

            if ($result === false) {
                self::handleResponse(false, "Ingrediente no encontrado", [], 404);
                return;
            }

            self::handleResponse(true, "Ingrediente encontrado", ["ingredient" => $result]);
        } catch (Exception $e) {
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->close();
            }
        }
    }

    public static function getCategories()
    {
        try {
            $mysql = new MySQL();
            $ingredients = new Ingredients($mysql);
            $result = $ingredients->getCategories();

            if ($result === false) {
                self::handleResponse(false, "Error al obtener las categorías", [], 500);
                return;
            }

            self::handleResponse(true, "Categorías obtenidas con éxito", ["categories" => $result]);
        } catch (Exception $e) {
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->close();
            }
        }
    }

    public static function getUnits()
    {
        try {
            $mysql = new MySQL();
            $ingredients = new Ingredients($mysql);
            $result = $ingredients->getUnits();

            if ($result === false) {
                self::handleResponse(false, "Error al obtener las unidades", [], 500);
                return;
            }

            self::handleResponse(true, "Unidades obtenidas con éxito", ["units" => $result]);
        } catch (Exception $e) {
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->close();
            }
        }
    }

    private static function validateUpdateIngredient($data)
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            return false;
        }
        if (!isset($data['name']) || empty(trim($data['name']))) {
            return false;
        }
        if (!isset($data['category']) || !is_numeric($data['category'])) {
            return false;
        }
        if (!isset($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
            return false;
        }
        if (!isset($data['unit']) || !is_numeric($data['unit'])) {
            return false;
        }
        if (!isset($data['criticalLevel']) || !is_numeric($data['criticalLevel']) || $data['criticalLevel'] < 0) {
            return false;
        }
        if (!isset($data['lowLevel']) || !is_numeric($data['lowLevel']) || $data['lowLevel'] < 0) {
            return false;
        }
        if ($data['lowLevel'] <= $data['criticalLevel']) {
            return false;
        }
        return true;
    }

    private static function sanitizeUpdateIngredientData($data)
    {
        return [
            'id' => filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT),
            'name' => htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8'),
            'category' => filter_var($data['category'], FILTER_SANITIZE_NUMBER_INT),
            'stock' => filter_var($data['stock'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'unit' => filter_var($data['unit'], FILTER_SANITIZE_NUMBER_INT),
            'criticalLevel' => filter_var($data['criticalLevel'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'lowLevel' => filter_var($data['lowLevel'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
        ];
    }

    public static function updateIngredient()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::handleResponse(false, "JSON inválido", [], 400);
            return;
        }

        if (!self::validateUpdateIngredient($data)) {
            self::handleResponse(false, "Datos inválidos", [], 400);
            return;
        }

        $validatedData = self::sanitizeUpdateIngredientData($data);

        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $ingredients = new Ingredients($mysql);
            $success = $ingredients->updateIngredient($validatedData);

            if ($success) {
                self::handleResponse(true, "Ingrediente actualizado correctamente");
            } else {
                self::handleResponse(false, "Error al actualizar el ingrediente", [], 500);
            }
        } catch (Exception $e) {
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    private static function validateAddIngredient($data)
    {
        if (!isset($data['name']) || empty(trim($data['name']))) {
            return false;
        }
        if (!isset($data['category']) || !is_numeric($data['category'])) {
            return false;
        }
        if (!isset($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
            return false;
        }
        if (!isset($data['unit']) || !is_numeric($data['unit'])) {
            return false;
        }
        if (!isset($data['criticalLevel']) || !is_numeric($data['criticalLevel']) || $data['criticalLevel'] < 0) {
            return false;
        }
        if (!isset($data['lowLevel']) || !is_numeric($data['lowLevel']) || $data['lowLevel'] < 0) {
            return false;
        }
        if ($data['lowLevel'] <= $data['criticalLevel']) {
            return false;
        }
        return true;
    }

    private static function sanitizeAddIngredientData($data)
    {
        return [
            'name' => htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8'),
            'category' => filter_var($data['category'], FILTER_SANITIZE_NUMBER_INT),
            'stock' => filter_var($data['stock'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'unit' => filter_var($data['unit'], FILTER_SANITIZE_NUMBER_INT),
            'criticalLevel' => filter_var($data['criticalLevel'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'lowLevel' => filter_var($data['lowLevel'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
        ];
    }

    public static function addIngredient()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::handleResponse(false, "JSON inválido", [], 400);
            return;
        }

        if (!self::validateAddIngredient($data)) {
            self::handleResponse(false, "Datos inválidos", [], 400);
            return;
        }

        $validatedData = self::sanitizeAddIngredientData($data);

        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $ingredients = new Ingredients($mysql);
            $success = $ingredients->addIngredient($validatedData);

            if ($success) {
                self::handleResponse(true, "Ingrediente agregado correctamente", [], 200);
            } else {
                self::handleResponse(false, "Error al agregar el ingrediente", [], 500);
            }
        } catch (Exception $e) {
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    private static function validateRestockIngredient($data)
    {
        if (!isset($data['ingredientId']) || !is_numeric($data['ingredientId'])) {
            return false;
        }
        if (!isset($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity'] <= 0) {
            return false;
        }
        return true;
    }

    private static function sanitizeRestockIngredientData($data)
    {
        return [
            'ingredientId' => filter_var($data['ingredientId'], FILTER_SANITIZE_NUMBER_INT),
            'quantity' => filter_var($data['quantity'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
        ];
    }

    public static function restockIngredient()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::handleResponse(false, "JSON inválido", [], 400);
            return;
        }

        if (!self::validateRestockIngredient($data)) {
            self::handleResponse(false, "Datos inválidos", [], 400);
            return;
        }

        $validatedData = self::sanitizeRestockIngredientData($data);

        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $ingredients = new Ingredients($mysql);
            $success = $ingredients->restockIngredient($validatedData);

            if ($success) {
                self::handleResponse(true, "Ingrediente reabastecido correctamente");
            } else {
                self::handleResponse(false, "Error al reabastecer el ingrediente", [], 500);
            }
        } catch (Exception $e) {
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    private static function validateDeleteIngredient($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            return false;
        }
        return true;
    }

    public static function deleteIngredient()
    {
        $id = $_GET['id'] ?? null;

        if (!self::validateDeleteIngredient($id)) {
            self::handleResponse(false, "ID de ingrediente inválido", [], 400);
            return;
        }

        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $ingredients = new Ingredients($mysql);
            $success = $ingredients->deleteIngredient($id);

            if ($success['success']) {
                self::handleResponse(true, $success['message']);
            } else {
                self::handleResponse(false, $success['message'], [], 500);
            }
        } catch (Exception $e) {
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    public static function getStockStates()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $ingredients = new Ingredients($mysql);
            $states = $ingredients->getStockStates();

            if ($states === false) {
                self::handleResponse(false, "Error al obtener los estados de stock", [], 500);
                return;
            }

            self::handleResponse(true, "Estados de stock obtenidos con éxito", ["states" => $states]);
        } catch (Exception $e) {
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    public static function searchIngredients()
    {
        try {
            $searchTerm = $_GET['search'] ?? '';
            $categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? filter_var($_GET['category'], FILTER_SANITIZE_NUMBER_INT) : null;
            $stateId = isset($_GET['state']) && $_GET['state'] !== '' ? filter_var($_GET['state'], FILTER_SANITIZE_NUMBER_INT) : null;

            error_log("Search Term: " . $searchTerm);
            error_log("Category ID: " . ($categoryId ?? 'null'));
            error_log("State ID: " . ($stateId ?? 'null'));

            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $ingredients = new Ingredients($mysql);
            $results = $ingredients->searchIngredients($searchTerm, $categoryId, $stateId);

            if (!$results['success']) {
                self::handleResponse(false, $results['message'], [], 500);
                return;
            }

            self::handleResponse(true, "Búsqueda realizada con éxito", ["ingredients" => $results['data']]);
        } catch (Exception $e) {
            error_log("Error en searchIngredients: " . $e->getMessage());
            self::handleResponse(false, "Error en el servidor: " . $e->getMessage(), [], 500);
        } finally {
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    public static function getLowStockIngredients()
    {
        try {
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
                return;
            }

            $ingredients = new Ingredients($mysql);
            $result = $ingredients->getLowStockIngredients();

            $mysql->desconectar();

            if ($result === false) {
                self::handleResponse(false, "Error al obtener los ingredientes con stock bajo", [], 500);
                return;
            }

            self::handleResponse(true, "Ingredientes con stock bajo obtenidos correctamente", $result);
        } catch (Exception $e) {
            error_log("Error en getLowStockIngredients: " . $e->getMessage());
            self::handleResponse(false, "Error interno del servidor", [], 500);
        }
    }
}

// Verificamos si la petición es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? null;

    //sanitizamos la acción
    if (!$action) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'No se especificó la acción']);
        exit;
    }
    $action = filter_var(trim($action), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //Enrutamos la accion con el switch llamando a la funcion correspondiente
    switch ($action) {
        case 'getAllIngredients':
            IngredientsController::getAllIngredients();
            break;
        case 'getIngredientById':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                self::handleResponse(false, "ID de ingrediente inválido", [], 400);
                return;
            }
            IngredientsController::getIngredientById($id);
            break;
        case 'getCategories':
            IngredientsController::getCategories();
            break;
        case 'getUnits':
            IngredientsController::getUnits();
            break;
        case 'updateIngredient':
            IngredientsController::updateIngredient();
            break;
        case 'addIngredient':
            IngredientsController::addIngredient();
            break;
        case 'restockIngredient':
            IngredientsController::restockIngredient();
            break;
        case 'deleteIngredient':
            IngredientsController::deleteIngredient();
            break;
        case 'getStockStates':
            IngredientsController::getStockStates();
            break;
        case 'searchIngredients':
            IngredientsController::searchIngredients();
            break;
        case 'getLowStockIngredients':
            IngredientsController::getLowStockIngredients();
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
