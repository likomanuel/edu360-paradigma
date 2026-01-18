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
}