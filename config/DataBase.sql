-- --------------------------------------------------------
-- Script Corregido Edu360 Staging
-- --------------------------------------------------------

SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;
SET NAMES utf8mb4;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET @OLD_TIME_ZONE=@@TIME_ZONE;
SET TIME_ZONE='+00:00';

-- 1. CREACIÓN DE BASE DE DATOS
CREATE DATABASE IF NOT EXISTS `u448703401_edu360` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `u448703401_edu360`;

-- 2. TABLA: evolucionadores (MAESTRA)
CREATE TABLE IF NOT EXISTS `evolucionadores` (
  `id_evolucionador` int(11) NOT NULL AUTO_INCREMENT,
  `hash_identidad` text NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `email_verificado` varchar(100) NOT NULL,
  `estatus_soberania` enum('Activo','En Consolidación','Suspendido') DEFAULT 'Activo',
  `total_udv_acumuladas` decimal(10,2) DEFAULT 0.00,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  `password` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `verificado` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_evolucionador`),
  UNIQUE KEY `email_verificado` (`email_verificado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `evolucionadores` (`id_evolucionador`, `hash_identidad`, `nombre_completo`, `email_verificado`, `estatus_soberania`, `total_udv_acumuladas`, `creado_at`, `password`, `foto`, `verificado`) VALUES
(2, '0x267212A4267212A4267212A4267212A4', 'Daniel Alfonsi', 'alfonsi.acosta@gmail.com', 'Activo', 3.00, '2026-01-14 20:43:17', '1nyIl0IE7DUUGPRFtGMSGcxrY4OTM/v76NaBf80rtck=', 'perfil_1768686193.jpg', 1);

-- 3. TABLA: artefactos_dominio (MAESTRA)
CREATE TABLE IF NOT EXISTS `artefactos_dominio` (
  `id_artefacto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `url_vault` varchar(255) NOT NULL,
  `nivel_trayectoria` varchar(255) NOT NULL,
  `densidad_cognitiva_udv` int(11) NOT NULL DEFAULT 0,
  `naturaleza_validacion` varchar(255) NOT NULL,
  PRIMARY KEY (`id_artefacto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `artefactos_dominio` (`id_artefacto`, `nombre`, `url_vault`, `nivel_trayectoria`, `densidad_cognitiva_udv`, `naturaleza_validacion`) VALUES
(1, 'Paradigma Edu360', 'https://drive.google.com/file/d/1jNwcmCFtRieZPtxK8rV-H7dGUgqtJDZ8/view?usp=sharing', 'Diplomado de Dominio', 100, 'Integración funcional de habilidades básicas');

-- 4. TABLA: artefactos_metas (DEPENDE DE artefactos_dominio)
CREATE TABLE IF NOT EXISTS `artefactos_metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_artefacto` int(11) NOT NULL DEFAULT 0,
  `position` int(11) DEFAULT NULL,
  `meta` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `objetivo` varchar(255) DEFAULT NULL,
  `valor_udv` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `id_artefacto` (`id_artefacto`),
  CONSTRAINT `FK__artefactos_dominio` FOREIGN KEY (`id_artefacto`) REFERENCES `artefactos_dominio` (`id_artefacto`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `artefactos_metas` (`id`, `id_artefacto`, `position`, `meta`, `descripcion`, `objetivo`, `valor_udv`) VALUES
(1, 1, 1, 'Transformación Mental y Paradigmática', 'Internalizar el cambio del modelo industrial al modelo "360"', 'Comprender la neuroeducación y cómo el cerebro aprende en entornos digitales', 20.00),
(2, 1, 2, 'Dominio de Ecosistemas Tecnológicos', 'Integrar herramientas de Inteligencia Artificial, Realidad Aumentada y entornos virtuales en el aula', 'Pasar de ser un consumidor de tecnología a un creador de recursos educativos disruptivos', 30.00),
(3, 1, 3, 'Diseño Pedagógico Innovador (ABP)', 'Implementar el Aprendizaje Basado en Proyectos con impacto social', 'Crear currículos flexibles que fomenten el pensamiento crítico y la resolución de problemas reales', 25.00),
(4, 1, 4, 'Liderazgo y Humanismo Digital', 'Desarrollar habilidades blandas (soft skills) y ética en el uso de datos', 'Ejercer un rol de mentoría que priorice el bienestar emocional del estudiante', 25.00);

-- 5. TABLA: audit_log_inquisidor (DEPENDE DE VARIAS)
CREATE TABLE IF NOT EXISTS `audit_log_inquisidor` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `id_artefacto` int(11) NOT NULL,
  `id_artefacto_meta` int(11) NOT NULL,
  `id_evolucionador` int(11) NOT NULL,
  `score_rigor` decimal(5,2) NOT NULL,
  `veredicto` enum('Acuñado','En Desarrollo') NOT NULL,
  `friccion_detectada` text DEFAULT NULL,
  `udv_otorgadas` decimal(10,2) DEFAULT 0.00,
  `auditado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_auditoria`),
  KEY `fk_audit_artefacto` (`id_artefacto`),
  KEY `id_artefacto_meta` (`id_artefacto_meta`),
  KEY `id_evolucionador` (`id_evolucionador`),
  CONSTRAINT `FK_audit_log_inquisidor_artefactos_metas` FOREIGN KEY (`id_artefacto_meta`) REFERENCES `artefactos_metas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_audit_log_inquisidor_evolucionadores` FOREIGN KEY (`id_evolucionador`) REFERENCES `evolucionadores` (`id_evolucionador`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_audit_artefacto` FOREIGN KEY (`id_artefacto`) REFERENCES `artefactos_dominio` (`id_artefacto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `audit_log_inquisidor` (`id_auditoria`, `id_artefacto`, `id_artefacto_meta`, `id_evolucionador`, `score_rigor`, `veredicto`, `friccion_detectada`, `udv_otorgadas`, `auditado_at`) VALUES
(1, 1, 1, 2, 0.00, 'En Desarrollo', NULL, 0.00, '2026-01-19 17:32:29');

-- 6. TABLA: chat_auditoria_log
CREATE TABLE IF NOT EXISTS `chat_auditoria_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_evolucionador` int(11) NOT NULL,
  `id_artefacto` int(11) NOT NULL,
  `id_artefacto_meta` int(11) NOT NULL,
  `role` enum('user','assistant') NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_evolucionador` (`id_evolucionador`,`id_artefacto`,`id_artefacto_meta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `chat_auditoria_log` (`id`, `id_evolucionador`, `id_artefacto`, `id_artefacto_meta`, `role`, `content`, `created_at`) VALUES
(1, 2, 1, 1, 'user', 'prueba', '2026-01-19 14:41:06'),
(2, 2, 1, 1, 'assistant', 'Evolucionador, la respuesta es genérica...', '2026-01-19 14:41:06'),
(3, 2, 1, 1, 'user', 'con este metodo el evolucionador podra obtener un titulo...', '2026-01-19 17:27:40'),
(4, 2, 1, 1, 'assistant', 'Evolucionador, tu SRAA actual menciona un beneficio general...', '2026-01-19 17:27:40');

-- 7. TABLA: nodos_activos
CREATE TABLE IF NOT EXISTS `nodos_activos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_evolucionador` int(11) NOT NULL,
  `stripe_session_id` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estatus` varchar(50) NOT NULL DEFAULT 'Activado',
  `fecha_activacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  `id_artefacto` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session` (`stripe_session_id`),
  KEY `idx_evolucionador` (`id_evolucionador`),
  CONSTRAINT `fk_nodos_evolucionador` FOREIGN KEY (`id_evolucionador`) REFERENCES `evolucionadores` (`id_evolucionador`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nodos_activos` (`id`, `id_evolucionador`, `stripe_session_id`, `monto`, `estatus`, `fecha_activacion`, `fecha_expiracion`, `id_artefacto`) VALUES
(4, 2, 'cs_test_a1W5SeRd4O947GaphMrG2Ieif13iWf2nW83pS24TJmRIosPlwB5FDXgt4o', 20.00, 'Activado', '2026-01-17 18:13:15', NULL, 1);

-- 8. TABLA: staging
CREATE TABLE IF NOT EXISTS `staging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `nivel` enum('Administrador','Tecnico','Usuario') NOT NULL DEFAULT 'Tecnico',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `staging` (`id`, `usuario`, `password`, `nivel`) VALUES
(1, 'alfonsi.acosta@gmail.com', 'f/t8tUSW+70FTNk9E2+PxUEPr8v0qgIV7d5Ofl419pY=', 'Tecnico'),
(2, 'likomanuel1975@gmail.com', 'fayfZreOxnXXGDWNBXVJf/H51EING4wtAZ1fvu9EaCU=', 'Tecnico');

-- 9. TRIGGER CORREGIDO
DELIMITER //
CREATE TRIGGER tr_actualizar_udv_soberania
AFTER INSERT ON audit_log_inquisidor
FOR EACH ROW
BEGIN
    IF NEW.veredicto = 'Acuñado' THEN
        UPDATE evolucionadores
        SET total_udv_acumuladas = total_udv_acumuladas + NEW.udv_otorgadas
        WHERE id_evolucionador = NEW.id_evolucionador;
    END IF;
END//
DELIMITER ;

-- 10. FINALIZACIÓN
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;
SET SQL_MODE=@OLD_SQL_MODE;
SET TIME_ZONE=@OLD_TIME_ZONE;