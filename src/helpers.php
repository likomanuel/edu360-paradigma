<?php
if (!defined('PROJECT_NAME')) {
    define('PROJECT_NAME', 'EDU360 - Paradigma');
}


if (!function_exists('base_url')) {
    /**
     * Devuelve la URL base del proyecto.
     * 
     * @param string $path Ruta adicional para anexar a la URL base.
     * @return string URL completa.
     */
    function base_url($path = '') {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        
        // Ajustamos la carpeta base según el proyecto
        $base_folder = '/edu360-paradigma'; 
        
        return $protocol . $domainName . $base_folder . '/' . ltrim($path, '/');
    }
}

if (!function_exists('js')) {
    function js($path) {
        return base_url('assets/js/' . ltrim($path, '/'));
    }
}

if (!function_exists('css')) {
    function css($path) {
        return base_url('assets/css/' . ltrim($path, '/'));
    }
}

if (!function_exists('vendor')) {
    function vendor($path) {
        // En este proyecto parece que los vendors están directamente en assets o en una carpeta específica
        // Basándonos en el uso en footer.php: vendor('libs/jquery/jquery.js')
        return base_url('assets/vendor/' . ltrim($path, '/'));
    }
}

if (!function_exists('img')) {
    function img($path) {
        return base_url('assets/img/' . ltrim($path, '/'));
    }
}
