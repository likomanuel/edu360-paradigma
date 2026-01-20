<?php
session_start();
// Cargar el autoloader de Composer para que tus clases funcionen
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar la clase Database y Modulo
require_once __DIR__ . '/../config/DataBase.php';
require_once __DIR__ . '/../config/modulo.php';

// Cargar funciones auxiliares (como base_url)
require_once __DIR__ . '/../src/helpers.php';

// 1. Obtenemos la URI
$request = $_SERVER['REQUEST_URI'];

// 2. Definimos la carpeta base (donde está tu index.php)
// Esto elimina el prefijo de la subcarpeta si el proyecto no está en la raíz
$base_path = $_ENV['BASE_URL'] ?? '/edu360-paradigma';
$route = str_replace($base_path, '', $request);

// 3. (Opcional) Eliminar parámetros GET si los hubiera (ej: ?id=1)
$route = explode('?', $route)[0];

// Asegurarse de que la ruta comience con / y no termine con / si no es la raíz
if ($route == '') $route = '/';
if ($route != '/' && str_ends_with($route, '/')) $route = rtrim($route, '/');

switch ($route) {
    case '/':
    case '/index':        
        $controller = new App\Controllers\HomeController(); 
        $controller->index();        
        break;

    case '/staging':
        $controller = new App\Controllers\StagingController(); 
        $controller->staging();        
        break;

    case '/session':
        $controller = new App\Controllers\SessionController(); 
        $controller->session();        
        break;

    case '/registro':
        $controller = new App\Controllers\SessionController(); 
        $controller->registro();        
        break;

    case '/closeSession':
        $controller = new App\Controllers\SessionController(); 
        $controller->closeSession();        
        break;

    case '/mipanel':
        $controller = new App\Controllers\PanelController(); 
        $controller->index();        
        break;

    case '/neuroeducacion':
        $controller = new App\Controllers\NeuroEducacionController(); 
        $controller->neuroeducacion();        
        break;

    case '/pagos':
        $controller = new App\Controllers\NeuroEducacionController(); 
        $controller->pagos();        
        break;

    case '/aula':
        $controller = new App\Controllers\PanelController(); 
        $controller->aula();        
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        break;
}
