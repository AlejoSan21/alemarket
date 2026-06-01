-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-06-2026 a las 18:36:57
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `alemarket1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas_bot`
--

CREATE TABLE `consultas_bot` (
  `id` int(11) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `consultas_bot`
--

INSERT INTO `consultas_bot` (`id`, `telefono`, `mensaje`, `respuesta`, `fecha`) VALUES
(1, NULL, 'ventas', 'No entiendo tu pregunta. Prueba: \"stock\", \"productos\" o \"stock bajo\".', '2026-05-20 11:35:53'),
(2, NULL, 'Productos', 'Inventario activo:• Bonyurt — 5 uds. ($3.200)• Leche 1L — 5 uds. ($3.200)• Oreo — 11 uds. ($2.000)• Yogurt  — 4 uds. ($2.200)', '2026-05-20 11:36:02'),
(3, NULL, 'Ventas', 'No entiendo tu pregunta. Prueba: \"stock\", \"productos\" o \"stock bajo\".', '2026-05-20 11:36:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 3, 1, 3500.00, 3500.00),
(2, 2, 3, 1, 3500.00, 3500.00),
(3, 3, 3, 1, 3500.00, 3500.00),
(4, 4, 3, 12, 3500.00, 42000.00),
(6, 6, 2, 2, 2200.00, 4400.00),
(7, 7, 1, 2, 3200.00, 6400.00),
(8, 8, 1, 2, 3200.00, 6400.00),
(9, 9, 2, 2, 2200.00, 4400.00),
(10, 10, 2, 1, 2200.00, 2200.00),
(11, 11, 1, 3, 3200.00, 9600.00),
(12, 11, 2, 2, 2200.00, 4400.00),
(13, 11, 3, 3, 3500.00, 10500.00),
(14, 12, 1, 1, 3200.00, 3200.00),
(15, 12, 2, 1, 2200.00, 2200.00),
(16, 12, 3, 1, 3500.00, 3500.00),
(17, 13, 1, 1, 3200.00, 3200.00),
(18, 14, 3, 1, 3500.00, 3500.00),
(19, 15, 1, 1, 3200.00, 3200.00),
(20, 16, 1, 1, 3200.00, 3200.00),
(21, 17, 2, 1, 2200.00, 2200.00),
(22, 18, 3, 1, 3200.00, NULL),
(23, 19, 3, 1, 3200.00, NULL),
(24, 19, 2, 1, 2200.00, NULL),
(25, 20, 3, 1, 3200.00, NULL),
(26, 21, 10, 1, 2000.00, NULL),
(27, 23, 10, 1, 2000.00, NULL),
(28, 24, 10, 1, 2000.00, NULL),
(29, 25, 10, 1, 2000.00, NULL),
(30, 26, 3, 1, 3200.00, NULL),
(31, 27, 10, 1, 2000.00, NULL),
(32, 28, 10, 1, 2000.00, NULL),
(33, 29, 3, 1, 3200.00, NULL),
(34, 30, 3, 1, 3200.00, NULL),
(35, 31, 3, 1, 3200.00, NULL),
(36, 32, 3, 1, 3200.00, NULL),
(39, 35, 3, 2, 3200.00, NULL),
(40, 36, 10, 1, 2000.00, NULL),
(46, 43, 14, 1, 3000.00, NULL),
(47, 44, 14, 1, 3500.00, NULL),
(48, 44, 1, 1, 3200.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id`, `nombre`) VALUES
(1, 'efectivo'),
(2, 'transferencia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_stock`
--

CREATE TABLE `movimientos_stock` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos_stock`
--

INSERT INTO `movimientos_stock` (`id`, `producto_id`, `tipo`, `cantidad`, `motivo`, `fecha`) VALUES
(1, 3, 'salida', 1, 'Venta registrada', '2026-02-24 14:26:30'),
(2, 3, 'salida', 1, 'Venta registrada', '2026-02-24 14:26:53'),
(3, 3, 'salida', 1, 'Venta registrada', '2026-02-24 14:27:02'),
(4, 3, 'salida', 12, 'Venta registrada', '2026-02-24 14:27:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`) VALUES
(1, 'control_total'),
(2, 'gestionar_stock'),
(3, 'registrar_ventas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo_barras` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo_barras`, `nombre`, `precio`, `stock`, `actualizado_en`, `estado`) VALUES
(1, '1212', 'Leche 1L', 3500.00, 4, '2026-05-28 15:47:02', 1),
(2, '121212', 'Yogurt ', 2200.00, 4, '2026-03-24 20:16:32', 1),
(3, '2121', 'Bonyurt', 3200.00, 5, '2026-05-26 13:52:18', 1),
(10, '22222222', 'Oreo', 2000.00, 11, '2026-05-26 13:52:15', 1),
(14, '1515', 'Paca de agua', 3500.00, 8, '2026-05-28 15:46:38', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'administrador'),
(2, 'empleado'),
(3, 'superadmin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `token_recuperacion` varchar(100) DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `usuario`, `password`, `rol_id`, `estado`, `creado_en`, `token_recuperacion`, `token_expiracion`) VALUES
(2, 'Alejolaya', '', 'Alejo1234', '$2y$10$142MTnSeLUodHFaJ1voNtOBHrM3SRnCnQXoJpdpjAIIVC/d4G9EhC', 3, 0, '2026-02-23 12:33:45', NULL, NULL),
(5, 'Alejandro Olaya', 'alejolaya2003@hotmail.com', 'AlejoSan', '$2y$10$.JsLDUmlgSNJYaQ2aBAkD.UYDJxrGc2kPs6qgN1Hvwtkd1aTYppc6', 3, 1, '2026-03-10 12:03:49', '2fadd1c4a623fbf88be9fdfa1ab125ae19fd0a0f12566c88b0f1ad0f5b5cab2d', '2026-06-01 14:56:47'),
(6, 'Alejandra Torres', 'kellyalejandradiaztorres07@gmail.com', 'Aleja0905', '$2y$10$mR0PbAecEoUpB0eRxNDWwO6uM6tyJBA5GxHyHllx69q2ZVya54YTm', 1, 1, '2026-03-12 13:42:00', 'd7d84a773afaf6f0eabaa4a9671f74b6d4453457be7bd7de674ef492fc290901', '2026-06-01 14:50:40'),
(11, 'Paulo', '', 'Paulo123', '$2y$10$9ekd09zcIuMsg6TScvQJL.rAvJM43un7jRCgDkkHUlVKDEbiNVPdW', 2, 1, '2026-04-08 12:16:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `metodo_pago_id` int(11) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `usuario_id`, `metodo_pago_id`, `total`, `fecha`) VALUES
(1, 2, 1, 3500.00, '2026-02-24 14:26:30'),
(2, 2, 1, 3500.00, '2026-02-24 14:26:53'),
(3, 2, 1, 3500.00, '2026-02-24 14:27:02'),
(4, 2, 1, 42000.00, '2026-02-24 14:27:16'),
(6, 2, 1, 4400.00, '2026-02-25 01:51:50'),
(7, 2, 1, 6400.00, '2026-02-25 01:53:18'),
(8, 2, 1, 6400.00, '2026-02-25 02:23:32'),
(9, 2, 1, 4400.00, '2026-02-26 11:34:36'),
(10, 2, 2, 2200.00, '2026-02-26 12:55:34'),
(11, 2, 1, 24500.00, '2026-03-02 12:05:02'),
(12, 2, 1, 8900.00, '2026-03-02 14:55:01'),
(13, 2, 2, 3200.00, '2026-03-02 15:43:52'),
(14, 2, 1, 3500.00, '2026-03-06 14:37:19'),
(15, 2, 1, 3200.00, '2026-03-06 14:44:18'),
(16, 2, 1, 3200.00, '2026-03-09 14:57:42'),
(17, 5, 1, 2200.00, '2026-03-17 14:04:24'),
(18, 5, 1, 3200.00, '2026-03-24 19:44:53'),
(19, 5, 1, 5400.00, '2026-03-24 20:00:54'),
(20, 5, 1, 3200.00, '2026-03-24 20:06:29'),
(21, 5, 1, 2000.00, '2026-03-24 20:19:13'),
(23, 5, 1, 2000.00, '2026-03-25 11:44:50'),
(24, 5, 1, 2000.00, '2026-03-25 12:11:47'),
(25, 5, 1, 2000.00, '2026-03-25 12:31:35'),
(26, 5, 1, 3200.00, '2026-03-25 12:47:01'),
(27, 5, 1, 2000.00, '2026-03-25 12:57:43'),
(28, 5, 1, 2000.00, '2026-03-25 12:58:04'),
(29, 5, 1, 3200.00, '2026-04-08 11:42:06'),
(30, 11, 1, 3200.00, '2026-04-08 12:28:14'),
(31, 11, 1, 3200.00, '2026-04-08 12:37:59'),
(32, 5, 1, 3200.00, '2026-04-08 14:09:33'),
(35, 5, 1, 6400.00, '2026-04-15 13:54:40'),
(36, 6, 1, 2000.00, '2026-04-20 12:51:21'),
(43, 5, 1, 3000.00, '2026-05-28 15:45:14'),
(44, 5, 1, 6700.00, '2026-05-28 15:46:38');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `consultas_bot`
--
ALTER TABLE `consultas_bot`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `usuario_2` (`usuario`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `metodo_pago_id` (`metodo_pago_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `consultas_bot`
--
ALTER TABLE `consultas_bot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  ADD CONSTRAINT `movimientos_stock_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
