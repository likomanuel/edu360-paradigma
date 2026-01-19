<?php
namespace App\Controllers;

class PanelController
{
    public function index()
    {
        require_once __DIR__ . '/../../views/mipanel/index.php';
    }

    public function aula()
    {
        require_once __DIR__ . '/../../views/mipanel/aula.php';
    }
}
