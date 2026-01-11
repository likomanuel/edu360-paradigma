<?php

namespace App\Controllers;

class SessionController
{
    public function session()
    {
        require_once __DIR__ . '/../../views/session/session.php';
    }   

    public function closeSession()
    {
        session_start();
        if(isset($_SESSION['staging']) && $_SESSION['staging'] == true) {
            $_SESSION['staging'] = false;
        }
        //setcookie("modulo", "", time() - 3600, "/");        
        session_destroy();
        header('Location: ' . base_url('/'));
        exit;
    }
}