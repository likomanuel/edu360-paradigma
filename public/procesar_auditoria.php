<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/DataBase.php';
require_once __DIR__ . '/../config/modulo.php';

use App\Controllers\NeuroEducacionController;

$modulo = new Modulo();
$user = $modulo->getUser($_SESSION['email']);

if (!$user) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$mensajeUsuario = $input['mensaje'] ?? '';

if (empty($mensajeUsuario)) {
    echo json_encode(['error' => 'Mensaje vacío']);
    exit;
}

$controller = new NeuroEducacionController();
$resultado = $controller->procesarAuditoria($user['id_evolucionador'], $mensajeUsuario);

echo json_encode($resultado);
