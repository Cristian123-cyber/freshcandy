<?php

class Ingredients
{
    public $lastQuery;
    public $lastParams;
    public $lastTipos;
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    public function getAllIngredients()
    {
        try {
            $sql = "SELECT i.*, 
                    ci.titulo_categoria as categoria,
                    u.abrev_unidad as unidad,
                    es.titulo_estado as estado
                    FROM ingredientes i
                    LEFT JOIN categorias_ingredientes ci ON i.Categorias_Ingredientes_id_categoria = ci.id_categoria
                    LEFT JOIN unidades u ON i.Unidades_id_unidad = u.id_unidad
                    LEFT JOIN estados_stock es ON i.Estados_Stock_id_estado = es.id_estado
                    ORDER BY i.id_ingrediente ASC";

            $result = $this->mysql->prepararConsultaSelect($sql);

            if (!$result) {
                return false;
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error al obtener los ingredientes: " . $e->getMessage());
            return false;
        }
    }

    public function getIngredientById($id)
    {
        try {
            $sql = "SELECT i.*, 
                    ci.titulo_categoria as categoria,
                    u.abrev_unidad as unidad,
                    es.titulo_estado as estado
                    FROM ingredientes i
                    LEFT JOIN categorias_ingredientes ci ON i.Categorias_Ingredientes_id_categoria = ci.id_categoria
                    LEFT JOIN unidades u ON i.Unidades_id_unidad = u.id_unidad
                    LEFT JOIN estados_stock es ON i.Estados_Stock_id_estado = es.id_estado
                    WHERE i.id_ingrediente = ?
                    LIMIT 1";

            $result = $this->mysql->prepararConsultaSelect($sql, "i", [$id]);

            if (!$result || empty($result)) {
                return false;
            }

            return $result[0];
        } catch (Exception $e) {
            error_log("Error al obtener el ingrediente: " . $e->getMessage());
            return false;
        }
    }

    public function getCategories()
    {
        try {
            $sql = "SELECT id_categoria, titulo_categoria 
                    FROM categorias_ingredientes 
                    ORDER BY titulo_categoria ASC";

            $result = $this->mysql->prepararConsultaSelect($sql);

            if (!$result) {
                return false;
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error al obtener las categorías: " . $e->getMessage());
            return false;
        }
    }

    public function getUnits()
    {
        try {
            $sql = "SELECT id_unidad, abrev_unidad, nombre_unidad 
                    FROM unidades 
                    ORDER BY nombre_unidad ASC";

            $result = $this->mysql->prepararConsultaSelect($sql);

            if (!$result) {
                return false;
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error al obtener las unidades: " . $e->getMessage());
            return false;
        }
    }

    public function updateIngredient($data)
    {
        try {
            // Determinar el nuevo estado del stock
            $estadoStock = 1; // Por defecto, estado óptimo
            if ($data['stock'] <= 0) {
                $estadoStock = 4; // Agotado
            } elseif ($data['stock'] <= $data['criticalLevel']) {
                $estadoStock = 3; // Crítico
            } elseif ($data['stock'] <= $data['lowLevel']) {
                $estadoStock = 2; // Bajo
            }

            $sql = "UPDATE ingredientes SET 
                    nombre_ing = ?,
                    Categorias_Ingredientes_id_categoria = ?,
                    stock_ing = ?,
                    Unidades_id_unidad = ?,
                    nivel_stock_critico = ?,
                    nivel_stock_bajo = ?,
                    Estados_Stock_id_estado = ?
                    WHERE id_ingrediente = ?";

            $params = [
                $data['name'],
                $data['category'],
                $data['stock'],
                $data['unit'],
                $data['criticalLevel'],
                $data['lowLevel'],
                $estadoStock,
                $data['id']
            ];

            $tipos = "sididdii";

            $result = $this->mysql->prepararConsulta($sql, $tipos, $params);

            if ($result === false) {
                error_log("Error al actualizar el ingrediente: " . $this->mysql->getError());
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar el ingrediente: " . $e->getMessage());
            return false;
        }
    }

    public function addIngredient($data)
    {
        try {
            // Determinar el estado del stock basado en los niveles
            $estadoStock = 1; // Por defecto, estado óptimo
            if ($data['stock'] <= $data['criticalLevel']) {
                $estadoStock = 3; // Crítico
            } elseif ($data['stock'] <= $data['lowLevel']) {
                $estadoStock = 2; // Bajo
            }

            $sql = "INSERT INTO ingredientes (
                    nombre_ing,
                    stock_ing,
                    nivel_stock_bajo,
                    nivel_stock_critico,
                    Categorias_Ingredientes_id_categoria,
                    Unidades_id_unidad,
                    Estados_Stock_id_estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $data['name'],
                $data['stock'],
                $data['lowLevel'],
                $data['criticalLevel'],
                $data['category'],
                $data['unit'],
                $estadoStock
            ];

            $tipos = "sdddiii";

            $result = $this->mysql->prepararConsulta($sql, $tipos, $params);

            if ($result === false) {
                error_log("Error al agregar el ingrediente: " . $this->mysql->getError());
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al agregar el ingrediente: " . $e->getMessage());
            return false;
        }
    }

    public function restockIngredient($data)
    {
        try {
            // Primero obtenemos el stock actual y los niveles
            $sql = "SELECT stock_ing, nivel_stock_bajo, nivel_stock_critico 
                    FROM ingredientes 
                    WHERE id_ingrediente = ?";

            $result = $this->mysql->prepararConsultaSelect($sql, "i", [$data['ingredientId']]);

            if (!$result || empty($result)) {
                error_log("Error al obtener el stock actual del ingrediente");
                return false;
            }

            $currentStock = $result[0]['stock_ing'];
            $newStock = $currentStock + $data['quantity'];

            // Determinar el nuevo estado del stock
            $estadoStock = 1; // Por defecto, estado óptimo
            if ($newStock <= $result[0]['nivel_stock_critico']) {
                $estadoStock = 3; // Crítico
            } elseif ($newStock <= $result[0]['nivel_stock_bajo']) {
                $estadoStock = 2; // Bajo
            }

            // Actualizar el stock y el estado
            $sql = "UPDATE ingredientes SET 
                    stock_ing = ?,
                    Estados_Stock_id_estado = ?
                    WHERE id_ingrediente = ?";

            $params = [
                $newStock,
                $estadoStock,
                $data['ingredientId']
            ];

            $tipos = "dii";

            $result = $this->mysql->prepararConsulta($sql, $tipos, $params);

            if ($result === false) {
                error_log("Error al actualizar el stock del ingrediente: " . $this->mysql->getError());
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al reabastecer el ingrediente: " . $e->getMessage());
            return false;
        }
    }

    public function deleteIngredient($id)
    {
        try {
            $sql = "DELETE FROM ingredientes WHERE id_ingrediente = ?";
            $result = $this->mysql->prepararConsulta($sql, "i", [$id]);

            if ($result === false) {
                error_log("Error al eliminar el ingrediente: " . $this->mysql->getError());
                return [
                    "success" => false,
                    "message" => "Error al eliminar el ingrediente: " . $this->mysql->getError()
                ];
            }

            return [
                "success" => true,
                "message" => "Ingrediente eliminado correctamente"
            ];
        } catch (Exception $e) {
            error_log("Error al eliminar el ingrediente: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Error al eliminar el ingrediente: " . $e->getMessage()
            ];
        }
    }

    public function getStockStates()
    {
        try {
            $sql = "SELECT id_estado, titulo_estado 
                    FROM estados_stock 
                    ORDER BY id_estado ASC";

            $result = $this->mysql->prepararConsultaSelect($sql);

            if (!$result) {
                return false;
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error al obtener los estados de stock: " . $e->getMessage());
            return false;
        }
    }

    public function searchIngredients($searchTerm, $categoryId = null, $stateId = null)
    {
        try {
            $sql = "SELECT i.*, 
                    ci.titulo_categoria as categoria,
                    u.abrev_unidad as unidad,
                    es.titulo_estado as estado
                    FROM ingredientes i
                    LEFT JOIN categorias_ingredientes ci ON i.Categorias_Ingredientes_id_categoria = ci.id_categoria
                    LEFT JOIN unidades u ON i.Unidades_id_unidad = u.id_unidad
                    LEFT JOIN estados_stock es ON i.Estados_Stock_id_estado = es.id_estado
                    WHERE (i.id_ingrediente LIKE ? OR i.nombre_ing LIKE ?)";

            $searchPattern = "%" . $searchTerm . "%";
            $params = [$searchPattern, $searchPattern];
            $tipos = "ss";

            if ($categoryId !== null && $categoryId !== '') {
                $sql .= " AND i.Categorias_Ingredientes_id_categoria = ?";
                $params[] = $categoryId;
                $tipos .= "i";
            }

            if ($stateId !== null && $stateId !== '') {
                $sql .= " AND i.Estados_Stock_id_estado = ?";
                $params[] = $stateId;
                $tipos .= "i";
            }

            $sql .= " ORDER BY i.id_ingrediente ASC";

            error_log("SQL Query: " . $sql);
            error_log("Params: " . print_r($params, true));
            error_log("Tipos: " . $tipos);

            $result = $this->mysql->prepararConsultaSelect($sql, $tipos, $params);

            // Si no hay resultados, devolvemos un array vacío en lugar de false
            if ($result === false) {
                error_log("Error en la consulta: ");
                return [
                    "success" => false,
                    "message" => "Error al buscar ingredientes: "
                ];
            }

            // Si hay resultados (incluso si es un array vacío), devolvemos éxito
            return [
                "success" => true,
                "message" => empty($result) ? "No se encontraron ingredientes" : "Ingredientes encontrados correctamente",
                "data" => $result
            ];
        } catch (Exception $e) {
            error_log("Error al buscar ingredientes: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Error al buscar ingredientes: " . $e->getMessage()
            ];
        }
    }

    public function getLowStockIngredients()
    {
        try {
            $sql = "SELECT i.*, 
                    ci.titulo_categoria as categoria,
                    u.abrev_unidad as unidad,
                    es.titulo_estado as estado
                    FROM ingredientes i
                    LEFT JOIN categorias_ingredientes ci ON i.Categorias_Ingredientes_id_categoria = ci.id_categoria
                    LEFT JOIN unidades u ON i.Unidades_id_unidad = u.id_unidad
                    LEFT JOIN estados_stock es ON i.Estados_Stock_id_estado = es.id_estado
                    WHERE i.Estados_Stock_id_estado IN (2, 3, 4)  -- 2 = Bajo, 3 = Crítico, 4 = Agotado
                    ORDER BY i.Estados_Stock_id_estado DESC, i.stock_ing ASC
                    LIMIT 5";

            $result = $this->mysql->prepararConsultaSelect($sql);

            // Si la consulta fue exitosa pero no hay resultados, devolvemos un array vacío
            if ($result === false) {
                return false; // Error real en la consulta
            }

            return $result; // Puede ser un array vacío o con resultados
        } catch (Exception $e) {
            error_log("Error al obtener los ingredientes con stock bajo: " . $e->getMessage());
            return false;
        }
    }
}
