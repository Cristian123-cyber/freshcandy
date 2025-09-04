<?php

header('Content-Type: application/json');
require_once '../models/MySQL.php';
require_once '../models/User.php';

class UserController
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

    private static function sanitizeChangePasswordData($data)
    {
        $sanitized = [];

        // Sanitizar current_password
        $current_password = trim($data['current_password']);
        if (empty($current_password)) {
            return false;
        }
        $sanitized['current_password'] = $current_password;

        // Sanitizar new_password
        $new_password = trim($data['new_password']);
        if (empty($new_password)) {
            return false;
        }

        // Validar formato de la nueva contraseña
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
            return false;
        }

        // Sanitizar confirm_password
        $confirm_password = trim($data['confirm_password']);
        if (empty($confirm_password)) {
            return false;
        }
        $sanitized['confirm_password'] = $confirm_password;

        // Validar que la nueva contraseña sea diferente a la actual
        if ($current_password === $new_password) {
            return false;
        }

        $sanitized['new_password'] = $new_password;

        return $sanitized;
    }

    private static function sanitizeUpdateProfileData($data)
    {
        $sanitized = [];

        // Sanitizar teléfono
        $phone = filter_var(trim($data['phone']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = preg_replace('/[^0-9+\-\s()]/', '', $phone);
        if (empty($phone)) {
            return false;
        }
        // Validar longitud del teléfono (entre 7 y 15 dígitos)
        $phoneDigits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phoneDigits) < 7 || strlen($phoneDigits) > 15) {
            return false;
        }
        $sanitized['phone'] = $phone;

        // Sanitizamos y validamos la dirección del cliente
        $address = filter_var(trim($data['address']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (strlen($address) < 5 || strlen($address) > 300) {
            return false;
        }
        $sanitized['address'] = $address;

        return $sanitized;
    }

    

   

    private static function validateChangePassword($data)
    {
        $requiredFields = ['current_password', 'new_password', 'confirm_password'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }
        return true;
    }

    private static function validateUpdateProfile($data)
    {
        $requiredFields = ['phone', 'address'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }
        return true;
    }

    public static function changePassword()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::handleResponse(false, "JSON inválido", [], 400);
        }

        if (!self::validateChangePassword($data)) {
            self::handleResponse(false, "Datos inválidos ACA", [], 400);
        }

        $validatedData = self::sanitizeChangePasswordData($data);
        if ($validatedData === false) {
            self::handleResponse(false, "Datos inválidos", [], 400);
        }



        

        $mysql = new MySQL();
        $mysql->conectar();

        
        if (!$mysql->getStatusConexion()) {
            self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
            return;
        }
        
        $user = new User($mysql);

        
        $success = $user->changePassword($validatedData);
        
        if ($success) {
            self::handleResponse(true, "Contraseña actualizada correctamente", [], 200);
        } else {
            self::handleResponse(false, "Credenciales incorrectas", [], 401);
        }
    }

    public static function updateProfile()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::handleResponse(false, "JSON inválido", [], 400);
            return;
        }

        if (!self::validateUpdateProfile($data)) {
            self::handleResponse(false, "Datos inválidos", [], 400);
        }

        $validatedData = self::sanitizeUpdateProfileData($data);
        if ($validatedData === false) {
            self::handleResponse(false, "Datos inválidos", [], 400);
        }

        $mysql = new MySQL();
        $mysql->conectar();

        if (!$mysql->getStatusConexion()) {
            self::handleResponse(false, "Error al conectar con la base de datos", [], 500);
        }
        
        $user = new User($mysql);
        $success = $user->updateProfile($validatedData);

        if ($success) {
            self::handleResponse(true, "Perfil actualizado correctamente", [], 200);
        } else {
            self::handleResponse(false, "Error al actualizar el perfil", [], 500);
        }
    }



}

// Verificamos si la petición es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? null;

    //sanitizamos la acción
    if (!$action) {
        UserController::handleResponse(false, 'No se especificó la acción', [], 400);
        exit;
    }
    $action = filter_var(trim($action), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //Enrutamos la accion con el switch llamando a la funcion correspondiente
    switch ($action) {
        case 'changePassword':
            UserController::changePassword();
            break;
        case 'updateProfile':
            UserController::updateProfile();
            break;
        default:
            UserController::handleResponse(false, 'Endpoint no encontrado', [], 404);
            exit;
    }
} else {
    UserController::handleResponse(false, 'Método no permitido', [], 405);
    exit;
}
