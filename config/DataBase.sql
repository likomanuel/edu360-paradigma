-- -----------------------------------------------------
-- Arquitectura de Datos: Paradigma EDU360
-- Versión: 10.7 (Rigor Federal / Soberanía Cognitiva)
-- Fecha: 06/01/2026
-- -----------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. TABLA: Evolucionadores (Sujetos de Soberanía)
CREATE TABLE IF NOT EXISTS evolucionadores (
  id_evolucionador INT(11) NOT NULL AUTO_INCREMENT,
  hash_identidad VARCHAR(255) NOT NULL,
  nombre_completo VARCHAR(150) NOT NULL,
  email_verificado VARCHAR(100) NOT NULL,
  estatus_soberania ENUM('Activo', 'En Consolidación', 'Suspendido') DEFAULT 'Activo',
  total_udv_acumuladas DECIMAL(10,2) DEFAULT '0.00',
  creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_evolucionador),
  UNIQUE KEY email_verificado (email_verificado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABLA: Nodos Fundacionales (Infraestructura de Élite)
CREATE TABLE IF NOT EXISTS nodos_fundacionales (
  id_nodo INT(11) NOT NULL AUTO_INCREMENT,
  id_titular INT(11) NOT NULL,
  jurisdiccion VARCHAR(100) DEFAULT 'Florida, USA',
  hash_contrato_apf VARCHAR(255) NOT NULL,
  fecha_activacion DATETIME NOT NULL,
  PRIMARY KEY (id_nodo),
  KEY fk_nodo_titular (id_titular),
  CONSTRAINT fk_nodo_titular FOREIGN KEY (id_titular) REFERENCES evolucionadores (id_evolucionador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABLA: Artefactos de Dominio (Evidencia de Legado)
CREATE TABLE IF NOT EXISTS artefactos_dominio (
  id_artefacto INT(11) NOT NULL AUTO_INCREMENT,
  id_evolucionador INT(11) NOT NULL,
  ruta_conocimiento VARCHAR(150) NOT NULL,
  url_vault VARCHAR(255) NOT NULL,
  timestamp_entrega TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_artefacto),
  KEY fk_artefacto_evolucionador (id_evolucionador),
  CONSTRAINT fk_artefacto_evolucionador FOREIGN KEY (id_evolucionador) REFERENCES evolucionadores (id_evolucionador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. TABLA: Audit Log Inquisidor (Rigor Algorítmico)
-- IMPORTANTE: Esta tabla es la fuente de verdad de las UDVs
CREATE TABLE IF NOT EXISTS audit_log_inquisidor (
  id_auditoria INT(11) NOT NULL AUTO_INCREMENT,
  id_artefacto INT(11) NOT NULL,
  score_rigor DECIMAL(5,2) NOT NULL,
  veredicto ENUM('Acuñado', 'En Desarrollo') NOT NULL,
  friccion_detectada TEXT DEFAULT NULL,
  udv_otorgadas DECIMAL(10,2) DEFAULT '0.00',
  auditado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_auditoria),
  KEY fk_audit_artefacto (id_artefacto),
  CONSTRAINT fk_audit_artefacto FOREIGN KEY (id_artefacto) REFERENCES artefactos_dominio (id_artefacto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. TRIGGER DE BLINDAJE: Actualización automática de UDVs
-- Evita la edición manual. Solo el éxito del Inquisidor suma créditos.
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
END //
DELIMITER ;

COMMIT;