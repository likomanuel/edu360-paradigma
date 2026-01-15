<?php

namespace App\Controllers;

class NeuroEducacionController
{
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
}