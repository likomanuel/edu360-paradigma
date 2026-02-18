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
        $nodo = $this->nodoActivo($id_evolucionador);
        if (!$nodo || !isset($nodo['id_artefacto'])) {
            // Si no hay nodo activo, intentar buscar el último culminado o usar uno por defecto ID 1
            $sqlLast = "SELECT id_artefacto FROM nodos_activos WHERE id_evolucionador = $id_evolucionador ORDER BY id DESC LIMIT 1";
            $lastNodo = $this->db->row_sqlconector($sqlLast);
            $id_artefacto = $lastNodo['id_artefacto'] ?? 1;
        } else {
            $id_artefacto = $nodo['id_artefacto'];
        }

        $artefactoActivo = $this->db->row_sqlconector("SELECT * FROM artefactos_dominio WHERE id_artefacto = $id_artefacto");
        return $artefactoActivo ?: ['nombre' => 'EDU360'];
    }

    public function nodoActivo($id_evolucionador)
    {
        $nodoActivo = $this->db->row_sqlconector("SELECT * FROM nodos_activos WHERE estatus = 'Activado' AND id_evolucionador = $id_evolucionador LIMIT 1");
        return $nodoActivo ?: null;
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
                'veredicto' => $veredicto,
                'udv_otorgadas' => $audit ? $audit['udv_otorgadas'] : 0,
                'valor_udv' => $meta['valor_udv']
            ];
        }

        return $results;
    }

    public function procesarAuditoria($id_evolucionador, $mensajeUsuario)
    {
        $gemini = new \App\Services\GeminiService();
        
        // 1. Obtener contexto de la meta activa
        $sqlNodo = "SELECT id_artefacto FROM nodos_activos WHERE id_evolucionador = $id_evolucionador AND estatus = 'Activado' LIMIT 1";
        $nodo = $this->db->row_sqlconector($sqlNodo);
        if (!$nodo) return ['error' => 'No hay nodo activo'];
        $id_artefacto = $nodo['id_artefacto'];

        $sqlMetas = "SELECT * FROM artefactos_metas WHERE id_artefacto = $id_artefacto ORDER BY position ASC";
        $metas = $this->db->array_sqlconector($sqlMetas);

        $sqlAudit = "SELECT * FROM audit_log_inquisidor WHERE id_evolucionador = $id_evolucionador AND id_artefacto = $id_artefacto";
        $audits = $this->db->array_sqlconector($sqlAudit);
        $auditMap = [];
        foreach ($audits as $a) $auditMap[$a['id_artefacto_meta']] = $a;

        $metaActual = null;
        $id_meta = null;
        foreach ($metas as $m) {
            $a = $auditMap[$m['id']] ?? null;
            if (!$a || $a['veredicto'] !== 'Acuñado') {
                $metaActual = $m;
                $metaActual['udv_otorgadas'] = $a ? $a['udv_otorgadas'] : 0;
                $id_meta = $m['id'];
                break;
            }
        }

        if (!$metaActual) return ['error' => 'No hay metas pendientes'];

        // 2. Obtener historial de chat
        $historial = $this->getHistorialChat($id_evolucionador, $id_artefacto, $id_meta);

        // 3. Llamar a Gemini
        try {
            $respuestaIA = $gemini->auditarRespuesta($metaActual, $mensajeUsuario, $historial);
        } catch (\Exception $e) {
            return ['error' => 'Error en la IA: ' . $e->getMessage()];
        }

        if (isset($respuestaIA['error'])) return $respuestaIA;

        // 4. Guardar mensajes en el log
        $this->db->sqlconector("INSERT INTO chat_auditoria_log (id_evolucionador, id_artefacto, id_artefacto_meta, role, content) VALUES ($id_evolucionador, $id_artefacto, $id_meta, 'user', '" . addslashes($mensajeUsuario) . "')");
        $this->db->sqlconector("INSERT INTO chat_auditoria_log (id_evolucionador, id_artefacto, id_artefacto_meta, role, content) VALUES ($id_evolucionador, $id_artefacto, $id_meta, 'assistant', '" . addslashes($respuestaIA['mensaje'] ?? '') . "')");

        // 5. Actualizar audit_log_inquisidor
        $nuevasUDV = (float)($respuestaIA['udv_otorgadas'] ?? 0);
        $udvTotales = (float)$metaActual['udv_otorgadas'] + $nuevasUDV;
        $veredicto = $respuestaIA['veredicto'] ?? 'En Desarrollo';

        if ($udvTotales >= $metaActual['valor_udv']) {
            $veredicto = 'Acuñado';
            $udvTotales = $metaActual['valor_udv'];
        }

        $this->db->sqlconector("UPDATE audit_log_inquisidor SET udv_otorgadas = $udvTotales, veredicto = '$veredicto', auditado_at = CURRENT_TIMESTAMP WHERE id_evolucionador = $id_evolucionador AND id_artefacto = $id_artefacto AND id_artefacto_meta = $id_meta");

        // 6. Sincronización de UDVs con la tabla evolucionadores (Solo si está Acuñado y no se ha sumado antes)
        if ($veredicto === 'Acuñado') {
            $sqlCheck = "SELECT sumado FROM audit_log_inquisidor 
                        WHERE id_evolucionador = $id_evolucionador 
                        AND id_artefacto = $id_artefacto 
                        AND id_artefacto_meta = $id_meta";
            $checkSum = $this->db->row_sqlconector($sqlCheck);

            if ($checkSum && (int)$checkSum['sumado'] === 0) {
                // Sumamos al total acumulado del evolucionador
                $this->db->sqlconector("UPDATE evolucionadores 
                                       SET total_udv_acumuladas = total_udv_acumuladas + $udvTotales 
                                       WHERE id_evolucionador = $id_evolucionador");
                
                // Marcamos como sumado para evitar redundancia
                $this->db->sqlconector("UPDATE audit_log_inquisidor 
                                       SET sumado = 1 
                                       WHERE id_evolucionador = $id_evolucionador 
                                       AND id_artefacto = $id_artefacto 
                                       AND id_artefacto_meta = $id_meta");
            }
        }

        return [
            'success' => true,
            'mensaje' => $respuestaIA['mensaje'] ?? 'Sin respuesta',
            'udv_otorgadas' => $nuevasUDV,
            'udv_totales' => $udvTotales,
            'veredicto' => $veredicto,
            'progresion' => ($veredicto === 'Acuñado')
        ];
    }

    private function getHistorialChat($id_evolucionador, $id_artefacto, $id_meta)
    {
        // Usamos una ventana deslizante de los últimos 15 mensajes para no sobrecargar el prompt
        // y mantener la relevancia de la conversación inmediata.
        $sql = "SELECT role, content FROM chat_auditoria_log 
                WHERE id_evolucionador = $id_evolucionador 
                AND id_artefacto = $id_artefacto 
                AND id_artefacto_meta = $id_meta 
                ORDER BY created_at DESC LIMIT 15";
        $historial = $this->db->array_sqlconector($sql) ?? [];
        
        // Revertimos para que la IA los reciba en orden cronológico (viejo -> nuevo)
        return array_reverse($historial);
    }
}