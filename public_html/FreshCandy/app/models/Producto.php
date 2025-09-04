<?php

class Producto
{
    private $mysql; //variable para la conexion a la base de datos


    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    // Obtener todos los productos
    // Devuelve un array de productos o false si hay error
    // Usa mysqli_fetch_assoc para obtener los resultados
    public function getAllProductos()
    {

        try {


            $query = "SELECT id_producto AS id, nombre_producto AS nombre, descripcion, precio_producto AS precio, image_url, etiquetas_producto.titulo_etiqueta, productos.fecha_creacion AS fecha_creacion
            FROM productos
            JOIN etiquetas_producto ON productos.Etiquetas_producto_id_etiqueta = etiquetas_producto.id_etiqueta
            WHERE productos.estado = 1
            ORDER BY productos.id_producto ASC";



            $result = $this->mysql->efectuarConsulta($query);




            //si no hay resultados, devuelve false
            if (!$result) {
                return false;
            }

            //crea un array para almacenar los productos
            $productos = [];

            //mientras haya filas, agrega los productos al array
            while ($fila = mysqli_fetch_assoc($result)) {
                $productos[] = $fila;
            }

            //devuelve el array de productos
            return $productos;
        } catch (\Throwable $th) {

            error_log("Error al obtener todos los productos: " . mysqli_error($this->mysql->conexion));
            return false;
        }
    }

    // Obtener un producto por su ID
    // Devuelve un array con los datos del producto o null si no existe
    // Usa mysqli_fetch_assoc para obtener los resultados
    public function getProductoById($id)
    {
        try {
            // Validación básica del ID
            if (!is_numeric($id) || $id <= 0) {
                throw new InvalidArgumentException("ID de producto no válido");
            }


            $id = (int) $id;


            // Consulta SQL con parámetro escapado para seguridad
            $query = "
               SELECT id_producto AS id, 
               nombre_producto AS nombre, 
               descripcion, 
               precio_producto AS precio, 
               image_url, 
               etiquetas_producto.titulo_etiqueta,
               productos.fecha_creacion AS fecha_creacion
               FROM productos
               JOIN etiquetas_producto ON productos.Etiquetas_producto_id_etiqueta = etiquetas_producto.id_etiqueta
               WHERE id_producto = ?
               LIMIT 1";

            $result = $this->mysql->prepararConsultaSelect($query, "i", [$id]);

        
            //si no hay resultados, devuelve null

            return ($result && !empty($result)) ? $result[0] : null;
        } catch (\Throwable $th) {

            error_log("Error: " . $th->getMessage());
            return false;
        }
    }

    public function buscarProductos($termino, $filtros = [])
    {
        try {
            $sql = "SELECT p.id_producto AS id, 
                           p.nombre_producto AS nombre, 
                           p.descripcion, 
                           p.precio_producto AS precio, 
                           p.image_url,
                           e.titulo_etiqueta AS categoria_nombre,
                           p.fecha_creacion
                    FROM productos p 
                    LEFT JOIN etiquetas_producto e ON p.Etiquetas_producto_id_etiqueta = e.id_etiqueta 
                    WHERE p.estado = 1"; // Asegurarse de que el producto esté activo
            $params = [];
            $types = '';

            // Búsqueda por término
            if (!empty($termino)) {
                $sql .= " AND (p.id_producto LIKE ? OR p.nombre_producto LIKE ? OR p.descripcion LIKE ?)";
                $termino = "%$termino%";
                $params[] = $termino;
                $params[] = $termino;
                $params[] = $termino;
                $types .= 'sss';
            }

            // Filtros
            if (!empty($filtros['categoria'])) {
                $sql .= " AND p.Etiquetas_producto_id_etiqueta = ?";
                $params[] = $filtros['categoria'];
                $types .= 'i';
            }

            if (!empty($filtros['precio_min'])) {
                $sql .= " AND p.precio_producto >= ?";
                $params[] = $filtros['precio_min'];
                $types .= 'd';
            }

            if (!empty($filtros['precio_max'])) {
                $sql .= " AND p.precio_producto <= ?";
                $params[] = $filtros['precio_max'];
                $types .= 'd';
            }

            // Ordenamiento
            if (!empty($filtros['ordenamiento'])) {
                switch ($filtros['ordenamiento']) {
                    case 'price_asc':
                        $sql .= " ORDER BY p.precio_producto ASC";
                        break;
                    case 'price_desc':
                        $sql .= " ORDER BY p.precio_producto DESC";
                        break;
                    case 'date_desc':
                        $sql .= " ORDER BY p.fecha_creacion DESC";
                        break;
                    case 'date_asc':
                        $sql .= " ORDER BY p.fecha_creacion ASC";
                        break;
                    default:
                        $sql .= " ORDER BY p.id_producto DESC";
                }
            } else {
                $sql .= " ORDER BY p.id_producto DESC";
            }

            $result = $this->mysql->prepararConsultaSelect($sql, $types, $params);
            return $result ?: [];
        } catch (Exception $e) {
            error_log("Error en buscarProductos (modelo): " . $e->getMessage());
            throw new Exception("Error interno al buscar productos.");
        }
    }

    public function obtenerCategorias()
    {
        try {
            $sql = "SELECT id_etiqueta AS id, titulo_etiqueta AS nombre FROM etiquetas_producto ORDER BY titulo_etiqueta";
            $result = $this->mysql->efectuarConsulta($sql);

            if (!$result) {
                return [];
            }

            $categorias = [];
            while ($fila = mysqli_fetch_assoc($result)) {
                $categorias[] = $fila;
            }
            return $categorias;
        } catch (Exception $e) {
            throw new Exception("Error al obtener categorías: " . $e->getMessage());
        }
    }

    public function crearProducto($datos)
    {
        try {
            $query = "INSERT INTO productos (nombre_producto, descripcion, precio_producto, image_url, Etiquetas_producto_id_etiqueta, fecha_creacion) 
                     VALUES (?, ?, ?, ?, ?, NOW())";

            $params = [
                $datos['nombre'],
                $datos['descripcion'],
                $datos['precio'],
                $datos['imagen'],
                $datos['etiqueta']
            ];

            $types = 'ssdsi'; // string, string, double, string, integer

            $result = $this->mysql->prepararConsultaInsert($query, $types, $params);

            if ($result) {
                return mysqli_insert_id($this->mysql->conexion);
            }

            return false;
        } catch (Exception $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            return false;
        }
    }

    public function updateProducto($id, $datos)
    {
        try {
            $campos = [];
            $params = [];
            $types = '';

            if (isset($datos['nombre'])) {
                $campos[] = 'nombre_producto = ?';
                $params[] = $datos['nombre'];
                $types .= 's';
            }
            if (isset($datos['descripcion'])) {
                $campos[] = 'descripcion = ?';
                $params[] = $datos['descripcion'];
                $types .= 's';
            }
            if (isset($datos['precio'])) {
                $campos[] = 'precio_producto = ?';
                $params[] = $datos['precio'];
                $types .= 'd';
            }
            if (isset($datos['etiqueta'])) {
                // Validar que la etiqueta sea numérica
                if (!is_numeric($datos['etiqueta'])) {
                    error_log('updateProducto: Etiqueta no es numérica: ' . print_r($datos['etiqueta'], true));
                    return false;
                }
                $campos[] = 'Etiquetas_producto_id_etiqueta = ?';
                $params[] = (int)$datos['etiqueta'];
                $types .= 'i';
            }
            if (isset($datos['imagen']) && $datos['imagen']) {
                $campos[] = 'image_url = ?';
                $params[] = $datos['imagen'];
                $types .= 's';
            }
            if (empty($campos)) {
                error_log('updateProducto: No hay campos para actualizar');
                return false;
            }
            $params[] = $id;
            $types .= 'i';
            $sql = 'UPDATE productos SET ' . implode(', ', $campos) . ' WHERE id_producto = ?';

            // LOG para depuración
            error_log('updateProducto SQL: ' . $sql);
            error_log('updateProducto types: ' . $types);
            error_log('updateProducto params: ' . print_r($params, true));

            $result = $this->mysql->prepararConsultaUpdate($sql, $types, $params);
            if (!$result) {
                error_log('updateProducto: Error en prepararConsultaUpdate');
            }
            return $result;
        } catch (Exception $e) {
            error_log('Error al actualizar producto: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteProducto($id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                error_log('deleteProducto: ID inválido: ' . print_r($id, true));
                return false;
            }

            // Convertir ID a entero
            $id = (int)$id;

            // Verificar si el producto existe
            $productoExistente = $this->getProductoById($id);
            if (!$productoExistente) {
                error_log('deleteProducto: Producto no encontrado con ID ' . $id);
                return false;
            }

            // Intentar eliminar relaciones con ingredientes primero
            try {
                $sqlIngredientes = 'DELETE FROM productos_has_ingredientes WHERE productos_id_producto = ?';
                $resultIngredientes = $this->mysql->prepararConsultaUpdate($sqlIngredientes, 'i', [$id]);
                if (!$resultIngredientes) {
                    error_log('deleteProducto: Error al eliminar relaciones con ingredientes para ID ' . $id);
                    // No retornamos false aquí porque queremos intentar eliminar el producto de todos modos
                }
            } catch (Exception $e) {
                error_log('deleteProducto: Error al intentar eliminar relaciones con ingredientes: ' . $e->getMessage());
            }

            // Eliminar el producto
            $sql = 'UPDATE productos SET productos.estado = 0 WHERE id_producto = ?';
            $result = $this->mysql->prepararConsultaUpdate($sql, 'i', [$id]);

            // Verificar si la consulta se ejecutó correctamente
            if ($result === false) {
                error_log('deleteProducto: Error al eliminar producto con ID ' . $id);
                file_put_contents('../controllers/debug.log', "[deleteProducto] Error al eliminar producto con ID " . $id . " " . mysqli_error($this->mysql->conexion) . "\n", FILE_APPEND);
                
                error_log('Error MySQL: ' . mysqli_error($this->mysql->conexion));
                return false;
            }

            error_log('deleteProducto: Producto eliminado exitosamente');
            return true;
        } catch (Exception $e) {
            file_put_contents('../controllers/debug.log', "[deleteProducto] Exception caught: " . $e->getMessage() . "\n", FILE_APPEND);    
            return false;
        }
    }

    public function obtenerIngredientes()
    {
        $sql = "SELECT id_ingrediente, nombre_ing FROM ingredientes ORDER BY nombre_ing";
        $result = $this->mysql->efectuarConsulta($sql);
        $ingredientes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ingredientes[] = $row;
        }
        return $ingredientes;
    }

    public function agregarIngredientesProducto($idProducto, $ingredientes)
    {
        foreach ($ingredientes as $ing) {
            $sql = "INSERT INTO productos_has_ingredientes (Productos_id_producto, Ingredientes_id_ingrediente, cantidad) VALUES (?, ?, ?)";
            $this->mysql->prepararConsultaInsert($sql, 'iid', [
                $idProducto,
                $ing['id'],
                $ing['cantidad']
            ]);
        }
    }

    public function obtenerIngredientesProducto($idProducto)
    {
        try {
            $sql = "SELECT 
                i.id_ingrediente,
                i.nombre_ing AS nombre,
                phi.cantidad,
                u.abrev_unidad AS unit,
                ci.titulo_categoria AS categoria
            FROM productos_has_ingredientes phi
            JOIN ingredientes i ON phi.Ingredientes_id_ingrediente = i.id_ingrediente
            JOIN unidades u ON i.Unidades_id_unidad = u.id_unidad
            JOIN categorias_ingredientes ci ON i.Categorias_Ingredientes_id_categoria = ci.id_categoria
            WHERE phi.Productos_id_producto = ?
            ORDER BY i.nombre_ing";

            $result = $this->mysql->prepararConsultaSelect($sql, 'i', [$idProducto]);

            if (!$result) {
                return [];
            }

            // Map categories to icon types
            $iconMap = [
                'Azúcares' => 'sugar',
                'Esencias' => 'spice',
                'Colorantes' => 'default',
                'Frutos secos' => 'vegetable',
                'Dulces' => 'candy',
                'Frutas' => 'fruit'
            ];

            // Add icon type to each ingredient
            foreach ($result as &$ingredient) {
                $ingredient['iconType'] = $iconMap[$ingredient['categoria']] ?? 'default';
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error al obtener ingredientes del producto: " . $e->getMessage());
            return [];
        }
    }

    public function eliminarIngredientesProducto($idProducto) {
        $sql = 'DELETE FROM productos_has_ingredientes WHERE productos_id_producto = ?';
        return $this->mysql->prepararConsultaUpdate($sql, 'i', [$idProducto]);
    }
}
