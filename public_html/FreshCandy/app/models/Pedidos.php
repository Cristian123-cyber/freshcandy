<?php

class Pedidos
{
    public $lastQuery;
    public $lastParams;
    public $lastTipos;
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    public function getPaymentMethods()
    {
        try {





            $sql = "SELECT id_metodo AS id, metodo, descripcion, indicaciones FROM metodo_pagos";

            $sqlInfoCuentas = "SELECT * FROM payment_accounts";



            $result = $this->mysql->efectuarConsulta($sql);
            $resultInfoCuentas = $this->mysql->efectuarConsulta($sqlInfoCuentas);

            if (!$result || !$resultInfoCuentas) {
                return false;
            }


            //Reestructuramos el resultado para que sea un array asociativo

            $paymentAccounts = [];
            while ($row = $resultInfoCuentas->fetch_assoc()) {
                $paymentAccounts[] = $row;
            }

            // Combinar los métodos de pago con la información de las cuentas
            $paymentMethods = [];
            while ($row = $result->fetch_assoc()) {
                $row['accounts'] = $paymentAccounts;
                $paymentMethods[] = $row;
            }


            return $paymentMethods;
        } catch (Exception $e) {
            error_log("Error al obtener los métodos de pago: " . $e->getMessage());
            return false;
        }
    }

    public function getDeliveryMethods()
    {
        try {

            $sql = "SELECT id_metodo AS id, nombre_metodo AS metodo, recargo FROM metodos_envio";

            $result = $this->mysql->efectuarConsulta($sql);

            if (!$result) {
                return false;
            }

            //Reestructuramos el resultado para que sea un array asociativo
            $deliveryMethods = [];
            while ($row = $result->fetch_assoc()) {
                $deliveryMethods[] = $row;
            }

            return $deliveryMethods;
        } catch (Exception $e) {
            error_log("Error al obtener los métodos de entrega: " . $e->getMessage());
            return false;
        }
    }

    public function validatePromoCode($code)
    {
        try {
            // Sanitize the code
            $code = $this->mysql->escapar($code);

            // Query to check if the promo code exists and is valid
            $sql = "SELECT id_codigo, porcentaje_descuento, titulo 
                    FROM descuentos 
                    WHERE codigo_promocional = ? 
                    AND porcentaje_descuento > 0";

            $result = $this->mysql->prepararConsultaSelect($sql, "s", [$code]);

            if (!$result || empty($result)) {
                return false;
            }

            // Como prepararConsultaSelect ya devuelve el array, tomamos el primer elemento
            $promoCode = $result[0];
            return [
                'id' => $promoCode['id_codigo'],
                'porcentaje' => $promoCode['porcentaje_descuento'],
                'titulo' => $promoCode['titulo']
            ];
        } catch (Exception $e) {
            error_log("Error al validar el código promocional: " . $e->getMessage());
            return false;
        }
    }

    private function obtenerCarritoInfo()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : null;

        if (!$carrito || !is_array($carrito) || empty($carrito) || !isset($carrito['items']) || !is_array($carrito['items']) || empty($carrito['items'])) {

            return false;
        } else {
            return $carrito;
        }
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
    public function createOrder($data)
    {
        try {

            $sql = "
                    INSERT INTO pedidos (fecha, monto_total, direccion_envio, nombre_destinatario, telefono_destinatario, ciudad_destino, notas_adicionales, Metodos_Envio_id_metodo, Descuentos_id_codigo, Metodo_pagos_id_metodo, Estados_pedido_id_estado, Clientes_id_cliente) VALUES 
                    (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)";
            $tipos = "dsssssiiii";  // Cadena de tipos para bind_param()




            $adress = $data['customer']['address'];
            $name = $data['customer']['name'];
            $phone = $data['customer']['phone'];
            $city = $data['customer']['city'];
            $notes = $data['notes'];
            $deliveryMethod = $data['deliveryMethod'];
            $paymentMethod = $data['paymentMethod'];

            $carrito = $this->obtenerCarritoInfo();



            if (!$carrito) {
                return [
                    'success' => false,
                    'message' => "Error al obtener el carrito"
                ];
            }



            $totalAmount = $carrito['subtotal'];
            $discountId = 4; //id sin descuento por defecto



            if ($data['infoDescuento']['aplica'] == true) {
                $discountId = $data['infoDescuento']['id'];

                // Get discount percentage from database
                $discountSql = "SELECT porcentaje_descuento FROM descuentos WHERE id_codigo = ? LIMIT 1";
                $discountResult = $this->mysql->prepararConsultaSelect($discountSql, "i", [$discountId]);

                if ($discountResult && !empty($discountResult)) {
                    $discountPercentage = $discountResult[0]['porcentaje_descuento'];
                    $totalAmount = $totalAmount * (1 - (floatval($discountPercentage) / 100));
                }
            }



            // Redondea el monto total a 2 decimales para evitar problemas con los centavos
            $totalAmount = round($totalAmount, 2);



            if ($deliveryMethod == 1) {
                // Consultar el recargo del método de envío
                $deliveryChargeSql = "SELECT recargo FROM metodos_envio WHERE id_metodo = ? LIMIT 1";
                $deliveryChargeResult = $this->mysql->prepararConsultaSelect($deliveryChargeSql, "i", [$deliveryMethod]);



                if ($deliveryChargeResult && !empty($deliveryChargeResult)) {
                    $deliveryCharge = floatval($deliveryChargeResult[0]['recargo']);
                    $totalAmount += $deliveryCharge;
                }
            }




            //obtener el id del cliente
            $clienteId = $this->obtenerIdCliente();

            if (!$clienteId) {
                return [
                    'success' => false,
                    'message' => "Error al obtener el id del cliente"
                ];
            }

            // Prepare parameters array for the main query
            $params = [
                $totalAmount,
                $adress,
                $name,
                $phone,
                $city,
                $notes,
                $deliveryMethod,
                $discountId,
                $paymentMethod,
                $clienteId
            ];


            $result = $this->mysql->prepararConsultaInsert($sql, $tipos, $params);



            if (!$result['success']) {
                return [
                    'success' => false,
                    'message' => $result['message']
                ];
            }

            $pedidoId = $result['id'];

            //Insertar los productos del carrito en la tabla de productos pedidos
            $resultInsertarProductos = $this->insertarProductosPedidos($pedidoId, $carrito);

            if (!$resultInsertarProductos) {
                return [
                    'success' => false,
                    'message' => "Error al insertar los productos del carrito"
                ];
            }

            //Limpiar el carrito
            $this->limpiarCarrito();

            return [
                'success' => true,
                'message' => "Pedido creado con éxito",
                'pedidoId' => $pedidoId
            ];
        } catch (Exception $e) {
            error_log("Error al crear el pedido: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Error al crear el pedido desde aqui"
            ];
        }
    }

    private function limpiarCarrito()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpiar solo la parte del carrito
        if (isset($_SESSION['carrito'])) {
            unset($_SESSION['carrito']);
        }
    }


    private function insertarProductosPedidos($pedidoId, $carrito)
    {

        foreach ($carrito['items'] as $item) {
            $productoId = $item['id'];
            $cantidad = $item['cantidad'];
            $precio = $item['precio'];

            $sql = "INSERT INTO pedidos_has_productos (Pedidos_id_pedido, Productos_id_producto, cantidad, precio_prod) VALUES (?, ?, ?, ?)";
            $tipos = "iiid";
            $params = [$pedidoId, $productoId, $cantidad, $precio];

            $result = $this->mysql->prepararConsulta($sql, $tipos, $params);

            if (!$result) {
                return false;
            }
        }

        return true;
    }


    public function getOrderById($id)
    {
        try {
            // Validación básica del ID
            if (!is_numeric($id) || $id <= 0) {
                throw new InvalidArgumentException("ID de producto no válido");
            }


            $id = (int) $id;


            // Consulta SQL con parámetro escapado para seguridad
            $sql = "SELECT 
                id_pedido AS id, 
                Metodos_Envio_id_metodo AS deliveryMethod,
                Metodo_pagos_id_metodo AS paymentMethod,
                nombre_destinatario AS customerName,
                telefono_destinatario AS customerPhone,
                direccion_envio AS customerAddress,
                ciudad_destino AS customerCity,
                notas_adicionales AS notes,
                fecha AS orderDate,
                monto_total AS total,
                Descuentos_id_codigo AS discountId,
                Estados_pedido_id_estado AS statusId,
                Clientes_id_cliente AS customerId
                FROM pedidos 
                WHERE id_pedido = ?
                LIMIT 1";

            $result = $this->mysql->prepararConsultaSelect($sql, "i", [$id]);

            //si no hay resultados, devuelve null

            return ($result && !empty($result)) ? $result[0] : null;
        } catch (\Throwable $th) {

            error_log("Error: " . $th->getMessage());
            return false;
        }
    }

    //Función que actualiza el stock de los ingredientes del pedido según cambios de estado
    public function updateIngredientStock($orderId, $estado, $oldEstado)
    {
        try {
            $estadosNoConsumen = [1, 5]; // 1=pendiente, 5=cancelado
            

            $estadosConsumen = [2, 3, 4]; // 2=en preparación, 3=enviado, 4=entregado

            $estadoAnteriorConsumia = in_array($oldEstado, $estadosConsumen);
            $estadoNuevoConsume = in_array($estado, $estadosConsumen);

            // Lógica de decisión:
            // - Si el estado anterior consumía y el nuevo no consume: RESTAURAR stock
            // - Si el estado anterior no consumía y el nuevo sí consume: RESTAR stock
            // - Si ambos consumen o ambos no consumen: NO hacer nada

            if ($estadoAnteriorConsumia === $estadoNuevoConsume) {
                // No hay cambio en el consumo de stock
                return true;
            }

            // Determinar la operación a realizar
            $operacion = '';
            if ($estadoAnteriorConsumia && !$estadoNuevoConsume) {
                $operacion = 'RESTAURAR'; // Sumar al stock
            } else if (!$estadoAnteriorConsumia && $estadoNuevoConsume) {
                $operacion = 'RESTAR'; // Restar del stock
            }

            // Obtener los productos del pedido
            $sql = "SELECT Productos_id_producto, cantidad 
                FROM pedidos_has_productos 
                WHERE Pedidos_id_pedido = ?";

            $orderProducts = $this->mysql->prepararConsultaSelect($sql, "i", [$orderId]);

            if (!$orderProducts) {
                return false;
            }

            // Para cada producto en el pedido
            foreach ($orderProducts as $product) {
                $productId = $product['Productos_id_producto'];
                $productQuantity = $product['cantidad'];

                // Obtener todos los ingredientes y sus cantidades para este producto
                $sql = "SELECT Ingredientes_id_ingrediente, cantidad 
                    FROM productos_has_ingredientes
                    WHERE Productos_id_producto = ?";

                $productIngredients = $this->mysql->prepararConsultaSelect($sql, "i", [$productId]);

                if (!$productIngredients) {
                    continue;
                }


                file_put_contents('../controllers/debug.log', "[updateIngredientStock] Product ID: $productId, Quantity: $productQuantity, Operation: $operacion\n", FILE_APPEND);

                // Para cada ingrediente, actualizar su stock
                foreach ($productIngredients as $ingredient) {
                    $ingredientId = $ingredient['Ingredientes_id_ingrediente'];
                    $ingredientQuantity = $ingredient['cantidad'];

                    // Calcular la cantidad total necesaria (cantidad de ingrediente * cantidad de producto)
                    $totalNeeded = $ingredientQuantity * $productQuantity;

                    // Preparar la consulta según la operación
                    if ($operacion === 'RESTAURAR') {
                        // Si cancelamos o ponemos en pendiente, restauramos el stock
                        $sqlStock = "UPDATE ingredientes 
                                SET stock_ing = stock_ing + ? 
                                WHERE id_ingrediente = ?";
                    } else if ($operacion === 'RESTAR') {
                        // Si pasamos a preparación/envío/entrega, restamos del stock
                        $sqlStock = "UPDATE ingredientes 
                                SET stock_ing = stock_ing - ? 
                                WHERE id_ingrediente = ?";
                    }

                    // Ejecutar la actualización del stock
                    $result = $this->mysql->prepararConsulta($sqlStock, "di", [$totalNeeded, $ingredientId]);

                    if (!$result) {
                        error_log("Error updating stock for ingredient ID: " . $ingredientId . " - Operation: " . $operacion);
                        continue;
                    }

                    // Actualizar el estado del stock basado en el nuevo nivel de stock
                    $sqlEstado = "UPDATE ingredientes 
                            SET Estados_Stock_id_estado = 
                                CASE 
                                    WHEN stock_ing <= 0 THEN 4
                                    WHEN stock_ing <= nivel_stock_critico THEN 3
                                    WHEN stock_ing <= nivel_stock_bajo THEN 2
                                    ELSE 1
                                END
                            WHERE id_ingrediente = ?";

                    $this->mysql->prepararConsulta($sqlEstado, "i", [$ingredientId]);

                    
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Error updating ingredient stock: " . $e->getMessage());
            return false;
        }
    }




    public function getPedidos($fecha = null, $estado = null, $tituloOcontenido = null)
    {
        try {
            $sql = "SELECT p.*, 
                    c.nombre_cliente as nombre_cliente,
                    e.titulo_estado as titulo_estado,
                    mp.metodo as metodo_pago,
                    me.nombre_metodo as metodo_envio
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.Clientes_id_cliente = c.id_cliente
                    LEFT JOIN estados_pedido e ON p.Estados_pedido_id_estado = e.id_estado
                    LEFT JOIN metodo_pagos mp ON p.Metodo_pagos_id_metodo = mp.id_metodo
                    LEFT JOIN metodos_envio me ON p.Metodos_Envio_id_metodo = me.id_metodo
                    WHERE 1=1";

            $params = [];
            $tipos = "";

            // Filtro por fecha
            if ($fecha) {
                $sql .= " AND DATE(p.fecha) = ?";
                $params[] = $fecha;
                $tipos .= "s";
            }

            // Filtro por estado
            if ($estado) {
                $sql .= " AND p.Estados_pedido_id_estado = ?";
                $params[] = $estado;
                $tipos .= "i";
            }

            // Filtro por título o contenido
            if ($tituloOcontenido) {
                $sql .= " AND (
                    CAST(p.id_pedido AS CHAR) LIKE ? OR 
                    p.notas_adicionales LIKE ? OR 
                    c.nombre_cliente LIKE ? OR
                    p.nombre_destinatario LIKE ?
                )";
                $searchTerm = "%" . $tituloOcontenido . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $tipos .= "ssss";
            }

            // Ordenar por fecha más reciente
            $sql .= " ORDER BY p.fecha DESC";

            // Guardar la consulta y parámetros para depuración
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            $this->lastTipos = $tipos;

            $result = $this->mysql->prepararConsultaSelect2($sql, $tipos, $params);

            // Si hay un error real en la consulta
            if ($result === false) {
                error_log("Error SQL getPedidos: Error en la consulta");
                return false;
            }

            // Retornamos el resultado (que será un array vacío si no hay resultados)
            return $result;
        } catch (Exception $e) {
            error_log("Error SQL getPedidos: " . $e->getMessage());
            if (isset($this->mysql) && method_exists($this->mysql, 'getLastError')) {
                error_log("MySQL error: " . $this->mysql->getLastError());
            }
            return false;
        }
    }

    public function updateOrderStatus($id, $nuevoEstado)
    {
        try {
            $sql = "UPDATE pedidos SET Estados_pedido_id_estado = ? WHERE id_pedido = ?";
            $result = $this->mysql->prepararConsulta($sql, "ii", [$nuevoEstado, $id]);
            return $result ? true : false;
        } catch (Exception $e) {
            error_log("Error al actualizar el estado del pedido: " . $e->getMessage());
            return false;
        }
    }

    public function getOrderStates()
    {
        try {
            $sql = "SELECT id_estado, titulo_estado FROM estados_pedido ORDER BY id_estado ASC";
            $result = $this->mysql->prepararConsultaSelect($sql);



            return $result;
        } catch (Exception $e) {
            error_log("Error al obtener los estados de pedido: " . $e->getMessage());
            return false;
        }
    }

    public function getSalesByWeek()
    {
        try {
            // Obtener el lunes de la semana actual usando timestamp
            $currentTimestamp = time();
            $dayOfWeek = date('N', $currentTimestamp); // 1 (lunes) a 7 (domingo)
            $daysToSubtract = $dayOfWeek - 1; // Días a restar para llegar al lunes

            $startDate = date('Y-m-d', $currentTimestamp - ($daysToSubtract * 86400));
            $endDate = date('Y-m-d', $currentTimestamp);

            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Current timestamp: " . $currentTimestamp . "\n", FILE_APPEND);
            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Day of week: " . $dayOfWeek . "\n", FILE_APPEND);
            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Start date: " . $startDate . " End date: " . $endDate . "\n", FILE_APPEND);

            // Verificar si hay pedidos en la base de datos
            $checkSql = "SELECT COUNT(*) as total FROM pedidos";
            $checkResult = $this->mysql->prepararConsultaSelect($checkSql);
            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Total orders in DB: " . print_r($checkResult, true) . "\n", FILE_APPEND);

            $sql = "SELECT 
                    DATE(fecha) as fecha,
                    COUNT(*) as total_pedidos
                    FROM pedidos 
                    WHERE DATE(fecha) >= ?
                    AND DATE(fecha) <= ?
                    AND Estados_pedido_id_estado NOT IN (5) -- Excluir pedidos cancelados
                    GROUP BY DATE(fecha)
                    ORDER BY fecha ASC";

            file_put_contents('../controllers/debug.log', "[getSalesByWeek] SQL Query: " . $sql . "\n", FILE_APPEND);
            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Query params: " . $startDate . ", " . $endDate . "\n", FILE_APPEND);

            $result = $this->mysql->prepararConsultaSelect($sql, "ss", [$startDate, $endDate]);

            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Query result: " . print_r($result, true) . "\n", FILE_APPEND);

            // Crear array con todos los días de la semana
            $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            $defaultData = array_fill(0, 7, 0);

            // Si hay resultados, procesarlos
            if ($result) {
                foreach ($result as $row) {
                    $date = new DateTime($row['fecha']);
                    $dayOfWeek = $date->format('N') - 1; // 0-6 para Lunes-Domingo
                    $defaultData[$dayOfWeek] = intval($row['total_pedidos']);
                    file_put_contents('../controllers/debug.log', "[getSalesByWeek] Processing date: " . $row['fecha'] . " Day of week: " . $dayOfWeek . " Total orders: " . $row['total_pedidos'] . "\n", FILE_APPEND);
                }
            }

            // Solo mostrar los días hasta hoy
            $today = new DateTime();
            $dayOfWeek = $today->format('N') - 1; // 0-6 para Lunes-Domingo
            $labels = array_slice($days, 0, $dayOfWeek + 1);
            $data = array_slice($defaultData, 0, $dayOfWeek + 1);

            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Final labels: " . print_r($labels, true) . "\n", FILE_APPEND);
            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Final data: " . print_r($data, true) . "\n", FILE_APPEND);

            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (Exception $e) {
            file_put_contents('../controllers/debug.log', "[getSalesByWeek] Exception caught: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    public function getSalesByMonth()
    {
        try {
            // Obtener el mes y año actual
            $currentMonth = date('m');
            $currentYear = date('Y');

            // Obtener la primera semana del mes
            $firstDayOfMonth = date('Y-m-d', strtotime("$currentYear-$currentMonth-01"));
            $firstWeekOfMonth = date('W', strtotime($firstDayOfMonth));

            // Obtener la semana actual
            $currentWeek = date('W');

            // Calcular el índice de la semana actual (0-3)
            $currentWeekIndex = $currentWeek - $firstWeekOfMonth;

            // Consulta SQL para obtener los pedidos del mes actual
            $sql = "SELECT 
                    WEEK(fecha) as semana,
                    COUNT(*) as total
                    FROM pedidos 
                    WHERE MONTH(fecha) = ?
                    AND YEAR(fecha) = ?
                    AND Estados_pedido_id_estado NOT IN (5)
                    GROUP BY WEEK(fecha)
                    ORDER BY semana ASC";

            $result = $this->mysql->prepararConsultaSelect($sql, "ii", [$currentMonth, $currentYear]);

            // Inicializar arrays para las 4 semanas del mes
            $labels = ["Semana 1", "Semana 2", "Semana 3", "Semana 4"];
            $data = array_fill(0, 4, 0);

            // Si hay resultados, procesarlos
            if ($result) {
                foreach ($result as $row) {
                    $weekNumber = intval($row['semana']);
                    $weekIndex = $weekNumber - $firstWeekOfMonth;

                    if ($weekIndex >= 0 && $weekIndex < 4) {
                        $data[$weekIndex] = intval($row['total']);
                    }
                }
            }

            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (Exception $e) {
            error_log("Error al obtener ventas por mes: " . $e->getMessage());
            return false;
        }
    }

    public function getSalesByYear()
    {
        try {
            $sql = "SELECT 
                    MONTH(fecha) as mes,
                    COUNT(*) as total
                    FROM pedidos 
                    WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY MONTH(fecha)
                    ORDER BY mes ASC";

            $result = $this->mysql->prepararConsultaSelect($sql);



            $months = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            ];

            $labels = [];
            $data = array_fill(0, 12, 0);

            if ($result) {
                foreach ($result as $row) {
                    $monthIndex = intval($row['mes']) - 1;
                    $data[$monthIndex] = intval($row['total']);
                }
            }

            return [
                'labels' => array_values($months),
                'data' => $data
            ];
        } catch (Exception $e) {
            error_log("Error al obtener ventas por año: " . $e->getMessage());
            return false;
        }
    }

    public function getPopularProducts()
    {
        try {
            // Primero obtenemos todos los productos
            $sqlProducts = "SELECT id_producto, nombre_producto FROM productos";
            $allProducts = $this->mysql->prepararConsultaSelect($sqlProducts);

            // Luego obtenemos las ventas
            $sql = "SELECT 
                    p.nombre_producto,
                    SUM(pp.cantidad) as total_pedidos
                    FROM productos p
                    LEFT JOIN pedidos_has_productos pp ON p.id_producto = pp.Productos_id_producto
                    LEFT JOIN pedidos ped ON pp.Pedidos_id_pedido = ped.id_pedido
                    WHERE ped.Estados_pedido_id_estado != 5 OR ped.Estados_pedido_id_estado IS NULL AND p.estado = 1
                    GROUP BY p.id_producto
                    ORDER BY total_pedidos DESC";

            $result = $this->mysql->prepararConsultaSelect($sql);

            $labels = [];
            $data = [];

            // Si no hay resultados de ventas, usamos todos los productos con valor 0
            if (!$result || empty($result)) {
                foreach ($allProducts as $product) {
                    $labels[] = $product['nombre_producto'];
                    $data[] = 0;
                }
            } else {
                // Si hay ventas, procesamos los resultados
                foreach ($result as $row) {
                    $labels[] = $row['nombre_producto'];
                    $data[] = intval($row['total_pedidos']);
                }
            }

            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (Exception $e) {
            error_log("Error al obtener productos populares: " . $e->getMessage());
            return false;
        }
    }

    public function getOrderDetails($orderId)
    {
        try {
            // Get basic order info with joins
            $sql = "SELECT 
                p.id_pedido,
                p.fecha,
                p.monto_total,
                p.direccion_envio,
                p.nombre_destinatario,
                p.telefono_destinatario,
                p.ciudad_destino,
                p.notas_adicionales,
                p.Metodos_Envio_id_metodo,
                p.Descuentos_id_codigo,
                p.Metodo_pagos_id_metodo,
                p.Estados_pedido_id_estado,
                p.Clientes_id_cliente,
                c.nombre_cliente,
                c.cedula,
                e.titulo_estado,
                mp.metodo as metodo_pago,
                mp.descripcion as descripcion_pago,
                me.nombre_metodo as metodo_envio,
                me.recargo as recargo_envio,
                d.codigo_promocional,
                d.porcentaje_descuento,
                d.titulo as titulo_descuento
                FROM pedidos p
                LEFT JOIN clientes c ON p.Clientes_id_cliente = c.id_cliente
                LEFT JOIN estados_pedido e ON p.Estados_pedido_id_estado = e.id_estado
                LEFT JOIN metodo_pagos mp ON p.Metodo_pagos_id_metodo = mp.id_metodo
                LEFT JOIN metodos_envio me ON p.Metodos_Envio_id_metodo = me.id_metodo
                LEFT JOIN descuentos d ON p.Descuentos_id_codigo = d.id_codigo
                WHERE p.id_pedido = ?
                LIMIT 1";

            $result = $this->mysql->prepararConsultaSelect($sql, "i", [$orderId]);

            if (!$result || empty($result)) {
                return false;
            }

            $orderDetails = $result[0];

            // Get order products
            $sqlProducts = "SELECT 
                pp.Productos_id_producto,
                pp.cantidad,
                pp.precio_prod,
                p.nombre_producto
                FROM pedidos_has_productos pp
                JOIN productos p ON pp.Productos_id_producto = p.id_producto
                WHERE pp.Pedidos_id_pedido = ?";

            $products = $this->mysql->prepararConsultaSelect($sqlProducts, "i", [$orderId]);

            if ($products === false) {
                return false;
            }

            // Calculate subtotal from products
            $subtotal = 0;
            foreach ($products as $product) {
                $subtotal += $product['precio_prod'] * $product['cantidad'];
            }

            // Add products and subtotal to order details
            $orderDetails['productos'] = $products;
            $orderDetails['subtotal'] = $subtotal;

            return $orderDetails;
        } catch (Exception $e) {
            error_log("Error al obtener detalles del pedido: " . $e->getMessage());
            return false;
        }
    }

    public function getOrderStatusById($id)
    {
        try {
            // Validación básica del ID
            if (!is_numeric($id) || $id <= 0) {
                throw new InvalidArgumentException("ID de estado no válido");
            }

            $id = (int) $id;

            // Consulta SQL con parámetro escapado para seguridad
            $sql = "SELECT pedidos.Estados_pedido_id_estado AS estado FROM pedidos    
                    WHERE pedidos.id_pedido = ?
                    LIMIT 1";

            $result = $this->mysql->prepararConsultaSelect($sql, "i", [$id]);

            //si no hay resultados, devuelve null
            return ($result && !empty($result)) ? $result[0] : null;
        } catch (\Throwable $th) {
            error_log("Error: " . $th->getMessage());
            return false;
        }
    }


    public function getRecentOrders()
    {
        try {
            $sql = "SELECT p.*, 
                    c.nombre_cliente as nombre_cliente,
                    e.titulo_estado as titulo_estado,
                    mp.metodo as metodo_pago,
                    me.nombre_metodo as metodo_envio
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.Clientes_id_cliente = c.id_cliente
                    LEFT JOIN estados_pedido e ON p.Estados_pedido_id_estado = e.id_estado
                    LEFT JOIN metodo_pagos mp ON p.Metodo_pagos_id_metodo = mp.id_metodo
                    LEFT JOIN metodos_envio me ON p.Metodos_Envio_id_metodo = me.id_metodo
                    ORDER BY p.fecha DESC
                    LIMIT 5";

            $result = $this->mysql->prepararConsultaSelect($sql);

            if (!$result) {
                return false;
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error al obtener los pedidos recientes: " . $e->getMessage());
            return false;
        }
    }
}
