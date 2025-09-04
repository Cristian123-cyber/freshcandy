<?php
    
header('Content-Type: application/json');
require_once '../models/MySQL.php';
require_once '../models/User.php';
require_once '../models/Admin.php';

class AuthController
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

    //Funcion para registrar un usuario
    public static function register()
    {
        try {
            // Obtener y validar datos JSON
            $data = json_decode(file_get_contents('php://input'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                self::handleResponse(false, 'JSON inválido', [], 400);
                return;
            }

            // Validar campos obligatorios
            $requiredFields = ['name', 'cedula', 'email', 'password', 'password_confirmation'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    self::handleResponse(false, "El campo $field es requerido", [], 400);
                    return;
                }
            }

            // Sanitizar inputs
            $name = filter_var(trim($data['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cedula = filter_var(trim($data['cedula']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_var(strtolower(trim($data['email'])), FILTER_SANITIZE_EMAIL);
            $password = trim($data['password']);
            $password_confirmation = trim($data['password_confirmation']);

            // Validaciones específicas
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                self::handleResponse(false, 'Email no válido', [], 400);
            }


            if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
                self::handleResponse(false, 'La contraseña debe tener al menos una mayúscula, una minúscula, un número y un símbolo', [], 400);
            }

            if (strlen($password) < 8) {
                self::handleResponse(false, 'La contraseña debe tener al menos 8 caracteres', [], 400);
            }

            if ($password !== $password_confirmation) {
                self::handleResponse(false, 'Las contraseñas no coinciden', [], 400);
            }



            // Conexión a la base de datos
            $mysql = new MySQL();
            $mysql->conectar();

            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
                return;
            }


            // Creamos una instancia del modelo de usuario
            $userModel = new User($mysql);

            // Verificar si el email ya existe
            // Escapar el email para evitar inyecciones SQL
            $escapedEmail = $mysql->escapar($email);

            // Verificar si el email ya existe
            if ($userModel->emailExists($escapedEmail)) {
                self::handleResponse(false, 'Ya existe un usuario con estos datos', [], 409);
                return;
            }

            // Escapar la cédula para evitar inyecciones SQL
            $escapedCedula = $mysql->escapar($cedula);

            // Verificar si la cédula ya existe
            if ($userModel->cedulaExists($escapedCedula)) {
                self::handleResponse(false, 'Ya existe un usuario con estos datos', [], 409);
                return;
            }

            // Registrar usuario
            if ($userModel->register($name, $escapedCedula, $escapedEmail, $password)) {

                //Obtener los datos del usuario
                $userData = $userModel->getUserByEmail($escapedEmail);

                //Guardamos la session
                self::guardarSession($userData);


                self::handleResponse(true, 'Usuario registrado con éxito', [
                    'user' => [
                        'name' => $name,
                        'email' => $email
                    ],
                    'redirect' => 'views/users/home.php'
                ]);
            } else {
                self::handleResponse(false, 'Error al registrar el usuario bb', [], 500);
            }

            //Desconectamos de la base de datos
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        } catch (Exception $e) {
            error_log("Error en registro: " . $e->getMessage());
            self::handleResponse(false, 'Error interno del servidor' . $e->getMessage(), [], 500);
        }
    }


    //Funcion para iniciar sesión
    public static function login()
    {
        // Capturamos el cuerpo de la petición
        // y lo decodificamos como un array asociativo con json_decode
        // Usamos 'php://input' para obtener el cuerpo de la petición
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificamos si hubo un error al decodificar el JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            self::handleResponse(false, 'JSON inválido', [], 400);
        }

        // Validaciones básicas
        if (empty($data['email']) || empty($data['password'])) {
            self::handleResponse(false, 'Email y contraseña son requeridos', [], 400);
        }

        // Sanitizamos los datos
        // Usamos htmlspecialchars para evitar XSS
        $email = filter_var(strtolower(trim($data['email'])), FILTER_SANITIZE_EMAIL);
        $password = trim($data["password"]);

        // Validamos la contraseña
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            self::handleResponse(false, 'La contraseña debe tener al menos una mayúscula, una minúscula, un número y un símbolo', [], 400);
        }

        // Validamos el formato del email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::handleResponse(false, 'Email no válido', [], 400);
        }

        // Conexión a la base de datos
        $mysql = new MySQL();
        $mysql->conectar();

        //verificamos el estado de la conexion
        if (!$mysql->getStatusConexion()) {

            self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
        }

        // Creamos una instancia del modelo de usuario
        $userModel = new User($mysql);
        // y escapamos el email para evitar inyecciones SQL
        $escapedEmail = $mysql->escapar($email);

        // Verificamos si el email pertenece a un cliente o administrador
        // y llamamos a la función correspondiente
        if ($userModel->isClientEmail($escapedEmail)) {

            if ($userModel->verifyClientLogin($escapedEmail, $password)) {
                // Login exitoso
                // Obtenemos los datos del cliente
                // y los enviamos como respuesta
                $userData = $userModel->getUserByEmail($escapedEmail);
                if (!$userData || $userData === null) {
                    self::handleResponse(false, 'Error al obtener los datos del usuario', [], 500);
                }

                //Guardamos la session
                self::guardarSession($userData);


                self::handleResponse(true, 'Inicio de sesión exitoso', [
                    'user' => $userData,
                    'redirect' => 'views/users/home.php'
                ]);
            } else {
                // Credenciales incorrectas
                self::handleResponse(false, 'Credenciales incorrectas', [], 401);
            }
        } else if ($userModel->isAdminEmail($escapedEmail)) {
            if ($userModel->verifyAdminLogin($escapedEmail, $password)) {
                // Login exitoso
                // Obtenemos los datos del administrador
                // y los enviamos como respuesta
                $adminData = $userModel->getAdminByEmail($escapedEmail);
                if (!$adminData || $adminData === null) {
                    self::handleResponse(false, 'Error al obtener los datos del administrador', [], 500);
                }

                //Guardamos la session
                self::guardarSession($adminData);

                self::handleResponse(true, 'Inicio de sesión exitoso', [
                    'user' => $adminData,
                    'redirect' => 'views/adminV2/adminView.php'
                ]);
            } else {

                // Credenciales incorrectas
                // Enviamos una respuesta de error
                self::handleResponse(false, 'Credenciales incorrectas', [], 401);
            }
        } else {

            // Email no encontrado
            // Enviamos una respuesta de error
            self::handleResponse(false, 'Email no encontrado', [], 404);
        }

        // Desconectamos de la base de datos
        if (isset($mysql)) {
            $mysql->desconectar();
        }
    }


    //Funcion para verificar si el usuario está autenticado desde el js
    public static function checkAuth($role)
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user']) && $_SESSION['user']['logged_in'] && $_SESSION['user']['role'] === $role) {
            self::handleResponse(true, 'Usuario autenticado', [
                'user' => $_SESSION['user']
            ]);
        } else {
            self::handleResponse(false, 'No autenticado', [], 401);
        }
    }

    //Funcion para cerrar sesión

    public static function logout()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Destruir todas las variables de sesión
            $_SESSION = array();



            // Destruir la sesión
            session_destroy();

            self::handleResponse(true, 'Sesión cerrada', [
                'redirect' => 'views/users/home.php'
            ]);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error al cerrar sesión', [], 500);
        }
    }


    //Funcion para obtener los datos del usuario
    public static function getUserData()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }



            if (empty($_SESSION['user'])) {
                self::handleResponse(false, 'No hay usuario autenticado', [], 401);
            }

            $userId = $_SESSION['user']['id'];

            //Sanitizamos el id
            $userId = filter_var(trim($userId), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            //Verificamos si el id es un número
            if (!is_numeric($userId)) {
                self::handleResponse(false, 'El id no es válido', [], 400);
            }

            $userId = (int) $userId;

            //Creamos una instancia del modelo de usuario
            $mysql = new MySQL();
            $mysql->conectar();

            //Verificamos el estado de la conexión
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
            }

            $userModel = new User($mysql);

            //Obtenemos los datos del usuario
            $userData = $userModel->getUserById($userId);

            //Verificamos si los datos del usuario son válidos
            if (!$userData || $userData === null) {
                self::handleResponse(false, 'Error al obtener los datos del usuario', [], 500);
            }

            //Si los datos son válidos, enviamos los datos del usuario como respuesta
            self::handleResponse(true, 'Datos del usuario obtenidos', [
                'user' => $userData
            ]);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error interno del servidor', [], 500);
        } finally {

            //Desconectamos de la base de datos
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    //Funcion para obtener los datos del administrador
    public static function getAdminData()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (empty($_SESSION['user'])) {
                self::handleResponse(false, 'No hay usuario autenticado', [], 401);
            }

            // Verificar que el usuario sea un administrador
            if ($_SESSION['user']['role'] !== 1) {
                self::handleResponse(false, 'No tienes permisos de administrador', [], 403);
            }

            $adminId = $_SESSION['user']['id'];

            // Sanitizamos el id
            $adminId = filter_var(trim($adminId), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Verificamos si el id es un número
            if (!is_numeric($adminId)) {
                self::handleResponse(false, 'El id no es válido', [], 400);
            }

            $adminId = (int) $adminId;

            // Creamos una instancia del modelo de administrador
            $mysql = new MySQL();
            $mysql->conectar();

            // Verificamos el estado de la conexión
            if (!$mysql->getStatusConexion()) {
                self::handleResponse(false, 'Error al conectar con la base de datos', [], 500);
            }

            $adminModel = new Admin($mysql);

            // Obtenemos los datos del administrador
            $adminData = $adminModel->getAdminById($adminId);

            // Verificamos si los datos del administrador son válidos
            if (!$adminData || $adminData === null) {
                self::handleResponse(false, 'Error al obtener los datos del administrador', [], 500);
            }

            // Si los datos son válidos, enviamos los datos del administrador como respuesta
            self::handleResponse(true, 'Datos del administrador obtenidos', [
                'admin' => $adminData
            ]);
        } catch (Exception $e) {
            self::handleResponse(false, 'Error interno del servidor', [], 500);
        } finally {
            // Desconectamos de la base de datos
            if (isset($mysql)) {
                $mysql->desconectar();
            }
        }
    }

    //Funcion para guardar la session y autenticar
    private static function guardarSession($userData)
    {
        // Iniciar sesión si no está activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Establecer datos de sesión
        $_SESSION['user'] = [
            'id' => $userData['id'],
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => (int) $userData['role'],
            'logged_in' => true
        ];
    }
}


//Funcion para sanitizar el rol que viene en el get
function sanitizeRol($rol)
{
    $rol = filter_var(trim($rol), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $rol = filter_var(trim($rol), FILTER_VALIDATE_INT);
    if (!$rol) {
        return false;
    }
    return $rol;
}

// Verificamos si la petición es POST
// Punto de entrada para el registro y login
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
        case 'register':
            AuthController::register();
            break;
        case 'login':
            AuthController::login();
            break;
        case 'checkAuth':
            //Obtenemos el rol
            $role = $_GET['role'] ?? null;

            //Verificamos si el rol existe
            if (!$role) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['success' => false, 'message' => 'No se especificó el rol']);
                exit;
            }

            //Sanitizamos el rol
            $role = sanitizeRol($role);
            if (!$role) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['success' => false, 'message' => 'El rol no es válido']);
                exit;
            }

            //Verificamos si el rol es válido
            if ($role !== 1 && $role !== 2) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['success' => false, 'message' => 'El rol no es válido']);
                exit;
            }

            //Continuamos con la verificación
            AuthController::checkAuth($role);
            break;
        case 'logout':
            AuthController::logout();
            break;
        case 'getUserData':
            AuthController::getUserData();
            break;
        case 'getAdminData':
            AuthController::getAdminData();
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
