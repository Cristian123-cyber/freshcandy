<?php

class User
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    public function changePassword($data)
    {
        $currentPassword = $data['current_password'];
        $newPassword = $data['new_password'];
        $confirmPassword = $data['confirm_password'];


        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return false;
        }


        if ($newPassword !== $confirmPassword) {
            return false;
        }


        $query = "SELECT password FROM clientes WHERE id_cliente = ? LIMIT 1";

        $clienteId = $this->obtenerIdCliente();

        if (!$clienteId) {
            return false;
        }

        $result = $this->mysql->prepararConsultaSelect($query, 'i', [$clienteId]);

        if (!$result || empty($result)) {
            return false;
        }

        $row = $result[0];
        $hashedPassword = $row['password'];


        $isVerified = $this->verificarPassword($currentPassword, $hashedPassword);

        if (!$isVerified) {
            return false;
        }

        $query = "UPDATE clientes SET password = ? WHERE id_cliente = ?";

        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $result = $this->mysql->prepararConsulta($query, 'si', [$hashedNewPassword, $clienteId]);

        if (!$result) {
            return false;
        }

        return true;
            
        

        
    }

    private function verificarPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    private function obtenerIdCliente()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $clienteId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

        if (!$clienteId) {
            return false;
        }

        return $clienteId;
    }

    public function updateProfile($data)
    {

        $address = $data['address'];
        $phone = $data['phone'];
     


        if (empty($address) || empty($phone)) {
            return false;
        }


        


        $query = "UPDATE clientes SET direccion_envio = ?, telefono_cliente = ? WHERE id_cliente = ?";

        $clienteId = $this->obtenerIdCliente();

        if (!$clienteId) {
            return false;
        }

        $result = $this->mysql->prepararConsulta($query, 'ssi', [$address, $phone, $clienteId]);

        if (!$result) {
            return false;
        }

        return true;

       

    
            
        
        
    }


    //funcion SOLO para pruebas no usar en ningun otro lado a menos que se necesite insertar un administrador de prueba
    public function registerAdmin($name, $email, $password){

        // Hash seguro de la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        if ($hashedPassword === false) {
            error_log("Error al generar hash en User::register()");
            return false;
        }


        $query = "INSERT INTO administradores (nombre_usuario, correo_admin, password, Roles_id_rol) VALUES 
        ('$name', '$email', '$hashedPassword', 1);";

        $result = $this->mysql->efectuarConsulta($query);

        if (!$result) {
            return false;
        }

        return true;
    }

    public function register($name, $cedula, $email, $password)
    {
        // Validación básica
        if (empty($name) || empty($cedula) || empty($email) || empty($password)) {
            error_log("Campos vacíos en User::register()");
            return false;
        }

        // Hash seguro de la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        if ($hashedPassword === false) {
            error_log("Error al generar hash en User::register()");
            return false;
        }
        

        // Escapar TODOS los valores
        $escapedName = $this->mysql->escapar($name);
        $escapedCedula = $this->mysql->escapar($cedula);
        $escapedEmail = $this->mysql->escapar($email);


        $query = "INSERT INTO clientes 
              (nombre_cliente, cedula, correo_cliente, password, Roles_id_rol) 
              VALUES 
              ('$escapedName', '$escapedCedula', '$escapedEmail', '$hashedPassword', 2)";

        $result = $this->mysql->efectuarConsulta($query);

        if (!$result) {
            return false; // Error en la consulta
        }

        return true;
    }



    public function isClientEmail($email)
    {
        // Verificar si el correo electrónico pertenece a un cliente
        $query = "SELECT id_cliente FROM clientes WHERE correo_cliente = '$email'";
        $result = $this->mysql->efectuarConsulta($query);
        return ($result && mysqli_num_rows($result) > 0);
    }

    public function isAdminEmail($email)
    {
        // Verificar si el correo electrónico pertenece a un administrador
        $query = "SELECT id_administrador FROM administradores WHERE correo_admin = '$email'";
        $result = $this->mysql->efectuarConsulta($query);
        return ($result && mysqli_num_rows($result) > 0);
    }

    public function verifyClientLogin($escapedEmail, $password)
    {
        //$this->registerAdmin('Administrador', 'admin2@gmail.com', '!Admin123');
        //verificar login en la tabla clientes
        $query = "SELECT password FROM clientes WHERE correo_cliente = '$escapedEmail'";
        $result = $this->mysql->efectuarConsulta($query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['password'];

            // Verificar la contraseña
            if (password_verify($password, $hashedPassword)) {
                return true; // Autenticación exitosa
            }
        }

        return false; // Autenticación fallida

    }

    public function verifyAdminLogin($escapedEmail, $password)
    {

        //verificar login en la tabla clientes
        $query = "SELECT password FROM administradores WHERE correo_admin = '$escapedEmail'";
        $result = $this->mysql->efectuarConsulta($query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['password'];

            // Verificar la contraseña
            if (password_verify($password, $hashedPassword)) {
                return true; // Autenticación exitosa
            }
        }

        return false; // Autenticación fallida

    }

    public function emailExists($email)
    {
        // Verificar si el correo electrónico ya existe
        $query = "SELECT id_cliente FROM clientes WHERE correo_cliente = '$email'";
        $result = $this->mysql->efectuarConsulta($query);


        $existeEnClientes = ($result && mysqli_num_rows($result) > 0);


        // Verificar en la tabla de administradores
        $query = "SELECT id_administrador FROM administradores WHERE correo_admin = '$email'";
        $result = $this->mysql->efectuarConsulta($query);

        $existeEnAdministradores = ($result && mysqli_num_rows($result) > 0);


        // Retornar true si existe en cualquiera de las tablas
        return $existeEnClientes || $existeEnAdministradores;
    }

    public function cedulaExists($cedula)
    {
        // Verificar si la cédula ya existe
        $query = "SELECT id_cliente FROM clientes WHERE cedula = '$cedula'";
        $result = $this->mysql->efectuarConsulta($query);

        $existeEnClientes = ($result && mysqli_num_rows($result) > 0);

        return $existeEnClientes;
    }
    public function getUserByEmail($email)
    {
        // Obtener datos del cliente por correo electrónico
        $query = "SELECT id_cliente AS id, nombre_cliente AS name, correo_cliente AS email, Roles_id_rol AS role FROM clientes WHERE correo_cliente = ?";
        $result = $this->mysql->prepararConsultaSelect($query, 's', [$email]);
        return ($result && !empty($result)) ? $result[0] : null;
    }

    public function getUserById($id)
    {
        $query = "SELECT id_cliente AS id, nombre_cliente AS name, 
                         cedula AS cedula, correo_cliente AS email, 
                         telefono_cliente AS telefono, 
                         Roles_id_rol AS role, direccion_envio AS direccion 
                  FROM clientes 
                  WHERE id_cliente = ?";
                  
        $resultado = $this->mysql->prepararConsultaSelect($query, 'i', [$id]);
        
        // prepararConsultaSelect devuelve:
        // - false en caso de error
        // - array vacío si no hay resultados
        // - array con datos si hay resultados
        
        return ($resultado && !empty($resultado)) ? $resultado[0] : null;
    }

    public function getAdminByEmail($email)
    {
        // Obtener datos del administrador por correo electrónico
        $query = "SELECT id_administrador AS id, nombre_usuario AS name, correo_admin AS email, Roles_id_rol AS role FROM administradores WHERE correo_admin = ?";
        $result = $this->mysql->prepararConsultaSelect($query, 's', [$email]);
        return ($result && !empty($result)) ? $result[0] : null;
    }
}
