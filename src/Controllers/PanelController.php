<?php
namespace App\Controllers;

class PanelController
{
    public function index()
    {
        require_once __DIR__ . '/../../views/mipanel/index.php';
    }
}
