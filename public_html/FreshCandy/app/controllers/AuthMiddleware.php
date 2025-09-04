<?php
//Middleware basico para proteger las página
class AuthMiddleware
{
    //Funcion para verificar si la petición es AJAX por si se llama desde una API (no implementado, solo para futuras implementaciones)
    private static function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    //Funcion para proteger la página de administrador
    public static function protectAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (self::checkAuthenticationAdmin()) {
            return true;
        }

        self::handleUnauthenticated(false);
    }

    //Funcion para proteger la página de usuario
    public static function protectUser()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (self::checkAuthenticationUser()) {
            return true;
        }

        self::handleUnauthenticated(true);
    }

    //Funcion para autenticar al administrador
    private static function checkAuthenticationAdmin()
    {
        return !empty($_SESSION['user']) &&
            $_SESSION['user']['logged_in'] &&
            $_SESSION['user']['role'] === 1;
    }

    //Funcion para autenticar al usuario
    private static function checkAuthenticationUser()
    {
        return !empty($_SESSION['user']) &&
            $_SESSION['user']['logged_in'];
    }

    //Funcion para manejar la respuesta de autenticación fallida
    private static function handleUnauthenticated($showNotification = true) //Por defecto se muestra una notificacion, si se indica false no se muestra
    {

        //Si la petición es AJAX, se devuelve un JSON con la respuesta de redireccionamiento
        if (self::isAjaxRequest()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'No autorizado',
                'redirect' => 'views/users/authForm3.php'
            ]);
            exit;
        } else {

            //Si la petición no es AJAX, se redirige a la página de login

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            //Si se debe mostrar una notificacion, se guarda en la session para capturarla con js

            if($showNotification){
                $_SESSION['flash_error'] = 'Se requiere iniciar sesión para acceder'; //Mensaje de error para el usuario
            }
            
            //Se redirige a la página de login
            header('Location: /views/users/authForm3.php');
            exit;
        }
    }
}
