<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Allow: GET, POST, OPTIONS, PUT, DELETE');
header('Content-Type: application/json');
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/modulo.php';

$modulo = new Modulo();

/**
 * --- Capa de Compatibilidad ---
 * Estas funciones permiten que el código existente siga funcionando
 * llamando internamente a los métodos de la clase Modulo y Database.
 */

function sqlconector($query, $params = []) {
    global $modulo;
    return $modulo->getDb()->sqlconector($query, $params);
}

function readJsonFile($path) {
    global $modulo;
    return $modulo->readJsonFile($path);
}

function createOrReplaceJsonFile($path, $data) {
    global $modulo;
    return $modulo->createOrReplaceJsonFile($path, $data);
}

/**
 * --- Lógica de Negocio (Placeholders) ---
 * Estas funciones son llamadas por el server pero no se encontraron definiciones.
 * Se dejan como placeholders para evitar errores fatales.
 */

function refreshDatos($usuario = null) {
    // Aquí debería ir la lógica para refrescar los datos del sistema
    return []; 
}

function readTrader($id) {
    // Aquí debería ir la lógica para leer la información de un trader específico
    return ['MONEDA' => 'USDT']; // Valor por defecto para pruebas
}
$method = $_SERVER['REQUEST_METHOD'];

date_default_timezone_set("UTC");

if ($method == "OPTIONS") {
    die();
}

if ($method == "POST") {
    try {
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        
        if(isset($data['changue'])){
            createOrReplaceJsonFile(Modulo::DATA_PATH, $data);
            echo json_encode(array('result' => true, 'order' => null, 'message' => 'Cambios realizados con exito.!'));
        }
        
        if(isset($data['guardar'])){
            echo json_encode(array('result' => true, 'order' => null, 'message' => 'Cambios realizados con exito.!'));
        }

        if(isset($data['borrar'])){          
            sqlconector("DELETE FROM TRADER WHERE ID={$data['borrar']}");

            $response = array('result' => true, 'order' => null, 'message' => 'Cambios realizados con exito.!');
            echo json_encode($response);
        }

        if(isset($data['cerrar-sesion'])){
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            session_destroy();
            echo json_encode(array('result' => true, 'order' => null, 'message' => 'Cambios realizados con exito.!'));        
        }

    } catch (Exception $e) {
        $response = array('error' => $e->getMessage());
        echo json_encode($response);
    }
}

if ($method == "GET") {
    
    if(isset($_GET['resetGanancias'])){
        $asset = $_GET['asset'];
        sqlconector("UPDATE DATOS SET GANANCIA = 0, PERDIDA = 0 WHERE ASSET='$asset'");
        echo json_encode(array('result' => true, 'order' => null, 'message' => 'Cambios realizados con exito.!'));
    }
    
    if(isset($_GET['status'])){
        echo json_encode(array('result' => true, 'order' => null, 'message' => 'Server Online'));
    }

    if(isset($_GET['getencryptedkey'])){
        $key = $_GET['key'];
        $encryptedKey = $modulo->encryptApiKey(trim($key), Modulo::ENCRYPTION_KEY);
        echo json_encode(array('result' => true, 'message' => 'Key encrypted', 'key' => $encryptedKey), JSON_UNESCAPED_SLASHES);
    }   

}
