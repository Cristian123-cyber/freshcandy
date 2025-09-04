-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 01-09-2025 a las 13:49:33
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
(4, 'admin666', 'admin1@freshcandy.com', '$2y$12$.mGCuAgVsj2zvQVJnfXU3OO9r.UzDWppmMduLD5m88mJFrcIO7qSG', 1);

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

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `cedula`, `correo_cliente`, `telefono_cliente`, `direccion_envio`, `password`, `Roles_id_rol`) VALUES
(1, 'Cristian Chisavo Gonzalez', '1234567890', 'cris@gmail.com', '3148907687', 'Calle Principal 123, Cartago', '$2y$10$LKpNYa4Rt0rA6zxqLYGQheC.vylsIwMZ2nBR0ICQQJIMeKoAiZM0m', 2),
(2, 'Carlos Rodríguez', '0987654321', 'carlos@correo.com', '555-987-6543', 'Avenida Central 456, Medellin', '$2y$10$3kfjsEkhDBdVSUH/qZJmZuJdSGZ5zZI.WIH2MWvWnqtOlAkdIBHcy', 2),
(3, 'Daniel Gonzalez', '123456789', 'dddcuentainstagram@gmail.com', NULL, NULL, '$2y$12$6/Xt1.h8rEVCuRtzOMXpeu/vspvqB76mcXG6sfZZiAYx4Whg9ljTW', 2),
(4, 'Cristian Chisavo', '1114151446', 'crischisavo@gmail.com', '3144667846', 'Barrio el prado calle 177777', '$2y$12$DPxOJihyDgZ/K.3b908aWO2A3G5XXGYcFnTe6EfPV8yovrApH5kHa', 2),
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
(17, 'Cristiano Ronaldo', '1234567', 'cr7thebest@gmail.com', NULL, NULL, '$2y$12$2m5T7LhBXZP6EuWFQ1K3z.9gIjiYkmcWkBc11pbi2gbFz0LGvaiHm', 2),
(18, 'Cristian', '11111111818181818', 'admin1@freshcarrndy.com', NULL, NULL, '$2y$12$6EnQJ0I8pmupS/JpHwaEFO/mNuA9wLirxW8zJEBa5MHWuOoNLkAOK', 2),
(19, 'POliii', '123456777', 'policia123@gmail.com', '3167894566', 'Barrio POLI', '$2y$12$fwLRttuBOhkCj6oQpXZbD.pRjmVPOxfGJIEeXF7myuHu3ewc7S.y2', 2),
(20, 'RUIZ', '100659211', 'rui1z@gmail.com', '3167894566', 'Barrio el polo 124141212', '$2y$12$9NlqlS790ub1KxJXeMG7UuO9iVCpmn7QV0dggaEniBMu7CfvTaCDa', 2);

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
(16, 'Leche en polvo pai', 1003.60, 15.00, 5.00, 7, 4, 1),
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
(66, '2025-06-04 10:07:35', 6500.00, 'Barrio el prado calle 177777', 'Cristian Chisavo', '3144667846', 'Cartago', '', 1, 4, 1, 5, 4),
(67, '2025-06-04 13:55:39', 6000.00, NULL, 'JOSE RUIZ', '3167894566', NULL, 'SALJKDJKD', 2, 1, 2, 2, 20),
(68, '2025-06-09 15:49:54', 10000.00, NULL, 'Cristian Chisavo', '3144667846', NULL, '', 2, 4, 1, 2, 4),
(69, '2025-06-10 15:24:05', 17375.00, 'Barrio el prado calle 177777', 'Cristian Chisavo', '3144667846', 'Cartago', '', 1, 1, 1, 1, 4),
(70, '2025-06-10 15:25:03', 18500.00, 'Barrio el prado calle 177777', 'Cristian Chisavo', '3144667846', 'Cartago', '', 1, 4, 1, 1, 4),
(71, '2025-06-10 15:56:34', 18000.00, 'Barrio el prado calle 177777', 'Cristian Chisavo', '3144667846', 'Cartago', '', 1, 4, 1, 5, 4);

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

--
-- Volcado de datos para la tabla `pedidos_has_productos`
--

INSERT INTO `pedidos_has_productos` (`Pedidos_id_pedido`, `Productos_id_producto`, `cantidad`, `precio_prod`) VALUES
(66, 6, 1, 0.00),
(68, 6, 2, 5000.00),
(69, 6, 1, 4500.00),
(69, 8, 1, 4000.00),
(69, 9, 1, 4000.00),
(69, 11, 1, 4000.00),
(69, 17, 1, 4000.00),
(70, 6, 1, 4500.00),
(70, 8, 2, 4000.00),
(70, 17, 1, 4000.00),
(71, 8, 4, 4000.00);

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

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre_producto`, `descripcion`, `precio_producto`, `image_url`, `fecha_creacion`, `Etiquetas_producto_id_etiqueta`, `estado`) VALUES
(6, 'Helado de café', 'Un clásico con energía: helado cremoso elaborado con auténtico café, perfecto para los amantes del sabor intenso y el aroma tostado. ¡Ideal para despertar los sentidos!', 4500.00, 'http://localhost/FreshCandy/app/assets/images/1748893703_Captura de pantalla 2025-06-02 144749.png', '2025-06-02', 1, 1),
(8, 'Helado de mango', 'Un viaje tropical en cada bocado. Disfruta de la jugosidad del mango maduro en un helado suave y vibrante. Puro sabor caribeño directo al corazón.', 4000.00, 'http://localhost/FreshCandy/app/assets/images/1748894350_composition-delicious-homemade-icecream.jpg', '2025-06-02', 5, 1),
(9, 'Helado de caramelo', 'Cremoso, dorado y con ese toque dulce que conquista. Este helado de caramelo es una delicia suave con notas de azúcar caramelizado que derrite cualquier antojo.', 4000.00, 'http://localhost/FreshCandy/app/assets/images/1748894424_delicious-ice-cream-studio.jpg', '2025-06-02', 1, 1),
(11, 'Helado de chocolate', 'Intenso, suave y lleno de cacao. Nuestro helado de chocolate es la definición de placer chocolatoso: una explosión de sabor para los verdaderos fanáticos del chocolate.', 4000.00, 'http://localhost/FreshCandy/app/assets/images/1748925153_Captura de pantalla 2025-06-02 145436.png', '2025-06-02', 3, 1),
(12, 'Hleado de chipsaJHFUI', 'LKSJFAIJFDSJFISHFJKSD', 4000.00, 'http://localhost/FreshCandy/app/assets/images/1749063970_Captura de pantalla 2025-06-02 145436.png', '2025-06-04', 3, 0),
(13, 'Helado de mierda', 'oeoeoeoeoeoeoooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo', 2000.00, 'http://localhost/FreshCandy/app/assets/images/1749503139_Captura de pantalla 2025-06-08 210049.jpg', '2025-06-09', 5, 0),
(14, 'HELADO DE MIERDA', 'ASDASDADFADFSADADASDASDA', 3000.00, 'http://localhost/FreshCandy/app/assets/images/1749503224_Captura de pantalla 2025-06-08 210049.jpg', '2025-06-09', 2, 0),
(15, 'helado de mondad', 'dddddddddd', 3000.00, 'http://localhost/FreshCandy/app/assets/images/1749503572_Captura de pantalla 2025-06-08 210049.jpg', '2025-06-09', 4, 0),
(16, 'helaod de mierda', 'sssssssssssssssssss', 3333.00, 'http://localhost/FreshCandy/app/assets/images/1749503716_Captura de pantalla 2025-06-08 210049.jpg', '2025-06-09', 1, 0),
(17, 'Helado de fresa', 'Un clásico irresistible. Nuestro helado de fresa está elaborado con fresas naturales cuidadosamente seleccionadas, que se mezclan con una base cremosa y suave para ofrecerte un sabor auténtico, refrescante y dulce en cada cucharada. ', 4000.00, 'http://localhost/FreshCandy/app/assets/images/1749586920_top-view-delicious-pink-ice-cream-still-life.jpg', '2025-06-10', 4, 1);

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
(6, 14, 1.00),
(6, 15, 1.00),
(6, 16, 1.00),
(6, 17, 1.00),
(6, 20, 1.00),
(8, 14, 0.30),
(8, 15, 300.00),
(8, 16, 0.20),
(8, 17, 200.00),
(8, 19, 0.20),
(9, 14, 0.30),
(9, 15, 200.00),
(9, 16, 0.20),
(9, 17, 200.00),
(9, 21, 300.00),
(11, 14, 0.30),
(11, 15, 100.00),
(11, 16, 0.10),
(11, 17, 100.00),
(11, 23, 0.20),
(17, 14, 1.00),
(17, 15, 1.00),
(17, 16, 1.00),
(17, 17, 1.00),
(17, 18, 1.00);

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
(18, 'Nuevo sabor de helado', 'Nuevo helado de mora', '2025-06-02 22:51:34', 1, 3, 4),
(19, 'Nuevo sabor de helado', 'Cuerpo sigerencia,.....', '2025-06-02 23:18:04', 1, 3, 4),
(20, 'Nuevo sabor de helado&#039;', 'fdsfjsuhfkdshjfdsj', '2025-06-04 13:53:28', 1, 3, 20);

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
  MODIFY `id_administrador` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categorias_ingredientes`
--
ALTER TABLE `categorias_ingredientes`
  MODIFY `id_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `id_pedido` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  MODIFY `id_sugerencia` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
