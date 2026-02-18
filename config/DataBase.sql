-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 18-02-2026 a las 20:48:47
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u448703401_edu360`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artefactos_dominio`
--

CREATE TABLE `artefactos_dominio` (
  `id_artefacto` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `url_vault` varchar(255) NOT NULL,
  `nivel_trayectoria` varchar(255) NOT NULL,
  `densidad_cognitiva_udv` int(11) NOT NULL DEFAULT 0,
  `naturaleza_validacion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `artefactos_dominio`
--

INSERT INTO `artefactos_dominio` (`id_artefacto`, `nombre`, `url_vault`, `nivel_trayectoria`, `densidad_cognitiva_udv`, `naturaleza_validacion`) VALUES
(1, 'Paradigma Edu360', 'https://drive.google.com/file/d/1jNwcmCFtRieZPtxK8rV-H7dGUgqtJDZ8/view?usp=sharing', 'Diplomado de Dominio', 20, 'Integración funcional de habilidades básicas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artefactos_metas`
--

CREATE TABLE `artefactos_metas` (
  `id` int(11) NOT NULL,
  `id_artefacto` int(11) NOT NULL DEFAULT 0,
  `position` int(11) DEFAULT NULL,
  `meta` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `objetivo` varchar(255) DEFAULT NULL,
  `valor_udv` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `artefactos_metas`
--

INSERT INTO `artefactos_metas` (`id`, `id_artefacto`, `position`, `meta`, `descripcion`, `objetivo`, `valor_udv`) VALUES
(1, 1, 1, 'Transformación Mental y Paradigmática', 'Internalizar el cambio del modelo industrial al modelo \"360\"', 'Comprender la neuroeducación y cómo el cerebro aprende en entornos digitales', 5.00),
(2, 1, 2, 'Dominio de Ecosistemas Tecnológicos', 'Integrar herramientas de Inteligencia Artificial, Realidad Aumentada y entornos virtuales en el aula', 'Pasar de ser un consumidor de tecnología a un creador de recursos educativos disruptivos', 5.00),
(3, 1, 3, 'Diseño Pedagógico Innovador (ABP)', 'Implementar el Aprendizaje Basado en Proyectos con impacto social', 'Crear currículos flexibles que fomenten el pensamiento crítico y la resolución de problemas reales', 5.00),
(4, 1, 4, 'Liderazgo y Humanismo Digital', 'Desarrollar habilidades blandas (soft skills) y ética en el uso de datos', 'Ejercer un rol de mentoría que priorice el bienestar emocional del estudiante', 5.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log_inquisidor`
--

CREATE TABLE `audit_log_inquisidor` (
  `id_auditoria` int(11) NOT NULL,
  `id_artefacto` int(11) NOT NULL,
  `id_artefacto_meta` int(11) NOT NULL,
  `id_evolucionador` int(11) NOT NULL,
  `score_rigor` decimal(5,2) NOT NULL,
  `veredicto` enum('Acuñado','En Desarrollo') NOT NULL,
  `friccion_detectada` text DEFAULT NULL,
  `udv_otorgadas` decimal(10,2) DEFAULT 0.00,
  `auditado_at` timestamp NULL DEFAULT current_timestamp(),
  `sumado` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `audit_log_inquisidor`
--
DELIMITER $$
CREATE TRIGGER `tr_actualizar_udv_soberania` AFTER INSERT ON `audit_log_inquisidor` FOR EACH ROW BEGIN
    IF NEW.veredicto = 'Acuñado' THEN
        UPDATE evolucionadores
        SET total_udv_acumuladas = total_udv_acumuladas + NEW.udv_otorgadas
        WHERE id_evolucionador = NEW.id_evolucionador;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_auditoria_log`
--

CREATE TABLE `chat_auditoria_log` (
  `id` int(11) NOT NULL,
  `id_evolucionador` int(11) NOT NULL,
  `id_artefacto` int(11) NOT NULL,
  `id_artefacto_meta` int(11) NOT NULL,
  `role` enum('user','assistant') NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evolucionadores`
--

CREATE TABLE `evolucionadores` (
  `id_evolucionador` int(11) NOT NULL,
  `hash_identidad` text NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `email_verificado` varchar(100) NOT NULL,
  `estatus_soberania` enum('Activo','En Consolidación','Suspendido') DEFAULT 'Activo',
  `total_udv_acumuladas` decimal(10,2) DEFAULT 0.00,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  `password` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `verificado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `LINKS`
--

CREATE TABLE `LINKS` (
  `ID` int(11) NOT NULL,
  `FECHA` timestamp NOT NULL DEFAULT current_timestamp(),
  `LINK` varchar(255) DEFAULT NULL,
  `CORREO` varchar(255) DEFAULT NULL,
  `BLOQUEADO` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nodos_activos`
--

CREATE TABLE `nodos_activos` (
  `id` int(11) NOT NULL,
  `id_evolucionador` int(11) NOT NULL,
  `stripe_session_id` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estatus` varchar(50) NOT NULL DEFAULT 'Activado',
  `fecha_activacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  `id_artefacto` int(11) DEFAULT 1,
  `certificado_generado` tinyint(1) DEFAULT 0,
  `tipo_nodo` enum('Beta','Alfa','Gamma','Omega') NOT NULL DEFAULT 'Omega'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `artefactos_dominio`
--
ALTER TABLE `artefactos_dominio`
  ADD PRIMARY KEY (`id_artefacto`);

--
-- Indices de la tabla `artefactos_metas`
--
ALTER TABLE `artefactos_metas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_artefacto` (`id_artefacto`);

--
-- Indices de la tabla `audit_log_inquisidor`
--
ALTER TABLE `audit_log_inquisidor`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `fk_audit_artefacto` (`id_artefacto`),
  ADD KEY `id_artefacto_meta` (`id_artefacto_meta`),
  ADD KEY `id_evolucionador` (`id_evolucionador`);

--
-- Indices de la tabla `chat_auditoria_log`
--
ALTER TABLE `chat_auditoria_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_evolucionador` (`id_evolucionador`,`id_artefacto`,`id_artefacto_meta`);

--
-- Indices de la tabla `evolucionadores`
--
ALTER TABLE `evolucionadores`
  ADD PRIMARY KEY (`id_evolucionador`),
  ADD UNIQUE KEY `email_verificado` (`email_verificado`);

--
-- Indices de la tabla `LINKS`
--
ALTER TABLE `LINKS`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `nodos_activos`
--
ALTER TABLE `nodos_activos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_session` (`stripe_session_id`),
  ADD KEY `idx_evolucionador` (`id_evolucionador`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `artefactos_dominio`
--
ALTER TABLE `artefactos_dominio`
  MODIFY `id_artefacto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `artefactos_metas`
--
ALTER TABLE `artefactos_metas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `audit_log_inquisidor`
--
ALTER TABLE `audit_log_inquisidor`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `chat_auditoria_log`
--
ALTER TABLE `chat_auditoria_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `evolucionadores`
--
ALTER TABLE `evolucionadores`
  MODIFY `id_evolucionador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `LINKS`
--
ALTER TABLE `LINKS`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `nodos_activos`
--
ALTER TABLE `nodos_activos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `artefactos_metas`
--
ALTER TABLE `artefactos_metas`
  ADD CONSTRAINT `FK__artefactos_dominio` FOREIGN KEY (`id_artefacto`) REFERENCES `artefactos_dominio` (`id_artefacto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `audit_log_inquisidor`
--
ALTER TABLE `audit_log_inquisidor`
  ADD CONSTRAINT `FK_audit_log_inquisidor_artefactos_metas` FOREIGN KEY (`id_artefacto_meta`) REFERENCES `artefactos_metas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_audit_log_inquisidor_evolucionadores` FOREIGN KEY (`id_evolucionador`) REFERENCES `evolucionadores` (`id_evolucionador`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_audit_artefacto` FOREIGN KEY (`id_artefacto`) REFERENCES `artefactos_dominio` (`id_artefacto`);

--
-- Filtros para la tabla `nodos_activos`
--
ALTER TABLE `nodos_activos`
  ADD CONSTRAINT `fk_nodos_evolucionador` FOREIGN KEY (`id_evolucionador`) REFERENCES `evolucionadores` (`id_evolucionador`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
