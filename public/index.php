 <?php
// Cargar el autoloader de Composer para que tus clases funcionen
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar funciones auxiliares (como base_url)
require_once __DIR__ . '/../src/helpers.php';

// 1. Obtenemos la URI
$request = $_SERVER['REQUEST_URI'];

// 2. Definimos la carpeta base (donde está tu index.php)
// Esto elimina "/edu360-paradigma" de la ruta
$base_path = '/edu360-paradigma';
$route = str_replace($base_path, '', $request);

// 3. (Opcional) Eliminar parámetros GET si los hubiera (ej: ?id=1)
$route = explode('?', $route)[0];

switch ($route) {
    case '/':
    case '/index':        
        $controller = new App\Controllers\HomeController(); 
        $controller->index();        
        break;

    case '/contacto':
        echo "<h1>Página de Contacto</h1>";
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        break;
}
