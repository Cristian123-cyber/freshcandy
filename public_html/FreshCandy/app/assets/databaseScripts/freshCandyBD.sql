-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 25-05-2025 a las 23:17:55
-- Versión del servidor: 8.0.42
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
(4, 'admin1', 'admin1@freshcandy.com', '$2y$12$Kj80Sck0oB85whhdxejQe.dTnsfIYN2yOwJRsw4JB9.luSLAqiAXy', 1);

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
(6, 'Frutas');

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

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `cedula`, `correo_cliente`, `telefono_cliente`, `direccion_envio`, `password`, `Roles_id_rol`) VALUES
(1, 'Cristian Chisavo Gonzalez', '1234567890', 'cris@gmail.com', '3148907687', 'Calle Principal 123, Cartago', '$2y$10$LKpNYa4Rt0rA6zxqLYGQheC.vylsIwMZ2nBR0ICQQJIMeKoAiZM0m', 2),
(2, 'Carlos Rodríguez', '0987654321', 'carlos@correo.com', '555-987-6543', 'Avenida Central 456, Medellin', '$2y$10$3kfjsEkhDBdVSUH/qZJmZuJdSGZ5zZI.WIH2MWvWnqtOlAkdIBHcy', 2),
(3, 'Daniel Gonzalez', '123456789', 'dddcuentainstagram@gmail.com', NULL, NULL, '$2y$12$6/Xt1.h8rEVCuRtzOMXpeu/vspvqB76mcXG6sfZZiAYx4Whg9ljTW', 2),
(4, 'Cristian Chisavo', '1114151446', 'crischisavo@gmail.com', '123456789', 'Transversal 4 barrio san jose', '$2y$12$i1fnK.dk2vyAojZCIrK5tujOb6ERL7oJijjgczFjaQR4DSBFpucZO', 2),
(5, 'Cristian Chisavo', '1231413421', 'crischavo@gmail.com', NULL, NULL, '$2y$12$UvUkI6iGq.OJzmN7mR5ykO9jEDnvxy9ehRffnVtsENQ7vXGW1fLw2', 2),
(6, '&#039; OR &#039;1&#039;=&#039;1', '12314134218888', 'crischajjjjvo@gmail.com', NULL, NULL, '$2y$12$92.NvZg5.qUJnLQHrcpEcekbxQtBhDS.atHDITvLK3lTcL5zt/Ihm', 2),
(7, '&#039; OR &#039;1&#039;=&#039;1', '1231413488', 'rifasroche80@gmail.com', NULL, NULL, '$2y$12$7/14uahzyy1peYr8rXGgE.D1kamfTfb6b/wqebpbU5DtIrhO.LvcG', 2),
(8, 'admin uno', '1222222222222222', 'admin1@fresh.com', NULL, NULL, '$2y$12$mBOwHscIkyFssfcay34PYOSntiRGoGxgGcX6Gcc2696xzFJVEQB5.', 2),
(9, 'Cristian', '132131231234', 'crischisav555o@gmail.com', NULL, NULL, '$2y$12$UvyqE/mmkGTmFivDV3ZdTuDnMea3e/l7fClSYzZskyVQs9l6eC5sK', 2),
(10, 'Cristian', '1321312312342', 'crischis3333avo@gmail.com', NULL, NULL, '$2y$12$A1Fb4k6BN87pxMSbW4JS0udyLfwacpli1ONxkedYRHwlQ9FRNezmi', 2),
(11, 'Cristian', '13213123123421', 'crischis33313avo@gmail.com', NULL, NULL, '$2y$12$LXwdsuGWNeqv8gYkrl6TZuwnxmB8hKDcH4t9RdPj7Wz1CAT.BEsju', 2),
(12, 'Cristian', '13213123123467678', 'crischis33337878avo@gmail.com', NULL, NULL, '$2y$12$.F3PSIPv2H5g9tD1HN6LyOdIfhZpda154ouF1e8W2ev9DdLlIZmGG', 2),
(13, 'Cristian', '1321312312346767811', 'crischis3333787338avo@gmail.com', NULL, NULL, '$2y$12$kPFcVQR.Sy1xRTdLTLJQceTWsXAraIZ/oncdR3gv8QuH7SvRi8.GG', 2),
(14, 'Cristian', '132131', 'crischis33332337878avo@gmail.com', NULL, NULL, '$2y$12$Q4gSfRBD.M1MaFxTebgwCOXWBNk2.lB55jT9Cd5kqGL.w8FOHSmM2', 2),
(15, 'Cristian', '1321313132131', 'crischis33332444337878avo@gmail.com', NULL, NULL, '$2y$12$A4LJkFR3HPsVoBlP7Vnz5OZVe9122VIfyZXUMu5IJ98LCiNwssO2m', 2),
(16, 'Cristian', '13213131321311', 'crischis333324443378789avo@gmail.com', NULL, NULL, '$2y$12$sDbGyrebzs68YPiLkbbn/OcY6mrrzacvrlbVHBvekMdQDZsCzCEFC', 2),
(17, 'Cristiano Ronaldo', '1234567', 'cr7thebest@gmail.com', NULL, NULL, '$2y$12$2m5T7LhBXZP6EuWFQ1K3z.9gIjiYkmcWkBc11pbi2gbFz0LGvaiHm', 2);

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
(1, 'Azúcar blanca', 50.00, 10.00, 5.00, 1, 1, 1),
(2, 'Esencia de vainilla', 2.50, 0.50, 0.25, 2, 3, 1),
(3, 'Colorante rojo', 1.00, 0.20, 0.10, 3, 3, 1),
(4, 'Almendras', 15.00, 5.00, 2.00, 4, 1, 1),
(5, 'Chocolate negro', 25.00, 8.00, 4.00, 5, 1, 1),
(6, 'Azúcar glass', 8.00, 3.00, 1.00, 1, 1, 2),
(7, 'Esencia de fresa', 0.30, 0.50, 0.25, 2, 3, 3),
(8, 'Fresas', 2.50, 0.50, 0.25, 6, 1, 1),
(9, 'Esencia de fresa', 0.30, 0.50, 0.25, 2, 3, 3),
(10, 'Cacao en polvo', 12.00, 5.00, 2.00, 5, 1, 1);

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
(1, 'Nequi', '3010101010', 'Fresh Candy'),
(2, 'Bancolombia', '10101010101010101', 'Fresh Candy'),
(3, 'Daviplata', '1010101010101010', 'Fresh Candy'),
(5, 'WhatsApp', '3234536696', 'Fresh Candy');

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

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `fecha`, `monto_total`, `direccion_envio`, `nombre_destinatario`, `telefono_destinatario`, `ciudad_destino`, `notas_adicionales`, `Metodos_Envio_id_metodo`, `Descuentos_id_codigo`, `Metodo_pagos_id_metodo`, `Estados_pedido_id_estado`, `Clientes_id_cliente`) VALUES
(1, '2025-05-10 14:30:00', 28.99, 'Calle Principal 123, Ciudad', 'Cristian Chisavo', '555-123-4567', 'Ciudad Capital', 'Dejar en portería', 1, 1, 2, 1, 1),
(2, '2025-05-12 10:15:00', 16.25, 'Avenida Central 456, Ciudad', 'Carlos Rodríguez', '555-987-6543', 'Ciudad Capital', NULL, 2, 1, 1, 4, 2),
(6, '2025-05-24 03:23:04', 30000.00, NULL, 'Cristian Chisavo', '1234567891', NULL, '', 2, 1, 1, 1, 4),
(7, '2025-05-24 03:24:26', 30000.00, NULL, 'Cristian Chisavo', '1234567891', NULL, '', 2, 1, 1, 1, 4),
(18, '2025-05-24 03:49:24', 36000.00, NULL, 'Cristian Chisavo', '123456789', NULL, 'vamos a ver', 2, 1, 1, 1, 4),
(19, '2025-05-24 03:50:44', 36000.00, NULL, 'Cristian Chisavo', '123456789', NULL, 'vamos a ver', 2, 1, 1, 1, 4),
(20, '2025-05-24 03:56:24', 28000.00, NULL, 'Cristian Chisavo', '123456789', NULL, 'ahroa si papai', 2, 4, 1, 1, 4),
(21, '2025-05-24 03:58:50', 30000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', 'ooo', 1, 4, 2, 1, 4),
(22, '2025-05-24 03:59:21', 25800.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', 'ooo', 1, 2, 2, 1, 4),
(23, '2025-05-24 16:16:49', 23000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', 'cambios mano', 1, 1, 1, 1, 4),
(24, '2025-05-24 16:17:42', 28000.00, NULL, 'Cristian Chisavo', '123456789', NULL, '', 2, 4, 2, 1, 4),
(25, '2025-05-24 17:25:05', 23000.00, 'Transversal 4 barrio san jose', 'gloria amparo chisavo gonzalez', '+5731313131313', 'Cartago', 'probando', 1, 1, 2, 1, 4),
(26, '2025-05-24 17:27:39', 23000.00, 'Transversal 4 barrio san jose', 'gloria amparo chisavo gonzalez', '121444444444', 'Cartago', 'probando ahora si', 1, 1, 2, 1, 4),
(27, '2025-05-24 17:31:02', 30000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', '', 1, 4, 1, 1, 4),
(28, '2025-05-24 17:32:19', 30000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', '', 1, 4, 2, 1, 4),
(29, '2025-05-24 17:32:50', 30000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', 'jj', 1, 4, 2, 1, 4),
(30, '2025-05-24 17:41:28', 28000.00, NULL, 'Juan camilo', '1234567891', NULL, 'SOmos los mejores', 2, 4, 1, 1, 4),
(31, '2025-05-24 17:42:14', 23000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', '', 1, 1, 2, 1, 4),
(32, '2025-05-24 17:46:16', 30000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', '', 1, 4, 1, 1, 4),
(33, '2025-05-24 17:50:33', 812000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', '', 1, 4, 1, 1, 4),
(34, '2025-05-24 17:51:39', 812000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', '', 1, 4, 1, 1, 4),
(35, '2025-05-25 04:29:37', 1192000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', '', 1, 4, 1, 1, 4),
(36, '2025-05-25 21:09:19', 28800.00, NULL, 'Eduar JAVIER', '1234567891', NULL, 'notas aquo', 2, 3, 2, 1, 4),
(37, '2025-05-25 21:20:36', 37800.00, NULL, 'Cristian Chisavo', '123456789', NULL, 'depurando', 2, 3, 1, 1, 4),
(38, '2025-05-25 21:24:13', 44000.00, 'Transversal 4 barrio san jose', 'Cristian Chisavo', '123456789', 'Cartago', '', 1, 4, 2, 1, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_has_productos`
--

CREATE TABLE `pedidos_has_productos` (
  `Pedidos_id_pedido` int NOT NULL,
  `Productos_id_producto` int NOT NULL,
  `cantidad` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `pedidos_has_productos`
--

INSERT INTO `pedidos_has_productos` (`Pedidos_id_pedido`, `Productos_id_producto`, `cantidad`) VALUES
(1, 1, 2),
(1, 2, 1),
(2, 2, 3),
(7, 1, 2),
(7, 3, 3),
(19, 3, 6),
(20, 2, 2),
(20, 3, 1),
(21, 2, 2),
(21, 3, 1),
(22, 2, 2),
(22, 3, 1),
(23, 2, 2),
(23, 3, 1),
(24, 2, 2),
(24, 3, 1),
(25, 2, 2),
(25, 3, 1),
(26, 2, 2),
(26, 3, 1),
(27, 2, 2),
(27, 3, 1),
(28, 2, 2),
(28, 3, 1),
(29, 2, 2),
(29, 3, 1),
(30, 2, 2),
(30, 3, 1),
(31, 2, 2),
(31, 3, 1),
(32, 2, 2),
(32, 3, 1),
(33, 2, 1),
(33, 3, 100),
(34, 2, 1),
(34, 3, 100),
(35, 2, 39),
(35, 3, 100),
(36, 3, 4),
(37, 2, 1),
(37, 3, 4),
(38, 2, 1),
(38, 3, 4);

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
  `Etiquetas_producto_id_etiqueta` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre_producto`, `descripcion`, `precio_producto`, `image_url`, `fecha_creacion`, `Etiquetas_producto_id_etiqueta`) VALUES
(1, 'Helado de fresa', 'Delicioso helado con sabores frutales y colores vibrantes', 8000.00, 'http://localhost/FreshCandy/app/assets/images/helado66.jpeg\n', '2024-03-15', 1),
(2, 'Helado de chocolate', 'Tableta de chocolate negro de alta calidad', 10000.00, 'http://localhost/FreshCandy/app/assets/images/helado66.jpeg', '2024-02-10', 3),
(3, 'Helado de coco', 'Delicioso helado con sabores frutales y colores vibrantes', 8000.00, 'http://localhost/FreshCandy/app/assets/images/helado66.jpeg', '2024-03-15', 5),
(4, 'Helado de cicle', 'Delicioso helado con sabores frutales y colores vibrantes', 18000.00, 'http://localhost/FreshCandy/app/assets/images/helado66.jpeg', '2024-03-15', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_has_ingredientes`
--

CREATE TABLE `productos_has_ingredientes` (
  `Productos_id_producto` int NOT NULL,
  `Ingredientes_id_ingrediente` int NOT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `productos_has_ingredientes`
--

INSERT INTO `productos_has_ingredientes` (`Productos_id_producto`, `Ingredientes_id_ingrediente`, `cantidad`) VALUES
(1, 1, 0.50),
(1, 8, 0.50),
(2, 4, 0.10),
(2, 5, 0.80),
(3, 1, 2.50),
(3, 4, 3.50),
(4, 1, 2.00),
(4, 5, 2.00);

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

--
-- Volcado de datos para la tabla `sugerencias`
--

INSERT INTO `sugerencias` (`id_sugerencia`, `titulo_sugerencia`, `sugerencia_info`, `fecha`, `Tipo_Sugerencia_id_tipo`, `Estado_Sugerencias_id_estado`, `Clientes_id_cliente`) VALUES
(1, 'Chocolate con menta', 'Me encantaría que tuvieran chocolates con sabor a menta', '2025-05-01 11:20:00', 1, 1, 2),
(2, 'Pago con nequi', 'Me encantaría que habilitaran pago mediante nequi', '2025-05-01 11:20:00', 2, 1, 1),
(3, 'Nuevo sabor de helado', 'Quiero helados de mango por favor', '2025-05-21 00:58:33', 1, 1, 4),
(4, 'Nuevo sabor de helado', '1111111111111', '2025-05-21 01:04:02', 4, 1, 4),
(5, '12313131313', '12131231241414124', '2025-05-21 01:10:24', 3, 1, 4),
(6, 'OE MANO', 'oeoeo jajaja', '2025-05-22 04:09:57', 2, 1, 17),
(7, 'Shushi666', 'hola como estas idsskakakakakak', '2025-05-24 17:48:07', 2, 1, 4),
(8, 'Shushi666', 'sdgsgsgsdsgsgdsgsg', '2025-05-24 17:48:26', 4, 1, 4),
(9, 'SDA', 'asdaddasdadasd', '2025-05-25 21:31:25', 1, 1, 4),
(10, 'dsadad', 'sdadadadsad', '2025-05-25 21:32:30', 3, 1, 4),
(11, 'dsasfdfa', 'aaaaaaaaaaaaaaa', '2025-05-25 21:32:53', 2, 1, 4);

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
(3, 'l', 'Litro'),
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
  MODIFY `id_administrador` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categorias_ingredientes`
--
ALTER TABLE `categorias_ingredientes`
  MODIFY `id_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id_ingrediente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id_pedido` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  MODIFY `id_sugerencia` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  ADD CONSTRAINT `fk_Pedidos_has_Productos_Pedidos1` FOREIGN KEY (`Pedidos_id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `fk_Pedidos_has_Productos_Productos1` FOREIGN KEY (`Productos_id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_Productos_Etiquetas_producto1` FOREIGN KEY (`Etiquetas_producto_id_etiqueta`) REFERENCES `etiquetas_producto` (`id_etiqueta`);

--
-- Filtros para la tabla `productos_has_ingredientes`
--
ALTER TABLE `productos_has_ingredientes`
  ADD CONSTRAINT `fk_Productos_has_Ingredientes_Ingredientes1` FOREIGN KEY (`Ingredientes_id_ingrediente`) REFERENCES `ingredientes` (`id_ingrediente`),
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
