-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 27-09-2025 a las 19:24:48
-- Versión del servidor: 8.0.43
-- Versión de PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `freshcandy_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_administrador` int NOT NULL,
  `nombre_usuario` varchar(100) DEFAULT NULL,
  `correo_admin` varchar(200) DEFAULT NULL,
  `password` varchar(1000) DEFAULT NULL,
  `Roles_id_rol` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_administrador`, `nombre_usuario`, `correo_admin`, `password`, `Roles_id_rol`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$12$/g50ijp3euU7WVK2YDFjbudzl2nHm6rEI1yuhMaEnvYvBUYJvnuQK', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_ingredientes`
--

CREATE TABLE `categorias_ingredientes` (
  `id_categoria` int NOT NULL,
  `titulo_categoria` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `categorias_ingredientes`
--

INSERT INTO `categorias_ingredientes` (`id_categoria`, `titulo_categoria`) VALUES
(1, 'Azúcares'),
(2, 'Esencias'),
(3, 'Colorantes'),
(4, 'Frutos secos'),
(5, 'Dulces'),
(6, 'Frutas'),
(7, 'Lacteos'),
(9, 'Ingrediente Saborizante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int NOT NULL,
  `nombre_cliente` varchar(200) DEFAULT NULL,
  `cedula` varchar(200) DEFAULT NULL,
  `correo_cliente` varchar(200) DEFAULT NULL,
  `telefono_cliente` varchar(200) DEFAULT NULL,
  `direccion_envio` varchar(500) DEFAULT NULL,
  `password` varchar(1000) NOT NULL,
  `Roles_id_rol` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuentos`
--

CREATE TABLE `descuentos` (
  `id_codigo` int NOT NULL,
  `codigo_promocional` varchar(100) DEFAULT NULL,
  `porcentaje_descuento` int DEFAULT NULL,
  `titulo` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `descuentos`
--

INSERT INTO `descuentos` (`id_codigo`, `codigo_promocional`, `porcentaje_descuento`, `titulo`) VALUES
(1, 'BIENVENIDA25', 25, 'Descuento bienvenida'),
(2, 'VERANO15', 15, 'Descuento verano'),
(3, 'FIDELIDAD10', 10, 'Descuento clientes fieles'),
(4, NULL, 0, 'Sin descuento');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_pedido`
--

CREATE TABLE `estados_pedido` (
  `id_estado` int NOT NULL,
  `titulo_estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `estados_pedido`
--

INSERT INTO `estados_pedido` (`id_estado`, `titulo_estado`) VALUES
(1, 'Pendiente'),
(2, 'En preparacion'),
(3, 'Enviado'),
(4, 'Entregado'),
(5, 'Cancelado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_stock`
--

CREATE TABLE `estados_stock` (
  `id_estado` int NOT NULL,
  `titulo_estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `estados_stock`
--

INSERT INTO `estados_stock` (`id_estado`, `titulo_estado`) VALUES
(1, 'Optimo'),
(2, 'Bajo'),
(3, 'Crítico'),
(4, 'Agotado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_sugerencias`
--

CREATE TABLE `estado_sugerencias` (
  `id_estado` int NOT NULL,
  `nombre_estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `estado_sugerencias`
--

INSERT INTO `estado_sugerencias` (`id_estado`, `nombre_estado`) VALUES
(1, 'Pendiente'),
(2, 'Revisada'),
(3, 'Eliminada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas_producto`
--

CREATE TABLE `etiquetas_producto` (
  `id_etiqueta` int NOT NULL,
  `titulo_etiqueta` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `etiquetas_producto`
--

INSERT INTO `etiquetas_producto` (`id_etiqueta`, `titulo_etiqueta`) VALUES
(1, 'Popular'),
(2, 'Edición limitada'),
(3, 'Nuevo'),
(4, 'Favorito'),
(5, 'Sin etiqueta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id_ingrediente` int NOT NULL,
  `nombre_ing` varchar(100) DEFAULT NULL,
  `stock_ing` decimal(10,2) DEFAULT NULL,
  `nivel_stock_bajo` decimal(10,2) DEFAULT NULL,
  `nivel_stock_critico` decimal(10,2) DEFAULT NULL,
  `Categorias_Ingredientes_id_categoria` int NOT NULL,
  `Unidades_id_unidad` int NOT NULL,
  `Estados_Stock_id_estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `ingredientes`
--

INSERT INTO `ingredientes` (`id_ingrediente`, `nombre_ing`, `stock_ing`, `nivel_stock_bajo`, `nivel_stock_critico`, `Categorias_Ingredientes_id_categoria`, `Unidades_id_unidad`, `Estados_Stock_id_estado`) VALUES
(14, 'Leche', 22.40, 20.00, 10.00, 7, 3, 1),
(15, 'Crema de leche', 5500.00, 20.00, 10.00, 7, 4, 1),
(17, 'Leche condensada', 7000.00, 30.00, 10.00, 7, 4, 1),
(18, 'Fresas', 98.20, 30.00, 10.00, 6, 1, 1),
(19, 'Mango', 99.80, 20.00, 10.00, 6, 1, 1),
(20, 'Café', 6400.00, 20.00, 10.00, 9, 2, 1),
(21, 'Caramelo', 400.00, 20.00, 10.00, 5, 4, 1),
(23, 'Chocolate', 205.00, 30.00, 20.00, 5, 1, 1),
(24, 'Mora', 1050.00, 50.00, 30.00, 6, 2, 1);

--
-- Disparadores `ingredientes`
--
DELIMITER $$
CREATE TRIGGER `trg_evitar_stock_negativo` BEFORE UPDATE ON `ingredientes` FOR EACH ROW BEGIN
    IF NEW.stock_ing < 0 THEN
        SET NEW.stock_ing = 0;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_envio`
--

CREATE TABLE `metodos_envio` (
  `id_metodo` int NOT NULL,
  `nombre_metodo` varchar(45) DEFAULT NULL,
  `recargo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `metodos_envio`
--

INSERT INTO `metodos_envio` (`id_metodo`, `nombre_metodo`, `recargo`) VALUES
(1, 'Entrega a domicilio', 2000.00),
(2, 'Recogida en tienda', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pagos`
--

CREATE TABLE `metodo_pagos` (
  `id_metodo` int NOT NULL,
  `metodo` varchar(100) DEFAULT NULL,
  `descripcion` varchar(500) NOT NULL,
  `indicaciones` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `metodo_pagos`
--

INSERT INTO `metodo_pagos` (`id_metodo`, `metodo`, `descripcion`, `indicaciones`) VALUES
(1, 'Pagar al recibir', 'Paga en efectivo o con tarjeta al momento de recibir tu pedido. Opción disponible para entregas a domicilio.', 'Ten el pago exacto preparado\nAceptamos efectivo y tarjetas (débito/crédito)\nEl repartidor traerá datáfono para pagos con tarjeta\nVerifica que el monto coincida con tu total'),
(2, 'Transferencia', 'Transfiere el valor de tu pedido a nuestras cuentas bancarias. Envíanos el comprobante por WhatsApp para confirmar.', 'Realiza la transferencia a cualquiera de nuestras cuentas\nToma captura del comprobante\nEnvíalo a nuestro WhatsApp (Indicado abajo, da click al icono de WhatsApp para ir al chat)\nImportante: tu pedido no será procesado hasta que el pago sea verificado.\nIncluye tu nombre y # de pedido en el mensaje');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_accounts`
--

CREATE TABLE `payment_accounts` (
  `id_cuenta` int NOT NULL,
  `entidad` varchar(200) NOT NULL,
  `numero_cuenta` varchar(500) NOT NULL,
  `titular` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `payment_accounts`
--

INSERT INTO `payment_accounts` (`id_cuenta`, `entidad`, `numero_cuenta`, `titular`) VALUES
(1, 'Nequi', '3226851705', 'Fresh Candy'),
(2, 'Bancolombia', 'XXXXXXXXXXXXXXXXXXX', 'Fresh Candy'),
(3, 'Daviplata', 'XXXXXXXXXXXXXXXXXXX', 'Fresh Candy'),
(5, 'WhatsApp', '3226851705', 'Fresh Candy');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT NULL,
  `direccion_envio` varchar(200) DEFAULT NULL,
  `nombre_destinatario` varchar(200) DEFAULT NULL,
  `telefono_destinatario` varchar(100) DEFAULT NULL,
  `ciudad_destino` varchar(100) DEFAULT NULL,
  `notas_adicionales` varchar(1000) DEFAULT NULL,
  `Metodos_Envio_id_metodo` int NOT NULL,
  `Descuentos_id_codigo` int NOT NULL,
  `Metodo_pagos_id_metodo` int NOT NULL,
  `Estados_pedido_id_estado` int NOT NULL,
  `Clientes_id_cliente` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_has_productos`
--

CREATE TABLE `pedidos_has_productos` (
  `Pedidos_id_pedido` int NOT NULL,
  `Productos_id_producto` int NOT NULL,
  `cantidad` int DEFAULT NULL,
  `precio_prod` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int NOT NULL,
  `nombre_producto` varchar(100) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `precio_producto` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(1000) DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `Etiquetas_producto_id_etiqueta` int NOT NULL,
  `estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_has_ingredientes`
--

CREATE TABLE `productos_has_ingredientes` (
  `Productos_id_producto` int NOT NULL,
  `Ingredientes_id_ingrediente` int NOT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int NOT NULL,
  `nombre_rol` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencias`
--

CREATE TABLE `sugerencias` (
  `id_sugerencia` int NOT NULL,
  `titulo_sugerencia` varchar(100) DEFAULT NULL,
  `sugerencia_info` varchar(500) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `Tipo_Sugerencia_id_tipo` int NOT NULL,
  `Estado_Sugerencias_id_estado` int NOT NULL,
  `Clientes_id_cliente` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_sugerencia`
--

CREATE TABLE `tipo_sugerencia` (
  `id_tipo` int NOT NULL,
  `nombre_tipo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tipo_sugerencia`
--

INSERT INTO `tipo_sugerencia` (`id_tipo`, `nombre_tipo`) VALUES
(1, 'Idea de producto'),
(2, 'Mejora de servicio'),
(3, 'Experiencia'),
(4, 'Otras');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

CREATE TABLE `unidades` (
  `id_unidad` int NOT NULL,
  `abrev_unidad` varchar(45) DEFAULT NULL,
  `nombre_unidad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `unidades`
--

INSERT INTO `unidades` (`id_unidad`, `abrev_unidad`, `nombre_unidad`) VALUES
(1, 'kg', 'Kilogramo'),
(2, 'g', 'Gramo'),
(3, 'L', 'Litro'),
(4, 'ml', 'Mililitro'),
(5, 'u', 'Unidad');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_administrador`),
  ADD UNIQUE KEY `correo_admin` (`correo_admin`),
  ADD KEY `fk_Administradores_Roles1_idx` (`Roles_id_rol`);

--
-- Indices de la tabla `categorias_ingredientes`
--
ALTER TABLE `categorias_ingredientes`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `cedula_UNIQUE` (`cedula`),
  ADD UNIQUE KEY `correo_cliente_UNIQUE` (`correo_cliente`),
  ADD KEY `fk_Clientes_Roles1_idx` (`Roles_id_rol`);

--
-- Indices de la tabla `descuentos`
--
ALTER TABLE `descuentos`
  ADD PRIMARY KEY (`id_codigo`),
  ADD UNIQUE KEY `codigo_promocional` (`codigo_promocional`);

--
-- Indices de la tabla `estados_pedido`
--
ALTER TABLE `estados_pedido`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `estados_stock`
--
ALTER TABLE `estados_stock`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `estado_sugerencias`
--
ALTER TABLE `estado_sugerencias`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `etiquetas_producto`
--
ALTER TABLE `etiquetas_producto`
  ADD PRIMARY KEY (`id_etiqueta`);

--
-- Indices de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`id_ingrediente`),
  ADD KEY `fk_Ingredientes_Categorias_Ingredientes_idx` (`Categorias_Ingredientes_id_categoria`),
  ADD KEY `fk_Ingredientes_Unidades1_idx` (`Unidades_id_unidad`),
  ADD KEY `fk_Ingredientes_Estados_Stock1_idx` (`Estados_Stock_id_estado`);

--
-- Indices de la tabla `metodos_envio`
--
ALTER TABLE `metodos_envio`
  ADD PRIMARY KEY (`id_metodo`);

--
-- Indices de la tabla `metodo_pagos`
--
ALTER TABLE `metodo_pagos`
  ADD PRIMARY KEY (`id_metodo`);

--
-- Indices de la tabla `payment_accounts`
--
ALTER TABLE `payment_accounts`
  ADD PRIMARY KEY (`id_cuenta`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `fk_Pedidos_Metodos_Envio1_idx` (`Metodos_Envio_id_metodo`),
  ADD KEY `fk_Pedidos_Descuentos1_idx` (`Descuentos_id_codigo`),
  ADD KEY `fk_Pedidos_Metodo_pagos1_idx` (`Metodo_pagos_id_metodo`),
  ADD KEY `fk_Pedidos_Estados_pedido1_idx` (`Estados_pedido_id_estado`),
  ADD KEY `fk_Pedidos_Clientes1_idx` (`Clientes_id_cliente`);

--
-- Indices de la tabla `pedidos_has_productos`
--
ALTER TABLE `pedidos_has_productos`
  ADD PRIMARY KEY (`Pedidos_id_pedido`,`Productos_id_producto`),
  ADD KEY `fk_Pedidos_has_Productos_Productos1_idx` (`Productos_id_producto`),
  ADD KEY `fk_Pedidos_has_Productos_Pedidos1_idx` (`Pedidos_id_pedido`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `fk_Productos_Etiquetas_producto1_idx` (`Etiquetas_producto_id_etiqueta`);

--
-- Indices de la tabla `productos_has_ingredientes`
--
ALTER TABLE `productos_has_ingredientes`
  ADD PRIMARY KEY (`Productos_id_producto`,`Ingredientes_id_ingrediente`),
  ADD KEY `fk_Productos_has_Ingredientes_Ingredientes1_idx` (`Ingredientes_id_ingrediente`),
  ADD KEY `fk_Productos_has_Ingredientes_Productos1_idx` (`Productos_id_producto`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  ADD PRIMARY KEY (`id_sugerencia`),
  ADD KEY `fk_Sugerencias_Tipo_Sugerencia1_idx` (`Tipo_Sugerencia_id_tipo`),
  ADD KEY `fk_Sugerencias_Estado_Sugerencias1_idx` (`Estado_Sugerencias_id_estado`),
  ADD KEY `fk_Sugerencias_Clientes1_idx` (`Clientes_id_cliente`);

--
-- Indices de la tabla `tipo_sugerencia`
--
ALTER TABLE `tipo_sugerencia`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`id_unidad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id_administrador` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `categorias_ingredientes`
--
ALTER TABLE `categorias_ingredientes`
  MODIFY `id_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `descuentos`
--
ALTER TABLE `descuentos`
  MODIFY `id_codigo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estados_pedido`
--
ALTER TABLE `estados_pedido`
  MODIFY `id_estado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estados_stock`
--
ALTER TABLE `estados_stock`
  MODIFY `id_estado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estado_sugerencias`
--
ALTER TABLE `estado_sugerencias`
  MODIFY `id_estado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `etiquetas_producto`
--
ALTER TABLE `etiquetas_producto`
  MODIFY `id_etiqueta` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id_ingrediente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `metodos_envio`
--
ALTER TABLE `metodos_envio`
  MODIFY `id_metodo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `metodo_pagos`
--
ALTER TABLE `metodo_pagos`
  MODIFY `id_metodo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `payment_accounts`
--
ALTER TABLE `payment_accounts`
  MODIFY `id_cuenta` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  MODIFY `id_sugerencia` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_sugerencia`
--
ALTER TABLE `tipo_sugerencia`
  MODIFY `id_tipo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `unidades`
--
ALTER TABLE `unidades`
  MODIFY `id_unidad` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD CONSTRAINT `fk_Administradores_Roles1` FOREIGN KEY (`Roles_id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_Clientes_Roles1` FOREIGN KEY (`Roles_id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD CONSTRAINT `fk_Ingredientes_Categorias_Ingredientes` FOREIGN KEY (`Categorias_Ingredientes_id_categoria`) REFERENCES `categorias_ingredientes` (`id_categoria`),
  ADD CONSTRAINT `fk_Ingredientes_Estados_Stock1` FOREIGN KEY (`Estados_Stock_id_estado`) REFERENCES `estados_stock` (`id_estado`),
  ADD CONSTRAINT `fk_Ingredientes_Unidades1` FOREIGN KEY (`Unidades_id_unidad`) REFERENCES `unidades` (`id_unidad`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_Pedidos_Clientes1` FOREIGN KEY (`Clientes_id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `fk_Pedidos_Descuentos1` FOREIGN KEY (`Descuentos_id_codigo`) REFERENCES `descuentos` (`id_codigo`),
  ADD CONSTRAINT `fk_Pedidos_Estados_pedido1` FOREIGN KEY (`Estados_pedido_id_estado`) REFERENCES `estados_pedido` (`id_estado`),
  ADD CONSTRAINT `fk_Pedidos_Metodo_pagos1` FOREIGN KEY (`Metodo_pagos_id_metodo`) REFERENCES `metodo_pagos` (`id_metodo`),
  ADD CONSTRAINT `fk_Pedidos_Metodos_Envio1` FOREIGN KEY (`Metodos_Envio_id_metodo`) REFERENCES `metodos_envio` (`id_metodo`);

--
-- Filtros para la tabla `pedidos_has_productos`
--
ALTER TABLE `pedidos_has_productos`
  ADD CONSTRAINT `fk_Pedidos_has_Productos_Pedidos1` FOREIGN KEY (`Pedidos_id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_Pedidos_has_Productos_Productos1` FOREIGN KEY (`Productos_id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_Productos_Etiquetas_producto1` FOREIGN KEY (`Etiquetas_producto_id_etiqueta`) REFERENCES `etiquetas_producto` (`id_etiqueta`);

--
-- Filtros para la tabla `productos_has_ingredientes`
--
ALTER TABLE `productos_has_ingredientes`
  ADD CONSTRAINT `fk_Productos_has_Ingredientes_Ingredientes1` FOREIGN KEY (`Ingredientes_id_ingrediente`) REFERENCES `ingredientes` (`id_ingrediente`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_Productos_has_Ingredientes_Productos1` FOREIGN KEY (`Productos_id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  ADD CONSTRAINT `fk_Sugerencias_Clientes1` FOREIGN KEY (`Clientes_id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `fk_Sugerencias_Estado_Sugerencias1` FOREIGN KEY (`Estado_Sugerencias_id_estado`) REFERENCES `estado_sugerencias` (`id_estado`),
  ADD CONSTRAINT `fk_Sugerencias_Tipo_Sugerencia1` FOREIGN KEY (`Tipo_Sugerencia_id_tipo`) REFERENCES `tipo_sugerencia` (`id_tipo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
