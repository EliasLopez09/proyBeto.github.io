-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-10-2025 a las 18:24:59
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
-- Base de datos: `liga_bdd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clubes`
--

CREATE TABLE `clubes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clubes`
--

INSERT INTO `clubes` (`id`, `nombre`, `status`) VALUES
(1, 'CLUB A', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `carnet` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `fechajuego` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id`, `club_id`, `codigo`, `nombre`, `apellido`, `carnet`, `status`, `fechajuego`) VALUES
(1, 1, 'JG0001', 'Julian', 'Carvajal', '1384', 1, '2025-10-09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id_persona` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fyh_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id_persona`, `nombre`, `apellido`, `email`, `password`, `fyh_creacion`) VALUES
(1, 'Elias', 'Lopez', 'elias@gmail.com', '$2y$10$jqcNAQKj9oHQ3TDm8UpYcuGh6lZ5omcV/TUQzI8rNyYQFuseGExQO', '2025-09-03 00:11:43');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clubes`
--
ALTER TABLE `clubes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD UNIQUE KEY `carnet` (`carnet`),
  ADD KEY `club_id` (`club_id`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clubes`
--
ALTER TABLE `clubes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `jugadores_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
