<?php

namespace App\Controllers;

use Database;
class NeuroEducacionController
{
    private $db;
    public function __construct()
    {
        $this->db = new Database();
    }
    public function pagos()
    {
        $titulo = "EDU360 - NeuroEducacion";
        require_once __DIR__ . '/../../views/neuroeducacion/pagos.php';
    }   
    public function neuroeducacion()
    {
        $titulo = "EDU360 - NeuroEducacion";
        require_once __DIR__ . '/../../views/neuroeducacion/neuroeducacion.php';
    }

    public function artefactoActivo($id_evolucionador)
    {
        $artefacto = $this->nodoActivo($id_evolucionador)['id_artefacto'];
        $artefactoActivo = $this->db->row_sqlconector("SELECT * FROM artefactos_dominio WHERE id_artefacto = $artefacto");
        return $artefactoActivo;
    }
    public function nodoActivo($id_evolucionador)
    {
        $nodoActivo = $this->db->row_sqlconector("SELECT * FROM nodos_activos WHERE estatus = 'Activado' AND id_evolucionador = $id_evolucionador");
        return $nodoActivo;
    }

    public function logrosEvolucionador($id_evolucionador): array
    {
        $sql = "SELECT n.*, a.nombre, a.nivel_trayectoria 
                FROM nodos_activos n 
                JOIN artefactos_dominio a ON n.id_artefacto = a.id_artefacto 
                WHERE n.id_evolucionador = $id_evolucionador";
        return $this->db->array_sqlconector($sql);
    }

    public function misEvoluciones($id_evolucionador): array
    {
        // Obtener el artefacto del nodo activo actual
        $sqlNodo = "SELECT id_artefacto FROM nodos_activos WHERE id_evolucionador = $id_evolucionador AND estatus = 'Activado' LIMIT 1";
        $nodo = $this->db->row_sqlconector($sqlNodo);
        
        if (!$nodo) return [];

        $id_artefacto = $nodo['id_artefacto'];

        // Obtener todas las metas de este artefacto
        $sqlMetas = "SELECT * FROM artefactos_metas WHERE id_artefacto = $id_artefacto ORDER BY position ASC";
        $metas = $this->db->array_sqlconector($sqlMetas);

        // Obtener los logs de auditoria para este evolucionador y este artefacto
        $sqlAudit = "SELECT * FROM audit_log_inquisidor WHERE id_evolucionador = $id_evolucionador AND id_artefacto = $id_artefacto";
        $audits = $this->db->array_sqlconector($sqlAudit);

        // Mapear auditorias por id_artefacto_meta
        $auditMap = [];
        foreach ($audits as $audit) {
            $auditMap[$audit['id_artefacto_meta']] = $audit;
        }

        $results = [];
        $foundCurrent = false;

        foreach ($metas as $meta) {
            $metaId = $meta['id'];
            $audit = $auditMap[$metaId] ?? null;

            $status = 'Bloqueado';
            $progress = 0;
            $veredicto = 'En Desarrollo';

            if ($audit) {
                $veredicto = $audit['veredicto'];
                $udv = $audit['udv_otorgadas'];
                $target = $meta['valor_udv'];
                
                $progress = ($target > 0) ? min(100, ($udv / $target) * 100) : 0;
                $status = ($veredicto === 'Acuñado') ? 'Culminado' : 'En Proceso';
                
                if ($status === 'En Proceso') $foundCurrent = true;
            } else {
                // Si no hay auditoría y no hemos encontrado la actual, la primera sin auditoría es la actual
                if (!$foundCurrent) {
                    $status = 'En Proceso';
                    $progress = 0;
                    $foundCurrent = true;

                    // PERSISTENCIA: Crear el registro inicial en la DB para que ya exista en auditoría
                    $sqlInsert = "INSERT INTO audit_log_inquisidor (id_artefacto, id_artefacto_meta, id_evolucionador, score_rigor, veredicto, udv_otorgadas) 
                                  VALUES ($id_artefacto, $metaId, $id_evolucionador, 0, 'En Desarrollo', 0)";
                    $this->db->sqlconector($sqlInsert);
                }
            }

            $results[] = [
                'meta' => $meta['meta'],
                'descripcion' => $meta['descripcion'],
                'objetivo' => $meta['objetivo'],
                'status' => $status,
                'progress' => $progress,
                'veredicto' => $veredicto
            ];
        }

        return $results;
    }
}