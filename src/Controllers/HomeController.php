<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        $modulo = new \Modulo();
        $db = $modulo->getDb();
        
        $result = $db->row_sqlconector("SELECT COUNT(tipo_nodo) AS sum_tipo_nodo FROM nodos_activos WHERE tipo_nodo = 'Beta' AND estatus='Activado'");
        $sum_tipo_nodo = (int)($result['sum_tipo_nodo'] ?? 0);
        
        $titulo = "EDU360 - Inicio";
        require_once __DIR__ . '/../../views/home/index.php';
    }   
}
