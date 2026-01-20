-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         11.8.2-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para edu360-staging
CREATE DATABASE IF NOT EXISTS `edu360-staging` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `edu360-staging`;

-- Volcando estructura para tabla edu360-staging.artefactos_dominio
CREATE TABLE IF NOT EXISTS `artefactos_dominio` (
  `id_artefacto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `url_vault` varchar(255) NOT NULL,
  `nivel_trayectoria` varchar(255) NOT NULL,
  `densidad_cognitiva_udv` int(11) NOT NULL DEFAULT 0,
  `naturaleza_validacion` varchar(255) NOT NULL,
  PRIMARY KEY (`id_artefacto`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla edu360-staging.artefactos_dominio: ~1 rows (aproximadamente)
DELETE FROM `artefactos_dominio`;
INSERT INTO `artefactos_dominio` (`id_artefacto`, `nombre`, `url_vault`, `nivel_trayectoria`, `densidad_cognitiva_udv`, `naturaleza_validacion`) VALUES
	(1, 'Paradigma Edu360', 'https://drive.google.com/file/d/1jNwcmCFtRieZPtxK8rV-H7dGUgqtJDZ8/view?usp=sharing', 'Diplomado de Dominio', 100, 'Integración funcional de habilidades básicas');

-- Volcando estructura para tabla edu360-staging.artefactos_metas
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla edu360-staging.artefactos_metas: ~4 rows (aproximadamente)
DELETE FROM `artefactos_metas`;
INSERT INTO `artefactos_metas` (`id`, `id_artefacto`, `position`, `meta`, `descripcion`, `objetivo`, `valor_udv`) VALUES
	(1, 1, 1, 'Transformación Mental y Paradigmática', 'Internalizar el cambio del modelo industrial al modelo "360"', 'Comprender la neuroeducación y cómo el cerebro aprende en entornos digitales', 20.00),
	(2, 1, 2, 'Dominio de Ecosistemas Tecnológicos', 'Integrar herramientas de Inteligencia Artificial, Realidad Aumentada y entornos virtuales en el aula', 'Pasar de ser un consumidor de tecnología a un creador de recursos educativos disruptivos', 30.00),
	(3, 1, 3, 'Diseño Pedagógico Innovador (ABP)', 'Implementar el Aprendizaje Basado en Proyectos con impacto social', 'Crear currículos flexibles que fomenten el pensamiento crítico y la resolución de problemas reales', 25.00),
	(4, 1, 4, 'Liderazgo y Humanismo Digital', 'Desarrollar habilidades blandas (soft skills) y ética en el uso de datos', 'Ejercer un rol de mentoría que priorice el bienestar emocional del estudiante', 25.00);

-- Volcando estructura para tabla edu360-staging.audit_log_inquisidor
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla edu360-staging.audit_log_inquisidor: ~1 rows (aproximadamente)
DELETE FROM `audit_log_inquisidor`;
INSERT INTO `audit_log_inquisidor` (`id_auditoria`, `id_artefacto`, `id_artefacto_meta`, `id_evolucionador`, `score_rigor`, `veredicto`, `friccion_detectada`, `udv_otorgadas`, `auditado_at`) VALUES
	(1, 1, 1, 2, 0.00, 'En Desarrollo', NULL, 0.00, '2026-01-19 17:32:29');

-- Volcando estructura para tabla edu360-staging.chat_auditoria_log
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla edu360-staging.chat_auditoria_log: ~6 rows (aproximadamente)
DELETE FROM `chat_auditoria_log`;
INSERT INTO `chat_auditoria_log` (`id`, `id_evolucionador`, `id_artefacto`, `id_artefacto_meta`, `role`, `content`, `created_at`) VALUES
	(1, 2, 1, 1, 'user', 'prueba', '2026-01-19 14:41:06'),
	(2, 2, 1, 1, 'assistant', 'Evolucionador, para proceder con la acuñación de UDVs en esta meta, es fundamental que tu Legado Cognitivo refleje una comprensión profunda del objetivo. La respuesta \'prueba\' es genérica y no evidencia la internalización de cómo el cerebro aprende en entornos digitales, ni el cambio del modelo industrial al \'360\'. ¿Podrías expandir tu SRAA y demostrar tu Soberanía Intelectual sobre la neuroeducación en contextos digitales, según el objetivo actual?', '2026-01-19 14:41:06'),
	(3, 2, 1, 1, 'user', 'con este metodo el evolucionador podra obtener un titulo demostrando que sabe de la materia', '2026-01-19 17:27:40'),
	(4, 2, 1, 1, 'assistant', 'Evolucionador, tu SRAA actual menciona un beneficio general de la titulación, pero no profundiza en el Legado Cognitivo específico que buscamos acuñar. El objetivo es comprender la neuroeducación y cómo el cerebro aprende en entornos digitales. Para la acuñación de UDVs, necesito evidencias de tu Soberanía Intelectual sobre estos conceptos fundamentales del paradigma EDU360. ¿Podrías explicitar cómo el modelo 360, a través de la neuroeducación, facilita el aprendizaje cerebral en el contexto digital, diferenciándolo del modelo industrial?', '2026-01-19 17:27:40'),
	(5, 2, 1, 1, 'user', 'edu360 te da la oportunidad de estudiar y demostrar que sabes de neuroeducacion', '2026-01-19 17:32:29'),
	(6, 2, 1, 1, 'assistant', 'Evolucionador, tu SRAA actual se limita a una descripción general de lo que EDU360 ofrece, sin profundizar en el Legado Cognitivo específico que buscamos acuñar. El objetivo es comprender la neuroeducación y cómo el cerebro aprende en entornos digitales, así como el contraste con el modelo industrial. Para la acuñación de UDVs, necesito evidencias de tu Soberanía Intelectual sobre estos conceptos fundamentales del paradigma EDU360. ¿Podrías explicar *cómo* la neuroeducación facilita el aprendizaje cerebral en el contexto digital, y cómo esto se diferencia del enfoque del modelo industrial en el marco del modelo 360?', '2026-01-19 17:32:29');

-- Volcando estructura para tabla edu360-staging.evolucionadores
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla edu360-staging.evolucionadores: ~1 rows (aproximadamente)
DELETE FROM `evolucionadores`;
INSERT INTO `evolucionadores` (`id_evolucionador`, `hash_identidad`, `nombre_completo`, `email_verificado`, `estatus_soberania`, `total_udv_acumuladas`, `creado_at`, `password`, `foto`, `verificado`) VALUES
	(2, '0x267212A4267212A4267212A4267212A4', 'Daniel Alfonsi', 'alfonsi.acosta@gmail.com', 'Activo', 3.00, '2026-01-14 20:43:17', '1nyIl0IE7DUUGPRFtGMSGcxrY4OTM/v76NaBf80rtck=', 'perfil_1768686193.jpg', 1);

-- Volcando estructura para tabla edu360-staging.nodos_activos
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla edu360-staging.nodos_activos: ~1 rows (aproximadamente)
DELETE FROM `nodos_activos`;
INSERT INTO `nodos_activos` (`id`, `id_evolucionador`, `stripe_session_id`, `monto`, `estatus`, `fecha_activacion`, `fecha_expiracion`, `id_artefacto`) VALUES
	(4, 2, 'cs_test_a1W5SeRd4O947GaphMrG2Ieif13iWf2nW83pS24TJmRIosPlwB5FDXgt4o', 20.00, 'Activado', '2026-01-17 18:13:15', NULL, 1);

-- Volcando estructura para tabla edu360-staging.nodos_fundacionales
CREATE TABLE IF NOT EXISTS `nodos_fundacionales` (
  `id_nodo` int(11) NOT NULL AUTO_INCREMENT,
  `id_titular` int(11) NOT NULL,
  `jurisdiccion` varchar(100) DEFAULT 'Florida, USA',
  `hash_contrato_apf` varchar(255) NOT NULL,
  `fecha_activacion` datetime NOT NULL,
  PRIMARY KEY (`id_nodo`),
  KEY `fk_nodo_titular` (`id_titular`),
  CONSTRAINT `fk_nodo_titular` FOREIGN KEY (`id_titular`) REFERENCES `evolucionadores` (`id_evolucionador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla edu360-staging.nodos_fundacionales: ~0 rows (aproximadamente)
DELETE FROM `nodos_fundacionales`;

-- Volcando estructura para tabla edu360-staging.staging
CREATE TABLE IF NOT EXISTS `staging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `nivel` enum('Administrador','Tecnico','Usuario') NOT NULL DEFAULT 'Tecnico',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla edu360-staging.staging: ~2 rows (aproximadamente)
DELETE FROM `staging`;
INSERT INTO `staging` (`id`, `usuario`, `password`, `nivel`) VALUES
	(1, 'alfonsi.acosta@gmail.com', 'f/t8tUSW+70FTNk9E2+PxUEPr8v0qgIV7d5Ofl419pY=', 'Tecnico'),
	(2, 'likomanuel1975@gmail.com', 'fayfZreOxnXXGDWNBXVJf/H51EING4wtAZ1fvu9EaCU=', 'Tecnico');

-- Volcando estructura para disparador edu360-staging.tr_actualizar_udv_soberania
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER tr_actualizar_udv_soberania
AFTER INSERT ON audit_log_inquisidor
FOR EACH ROW
BEGIN
    IF NEW.veredicto = 'Acuñado' THEN
        UPDATE evolucionadores
        SET total_udv_acumuladas = total_udv_acumuladas + NEW.udv_otorgadas
        WHERE id_evolucionador = (SELECT id_evolucionador FROM artefactos_dominio WHERE id_artefacto = NEW.id_artefacto);
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
