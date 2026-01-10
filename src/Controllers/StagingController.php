<?php

namespace App\Controllers;

class StagingController
{
    public function staging()
    {
        require_once __DIR__ . '/../../views/session/staging.php';
    }   
}