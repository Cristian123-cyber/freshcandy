<?php


class Sugerencia {
    private $mysql;

    public function __construct($mysql) {
        $this->mysql = $mysql;
    }

    public function insertarSugerencia($titulo, $cuerpo, $idTipoSugerencia, $idCliente) {
        try {
            $sql = "INSERT INTO sugerencias (titulo_sugerencia, sugerencia_info, Tipo_Sugerencia_id_tipo, Clientes_id_cliente, fecha, Estado_Sugerencias_id_estado) 
            VALUES (?, ?, ?, ?, NOW(), 1)";

            $tipos = "ssii";
            $params = [$titulo, $cuerpo, $idTipoSugerencia, $idCliente];

            $result = $this->mysql->prepararConsulta($sql, $tipos, $params);

            if(!$result){
                return false;
            }

            return true;


        } catch (Exception $e) {
            error_log("Error en Sugerencia::insertarSugerencia: " . $e->getMessage());
            return false;
        }
    }

    public function getSugerenciaById($id) {
        try {
            if (empty($id) || !is_numeric($id)) {
                return ['success' => false, 'message' => 'ID de sugerencia inválido'];
            }

            $id = $this->mysql->escapar($id);
            $query = "SELECT s.*, ts.nombre_tipo as tipo_sugerencia, c.nombre_cliente as nombre_cliente 
                     FROM sugerencias s 
                     INNER JOIN tipo_sugerencia ts ON s.Tipo_Sugerencia_id_tipo = ts.id_tipo 
                     INNER JOIN clientes c ON s.Clientes_id_cliente = c.id_cliente 
                     WHERE s.id_sugerencia = '$id'";
            
            $result = $this->mysql->efectuarConsulta($query);
            
            if (!$result || mysqli_num_rows($result) === 0) {
                return ['success' => false, 'message' => 'Sugerencia no encontrada'];
            }

            return ['success' => true, 'data' => mysqli_fetch_assoc($result)];
        } catch (Exception $e) {
            error_log("Error al obtener sugerencia: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al obtener la sugerencia'];
        }
    }

    public function getTiposSugerencias() {
        try {
            if (!$this->mysql->getStatusConexion()) {
                throw new Exception('No hay conexión a la base de datos');
            }

            $query = "SELECT id_tipo, nombre_tipo FROM tipo_sugerencia ORDER BY nombre_tipo ASC";
            
            
            $result = $this->mysql->efectuarConsulta($query);

            if (!$result) {
                $error = $this->mysql->getError();
                
                throw new Exception('Error al ejecutar la consulta: ' . $error);
            }

            $tiposSugerencias = [];
            while ($fila = mysqli_fetch_assoc($result)) {
                $tiposSugerencias[] = $fila;
            }

            if (empty($tiposSugerencias)) {
                
                return [
                    'success' => false,
                    'message' => 'No se encontraron tipos de sugerencias'
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'tiposSugerencias' => $tiposSugerencias
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener tipos de sugerencias: ' . $e->getMessage()
            ];
        }
    }

    // Nuevo método para obtener los estados de sugerencias
    public function getEstadosSugerencias() {
        try {
            if (!$this->mysql->getStatusConexion()) {
                throw new Exception('No hay conexión a la base de datos');
            }

            $query = "SELECT MIN(id_estado) as id_estado, nombre_estado FROM estado_sugerencias GROUP BY nombre_estado ORDER BY id_estado ASC";
            $result = $this->mysql->efectuarConsulta($query);

            if (!$result) {
                $error = $this->mysql->getError();
                throw new Exception('Error al ejecutar la consulta: ' . $error);
            }

            $estados = [];
            while ($fila = mysqli_fetch_assoc($result)) {
                $estados[] = $fila;
            }

            return [
                'success' => true,
                'data' => [
                    'estados' => $estados
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener estados de sugerencias: ' . $e->getMessage()
            ];
        }
    }
}
?>