<?php
setcookie("modulo", $_ENV['MODULO'], time() + 3600, "/");
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>EDU360 · The Paradigm</title>
  <meta name="description"
    content="Creado por el Dr. Manuel Aguilera, PhD. El primer ecosistema educativo digital avanzado para el mundo hispano.">

    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <title><?php echo PROJECT_NAME; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo img('favicon/favicon.ico'); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo vendor('fonts/remixicon/remixicon.css'); ?>" />
    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css -->
    <link rel="stylesheet" href="<?php echo css('style.css'); ?>" />
    <link rel="stylesheet" href="<?php echo css('sweetalert2.css'); ?>" />
    <!-- endbuild -->
    <script src="<?php echo js('jquery-3.5.1.js'); ?>"></script>
    <script src="<?php echo js('sweetalert2.js'); ?>"></script>
    <script async src="https://js.stripe.com/v3/buy-button.js"></script>
</head>

<body>
<header>
        <div class="logo">
            <img src="<?= base_url('/public/assets/img/logo_paradigma_removebg.png') ?>" alt="Logo" width="50" height="40"> 
            <?php if(isset($_COOKIE['modulo']) && $_COOKIE['modulo'] == 'staging') { ?>
                <span style="color: #FF0000;">Paradigma EDU360 Staging</span>
            <?php } else { ?>
                <span>Paradigma EDU360</span>
            <?php } ?>
        </div>
        <nav>
            <ul>
                <li><a href="#">Programas</a></li>
                <li><a href="#">IA & Neuroeducación</a></li>
                <li><a href="<?php 
                if(isset($_SESSION['staging']) && $_SESSION['staging'] == true) {
                    echo base_url('/closeSession');
                }
                else{
                    if(isset($_COOKIE['modulo']) && $_COOKIE['modulo'] == 'staging') {
                        echo base_url('/staging');
                    } else {
                        echo base_url('/session');
                    }
                }?>"
                >
                <?php if(isset($_SESSION['staging']) && $_SESSION['staging'] == true) { ?>
                    <i class="fas fa-user-astronaut"></i> Cerrar Sesión
                <?php } else { ?>
                    <i class="fas fa-user"></i> Iniciar Sesión
                <?php } ?></a></li>
                <li><a href="#">Contacto</a></li>
            </ul>
        </nav>
        <div class="search-icon">
            <i class="fas fa-search"></i>
        </div>
    </header>    
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">