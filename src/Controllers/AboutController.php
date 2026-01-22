<?php

namespace App\Controllers;

class AboutController
{
    public function quienesSomos()
    {
        $titulo = "EDU360 - ¿Quiénes Somos?";
        require_once __DIR__ . '/../../views/about/quienes_somos.php';
    }

    public function queHacemos()
    {
        $titulo = "EDU360 - ¿Qué Hacemos?";
        require_once __DIR__ . '/../../views/about/que_hacemos.php';
    }

    public function comoLoHacemos()
    {
        $titulo = "EDU360 - ¿Cómo lo Hacemos?";
        require_once __DIR__ . '/../../views/about/como_lo_hacemos.php';
    }

    public function contacto()
    {
        $titulo = "EDU360 - Contáctanos";
        require_once __DIR__ . '/../../views/about/contacto.php';
    }
}
