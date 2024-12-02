CREATE DATABASE FARMACIA ;
USE FARMACIA;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 02-12-2024 a las 08:16:08
-- Versión del servidor: 8.0.37
-- Versión de PHP: 8.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `FARMACIA`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ClasificacionM`
--

CREATE TABLE `ClasificacionM` (
  `idClasificacion` int NOT NULL,
  `Tipo` enum('Antibiotico','Medicamento en Refrigeración','Vitaminas/Suplementos','Medicamento Controlado','Medicamento de Libre Venta','Articulos no Medicamento/Insumos') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ClasificacionM`
--

INSERT INTO `ClasificacionM` (`idClasificacion`, `Tipo`) VALUES
(1, 'Antibiotico'),
(2, 'Medicamento en Refrigeración'),
(3, 'Vitaminas/Suplementos'),
(4, 'Medicamento Controlado'),
(5, 'Medicamento de Libre Venta'),
(6, 'Articulos no Medicamento/Insumos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Comisiones`
--

CREATE TABLE `Comisiones` (
  `idComision` int NOT NULL,
  `idMedicamento` int DEFAULT NULL,
  `porcentaje_comision` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Comisiones`
--

INSERT INTO `Comisiones` (`idComision`, `idMedicamento`, `porcentaje_comision`) VALUES
(1, 2, 5.00),
(2, 4, 8.00),
(3, 3, 50.00),
(4, 1, 67.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Detalle_Pedidos`
--

CREATE TABLE `Detalle_Pedidos` (
  `idDetallePedido` int NOT NULL,
  `idPedido` int DEFAULT NULL,
  `idMedicamento` int DEFAULT NULL,
  `Cantidad` int NOT NULL,
  `Precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Detalle_Pedidos`
--

INSERT INTO `Detalle_Pedidos` (`idDetallePedido`, `idPedido`, `idMedicamento`, `Cantidad`, `Precio`) VALUES
(1, 1, 2, 35, 89.00),
(2, 2, 2, 5, 89.00),
(3, 5, 2, 6, 89.00),
(4, 7, 2, 6, 89.00),
(5, 14, 2, 8, 89.00),
(6, 15, 2, 8, 89.00),
(7, 19, 12, 67, 45.00),
(8, 20, 15, 78, 45.00),
(9, 20, 16, 56, 14.00),
(10, 21, 16, 6, 14.00),
(11, 21, 17, 7, 14.00),
(12, 22, 15, 67, 45.00),
(13, 22, 17, 78, 14.00),
(14, 22, 18, 56, 14.00),
(15, 23, 12, 6, 45.00),
(16, 23, 17, 45, 14.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Detalle_Ventas`
--

CREATE TABLE `Detalle_Ventas` (
  `idDetalleVenta` int NOT NULL,
  `idVenta` int DEFAULT NULL,
  `idMedicamento` int DEFAULT NULL,
  `Cantidad` int NOT NULL,
  `PrecioUnitario` decimal(10,2) NOT NULL,
  `PrecioTotal` decimal(10,2) NOT NULL,
  `idEmpleado` int DEFAULT NULL,
  `idComision` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `EliminacionMedicamento`
--

CREATE TABLE `EliminacionMedicamento` (
  `idEliminacion` int NOT NULL,
  `MedRegresable` varchar(100) DEFAULT NULL,
  `Detalle` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `EliminacionMedicamento`
--

INSERT INTO `EliminacionMedicamento` (`idEliminacion`, `MedRegresable`, `Detalle`) VALUES
(1, 'Regresable', 'Regresar sin costo extra al proveedor'),
(2, 'No Regresable', 'No se puede regresar o se debe pagar para ser destruido'),
(3, 'Remplazable', 'Se regresa el medicamento caducado y el proveedor lo remplaza por medicamento nuevo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Empleados`
--

CREATE TABLE `Empleados` (
  `idEmpleado` int NOT NULL,
  `Nombre` varchar(25) NOT NULL,
  `ApellidoP` varchar(25) NOT NULL,
  `ApellidoM` varchar(25) NOT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `CURP` varchar(20) NOT NULL,
  `RFC` varchar(15) NOT NULL,
  `idPuesto` int DEFAULT NULL,
  `idSucursal` int DEFAULT NULL,
  `Salario` decimal(10,2) DEFAULT NULL,
  `estatus` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `Comisiones` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Empleados`
--

INSERT INTO `Empleados` (`idEmpleado`, `Nombre`, `ApellidoP`, `ApellidoM`, `Telefono`, `CURP`, `RFC`, `idPuesto`, `idSucursal`, `Salario`, `estatus`, `Comisiones`) VALUES
(1, 'JOSE JUAN', 'SANCHEZ', 'CONTRERA', '333333333', 'VIGV820113MGTLNR0', 'ERTYSHDEW', 1, 1, 5000.00, 'Activo', 0),
(12, 'MARIA ', 'HERRERA', 'SANCHEZ', '4561092174', 'VIGV820113MGTLNR07', 'RRTEUWUUW', 4, 3, 1500.00, 'Activo', 0),
(13, 'JULIAN', 'PEREZ', 'MARTINEZ', '4561092174', 'VIGV820113MGTLNR07', 'ERTYSHDEWR', 2, 3, 4000.00, 'Activo', 0),
(14, 'KATIA ITZEL', 'ALCOCER', 'AGUILAR', '4561092174', 'VIGV820113MGTLNR07', 'ERTYSHDEWR', 2, 4, 2000.00, 'Activo', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Medicamento`
--

CREATE TABLE `Medicamento` (
  `idMedicamento` int NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `idClasificacion` int DEFAULT NULL,
  `Cantidad` int DEFAULT NULL,
  `PrecioCompra` decimal(10,2) NOT NULL,
  `PrecioVenta` decimal(10,2) NOT NULL,
  `idEliminacion` int DEFAULT NULL,
  `idProveedor` int DEFAULT NULL,
  `Descripcion` varchar(600) DEFAULT NULL,
  `Estatus` enum('Disponible','Vendido','Caducado','Eliminado') NOT NULL,
  `idSucursal` int DEFAULT NULL,
  `fechaCaducidad` date DEFAULT NULL,
  `DiasRestantes` int DEFAULT NULL,
  `EstadoCaducidad` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Medicamento`
--

INSERT INTO `Medicamento` (`idMedicamento`, `Nombre`, `idClasificacion`, `Cantidad`, `PrecioCompra`, `PrecioVenta`, `idEliminacion`, `idProveedor`, `Descripcion`, `Estatus`, `idSucursal`, `fechaCaducidad`, `DiasRestantes`, `EstadoCaducidad`) VALUES
(1, 'PARASETAMOL', 2, 2, 67.00, 89.00, 1, 2, 'RERR', 'Caducado', 3, '2024-11-01', -30, 'Caducado'),
(2, 'PARASETAMOL', 2, 2, 67.00, 89.00, 1, 2, 'RERR', 'Disponible', 2, '2024-12-26', 25, 'Vender con emergencia'),
(3, 'AGRIFEN', 2, 2, 67.00, 89.00, 2, 2, 'RERR', 'Caducado', 3, '2024-11-05', -26, 'Caducado'),
(4, 'LORATADINA', 3, 565, 23.00, 45.00, 1, 3, 'ERT', 'Caducado', 5, '2024-11-14', -17, 'Caducado'),
(5, 'LORATADINA/ IBUPROFENO', 3, 56, 23.00, 45.00, 1, 4, 'ERT', 'Caducado', 2, '2024-11-22', -9, 'Caducado'),
(6, 'LORATADINA/ IBUPROFENO', 3, 56, 23.00, 45.00, 1, 1, 'ERT', 'Caducado', 1, '2024-11-01', -30, 'Caducado'),
(7, 'LORATADINA/ IBUPROFENO', 4, 56, 23.00, 45.00, 2, 2, 'ERT', 'Caducado', 2, '2024-11-12', -19, 'Caducado'),
(8, 'LORATADINA/ IBUPROFENO', 4, 56, 23.00, 45.00, 2, 2, 'ERT', 'Caducado', 1, '2024-11-27', -4, 'Caducado'),
(9, 'LORATADINA/ IBUPROFENO', 4, 56, 23.00, 45.00, 2, 2, 'ERT', 'Caducado', 3, '2024-11-19', -12, 'Caducado'),
(10, 'LORATADINA/ IBUPROFENO', 4, 56, 23.00, 45.00, 2, 2, 'ERT', 'Caducado', 4, '2024-11-13', -18, 'Caducado'),
(11, ' IBUPROFENO', 4, 56, 23.00, 45.00, 2, 2, 'ERT', 'Caducado', 2, '2024-11-27', -4, 'Caducado'),
(12, ' IBUPROFENO', 4, 56, 23.00, 45.00, 2, 2, 'ERT', 'Disponible', 3, '2024-12-18', 17, 'Vender con emergencia'),
(13, 'FFFFFFFFFFFFF', 3, 45, 67.00, 45.00, 2, 1, 'RERR', 'Eliminado', 4, '2024-11-12', -19, 'Caducado'),
(14, 'FFFFFFFFFFFFF', 3, 45, 67.00, 45.00, 2, 1, 'RERR', 'Eliminado', 3, '2025-02-21', 82, 'Se acerca a fecha limite'),
(15, 'GELMICIN', 3, 45, 67.00, 45.00, 2, 1, 'RERR', 'Caducado', 2, '2024-11-02', -29, 'Caducado'),
(16, 'DICLOFENACO', 1, 56, 10.00, 14.00, 2, 1, 'MEDICAMENTO PARA LA GASTRITIS', 'Disponible', 2, '2025-12-26', 390, 'Medicamento en buen estado'),
(17, 'AMBROXOL', 1, 56, 10.00, 14.00, 2, 1, 'MEDICAMENTO PARA LA GASTRITIS', 'Disponible', 4, '2025-06-05', 186, 'Medicamento en buen estado'),
(18, 'LORATADINA', 1, 56, 10.00, 14.00, 2, 1, 'MEDICAMENTO PARA LA GASTRITIS', 'Disponible', 2, '2025-07-17', 228, 'Medicamento en buen estado'),
(19, 'OMEPRRAZOL', 1, 56, 10.00, 14.00, 2, 1, 'MEDICAMENTO PARA LA GASTRITIS', 'Disponible', 4, '2026-03-25', 479, 'Medicamento en buen estado'),
(20, 'SABRITAS', 6, 56, 15.00, 18.00, 2, 1, 'COMIDA', 'Caducado', 1, '2024-11-28', -3, 'Caducado'),
(21, 'SABRITAS', 6, 56, 15.00, 18.00, 2, 1, 'COMIDA', 'Caducado', 2, '2024-11-28', -3, 'Caducado');

--
-- Disparadores `Medicamento`
--
DELIMITER $$
CREATE TRIGGER `actualizar_estado_medicamento` BEFORE INSERT ON `Medicamento` FOR EACH ROW BEGIN
    DECLARE dias_restantes INT;
    SET dias_restantes = DATEDIFF(NEW.fechaCaducidad, CURDATE());
    
    SET NEW.DiasRestantes = dias_restantes;
    
    IF dias_restantes < 0 THEN
        SET NEW.Estatus = 'Caducado';
    ELSEIF dias_restantes <= 60 THEN
        SET NEW.EstadoCaducidad = 'Vender con emergencia';
    ELSEIF dias_restantes <= 90 THEN
        SET NEW.EstadoCaducidad = 'Se acerca a fecha limite';
    ELSE
        SET NEW.EstadoCaducidad = 'Medicamento en buen estado';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ofertas`
--

CREATE TABLE `Ofertas` (
  `idOferta` int NOT NULL,
  `idMedicamento` int DEFAULT NULL,
  `FechaInicio` date NOT NULL,
  `FechaFin` date NOT NULL,
  `PorcentajeDescuento` decimal(5,2) NOT NULL,
  `estatus` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Ofertas`
--

INSERT INTO `Ofertas` (`idOferta`, `idMedicamento`, `FechaInicio`, `FechaFin`, `PorcentajeDescuento`, `estatus`) VALUES
(1, 2, '2024-12-01', '2024-12-02', 5.00, 'Activo'),
(2, 16, '2024-12-01', '2024-12-02', 10.00, 'Activo'),
(3, 15, '2024-12-01', '2024-12-07', 5.00, 'Activo'),
(4, 17, '2024-12-01', '2024-12-07', 8.00, 'Activo'),
(5, 2, '2024-12-01', '2024-12-27', 50.00, 'Activo'),
(6, 18, '2024-12-01', '2024-12-06', 4.00, 'Activo'),
(7, 3, '2024-12-01', '2024-12-05', 4.00, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Pedidos`
--

CREATE TABLE `Pedidos` (
  `idPedido` int NOT NULL,
  `idSucursal` int DEFAULT NULL,
  `idProveedor` int DEFAULT NULL,
  `FechaPedido` date NOT NULL,
  `Estado` enum('Pendiente','Aprobado','Recibido','Rechazado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Pedidos`
--

INSERT INTO `Pedidos` (`idPedido`, `idSucursal`, `idProveedor`, `FechaPedido`, `Estado`) VALUES
(1, 4, 1, '2024-12-01', 'Rechazado'),
(2, 1, 1, '2024-12-01', 'Aprobado'),
(3, 1, 1, '2024-12-01', 'Pendiente'),
(4, 1, 1, '2024-12-01', 'Pendiente'),
(5, 1, 2, '2024-12-01', 'Rechazado'),
(6, 1, 1, '2024-12-01', 'Pendiente'),
(7, 4, 1, '2024-12-01', 'Aprobado'),
(8, 3, 3, '2024-12-01', 'Pendiente'),
(9, 1, 1, '2024-12-01', 'Pendiente'),
(10, 1, 1, '2024-12-01', 'Pendiente'),
(11, 1, 1, '2024-12-01', 'Pendiente'),
(12, 1, 1, '2024-12-01', 'Pendiente'),
(13, 1, 1, '2024-12-01', 'Pendiente'),
(14, 1, 1, '2024-12-01', 'Rechazado'),
(15, 1, 1, '2024-12-01', 'Aprobado'),
(16, 1, 1, '2024-12-01', 'Pendiente'),
(17, 1, 1, '2024-12-01', 'Pendiente'),
(18, 4, 2, '2024-12-01', 'Pendiente'),
(19, 1, 1, '2024-12-01', 'Pendiente'),
(20, 1, 1, '2024-12-01', 'Pendiente'),
(21, 1, 1, '2024-12-01', 'Pendiente'),
(22, 1, 3, '2024-12-01', 'Pendiente'),
(23, 3, 3, '2024-12-02', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Proveedores`
--

CREATE TABLE `Proveedores` (
  `idProveedor` int NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Direccion` varchar(250) DEFAULT NULL,
  `estatus` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Proveedores`
--

INSERT INTO `Proveedores` (`idProveedor`, `Nombre`, `Telefono`, `Direccion`, `estatus`) VALUES
(1, 'Laboratorio', '123', 'rtyyy', 'Activo'),
(2, 'Laboratorio19', '4561238909', 'Calle Moreliano NO.#8, Salvatierra, Guanajuato', 'Activo'),
(3, 'Laboratorio1', '4561238903', 'Calle Moreliano NO.#8, Salvatierra, Guanajuato', 'Activo'),
(4, 'Laboratorio7', '4561238903', 'Calle Moreliano NO.#8, Salvatierra, Guanajuato', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Puesto`
--

CREATE TABLE `Puesto` (
  `idPuesto` int NOT NULL,
  `Puesto` enum('Dueño','Gerente','Cajero','Almacenista') NOT NULL,
  `Salario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Puesto`
--

INSERT INTO `Puesto` (`idPuesto`, `Puesto`, `Salario`) VALUES
(1, 'Dueño', '5000'),
(2, 'Gerente', '4000'),
(3, 'Cajero', '2000'),
(4, 'Almacenista', '1500');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Sucursales`
--

CREATE TABLE `Sucursales` (
  `idSucursal` int NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Direccion` varchar(250) DEFAULT NULL,
  `Usuario` varchar(50) DEFAULT NULL,
  `Clave` varchar(255) DEFAULT NULL,
  `estatus` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Sucursales`
--

INSERT INTO `Sucursales` (`idSucursal`, `Nombre`, `Telefono`, `Direccion`, `Usuario`, `Clave`, `estatus`) VALUES
(1, 'La Pildora Principal', '4561092174', 'Calle Zedron No.#45 Yuriria, Guanajuato', 'admin@example.com', '10203040', 'Activo'),
(2, 'Sucursal Moroleón', '4561092174', 'Calle Juarez No.#78 Moroleon, Guanajuato', 'usuario@example.com', '12345', 'Activo'),
(3, 'Sucursal Huanímaro', '4561073172', 'Calle Gutiérrez No.#34 Huanímaro Guanajuato', 'usuario2@example.com', '123456', 'Activo'),
(4, 'Sucursal Salvatierra', '4561238903', 'Calle Moreliano No.#99, Salvatierra, Guanajuato', 'usuario3@example.com', '1234567', 'Activo'),
(5, 'Sucursal Queretaro', '3456789009', 'Calle Mendez No.#1, Queretaro', 'usuario7@example.com', '1234', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ventas`
--

CREATE TABLE `Ventas` (
  `idVenta` int NOT NULL,
  `idSucursal` int DEFAULT NULL,
  `FechaVenta` date NOT NULL,
  `NombrePaciente` varchar(100) DEFAULT NULL,
  `NombreDoctor` varchar(100) DEFAULT NULL,
  `TelefonoDoctor` varchar(15) DEFAULT NULL,
  `CedulaDoctor` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ClasificacionM`
--
ALTER TABLE `ClasificacionM`
  ADD PRIMARY KEY (`idClasificacion`);

--
-- Indices de la tabla `Comisiones`
--
ALTER TABLE `Comisiones`
  ADD PRIMARY KEY (`idComision`),
  ADD KEY `idMedicamento` (`idMedicamento`);

--
-- Indices de la tabla `Detalle_Pedidos`
--
ALTER TABLE `Detalle_Pedidos`
  ADD PRIMARY KEY (`idDetallePedido`),
  ADD KEY `idPedido` (`idPedido`),
  ADD KEY `idMedicamento` (`idMedicamento`);

--
-- Indices de la tabla `Detalle_Ventas`
--
ALTER TABLE `Detalle_Ventas`
  ADD PRIMARY KEY (`idDetalleVenta`),
  ADD KEY `idEmpleado` (`idEmpleado`),
  ADD KEY `idComision` (`idComision`),
  ADD KEY `idVenta` (`idVenta`),
  ADD KEY `idMedicamento` (`idMedicamento`);

--
-- Indices de la tabla `EliminacionMedicamento`
--
ALTER TABLE `EliminacionMedicamento`
  ADD PRIMARY KEY (`idEliminacion`);

--
-- Indices de la tabla `Empleados`
--
ALTER TABLE `Empleados`
  ADD PRIMARY KEY (`idEmpleado`),
  ADD KEY `idPuesto` (`idPuesto`),
  ADD KEY `FK_idSucursal` (`idSucursal`);

--
-- Indices de la tabla `Medicamento`
--
ALTER TABLE `Medicamento`
  ADD PRIMARY KEY (`idMedicamento`),
  ADD KEY `idClasificacion` (`idClasificacion`),
  ADD KEY `idEliminacion` (`idEliminacion`),
  ADD KEY `idProveedor` (`idProveedor`),
  ADD KEY `idSucursal` (`idSucursal`);

--
-- Indices de la tabla `Ofertas`
--
ALTER TABLE `Ofertas`
  ADD PRIMARY KEY (`idOferta`),
  ADD KEY `idMedicamento` (`idMedicamento`);

--
-- Indices de la tabla `Pedidos`
--
ALTER TABLE `Pedidos`
  ADD PRIMARY KEY (`idPedido`),
  ADD KEY `idSucursal` (`idSucursal`),
  ADD KEY `idProveedor` (`idProveedor`);

--
-- Indices de la tabla `Proveedores`
--
ALTER TABLE `Proveedores`
  ADD PRIMARY KEY (`idProveedor`);

--
-- Indices de la tabla `Puesto`
--
ALTER TABLE `Puesto`
  ADD PRIMARY KEY (`idPuesto`);

--
-- Indices de la tabla `Sucursales`
--
ALTER TABLE `Sucursales`
  ADD PRIMARY KEY (`idSucursal`);

--
-- Indices de la tabla `Ventas`
--
ALTER TABLE `Ventas`
  ADD PRIMARY KEY (`idVenta`),
  ADD KEY `idSucursal` (`idSucursal`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ClasificacionM`
--
ALTER TABLE `ClasificacionM`
  MODIFY `idClasificacion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `Comisiones`
--
ALTER TABLE `Comisiones`
  MODIFY `idComision` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `Detalle_Pedidos`
--
ALTER TABLE `Detalle_Pedidos`
  MODIFY `idDetallePedido` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `Detalle_Ventas`
--
ALTER TABLE `Detalle_Ventas`
  MODIFY `idDetalleVenta` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `EliminacionMedicamento`
--
ALTER TABLE `EliminacionMedicamento`
  MODIFY `idEliminacion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Empleados`
--
ALTER TABLE `Empleados`
  MODIFY `idEmpleado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `Medicamento`
--
ALTER TABLE `Medicamento`
  MODIFY `idMedicamento` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `Ofertas`
--
ALTER TABLE `Ofertas`
  MODIFY `idOferta` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `Pedidos`
--
ALTER TABLE `Pedidos`
  MODIFY `idPedido` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `Proveedores`
--
ALTER TABLE `Proveedores`
  MODIFY `idProveedor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `Puesto`
--
ALTER TABLE `Puesto`
  MODIFY `idPuesto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `Sucursales`
--
ALTER TABLE `Sucursales`
  MODIFY `idSucursal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `Ventas`
--
ALTER TABLE `Ventas`
  MODIFY `idVenta` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Comisiones`
--
ALTER TABLE `Comisiones`
  ADD CONSTRAINT `comisiones_ibfk_1` FOREIGN KEY (`idMedicamento`) REFERENCES `Medicamento` (`idMedicamento`);

--
-- Filtros para la tabla `Detalle_Pedidos`
--
ALTER TABLE `Detalle_Pedidos`
  ADD CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`idPedido`) REFERENCES `Pedidos` (`idPedido`),
  ADD CONSTRAINT `detalle_pedidos_ibfk_2` FOREIGN KEY (`idMedicamento`) REFERENCES `Medicamento` (`idMedicamento`);

--
-- Filtros para la tabla `Detalle_Ventas`
--
ALTER TABLE `Detalle_Ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`idEmpleado`) REFERENCES `Empleados` (`idEmpleado`),
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`idComision`) REFERENCES `Comisiones` (`idComision`),
  ADD CONSTRAINT `detalle_ventas_ibfk_3` FOREIGN KEY (`idVenta`) REFERENCES `Ventas` (`idVenta`),
  ADD CONSTRAINT `detalle_ventas_ibfk_4` FOREIGN KEY (`idMedicamento`) REFERENCES `Medicamento` (`idMedicamento`);

--
-- Filtros para la tabla `Empleados`
--
ALTER TABLE `Empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`idPuesto`) REFERENCES `Puesto` (`idPuesto`),
  ADD CONSTRAINT `FK_idSucursal` FOREIGN KEY (`idSucursal`) REFERENCES `Sucursales` (`idSucursal`);

--
-- Filtros para la tabla `Medicamento`
--
ALTER TABLE `Medicamento`
  ADD CONSTRAINT `idSucursal` FOREIGN KEY (`idSucursal`) REFERENCES `Sucursales` (`idSucursal`),
  ADD CONSTRAINT `medicamento_ibfk_1` FOREIGN KEY (`idClasificacion`) REFERENCES `ClasificacionM` (`idClasificacion`),
  ADD CONSTRAINT `medicamento_ibfk_2` FOREIGN KEY (`idEliminacion`) REFERENCES `EliminacionMedicamento` (`idEliminacion`),
  ADD CONSTRAINT `medicamento_ibfk_3` FOREIGN KEY (`idProveedor`) REFERENCES `Proveedores` (`idProveedor`);

--
-- Filtros para la tabla `Ofertas`
--
ALTER TABLE `Ofertas`
  ADD CONSTRAINT `ofertas_ibfk_1` FOREIGN KEY (`idMedicamento`) REFERENCES `Medicamento` (`idMedicamento`);

--
-- Filtros para la tabla `Pedidos`
--
ALTER TABLE `Pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`idSucursal`) REFERENCES `Sucursales` (`idSucursal`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`idProveedor`) REFERENCES `Proveedores` (`idProveedor`);

--
-- Filtros para la tabla `Ventas`
--
ALTER TABLE `Ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`idSucursal`) REFERENCES `Sucursales` (`idSucursal`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`%` EVENT `actualizar_medicamentos_diario` ON SCHEDULE EVERY 1 DAY STARTS '2024-11-30 18:37:54' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE Medicamento
SET DiasRestantes = DATEDIFF(fechaCaducidad, CURDATE()),
    EstadoCaducidad = CASE 
                        WHEN DATEDIFF(fechaCaducidad, CURDATE()) < 0 THEN 'Caducado'
                        WHEN DATEDIFF(fechaCaducidad, CURDATE()) <= 60 THEN 'Vender con emergencia'
                        WHEN DATEDIFF(fechaCaducidad, CURDATE()) <= 90 THEN 'Se acerca a fecha limite'
                        ELSE 'Medicamento en buen estado'
                      END,
    Estatus = CASE 
                WHEN DATEDIFF(fechaCaducidad, CURDATE()) < 0 THEN 'Caducado'
                ELSE Estatus
              END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
