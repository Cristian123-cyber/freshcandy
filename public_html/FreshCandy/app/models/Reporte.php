<?php

// Modelo para la generación de reportes
class Reporte
{
    private $mysql;

    // Constructor: recibe la instancia de conexión MySQL
    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    /**
     * Reporte de inventario de ingredientes
     * @param int|null $categoriaId  ID de la categoría (opcional)
     * @param int|null $estadoStock  ID del estado de stock (opcional)
     * @return array  Lista de ingredientes con su información
     */
    public function getInventarioIngredientes($categoriaId = null, $estadoStock = null)
    {
        $query = "SELECT i.nombre_ing, c.titulo_categoria, i.stock_ing, u.nombre_unidad, e.titulo_estado
                  FROM ingredientes i
                  LEFT JOIN categorias_ingredientes c ON i.Categorias_Ingredientes_id_categoria = c.id_categoria
                  LEFT JOIN unidades u ON i.Unidades_id_unidad = u.id_unidad
                  LEFT JOIN estados_stock e ON i.Estados_Stock_id_estado = e.id_estado
                  WHERE 1=1";
        if ($categoriaId) {
            $query .= " AND i.Categorias_Ingredientes_id_categoria = " . intval($categoriaId);
        }
        if ($estadoStock) {
            $query .= " AND i.Estados_Stock_id_estado = " . intval($estadoStock);
        }
        $result = $this->mysql->efectuarConsulta($query);
        if ($result === false) {
            die("Error en la consulta: " . $this->mysql->getConexion()->error . "<br>SQL: " . $query);
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Reporte de pedidos por fecha
     * @param string $fechaInicio  Fecha de inicio (YYYY-MM-DD)
     * @param string $fechaFin     Fecha de fin (YYYY-MM-DD)
     * @param int|null $estado     ID del estado del pedido (opcional)
     * @return array  Lista de pedidos
     */
    public function getPedidosPorFecha($fechaInicio, $fechaFin, $estado = null)
    {
        $query = "SELECT p.id_pedido, p.fecha, c.nombre_cliente, p.monto_total, ep.titulo_estado
                  FROM pedidos p
                  LEFT JOIN clientes c ON p.Clientes_id_cliente = c.id_cliente
                  LEFT JOIN estados_pedido ep ON p.Estados_pedido_id_estado = ep.id_estado
                  WHERE DATE(p.fecha) BETWEEN '" . $fechaInicio . "' AND '" . $fechaFin . "'";
        if ($estado) {
            $query .= " AND p.Estados_pedido_id_estado = " . intval($estado);
        }
        $result = $this->mysql->efectuarConsulta($query);
        if ($result === false) {
            die("Error en la consulta: " . $this->mysql->getConexion()->error . "<br>SQL: " . $query);
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Reporte de pedidos por producto
     * @param string $fechaInicio  Fecha de inicio (YYYY-MM-DD)
     * @param string $fechaFin     Fecha de fin (YYYY-MM-DD)
     * @param int|null $categoriaId  ID de la categoría de producto (opcional)
     * @return array  Lista de productos vendidos con totales
     */
    public function getPedidosPorProducto($fechaInicio, $fechaFin, $categoriaId = null)
    {
        // NOTA: No existe tabla categorias_productos, se debe usar etiquetas_producto y el campo Etiquetas_producto_id_etiqueta en productos
        $query = "SELECT pr.nombre_producto, ep.titulo_etiqueta AS nombre_categoria, 
                         SUM(php.cantidad) AS unidades_vendidas, 
                         SUM(pr.precio_producto * php.cantidad) AS total_ventas
                  FROM pedidos_has_productos php
                  LEFT JOIN productos pr ON php.Productos_id_producto = pr.id_producto
                  LEFT JOIN pedidos p ON php.Pedidos_id_pedido = p.id_pedido
                  LEFT JOIN etiquetas_producto ep ON pr.Etiquetas_producto_id_etiqueta = ep.id_etiqueta
                  WHERE DATE(p.fecha) BETWEEN '" . $fechaInicio . "' AND '" . $fechaFin . "'";
        if ($categoriaId) {
            $query .= " AND pr.Etiquetas_producto_id_etiqueta = " . intval($categoriaId);
        }
        $query .= " GROUP BY pr.id_producto, ep.id_etiqueta";
        $result = $this->mysql->efectuarConsulta($query);
        if ($result === false) {
            die("Error en la consulta: " . $this->mysql->getConexion()->error . "<br>SQL: " . $query);
        }
        $data = [];
        $totalVentas = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $totalVentas += $row['total_ventas'];
            $data[] = $row;
        }
        // Calcular porcentaje del total para cada producto
        foreach ($data as &$row) {
            $row['porcentaje'] = $totalVentas > 0 ? round(($row['total_ventas'] / $totalVentas) * 100) : 0;
        }
        return $data;
    }
}
