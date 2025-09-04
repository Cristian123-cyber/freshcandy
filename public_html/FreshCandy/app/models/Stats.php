<?php

class Stats
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    // Métodos para obtener estadísticas del dashboard principal

    public function getPedidosHoy()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha) = CURDATE()";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["pedidos_hoy" => (int)$row["total"]];
    }

    public function getPedidosPendientes()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 1";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["pedidos_pendientes" => (int)$row["total"]];
    }

    public function getStockBajo()
    {
        $query = "SELECT COUNT(*) as total FROM ingredientes WHERE Estados_Stock_id_estado BETWEEN 2 AND 3";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["stock_bajo" => (int)$row["total"]];
    }

    public function getNuevasSugerencias()
    {
        $query = "SELECT COUNT(*) as total FROM sugerencias WHERE Estado_Sugerencias_id_estado = 1";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["nuevas_sugerencias" => (int)$row["total"]];
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

    public function getEstadisticasCliente()
    {
        $clienteId = $this->obtenerIdCliente();

        if (!$clienteId) {
            return [
                "total_pedidos" => 0,
                "producto_favorito" => null
            ];
        }

        $queryPedidos = "SELECT COUNT(*) as total FROM pedidos WHERE pedidos.Clientes_id_cliente = $clienteId";
        $resultPedidos = $this->mysql->efectuarConsulta($queryPedidos);
        $rowPedidos = $resultPedidos ? mysqli_fetch_assoc($resultPedidos) : ["total" => 0];

        // Obtener el producto favorito (el más pedido)
        $queryFavorito = "SELECT 
                            pr.nombre_producto as Helado_Favorito, 
                            SUM(pp.cantidad) as total_pedidos,
                            COUNT(DISTINCT p.id_pedido) as veces_pedido
                         FROM pedidos p
                         JOIN pedidos_has_productos pp ON p.id_pedido = pp.Pedidos_id_pedido
                         JOIN productos pr ON pp.Productos_id_producto = pr.id_producto
                         WHERE p.Clientes_id_cliente = $clienteId
                         AND p.Estados_pedido_id_estado != 5  -- Excluir pedidos cancelados
                         GROUP BY pr.id_producto, pr.nombre_producto
                         HAVING total_pedidos > 0
                         ORDER BY total_pedidos DESC, veces_pedido DESC
                         LIMIT 1";

        $resultFavorito = $this->mysql->efectuarConsulta($queryFavorito);
        $rowFavorito = $resultFavorito ? mysqli_fetch_assoc($resultFavorito) : null;

        return [
            "total_pedidos" => (int)$rowPedidos["total"],
            "producto_favorito" => $rowFavorito ? $rowFavorito["Helado_Favorito"] : "No hay pedidos registrados"
        ];
    }

    // Métodos para obtener estadísticas de clientes
    public function getTotalClientes()
    {
        $query = "SELECT COUNT(*) as total FROM clientes";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["total_clientes" => (int)$row["total"]];
    }

    public function getClienteMasPedidos()
    {
        $query = "SELECT c.nombre_cliente, COUNT(p.id_pedido) as total_pedidos
                  FROM clientes c
                  JOIN pedidos p ON c.id_cliente = p.Clientes_id_cliente
                  GROUP BY c.id_cliente
                  ORDER BY total_pedidos DESC
                  LIMIT 1";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        return [
            "cliente_mas_pedidos" => $row ? $row["nombre_cliente"] : null,
            "total_pedidos" => $row ? (int)$row["total_pedidos"] : 0
        ];
    }

    public function getClienteMayorGasto()
    {
        $query = "SELECT c.nombre_cliente, SUM(p.monto_total) as total_gasto
                  FROM clientes c
                  JOIN pedidos p ON c.id_cliente = p.Clientes_id_cliente
                  GROUP BY c.id_cliente
                  ORDER BY total_gasto DESC
                  LIMIT 1";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        return [
            "cliente_mayor_gasto" => $row ? $row["nombre_cliente"] : null,
            "total_gasto" => $row ? (float)$row["total_gasto"] : 0
        ];
    }

    // Métodos para obtener estadísticas de pedidos
    public function getPedidosCompletados()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 4";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["pedidos_completados" => (int)$row["total"]];
    }

    public function getPedidosCancelados()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 5";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["pedidos_cancelados" => (int)$row["total"]];
    }

    public function getPedidosAyer()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha) = CURDATE() - INTERVAL 1 DAY";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["pedidos_ayer" => (int)$row["total"]];
    }

    // Métodos para obtener estadísticas de productos
    public function getHeladoMasVendido()
    {
        $query = "SELECT pr.nombre_producto AS helado, SUM(pp.cantidad) AS total_vendidos
        FROM pedidos_has_productos pp
        JOIN productos pr ON pp.Productos_id_producto = pr.id_producto
        JOIN pedidos p ON pp.pedidos_id_pedido = p.id_pedido 
        WHERE p.Estados_pedido_id_estado != 5  -- Excluir pedidos cancelados
        GROUP BY pr.id_producto
        ORDER BY total_vendidos DESC
        LIMIT 1";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        return ["helado_mas_vendido" => $row ? $row["helado"] : "No hay ventas registradas", "ventas_mas_vendido" => $row ? (int)$row["total_vendidos"] : 0];
    }

    public function getHeladoMenosVendido()
    {
        $query = "SELECT pr.nombre_producto AS helado, SUM(pp.cantidad) AS total_vendidos
        FROM pedidos_has_productos pp
        JOIN productos pr ON pp.Productos_id_producto = pr.id_producto
        JOIN pedidos p ON pp.pedidos_id_pedido = p.id_pedido 
        WHERE p.Estados_pedido_id_estado != 5  -- Excluir pedidos cancelados
        GROUP BY pr.id_producto
        ORDER BY total_vendidos ASC
        LIMIT 1";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        return ["helado_menos_vendido" => $row ? $row["helado"] : "No hay ventas registradas", "ventas_menos_vendido" => $row ? (int)$row["total_vendidos"] : 0];
    }

    public function getTotalProductos()
    {
        $query = "SELECT COUNT(*) as total FROM productos WHERE productos.estado = 1";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["total_productos" => (int)$row["total"]];
    }

    // Métodos para obtener estadísticas de inventario
    public function getIngredientesStockBajo()
    {
        $query = "SELECT COUNT(*) as total FROM ingredientes WHERE Estados_Stock_id_estado=2";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["productos_stock_bajo" => (int)$row["total"]];
    }

    public function getProductosSinExistencias()
    {
        $query = "SELECT COUNT(*) as total FROM ingredientes WHERE Estados_Stock_id_estado=4";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["productos_sin_existencias" => (int)$row["total"]];
    }

    public function getProductosCriticos($umbral = 2)
    {
        $query = "SELECT COUNT(*) as total FROM ingredientes WHERE Estados_Stock_id_estado=3";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["productos_criticos" => (int)$row["total"]];
    }

    // Métodos para obtener estadísticas de sugerencias
    public function getTotalSugerencias()
    {
        $query = "SELECT COUNT(DISTINCT id_sugerencia) as total FROM sugerencias";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["total_sugerencias" => (int)$row["total"]];
    }

    public function getSugerenciasPendientes()
    {
        $query = "SELECT COUNT(DISTINCT s.id_sugerencia) as total FROM sugerencias s LEFT JOIN estado_sugerencias es ON s.Estado_Sugerencias_id_estado = es.id_estado WHERE es.nombre_estado = 'Pendiente'";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["sugerencias_pendientes" => (int)$row["total"]];
    }

    public function getSugerenciasRevisadas()
    {
        $query = "SELECT COUNT(DISTINCT s.id_sugerencia) as total FROM sugerencias s LEFT JOIN estado_sugerencias es ON s.Estado_Sugerencias_id_estado = es.id_estado WHERE es.nombre_estado = 'Revisada'";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["sugerencias_revisadas" => (int)$row["total"]];
    }

    public function getSugerenciasEliminadas()
    {
        $query = "SELECT COUNT(DISTINCT s.id_sugerencia) as total FROM sugerencias s LEFT JOIN estado_sugerencias es ON s.Estado_Sugerencias_id_estado = es.id_estado WHERE es.nombre_estado = 'Eliminada'";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["sugerencias_eliminadas" => (int)$row["total"]];
    }

    public function getPedidosPendientesHoy()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 1 AND DATE(fecha) = CURDATE()";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["pendientes_hoy" => (int)$row["total"]];
    }

    public function getPedidosPendientesAyer()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 1 AND DATE(fecha) = CURDATE() - INTERVAL 1 DAY";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["pendientes_ayer" => (int)$row["total"]];
    }

    public function getPedidosCompletadosHoy()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 4 AND DATE(fecha) = CURDATE()";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["completados_hoy" => (int)$row["total"]];
    }

    public function getPedidosCompletadosAyer()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 4 AND DATE(fecha) = CURDATE() - INTERVAL 1 DAY";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["completados_ayer" => (int)$row["total"]];
    }

    public function getPedidosCanceladosHoy()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 5 AND DATE(fecha) = CURDATE()";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["cancelados_hoy" => (int)$row["total"]];
    }

    public function getPedidosCanceladosAyer()
    {
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE Estados_pedido_id_estado = 5 AND DATE(fecha) = CURDATE() - INTERVAL 1 DAY";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["cancelados_ayer" => (int)$row["total"]];
    }

    public function getSugerenciasNuevasHoy()
    {
        $query = "SELECT COUNT(*) as total FROM sugerencias WHERE DATE(fecha) = CURDATE()";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["sugerencias_hoy" => (int)$row["total"]];
    }

    public function getSugerenciasNuevasAyer()
    {
        $query = "SELECT COUNT(*) as total FROM sugerencias WHERE DATE(fecha) = CURDATE() - INTERVAL 1 DAY";
        $result = $this->mysql->efectuarConsulta($query);
        $row = $result ? mysqli_fetch_assoc($result) : ["total" => 0];
        return ["sugerencias_ayer" => (int)$row["total"]];
    }

    //PONGA CUIDADO MANO MALPARIDO, LLAMA ESTOS METODOS DESDE EL CONTROLADOR, AQUI EN ESTE MODELO SE MANEJA TODO LO DE CONEXION A LA BASE DE DATOS
    // EJECUTAR CONSULTAS CON EL MODELO DE MYSQL USANDO LA FUNCION $this->mysql->efectuarConsulta() que devuelve los datos de la consulta, desde aqui
    //devuelve los datos de la consulta, desde aqui hacia el controlador
    //DESDE EL CONTROLADOR LAS ENVIA CON LA FUNCION handleResponse() y en el parametro $data de la funcion le pasas los datos que devuelve el modelo

}
