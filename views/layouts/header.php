<?php
setcookie("modulo", $_ENV['MODULO'], time() + 3600, "/");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <link rel="icon" type="image/x-icon" href="<?php echo img('favicon/university/favicon.ico'); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo vendor('fonts/remixicon/remixicon.css'); ?>" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css -->
    <link rel="stylesheet" href="<?php echo css('style.css'); ?>" />
    <link rel="stylesheet" href="<?php echo css('sweetalert2.css'); ?>" />
    <!-- endbuild -->
    <script src="<?php echo js('jquery-3.5.1.js'); ?>"></script>
    <script src="<?php echo js('sweetalert2.js'); ?>"></script>
</head>

<body>
<header>
        <div class="logo" style="cursor: pointer;" onclick="window.location.href='<?php echo base_url('/'); ?>'">
            <img src="<?php echo img('favicon/university/android-chrome-192x192.png') ?>" alt="Logo" width="40" height="40"> 
            <?php if(isset($_COOKIE['modulo']) && $_COOKIE['modulo'] == 'staging') { ?>
                <span style="color: #FF0000;">Paradigma EDU360 Staging</span>
            <?php } else { ?>
                <span>Paradigma EDU360</span>
            <?php } ?>
        </div>
        <nav>
            <ul>                
                <li><a href="<?php 
                if(isset($_SESSION['email'])) {
                    echo base_url('/mipanel');
                }
                else{
                    echo base_url('/registro'); 
                }
                ?>"
                >
                <?php if(isset($_SESSION['email'])) { ?>                    
                    <i class="fas fa-user-astronaut"></i> Mi Panel
                <?php } else { ?>
                    <i class="fas fa-user"></i> Registrarse
                <?php } ?></a></li>
                <li><a href="<?php 
                if(isset($_SESSION['email'])) {
                    echo base_url('/closeSession');
                }
                else{
                    if(isset($_COOKIE['modulo']) && $_COOKIE['modulo'] == 'staging') {
                        echo base_url('/debug');
                    } else {
                        echo base_url('/session');
                    }
                }?>"
                >
                <?php if(isset($_SESSION['email'])) { ?>
                    <i class="fas fa-user-astronaut"></i> Cerrar Sesión
                <?php } else { ?>
                    <i class="fas fa-user"></i> Iniciar Sesión
                <?php } ?></a></li>                
            </ul>
        </nav>
        <div class="search-icon">
            <i class="fas fa-search"></i>
        </div>
    </header>    
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">