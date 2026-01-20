<?php
namespace App\Controllers;

use Database;
use Modulo;

class PanelController
{
    private $db;
    private $modulo;

    public function __construct()
    {
        $this->db = new Database();
        $this->modulo = new Modulo();
    }

    public function index()
    {
        if (isset($_SESSION['email'])) {
            $user = $this->modulo->getUser($_SESSION['email']);
            if ($user) {
                $this->checkAndGenerateCertificate($user);
            }
        }
        require_once __DIR__ . '/../../views/mipanel/index.php';
    }

    private function checkAndGenerateCertificate($user)
    {
        $id_evolucionador = $user['id_evolucionador'];
        
        // 1. Obtener el nodo activo actual
        $sqlNodo = "SELECT id, id_artefacto FROM nodos_activos WHERE id_evolucionador = $id_evolucionador AND estatus = 'Activado' LIMIT 1";
        $nodo = $this->db->row_sqlconector($sqlNodo);
        
        if (!$nodo) return;

        $id_nodo = $nodo['id'];
        $id_artefacto = $nodo['id_artefacto'];

        // 2. Obtener la densidad cognitiva del artefacto
        $sqlArtefacto = "SELECT nombre, densidad_cognitiva_udv FROM artefactos_dominio WHERE id_artefacto = $id_artefacto";
        $artefacto = $this->db->row_sqlconector($sqlArtefacto);
        
        if (!$artefacto) return;

        $densidad_requerida = (float)$artefacto['densidad_cognitiva_udv'];

        // 3. Sumar UDVs otorgadas en audit_log_inquisidor para este artefacto
        $sqlSumUDV = "SELECT SUM(udv_otorgadas) as total_udv FROM audit_log_inquisidor WHERE id_evolucionador = $id_evolucionador AND id_artefacto = $id_artefacto";
        $resultSum = $this->db->row_sqlconector($sqlSumUDV);
        $total_udv = (float)($resultSum['total_udv'] ?? 0);

        // 4. Verificar si se alcanzó la densidad
        if ($total_udv >= $densidad_requerida) {
            // Generar certificado
            require_once __DIR__ . '/../../public/generar_certificado.php';
            
            $nombreAlumno = $user['nombre_completo'];
            $emailAlumno = $user['email_verificado'];
            $hashUser = $user['hash_identidad'];
            $nombreCertificado = "Certificado_" . $id_artefacto . "_" . time();
            
            // Ruta específica del usuario: public/users/{hash}/certificados/
            $rutaDestino = __DIR__ . "/../../public/users/{$hashUser}/certificados/{$nombreCertificado}.png";
            
            // Llamar a la función de generación (necesitaremos ajustar generar_certificado.php para aceptar ruta)
            if (function_exists('generarCertificadoEdu360')) {
                generarCertificadoEdu360($nombreAlumno, $nombreCertificado, $emailAlumno, $rutaDestino);
                
                // 5. Culminar el diplomado (Cerrar el nodo)
                $sqlUpdate = "UPDATE nodos_activos SET estatus = 'Cerrado' WHERE id = $id_nodo";
                $this->db->sqlconector($sqlUpdate);
            }
        }
    }

    public function aula()
    {
        require_once __DIR__ . '/../../views/mipanel/aula.php';
    }
}
