<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

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
        // Usamos HOST para asegurar una URL absoluta (necesaria para Stripe)
        $base = rtrim($_ENV['HOST'] ?? $_ENV['BASE_URL'], '/');
        
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('js')) {
    function js($path) {
        return base_url('public/assets/js/' . ltrim($path, '/'));
    }
}

if (!function_exists('css')) {
    function css($path) {
        return base_url('public/assets/css/' . ltrim($path, '/'));
    }
}

if (!function_exists('vendor')) {
    function vendor($path) {
        // En este proyecto parece que los vendors están directamente en assets o en una carpeta específica
        // Basándonos en el uso en footer.php: vendor('libs/jquery/jquery.js')
        return base_url('vendor/' . ltrim($path, '/'));
    }
}

if (!function_exists('img')) {
    function img($path) {
        return base_url('public/assets/img/' . ltrim($path, '/'));
    }
}

if (!function_exists('fonts')) {
    function fonts($path) {
        return base_url('public/assets/fonts/' . ltrim($path, '/'));
    }
}

if (!function_exists('modulo')) {
    function modulo() {
        return $_ENV['MODULO'];
    }
}

if (!function_exists('host')) {
    function host() {
        return $_ENV['HOST'];
    }
}

if (!function_exists('public_path')) {
    /**
     * Devuelve la ruta absoluta al directorio public.
     */
    function public_path($path = '') {
        return dirname(__DIR__) . '/public/' . ltrim($path, '/');
    }
}

if (!function_exists('img_path')) {
    /**
     * Devuelve la ruta absoluta a una imagen.
     */
    function img_path($path) {
        return public_path('assets/img/' . ltrim($path, '/'));
    }
}

if (!function_exists('fonts_path')) {
    /**
     * Devuelve la ruta absoluta a una fuente.
     */
    function fonts_path($path) {
        return public_path('assets/fonts/' . ltrim($path, '/'));
    }
}