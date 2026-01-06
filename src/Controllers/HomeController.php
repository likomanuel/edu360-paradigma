<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        $titulo = "EDU360 - Inicio";
        require_once __DIR__ . '/../../views/home/index.php';
    }   
}
