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
  `id_evolucionador` int(11) NOT NULL,
  `ruta_conocimiento` varchar(150) NOT NULL,
  `url_vault` varchar(255) NOT NULL,
  `timestamp_entrega` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_artefacto`),
  KEY `fk_artefacto_evolucionador` (`id_evolucionador`),
  CONSTRAINT `fk_artefacto_evolucionador` FOREIGN KEY (`id_evolucionador`) REFERENCES `evolucionadores` (`id_evolucionador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla edu360-staging.audit_log_inquisidor
CREATE TABLE IF NOT EXISTS `audit_log_inquisidor` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `id_artefacto` int(11) NOT NULL,
  `score_rigor` decimal(5,2) NOT NULL,
  `veredicto` enum('Acuñado','En Desarrollo') NOT NULL,
  `friccion_detectada` text DEFAULT NULL,
  `udv_otorgadas` decimal(10,2) DEFAULT 0.00,
  `auditado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_auditoria`),
  KEY `fk_audit_artefacto` (`id_artefacto`),
  CONSTRAINT `fk_audit_artefacto` FOREIGN KEY (`id_artefacto`) REFERENCES `artefactos_dominio` (`id_artefacto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla edu360-staging.nodos_activos
CREATE TABLE IF NOT EXISTS `nodos_activos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_evolucionador` int(11) NOT NULL,
  `stripe_session_id` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estatus` varchar(50) NOT NULL DEFAULT 'Activado',
  `fecha_activacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session` (`stripe_session_id`),
  KEY `idx_evolucionador` (`id_evolucionador`),
  CONSTRAINT `fk_nodos_evolucionador` FOREIGN KEY (`id_evolucionador`) REFERENCES `evolucionadores` (`id_evolucionador`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla edu360-staging.staging
CREATE TABLE IF NOT EXISTS `staging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `nivel` enum('Administrador','Tecnico','Usuario') NOT NULL DEFAULT 'Tecnico',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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
