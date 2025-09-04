<?php


class MySQL
{
    //clase

    private $host;
    private $user;
    private $password;
    private $dbname;

    public $conexion;

    // Método para escribir logs de depuración
    private function writeDebugLog($message, $level = 'INFO')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        
        // Escribir en el archivo debug.log
        file_put_contents(__DIR__ . '/debug.log', $logMessage, FILE_APPEND | LOCK_EX);
    }

    public function __construct()
    {
        $isDocker = file_exists('/.dockerenv') || file_exists('/proc/1/cgroup');
        $isProduction = getenv('APP_ENV') === 'production';

        if ($isProduction) {
            // Configuración para producción usando variables de entorno
            $this->host = getenv('DB_HOST') ?: 'mysql';
            $this->user = getenv('DB_USER') ?: 'freshcandy_user';
            $this->password = getenv('DB_PASSWORD');
            $this->dbname = getenv('DB_NAME') ?: 'freshcandy_bd';
        } else {
            // Configuración para desarrollo
            $host = $isDocker ? 'mysql' : 'localhost';
            $this->host = $host;
            $this->user = 'root';
            $this->password = 'freshcandy123';
            $this->dbname = 'freshcandy_bd';
        }
    }
    //con esta funcion verificaremos el estado de la conexion antes de intentar efectuar cualquier accion que requiera una conexion a la base de datos
    public function getStatusConexion()
    {
        return $this->conexion !== null;
    }

    //metodo pARA CONECTAR A LA BASE DE DATOS
    public function conectar()
    {
        // Verifica si ya hay una conexión activa
        if ($this->conexion) {
            return;
        }

        $this->writeDebugLog("Iniciando proceso de conexión a la base de datos");
        $this->writeDebugLog("Host: {$this->host}, Usuario: {$this->user}, Base de datos: {$this->dbname}, Pass: {$this->password}");
        
        try {
            $this->writeDebugLog("Intentando conexión con password configurado");
            $intentoConexion = @mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
            if ($intentoConexion) {
                $this->conexion = $intentoConexion;
                mysqli_set_charset($this->conexion, "utf8");

                $this->writeDebugLog("Conexión exitosa a la base de datos", 'SUCCESS');
                error_log("Conexión exitosa con contraseña: '{$this->password}'"); //usa error_log para ver en el log para no interferir con llamadas AJAX
                return;
            } else {
                $this->writeDebugLog("Falló la conexión: " . mysqli_connect_error(), 'ERROR');
            }
        } catch (mysqli_sql_exception $e) {
            $this->writeDebugLog("Excepción durante la conexión: " . $e->getMessage(), 'ERROR');
            error_log("⚠️ Falló con '{$this->password}': " . $e->getMessage());
        }
        
        // Si no se pudo conectar, muestra un mensaje de error
        $errorMsg = "No se pudo conectar a la base de datos. Error: " . mysqli_connect_error();
        $this->writeDebugLog($errorMsg, 'CRITICAL');
        error_log("❌ " . $errorMsg);
        $this->conexion = null; //seteamos la conexion como NULL para manejar errores de conexion desde afuera
    }


    //Desconectar base de datos
    public function desconectar()
    {
        if ($this->conexion) {
            mysqli_close($this->conexion);
            $this->conexion = null;
        }
    }

    public function efectuarConsulta($consulta)
    {
        if (!$this->conexion) {
            $this->conectar();
        }

        if (!$this->getStatusConexion()) {
            
            error_log("No hay conexión a la base de datos");
            return false;
        }

        // Log de la consulta (truncada si es muy larga)
        $consultaTruncada = strlen($consulta) > 100 ? substr($consulta, 0, 100) . '...' : $consulta;
   

        mysqli_query($this->conexion, "SET NAMES 'utf8'");
        mysqli_query($this->conexion, "SET CHARACTER SET 'utf8'");

        $resultado = mysqli_query($this->conexion, $consulta);

        if (!$resultado) {
            $errorMsg = "Error en la consulta: " . mysqli_error($this->conexion);
           
            error_log($errorMsg);
            return false;
        }

        
        return $resultado;
    }

    //metodo para escapar los valores de las consultas, escudo basico contra SQL injection
    public function escapar($valor)
    {
        if (!$this->conexion) {
            $this->conectar(); // Asegurar que hay conexión
        }
        return mysqli_real_escape_string($this->conexion, $valor);
    }

    //metodo para preparar consultas usando consultas preparadas para mejor seguridad
    public function prepararConsulta($sql, $tipos, $params) //sql es la consulta, tipos es el tipo de datos, params es el array de parametros
    {
        if (!$this->conexion) {
            $this->conectar();
        }

        if (!$this->getStatusConexion()) {
            
            error_log("No hay conexión a la base de datos");
            return false;
        }

        // Log de la consulta preparada
        $sqlTruncado = strlen($sql) > 100 ? substr($sql, 0, 100) . '...' : $sql;
       

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            $errorMsg = "Error al preparar la consulta: " . $this->conexion->error;
           
            error_log($errorMsg);
            return false;
        }

        $refs = [];
        $refs[] = $tipos;
        foreach ($params as $key => $value) {
            $refs[] = &$params[$key]; // <- pasamos referencias limpias
        }

        call_user_func_array([$stmt, 'bind_param'], $refs);

        if (!$stmt->execute()) {
            $errorMsg = "Error al ejecutar la consulta preparada: " . $stmt->error;
           
            error_log($errorMsg);
            return false;
        }

        
        return true;
    }

    //metodo para preparar consultas usando consultas preparadas para mejor seguridad
    public function prepararConsultaInsert($sql, $tipos, $params) //sql es la consulta, tipos es el tipo de datos, params es el array de parametros
    {
        if (!$this->conexion) {
            $this->conectar();
        }


        if (!$this->getStatusConexion()) {
            error_log("No hay conexión a la base de datos");
            return [
                'success' => false,
                'message' => 'No hay conexión a la base de datos'
            ];
        }



        $stmt = $this->conexion->prepare($sql);



        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $this->conexion->error);
            return [
                'success' => false,
                'message' => 'Error al preparar la consulta'
            ];
        }

        $refs = [];
        $refs[] = $tipos;
        foreach ($params as $key => $value) {
            $refs[] = &$params[$key]; // <- pasamos referencias limpias
        }



        // Verificar si bind_param tuvo éxito
        if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
            error_log("Error al vincular parámetros: " . $stmt->error);
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Error al vincular parámetros'
            ];
        }



        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta preparada: " . $stmt->error);
            return [
                'success' => false,
                'message' => 'Error al ejecutar la consulta'
            ];
        }





        $id_insertado = $this->conexion->insert_id;

        return [
            'success' => true,
            'id' => $id_insertado
        ];
    }

    public function prepararConsultaSelect($sql, $tipos = "", $params = [])
    {
        if (!$this->conexion) {
            $this->conectar();
        }

        if (!$this->getStatusConexion()) {
            error_log("No hay conexión a la base de datos");
            return false; // Error de conexión
        }

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $this->conexion->error);
            return false; // Error al preparar
        }

        // Solo hace bind_param si hay parámetros
        if (!empty($params)) {
            // Usar call_user_func_array para bind_param si PHP < 5.6 o si se necesita compatibilidad
            // Si estás usando PHP >= 5.6, puedes usar ...$params directamente
            // Aquí asumimos compatibilidad más amplia
            $refs = [];
            $refs[] = $tipos;
            foreach ($params as $key => $value) {
                $refs[] = &$params[$key]; // Pasar referencias
            }
            if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
                error_log("Error al vincular parámetros: " . $stmt->error);
                $stmt->close();
                return false; // Error al vincular parámetros
            }
        }

        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            $stmt->close();
            return false; // Error al ejecutar
        }

        $resultado = $stmt->get_result();

        if (!$resultado) {
            // Esto puede pasar si la consulta no es un SELECT o si hay un error después de execute
            error_log("Error o resultado no válido después de execute: " . $stmt->error);
            $stmt->close();
            return false; // Error o resultado no válido
        }

        // Si get_result fue exitoso, fetch_all debería devolver un array (vacío si no hay filas)
        $rows = $resultado->fetch_all(MYSQLI_ASSOC);

        $stmt->close(); // Cerrar el statement
        $resultado->free(); // Liberar el resultado

        // Retornar array vacío en lugar de false cuando no hay resultados
        return $rows;
    }
    public function prepararConsultaSelect2($sql, $tipos = "", $params = [])
    {
        if (!$this->conexion) {
            $this->conectar();
        }

        if (!$this->getStatusConexion()) {
            error_log("No hay conexión a la base de datos");
            return false; // Error de conexión
        }

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $this->conexion->error);
            return false; // Error al preparar
        }

        // Solo hace bind_param si hay parámetros
        if (!empty($params)) {
            $refs = [];
            $refs[] = $tipos;
            foreach ($params as $key => $value) {
                $refs[] = &$params[$key];
            }
            if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
                error_log("Error al vincular parámetros: " . $stmt->error);
                $stmt->close();
                return false;
            }
        }

        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $resultado = $stmt->get_result();

        if (!$resultado) {
            error_log("Error o resultado no válido después de execute: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $rows = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $resultado->free();

        // Si no hay resultados, retornamos array vacío
        return empty($rows) ? [] : $rows;
    }

    public function prepararConsultaUpdate($sql, $tipos, $params)
    {
        if (!$this->conexion) {
            $this->conectar();
        }
        if (!$this->getStatusConexion()) {
            error_log("No hay conexión a la base de datos");
            return false;
        }
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $this->conexion->error);
            return false;
        }
        $refs = [];
        $refs[] = $tipos;
        foreach ($params as $key => $value) {
            $refs[] = &$params[$key];
        }
        if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
            error_log("Error al vincular parámetros: " . $stmt->error);
            $stmt->close();
            return false;
        }
        $result = $stmt->execute();
        if (!$result) {
            error_log("Error al ejecutar la consulta preparada: " . $stmt->error);
            $stmt->close();
            return false;
        }
        // Obtener el número de filas afectadas antes de cerrar el statement
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        // Devolver true si la consulta se ejecutó correctamente
        return true;
    }
}
